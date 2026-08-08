@extends('Layouts.LayoutOperator')

@section('title', 'Scan QR Absensi Otomatis')

@section('content')

<div class="container-fluid py-2">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Scan QR Absensi Otomatis</h3>
            <p class="text-muted mb-0">Sistem Absensi Berbasis Waktu Server (06:00:00 – 06:30:59 WIB)</p>
        </div>

        <div class="d-flex align-items-center gap-3">
            {{-- Status Scan Badge (Live System Time) --}}
            <span id="status-badge" class="badge fs-6 px-3 py-2 {{ $isScanOpen ? 'bg-success' : 'bg-danger' }}">
                <i class="bi {{ $isScanOpen ? 'bi-check-circle-fill' : 'bi-clock-history' }} me-1"></i>
                <span id="status-badge-text">
                    @if($isScanOpen)
                        DIBUKA OTOMATIS (06:00 - 06:30 WIB)
                    @elseif($isPastLimit)
                        DITUTUP OTOMATIS (Pukul 06:31 WIB)
                    @else
                        BELUM DIBUKA (Pukul 06:00 WIB)
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
                <span>Sistem absensi QR belum dibuka. Pintu absensi akan terbuka otomatis tepat pada pukul 06:00:00 WIB.</span>
            </div>
        @endif
    </div>

    {{-- Main Centered Container --}}
    <div class="row justify-content-center">
        <div class="col-12 col-md-12 col-lg-10 col-xl-9">

            {{-- Scanner Card --}}
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-primary text-white p-3 text-center">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-qr-code-scan me-2"></i> Scanner QR Code Siswa
                    </h5>
                </div>
                <div class="card-body p-4 text-center">

                    {{-- Header Instruction --}}
                    <div class="mb-3">
                        <p class="fw-semibold text-dark mb-0 fs-6">Arahkan QR Code Siswa ke dalam area pemindaian.</p>
                        <small class="text-muted">Kamera akan memindai secara otomatis saat jam absensi sedang dibuka.</small>
                    </div>

                    {{-- Scanner Placeholder (Closed State) --}}
                    <div id="scanner-placeholder" class="py-5 bg-dark text-white rounded-3 scanner-responsive-box {{ $isScanOpen ? 'd-none' : '' }}" style="display:flex; flex-direction:column; justify-content:center; align-items:center;">
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
                                Pintu absensi QR akan dibuka otomatis oleh sistem tepat pukul 06:00:00 WIB.
                            @endif
                        </p>
                    </div>

                    {{-- Scanner Camera Wrapper (Active State) --}}
                    <div id="reader-wrapper" class="position-relative mx-auto rounded-3 overflow-hidden bg-dark scanner-responsive-box {{ $isScanOpen ? '' : 'd-none' }}" style="width:100%;">
                        <div id="reader" style="width:100%;"></div>
                    </div>

                    {{-- Sub Instruction --}}
                    <div class="mt-3">
                        <small class="text-muted"><i class="bi bi-shield-check text-success me-1"></i> Sistem memverifikasi waktu server secara real-time untuk mencegah kecurangan.</small>
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
                        <li class="mb-2"><strong>Buka Otomatis:</strong> Sistem membuka scanner QR tepat pukul <strong>06:00:00 WIB</strong>.</li>
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
    .scanner-responsive-box, #reader, #scanner-placeholder {
        min-height: 340px;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    @media (min-width: 992px) {
        .scanner-responsive-box, #reader, #scanner-placeholder {
            min-height: 500px;
        }
    }
    #reader {
        width: 100% !important;
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
        max-width: 100% !important;
        height: auto !important;
        max-height: 100% !important;
        object-fit: contain !important;
        object-position: center center !important;
        margin-left: auto !important;
        margin-right: auto !important;
        display: block !important;
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
        const shouldBeOpen = (currentTimeVal >= '06:00:00' && currentTimeVal <= '06:30:59');

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

        if (timeVal >= '06:00:00' && timeVal <= '06:30:59') {
            if (badge) badge.className = 'badge fs-6 px-3 py-2 bg-success';
            if (badgeText) badgeText.textContent = 'DIBUKA OTOMATIS (06:00 - 06:30 WIB)';
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
            if (badgeText) badgeText.textContent = 'BELUM DIBUKA (Pukul 06:00 WIB)';
            if (placeholder) placeholder.classList.remove('d-none');
            if (wrapper) wrapper.classList.add('d-none');
            if (cdBanner) cdBanner.classList.add('d-none');
            if (pTitle) pTitle.textContent = 'Absensi QR Belum Dibuka';
            if (pSubtitle) pSubtitle.textContent = 'Pintu absensi QR akan dibuka otomatis oleh sistem tepat pukul 06:00:00 WIB.';
            stopScanner();
        }
    }

    setInterval(checkSystemTime, 1000);

    // Initialize HTML5 Scanner
    function startScanner() {
        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode("reader");
        }

        if (html5QrCode.isScanning) return;

        Html5Qrcode.getCameras()
            .then(cameras => {
                if (!cameras.length) {
                    showError("Kamera tidak ditemukan.");
                    return;
                }

                html5QrCode.start(
                    cameras[0].id,
                    {
                        fps: 15,
                        qrbox: { width: 280, height: 280 },
                        aspectRatio: 1.333333
                    },
                    onScanSuccess,
                    onScanFailure
                ).catch(err => {
                    showError("Gagal membuka kamera.");
                });
            })
            .catch(() => {
                showError("Kamera tidak dapat diakses.");
            });
    }

    function stopScanner() {
        if (html5QrCode && html5QrCode.isScanning) {
            html5QrCode.stop().catch(() => {});
        }
    }

    if (isScanActive) {
        startScanner();
    }

    function resetScanner() {
        setTimeout(() => {
            processing = false;
        }, 2000);
    }

    function showSuccess(data) {
        document.getElementById('scan-result').innerHTML = `
            <div class="alert alert-success mb-0 rounded-3">
                <i class="bi bi-check-circle-fill fs-1"></i>
                <h6 class="mt-2 fw-bold">${data.message}</h6>
                <hr class="my-2">
                <h5 class="fw-bold mb-1">${data.student.name}</h5>
                <p class="mb-0 small">NIS: ${data.student.nis} | Kelas: ${data.student.class}</p>
                <div class="badge bg-success mt-2">Check-in: ${data.time} WIB</div>
            </div>
        `;

        prependHistory(data.student.name, data.student.class, data.time);
    }

    function showError(message) {
        document.getElementById('scan-result').innerHTML = `
            <div class="alert alert-danger mb-0 rounded-3">
                <i class="bi bi-x-circle-fill fs-1"></i>
                <h6 class="mt-2 fw-bold mb-0">${message}</h6>
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
                showSuccess(data);
            } else {
                showError(data.message);
            }
            resetScanner();
        })
        .catch(() => {
            showError("Terjadi kesalahan pada server.");
            resetScanner();
        });
    }

    function onScanFailure(error) {
        // sengaja dikosongkan
    }
});
</script>
@endpush
