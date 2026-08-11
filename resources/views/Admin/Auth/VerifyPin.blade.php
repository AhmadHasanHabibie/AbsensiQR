<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi PIN - Administrator</title>
    
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            min-height: 100vh;
            color: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .pin-card {
            background: rgba(30, 27, 75, 0.85);
            backdrop-filter: blur(20px);
            border: 1px solid #3730a3;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
            max-width: 480px;
            width: 100%;
            animation: fadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .icon-box {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: #ffffff;
            font-size: 34px;
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3);
        }

        .icon-box.cooldown {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            box-shadow: 0 10px 20px rgba(220, 38, 38, 0.3);
        }

        .pin-input-group .form-control {
            font-size: 24px;
            letter-spacing: 12px;
            text-align: center;
            font-weight: 700;
            background: #0f172a !important;
            color: #818cf8 !important;
            border: 1.5px solid #4338ca;
            border-radius: 14px 0 0 14px !important;
            padding: 12px;
        }

        .pin-input-group .form-control:disabled {
            background: #1e1b4b !important;
            color: #64748b !important;
            cursor: not-allowed;
        }

        .pin-input-group .form-control:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.25);
        }

        .pin-input-group .btn-toggle-pin {
            border-radius: 0 14px 14px 0 !important;
            border: 1.5px solid #4338ca;
            border-left: none;
            background: #0f172a;
            color: #94a3b8;
            padding: 0 18px;
            transition: color 0.2s ease;
        }

        .pin-input-group .btn-toggle-pin:disabled {
            background: #1e1b4b;
            color: #475569;
            cursor: not-allowed;
        }

        .timer-display {
            font-size: 32px;
            font-weight: 800;
            letter-spacing: 2px;
            color: #f87171;
        }
    </style>
