@extends('Layouts.LayoutOperator')

@section('title', 'Scan QR Absensi Otomatis')

@section('content')

{{-- ============================================================ --}}
{{-- HOLIDAY LOCK MODAL (TAHAP 4) --}}
{{-- ============================================================ --}}
@if ($dailyStatus['is_holiday'])
<div class="modal fade" id="holidayLockModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="holidayLockModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-danger text-white rounded-top-4 p-3">
                <h5 class="modal-title fw-bold mb-0" id="holidayLockModalLabel">
                    <i class="bi bi-calendar-x-fill me-2"></i> Maaf Sedang Libur
                </h5>
                <button type="button" id="holidayModalCloseBtn" class="btn-close btn-close-white" aria-label="Kembali ke Dashboard"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <div style="width:72px;height:72px;background:rgba(220,53,69,0.1);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                        <i class="bi bi-moon-stars-fill text-danger" style="font-size:32px;"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Scan QR Tidak Tersedia</h5>
                    <p class="text-muted mb-1">Hari ini adalah <strong class="text-danger">{{ $dailyStatus['status'] }}</strong>.</p>
                    <p class="text-muted small mb-0">Fitur Scan QR hanya aktif pada hari belajar. Silakan kembali ke Dashboard.</p>
                </div>
                <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">
                    <i class="bi bi-info-circle me-1"></i> {{ \Carbon\Carbon::now('Asia/Jakarta')->isoFormat('dddd, D MMMM YYYY') }}
                </span>
            </div>
            <div class="modal-footer bg-light rounded-bottom-4 p-3 d-flex justify-content-center">
                <a href="{{ route('operator.dashboard') }}" class="btn btn-danger px-5 fw-semibold rounded-pill">
                    <i class="bi bi-arrow-left me-2"></i>Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var holidayModal = new bootstrap.Modal(document.getElementById('holidayLockModal'));
        holidayModal.show();
        document.getElementById('holidayModalCloseBtn').addEventListener('click', function () {
            window.location.href = '{{ route("operator.dashboard") }}';
        });
    });
</script>
@endif

