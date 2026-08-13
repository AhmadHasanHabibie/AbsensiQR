@extends('Layouts.LayoutSiswa')

@section('title', 'Scan QR Absensi')

@section('content')

@php
    $isScanOpen  = \App\Services\AttendanceTimeService::isAttendanceOpen();
    $isPastLimit = \App\Services\AttendanceTimeService::isAttendanceExpired();
@endphp

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

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-primary mb-1">Scan QR Absensi Siswa</h3>
            <p class="text-muted mb-0">Halaman pemindaian QR Code kehadiran tepat waktu (03:00 - 06:30 WIB).</p>
        </div>
        <span class="badge fs-6 px-3 py-2 rounded-3 {{ $isScanOpen ? 'bg-success' : 'bg-danger' }}">
            <i class="bi {{ $isScanOpen ? 'bi-check-circle-fill' : 'bi-clock-history' }} me-1"></i>
            {{ $isScanOpen ? 'SCAN DIBUKA (03:00 - 06:30 WIB)' : 'SCAN DITUTUP (Pukul 06:31 WIB)' }}
        </span>
    </div>

    @if(!$isScanOpen)
        {{-- EMPTY STATE APABILA DITUTUP / PAST LIMIT --}}
        <div class="row justify-content-center my-4">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 text-center p-4 p-md-5 bg-white">
                    <div class="card-body">
                        <i class="bi bi-clock-history text-secondary display-1 d-block mb-3 opacity-50"></i>
                        <h4 class="fw-bold text-dark mb-2">Maaf, waktu absensi QR hari ini telah berakhir.</h4>
                        <p class="text-muted mb-4 small">Sistem absensi QR tepat waktu beroperasi mulai pukul <strong>03:00:00 WIB</strong> dan ditutup otomatis pada pukul <strong>06:31:00 WIB</strong>. Apabila Anda terlambat hadir di sekolah, silakan melapor kepada <strong>Petugas Operator</strong> di gerbang utama.</p>
                        <a href="{{ route('siswa.dashboard') }}" class="btn btn-primary px-4 py-2.5 rounded-pill shadow-sm fw-semibold">
                            <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard Siswa
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- ACTIVE SCANNER SETUP APABILA DALAM JENDELA WAKTU 03:00 - 06:30 WIB --}}
        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-primary text-white py-3 text-center d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold mx-auto"><i class="bi bi-camera-video me-2"></i> Kamera Scanner Siswa</h5>
                    </div>
                    <div class="card-body p-4 text-center">

                        <div class="mb-3">
                            <p class="fw-semibold text-dark mb-0 fs-6">Arahkan QR Code Anda ke dalam area pemindaian.</p>
                            <small class="text-muted">Gunakan kamera belakang atau tombol ganti kamera untuk hasil terbaik.</small>
                        </div>

                        {{-- Scanner Camera Wrapper --}}
                        <div id="reader-wrapper" class="position-relative mx-auto rounded-4 overflow-hidden bg-dark scanner-responsive-box" style="width:100%;">
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

                            {{-- Fullscreen Toast Pop-up Overlay (Khusus Mode Layar Penuh) --}}
                            <div id="fullscreen-toast-overlay" class="position-absolute bottom-0 start-0 end-0 p-3 p-md-4 d-none" style="z-index: 999; pointer-events: none;">
                                <div id="fullscreen-toast-card" class="card border-0 shadow-lg rounded-4 overflow-hidden mx-auto" style="max-width: 480px; pointer-events: auto; background: rgba(15, 23, 42, 0.95); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.2) !important;">
                                    <div id="fullscreen-toast-header" class="px-3 py-2 text-white fw-bold d-flex align-items-center justify-content-between bg-success bg-gradient">
                                        <span id="fullscreen-toast-title" class="d-flex align-items-center gap-2 small text-uppercase font-monospace">
                                            <i class="bi bi-check-circle-fill fs-6"></i> <span id="fullscreen-toast-title-text">ABSENSI BERHASIL</span>
                                        </span>
                                        <button type="button" class="btn-close btn-close-white btn-sm" onclick="hideFullscreenToast()"></button>
                                    </div>
                                    <div class="card-body p-3 text-white" id="fullscreen-toast-body">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <small class="text-muted"><i class="bi bi-shield-check text-success me-1"></i> Posisikan QR Code di tengah kotak. Hasil absensi akan pop-up otomatis di tengah layar.</small>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">Status Scan</h5>
                    </div>
                    <div class="card-body text-center py-4">
                        <div id="scan-result">
                            <i class="bi bi-qr-code-scan display-3 text-secondary mb-3 opacity-50"></i>
                            <h5 class="fw-bold">Belum Ada QR</h5>
                            <p class="text-muted mb-0 small">Hasil scan QR akan muncul di sini.</p>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">Petunjuk Absensi</h5>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0 text-muted small">
                            <li class="mb-2">Arahkan kamera tepat ke QR Code.</li>
                            <li class="mb-2">Waktu tutup otomatis adalah pukul 06:31:00 WIB.</li>
                            <li>Tunggu konfirmasi absensi berhasil dari pop-up di layar.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

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

    .scanner-responsive-box, #reader {
        min-height: 380px;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background-color: #0f172a !important;
    }
    @media (min-width: 992px) {
        .scanner-responsive-box, #reader {
            min-height: 480px;
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
        object-fit: contain !important;
        object-position: center center !important;
        margin-left: auto !important;
        margin-right: auto !important;
        display: block !important;
    }

    .scanner-box-frame {
        width: 240px;
        height: 240px;
        border: 2.5px solid rgba(255, 255, 255, 0.5);
        box-shadow: 0 0 0 4000px rgba(0, 0, 0, 0.5);
        border-radius: 20px;
        position: relative;
        overflow: hidden;
    }
    @media (min-width: 576px) {
        .scanner-box-frame {
            width: 280px;
            height: 280px;
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
    const readerElem = document.getElementById("reader");
    if (!readerElem) return;

    let html5QrCode = null;
    let processing = false;
    let availableCameras = [];
    let backCameraId = null;
    let frontCameraId = null;
    let isFrontCamera = false;
    let isSwitchingCamera = false;
    let modalInstance = null;
    let modalTimeout = null;

    // Audio Chime Feedback
    function playAudioBeep(type = 'success') {
        try {
            const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.connect(gain);
            gain.connect(audioCtx.destination);

            if (type === 'success') {
                osc.frequency.setValueAtTime(587.33, audioCtx.currentTime);
                osc.frequency.setValueAtTime(880, audioCtx.currentTime + 0.1);
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

    let fullscreenToastTimeout = null;

    function isScannerFullscreen() {
        const wrapper = document.getElementById('reader-wrapper');
        return !!(document.fullscreenElement || (wrapper && wrapper.classList.contains('scanner-fullscreen-active')));
    }

    window.hideFullscreenToast = function() {
        const overlay = document.getElementById('fullscreen-toast-overlay');
        if (overlay) {
            overlay.classList.add('d-none');
        }
    };

    function showFullscreenToast(type, data) {
        const overlay = document.getElementById('fullscreen-toast-overlay');
        const header = document.getElementById('fullscreen-toast-header');
        const titleText = document.getElementById('fullscreen-toast-title-text');
        const body = document.getElementById('fullscreen-toast-body');

        if (!overlay || !body) return;

        if (type === 'success') {
            if (header) header.className = 'px-3 py-2 text-white fw-bold d-flex align-items-center justify-content-between bg-success bg-gradient';
            if (titleText) titleText.textContent = 'ABSENSI BERHASIL!';

            const studentName = data.student?.name || 'Siswa';
            const studentNis = data.student?.nis || '-';
            const studentClass = data.student?.class || '-';
            const checkTime = data.time ? `${data.time} WIB` : '';

            body.innerHTML = `
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-success bg-opacity-25 text-success rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:48px; height:48px; flex-shrink:0;">
                        <i class="bi bi-check-lg fs-2"></i>
                    </div>
                    <div class="flex-grow-1 text-start">
                        <h5 class="fw-bold mb-1 text-white">${studentName}</h5>
                        <div class="d-flex flex-wrap align-items-center gap-2 small text-light opacity-90">
                            <span><i class="bi bi-card-text me-1 text-info"></i>NIS: <strong>${studentNis}</strong></span>
                            <span>|</span>
                            <span><i class="bi bi-building me-1 text-warning"></i>Kelas: <strong>${studentClass}</strong></span>
                        </div>
                        ${checkTime ? `<div class="mt-1.5"><span class="badge bg-success font-monospace px-2.5 py-1 fs-7"><i class="bi bi-clock-fill me-1"></i>${checkTime}</span></div>` : ''}
                    </div>
                </div>
            `;
        } else {
            if (header) header.className = 'px-3 py-2 text-white fw-bold d-flex align-items-center justify-content-between bg-danger bg-gradient';
            if (titleText) titleText.textContent = 'SCAN GAGAL!';

            const errorMsg = typeof data === 'string' ? data : (data.message || 'Proses scan gagal.');
            const studentName = data.student?.name;
            const studentNis = data.student?.nis;

            body.innerHTML = `
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-danger bg-opacity-25 text-danger rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:44px; height:44px; flex-shrink:0;">
                        <i class="bi bi-exclamation-triangle-fill fs-3"></i>
                    </div>
                    <div class="flex-grow-1 text-start">
                        <h6 class="fw-bold mb-1 text-white">${errorMsg}</h6>
                        ${studentName ? `<small class="text-light opacity-75">${studentName} (NIS: ${studentNis || '-'})</small>` : ''}
                    </div>
                </div>
            `;
        }

        overlay.classList.remove('d-none');

        if (fullscreenToastTimeout) clearTimeout(fullscreenToastTimeout);
        fullscreenToastTimeout = setTimeout(() => {
            hideFullscreenToast();
        }, 2200);
    }

    // Centered Result Modal Popup Handler
    function showSuccessModal(data) {
        playAudioBeep('success');
        showSuccess(data);

        if (isScannerFullscreen()) {
            showFullscreenToast('success', data);
            return;
        }

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
                    <h3 class="fw-bold text-dark mb-1">${data.student?.name || 'Siswa'}</h3>
                    <p class="text-muted mb-3 fs-6">NIS: <strong>${data.student?.nis || '-'}</strong> | Kelas: <strong>${data.student?.class || '-'}</strong></p>
                    <span class="badge bg-success fs-6 px-4 py-2.5 rounded-pill shadow-sm">
                        <i class="bi bi-clock-fill me-1"></i> Absensi Berhasil
                    </span>
                </div>
            `;
        }

        if (!modalInstance) {
            modalInstance = new bootstrap.Modal(document.getElementById('scanResultModal'));
        }

        if (modalTimeout) clearTimeout(modalTimeout);
        modalInstance.show();

        modalTimeout = setTimeout(() => {
            modalInstance.hide();
        }, 2000);
    }

    function showErrorModal(message, data = null) {
        playAudioBeep('error');
        showError(message);

        if (isScannerFullscreen()) {
            showFullscreenToast('error', data || message);
            return;
        }

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

        modalTimeout = setTimeout(() => {
            modalInstance.hide();
        }, 2500);
    }

    document.getElementById('scanResultModal')?.addEventListener('hidden.bs.modal', function () {
        processing = false;
    });

    function showSuccess(data) {
        document.getElementById('scan-result').innerHTML = `
            <div class="alert alert-success mb-0 rounded-3">
                <i class="bi bi-check-circle-fill fs-1 text-success"></i>
                <h5 class="mt-3 fw-bold text-dark">${data.message || 'Absensi Berhasil'}</h5>
                <hr class="my-2">
                <h5 class="fw-bold mb-1 text-dark">${data.student?.name || 'Siswa'}</h5>
                <p class="mb-1 text-muted">NIS : ${data.student?.nis || '-'}</p>
                <p class="mb-0 text-muted">Kelas : ${data.student?.class || '-'}</p>
            </div>
        `;
    }

    function showError(message) {
        document.getElementById('scan-result').innerHTML = `
            <div class="alert alert-danger mb-0 rounded-3">
                <i class="bi bi-x-circle-fill fs-1 text-danger"></i>
                <h5 class="mt-3 mb-0 text-dark">${message}</h5>
            </div>
        `;
    }

    let lastScannedQr = null;
    let lastScanTime = 0;

    function onScanSuccess(decodedText) {
        const now = Date.now();

        // Cegah scan berulang untuk QR token SAMA dalam kurun waktu 2.5 detik
        if (decodedText === lastScannedQr && (now - lastScanTime) < 2500) {
            return;
        }

        // Cegah request bersamaan secara berlebihan
        if (processing && (now - lastScanTime) < 500) {
            return;
        }

        processing = true;
        lastScannedQr = decodedText;
        lastScanTime = now;

        fetch("{{ route('guru.scan.store') }}", {
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
                showErrorModal(data.message, data);
            }
        })
        .catch(() => {
            showErrorModal("Terjadi kesalahan pada server.");
        })
        .finally(() => {
            // Lepas lock 'processing' setelah 600ms agar QR siswa berikutnya dapat langsung di-scan tanpa jeda lama!
            setTimeout(() => {
                processing = false;
            }, 600);
        });
    }

    function onScanFailure(error) {
        // sengaja dikosongkan
    }

    function updateVisualFrame(boxSize) {
        const frame = document.querySelector('.scanner-box-frame');
        if (frame && boxSize) {
            frame.style.width = boxSize + 'px';
            frame.style.height = boxSize + 'px';
        }
    }

    function createScannerConfig() {
        return {
            fps: 30,
            qrbox: function(viewfinderWidth, viewfinderHeight) {
                const minEdge = Math.min(viewfinderWidth, viewfinderHeight);
                const boxSize = Math.max(220, Math.min(Math.floor(minEdge * 0.65), 550));
                updateVisualFrame(boxSize);
                return {
                    width: boxSize,
                    height: boxSize
                };
            },
            videoConstraints: {
                width: { ideal: 1280, min: 640 },
                height: { ideal: 720, min: 480 }
            },
            experimentalFeatures: {
                useBarCodeDetectorIfSupported: true
            }
        };
    }

    function detectCameraIds(cameras) {
        if (!cameras || !cameras.length) return;

        backCameraId = null;
        frontCameraId = null;

        const backCam = cameras.find(c => {
            const label = (c.label || '').toLowerCase();
            return label.includes('back') || label.includes('rear') || label.includes('environment') || label.includes('belakang');
        });

        const frontCam = cameras.find(c => {
            const label = (c.label || '').toLowerCase();
            return label.includes('front') || label.includes('user') || label.includes('depan') || label.includes('selfie');
        });

        if (backCam && backCam.id) {
            backCameraId = backCam.id;
        }

        if (frontCam && frontCam.id) {
            frontCameraId = frontCam.id;
        }

        console.log("BACK CAMERA ID:", backCameraId);
        console.log("FRONT CAMERA ID:", frontCameraId);
    }

    function getCameraConstraintForTarget(targetIsFront) {
        const targetModeStr = targetIsFront ? "FRONT" : "BACK";
        const currentModeStr = isFrontCamera ? "FRONT" : "BACK";
        console.log("CURRENT CAMERA:", currentModeStr);
        console.log("TARGET CAMERA:", targetModeStr);

        const mode = targetIsFront ? "user" : "environment";

        if (targetIsFront && frontCameraId) {
            return frontCameraId;
        } else if (!targetIsFront && backCameraId) {
            return backCameraId;
        }

        return { facingMode: { ideal: mode } };
    }

    function startCameraWithTarget(targetIsFront) {
        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode("reader");
        }

        if (html5QrCode.isScanning) {
            return Promise.resolve();
        }

        const cameraChoice = getCameraConstraintForTarget(targetIsFront);
        const config = createScannerConfig();

        return html5QrCode.start(
            cameraChoice,
            config,
            onScanSuccess,
            onScanFailure
        ).then(() => {
            isFrontCamera = targetIsFront;
            const cameraLabelElem = document.getElementById('camera-label-text');
            if (cameraLabelElem) {
                cameraLabelElem.textContent = isFrontCamera ? "Kamera Depan" : "Kamera Belakang";
            }
        }).catch(err => {
            const fallbackMode = targetIsFront ? "user" : "environment";
            return html5QrCode.start(
                { facingMode: fallbackMode },
                config,
                onScanSuccess,
                onScanFailure
            ).then(() => {
                isFrontCamera = targetIsFront;
                const cameraLabelElem = document.getElementById('camera-label-text');
                if (cameraLabelElem) {
                    cameraLabelElem.textContent = isFrontCamera ? "Kamera Depan" : "Kamera Belakang";
                }
            }).catch(e => {
                showErrorModal("Gagal mengakses " + (targetIsFront ? "kamera depan" : "kamera belakang") + ".");
            });
        });
    }

    function startScanner() {
        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode("reader");
        }

        if (html5QrCode.isScanning) return;

        Html5Qrcode.getCameras()
            .then(cameras => {
                if (cameras && cameras.length) {
                    availableCameras = cameras;
                    detectCameraIds(cameras);
                }
                startCameraWithTarget(isFrontCamera);
            })
            .catch(() => {
                startCameraWithTarget(isFrontCamera);
            });
    }

    // Switch Camera Handler
    document.getElementById('btn-switch-camera')?.addEventListener('click', function() {
        if (isSwitchingCamera) return;

        if (availableCameras && availableCameras.length === 1) {
            showErrorModal("Kamera lain tidak tersedia pada perangkat Anda.");
            return;
        }

        isSwitchingCamera = true;
        const targetIsFront = !isFrontCamera;

        const doSwitch = () => {
            startCameraWithTarget(targetIsFront).finally(() => {
                isSwitchingCamera = false;
            });
        };

        if (html5QrCode && html5QrCode.isScanning) {
            html5QrCode.stop().then(doSwitch).catch(() => {
                doSwitch();
            });
        } else {
            doSwitch();
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

    startScanner();
});
</script>
@endpush