<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmergencyAuditExport implements FromCollection, ShouldAutoSize, WithStyles
{
    protected $audits;

    public function __construct($audits)
    {
        $this->audits = $audits;
    }

    public function collection()
    {
        $rows = collect();

        $rows->push(['SISTEM ABSENSI QR CODE - SMKN 17 JAKARTA']);
        $rows->push(['LAPORAN AUDIT TRAIL ABSENSI DARURAT']);
        $rows->push(['Dicetak Pada: ' . now()->isoFormat('D MMMM YYYY, HH:mm:ss') . ' WIB']);
        $rows->push([]);

        $rows->push([
            'No',
            'Tanggal & Jam Input',
            'Nama Siswa',
            'NIS',
            'Kelas',
            'Alasan Darurat',
            'Operator Penginput',
            'Status Awal',
            'Guru Wali Validasi',
            'Waktu Validasi',
            'Status Final',
            'Jenis Validasi',
            'IP Address',
            'Catatan Operator'
        ]);

        foreach ($this->audits as $index => $audit) {
            $rows->push([
                $index + 1,
                optional($audit->input_at)->isoFormat('D MMM YYYY, HH:mm') . ' WIB',
                optional($audit->student)->name ?? '-',
                optional($audit->student)->nis ?? '-',
                optional($audit->schoolClass)->name ?? '-',
                $audit->reason ?? '-',
                optional($audit->operator)->name ?? 'Operator',
                $audit->initial_status ?? 'Hadir Manual',
                optional($audit->teacher)->name ?? '-',
                $audit->validated_at ? $audit->validated_at->isoFormat('D MMM YYYY, HH:mm') . ' WIB' : 'Belum Divalidasi',
                $audit->final_status ?? 'Menunggu Validasi',
                $audit->validation_type === 'automatic' ? 'Disetujui Otomatis' : ($audit->validation_type === 'manual' ? 'Diubah Manual' : '-'),
                $audit->ip_address ?? '-',
                $audit->note ?? '-'
            ]);
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:N1');
        $sheet->mergeCells('A2:N2');
        $sheet->mergeCells('A3:N3');

        $sheet->getStyle('A1:A3')->getFont()->setBold(true);
        $sheet->getStyle('A1:A2')->getFont()->setSize(13);
        $sheet->getStyle('A1:N3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle('A5:N5')->getFont()->setBold(true);
        $sheet->getStyle('A5:N5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFC107');
        $sheet->getStyle('A5:N5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A5:N5')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $highestRow = $sheet->getHighestRow();
        if ($highestRow >= 6) {
            $sheet->getStyle("A6:N{$highestRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle("A6:A{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }
    }
}
