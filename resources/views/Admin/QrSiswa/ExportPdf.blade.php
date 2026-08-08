<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Daftar QR Code Siswa - {{ $class->name }}</title>
    <style>
        @page {
            margin: 25px 30px 40px 30px;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            color: #111;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #333;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .school-title {
            text-align: center;
        }
        .school-title h2 {
            margin: 0;
            font-size: 15pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .school-title h4 {
            margin: 2px 0;
            font-size: 12pt;
            color: #198754;
        }
        .school-title p {
            margin: 0;
            font-size: 8.5pt;
            color: #555;
        }
        .meta-info {
            width: 100%;
            margin-bottom: 12px;
            background: #f8f9fa;
            border: 1px solid #ddd;
            padding: 8px 12px;
            border-radius: 4px;
        }
        .meta-info td {
            font-size: 9.5pt;
        }
        .grid-table {
            width: 100%;
            border-collapse: collapse;
        }
        .grid-table tr {
            page-break-inside: avoid;
        }
        .grid-td {
            width: 50%;
            vertical-align: top;
            padding: 6px;
        }
        .qr-card {
            border: 1.5px solid #333;
            border-radius: 8px;
            padding: 10px;
            text-align: center;
            background: #fff;
        }
        .card-header-mini {
            font-size: 8pt;
            font-weight: bold;
            color: #198754;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            border-bottom: 1px dashed #ccc;
            padding-bottom: 4px;
            margin-bottom: 6px;
        }
        .qr-img {
            width: 140px;
            height: 140px;
            margin: 4px auto;
            display: block;
        }
        .qr-placeholder {
            width: 140px;
            height: 140px;
            margin: 4px auto;
            border: 1px solid #ccc;
            line-height: 140px;
            font-size: 9pt;
            color: #999;
        }
        .student-name {
            font-size: 10.5pt;
            font-weight: bold;
            margin-top: 4px;
            margin-bottom: 2px;
            color: #111;
        }
        .student-nis {
            font-size: 9pt;
            color: #444;
            margin-bottom: 2px;
        }
        .student-class {
            font-size: 8.5pt;
            font-weight: bold;
            color: #0d6efd;
        }
        .footer {
            position: fixed;
            bottom: -25px;
            left: 0;
            right: 0;
            height: 20px;
            text-align: center;
            font-size: 8.5pt;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }
        .pagenum:before {
            content: counter(page);
        }
    </style>
</head>
<body>

    <!-- KOP HEADER -->
    <table class="header-table">
        <tr>
            <td width="12%" style="text-align: center;">
                <div style="font-size: 22pt; font-weight: bold; color: #198754;">17</div>
            </td>
            <td width="88%" class="school-title">
                <h2>SMK NEGERI 17 JAKARTA</h2>
                <h4>SISTEM ABSENSI QR CODE</h4>
                <p>Jl. Slipi I No. 1, Palmerah, Jakarta Barat | Website: smkn17jakarta.sch.id</p>
            </td>
        </tr>
    </table>

    <!-- SUBJUDUL & META INFO -->
    <table class="meta-info">
        <tr>
            <td width="50%"><strong>Daftar QR Code Siswa</strong></td>
            <td width="50%" style="text-align: right;"><strong>Kelas:</strong> {{ $class->name }}</td>
        </tr>
        <tr>
            <td>Total Siswa: {{ $students->count() }} Orang</td>
            <td style="text-align: right;">Dicetak: {{ $printDate->translatedFormat('d F Y H:i') }} WIB</td>
        </tr>
    </table>

    <!-- TABEL GRID 2 KOLOM RENDER SELURUH DATA SISWA -->
    <table class="grid-table">
        @foreach ($students->chunk(2) as $row)
            <tr>
                @foreach ($row as $student)
                    <td class="grid-td">
                        <div class="qr-card">
                            <div class="card-header-mini">SMKN 17 JAKARTA &bull; KARTU QR ABSENSI</div>

                            @if ($student->qr_code && file_exists(public_path('storage/' . $student->qr_code)))
                                <img src="{{ public_path('storage/' . $student->qr_code) }}" class="qr-img">
                            @else
                                <div class="qr-placeholder">
                                    QR Belum Dibuat
                                </div>
                            @endif

                            <div class="student-name">{{ $student->name }}</div>
                            <div class="student-nis">NIS: {{ $student->nis ?? '-' }}</div>
                            <div class="student-class">{{ $class->name }}</div>
                        </div>
                    </td>
                @endforeach
                @if ($row->count() == 1)
                    <td class="grid-td"></td>
                @endif
            </tr>
        @endforeach
    </table>

    <!-- FOOTER -->
    <div class="footer">
        SMKN 17 Jakarta &mdash; Sistem Absensi QR Code &bull; Halaman <span class="pagenum"></span>
    </div>

</body>
</html>
