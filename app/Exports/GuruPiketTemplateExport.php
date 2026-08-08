<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GuruPiketTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    /**
     * Baris contoh data
     */
    public function array(): array
    {
        return [
            [
                'Siti Rahayu',
                '197903152005011002',
                'siti.rahayu',
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
            'Nama Guru Piket',
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
                    'startColor' => ['argb' => 'FFFFC107'],
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
