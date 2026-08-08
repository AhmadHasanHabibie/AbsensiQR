<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Audit Absensi Darurat - SMKN 17 Jakarta</title>
    <style>
        @page {
            margin: 20px 25px;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9px;
            color: #1a1a2e;
            line-height: 1.3;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #d97706;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .school-title {
            font-size: 16px;
            font-weight: bold;
            color: #1a1a2e;
            margin: 0;
            text-transform: uppercase;
        }
        .school-subtitle {
            font-size: 10px;
            color: #4b5563;
        }
        .report-title {
            font-size: 12px;
            color: #d97706;
            font-weight: bold;
            margin: 4px 0 0;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .data-table th {
            background: #fef08a;
            color: #78350f;
            border: 1px solid #d97706;
            padding: 6px 4px;
            text-align: center;
            font-weight: bold;
        }
        .data-table td {
            border: 1px solid #cbd5e1;
            padding: 4px 5px;
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 8px;
        }
        .badge-warning { background: #fef08a; color: #854d0e; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .footer-table {
            margin-top: 18px;
            width: 100%;
            border-top: 1px solid #cbd5e1;
            padding-top: 6px;
            font-size: 8px;
            color: #64748b;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td width="70%">
                <div class="school-title">SMKN 17 JAKARTA</div>
                <div class="school-subtitle">Sistem Absensi QR Code & Audit Trail Enterprise</div>
                <div class="report-title">LAPORAN AUDIT TRAIL ABSENSI DARURAT</div>
            </td>
            <td width="30%" class="text-right">
                <div>Tanggal Cetak: {{ $printedAt }} WIB</div>
                <div>Dicetak Oleh: {{ $adminName ?? 'Administrator' }}</div>
                <div>Total Data: {{ $audits->count() }} Record</div>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="12%">Waktu Input</th>
                <th width="16%">Nama Siswa</th>
                <th width="8%">NIS</th>
                <th width="8%">Kelas</th>
                <th width="12%">Alasan</th>
                <th width="12%">Operator Input</th>
                <th width="8%">Status Awal</th>
                <th width="12%">Guru Validasi</th>
                <th width="9%">Status Final</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($audits as $index => $item)
                @php
                    $finalLower = strtolower($item->final_status ?? '');
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ optional($item->input_at)->isoFormat('D MMM YY, HH:mm') }}</td>
                    <td><strong>{{ optional($item->student)->name ?? '-' }}</strong></td>
                    <td class="text-center">{{ optional($item->student)->nis ?? '-' }}</td>
                    <td class="text-center">{{ optional($item->schoolClass)->name ?? '-' }}</td>
                    <td>{{ $item->reason }}</td>
                    <td>{{ optional($item->operator)->name ?? 'Operator' }}</td>
                    <td class="text-center"><span class="badge badge-warning">Hadir Manual</span></td>
                    <td>{{ optional($item->teacher)->name ?? '-' }}</td>
                    <td class="text-center">
                        <span class="badge badge-{{ $finalLower === 'hadir' ? 'success' : 'danger' }}">
                            {{ $item->final_status ?? 'Menunggu' }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center py-3">Tidak ada data audit absensi darurat.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="footer-table">
        <tr>
            <td>Administrator SMKN 17 JAKARTA &mdash; Audit Trail System (Read-Only)</td>
            <td class="text-right">Dicetak Oleh: {{ $adminName ?? 'Administrator' }} pada {{ $printedAt }} WIB</td>
        </tr>
    </table>

</body>
</html>
