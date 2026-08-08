@extends('Layouts.LayoutOperator')

@section('title', 'Dashboard Operator')

@section('content')

@php
    $totalScanHariIni = \App\Models\Attendance::whereDate('attendance_date', today())
        ->whereNotNull('check_in')
        ->count();

    $excludedStudentIds = \App\Models\Attendance::whereDate('attendance_date', today())
        ->where(function ($query) {
            $query->whereIn('status', ['hadir', 'izin', 'sakit'])
                ->orWhere('is_late', true);
        })
        ->pluck('student_id');

    $siswaBelumScan = \App\Models\User::where('role', 'student')
        ->where('status', true)
        ->whereNotIn('id', $excludedStudentIds)
        ->count();

    $siswaTerlambat = \App\Models\Attendance::whereDate('attendance_date', today())
        ->where('is_late', true)
        ->count();

    $isScanOpen  = \App\Services\AttendanceTimeService::isAttendanceOpen();
    $isPastLimit = \App\Services\AttendanceTimeService::isAttendanceExpired();
@endphp

<div class="container-fluid py-2">

    {{-- 1. HEADER --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1 text-dark">Dashboard Operator</h3>
            <p class="text-muted mb-0 small">Sistem Operasional Absensi QR Code Lapangan</p>
        </div>
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <span class="badge fs-6 px-3 py-2 rounded-3 {{ $isScanOpen ? 'bg-success' : 'bg-danger' }}">
                <i class="bi {{ $isScanOpen ? 'bi-check-circle-fill' : 'bi-clock-history' }} me-1"></i>
                @if($isScanOpen)
                    DIBUKA OTOMATIS (06:00 - 06:30 WIB)
                @elseif($isPastLimit)
                    DITUTUP OTOMATIS (06:31 WIB)
                @else
                    BELUM DIBUKA (06:00 WIB)
                @endif
            </span>
            <div class="text-end border-start ps-3 d-none d-sm-block">
                <div class="fw-bold text-dark small"><i class="bi bi-calendar-event text-primary me-1"></i> {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY') }}</div>
                <div id="current-clock" class="text-muted small"><i class="bi bi-clock text-primary me-1"></i> {{ \Carbon\Carbon::now()->format('H:i:s') }} WIB</div>
            </div>
        </div>
    </div>

    {{-- 2. WELCOME CARD --}}
    <div class="card border-0 rounded-4 shadow-sm bg-white mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-3">
                <div style="width:60px;height:60px;background:linear-gradient(135deg,#0d6efd,#0dcaf0);border-radius:16px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:28px;" class="shadow-sm flex-shrink-0">
                    <i class="bi bi-person-workspace"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                        <h4 class="fw-bold mb-0 text-dark">Selamat Datang, {{ Auth::user()->name }}!</h4>
                        <span class="badge bg-primary px-3 py-1 fs-6 rounded-pill">Operator Operasional</span>
                    </div>
                    <p class="text-muted mb-0 small">Petugas operasional bertugas mengelola pemindaian Scan QR absensi siswa dan mencatat keterlambatan secara real-time.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. QUICK ACTION --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6">
            <a href="{{ route('operator.scan.index') }}" class="btn btn-primary btn-lg w-100 py-3 rounded-4 shadow-sm d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-qr-code-scan fs-4"></i>
                <span class="fw-bold fs-5">Scan QR</span>
            </a>
        </div>
        <div class="col-12 col-md-6">
            <a href="{{ route('operator.terlambat.index') }}" class="btn btn-outline-primary btn-lg w-100 py-3 rounded-4 shadow-sm d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-alarm fs-4"></i>
                <span class="fw-bold fs-5">Data Terlambat</span>
            </a>
        </div>
    </div>

    {{-- 4. STATISTIK CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:48px;height:48px;background:linear-gradient(135deg,#0d6efd,#0dcaf0);border-radius:14px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:22px;" class="flex-shrink-0">
                        <i class="bi bi-qr-code-scan"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Scan Hari Ini</div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $totalScanHariIni }} <span class="fs-6 text-muted fw-normal">Siswa</span></h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:48px;height:48px;background:linear-gradient(135deg,#6c757d,#495057);border-radius:14px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:22px;" class="flex-shrink-0">
                        <i class="bi bi-person-exclamation"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Belum Scan</div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $siswaBelumScan }} <span class="fs-6 text-muted fw-normal">Siswa</span></h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:48px;height:48px;background:linear-gradient(135deg,#ffc107,#ffb300);border-radius:14px;display:flex;align-items:center;justify-content:center;color:#000;font-size:22px;" class="flex-shrink-0">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Terlambat</div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $siswaTerlambat }} <span class="fs-6 text-muted fw-normal">Siswa</span></h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:48px;height:48px;background:linear-gradient(135deg,{{ $isScanOpen ? '#198754,#20c997' : '#dc3545,#c82333' }});border-radius:14px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:22px;" class="flex-shrink-0">
                        <i class="bi {{ $isScanOpen ? 'bi-clock-fill' : 'bi-shield-lock-fill' }}"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Status & Sisa Waktu Scan</div>
                        <h6 class="mb-1">
                            <span class="badge {{ $isScanOpen ? 'bg-success' : 'bg-danger' }} px-2.5 py-1 fs-6">
                                @if($isScanOpen)
                                    DIBUKA OTOMATIS
                                @elseif($isPastLimit)
                                    DITUTUP OTOMATIS
                                @else
                                    BELUM DIBUKA
                                @endif
                            </span>
                        </h6>
                        <small class="text-muted font-monospace d-block">
                            @if($isScanOpen)
                                Sisa: <strong id="operator-dashboard-countdown" class="text-danger font-monospace">00:00:00</strong>
                            @elseif($isPastLimit)
                                Scan QR ditutup 06.31 WIB
                            @else
                                Buka 06:00:00 WIB
                            @endif
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 5. RINGKASAN OPERATOR --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 bg-white">
                <div class="card-header bg-light border-0 rounded-top-4 p-3 d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="bi bi-shield-check me-2 text-primary"></i> Ringkasan Sesi Operasional
                    </h6>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">
                        <i class="bi bi-check-circle-fill me-1"></i> Sistem Normal
                    </span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <div class="p-3 bg-light rounded-3 border">
                                <div class="text-muted small mb-1">Nama Petugas Operator</div>
                                <div class="fw-bold text-dark fs-6">{{ Auth::user()->name }}</div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="p-3 bg-light rounded-3 border">
                                <div class="text-muted small mb-1">Hak Akses Sistem (Role)</div>
                                <div class="fw-bold text-primary fs-6">Operator Operasional</div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="p-3 bg-light rounded-3 border">
                                <div class="text-muted small mb-1">Cakupan Tugas Mandiri</div>
                                <div class="fw-semibold text-muted small">Memantau Scan QR Otomatis & Input Siswa Terlambat</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@push('js')
<script>
    function updateClockAndCountdown() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const clockEl = document.getElementById('current-clock');
        if (clockEl) {
            clockEl.innerHTML = `<i class="bi bi-clock text-primary me-1"></i> ${hours}:${minutes}:${seconds} WIB`;
        }

        const cdEl = document.getElementById('operator-dashboard-countdown');
        if (cdEl) {
            const targetTime = new Date(now);
            targetTime.setHours(6, 31, 0, 0);
            const diff = targetTime.getTime() - now.getTime();

            if (diff > 0 && now.getHours() === 6 && now.getMinutes() < 31) {
                const totalSecs = Math.floor(diff / 1000);
                const h = String(Math.floor(totalSecs / 3600)).padStart(2, '0');
                const m = String(Math.floor((totalSecs % 3600) / 60)).padStart(2, '0');
                const s = String(totalSecs % 60).padStart(2, '0');
                cdEl.textContent = `${h}:${m}:${s}`;
            } else {
                cdEl.textContent = "00:00:00";
            }
        }
    }
    setInterval(updateClockAndCountdown, 1000);
    updateClockAndCountdown();
</script>
@endpush
