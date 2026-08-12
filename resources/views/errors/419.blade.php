<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 - Sesi Berakhir | Sistem Absensi QR Code</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #090d16 0%, #0f172a 50%, #1e293b 100%);
            min-height: 100vh;
            color: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            margin: 0;
            overflow-x: hidden;
        }
        .error-card {
            background: rgba(30, 41, 59, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 28px;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.7);
            max-width: 580px;
            width: 100%;
            position: relative;
            z-index: 10;
        }
        .error-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(245, 158, 11, 0.15);
            border: 1px solid rgba(245, 158, 11, 0.3);
            color: #fde047;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1px;
            padding: 6px 16px;
            border-radius: 50px;
            text-transform: uppercase;
        }
        .icon-box {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            border: 3px solid rgba(255, 255, 255, 0.2);
            border-radius: 26px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            color: #ffffff;
            font-size: 44px;
            box-shadow: 0 12px 30px rgba(217, 119, 6, 0.35);
        }
        .btn-action {
            transition: all 0.25s ease;
        }
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>

    <div class="error-card p-4 p-sm-5 text-center">
        <div class="icon-box">
            <i class="bi bi-clock-history"></i>
        </div>

        <div class="error-badge mb-3">
            <i class="bi bi-hourglass-split"></i> HTTP Status 419
        </div>

        <h2 class="fw-bold text-white mb-2 fs-3">Sesi Halaman Berakhir</h2>
        
        <p class="text-white-50 mb-4 fs-6">
            Sesi keamanan form Anda telah kedaluwarsa karena tidak ada aktivitas untuk beberapa waktu. Silakan muat ulang halaman untuk memperbarui token keamanan.
        </p>

        @php
            $dashboardUrl = url('/');
            if (Auth::check()) {
                $role = Auth::user()->role;
                if ($role === 'super_admin') $dashboardUrl = route('superadmin.dashboard');
                elseif ($role === 'admin') $dashboardUrl = route('admin.dashboard');
                elseif ($role === 'operator') $dashboardUrl = route('operator.dashboard');
                elseif ($role === 'teacher') $dashboardUrl = route('guru.dashboard');
                elseif ($role === 'piket' || $role === 'guru_piket') $dashboardUrl = route('piket.dashboard');
                elseif ($role === 'student') $dashboardUrl = route('siswa.dashboard');
            }
        @endphp

        <div class="d-flex flex-column flex-sm-row align-items-center justify-content-center gap-2 mt-2">
            <button onclick="reloadCurrentPage()" class="btn btn-warning text-dark px-4 py-2.5 rounded-pill fw-bold btn-action w-100 w-sm-auto">
                <i class="bi bi-arrow-clockwise me-1"></i> Muat Ulang & Refresh Token
            </button>
            <a href="{{ $dashboardUrl }}" class="btn btn-primary px-4 py-2.5 rounded-pill fw-bold btn-action w-100 w-sm-auto">
                <i class="bi bi-house-door-fill me-1"></i> Dashboard Utama
            </a>
            <button onclick="window.history.back()" class="btn btn-outline-light px-4 py-2.5 rounded-pill fw-semibold btn-action w-100 w-sm-auto">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </button>
        </div>

        <div class="mt-4 pt-3 border-top border-secondary border-opacity-25 text-white-50 small">
            SMKN 17 Jakarta &mdash; Sistem Absensi QR Code
        </div>
    </div>

    <script>
        function reloadCurrentPage() {
            window.location.reload();
        }
    </script>
</body>
</html>
