<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Absensi Harian - Guru Piket</title>
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
            padding: 4px 6px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
        }
        .class-box {
            margin-top: 16px;
            page-break-inside: avoid;
        }
        .class-header {
            background: #0284c7;
            color: #ffffff;
            padding: 6px 10px;
            font-weight: bold;
            font-size: 11px;
            border-top-left-radius: 4px;
            border-top-right-radius: 4px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
        }
        .data-table th {
            background: #e2e8f0;
            color: #1e293b;
            border: 1px solid #cbd5e1;
            padding: 6px 5px;
            text-align: center;
            font-weight: bold;
        }
        .data-table td {
            border: 1px solid #cbd5e1;
            padding: 5px 6px;
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .summary-box {
            width: 50%;
            margin-left: auto;
            margin-top: 8px;
            border-collapse: collapse;
        }
        .summary-box td {
            border: 1px solid #cbd5e1;
            padding: 4px 8px;
        }
        .summary-box .lbl {
            background: #f1f5f9;
            font-weight: bold;
        }
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
                <div class="report-title">Laporan Absensi Harian — Guru Piket</div>
            </td>
            <td width="30%" class="text-right">
                <div style="font-size: 11px; font-weight: bold; color: #075985;">Periode: {{ $dateLabel ?? '-' }}</div>
                <div style="font-size: 9px; color: #64748b;">Tanggal Cetak: {{ $printedAt }}</div>
            </td>
        </tr>
    </table>

    {{-- Agregat Ringkasan Sekolah --}}
    <table class="info-table">
        <tr>
            <td width="16%"><strong>Total Hadir:</strong> {{ $hadir }}</td>
            <td width="16%"><strong>Total Terlambat:</strong> {{ $terlambat }}</td>
            <td width="16%"><strong>Total Izin:</strong> {{ $izin }}</td>
            <td width="16%"><strong>Total Sakit:</strong> {{ $sakit }}</td>
            <td width="16%"><strong>Total Alpa:</strong> {{ $alpa }}</td>
            <td width="20%"><strong>Dicetak Oleh:</strong> {{ $piketName }}</td>
        </tr>
    </table>

    {{-- Data Rekap Kelas --}}
    @forelse ($classGroups as $group)
        <div class="class-box">
            <div class="class-header">
                KELAS: {{ $group['class_name'] }} &nbsp;|&nbsp; Wali Kelas: {{ $group['teacher_name'] }} &nbsp;|&nbsp; Jumlah Siswa: {{ $group['students']->count() }}
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th width="4%">No</th>
                        <th width="30%" class="text-left">Nama Siswa</th>
                        <th width="15%" class="text-left">NIS</th>
                        <th width="8%">Hadir</th>
                        <th width="10%">Terlambat</th>
                        <th width="8%">Izin</th>
                        <th width="8%">Sakit</th>
                        <th width="8%">Alpa</th>
                        <th width="9%">Kehadiran</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($group['students'] as $student)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-left"><strong>{{ $student['name'] }}</strong></td>
                            <td class="text-left">{{ $student['nis'] }}</td>
                            <td class="text-center">{{ $student['hadir'] }}</td>
                            <td class="text-center">{{ $student['terlambat'] }}</td>
                            <td class="text-center">{{ $student['izin'] }}</td>
                            <td class="text-center">{{ $student['sakit'] }}</td>
                            <td class="text-center">{{ $student['alpa'] }}</td>
                            <td class="text-center">{{ $student['percentage'] }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">Tidak ada data absensi untuk kelas ini pada periode yang dipilih.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <table class="summary-box">
                <tr><td class="lbl">Total Hadir</td><td class="text-right"><strong>{{ $group['totals']['hadir'] }}</strong></td></tr>
                <tr><td class="lbl">Total Terlambat</td><td class="text-right"><strong>{{ $group['totals']['terlambat'] }}</strong></td></tr>
                <tr><td class="lbl">Total Izin</td><td class="text-right"><strong>{{ $group['totals']['izin'] }}</strong></td></tr>
                <tr><td class="lbl">Total Sakit</td><td class="text-right"><strong>{{ $group['totals']['sakit'] }}</strong></td></tr>
                <tr><td class="lbl">Total Alpa</td><td class="text-right"><strong>{{ $group['totals']['alpa'] }}</strong></td></tr>
                <tr><td class="lbl">Persentase Kehadiran Kelas</td><td class="text-right"><strong>{{ $group['percentage'] }}%</strong></td></tr>
            </table>
        </div>
    @empty
        <p class="text-center py-4">Tidak ada data laporan absensi.</p>
    @endforelse

    {{-- Footer Cetak --}}
    <table class="pdf-footer">
        <tr>
            <td width="50%">
                Dicetak oleh: <strong>{{ $piketName }}</strong> (Guru Piket)
            </td>
            <td width="50%" class="text-right">
                Tanggal Cetak: {{ $printedAt }} WIB
            </td>
        </tr>
    </table>

</body>
</html>
