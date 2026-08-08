<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>QR Code Siswa</title>

    <style>

        body {

            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            color: #333;

        }

        .container {

            width: 100%;
            text-align: center;

        }

        .title {

            font-size: 22px;
            font-weight: bold;
            margin-bottom: 5px;

        }

        .subtitle {

            font-size: 13px;
            color: #666;
            margin-bottom: 30px;

        }

        table {

            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;

        }

        table td {

            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;

        }

        .label {

            width: 170px;
            font-weight: bold;
            background: #f5f5f5;

        }

        .qr {

            text-align: center;
            margin-top: 20px;

        }

        .qr img {

            width: 250px;
            height: 250px;

        }

        .footer {

            margin-top: 30px;
            font-size: 12px;
            color: #777;

        }

    </style>

</head>

<body>

    <div class="container">

        <div class="title">

            SMKN 17 Jakarta

        </div>

        <div class="subtitle">

            QR Code Absensi Siswa

        </div>

    </div>

    <table>

        <tr>

            <td class="label">

                Nama Siswa

            </td>

            <td>

                {{ $siswa->name }}

            </td>

        </tr>

        <tr>

            <td class="label">

                NIS

            </td>

            <td>

                {{ $siswa->nis }}

            </td>

        </tr>

        <tr>

            <td class="label">

                Username

            </td>

            <td>

                {{ $siswa->username }}

            </td>

        </tr>

        <tr>

            <td class="label">

                Kelas

            </td>

            <td>

                {{ $siswa->schoolClass->name ?? '-' }}

            </td>

        </tr>

        <tr>

            <td class="label">

                Status

            </td>

            <td>

                {{ $siswa->status ? 'Aktif' : 'Nonaktif' }}

            </td>

        </tr>

    </table>

    <div class="qr">

        @if($siswa->qr_code)

            <img src="{{ public_path('storage/' . $siswa->qr_code) }}">

        @endif

    </div>

    <div class="footer">

        QR Code ini merupakan identitas resmi siswa dan digunakan untuk proses
        absensi di lingkungan SMKN 17 Jakarta.

        <br><br>

        Dicetak pada :
        {{ now()->format('d F Y H:i') }}

    </div>

</body>

</html>