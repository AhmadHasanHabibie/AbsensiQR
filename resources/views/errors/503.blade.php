<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemeliharaan Sistem - Sistem Absensi QR Code</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #090d16 0%, #1e293b 100%);
            min-height: 100vh;
            color: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .maintenance-card {
            background: rgba(30, 41, 59, 0.9);
            backdrop-filter: blur(20px);
            border: 1px solid #334155;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
            max-width: 600px;
            width: 100%;
        }
        .logo-box {
            width: 86px;
            height: 86px;
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: #ffffff;
            font-size: 42px;
            box-shadow: 0 10px 20px rgba(2, 132, 199, 0.3);
        }
    </style>
</head>
<body>

    <div class="maintenance-card p-4 p-md-5 text-center">
        <!-- Logo / Icon Sistem -->
        <div class="logo-box">
            <i class="bi bi-qr-code-scan"></i>
        </div>

        <div class="fw-bold text-info text-uppercase font-monospace fs-12 letter-spacing-1 mb-1">
            Sistem Absensi QR Code
        </div>

        <h2 class="fw-bold text-white mb-2">Aplikasi sedang dalam proses pemeliharaan.</h2>
        <p class="text-secondary mb-4">
            {{ $maintenanceDetails['message'] ?? 'Sistem sedang dalam proses pemeliharaan rutin untuk meningkatkan kualitas dan stabilitas layanan aplikasi.' }}
        </p>

        <!-- Informasi Estimasi & Waktu Pemeliharaan -->
        <div class="p-3 bg-dark rounded-3 border border-secondary border-opacity-25 text-start mb-4">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-sm-6">
                    <div class="text-secondary small font-monospace"><i class="bi bi-clock me-1 text-warning"></i> Estimasi Selesai:</div>
                    <div class="fw-bold text-warning font-monospace fs-15 mt-1">
                        {{ $maintenanceDetails['estimate_completion'] ?? 'Segera' }}
                    </div>
                </div>
                <div class="col-12 col-sm-6 text-sm-end">
                    <div class="text-secondary small font-monospace"><i class="bi bi-calendar-event me-1 text-info"></i> Dimulai Pada:</div>
                    <div class="fw-bold text-info font-monospace fs-15 mt-1">
                        {{ isset($maintenanceDetails['activated_at']) && $maintenanceDetails['activated_at'] ? \Carbon\Carbon::parse($maintenanceDetails['activated_at'])->translatedFormat('d F Y, H:i') : now()->translatedFormat('d F Y') }}
                    </div>
                </div>
            </div>
        </div>

        <p class="text-secondary small mb-4">
            Silakan lakukan pembaruan halaman secara berkala untuk mengecek ketersediaan kembali layanan absensi.
        </p>

        <!-- Action Buttons (Dua Tombol: Refresh Halaman & Keluar) -->
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <button onclick="window.location.reload();" class="btn btn-info font-monospace fw-bold px-4 py-2 rounded-pill">
                <i class="bi bi-arrow-clockwise me-1"></i> Refresh Halaman
            </button>
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-danger font-monospace fw-bold px-4 py-2 rounded-pill">
                    <i class="bi bi-box-arrow-right me-1"></i> Keluar
                </button>
            </form>
        </div>
    </div>

</body>
</html>
