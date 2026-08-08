<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Rekap Absensi</title>
    <style>
        @page { margin: 22px 24px; }
        body { font-family: Arial, sans-serif; font-size: 9px; color: #222; }
        .school-header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 9px; margin-bottom: 14px; }
        .school-header h2 { margin: 0; font-size: 17px; }
        .school-header h3 { margin: 3px 0; font-size: 14px; }
        .school-header p { margin: 0; font-size: 10px; }
        .report-info { width: 100%; margin-bottom: 14px; }
        .report-info td { padding: 2px 0; }
        .class-section { margin-top: 18px; page-break-inside: avoid; }
        .class-title { background: #f1f1f1; border: 1px solid #777; padding: 8px 10px; font-size: 11px; font-weight: bold; }
        .class-info, .data, .class-summary { width: 100%; border-collapse: collapse; }
        .class-info { margin: 0 0 8px; }
        .class-info td { border: 1px solid #bbb; padding: 5px 8px; }
        .data th { background: #e9ecef; border: 1px solid #777; padding: 6px 4px; text-align: center; }
        .data td { border: 1px solid #999; padding: 5px 4px; }
        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .class-summary { width: 48%; margin: 8px 0 0 auto; }
        .class-summary td { border: 1px solid #999; padding: 5px 8px; }
        .class-summary .label { background: #f7f7f7; font-weight: bold; }
        .footer { margin-top: 22px; text-align: right; font-size: 9px; }
    </style>
</head>
<body>
    <div class="school-header"><h2>SISTEM ABSENSI QR CODE</h2><h3>SMKN 17 JAKARTA</h3><p>Laporan Rekap Absensi Siswa</p></div>
    <table class="report-info"><tr><td width="14%"><strong>Periode</strong></td><td>: {{ $dateLabel ?? '-' }}</td></tr><tr><td><strong>Dicetak Oleh</strong></td><td>: Guru</td></tr></table>

    @forelse ($classGroups as $group)
        <div class="class-section">
            <div class="class-title">KELAS : {{ $group['class_name'] }}</div>
            <table class="class-info"><tr><td width="50%"><strong>Wali Kelas:</strong> {{ $group['teacher_name'] }}</td><td><strong>Jumlah Siswa:</strong> {{ $group['students']->count() }}</td></tr></table>
            <table class="data">
                <thead><tr><th width="4%">No</th><th width="25%" class="text-left">Nama Siswa</th><th width="12%" class="text-left">NIS</th><th width="8%">Hadir</th><th width="10%">Terlambat</th><th width="8%">Izin</th><th width="8%">Sakit</th><th width="8%">Alpa</th><th width="17%">Persentase Kehadiran</th></tr></thead>
                <tbody>
                    @forelse ($group['students'] as $student)
                        <tr><td class="text-right">{{ $loop->iteration }}</td><td class="text-left">{{ $student['name'] }}</td><td class="text-left">{{ $student['nis'] }}</td><td class="text-right">{{ $student['hadir'] }}</td><td class="text-right">{{ $student['terlambat'] }}</td><td class="text-right">{{ $student['izin'] }}</td><td class="text-right">{{ $student['sakit'] }}</td><td class="text-right">{{ $student['alpa'] }}</td><td class="text-right">{{ $student['percentage'] }}%</td></tr>
                    @empty
                        <tr><td colspan="9" class="text-center">Tidak ada data absensi pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <table class="class-summary">
                <tr><td class="label">Total Hadir</td><td class="text-right">{{ $group['totals']['hadir'] }}</td></tr>
                <tr><td class="label">Total Terlambat</td><td class="text-right">{{ $group['totals']['terlambat'] }}</td></tr>
                <tr><td class="label">Total Izin</td><td class="text-right">{{ $group['totals']['izin'] }}</td></tr>
                <tr><td class="label">Total Sakit</td><td class="text-right">{{ $group['totals']['sakit'] }}</td></tr>
                <tr><td class="label">Total Alpa</td><td class="text-right">{{ $group['totals']['alpa'] }}</td></tr>
                <tr><td class="label">Persentase Kehadiran Kelas</td><td class="text-right">{{ $group['percentage'] }}%</td></tr>
            </table>
        </div>
    @empty
        <p class="text-center">Tidak ada data absensi.</p>
    @endforelse
    <div class="footer">Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}</div>
</body>
</html>
