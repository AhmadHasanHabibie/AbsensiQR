<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminAttendanceExport implements
    FromCollection,
    ShouldAutoSize,
    WithStyles
{
    /**
     * Data absensi yang akan diexport.
     */
    protected $attendances;

    /**
     * Posisi baris yang membutuhkan format khusus.
     */
    protected $schoolHeaderRows = [];
    protected $classHeaderRows = [];
    protected $classInfoRows = [];
    protected $tableHeaderRows = [];
    protected $tableDataRows = [];
    protected $summaryRows = [];
    protected $summaryPercentageRows = [];
    protected $firstTableHeaderRow = null;
    protected $lastRow = 1;

    /**
     * Constructor.
     */
    public function __construct($attendances)
    {
        $this->attendances = $attendances;
    }

    /**
     * Data export dalam satu worksheet, dikelompokkan per kelas.
     */
    public function collection()
    {
        $rows = collect();
        $classGroups = static::groupClasses(
            collect($this->attendances)->filter(fn($attendance) => optional($attendance)->student)
        );

        $this->schoolHeaderRows = [];
        $this->classHeaderRows = [];
        $this->classInfoRows = [];
        $this->tableHeaderRows = [];
        $this->tableDataRows = [];
        $this->summaryRows = [];
        $this->summaryPercentageRows = [];
        $this->firstTableHeaderRow = null;

        $rows->push(['SISTEM ABSENSI QR CODE']);
        $this->schoolHeaderRows[] = $rows->count();
        $rows->push(['SMKN 17 JAKARTA']);
        $this->schoolHeaderRows[] = $rows->count();
        $rows->push(['LAPORAN REKAP ABSENSI SISWA']);
        $this->schoolHeaderRows[] = $rows->count();
        foreach ($classGroups as $group) {
            $rowNumber = 0;

            $rows->push(array_pad(['KELAS : ' . $group['class_name']], 9, ''));
            $this->classHeaderRows[] = $rows->count();

            $rows->push(array_pad(['Wali Kelas : ' . $group['teacher_name']], 9, ''));
            $this->classInfoRows[] = $rows->count();

            $rows->push(array_pad(['Jumlah Siswa : ' . $group['students']->count()], 9, ''));
            $this->classInfoRows[] = $rows->count();

            $rows->push(['No', 'Nama Siswa', 'NIS', 'Hadir', 'Terlambat', 'Izin', 'Sakit', 'Alpha', 'Persentase']);
            $this->tableHeaderRows[] = $rows->count();
            $this->firstTableHeaderRow ??= $rows->count();

            foreach ($group['students'] as $student) {
                $rowNumber++;

                $rows->push([
                    $rowNumber,
                    $student['name'],
                    $student['nis'],
                    $student['hadir'],
                    $student['terlambat'],
                    $student['izin'],
                    $student['sakit'],
                    $student['alpa'],
                    $student['percentage'] / 100,
                ]);
                $this->tableDataRows[] = $rows->count();
            }

            $summary = [
                'Total Hadir' => $group['totals']['hadir'],
                'Total Terlambat' => $group['totals']['terlambat'],
                'Total Izin' => $group['totals']['izin'],
                'Total Sakit' => $group['totals']['sakit'],
                'Total Alpa' => $group['totals']['alpa'],
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

    /**
     * Group attendances by class and student summaries.
     */
    public static function groupClasses($attendances)
    {
        $attendances = collect($attendances);

        $classes = $attendances
            ->map(fn($attendance) => optional($attendance->student)->schoolClass)
            ->filter()
            ->unique('id')
            ->values();

        $classDetails = \App\Models\SchoolClass::query()
            ->with('teacher')
            ->whereIn('id', $classes->pluck('id'))
            ->get()
            ->keyBy('id');

        $groups = $attendances->groupBy(function ($attendance) {
            return optional(optional($attendance->student)->schoolClass)->name ?: 'Belum Ditentukan';
        });

        return $groups
            ->sortBy(function ($students, $className) {
                preg_match('/^(XII|XI|X)\b/i', trim($className), $matches);

                $levelOrder = match (strtoupper($matches[1] ?? '')) {
                    'X' => 1,
                    'XI' => 2,
                    'XII' => 3,
                    default => 99,
                };

                return sprintf('%02d-%s', $levelOrder, mb_strtolower($className));
            })
            ->map(function ($students, $className) use ($classDetails) {
                $class = optional($students->first()->student)->schoolClass;
                $class = $class ? $classDetails->get($class->id, $class) : null;
                $studentGroups = $students->groupBy('student_id');

                $studentsSummary = $studentGroups
                    ->map(function ($records) {
                        $student = optional($records->first()->student);
                        $hadir = $records->where('status', 'hadir')->count();
                        $terlambat = $records->where('is_late', true)->count();
                        $izin = $records->where('status', 'izin')->count();
                        $sakit = $records->where('status', 'sakit')->count();
                        $alpa = $records->where('status', 'alpa')->count();
                        $total = $records->count();

                        return [
                            'name' => $student->name ?? '-',
                            'nis' => $student->nis ?? '-',
                            'hadir' => $hadir,
                            'terlambat' => $terlambat,
                            'izin' => $izin,
                            'sakit' => $sakit,
                            'alpa' => $alpa,
                            'percentage' => $total ? round(($hadir / $total) * 100, 2) : 0,
                        ];
                    })
                    ->sortBy(fn($summary) => $summary['name'], SORT_NATURAL | SORT_FLAG_CASE)
                    ->values();

                $totals = [
                    'hadir' => $studentsSummary->sum('hadir'),
                    'terlambat' => $studentsSummary->sum('terlambat'),
                    'izin' => $studentsSummary->sum('izin'),
                    'sakit' => $studentsSummary->sum('sakit'),
                    'alpa' => $studentsSummary->sum('alpa'),
                ];
                $totalAttendance = $totals['hadir']
                    + $totals['izin']
                    + $totals['sakit']
                    + $totals['alpa'];

                return [
                    'class_name' => $className,
                    'teacher_name' => $class?->teacher?->name ?? 'Belum ditentukan',
                    'students' => $studentsSummary,
                    'totals' => $totals,
                    'percentage' => $totalAttendance ? round(($totals['hadir'] / $totalAttendance) * 100, 2) : 0,
                ];
            })
            ->values();
    }

    /**
     * Style Excel.
     */
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
            $sheet->getStyle("A{$row}:I{$row}")->getFont()->setBold(true);
            $sheet->getStyle("A{$row}:I{$row}")->getFont()->getColor()->setRGB('FFFFFF');
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
