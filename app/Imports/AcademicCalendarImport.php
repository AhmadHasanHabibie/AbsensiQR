<?php

namespace App\Imports;

use App\Models\AcademicCalendar;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class AcademicCalendarImport implements ToCollection
{
    /*
    |--------------------------------------------------------------------------
    | Public Properties
    |--------------------------------------------------------------------------
    */

    /** @var int Jumlah baris berhasil diimport */
    public int $success = 0;

    /** @var array Daftar error per baris */
    public array $failed = [];

    /** @var string|null Tahun ajaran yang terdeteksi dari file */
    public ?string $detectedYear = null;

    /*
    |--------------------------------------------------------------------------
    | Expected Header
    |--------------------------------------------------------------------------
    */
    private array $expectedHeaders = [
        'academic_year',
        'date',
        'day_name',
        'month',
        'semester',
        'status',
        'category',
        'activity',
        'qr_status',
        'teacher_attendance',
        'student_attendance',
        'operator_attendance',
        'description',
    ];

    /*
    |--------------------------------------------------------------------------
    | Collection Handler (Main Entry)
    |--------------------------------------------------------------------------
    */

    public function collection(Collection $rows): void
    {
        /*
        |--------------------------------------------------------------------------
        | Validasi Header
        |--------------------------------------------------------------------------
        */
        if ($rows->isEmpty()) {
            $this->failed[] = [
                'row'   => 1,
                'name'  => '-',
                'error' => 'File Excel kosong atau tidak dapat dibaca.',
            ];
            return;
        }

        $headerRow = $rows->first()->map(fn ($h) => strtolower(trim($h ?? '')))->toArray();

        foreach ($this->expectedHeaders as $i => $expected) {
            if (($headerRow[$i] ?? '') !== $expected) {
                $this->failed[] = [
                    'row'   => 1,
                    'name'  => '-',
                    'error' => "Header kolom tidak sesuai template. Kolom " . ($i + 1) . " seharusnya '{$expected}', ditemukan '" . ($headerRow[$i] ?? '-') . "'. Gunakan template yang disediakan.",
                ];
                return;
            }
        }

        // Hapus baris header
        $dataRows = $rows->skip(1)->values()->filter(function ($row) {
            // Skip baris yang benar-benar kosong
            return collect($row)->filter(fn ($v) => $v !== null && $v !== '')->isNotEmpty();
        })->values();

        if ($dataRows->isEmpty()) {
            $this->failed[] = [
                'row'   => 2,
                'name'  => '-',
                'error' => 'Tidak ada data di bawah header. Minimal harus ada 1 baris data.',
            ];
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Tahap 1: Validasi Semua Baris Terlebih Dahulu
        |--------------------------------------------------------------------------
        */
        $validated = [];
        $datesInFile = [];

        foreach ($dataRows as $index => $row) {
            $rowNumber = $index + 2; // +2 karena baris 1 = header

            try {
                $parsed = $this->parseAndValidateRow($row, $rowNumber, $datesInFile);
                $validated[] = $parsed;
                // Track tanggal+tahun yang sudah ada di file ini untuk cek duplikat
                $datesInFile[$parsed['academic_year'] . '_' . $parsed['date']] = $rowNumber;
            } catch (\Exception $e) {
                $this->failed[] = [
                    'row'   => $rowNumber,
                    'name'  => $this->safeGet($row, 1) ?: '-',
                    'error' => $e->getMessage(),
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Jika Ada Error — Stop, Jangan Simpan Apapun
        |--------------------------------------------------------------------------
        */
        if (! empty($this->failed)) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Tahap 2: Cek Duplikat Dengan Database
        |--------------------------------------------------------------------------
        */
        foreach ($validated as $index => $data) {
            $rowNumber = $index + 2;
            $existsInDb = AcademicCalendar::where('academic_year', $data['academic_year'])
                ->where('date', $data['date'])
                ->exists();

            if ($existsInDb) {
                $this->failed[] = [
                    'row'   => $rowNumber,
                    'name'  => $data['date'],
                    'error' => "Tanggal {$data['date']} sudah ada di database untuk tahun ajaran {$data['academic_year']}. Hapus data lama terlebih dahulu.",
                ];
            }
        }

        if (! empty($this->failed)) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Tahap 3: Simpan Dalam Satu Transaksi
        |--------------------------------------------------------------------------
        */
        DB::transaction(function () use ($validated) {
            foreach ($validated as $data) {
                AcademicCalendar::create($data);
                $this->success++;
            }

            // Simpan tahun ajaran yang terdeteksi
            if (! empty($validated)) {
                $this->detectedYear = $validated[0]['academic_year'];
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Row Parser & Validator
    |--------------------------------------------------------------------------
    */

    /**
     * Parse dan validasi satu baris Excel.
     *
     * @throws \Exception
     */
    private function parseAndValidateRow($row, int $rowNumber, array $datesInFile): array
    {
        /*
        |--------------------------------------------------------------------------
        | Ambil Nilai Mentah
        |--------------------------------------------------------------------------
        */
        $academicYear       = trim($this->safeGet($row, 0) ?? '');
        $dateRaw            = trim($this->safeGet($row, 1) ?? '');
        $dayName            = trim($this->safeGet($row, 2) ?? '');
        $month              = trim($this->safeGet($row, 3) ?? '');
        $semester           = trim($this->safeGet($row, 4) ?? '');
        $status             = trim($this->safeGet($row, 5) ?? '');
        $category           = trim($this->safeGet($row, 6) ?? '');
        $activity           = trim($this->safeGet($row, 7) ?? '');
        $qrStatus           = trim($this->safeGet($row, 8) ?? '0');
        $teacherAttendance  = trim($this->safeGet($row, 9) ?? '0');
        $studentAttendance  = trim($this->safeGet($row, 10) ?? '0');
        $operatorAttendance = trim($this->safeGet($row, 11) ?? '0');
        $description        = trim($this->safeGet($row, 12) ?? '');

        /*
        |--------------------------------------------------------------------------
        | Validasi Kolom Wajib
        |--------------------------------------------------------------------------
        */
        if (empty($academicYear)) {
            throw new \Exception("Kolom 'academic_year' wajib diisi.");
        }

        if (! preg_match('/^\d{4}\/\d{4}$/', $academicYear)) {
            throw new \Exception("Format 'academic_year' harus 'YYYY/YYYY' (contoh: 2026/2027). Ditemukan: '{$academicYear}'.");
        }

        if (empty($dateRaw)) {
            throw new \Exception("Kolom 'date' wajib diisi.");
        }

        /*
        |--------------------------------------------------------------------------
        | Parse Tanggal
        |--------------------------------------------------------------------------
        */
        $date = $this->parseDate($dateRaw);

        if (! $date) {
            throw new \Exception("Format tanggal tidak valid: '{$dateRaw}'. Gunakan format YYYY-MM-DD (contoh: 2026-07-13).");
        }

        $dateString = $date->toDateString();

        /*
        |--------------------------------------------------------------------------
        | Validasi Semester
        |--------------------------------------------------------------------------
        */
        if (empty($semester)) {
            throw new \Exception("Kolom 'semester' wajib diisi.");
        }

        if (! in_array($semester, AcademicCalendar::SEMESTERS)) {
            $valid = implode(', ', AcademicCalendar::SEMESTERS);
            throw new \Exception("Semester tidak valid: '{$semester}'. Nilai yang diperbolehkan: {$valid}.");
        }

        /*
        |--------------------------------------------------------------------------
        | Validasi Status
        |--------------------------------------------------------------------------
        */
        if (empty($status)) {
            throw new \Exception("Kolom 'status' wajib diisi.");
        }

        if (! in_array($status, AcademicCalendar::STATUSES)) {
            $valid = implode(', ', AcademicCalendar::STATUSES);
            throw new \Exception("Status tidak valid: '{$status}'. Nilai yang diperbolehkan: {$valid}.");
        }

        /*
        |--------------------------------------------------------------------------
        | Validasi Kategori
        |--------------------------------------------------------------------------
        */
        if (empty($category)) {
            throw new \Exception("Kolom 'category' wajib diisi.");
        }

        if (! in_array($category, AcademicCalendar::CATEGORIES)) {
            $valid = implode(', ', AcademicCalendar::CATEGORIES);
            throw new \Exception("Kategori tidak valid: '{$category}'. Nilai yang diperbolehkan: {$valid}.");
        }

        /*
        |--------------------------------------------------------------------------
        | Cek Duplikat Dalam File (baris sebelumnya)
        |--------------------------------------------------------------------------
        */
        $fileKey = $academicYear . '_' . $dateString;
        if (isset($datesInFile[$fileKey])) {
            throw new \Exception("Tanggal {$dateString} duplikat dalam file ini (sudah ada di baris {$datesInFile[$fileKey]}).");
        }

        /*
        |--------------------------------------------------------------------------
        | Auto-fill day_name & month Jika Kosong
        |--------------------------------------------------------------------------
        */
        if (empty($dayName)) {
            $dayName = $this->getDayNameIndonesian($date->dayOfWeekIso);
        }

        if (empty($month) || ! is_numeric($month)) {
            $month = $date->month;
        }

        /*
        |--------------------------------------------------------------------------
        | Boolean Flags
        |--------------------------------------------------------------------------
        */
        $toBool = fn ($v) => in_array(strtolower((string)$v), ['1', 'true', 'ya', 'yes', 'aktif'], true);

        return [
            'academic_year'       => $academicYear,
            'date'                => $dateString,
            'day_name'            => $dayName,
            'month'               => (int) $month,
            'semester'            => $semester,
            'status'              => $status,
            'category'            => $category,
            'activity'            => $activity ?: null,
            'qr_status'           => $toBool($qrStatus),
            'teacher_attendance'  => $toBool($teacherAttendance),
            'student_attendance'  => $toBool($studentAttendance),
            'operator_attendance' => $toBool($operatorAttendance),
            'description'         => $description ?: null,
            'is_active'           => false, // Default nonaktif, aktifkan manual
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Ambil nilai dari row dengan aman.
     */
    private function safeGet($row, int $index): mixed
    {
        if (is_array($row)) {
            return $row[$index] ?? null;
        }

        return $row[$index] ?? null;
    }

    /**
     * Parse berbagai format tanggal yang umum di Excel.
     */
    private function parseDate(mixed $raw): ?Carbon
    {
        if (empty($raw) && $raw !== '0') {
            return null;
        }

        // Jika numerik → Excel serial date
        if (is_numeric($raw)) {
            try {
                return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $raw));
            } catch (\Throwable) {
                return null;
            }
        }

        // Coba beberapa format string
        $formats = ['Y-m-d', 'd-m-Y', 'd/m/Y', 'Y/m/d', 'd-M-Y', 'd M Y'];

        foreach ($formats as $format) {
            try {
                $dt = Carbon::createFromFormat($format, $raw);
                if ($dt !== false) {
                    return $dt;
                }
            } catch (\Throwable) {
                continue;
            }
        }

        // Last resort: Carbon::parse
        try {
            return Carbon::parse($raw);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Nama hari dalam Bahasa Indonesia.
     */
    private function getDayNameIndonesian(int $isoDay): string
    {
        return match ($isoDay) {
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
            default => 'Senin',
        };
    }
}
