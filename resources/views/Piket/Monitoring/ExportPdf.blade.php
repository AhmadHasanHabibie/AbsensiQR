<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Monitoring Absensi - Guru Piket</title>
    <style>
        @page {
            margin: 24px 30px;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            color: #1a1a2e;
            line-height: 1.4;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0284c7;
            padding-bottom: 10px;
            margin-bottom: 16px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .school-title {
            font-size: 18px;
            font-weight: bold;
            color: #075985;
            margin: 0;
            text-transform: uppercase;
        }
        .school-subtitle {
            font-size: 14px;
            font-weight: bold;
            color: #333;
            margin: 2px 0;
        }
        .report-title {
            font-size: 12px;
            color: #0284c7;
            font-weight: bold;
            margin: 0;
        }
        .info-table {
            width: 100%;
            margin-bottom: 14px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 6px 8px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .data-table th {
            background: #0284c7;
            color: #ffffff;
            border: 1px solid #075985;
            padding: 7px 6px;
            text-align: center;
            font-weight: bold;
        }
        .data-table td {
            border: 1px solid #cbd5e1;
            padding: 6px 6px;
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .pdf-footer {
            margin-top: 24px;
            width: 100%;
            border-top: 1px solid #cbd5e1;
            padding-top: 8px;
            font-size: 9px;
            color: #64748b;
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <table class="header-table">
        <tr>
            <td width="70%">
                <h1 class="school-title">SMKN 17 JAKARTA</h1>
                <div class="school-subtitle">Sistem Absensi QR Code</div>
                <div class="report-title">Monitoring Konfirmasi Absensi — Guru Piket</div>
            </td>
            <td width="30%" class="text-right">
                <div style="font-size: 11px; font-weight: bold; color: #075985;">Periode: {{ $periodLabel }}</div>
                <div style="font-size: 9px; color: #64748b;">Tanggal Cetak: {{ $printedAt }}</div>
            </td>
        </tr>
    </table>

    {{-- Agregat Ringkasan Sekolah --}}
    <table class="info-table">
        <tr>
            <td width="25%"><strong>Total Kelas:</strong> {{ $totalClasses }}</td>
            <td width="25%"><strong>Sudah Konfirmasi:</strong> {{ $confirmedCount }}</td>
            <td width="25%"><strong>Belum Konfirmasi:</strong> {{ $unconfirmedCount }}</td>
            <td width="25%"><strong>Persentase Konfirmasi:</strong> {{ $percentage }}%</td>
        </tr>
    </table>

    {{-- Data Rekap Monitoring Kelas --}}
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="25%" class="text-left">Kelas</th>
                <th width="30%" class="text-left">Guru Wali Kelas</th>
                <th width="15%">Siswa</th>
                <th width="25%">Status Konfirmasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-left"><strong>{{ $item->name }}</strong></td>
                    <td class="text-left">{{ $item->teacher_name }}</td>
                    <td class="text-center">{{ $item->students_count }} Siswa</td>
                    <td class="text-center">
                        <strong>{{ $item->status_label }}</strong>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-4">Tidak ada data monitoring kelas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Footer Cetak --}}
    <table class="pdf-footer">
        <tr>
            <td width="50%">
                Dicetak oleh: <strong>{{ $piketName }}</strong> (Guru Piket)
            </td>
            <td width="50%" class="text-right">
                Waktu Cetak: {{ $printedAt }} WIB
            </td>
        </tr>
    </table>

</body>
</html>
