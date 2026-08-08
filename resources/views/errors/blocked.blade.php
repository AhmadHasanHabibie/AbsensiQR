<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Diblokir - Sistem Absensi QR Code</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-card {
            max-width: 500px;
            width: 100%;
        }
    </style>
</head>
<body>

<div class="container p-3">
    <div class="card error-card mx-auto border-0 shadow-lg rounded-4 overflow-hidden text-center p-4">
        <div class="card-body">
            <div style="width:80px;height:80px;background:#fff0f1;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;margin-bottom:20px;">
                <i class="bi bi-shield-x text-danger display-4"></i>
            </div>

            <h3 class="fw-bold text-dark mb-2">Akses Diblokir</h3>
            
            <p class="text-muted leading-relaxed mb-4">
                Sistem mendeteksi aktivitas yang tidak wajar dari perangkat Anda.<br>
                Silakan coba kembali beberapa saat lagi.
            </p>

            <div class="p-3 bg-light rounded-3 text-secondary small mb-4">
                <i class="bi bi-info-circle me-1"></i> Apabila Anda merasa ini adalah kesalahan, silakan hubungi Administrator Sekolah.
            </div>

            <a href="{{ route('login') }}" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Login
            </a>
        </div>
    </div>
</div>

</body>
</html>
