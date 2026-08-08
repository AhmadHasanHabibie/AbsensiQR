<?php

namespace App\Exports;

use App\Models\AcademicCalendar;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AcademicCalendarTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    /**
     * Data contoh (satu baris demonstrasi).
     */
    public function array(): array
    {
        return [
            [
                '2026/2027',       // academic_year
                '2026-07-13',      // date (format: YYYY-MM-DD)
                'Senin',           // day_name
                '7',               // month
                'Ganjil',          // semester
                'Hari Belajar',    // status
                'Akademik',        // category
                'KBM Normal',      // activity
                '1',               // qr_status (1=Ya, 0=Tidak)
                '1',               // teacher_attendance (1=Ya, 0=Tidak)
                '1',               // student_attendance (1=Ya, 0=Tidak)
                '1',               // operator_attendance (1=Ya, 0=Tidak)
                'Hari pertama masuk semester ganjil', // description
            ],
            [
                '2026/2027',
                '2026-08-17',
                'Senin',
                '8',
                'Ganjil',
                'Libur Nasional',
                'Libur Nasional',
                'Hari Kemerdekaan RI',
                '0',
                '0',
                '0',
                '0',
                'HUT Republik Indonesia ke-81',
            ],
        ];
    }

    /**
     * Header kolom Excel.
     */
    public function headings(): array
    {
        return [
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
    }

    /**
     * Style header row.
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => [
                    'bold'  => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size'  => 11,
                ],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0284C7'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }

    /**
     * Lebar kolom (dalam karakter).
     */
    public function columnWidths(): array
    {
        return [
            'A' => 15, // academic_year
            'B' => 14, // date
            'C' => 12, // day_name
            'D' => 8,  // month
            'E' => 10, // semester
            'F' => 20, // status
            'G' => 22, // category
            'H' => 25, // activity
            'I' => 12, // qr_status
            'J' => 20, // teacher_attendance
            'K' => 20, // student_attendance
            'L' => 22, // operator_attendance
            'M' => 35, // description
        ];
    }
}
