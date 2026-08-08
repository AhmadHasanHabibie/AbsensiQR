@extends('Layouts.LayoutGuruPiket')

@section('title', 'Dashboard Guru Piket')

@section('content')

<div class="container-fluid py-2">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Dashboard Guru Piket</h3>
            <p class="text-muted mb-0">Pusat Monitoring Harian Absensi Sekolah</p>
        </div>
        <div class="text-end d-none d-sm-block">
            <div class="fw-semibold text-dark">{{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY') }}</div>
            <div id="realtime-clock" class="text-muted small">{{ \Carbon\Carbon::now()->format('H:i:s') }} WIB</div>
        </div>
    </div>

    {{-- Card Selamat Datang --}}
    <div class="card border-0 rounded-4 shadow-sm bg-white mb-4">
        <div class="card-body p-4 p-lg-5">
            <div class="d-flex align-items-center gap-4 flex-wrap">
                <div style="width:72px;height:72px;background:linear-gradient(135deg,#0284c7,#38bdf8);border-radius:20px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:32px;" class="shadow-sm flex-shrink-0">
                    <i class="bi bi-person-badge"></i>
                </div>
                <div class="flex-grow-1">
                    <h4 class="fw-bold mb-1">Selamat Datang, {{ Auth::user()->name }}!</h4>
                    <p class="text-muted mb-2">Petugas Guru Piket Sekolah — Monitoring Absensi Real-time.</p>
                    <div class="d-flex gap-2 align-items-center flex-wrap">
                        <span class="badge bg-info text-dark px-3 py-2 fs-6">Role: Guru Piket</span>
                        <a href="{{ route('piket.monitoring.index') }}" class="btn btn-primary btn-sm px-3 rounded-3">
                            <i class="bi bi-display me-1"></i> Buka Monitoring Absensi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 4 Stat Cards Ringkasan Konfirmasi Absensi --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:52px;height:52px;background:linear-gradient(135deg,#0d6efd,#0dcaf0);border-radius:16px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:24px;" class="shadow-sm">
                        <i class="bi bi-building"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Kelas Hari Ini</div>
                        <h3 class="fw-bold mb-0 text-dark">{{ $totalClasses }} <span class="fs-6 text-muted fw-normal">Kelas</span></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:52px;height:52px;background:linear-gradient(135deg,#198754,#2ecc71);border-radius:16px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:24px;" class="shadow-sm">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Sudah Konfirmasi</div>
                        <h3 class="fw-bold mb-0 text-success">{{ $confirmedCount }} <span class="fs-6 text-muted fw-normal">Kelas</span></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:52px;height:52px;background:linear-gradient(135deg,#ffc107,#fbbf24);border-radius:16px;display:flex;align-items:center;justify-content:center;color:#000;font-size:24px;" class="shadow-sm">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Belum Konfirmasi</div>
                        <h3 class="fw-bold mb-0 text-warning">{{ $unconfirmedCount }} <span class="fs-6 text-muted fw-normal">Kelas</span></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:52px;height:52px;background:linear-gradient(135deg,#0284c7,#38bdf8);border-radius:16px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:24px;" class="shadow-sm">
                        <i class="bi bi-pie-chart-fill"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Persentase Konfirmasi</div>
                        <h3 class="fw-bold mb-0 text-info">{{ $percentage }}%</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@push('js')
<script>
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const clockEl = document.getElementById('realtime-clock');
        if (clockEl) {
            clockEl.textContent = `${hours}:${minutes}:${seconds} WIB`;
        }
    }
    setInterval(updateClock, 1000);
</script>
@endpush