{{-- ============================================================ --}}
{{-- CENTERED SCAN RESULT MODAL (POP-UP DITENGAH) --}}
{{-- ============================================================ --}}
<div class="modal fade" id="scanResultModal" tabindex="-1" aria-labelledby="scanResultModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div id="modal-header-bg" class="p-4 text-center text-white">
                <div id="modal-icon-container" class="mb-2"></div>
                <h4 id="modal-title" class="fw-bold mb-1"></h4>
                <p id="modal-subtitle" class="mb-0 small opacity-75"></p>
            </div>
            <div id="modal-body-content" class="modal-body p-4 text-center">
                {{-- Dynamically Populated --}}
            </div>
            <div class="modal-footer bg-light p-3 justify-content-center border-0">
                <button type="button" class="btn btn-secondary px-4 rounded-pill fw-semibold" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid py-2">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Scan QR Absensi Otomatis</h3>
            <p class="text-muted mb-0">Sistem Absensi Berbasis Waktu Server (03:00:00 – 06:30:59 WIB)</p>
        </div>

        <div class="d-flex align-items-center gap-3">
            {{-- Status Scan Badge (Live System Time) --}}
            <span id="status-badge" class="badge fs-6 px-3 py-2 {{ $isScanOpen ? 'bg-success' : 'bg-danger' }}">
                <i class="bi {{ $isScanOpen ? 'bi-check-circle-fill' : 'bi-clock-history' }} me-1"></i>
                <span id="status-badge-text">
                    @if($isScanOpen)
                        DIBUKA OTOMATIS (03:00 - 06:30 WIB)
                    @elseif($isPastLimit)
                        DITUTUP OTOMATIS (Pukul 06:31 WIB)
                    @else
                        BELUM DIBUKA (Pukul 03:00 WIB)
                    @endif
                </span>
            </span>

            {{-- Tanggal Hari Ini & Live Server Clock --}}
            <div class="text-end d-none d-sm-block">
                <div class="fw-semibold text-dark">{{ \Carbon\Carbon::now('Asia/Jakarta')->isoFormat('D MMMM YYYY') }}</div>
                <div id="realtime-clock" class="text-muted small font-monospace fw-bold">{{ \Carbon\Carbon::now('Asia/Jakarta')->format('H:i:s') }} WIB</div>
            </div>
        </div>
    </div>

    {{-- Countdown Banner when Scan is Open --}}
    <div id="countdown-banner" class="alert alert-primary border-0 shadow-sm d-flex align-items-center justify-content-between p-3 mb-4 rounded-4 {{ $isScanOpen ? '' : 'd-none' }}">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-stopwatch fs-3 text-primary me-1"></i>
            <div>
                <strong class="d-block text-dark">Sisa Waktu Scan QR Tepat Waktu</strong>
                <small class="text-muted">Pintu absensi QR akan ditutup otomatis oleh sistem pada pukul 06:31:00 WIB.</small>
            </div>
        </div>
        <div class="bg-white px-3 py-1.5 rounded-3 border shadow-sm text-end">
            <small class="text-muted d-block" style="font-size: 11px;">COUNTDOWN</small>
            <div id="scan-countdown-timer" class="font-monospace fw-bold fs-5 text-danger">00:00:00</div>
        </div>
    </div>

    {{-- Alert Container --}}
    <div id="warning-alert-container">
        @if($isPastLimit)
            <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center gap-2 mb-4 rounded-4">
                <span class="badge bg-danger text-white px-2.5 py-1.5 me-1"><i class="bi bi-clock-fill me-1"></i> Waktu Absensi Berakhir</span>
                <span>Waktu absensi QR tepat waktu telah berakhir pukul 06.30 WIB. Pemindaian QR Code kini ditutup secara otomatis oleh sistem.</span>
            </div>
        @elseif(!$isScanOpen)
            <div class="alert alert-info border-0 shadow-sm d-flex align-items-center gap-2 mb-4 rounded-4">
                <span class="badge bg-primary text-white px-2.5 py-1.5 me-1"><i class="bi bi-info-circle-fill me-1"></i> Informasi Jadwal</span>
                <span>Sistem absensi QR belum dibuka. Pintu absensi akan terbuka otomatis tepat pada pukul 03:00:00 WIB.</span>
            </div>
        @endif
    </div>

    {{-- Main Centered Container --}}
    <div class="row justify-content-center">
        <div class="col-12 col-md-12 col-lg-10 col-xl-9">

            {{-- Scanner Card --}}
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-primary text-white p-3 text-center d-flex justify-content-between align-items-center">
                    <div class="mx-auto fw-semibold">
                        <i class="bi bi-qr-code-scan me-2"></i> Scanner QR Code Siswa
                    </div>
                </div>
                <div class="card-body p-4 text-center">

                    {{-- Header Instruction --}}
                    <div class="mb-3">
                        <p class="fw-semibold text-dark mb-0 fs-6">Arahkan QR Code Siswa ke dalam area pemindaian.</p>
                        <small class="text-muted">Kamera memindai otomatis. Gunakan tombol kontrol untuk ganti kamera / mode layar penuh.</small>
                    </div>

                    {{-- Scanner Placeholder (Closed State) --}}
                    <div id="scanner-placeholder" class="py-5 bg-dark text-white rounded-4 scanner-responsive-box {{ $isScanOpen ? 'd-none' : '' }}" style="display:flex; flex-direction:column; justify-content:center; align-items:center;">
                        <i class="bi bi-shield-lock display-1 text-secondary opacity-50 mb-2"></i>
                        <h5 class="mt-2 text-white fw-bold" id="placeholder-title">
                            @if($isPastLimit)
                                Waktu Absensi QR Telah Berakhir
                            @else
                                Absensi QR Belum Dibuka
                            @endif
                        </h5>
                        <p class="text-muted small mb-0 px-3 text-center" id="placeholder-subtitle">
                            @if($isPastLimit)
                                Sesuai ketentuan, absensi tepat waktu secara otomatis ditutup pada pukul 06:31:00 WIB.
                            @else
                                Pintu absensi QR akan dibuka otomatis oleh sistem tepat pukul 03:00:00 WIB.
                            @endif
                        </p>
                    </div>

                    {{-- Scanner Camera Wrapper (Active State) --}}
                    <div id="reader-wrapper" class="position-relative mx-auto rounded-4 overflow-hidden bg-dark scanner-responsive-box {{ $isScanOpen ? '' : 'd-none' }}" style="width:100%;">
                        {{-- Controls overlay bar --}}
                        <div class="scanner-controls-overlay position-absolute top-0 start-0 end-0 p-3 d-flex justify-content-between align-items-center" style="z-index: 10; background: linear-gradient(to bottom, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0) 100%);">
                            <span class="badge bg-success bg-opacity-90 text-white fw-medium font-monospace fs-7 shadow-sm d-flex align-items-center gap-1.5 px-2.5 py-1.5">
                                <span class="pulse-dot"></span> LIVE SCANNER
                            </span>
                            <div class="d-flex gap-2">
                                <button id="btn-switch-camera" type="button" class="btn btn-dark btn-sm rounded-pill px-3 shadow-sm border border-secondary border-opacity-50 text-white" title="Ganti Kamera (Depan / Belakang)">
                                    <i class="bi bi-camera-fill me-1 text-warning"></i> <span id="camera-label-text">Kamera Belakang</span>
                                </button>
                                <button id="btn-fullscreen-toggle" type="button" class="btn btn-dark btn-sm rounded-circle shadow-sm border border-secondary border-opacity-50 text-white" style="width:36px; height:36px;" title="Mode Layar Penuh (Fullscreen)">
                                    <i class="bi bi-fullscreen"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Scanner Viewfinder Overlay Frame --}}
                        <div class="scanner-viewfinder-overlay position-absolute top-0 start-0 end-0 bottom-0 pointer-events-none d-flex align-items-center justify-content-center" style="z-index: 5;">
                            <div class="scanner-box-frame">
                            </div>
                        </div>

                        <div id="reader" style="width:100%;"></div>
                    </div>

                    {{-- Sub Instruction --}}
                    <div class="mt-3">
                        <small class="text-muted"><i class="bi bi-shield-check text-success me-1"></i> Sistem memverifikasi waktu server secara real-time. Notifikasi scan akan muncul otomatis di tengah layar.</small>
                    </div>

                </div>
            </div>

            {{-- Hasil Scan Card --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-warning text-dark p-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-person-check me-2"></i> Hasil Scan Terakhir
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div id="scan-result" class="text-center text-muted py-3">
                        @if($isPastLimit)
                            <div class="alert alert-warning mb-0 rounded-3 text-center shadow-sm">
                                <i class="bi bi-alarm-fill fs-1 text-warning"></i>
                                <h6 class="mt-2 fw-bold text-dark">Waktu Absensi QR Berakhir</h6>
                                <p class="mb-3 small text-muted">Seluruh siswa yang belum melakukan scan QR otomatis tercatat belum hadir. Apabila ada siswa yang baru tiba, silakan catat melalui menu <strong>Data Terlambat</strong>.</p>
                                <a href="{{ route('operator.terlambat.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm rounded-pill">
                                    <i class="bi bi-plus-circle me-1"></i> Input Data Terlambat
                                </a>
                            </div>
                        @else
                            <i class="bi bi-person-badge display-4 opacity-50"></i>
                            <p class="mt-3 mb-0">Belum ada QR Code yang dipindai.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Informasi System Controlled Attendance Card --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-info text-white p-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-info-circle me-2"></i> Ketentuan Absensi Otomatis Berbasis Waktu
                    </h5>
                </div>
                <div class="card-body p-4">
                    <ul class="mb-0 text-muted">
                        <li class="mb-2"><strong>Buka Otomatis:</strong> Sistem membuka scanner QR tepat pukul <strong>03:00:00 WIB</strong>.</li>
                        <li class="mb-2"><strong>Batas Tepat Waktu:</strong> Absensi dianggap tepat waktu sampai pukul <strong>06:30:59 WIB</strong>.</li>
                        <li class="mb-2"><strong>Tutup Otomatis:</strong> Sistem menutup total pemindaian tepat pukul <strong>06:31:00 WIB</strong>. Tidak ada tombol manual.</li>
                        <li class="mb-0"><strong>Siswa Terlambat:</strong> Siswa yang hadir setelah 06:30 WIB wajib diproses melalui menu <strong>Data Terlambat</strong> oleh Operator.</li>
                    </ul>
                </div>
            </div>

            {{-- Riwayat Scan Hari Ini Card --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-dark text-white p-3">
                    <h5 class="mb-0 fw-semibold fs-6">
                        <i class="bi bi-clock-history me-2"></i> Riwayat Scan Hari Ini
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 400px;">
                        <table class="table table-hover align-middle mb-0 text-sm" id="table-history">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th class="ps-4">Siswa</th>
                                    <th>Kelas</th>
                                    <th>Jam Scan</th>
                                    <th class="pe-4 text-end">Status</th>
                                </tr>
                            </thead>
                            <tbody id="history-list">
                                @forelse($recentScans as $scan)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold text-dark">{{ optional($scan->student)->name }}</div>
                                        </td>
                                        <td>{{ optional(optional($scan->student)->schoolClass)->name ?? '-' }}</td>
                                        <td class="text-nowrap small fw-semibold font-monospace">
                                            {{ \Carbon\Carbon::parse($scan->check_in)->format('H:i:s') }} WIB
                                        </td>
                                        <td class="pe-4 text-end">
                                            <span class="badge bg-success">Hadir</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="empty-history-row">
                                        <td colspan="4" class="text-center text-muted py-4 small">
                                            Belum ada absensi dipindai hari ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

@endsection

@push('css')
<style>
    .pulse-dot {
        width: 8px;
        height: 8px;
        background-color: #22c55e;
        border-radius: 50%;
        display: inline-block;
        animation: pulseAnimation 1.5s infinite;
    }
    @keyframes pulseAnimation {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(34, 197, 94, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
    }

    .scanner-responsive-box, #reader, #scanner-placeholder {
        min-height: 380px;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background-color: #0f172a !important;
    }
    @media (min-width: 992px) {
        .scanner-responsive-box, #reader, #scanner-placeholder {
            min-height: 520px;
        }
    }
    #reader {
        width: 100% !important;
        height: 100% !important;
        border: none !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    #reader__scan_region {
        width: 100% !important;
        height: 100% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        position: relative !important;
    }
    #reader video,
    #reader__scan_region video {
        width: 100% !important;
        height: 100% !important;
        max-width: 100% !important;
        max-height: 100% !important;
        object-fit: cover !important;
        object-position: center center !important;
        margin-left: auto !important;
        margin-right: auto !important;
        display: block !important;
    }

    .scanner-box-frame {
        width: 250px;
        height: 250px;
        border: 2.5px solid rgba(255, 255, 255, 0.5);
        box-shadow: 0 0 0 4000px rgba(0, 0, 0, 0.5);
        border-radius: 20px;
        position: relative;
        overflow: hidden;
    }
    @media (min-width: 576px) {
        .scanner-box-frame {
            width: 290px;
            height: 290px;
        }
    }

    .scanner-line {
        display: none !important;
    }

    .scanner-fullscreen-active {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        max-height: 100vh !important;
        z-index: 99999 !important;
        border-radius: 0 !important;
    }
</style>
@endpush

@push('js')
<script src="{{ asset('assets/js/html5-qrcode.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    let html5QrCode = null;
    let isScanActive = {{ $isScanOpen ? 'true' : 'false' }};
    let processing = false;
    let availableCameras = [];
    let currentCameraIndex = 0;
    let modalInstance = null;
    let modalTimeout = null;

    // Web Audio Synthesizer Beep Feedback
    function playAudioBeep(type = 'success') {
        try {
            const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.connect(gain);
            gain.connect(audioCtx.destination);

            if (type === 'success') {
                osc.frequency.setValueAtTime(587.33, audioCtx.currentTime); // D5
                osc.frequency.setValueAtTime(880, audioCtx.currentTime + 0.1); // A5
                gain.gain.setValueAtTime(0.3, audioCtx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.3);
                osc.start(audioCtx.currentTime);
                osc.stop(audioCtx.currentTime + 0.3);
            } else {
                osc.type = 'sawtooth';
                osc.frequency.setValueAtTime(300, audioCtx.currentTime);
                osc.frequency.setValueAtTime(150, audioCtx.currentTime + 0.15);
                gain.gain.setValueAtTime(0.3, audioCtx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.35);
                osc.start(audioCtx.currentTime);
                osc.stop(audioCtx.currentTime + 0.35);
            }
        } catch(e) {}
    }

    // Realtime Server Time Tracking
    function checkSystemTime() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const timeStr = `${hours}:${minutes}:${seconds} WIB`;
        
        const clockElem = document.getElementById('realtime-clock');
        if (clockElem) {
            clockElem.textContent = timeStr;
        }

        const cdTimer = document.getElementById('scan-countdown-timer');
        if (cdTimer) {
            const targetTime = new Date(now);
            targetTime.setHours(6, 31, 0, 0);
            const diff = targetTime.getTime() - now.getTime();

            if (diff > 0 && now.getHours() === 6 && now.getMinutes() < 31) {
                const totalSecs = Math.floor(diff / 1000);
                const h = String(Math.floor(totalSecs / 3600)).padStart(2, '0');
                const m = String(Math.floor((totalSecs % 3600) / 60)).padStart(2, '0');
                const s = String(totalSecs % 60).padStart(2, '0');
                cdTimer.textContent = `${h}:${m}:${s}`;
            } else {
                cdTimer.textContent = "00:00:00";
            }
        }

        const currentTimeVal = `${hours}:${minutes}:${seconds}`;
        const isTestingActive = {{ \App\Services\AttendanceTimeService::isTestingModeActive() ? 'true' : 'false' }};
        const shouldBeOpen = isTestingActive || (currentTimeVal >= '03:00:00' && currentTimeVal <= '06:30:59');

        if (shouldBeOpen !== isScanActive) {
            isScanActive = shouldBeOpen;
            updateScannerSystemState(currentTimeVal);
        }
    }

    function updateScannerSystemState(timeVal) {
        const badge = document.getElementById('status-badge');
        const badgeText = document.getElementById('status-badge-text');
        const placeholder = document.getElementById('scanner-placeholder');
        const wrapper = document.getElementById('reader-wrapper');
        const pTitle = document.getElementById('placeholder-title');
        const pSubtitle = document.getElementById('placeholder-subtitle');
        const cdBanner = document.getElementById('countdown-banner');
        const isTestingActive = {{ \App\Services\AttendanceTimeService::isTestingModeActive() ? 'true' : 'false' }};

        if (isTestingActive || (timeVal >= '03:00:00' && timeVal <= '06:30:59')) {
            if (badge) badge.className = 'badge fs-6 px-3 py-2 bg-success';
            if (badgeText) badgeText.textContent = isTestingActive ? 'MODE TESTING AKTIF (KAMERA TERBUKA)' : 'DIBUKA OTOMATIS (03:00 - 06:30 WIB)';
            if (placeholder) placeholder.classList.add('d-none');
            if (wrapper) wrapper.classList.remove('d-none');
            if (cdBanner) cdBanner.classList.remove('d-none');
            startScanner();
        } else if (timeVal >= '06:31:00') {
            if (badge) badge.className = 'badge fs-6 px-3 py-2 bg-danger';
            if (badgeText) badgeText.textContent = 'DITUTUP OTOMATIS (Pukul 06:31 WIB)';
            if (placeholder) placeholder.classList.remove('d-none');
            if (wrapper) wrapper.classList.add('d-none');
            if (cdBanner) cdBanner.classList.add('d-none');
            if (pTitle) pTitle.textContent = 'Waktu Scan QR Telah Berakhir';
            if (pSubtitle) pSubtitle.textContent = 'Sesuai ketentuan, absensi tepat waktu secara otomatis ditutup pada pukul 06:31:00 WIB. Kamera scanner telah dimatikan.';
            stopScanner();

            const scanResult = document.getElementById('scan-result');
            if (scanResult && !scanResult.querySelector('.alert-warning')) {
                scanResult.innerHTML = `
                    <div class="alert alert-warning mb-0 rounded-3 text-center shadow-sm">
                        <i class="bi bi-alarm-fill fs-1 text-warning"></i>
                        <h6 class="mt-2 fw-bold text-dark">Waktu Absensi QR Berakhir</h6>
                        <p class="mb-3 small text-muted">Seluruh siswa yang belum melakukan scan QR otomatis tercatat belum hadir. Silakan akses menu <strong>Data Terlambat</strong> untuk mencatat siswa yang terlambat.</p>
                        <a href="{{ route('operator.terlambat.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm rounded-pill">
                            <i class="bi bi-plus-circle me-1"></i> Input Data Terlambat
                        </a>
                    </div>
                `;
            }
        } else {
            if (badge) badge.className = 'badge fs-6 px-3 py-2 bg-danger';
            if (badgeText) badgeText.textContent = 'BELUM DIBUKA (Pukul 03:00 WIB)';
            if (placeholder) placeholder.classList.remove('d-none');
            if (wrapper) wrapper.classList.add('d-none');
            if (cdBanner) cdBanner.classList.add('d-none');
            if (pTitle) pTitle.textContent = 'Absensi QR Belum Dibuka';
            if (pSubtitle) pSubtitle.textContent = 'Pintu absensi QR akan dibuka otomatis oleh sistem tepat pukul 03:00:00 WIB.';
            stopScanner();
        }
    }

    setInterval(checkSystemTime, 1000);

    // Initialize HTML5 Scanner with Back Camera Default & Camera Switcher
    function startScanner() {
        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode("reader");
        }

        if (html5QrCode.isScanning) return;

        Html5Qrcode.getCameras()
            .then(cameras => {
                if (!cameras.length) {
                    showErrorModal("Kamera tidak ditemukan pada perangkat Anda.");
                    return;
                }

                availableCameras = cameras;

                // Pick environment / back camera by default
                let backCamIdx = cameras.findIndex(c => {
                    const label = (c.label || '').toLowerCase();
                    return label.includes('back') || label.includes('rear') || label.includes('environment') || label.includes('belakang');
                });

                if (backCamIdx !== -1) {
                    currentCameraIndex = backCamIdx;
                } else if (cameras.length > 1) {
                    currentCameraIndex = cameras.length - 1; // back camera is usually last on mobile
                } else {
                    currentCameraIndex = 0;
                }

                startCameraWithConfig();
            })
            .catch(() => {
                // Fallback to facingMode environment
                startCameraWithFacingMode();
            });
    }

    function startCameraWithConfig() {
        const cameraId = availableCameras[currentCameraIndex].id;
        const camLabel = availableCameras[currentCameraIndex].label || 'Kamera ' + (currentCameraIndex + 1);
        const cameraLabelElem = document.getElementById('camera-label-text');
        if (cameraLabelElem) {
            cameraLabelElem.textContent = camLabel.length > 18 ? camLabel.substring(0, 15) + '...' : camLabel;
        }

        html5QrCode.start(
            cameraId,
            {
                fps: 20,
                qrbox: function(viewfinderWidth, viewfinderHeight) {
                    const minDim = Math.min(viewfinderWidth, viewfinderHeight);
                    return { width: Math.floor(minDim * 0.75), height: Math.floor(minDim * 0.75) };
                }
            },
            onScanSuccess,
            onScanFailure
        ).catch(err => {
            // Fallback facingMode if camera ID fail
            startCameraWithFacingMode();
        });
    }

    function startCameraWithFacingMode() {
        html5QrCode.start(
            { facingMode: "environment" },
            {
                fps: 20,
                qrbox: function(viewfinderWidth, viewfinderHeight) {
                    const minDim = Math.min(viewfinderWidth, viewfinderHeight);
                    return { width: Math.floor(minDim * 0.75), height: Math.floor(minDim * 0.75) };
                }
            },
            onScanSuccess,
            onScanFailure
        ).catch(err => {
            showErrorModal("Gagal mengakses kamera belakang.");
        });
    }

    function stopScanner() {
        if (html5QrCode && html5QrCode.isScanning) {
            html5QrCode.stop().catch(() => {});
        }
    }

    // Switch Camera Handler
    document.getElementById('btn-switch-camera')?.addEventListener('click', function() {
        if (!availableCameras || availableCameras.length < 2) {
            // If API didn't list cameras, toggle facingMode
            if (html5QrCode && html5QrCode.isScanning) {
                html5QrCode.stop().then(() => {
                    currentCameraIndex = currentCameraIndex === 0 ? 1 : 0;
                    const mode = currentCameraIndex === 0 ? "environment" : "user";
                    document.getElementById('camera-label-text').textContent = currentCameraIndex === 0 ? "Kamera Belakang" : "Kamera Depan";
                    html5QrCode.start({ facingMode: mode }, { fps: 20 }, onScanSuccess, onScanFailure);
                });
            }
            return;
        }

        if (html5QrCode && html5QrCode.isScanning) {
            html5QrCode.stop().then(() => {
                currentCameraIndex = (currentCameraIndex + 1) % availableCameras.length;
                startCameraWithConfig();
            });
        }
    });

    // Fullscreen Toggle Handler
    document.getElementById('btn-fullscreen-toggle')?.addEventListener('click', function() {
        const wrapper = document.getElementById('reader-wrapper');
        if (!wrapper) return;

        if (!document.fullscreenElement) {
            if (wrapper.requestFullscreen) {
                wrapper.requestFullscreen();
            } else if (wrapper.webkitRequestFullscreen) {
                wrapper.webkitRequestFullscreen();
            }
            wrapper.classList.add('scanner-fullscreen-active');
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            }
            wrapper.classList.remove('scanner-fullscreen-active');
        }
    });

    document.addEventListener('fullscreenchange', function() {
        const wrapper = document.getElementById('reader-wrapper');
        if (!document.fullscreenElement && wrapper) {
            wrapper.classList.remove('scanner-fullscreen-active');
        }
    });

    if (isScanActive) {
        startScanner();
    }

    // Centered Result Modal Popup Handler
    function showSuccessModal(data) {
        playAudioBeep('success');

        const headerBg = document.getElementById('modal-header-bg');
        const iconContainer = document.getElementById('modal-icon-container');
        const title = document.getElementById('modal-title');
        const subtitle = document.getElementById('modal-subtitle');
        const bodyContent = document.getElementById('modal-body-content');

        if (headerBg) headerBg.className = 'p-4 text-center text-white bg-success bg-gradient';
        if (iconContainer) iconContainer.innerHTML = '<i class="bi bi-check-circle-fill display-1"></i>';
        if (title) title.textContent = 'ABSENSI BERHASIL!';
        if (subtitle) subtitle.textContent = data.message || 'Siswa berhasil tercatat hadir tepat waktu.';

        if (bodyContent) {
            bodyContent.innerHTML = `
                <div class="py-2">
                    <h3 class="fw-bold text-dark mb-1">${data.student.name}</h3>
                    <p class="text-muted mb-3 fs-6">NIS: <strong>${data.student.nis}</strong> | Kelas: <strong>${data.student.class}</strong></p>
                    <span class="badge bg-success fs-6 px-4 py-2.5 rounded-pill shadow-sm">
                        <i class="bi bi-clock-fill me-1"></i> Jam Absen: ${data.time} WIB
                    </span>
                </div>
            `;
        }

        if (!modalInstance) {
            modalInstance = new bootstrap.Modal(document.getElementById('scanResultModal'));
        }

        if (modalTimeout) clearTimeout(modalTimeout);
        modalInstance.show();

        showSuccess(data);

        modalTimeout = setTimeout(() => {
            modalInstance.hide();
        }, 2500);
    }

    function showErrorModal(message) {
        playAudioBeep('error');

        const headerBg = document.getElementById('modal-header-bg');
        const iconContainer = document.getElementById('modal-icon-container');
        const title = document.getElementById('modal-title');
        const subtitle = document.getElementById('modal-subtitle');
        const bodyContent = document.getElementById('modal-body-content');

        if (headerBg) headerBg.className = 'p-4 text-center text-white bg-danger bg-gradient';
        if (iconContainer) iconContainer.innerHTML = '<i class="bi bi-x-circle-fill display-1"></i>';
        if (title) title.textContent = 'SCAN GAGAL!';
        if (subtitle) subtitle.textContent = 'Proses absensi QR Code tidak dapat diproses.';

        if (bodyContent) {
            bodyContent.innerHTML = `
                <div class="py-2">
                    <div class="alert alert-danger border-0 rounded-3 mb-0 fs-6">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> ${message}
                    </div>
                </div>
            `;
        }

        if (!modalInstance) {
            modalInstance = new bootstrap.Modal(document.getElementById('scanResultModal'));
        }

        if (modalTimeout) clearTimeout(modalTimeout);
        modalInstance.show();

        showError(message);

        modalTimeout = setTimeout(() => {
            modalInstance.hide();
        }, 3000);
    }

    document.getElementById('scanResultModal')?.addEventListener('hidden.bs.modal', function () {
        processing = false;
    });

    function showSuccess(data) {
        document.getElementById('scan-result').innerHTML = `
            <div class="alert alert-success mb-0 rounded-3">
                <i class="bi bi-check-circle-fill fs-1 text-success"></i>
                <h6 class="mt-2 fw-bold text-dark">${data.message}</h6>
                <hr class="my-2">
                <h5 class="fw-bold mb-1 text-dark">${data.student.name}</h5>
                <p class="mb-0 small text-muted">NIS: ${data.student.nis} | Kelas: ${data.student.class}</p>
                <div class="badge bg-success mt-2">Check-in: ${data.time} WIB</div>
            </div>
        `;

        prependHistory(data.student.name, data.student.class, data.time);
    }

    function showError(message) {
        document.getElementById('scan-result').innerHTML = `
            <div class="alert alert-danger mb-0 rounded-3">
                <i class="bi bi-x-circle-fill fs-1 text-danger"></i>
                <h6 class="mt-2 fw-bold mb-0 text-dark">${message}</h6>
            </div>
        `;
    }

    function prependHistory(name, className, time) {
        const emptyRow = document.getElementById('empty-history-row');
        if (emptyRow) {
            emptyRow.remove();
        }

        const tbody = document.getElementById('history-list');
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td class="ps-4">
                <div class="fw-bold text-dark">${name}</div>
            </td>
            <td>${className || '-'}</td>
            <td class="text-nowrap small fw-semibold font-monospace">${time} WIB</td>
            <td class="pe-4 text-end"><span class="badge bg-success">Hadir</span></td>
        `;

        tbody.insertBefore(newRow, tbody.firstChild);

        if (tbody.children.length > 10) {
            tbody.removeChild(tbody.lastChild);
        }
    }

    function onScanSuccess(decodedText) {
        if (processing) return;
        processing = true;

        fetch("{{ route('operator.scan.store') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ qr_token: decodedText })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showSuccessModal(data);
            } else {
                showErrorModal(data.message);
            }
        })
        .catch(() => {
            showErrorModal("Terjadi kesalahan pada server.");
        });
    }

    function onScanFailure(error) {
        // sengaja dikosongkan
    }
});
</script>
@endpush
