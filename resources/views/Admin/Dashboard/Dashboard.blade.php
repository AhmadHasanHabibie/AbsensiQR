@extends('Layouts.LayoutAdmin')

@section('title', 'Dashboard')

@section('content')

@php
    $isAdminScanOpen  = \App\Services\AttendanceTimeService::isAttendanceOpen();
    $isAdminPastLimit = \App\Services\AttendanceTimeService::isAttendanceExpired();
    $now              = \Carbon\Carbon::now('Asia/Jakarta');
@endphp

<style>
/* ─── Dashboard Premium CSS ─────────────────────────────── */
:root {
    --db-primary      : #2563EB;
    --db-primary-dark : #1D4ED8;
    --db-success      : #16A34A;
    --db-warning      : #F59E0B;
    --db-danger       : #EF4444;
    --db-info         : #0891B2;
    --db-bg           : #F8FAFC;
    --db-card         : #FFFFFF;
    --db-border       : #E5E7EB;
    --db-text         : #111827;
    --db-muted        : #6B7280;
    --db-radius       : 20px;
    --db-radius-inner : 16px;
    --db-shadow       : 0 8px 24px rgba(15,23,42,.05);
    --db-shadow-hover : 0 16px 40px rgba(37,99,235,.10);
    --db-transition   : .25s ease;
}

/* Page background */
.page-content { background: var(--db-bg); }

/* ─── STAT CARD ─── */
.stat-card {
    background    : var(--db-card);
    border        : 1px solid var(--db-border);
    border-radius : var(--db-radius);
    box-shadow    : var(--db-shadow);
    transition    : var(--db-transition);
    overflow      : hidden;
    height        : 100%;
}
.stat-card:hover {
    transform  : translateY(-3px);
    box-shadow : var(--db-shadow-hover);
}
.stat-card-body { padding: 28px; }

