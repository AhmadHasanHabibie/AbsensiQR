<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Super Administrator (System Owner)</title>

    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @stack('styles')

    <style>
        /* Hide native Edge browser password reveal button */
        input::-ms-reveal,
        input::-ms-clear {
            display: none !important;
        }

        :root {
            --sa-bg-light: #f0f4f8;
            --sa-card-bg: #ffffff;
            --sa-border: #e2e8f0;
            --sa-primary: #0284c7;
            --sa-primary-dark: #0369a1;
            --sa-sidebar-bg: #0a1628;
            --sa-text-main: #1e293b;
            --sa-text-muted: #64748b;
            --sidebar-width: 270px;
            --transition-speed: 0.25s;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--sa-bg-light);
            color: var(--sa-text-main);
            overflow-x: hidden;
            min-height: 100vh;
        }

        /* Global Page Scrollbar */
        html, body {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f1f5f9;
        }

        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Dedicated Sidebar Custom Scrollbar */
        .sa-sidebar,
        .sidebar-menu,
        .sa-sidebar * {
            scrollbar-width: thin;
            scrollbar-color: #355C9A transparent;
        }

        .sa-sidebar::-webkit-scrollbar,
        .sidebar-menu::-webkit-scrollbar {
            width: 4px !important;
        }

        .sa-sidebar::-webkit-scrollbar-track,
        .sidebar-menu::-webkit-scrollbar-track {
            background: transparent !important;
        }

        .sa-sidebar::-webkit-scrollbar-thumb,
        .sidebar-menu::-webkit-scrollbar-thumb {
            background: #355C9A !important;
            border-radius: 20px !important;
        }

        /* Fix Laravel Pagination Giant SVG Arrows Bug */
        .pagination svg,
        nav[role="navigation"] svg,
        .page-link svg,
        svg.w-5.h-5,
        svg.w-4.h-4 {
            width: 1.25rem !important;
            height: 1.25rem !important;
            max-width: 1.25rem !important;
            max-height: 1.25rem !important;
            display: inline-block !important;
            vertical-align: middle !important;
        }

        nav[role="navigation"] {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        /* ====================== SIDEBAR ====================== */
        .sidebar {
            background: linear-gradient(180deg, #0a1628 0%, #0f172a 100%);
            color: #fff;
            display: flex;
            flex-direction: column;
            z-index: 1045;
            transition: all var(--transition-speed) ease;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.12);
        }

        @media (min-width: 992px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                width: var(--sidebar-width);
                height: 100vh;
            }
            .main-content {
                margin-left: var(--sidebar-width);
            }
        }

        @media (max-width: 991.98px) {
            .main-content {
                margin-left: 0 !important;
            }
            .sidebar.offcanvas-lg {
                width: 290px;
                max-width: 85%;
            }
        }

        .sidebar-brand {
            padding: 24px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            text-align: center;
            background: rgba(0, 0, 0, 0.2);
        }

        .brand-icon {
            width: 52px;
            height: 52px;
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: #fff;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            font-size: 26px;
            box-shadow: 0 8px 20px rgba(2, 132, 199, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .brand-title {
            font-size: 16px;
            font-weight: 800;
            letter-spacing: 0.3px;
            color: #fff;
            margin: 0;
        }

        .brand-subtitle {
            font-size: 10px;
            font-weight: 700;
            color: #38bdf8;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-top: 4px;
        }

        .sidebar-menu {
            flex: 1;
            padding: 14px 10px;
            overflow-y: auto;
        }

        .menu-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #64748b;
            padding: 14px 12px 6px;
            font-weight: 700;
        }

        .sidebar-menu .nav-link {
            display: flex;
            align-items: center;
            gap: 11px;
            color: #94a3b8;
            padding: 11px 14px;
            border-radius: 10px;
            margin-bottom: 2px;
            font-size: 13.5px;
            font-weight: 600;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .sidebar-menu .nav-link i {
            font-size: 17px;
            width: 20px;
            text-align: center;
            color: #64748b;
            transition: color 0.2s ease;
            flex-shrink: 0;
        }

        .sidebar-menu .nav-link:hover {
            background: rgba(255, 255, 255, 0.07);
            color: #e2e8f0;
            transform: translateX(3px);
        }

        .sidebar-menu .nav-link:hover i {
            color: #38bdf8;
        }

        .sidebar-menu .nav-link.active {
            background: linear-gradient(135deg, rgba(2,132,199,0.25) 0%, rgba(3,105,161,0.2) 100%);
            color: #ffffff;
            border: 1px solid rgba(2, 132, 199, 0.3);
            box-shadow: 0 2px 10px rgba(2, 132, 199, 0.15);
            font-weight: 700;
        }

        .sidebar-menu .nav-link.active i {
            color: #38bdf8;
        }

        .sidebar-footer {
            padding: 14px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(0, 0, 0, 0.15);
        }

        .system-badge {
            background: rgba(2, 132, 199, 0.15);
            border: 1px solid rgba(56, 189, 248, 0.2);
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* ====================== TOP NAVBAR ====================== */
        .top-navbar {
            background: #ffffff;
            border-bottom: 1px solid var(--sa-border);
            padding: 12px 20px;
            position: sticky;
            top: 0;
            z-index: 1030;
            box-shadow: 0 1px 8px rgba(0, 0, 0, 0.05);
        }

        /* ====================== CARD UTILITIES ====================== */
        .transition-all {
            transition: all 0.25s ease;
        }

        .border-hover:hover {
            border-color: var(--sa-primary) !important;
            transform: translateY(-3px);
            box-shadow: 0 10px 24px rgba(2, 132, 199, 0.14) !important;
        }

        /* ====================== TYPOGRAPHY ====================== */
        .fs-11 { font-size: 11px !important; }
        .fs-12 { font-size: 12px !important; }
        .fs-13 { font-size: 13px !important; }
        .fs-14 { font-size: 14px !important; }
        .fs-15 { font-size: 15px !important; }
        .letter-spacing-1 { letter-spacing: 1px; }

        /* ====================== TOAST SYSTEM ====================== */
        #sa-toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 99999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            pointer-events: none;
        }

        .sa-toast {
            min-width: 300px;
            max-width: 400px;
            border-radius: 14px;
            padding: 14px 18px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
            pointer-events: auto;
            transform: translateX(120%);
            opacity: 0;
            transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
            border: 1px solid;
        }

        .sa-toast.show {
            transform: translateX(0);
            opacity: 1;
        }

        .sa-toast.hiding {
            transform: translateX(120%);
            opacity: 0;
            transition: all 0.3s ease;
        }

        .sa-toast.success {
            background: #f0fdf4;
            border-color: #bbf7d0;
            color: #166534;
        }

        .sa-toast.error {
            background: #fef2f2;
            border-color: #fecaca;
            color: #991b1b;
        }

        .sa-toast.warning {
            background: #fffbeb;
            border-color: #fed7aa;
            color: #92400e;
        }

        .sa-toast.info {
            background: #eff6ff;
            border-color: #bfdbfe;
            color: #1e40af;
        }

        .sa-toast-icon {
            font-size: 20px;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .sa-toast-body {
            flex: 1;
        }

        .sa-toast-title {
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .sa-toast-msg {
            font-size: 13px;
            font-weight: 500;
            opacity: 0.85;
        }

        .sa-toast-close {
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            font-size: 16px;
            opacity: 0.5;
            line-height: 1;
            flex-shrink: 0;
            transition: opacity 0.15s;
        }

        .sa-toast-close:hover { opacity: 1; }

        /* ====================== CUSTOM MODAL ====================== */
        .sa-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            z-index: 99998;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            opacity: 0;
            visibility: hidden;
            transition: all 0.25s ease;
            backdrop-filter: blur(4px);
        }

        .sa-modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .sa-modal-box {
            background: #fff;
            border-radius: 20px;
            padding: 32px;
            max-width: 440px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            transform: scale(0.92) translateY(20px);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .sa-modal-overlay.active .sa-modal-box {
            transform: scale(1) translateY(0);
        }

        .sa-modal-icon-wrap {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 32px;
        }

        .sa-modal-icon-wrap.danger  { background: #fef2f2; color: #dc2626; }
        .sa-modal-icon-wrap.warning { background: #fffbeb; color: #d97706; }
        .sa-modal-icon-wrap.info    { background: #eff6ff; color: #2563eb; }
        .sa-modal-icon-wrap.success { background: #f0fdf4; color: #16a34a; }

        .sa-modal-title {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .sa-modal-desc {
            font-size: 14px;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 24px;
        }

        .sa-modal-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
        }

        .sa-modal-btn {
            padding: 10px 28px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            border: 2px solid transparent;
        }

        .sa-modal-btn-cancel {
            background: #f1f5f9;
            color: #475569;
            border-color: #e2e8f0;
        }

        .sa-modal-btn-cancel:hover {
            background: #e2e8f0;
        }

        .sa-modal-btn-confirm.danger {
            background: #dc2626;
            color: #fff;
        }

        .sa-modal-btn-confirm.danger:hover {
            background: #b91c1c;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(220,38,38,0.3);
        }

        .sa-modal-btn-confirm.warning {
            background: #d97706;
            color: #fff;
        }

        .sa-modal-btn-confirm.warning:hover {
            background: #b45309;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(217,119,6,0.3);
        }

        .sa-modal-btn-confirm.success {
            background: #16a34a;
            color: #fff;
        }

        .sa-modal-btn-confirm.success:hover {
            background: #15803d;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(22,163,74,0.3);
        }

        .sa-modal-btn-confirm.info {
            background: #2563eb;
            color: #fff;
        }

        .sa-modal-btn-confirm.info:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37,99,235,0.3);
        }

        .sa-modal-btn-loading {
            display: none;
            align-items: center;
            gap: 8px;
        }

        .sa-modal-btn-loading.active {
            display: flex;
        }

        .sa-loading-spinner {
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,0.4);
            border-top-color: #fff;
            border-radius: 50%;
            animation: sa-spin 0.7s linear infinite;
        }

        @keyframes sa-spin {
            to { transform: rotate(360deg); }
        }

        /* ====================== EMPTY STATE ====================== */
        .sa-empty-state {
            padding: 60px 20px;
            text-align: center;
        }

        .sa-empty-icon {
            width: 80px;
            height: 80px;
            background: #f1f5f9;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 32px;
            color: #94a3b8;
        }

        .sa-empty-title {
            font-size: 16px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 6px;
        }

        .sa-empty-desc {
            font-size: 13px;
            color: #94a3b8;
        }

        /* ====================== LOADING OVERLAY ====================== */
        .sa-page-loading {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.5);
            z-index: 99997;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(2px);
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s ease;
        }

        .sa-page-loading.active {
            opacity: 1;
            visibility: visible;
        }

        .sa-loading-box {
            background: #fff;
            border-radius: 20px;
            padding: 32px 40px;
            text-align: center;
            box-shadow: 0 20px 50px rgba(0,0,0,0.2);
        }

        .sa-loading-ring {
            width: 48px;
            height: 48px;
            border: 4px solid #e2e8f0;
            border-top-color: var(--sa-primary);
            border-radius: 50%;
            animation: sa-spin 0.8s linear infinite;
            margin: 0 auto 16px;
        }
    </style>
</head>
<body>

@php
    $isGlobalTestingActive = \App\Services\AttendanceTimeService::isTestingModeActive();
@endphp

@if($isGlobalTestingActive)
<div class="alert alert-warning border-0 rounded-0 shadow-sm text-center py-2.5 px-3 mb-0 sticky-top d-flex align-items-center justify-content-center gap-2" style="z-index: 1045; background: linear-gradient(90deg, #f59e0b, #d97706); color: #ffffff; font-weight: 700; font-size: 14px;">
    <span class="fs-5">🧪</span>
    <span><strong>MODE TESTING AKTIF</strong> &mdash; Sistem sedang dalam mode pengujian. Aturan waktu/lock tertentu sedang disimulasikan.</span>
</div>
@endif

    <!-- ====================== TOAST CONTAINER ====================== -->
    <div id="sa-toast-container"></div>

    <!-- ====================== GLOBAL LOGOUT MODAL ====================== -->
    <div class="sa-modal-overlay" id="modalLogout">
        <div class="sa-modal-box">
            <div class="sa-modal-icon-wrap warning">
                <i class="bi bi-box-arrow-right"></i>
            </div>
            <div class="sa-modal-title">Konfirmasi Logout</div>
            <div class="sa-modal-desc">
                Apakah Anda yakin ingin keluar dari <strong>Super Administrator Panel</strong>?<br>
                Sesi aktif akan dihentikan.
            </div>
            <div class="sa-modal-actions">
                <button type="button" class="sa-modal-btn sa-modal-btn-cancel" onclick="saModalClose('modalLogout')">
                    <i class="bi bi-x-circle me-1"></i> Batal
                </button>
                <button type="button" class="sa-modal-btn sa-modal-btn-confirm warning" id="btnLogoutConfirm"
                        onclick="document.getElementById('logoutForm').submit(); saLoadingShow('Sedang logout...')">
                    <span id="logoutBtnText"><i class="bi bi-box-arrow-right me-1"></i> Ya, Logout</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ====================== TESTING MODE OFF CONFIRMATION MODAL ====================== -->
    <div class="sa-modal-overlay" id="modalTestingModeOff">
        <div class="sa-modal-box">
            <div class="sa-modal-icon-wrap warning">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
            <div class="sa-modal-title">Nonaktifkan Testing Mode?</div>
            <div class="sa-modal-desc">
                SuperAdmin akan kembali ke mode normal.<br>
                Sesi testing akan dihentikan.
            </div>
            <div class="sa-modal-actions">
                <button type="button" class="sa-modal-btn sa-modal-btn-cancel" onclick="cancelTestingModeOff()">
                    <i class="bi bi-x-circle me-1"></i> Batal
                </button>
                <button type="button" class="sa-modal-btn sa-modal-btn-confirm warning" onclick="confirmDisableTestingMode()">
                    <i class="bi bi-power me-1"></i> Nonaktifkan
                </button>
            </div>
        </div>
    </div>

    <!-- ====================== TESTING FEATURE INTERCEPT MODAL ====================== -->
    <div class="sa-modal-overlay" id="modalTestingFeatureNotice">
        <div class="sa-modal-box" style="max-width: 440px;">
            <div class="sa-modal-icon-wrap warning" style="background: rgba(245,158,11,0.15); color: #d97706;">
                <i class="bi bi-bug-fill"></i>
            </div>
            <div class="sa-modal-title text-dark fw-bold">🧪 TESTING MODE</div>
            <div class="sa-modal-desc text-dark text-start mt-2 fs-13">
                Anda sedang membuka fitur dalam mode pengujian SuperAdmin.<br><br>
                <div class="p-2.5 rounded-3 bg-light border text-muted small">
                    <i class="bi bi-shield-check text-success me-1"></i> Mode ini tidak mengubah aturan production pengguna lain.
                </div>
            </div>
            <div class="sa-modal-actions mt-3">
                <button type="button" class="sa-modal-btn sa-modal-btn-cancel" onclick="cancelTestingFeatureOpen()">
                    <i class="bi bi-x-circle me-1"></i> Batal
                </button>
                <button type="button" class="sa-modal-btn sa-modal-btn-confirm success" onclick="proceedTestingFeatureOpen()">
                    <i class="bi bi-arrow-right-circle me-1"></i> Lanjutkan
                </button>
            </div>
        </div>
    </div>

    <!-- ====================== PAGE LOADING OVERLAY ====================== -->
    <div class="sa-page-loading" id="saPageLoading">
        <div class="sa-loading-box">
            <div class="sa-loading-ring"></div>
            <div class="fw-bold text-dark fs-14" id="saLoadingText">Memproses...</div>
            <div class="text-muted small mt-1">Mohon tunggu sebentar</div>
        </div>
    </div>

    <!-- SIDEBAR (Offcanvas for mobile/tablet) -->
    <div class="sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
        <div class="sidebar-brand">
            <div class="brand-icon">
                <i class="bi bi-shield-lock-fill"></i>
            </div>
            <h1 class="brand-title">Super Admin</h1>
            <div class="brand-subtitle">System Owner &amp; Maintenance</div>
        </div>

        <div class="sidebar-menu">
            <div class="menu-label">Utama</div>
            <a href="{{ route('superadmin.dashboard') }}" class="nav-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>

            <div class="menu-label">Developer Center</div>
            <a href="{{ route('superadmin.monitoring.index') }}" class="nav-link {{ request()->routeIs('superadmin.monitoring.*') ? 'active' : '' }}">
                <i class="bi bi-activity"></i>
                <span>Monitoring Sistem</span>
            </a>
            <a href="{{ route('superadmin.server-info.index') }}" class="nav-link {{ request()->routeIs('superadmin.server-info.*') ? 'active' : '' }}">
                <i class="bi bi-cpu"></i>
                <span>Informasi Server</span>
            </a>
            <a href="{{ route('superadmin.backup.index') }}" class="nav-link {{ request()->routeIs('superadmin.backup.*') ? 'active' : '' }}">
                <i class="bi bi-database-down"></i>
                <span>Backup Database</span>
            </a>
            <a href="{{ route('superadmin.maintenance.index') }}" class="nav-link {{ request()->routeIs('superadmin.maintenance.*') ? 'active' : '' }}">
                <i class="bi bi-tools"></i>
                <span>Pemeliharaan Sistem</span>
            </a>
            <a href="{{ route('superadmin.config.index') }}" class="nav-link {{ request()->routeIs('superadmin.config.*') ? 'active' : '' }}">
                <i class="bi bi-sliders"></i>
                <span>Konfigurasi Sistem</span>
            </a>
            <a href="{{ route('superadmin.academic-calendar.index') }}" class="nav-link {{ request()->routeIs('superadmin.academic-calendar.*') ? 'active' : '' }}">
                <i class="bi bi-calendar2-week-fill"></i>
                <span>Kalender Akademik</span>
            </a>
            <a href="{{ route('superadmin.attendance-operation.index') }}" class="nav-link {{ request()->routeIs('superadmin.attendance-operation.*') ? 'active' : '' }}">
                <i class="bi bi-shield-exclamation"></i>
                <span>Operasional Absensi</span>
            </a>

            <div class="menu-label">Audit &amp; Informasi</div>
            <a href="{{ route('superadmin.activity-log.index') }}" class="nav-link {{ request()->routeIs('superadmin.activity-log.*') ? 'active' : '' }}">
                <i class="bi bi-journal-text"></i>
                <span>Riwayat Aktivitas</span>
            </a>
            <a href="{{ route('superadmin.about.index') }}" class="nav-link {{ request()->routeIs('superadmin.about.*') ? 'active' : '' }}">
                <i class="bi bi-info-circle"></i>
                <span>Tentang Aplikasi</span>
            </a>

            <div class="menu-label text-warning d-flex align-items-center gap-1 mt-3">
                <i class="bi bi-flask text-warning"></i> 🧪 Testing Mode
            </div>
            <div class="px-3 py-2.5 my-1 mx-3 rounded-3 border border-warning border-opacity-25 bg-warning bg-opacity-10">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-bug-fill text-warning fs-5"></i>
                        <div>
                            <div class="fw-bold text-white fs-13">Testing Mode</div>
                            <div class="text-warning small" style="font-size: 10px;" id="testingModeSubtext">
                                {{ $isGlobalTestingActive ? 'STAT: ON' : 'STAT: OFF' }}
                            </div>
                        </div>
                    </div>
                    <div class="form-check form-switch m-0">
                        <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="testingModeToggle" {{ $isGlobalTestingActive ? 'checked' : '' }} onchange="handleTestingToggleChange(this)">
                    </div>
                </div>
            </div>
        </div>

        <div class="sidebar-footer">
            <div class="system-badge">
                <i class="bi bi-cpu-fill text-info fs-5"></i>
                <div>
                    <div class="fw-bold text-white fs-13">System Developer</div>
                    <div class="text-white-50" style="font-size: 10px;">Root Maintenance Mode</div>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT AREA -->
    <div class="main-content">

        <!-- TOP NAVBAR -->
        <nav class="top-navbar d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-outline-secondary btn-sm d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div class="d-none d-sm-flex align-items-center gap-2">
                    <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-2 rounded-pill fw-semibold">
                        <i class="bi bi-shield-check me-1"></i> System Owner Mode
                    </span>
                    @if(session('superadmin_testing_mode'))
                        <span class="badge bg-warning text-dark border border-warning px-3 py-2 rounded-pill fw-bold shadow-sm d-flex align-items-center gap-1">
                            <i class="bi bi-bug-fill"></i> 🧪 TESTING MODE AKTIF
                        </span>
                    @endif
                </div>
            </div>

            <!-- USER MENU -->
            <div class="d-flex align-items-center gap-3">
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center gap-2 text-decoration-none text-dark dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="bg-info-subtle text-info border border-info-subtle rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width:38px;height:38px;font-size:13px;">
                            SA
                        </div>
                        <div class="d-none d-md-block text-start">
                            <div class="fw-bold fs-14 text-dark">{{ Auth::user()->name }}</div>
                            <div class="text-muted fs-12">Super Administrator</div>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-2 rounded-3" style="min-width:220px;">
                        <li>
                            <div class="px-3 py-2 border-bottom">
                                <div class="fw-bold text-primary">{{ Auth::user()->name }}</div>
                                <div class="text-muted small">@ {{ Auth::user()->username }}</div>
                                <span class="badge bg-info-subtle text-info border border-info-subtle mt-1 fs-11">System Owner</span>
                            </div>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item text-danger py-2 fw-semibold"
                                    onclick="saModalOpen('modalLogout')">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout System
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Flash Session Toast Trigger -->
        @if(session('success'))
        <div id="flash-success-msg" class="d-none">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div id="flash-error-msg" class="d-none">{{ session('error') }}</div>
        @endif
        @if(session('warning'))
        <div id="flash-warning-msg" class="d-none">{{ session('warning') }}</div>
        @endif
        @if(session('info'))
        <div id="flash-info-msg" class="d-none">{{ session('info') }}</div>
        @endif

        <!-- PAGE CONTENT -->
        <main class="p-3 p-md-4">
            @yield('content')
        </main>
    </div>

    <!-- Invisible Logout Form -->
    <form id="logoutForm" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        /* ==================== SA MODAL SYSTEM ==================== */
        function saModalOpen(id) {
            const overlay = document.getElementById(id);
            if (overlay) {
                overlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }

        function saModalClose(id) {
            const overlay = document.getElementById(id);
            if (overlay) {
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        }

        /* Close modal on overlay click */
        document.querySelectorAll('.sa-modal-overlay').forEach(function(overlay) {
            overlay.addEventListener('click', function(e) {
                if (e.target === overlay) {
                    overlay.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });
        });

        /* Close modal on Escape key */
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.sa-modal-overlay.active').forEach(function(m) {
                    m.classList.remove('active');
                    document.body.style.overflow = '';
                });
            }
        });

        /* ==================== SA TOAST SYSTEM ==================== */
        function saToast(message, type = 'info', title = null, duration = 4500) {
            const icons = {
                success: 'bi-check-circle-fill',
                error:   'bi-exclamation-circle-fill',
                warning: 'bi-exclamation-triangle-fill',
                info:    'bi-info-circle-fill'
            };
            const titles = {
                success: 'Berhasil',
                error:   'Terjadi Kesalahan',
                warning: 'Perhatian',
                info:    'Informasi'
            };

            const container = document.getElementById('sa-toast-container');
            const toast = document.createElement('div');
            toast.className = `sa-toast ${type}`;
            toast.innerHTML = `
                <i class="bi ${icons[type] || icons.info} sa-toast-icon"></i>
                <div class="sa-toast-body">
                    <div class="sa-toast-title">${title || titles[type] || 'Notifikasi'}</div>
                    <div class="sa-toast-msg">${message}</div>
                </div>
                <button class="sa-toast-close" onclick="this.closest('.sa-toast').remove()">
                    <i class="bi bi-x"></i>
                </button>
            `;

            container.appendChild(toast);

            requestAnimationFrame(() => {
                requestAnimationFrame(() => toast.classList.add('show'));
            });

            setTimeout(() => {
                toast.classList.add('hiding');
                setTimeout(() => toast.remove(), 350);
            }, duration);
        }

        /* ==================== SA LOADING SYSTEM ==================== */
        function saLoadingShow(text = 'Memproses...') {
            const overlay = document.getElementById('saPageLoading');
            const textEl  = document.getElementById('saLoadingText');
            if (overlay) {
                if (textEl) textEl.textContent = text;
                overlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }

        function saLoadingHide() {
            const overlay = document.getElementById('saPageLoading');
            if (overlay) {
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        }

        /* ==================== FLASH SESSION TOASTS & MODAL CLEANUP ==================== */
        document.addEventListener('DOMContentLoaded', function() {
            document.addEventListener('hidden.bs.modal', function () {
                if (!document.querySelector('.modal.show')) {
                    document.querySelectorAll('.modal-backdrop').forEach(function (el) { el.remove(); });
                    document.body.classList.remove('modal-open');
                    document.body.style.removeProperty('overflow');
                    document.body.style.removeProperty('padding-right');
                }
            });

            const successEl = document.getElementById('flash-success-msg');
            const errorEl   = document.getElementById('flash-error-msg');
            const warningEl = document.getElementById('flash-warning-msg');
            const infoEl    = document.getElementById('flash-info-msg');

            if (successEl && successEl.textContent.trim()) {
                saToast(successEl.textContent.trim(), 'success');
            }
            if (errorEl && errorEl.textContent.trim()) {
                saToast(errorEl.textContent.trim(), 'error');
            }
            if (warningEl && warningEl.textContent.trim()) {
                saToast(warningEl.textContent.trim(), 'warning');
            }
            if (infoEl && infoEl.textContent.trim()) {
                saToast(infoEl.textContent.trim(), 'info');
            }
        });

        /* ==================== SUPERADMIN TESTING MODE JS ==================== */
        let pendingTestingFeatureUrl = null;

        function handleTestingToggleChange(checkbox) {
            if (!checkbox.checked) {
                // Revert switch visually first and open confirmation modal
                checkbox.checked = true;
                saModalOpen('modalTestingModeOff');
            } else {
                sendTestingModeRequest(true);
            }
        }

        function cancelTestingModeOff() {
            saModalClose('modalTestingModeOff');
            const toggle = document.getElementById('testingModeToggle');
            if (toggle) toggle.checked = true;
        }

        function confirmDisableTestingMode() {
            saModalClose('modalTestingModeOff');
            sendTestingModeRequest(false);
        }

        function sendTestingModeRequest(activeState) {
            saLoadingShow('Mengubah Mode Testing...');
            fetch("{{ route('superadmin.toggle-testing-mode') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ active: activeState })
            })
            .then(res => res.json())
            .then(data => {
                saLoadingHide();
                if (data.success) {
                    saToast(data.message, 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 800);
                } else {
                    saToast(data.message || 'Gagal mengubah Mode Testing', 'error');
                }
            })
            .catch(() => {
                saLoadingHide();
                saToast('Terjadi kesalahan pada koneksi server.', 'error');
            });
        }

        // Intercept testing features navigation when Testing Mode is ON
        document.addEventListener('DOMContentLoaded', function() {
            const isTestingActive = {{ \App\Services\AttendanceTimeService::isTestingModeActive() ? 'true' : 'false' }};
            if (!isTestingActive) return;

            // Intercept navigation for designated testing feature links
            const testingLinks = document.querySelectorAll('a[href*="attendance-operation"], a[href*="academic-calendar"], a[href*="monitoring"], a[href*="backup"], a[href*="maintenance"], a[href*="config"]');

            testingLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    pendingTestingFeatureUrl = this.getAttribute('href');
                    saModalOpen('modalTestingFeatureNotice');
                });
            });
        });

        function cancelTestingFeatureOpen() {
            saModalClose('modalTestingFeatureNotice');
            pendingTestingFeatureUrl = null;
        }

        function proceedTestingFeatureOpen() {
            saModalClose('modalTestingFeatureNotice');
            if (pendingTestingFeatureUrl) {
                saLoadingShow('Membuka Fitur Testing...');
                window.location.href = pendingTestingFeatureUrl;
            }
        }
    </script>

    @stack('scripts')
</body>
</html>
