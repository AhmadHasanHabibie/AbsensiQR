<?php

namespace App\Exports;

use App\Models\SchoolClass;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GuruAttendanceExport implements FromCollection, ShouldAutoSize, WithStyles
{
    protected $attendances;
    protected $schoolHeaderRows = [];
    protected $classHeaderRows = [];
    protected $classInfoRows = [];
    protected $tableHeaderRows = [];
    protected $tableDataRows = [];
    protected $summaryRows = [];
    protected $summaryPercentageRows = [];
    protected $firstTableHeaderRow = null;
    protected $lastRow = 1;

    public function __construct($attendances)
    {
        $this->attendances = $attendances;
    }

    public function collection()
    {
        $rows = collect();
        $classGroups = static::groupClasses(collect($this->attendances)->filter(fn($attendance) => optional($attendance)->student));

        $this->schoolHeaderRows = [];
        $this->classHeaderRows = [];
        $this->classInfoRows = [];
        $this->tableHeaderRows = [];
        $this->tableDataRows = [];
        $this->summaryRows = [];
        $this->summaryPercentageRows = [];
        $this->firstTableHeaderRow = null;

        foreach (['SISTEM ABSENSI QR CODE', 'SMKN 17 JAKARTA', 'LAPORAN REKAP ABSENSI SISWA'] as $heading) {
            $rows->push([$heading]);
            $this->schoolHeaderRows[] = $rows->count();
        }

        foreach ($classGroups as $group) {
            $rowNumber = 0;
            $rows->push(array_pad(['KELAS : ' . $group['class_name']], 9, ''));
            $this->classHeaderRows[] = $rows->count();
            $rows->push(array_pad(['Wali Kelas : ' . $group['teacher_name']], 9, ''));
            $this->classInfoRows[] = $rows->count();
            $rows->push(array_pad(['Jumlah Siswa : ' . $group['students']->count()], 9, ''));
            $this->classInfoRows[] = $rows->count();

            $rows->push(['No', 'Nama Siswa', 'NIS', 'Hadir', 'Terlambat', 'Izin', 'Sakit', 'Alfa', 'Persentase']);
            $this->tableHeaderRows[] = $rows->count();
            $this->firstTableHeaderRow ??= $rows->count();

            foreach ($group['students'] as $student) {
                $rowNumber++;
                $rows->push([$rowNumber, $student['name'], $student['nis'], $student['hadir'], $student['terlambat'], $student['izin'], $student['sakit'], $student['alpa'], $student['percentage'] / 100]);
                $this->tableDataRows[] = $rows->count();
            }

            $summary = [
                'Total Hadir' => $group['totals']['hadir'],
                'Total Terlambat' => $group['totals']['terlambat'],
                'Total Izin' => $group['totals']['izin'],
                'Total Sakit' => $group['totals']['sakit'],
                'Total Alfa' => $group['totals']['alpa'],
                'Persentase Kehadiran Kelas' => $group['percentage'] / 100,
            ];

            foreach ($summary as $label => $value) {
                $rows->push(array_pad([$label, $value], 9, ''));
                $this->summaryRows[] = $rows->count();
                if ($label === 'Persentase Kehadiran Kelas') {
                    $this->summaryPercentageRows[] = $rows->count();
                }
            }

            $rows->push(array_fill(0, 9, ''));
            $rows->push(array_fill(0, 9, ''));
        }

        $this->lastRow = max($rows->count(), 1);

        return $rows;
    }

    public static function groupClasses($attendances)
    {
        $attendances = collect($attendances);
        $classIds = $attendances->map(fn($attendance) => optional($attendance->student)->class_id)->filter()->unique();
        $classDetails = SchoolClass::with('teacher')->whereIn('id', $classIds)->get()->keyBy('id');

        return $attendances->groupBy(fn($attendance) => optional(optional($attendance->student)->schoolClass)->name ?: 'Belum Ditentukan')
            ->sortBy(function ($students, $className) {
                preg_match('/^(XII|XI|X)\b/i', trim($className), $matches);
                $level = match (strtoupper($matches[1] ?? '')) { 'X' => 1, 'XI' => 2, 'XII' => 3, default => 99 };
                return sprintf('%02d-%s', $level, mb_strtolower($className));
            })
            ->map(function ($students, $className) use ($classDetails) {
                $schoolClass = optional($students->first()->student)->schoolClass;
                $schoolClass = $schoolClass ? $classDetails->get($schoolClass->id, $schoolClass) : null;

                $studentsSummary = $students->groupBy('student_id')->map(function ($records) {
                    $student = optional($records->first()->student);
                    $hadir = $records->where('status', 'hadir')->count();
                    $total = $records->count();

                    return [
                        'name' => $student->name ?? '-',
                        'nis' => $student->nis ?? '-',
                        'hadir' => $hadir,
                        'terlambat' => $records->where('status', 'hadir')->where('is_late', true)->count(),
                        'izin' => $records->where('status', 'izin')->count(),
                        'sakit' => $records->where('status', 'sakit')->count(),
                        'alpa' => $records->where('status', 'alpa')->count(),
                        'percentage' => $total ? round(($hadir / $total) * 100, 2) : 0,
                    ];
                })->sortBy(fn($summary) => $summary['name'], SORT_NATURAL | SORT_FLAG_CASE)->values();

                $totals = [
                    'hadir' => $studentsSummary->sum('hadir'),
                    'terlambat' => $studentsSummary->sum('terlambat'),
                    'izin' => $studentsSummary->sum('izin'),
                    'sakit' => $studentsSummary->sum('sakit'),
                    'alpa' => $studentsSummary->sum('alpa'),
                ];
                $totalAttendance = $totals['hadir'] + $totals['izin'] + $totals['sakit'] + $totals['alpa'];

                return [
                    'class_name' => $className,
                    'teacher_name' => $schoolClass?->teacher?->name ?? 'Belum ditentukan',
                    'students' => $studentsSummary,
                    'totals' => $totals,
                    'percentage' => $totalAttendance ? round(($totals['hadir'] / $totalAttendance) * 100, 2) : 0,
                ];
            })->values();
    }

    public function styles(Worksheet $sheet)
    {
        foreach ($this->schoolHeaderRows as $row) {
            $sheet->mergeCells("A{$row}:I{$row}");
            $sheet->getStyle("A{$row}:I{$row}")->getFont()->setBold(true)->setSize(13);
            $sheet->getStyle("A{$row}:I{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }
        foreach ($this->classHeaderRows as $row) {
            $sheet->getStyle("A{$row}:I{$row}")->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle("A{$row}:I{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9E2F3');
        }
        foreach ($this->classInfoRows as $row) {
            $sheet->getStyle("A{$row}:I{$row}")->getFont()->setBold(true);
        }
        foreach ($this->tableHeaderRows as $row) {
            $sheet->getStyle("A{$row}:I{$row}")->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
            $sheet->getStyle("A{$row}:I{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('0D6EFD');
            $sheet->getStyle("A{$row}:I{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("A{$row}:I{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }
        foreach ($this->tableDataRows as $row) {
            $sheet->getStyle("A{$row}:I{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle("A{$row}:I{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$row}:B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("I{$row}:I{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_0);
        }
        foreach ($this->summaryRows as $row) {
            $sheet->getStyle("A{$row}:B{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle("A{$row}:A{$row}")->getFont()->setBold(true);
            $sheet->getStyle("B{$row}:B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }
        foreach ($this->summaryPercentageRows as $row) {
            $sheet->getStyle("B{$row}:B{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_0);
        }
        if ($this->firstTableHeaderRow) {
            $sheet->freezePane('A' . ($this->firstTableHeaderRow + 1));
        }
    }
}
