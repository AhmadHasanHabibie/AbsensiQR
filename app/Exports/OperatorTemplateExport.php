<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OperatorTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    /**
     * Baris contoh data
     */
    public function array(): array
    {
        return [
            [
                'Andi Saputra',
                '198765432101',
                'andi.saputra',
                'password123',
                'Aktif',
            ],
        ];
    }

    /**
     * Baris header kolom
     */
    public function headings(): array
    {
        return [
            'Nama Operator',
            'NIP',
            'Username',
            'Password',
            'Status',
        ];
    }

    /**
     * Styling header
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                ],
                'fill' => [
                    'fillType'   => 'solid',
                    'startColor' => ['argb' => 'FF0D6EFD'],
                ],
            ],
        ];
    }

    /**
     * Lebar kolom
     */
    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 22,
            'C' => 18,
            'D' => 16,
            'E' => 12,
        ];
    }
}
