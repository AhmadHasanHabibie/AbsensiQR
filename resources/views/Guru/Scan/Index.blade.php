@extends('Layouts.LayoutGuru')

@section('title', 'Scan QR Absensi Wali Kelas')

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
                <a href="{{ route('guru.dashboard') }}" class="btn btn-danger px-5 fw-semibold rounded-pill">
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
            window.location.href = '{{ route("guru.dashboard") }}';
        });
    });
</script>
@endif

<div class="container-fluid py-2">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Scan QR Absensi Wali Kelas</h3>
            <p class="text-muted mb-0">Pemindaian QR Code kehadiran tepat waktu siswa kelas (03:00 - 06:30 WIB).</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="badge fs-6 px-3 py-2 rounded-3 {{ $isScanOpen ? 'bg-success' : 'bg-danger' }}">
                <i class="bi {{ $isScanOpen ? 'bi-check-circle-fill' : 'bi-clock-history' }} me-1"></i>
                {{ $isScanOpen ? 'SCAN DIBUKA (03:00 - 06:30 WIB)' : 'SCAN DITUTUP (Pukul 06:31 WIB)' }}
            </span>
            <div class="text-end d-none d-sm-block">
                <div class="fw-semibold text-dark">{{ \Carbon\Carbon::now('Asia/Jakarta')->isoFormat('D MMMM YYYY') }}</div>
                <div id="realtime-clock" class="text-muted small font-monospace fw-bold">{{ \Carbon\Carbon::now('Asia/Jakarta')->format('H:i:s') }} WIB</div>
            </div>
        </div>
    </div>

    @if(!$isScanOpen)
        {{-- EMPTY STATE SAMA SEPERTI SISWA BILA SCAN SUDAH DITUTUP --}}
        <div class="row justify-content-center my-4">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 text-center p-4 p-md-5 bg-white">
                    <div class="card-body">
                        <i class="bi bi-clock-history text-secondary display-1 d-block mb-3 opacity-50"></i>
                        <h4 class="fw-bold text-dark mb-2">Waktu Scan QR Telah Berakhir</h4>
                        <p class="text-muted mb-4 small">Sistem pemindaian QR absensi secara otomatis ditutup pada pukul <strong>06:31:00 WIB</strong>. Seluruh siswa yang belum melakukan scan QR tercatat belum hadir secara sistem. Anda dapat melakukan rekapitulasi atau koreksi pada menu <strong>Kelola Absensi Kelas</strong>.</p>
                        <a href="{{ route('guru.dashboard') }}" class="btn btn-primary px-4 py-2.5 rounded-pill shadow-sm fw-semibold">
                            <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard Wali Kelas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- Main Centered Container --}}
        <div class="row justify-content-center">
            <div class="col-12 col-md-12 col-lg-10 col-xl-9">

                {{-- Scanner Card --}}
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-primary text-white p-3 text-center">
                        <h5 class="mb-0 fw-semibold">
                            <i class="bi bi-qr-code-scan me-2"></i> Scanner QR Code Wali Kelas
                        </h5>
                    </div>
                    <div class="card-body p-4 text-center">

                        {{-- Instruksi Di Atas Kamera --}}
                        <div class="mb-3">
                            <p class="fw-semibold text-dark mb-0 fs-6">Arahkan QR Code siswa kelas Anda ke dalam area pemindaian.</p>
                            <small class="text-muted">Pastikan QR Code berada tepat di tengah kotak scanner.</small>
                        </div>

                        {{-- Camera Reader Box --}}
                        <div class="position-relative mx-auto rounded-3 overflow-hidden bg-dark scanner-responsive-box" style="width:100%;">
                            <div id="reader" style="width:100%;"></div>
                        </div>

                        {{-- Instruksi Di Bawah Kamera --}}
                        <div class="mt-3">
                            <small class="text-muted"><i class="bi bi-person-bounding-box me-1"></i> Posisikan tubuh tepat di depan kamera agar proses pemindaian lebih cepat.</small>
                        </div>

                    </div>
                </div>

                {{-- Hasil Scan Card (Di Bawah Scanner) --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-warning text-dark p-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="bi bi-person-check me-2"></i> Hasil Scan Terakhir
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div id="scan-result" class="text-center text-muted py-3">
                            <i class="bi bi-person-badge display-4 opacity-50"></i>
                            <p class="mt-3 mb-0">Belum ada QR Code yang dipindai.</p>
                        </div>
                    </div>
                </div>

                {{-- Informasi Card (Di Bawah Hasil Scan) --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-success text-white p-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="bi bi-info-circle me-2"></i> Ketentuan Absensi Wali Kelas
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <ul class="mb-0 text-muted small">
                            <li class="mb-2">Scanner ini hanya menerima QR Code siswa dari kelas wali Anda.</li>
                            <li class="mb-2">Batas waktu pemindaian otomatis tepat waktu ditutup pukul <strong>06:31:00 WIB</strong>.</li>
                            <li class="mb-0">Setelah pukul 06.31 WIB, lakukan konfirmasi akhir kehadiran melalui halaman <strong>Dashboard / Kelola Absensi</strong>.</li>
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
    .scanner-responsive-box, #reader {
        min-height: 340px;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    @media (min-width: 992px) {
        .scanner-responsive-box, #reader {
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

    const readerElem = document.getElementById("reader");
    if (!readerElem) return;

    const html5QrCode = new Html5Qrcode("reader");
    let processing = false;

    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const clockElem = document.getElementById('realtime-clock');
        if (clockElem) {
            clockElem.textContent = `${hours}:${minutes}:${seconds} WIB`;
        }
    }
    setInterval(updateClock, 1000);

    function resetScanner() {
        setTimeout(() => {
            processing = false;
        }, 2000);
    }

    function showSuccess(data) {
        document.getElementById('scan-result').innerHTML = `
            <div class="alert alert-success mb-0 rounded-3">
                <i class="bi bi-check-circle-fill fs-1"></i>
                <h5 class="mt-3 fw-bold">${data.message}</h5>
                <hr class="my-2">
                <h5 class="fw-bold mb-1">${data.student.name}</h5>
                <p class="mb-1">NIS : ${data.student.nis}</p>
                <p class="mb-0">Kelas : ${data.student.class}</p>
            </div>
        `;
    }

    function showError(message) {
        document.getElementById('scan-result').innerHTML = `
            <div class="alert alert-danger mb-0 rounded-3">
                <i class="bi bi-x-circle-fill fs-1"></i>
                <h5 class="mt-3 mb-0">${message}</h5>
            </div>
        `;
    }

    function onScanSuccess(decodedText) {
        if (processing) return;
        processing = true;

        fetch("{{ route('guru.scan.store') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                qr_token: decodedText
            })
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
            );
        })
        .catch(() => {
            showError("Kamera tidak dapat diakses.");
        });
});
</script>
@endpush