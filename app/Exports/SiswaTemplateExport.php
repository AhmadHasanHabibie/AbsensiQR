<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SiswaTemplateExport implements FromArray, WithHeadings, WithStyles
{
    /**
     * @return array
     */
    public function array(): array
    {
        return [
            ['John Doe', '1234567890', 'john.doe', 'password123', 'Aktif', 'X IPA 1'],
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'name',
            'nis',
            'username',
            'password',
            'status',
            'class_name',
        ];
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0D6EFD'],
                ],
            ],
        ];
    }
}

