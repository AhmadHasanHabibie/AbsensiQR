<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title') | SMKN 17 JAKARTA</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #0a1628;
            position: relative;
            overflow: hidden;
        }

        /* Unified Custom Scrollbar */
        html, body, div {
            scrollbar-width: thin;
            scrollbar-color: #334155 #0f172a;
        }

        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #0f172a;
        }

        ::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }

        /* Animated Background */
        body::before {
            content: '';
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 30% 50%, rgba(13, 110, 253, 0.15) 0%, transparent 50%),
                        radial-gradient(circle at 70% 50%, rgba(111, 66, 193, 0.1) 0%, transparent 50%),
                        radial-gradient(circle at 50% 80%, rgba(25, 135, 84, 0.08) 0%, transparent 50%);
            animation: bgShift 20s ease-in-out infinite;
            z-index: 0;
        }

        @keyframes bgShift {
            0%, 100% {
                transform: translate(0, 0) rotate(0deg);
            }
            25% {
                transform: translate(5%, 5%) rotate(2deg);
            }
            50% {
                transform: translate(-5%, -2%) rotate(-2deg);
            }
            75% {
                transform: translate(3%, -3%) rotate(1deg);
            }
        }

        /* Floating particles */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 15s infinite;
        }

        .particle:nth-child(1) { top: 20%; left: 10%; animation-delay: 0s; }
        .particle:nth-child(2) { top: 60%; left: 20%; animation-delay: 2s; width: 6px; height: 6px; }
        .particle:nth-child(3) { top: 30%; left: 80%; animation-delay: 4s; }
        .particle:nth-child(4) { top: 70%; left: 70%; animation-delay: 6s; width: 3px; height: 3px; }
        .particle:nth-child(5) { top: 80%; left: 40%; animation-delay: 8s; width: 5px; height: 5px; }
        .particle:nth-child(6) { top: 10%; left: 60%; animation-delay: 10s; }

        @keyframes float {
            0%, 100% {
                transform: translateY(0) scale(1);
                opacity: 0.3;
            }
            50% {
                transform: translateY(-50px) scale(1.5);
                opacity: 0.8;
            }
        }

        .login-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 440px;
            padding: 20px;
            animation: fadeInUp 0.6s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-card {
            border: none;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.3);
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
        }

        .login-header {
            text-align: center;
            padding: 40px 30px 20px;
            background: linear-gradient(180deg, #f8f9ff 0%, #ffffff 100%);
        }

        .login-header .logo-icon {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, #0d6efd, #6f42c1);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            font-size: 36px;
            color: #fff;
            box-shadow: 0 8px 30px rgba(13, 110, 253, 0.25);
        }

        .login-header h3 {
            font-weight: 800;
            font-size: 22px;
            color: #1a1a2e;
            margin-bottom: 4px;
        }

        .login-header p {
            color: #6c757d;
            font-size: 14px;
            margin: 0;
        }

        .login-body {
            padding: 20px 30px 40px;
        }

        /* Form Styles */
        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #495057;
            margin-bottom: 6px;
        }

        /* Hide native Edge browser password reveal button */
        input::-ms-reveal,
        input::-ms-clear {
            display: none !important;
        }

        .input-group {
            border-radius: 12px;
            overflow: hidden;
        }

        .input-group-text {
            background: #f8f9fa;
            border: 1.5px solid #e0e0e0;
            border-right: none;
            color: #6c757d;
            padding: 10px 14px;
            font-size: 16px;
        }

        .form-control {
            border-radius: 0 12px 12px 0;
            padding: 10px 14px;
            font-size: 14px;
            border: 1.5px solid #e0e0e0;
            border-left: none;
            transition: all 0.2s ease;
            height: 48px;
        }

        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.12);
        }

        .form-control:focus + .input-group-text,
        .input-group:focus-within .input-group-text {
            border-color: #0d6efd;
        }

        .btn-primary {
            height: 48px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 15px;
            background: linear-gradient(135deg, #0d6efd, #0a58ca);
            border: none;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(13, 110, 253, 0.35);
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            color: rgba(255, 255, 255, 0.6);
            font-size: 13px;
            position: relative;
            z-index: 1;
        }

        .alert {
            border: none;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 13px;
        }

        .alert-success {
            background: #d1e7dd;
            color: #0f5132;
        }

        .alert-danger {
            background: #f8d7da;
            color: #842029;
        }

        .text-danger.small {
            font-size: 12px;
            margin-top: 4px;
        }
    </style>

    @stack('css')
</head>

<body>

    {{-- TOAST NOTIFICATION CONTAINER --}}
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1095;">
        @if(session('success'))
            <div class="toast align-items-center text-white bg-success border-0 shadow-lg rounded-3 show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="4000">
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-center gap-2 fs-6">
                        <i class="bi bi-check-circle-fill fs-5"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="toast align-items-center text-white bg-danger border-0 shadow-lg rounded-3 show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-center gap-2 fs-6">
                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        @endif

        @if(session('warning'))
            <div class="toast align-items-center text-dark bg-warning border-0 shadow-lg rounded-3 show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="4500">
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-center gap-2 fs-6">
                        <i class="bi bi-exclamation-circle-fill fs-5"></i>
                        <span>{{ session('warning') }}</span>
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        @endif

        @if(session('info'))
            <div class="toast align-items-center text-white bg-info border-0 shadow-lg rounded-3 show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="4000">
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-center gap-2 fs-6">
                        <i class="bi bi-info-circle-fill fs-5"></i>
                        <span>{{ session('info') }}</span>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        @endif
    </div>

    <!-- Particles -->
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <div class="login-wrapper">

        <div class="card login-card">

            <div class="login-header">
                <div class="logo-icon">
                    <i class="bi bi-qr-code-scan"></i>
                </div>
                <h3>SMKN 17 JAKARTA</h3>
                <p>Sistem Absensi QR Code</p>
            </div>

            <div class="login-body">
                @yield('content')
            </div>

        </div>

        <div class="footer">
            © {{ date('Y') }} SMKN 17 JAKARTA
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize Bootstrap Toasts
        var toastElList = [].slice.call(document.querySelectorAll('.toast'));
        toastElList.map(function (toastEl) {
            var toast = new bootstrap.Toast(toastEl, { autohide: true });
            toast.show();
        });

        // Form Submission Loading Indicator
        document.querySelectorAll('form:not([data-no-loading])').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                if (form.checkValidity && !form.checkValidity()) {
                    return;
                }
                var submitBtn = form.querySelector('button[type="submit"]:not([data-no-loading])');
                if (submitBtn && !submitBtn.disabled) {
                    setTimeout(function() {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memproses...';
                    }, 10);
                }
            });
        });
    });
    </script>

    @stack('js')

</body>

</html>