/* ─── ICON CONTAINER ─── */
.icon-wrap {
    width          : 64px;
    height         : 64px;
    border-radius  : 18px;
    display        : flex;
    align-items    : center;
    justify-content: center;
    font-size      : 30px;
    flex-shrink    : 0;
}
.icon-wrap-blue    { background: #EFF6FF; color: var(--db-primary); }
.icon-wrap-green   { background: #F0FDF4; color: var(--db-success); }
.icon-wrap-amber   { background: #FFFBEB; color: var(--db-warning); }
.icon-wrap-red     { background: #FEF2F2; color: var(--db-danger); }
.icon-wrap-cyan    { background: #ECFEFF; color: var(--db-info); }
.icon-wrap-purple  { background: #F5F3FF; color: #7C3AED; }

/* ─── STAT NUMBER ─── */
.stat-number {
    font-size   : 44px;
    font-weight : 700;
    line-height : 1;
    letter-spacing: -1px;
}
.stat-label {
    font-size   : 13px;
    font-weight : 600;
    color       : var(--db-muted);
    text-transform: uppercase;
    letter-spacing: .6px;
    margin-bottom: 6px;
}
.stat-sub {
    font-size  : 13px;
    color      : var(--db-muted);
    margin-top : 6px;
}

/* ─── HERO BANNER ─── */
.hero-banner {
    background    : linear-gradient(135deg, #2563eb 0%, #3b82f6 55%, #7c3aed 100%);
    border-radius : 24px;
    padding       : 36px 36px;
    position      : relative;
    overflow      : hidden;
    box-shadow    : 0 20px 60px rgba(37,99,235,.3);
}
.hero-banner::before {
    content       : '';
    position      : absolute;
    inset         : 0;
    background    : radial-gradient(ellipse at top right, rgba(255,255,255,.12) 0%, transparent 65%);
    pointer-events: none;
}
.hero-banner::after {
    content       : '';
    position      : absolute;
    width         : 280px;
    height        : 280px;
    border-radius : 50%;
    background    : rgba(255,255,255,.04);
    top           : -80px;
    right         : -60px;
    pointer-events: none;
}
.hero-icon {
    width          : 72px;
    height         : 72px;
    border-radius  : 20px;
    background     : rgba(255,255,255,0.18);
    backdrop-filter: blur(12px);
    border         : 1px solid rgba(255,255,255,.25);
    display        : flex;
    align-items    : center;
    justify-content: center;
    font-size      : 34px;
    color          : #fff;
    flex-shrink    : 0;
}
.hero-title {
    font-size  : 28px;
    font-weight: 700;
    color      : #fff;
    margin     : 0;
    line-height: 1.2;
}
.hero-sub {
    font-size  : 15px;
    color      : rgba(255,255,255,.78);
    margin     : 6px 0 0;
    line-height: 1.5;
}

/* ─── ATTENDANCE BLOCK CARDS ─── */
.att-block {
    border-radius  : var(--db-radius-inner);
    padding        : 22px 20px;
    display        : flex;
    flex-direction : column;
    align-items    : center;
    gap            : 6px;
    transition     : var(--db-transition);
}
.att-block:hover { transform: scale(1.015); }
.att-block-num {
    font-size  : 36px;
    font-weight: 700;
    line-height: 1;
}
.att-block-label {
    font-size  : 12px;
    font-weight: 600;
    letter-spacing: .5px;
    text-transform: uppercase;
}

/* ─── SECTION CARD ─── */
.section-card {
    background    : var(--db-card);
    border        : 1px solid var(--db-border);
    border-radius : var(--db-radius);
    box-shadow    : var(--db-shadow);
    overflow      : hidden;
}
.section-card-header {
    padding    : 22px 28px 18px;
    border-bottom: 1px solid var(--db-border);
    background   : #fff;
}
.section-card-body { padding: 28px; }

/* ─── BADGE STATUS ─── */
.scan-badge {
    display        : inline-flex;
    align-items    : center;
    gap            : 8px;
    padding        : 6px 16px;
    border-radius  : 999px;
    font-size      : 13px;
    font-weight    : 600;
}
.scan-badge-open   { background: #DCFCE7; color: #15803D; }
.scan-badge-closed { background: #F1F5F9; color: #475569; }
.scan-badge-soon   { background: #FEF9C3; color: #92400E; }
.dot-pulse {
    width          : 8px;
    height         : 8px;
    border-radius  : 50%;
    background     : #22C55E;
    box-shadow     : 0 0 0 0 rgba(34,197,94,.6);
    animation      : pulse-green 1.8s infinite;
}
@keyframes pulse-green {
    0%  { box-shadow: 0 0 0 0 rgba(34,197,94,.6); }
    70% { box-shadow: 0 0 0 8px rgba(34,197,94,0); }
    100%{ box-shadow: 0 0 0 0 rgba(34,197,94,0); }
}

/* ─── FADE-UP ANIMATION ─── */
@keyframes fadeUp {
    from { opacity:0; transform:translateY(18px); }
    to   { opacity:1; transform:translateY(0); }
}
.fade-up { animation: fadeUp .45s ease both; }
.fade-up-1 { animation-delay:.05s; }
.fade-up-2 { animation-delay:.10s; }
.fade-up-3 { animation-delay:.15s; }
.fade-up-4 { animation-delay:.20s; }
.fade-up-5 { animation-delay:.25s; }
.fade-up-6 { animation-delay:.30s; }
</style>

<div class="container-fluid px-0">

    {{-- ─── HERO BANNER ───────────────────────── --}}
    <div class="hero-banner mb-4 fade-up fade-up-1">
        <div class="d-flex align-items-center gap-4">
            <div class="hero-icon">
                <i class="bi bi-speedometer2"></i>
            </div>
            <div>
                <h1 class="hero-title">Selamat Datang, {{ Auth::user()->name }} 👋</h1>
                <p class="hero-sub">
                    Sistem Absensi QR Code &mdash; <strong style="color:#fff;">SMKN 17 Jakarta</strong>
                    &nbsp;·&nbsp; {{ $now->isoFormat('dddd, D MMMM YYYY') }}
                </p>
            </div>
        </div>
    </div>

    {{-- ─── ROW 1: STAT CARDS ────────────────── --}}
    <div class="row g-4 mb-4">

        {{-- Total Guru --}}
        <div class="col-xl-4 col-md-6 fade-up fade-up-2">
            <div class="stat-card">
                <div class="stat-card-body d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-label">Total Guru</p>
                        <div class="stat-number text-primary">{{ $totalGuru }}</div>
                        <p class="stat-sub"><i class="bi bi-person-workspace me-1"></i>Tenaga Pengajar Aktif</p>
                    </div>
                    <div class="icon-wrap icon-wrap-blue">
                        <i class="bi bi-person-workspace"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Siswa --}}
        <div class="col-xl-4 col-md-6 fade-up fade-up-3">
            <div class="stat-card">
                <div class="stat-card-body d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-label">Total Siswa</p>
                        <div class="stat-number" style="color:var(--db-success);">{{ $totalSiswa }}</div>
                        <p class="stat-sub"><i class="bi bi-people me-1"></i>Siswa Terdaftar</p>
                    </div>
                    <div class="icon-wrap icon-wrap-green">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Kelas --}}
        <div class="col-xl-4 col-md-12 fade-up fade-up-4">
            <div class="stat-card">
                <div class="stat-card-body d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-label">Total Kelas</p>
                        <div class="stat-number" style="color:var(--db-warning);">{{ $totalKelas }}</div>
                        <p class="stat-sub"><i class="bi bi-building me-1"></i>Rombongan Belajar Aktif</p>
                    </div>
                    <div class="icon-wrap icon-wrap-amber">
                        <i class="bi bi-building"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ─── ROW 2: STATUS CARDS ───────────────── --}}
    <div class="row g-4 mb-4">

        {{-- Status Scan QR --}}
        <div class="col-xl-6 fade-up fade-up-5">
            <div class="stat-card">
                <div class="stat-card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <p class="stat-label mb-0">Status Scan QR Hari Ini</p>
                        <div class="icon-wrap {{ $isAdminScanOpen ? 'icon-wrap-green' : 'icon-wrap-cyan' }}" style="width:48px;height:48px;font-size:22px;border-radius:14px;">
                            <i class="bi {{ $isAdminScanOpen ? 'bi-qr-code-scan' : 'bi-shield-lock-fill' }}"></i>
                        </div>
                    </div>

                    {{-- Badge Status --}}
                    <div class="mb-3">
                        @if($isAdminScanOpen)
                            <span class="scan-badge scan-badge-open">
                                <span class="dot-pulse"></span>
                                Scan Dibuka Otomatis
                            </span>
                        @elseif($isAdminPastLimit)
                            <span class="scan-badge scan-badge-closed">
                                <span style="width:8px;height:8px;border-radius:50%;background:#94A3B8;display:inline-block;"></span>
                                Scan Ditutup Otomatis
                            </span>
                        @else
                            <span class="scan-badge scan-badge-soon">
                                <i class="bi bi-clock-history"></i>
                                Belum Dibuka
                            </span>
                        @endif
                    </div>

                    <p class="stat-sub mb-0">
                        <i class="bi bi-clock me-1"></i>
                        Jadwal tutup sistem: <strong>06:31 WIB</strong>
                    </p>
                </div>
            </div>
        </div>

        {{-- Absensi Darurat --}}
        <div class="col-xl-6 fade-up fade-up-6">
            <a href="{{ route('admin.emergency-audit.index') }}" class="text-decoration-none d-block h-100">
                <div class="stat-card h-100">
                    <div class="stat-card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <p class="stat-label mb-0">Absensi Darurat</p>
                                <span class="badge rounded-pill mt-1" style="background:#FEF3C7;color:#92400E;font-size:11px;font-weight:600;padding:4px 10px;">Hari Ini</span>
                            </div>
                            <div class="icon-wrap icon-wrap-amber" style="width:48px;height:48px;font-size:22px;border-radius:14px;">
                                <i class="bi bi-shield-exclamation"></i>
                            </div>
                        </div>

                        <div class="stat-number" style="color:var(--db-warning);font-size:40px;">
                            {{ $totalEmergencyHariIni ?? 0 }}
                        </div>
                        <p class="stat-sub mb-2"><i class="bi bi-person-check me-1"></i>Presensi Manual</p>
                        <div class="mt-3">
                            <span style="font-size:13px;font-weight:600;color:var(--db-primary);">
                                <i class="bi bi-arrow-right-circle me-1"></i>Buka Audit Center
                            </span>
                        </div>
                    </div>
                </div>
            </a>
        </div>

    </div>

    {{-- ─── ROW 3: STATISTIK KEHADIRAN ────────── --}}
    <div class="section-card fade-up" style="animation-delay:.35s;">
        <div class="section-card-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <div class="icon-wrap icon-wrap-blue" style="width:44px;height:44px;font-size:20px;border-radius:14px;">
                    <i class="bi bi-bar-chart-fill"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0" style="color:var(--db-text);font-size:18px;">Statistik Kehadiran</h5>
                    <p class="mb-0" style="font-size:13px;color:var(--db-muted);">Rekap kehadiran siswa hari ini</p>
                </div>
            </div>
            <span class="badge rounded-pill" style="background:#EFF6FF;color:var(--db-primary);font-size:12px;font-weight:600;padding:6px 14px;">
                {{ $now->isoFormat('D MMM YYYY') }}
            </span>
        </div>

        <div class="section-card-body">
            <div class="row g-3">

                {{-- Hadir --}}
                <div class="col-6 col-md-3">
                    <div class="att-block" style="background:#F0FDF4;">
                        <div class="icon-wrap icon-wrap-green" style="width:52px;height:52px;font-size:24px;border-radius:15px;">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <div class="att-block-num" style="color:var(--db-success);">{{ $hadirHariIni }}</div>
                        <div class="att-block-label" style="color:#15803D;">Hadir</div>
                    </div>
                </div>

                {{-- Izin --}}
                <div class="col-6 col-md-3">
                    <div class="att-block" style="background:#FFFBEB;">
                        <div class="icon-wrap icon-wrap-amber" style="width:52px;height:52px;font-size:24px;border-radius:15px;">
                            <i class="bi bi-envelope-fill"></i>
                        </div>
                        <div class="att-block-num" style="color:var(--db-warning);">{{ $izinHariIni }}</div>
                        <div class="att-block-label" style="color:#92400E;">Izin</div>
                    </div>
                </div>

                {{-- Sakit --}}
                <div class="col-6 col-md-3">
                    <div class="att-block" style="background:#ECFEFF;">
                        <div class="icon-wrap icon-wrap-cyan" style="width:52px;height:52px;font-size:24px;border-radius:15px;">
                            <i class="bi bi-heart-pulse-fill"></i>
                        </div>
                        <div class="att-block-num" style="color:var(--db-info);">{{ $sakitHariIni }}</div>
                        <div class="att-block-label" style="color:#0E7490;">Sakit</div>
                    </div>
                </div>

                {{-- Alpa --}}
                <div class="col-6 col-md-3">
                    <div class="att-block" style="background:#FEF2F2;">
                        <div class="icon-wrap icon-wrap-red" style="width:52px;height:52px;font-size:24px;border-radius:15px;">
                            <i class="bi bi-x-circle-fill"></i>
                        </div>
                        <div class="att-block-num" style="color:var(--db-danger);">{{ $alpaHariIni }}</div>
                        <div class="att-block-label" style="color:#B91C1C;">Alpa</div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

@endsection
