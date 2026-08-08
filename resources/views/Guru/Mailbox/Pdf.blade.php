<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        @page {
            margin: 30px 40px;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #111;
        }
        .header-table {
            width: 100%;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .logo {
            width: 70px;
            height: auto;
        }
        .school-title {
            text-align: center;
        }
        .school-title h2 {
            margin: 0;
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .school-title h3 {
            margin: 2px 0;
            font-size: 14pt;
            font-weight: bold;
        }
        .school-title p {
            margin: 0;
            font-size: 9pt;
            color: #333;
        }
        .letter-meta {
            width: 100%;
            margin-bottom: 20px;
        }
        .letter-meta td {
            padding: 2px 0;
            vertical-align: top;
        }
        .recipient {
            margin-bottom: 20px;
        }
        .content-body {
            text-align: justify;
            margin-bottom: 20px;
        }
        .meeting-table {
            width: 90%;
            margin: 15px auto;
            border-collapse: collapse;
        }
        .meeting-table td {
            border: 1px solid #666;
            padding: 8px 12px;
            font-size: 10.5pt;
        }
        .meeting-table td.label {
            background-color: #f5f5f5;
            font-weight: bold;
            width: 35%;
        }
        .signature-table {
            width: 100%;
            margin-top: 40px;
            page-break-inside: avoid;
        }
        .signature-table td {
            text-align: center;
            vertical-align: top;
        }
        .signature-space {
            height: 70px;
        }
        .badge-alpa {
            color: #d9534f;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <!-- KOP SURAT -->
    <table class="header-table">
        <tr>
            <td width="15%" class="text-center">
                <div style="font-size: 24pt; font-weight: bold; color: #198754; text-align: center;">17</div>
            </td>
            <td width="85%" class="school-title">
                <h2>PEMERINTAH PROVINSI DKI JAKARTA</h2>
                <h3>SMK NEGERI 17 JAKARTA</h3>
                <p>Jl. Slipi I No. 1, Palmerah, Jakarta Barat | Telp: (021) 5347890 | Website: smkn17jakarta.sch.id</p>
            </td>
        </tr>
    </table>

    <!-- NOMOR & TANGGAL SURAT -->
    <table class="letter-meta">
        <tr>
            <td width="15%"><strong>Nomor</strong></td>
            <td width="45%">: {{ $letterNumber }}</td>
            <td width="40%" style="text-align: right;">Jakarta, {{ $createdDate->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td><strong>Lampiran</strong></td>
            <td>: -</td>
            <td></td>
        </tr>
        <tr>
            <td><strong>Perihal</strong></td>
            <td>: <strong>{{ $title }}</strong></td>
            <td></td>
        </tr>
    </table>

    <!-- TUJUAN SURAT -->
    <div class="recipient">
        Kepada Yth.<br>
        <strong>Bapak / Ibu Orang Tua / Wali dari {{ $student->name }}</strong><br>
        Siswa Kelas {{ $class->name }} (NIS: {{ $student->nis ?? '-' }})<br>
        Di Tempat
    </div>

    <!-- ISI SURAT -->
    <div class="content-body">
        <p>Dengan hormat,</p>
        <p>
            Sehubungan dengan keikutsertaan siswa dalam Kegiatan Belajar Mengajar (KBM) di SMKN 17 Jakarta, berdasarkan catatan Sistem Rekapitulasi Absensi Sekolah, kami memberitahukan bahwa siswa yang bersangkutan tercatat memiliki
            @if(($mailType ?? 'alpha') === 'late')
                catatan <strong class="badge-alpa">KETERLAMBATAN</strong> sebanyak <strong>{{ $categoryTotal ?? 0 }} kali</strong>
            @elseif(($mailType ?? 'alpha') === 'permission')
                pengajuan <strong class="badge-alpa">IZIN</strong> sebanyak <strong>{{ $categoryTotal ?? 0 }} kali</strong>
            @else
                ketidakhadiran tanpa keterangan (<strong class="badge-alpa">ALFA</strong>) sebanyak <strong>{{ $categoryTotal ?? $alphaTotal ?? 0 }} kali</strong>
            @endif
            pada periode minggu <strong>{{ $weekStart->translatedFormat('d F Y') }} s/d {{ $weekEnd->translatedFormat('d F Y') }}</strong>.
        </p>

        <p>{{ $description }}</p>

        <p>Mengingat pentingnya hal tersebut demi kelancaran proses pendidikan putra/putri Bapak/Ibu, kami mengharapkan kehadiran Bapak/Ibu pada:</p>

        <!-- JADWAL PERTEMUAN -->
        <table class="meeting-table">
            <tr>
                <td class="label">Hari / Tanggal</td>
                <td>: {{ $meetingDate->translatedFormat('l, d F Y') }}</td>
            </tr>
            <tr>
                <td class="label">Pukul / Jam</td>
                <td>: {{ $meetingTime }} WIB</td>
            </tr>
            <tr>
                <td class="label">Tempat / Lokasi</td>
                <td>: {{ $meetingLocation }}</td>
            </tr>
            <tr>
                <td class="label">Menemui</td>
                <td>: {{ $teacher->name }} (Wali Kelas {{ $class->name }})</td>
            </tr>
            @if(!empty($notes))
            <tr>
                <td class="label">Catatan Tambahan</td>
                <td>: {{ $notes }}</td>
            </tr>
            @endif
        </table>

        <p>Demikian surat ini kami sampaikan. Atas perhatian dan kerja sama Bapak/Ibu Orang Tua/Wali Siswa, kami ucapkan terima kasih.</p>
    </div>

    <!-- TANDA TANGAN -->
    <table class="signature-table">
        <tr>
            <td width="50%"></td>
            <td width="50%">
                Wali Kelas {{ $class->name }}<br>
                SMKN 17 Jakarta
                <div class="signature-space"></div>
                <strong><u>{{ $teacher->name }}</u></strong><br>
                NIP. {{ $teacher->nip ?? '-' }}
            </td>
        </tr>
    </table>

</body>
</html>