</head>
<body>

    @php
        $effectiveCooldown = session('cooldown_seconds', $cooldownSeconds ?? 0);
        $isLocked = $isCooldownActive || $effectiveCooldown > 0;
    @endphp

    <div class="pin-card p-4 p-md-5">
        <div class="text-center mb-4">
            <div class="icon-box {{ $isLocked ? 'cooldown' : '' }}">
                <i class="bi bi-{{ $isLocked ? 'lock-fill' : 'shield-lock-fill' }}"></i>
            </div>
            <span class="badge bg-{{ $isLocked ? 'danger' : 'primary' }}-subtle text-{{ $isLocked ? 'danger' : 'indigo' }} border border-{{ $isLocked ? 'danger' : 'primary' }}-subtle px-3 py-1 font-monospace fw-bold mb-2">
                {{ $isLocked ? 'ACCOUNT COOLDOWN LOCKED' : '2-STAGE AUTHENTICATION' }}
            </span>
            <h3 class="fw-bold text-white mb-2">Verifikasi PIN Admin</h3>
            <p class="text-secondary small mb-0">
                {{ $isLocked ? 'Akun Administrator dikunci sementara demi keamanan sistem.' : 'Masukkan PIN keamanan 6-digit untuk melanjutkan ke Dashboard Administrator.' }}
            </p>
        </div>

        <!-- ERROR & ATTEMPT NOTIFICATION -->
        @if($errors->any())
        <div class="alert alert-danger bg-danger bg-opacity-10 border-danger text-danger alert-dismissible fade show rounded-3 p-3 mb-4" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill fs-5 flex-shrink-0"></i>
                <div class="small font-semibold">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- COOLDOWN TIMER DISPLAY CARD -->
        @if($isLocked)
        <div class="p-3 bg-dark bg-opacity-75 rounded-3 border border-danger border-opacity-50 text-center mb-4">
            <div class="text-secondary small font-monospace text-uppercase mb-1">
                <i class="bi bi-hourglass-split me-1 text-danger"></i> Silakan coba kembali dalam:
            </div>
            <div class="timer-display font-monospace" id="countdownTimer">
                {{ sprintf('%02d:%02d', floor($effectiveCooldown / 60), $effectiveCooldown % 60) }}
            </div>
            <div class="progress bg-secondary bg-opacity-25 mt-3" style="height: 6px;">
                <div class="progress-bar bg-danger progress-bar-striped progress-bar-animated" id="cooldownProgressBar" style="width: 100%;"></div>
            </div>
        </div>
        @endif

        <!-- PIN INPUT FORM -->
        <form action="{{ route('admin.pin.process') }}" method="POST" autocomplete="off" id="pinForm">
            @csrf

            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label for="pinInput" class="form-label text-secondary small font-monospace mb-0">PIN Keamanan (6 Digit Angka)</label>
                    @if(!$isLocked && (session('attempts_remaining') || isset($attemptsRemaining)))
                        @php $rem = session('attempts_remaining', $attemptsRemaining ?? 3); @endphp
                        @if($rem < 3)
                        <span class="badge bg-warning text-dark font-monospace fw-bold">Sisa Percobaan: {{ $rem }}</span>
                        @endif
                    @endif
                </div>

                <div class="input-group pin-input-group">
                    <input type="password" 
                           name="pin" 
                           id="pinInput" 
                           maxlength="6" 
                           inputmode="numeric" 
                           pattern="[0-9]*" 
                           class="form-control font-monospace @error('pin') is-invalid @enderror" 
                           placeholder="••••••" 
                           {{ $isLocked ? 'disabled' : '' }}
                           required 
                           autofocus>
                    <button class="btn btn-toggle-pin" type="button" id="togglePinBtn" {{ $isLocked ? 'disabled' : '' }} title="Tampilkan/Sembunyikan PIN">
                        <i class="bi bi-eye-slash-fill" id="togglePinIcon"></i>
                    </button>
                </div>
            </div>

            <div class="d-grid gap-2 mb-3">
                <button type="submit" id="submitBtn" class="btn btn-primary font-monospace fw-bold py-3 rounded-3 shadow" style="background-color: #4f46e5; border-color: #4338ca;" {{ $isLocked ? 'disabled' : '' }}>
                    <i class="bi bi-key-fill me-1"></i> {{ $isLocked ? 'Akun Terkunci (Cooldown)' : 'Verifikasi & Masuk Dashboard' }}
                </button>
            </div>
        </form>

        <div class="text-center border-top border-secondary border-opacity-25 pt-3 mt-4">
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-link text-secondary text-decoration-none small">
                    <i class="bi bi-box-arrow-left me-1"></i> Batal & Logout Administrator
                </button>
            </form>
        </div>
    </div>

    <!-- Bootstrap 5 & Realtime JS Countdown -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Eye Icon Toggle
            const pinInput = document.getElementById('pinInput');
            const togglePinBtn = document.getElementById('togglePinBtn');
            const togglePinIcon = document.getElementById('togglePinIcon');

            if (togglePinBtn && pinInput && togglePinIcon) {
                togglePinBtn.addEventListener('click', function () {
                    if (pinInput.type === 'password') {
                        pinInput.type = 'text';
                        togglePinIcon.classList.remove('bi-eye-slash-fill');
                        togglePinIcon.classList.add('bi-eye-fill');
                    } else {
                        pinInput.type = 'password';
                        togglePinIcon.classList.remove('bi-eye-fill');
                        togglePinIcon.classList.add('bi-eye-slash-fill');
                    }
                });
            }

            // Realtime Countdown Timer JavaScript (Server-side synced)
            let secondsRemaining = parseInt("{{ $effectiveCooldown }}", 10) || 0;
            const totalDuration = 180; // 3 minutes total

            if (secondsRemaining > 0) {
                const timerDisplay = document.getElementById('countdownTimer');
                const progressBar = document.getElementById('cooldownProgressBar');
                const submitBtn = document.getElementById('submitBtn');

                const interval = setInterval(function () {
                    secondsRemaining--;

                    if (secondsRemaining <= 0) {
                        clearInterval(interval);
                        if (timerDisplay) timerDisplay.textContent = "00:00";
                        if (progressBar) progressBar.style.width = "0%";

                        // Refresh page to allow PIN entry
                        window.location.reload();
                    } else {
                        const minutes = Math.floor(secondsRemaining / 60);
                        const seconds = secondsRemaining % 60;
                        const formatted = String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');

                        if (timerDisplay) timerDisplay.textContent = formatted;
                        if (progressBar) {
                            const percent = (secondsRemaining / totalDuration) * 100;
                            progressBar.style.width = percent.toFixed(1) + "%";
                        }
                    }
                }, 1000);
            }
        });
    </script>
</body>
</html>
