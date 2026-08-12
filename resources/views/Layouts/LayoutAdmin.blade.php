<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Admin SMKN 17</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/admin-design.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* Hide native Edge browser password reveal button */
        input::-ms-reveal,
        input::-ms-clear {
            display: none !important;
        }

        :root {
            --primary      : #2563EB;
            --primary-dark : #1D4ED8;
            --primary-bg   : #0A1628;
            --sidebar-width: 268px;
            --transition   : 0.25s ease;
            --radius-card  : 20px;
            --shadow-soft  : 0 8px 24px rgba(15,23,42,.05);
            --shadow-hover : 0 16px 40px rgba(37,99,235,.10);
            --border-color : #E5E7EB;
            --bg-page      : #F8FAFC;
        }

        * { box-sizing: border-box; }

        body {
            font-family    : 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background     : var(--bg-page);
            overflow-x     : hidden;
            color          : #111827;
        }

        /* ── Scrollbar ── */
        html, body {
            scrollbar-width: thin;
            scrollbar-color: #CBD5E1 #F1F5F9;
        }
        ::-webkit-scrollbar        { width:6px; height:6px; }
        ::-webkit-scrollbar-track  { background:#F1F5F9; }
        ::-webkit-scrollbar-thumb  { background:#CBD5E1; border-radius:4px; }
        ::-webkit-scrollbar-thumb:hover { background:#94A3B8; }

        /* Sidebar scrollbar stays dark */
        .sidebar, .sidebar-menu, .offcanvas-body {
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,.2) transparent;
        }
        .sidebar::-webkit-scrollbar-track,
        .sidebar-menu::-webkit-scrollbar-track { background:transparent !important; }
        .sidebar::-webkit-scrollbar-thumb,
        .sidebar-menu::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,.2) !important;
            border-radius:20px !important;
        }

        /* ── Pagination SVG Fix ── */
        .pagination svg, nav[role="navigation"] svg, .page-link svg,
        svg.w-5.h-5, svg.w-4.h-4 {
            width:1.25rem !important; height:1.25rem !important;
            max-width:1.25rem !important; max-height:1.25rem !important;
            display:inline-block !important; vertical-align:middle !important;
        }
        nav[role="navigation"] {
            display:flex; align-items:center;
            justify-content:space-between; flex-wrap:wrap; gap:.5rem;
        }

        /* ── Global Cards ── */
        .card {
            border        : 1px solid var(--border-color) !important;
            border-radius : var(--radius-card) !important;
            box-shadow    : var(--shadow-soft) !important;
            transition    : var(--transition) !important;
        }
        .card:hover {
            transform  : translateY(-2px);
            box-shadow : var(--shadow-hover) !important;
        }
        .card-header {
            border-radius : var(--radius-card) var(--radius-card) 0 0 !important;
        }

        /* ── Global Buttons ── */
        .btn {
            border-radius : 14px !important;
            padding       : 8px 20px !important;
            font-weight   : 600 !important;
            font-size     : 14px !important;
            transition    : all 250ms ease-in-out !important;
            box-shadow    : 0 1px 3px rgba(0,0,0,.06) !important;
        }
        .btn-sm {
            padding       : 5px 14px !important;
            font-size     : 13px !important;
            border-radius : 10px !important;
        }
        .btn:hover:not(:disabled) {
            transform  : translateY(-1px) !important;
            box-shadow : 0 6px 20px rgba(37,99,235,.18) !important;
        }
        .btn-primary  { background:#2563EB !important; border-color:#2563EB !important; }
        .btn-primary:hover { background:#1D4ED8 !important; border-color:#1D4ED8 !important; }

        /* ─── SIDEBAR ─── */
        .sidebar {
            background    : linear-gradient(175deg, #0A1628 0%, #0F2249 55%, #1a3a6e 100%);
            color         : #fff;
            display       : flex;
            flex-direction: column;
            z-index       : 1045;
            transition    : all var(--transition);
            box-shadow    : 4px 0 32px rgba(0,0,0,.18);
        }
        @media (min-width: 992px) {
            .sidebar {
                position: fixed; top:0; left:0;
                width:var(--sidebar-width); height:100vh;
            }
            .content { margin-left:var(--sidebar-width); }
        }
        @media (max-width: 991.98px) {
            .content { margin-left:0 !important; }
            .sidebar.offcanvas-lg { width:280px; max-width:80%; }
        }

        /* Sidebar header */
        .sidebar-header {
            text-align    : center;
            padding       : 28px 20px 22px;
            border-bottom : 1px solid rgba(255,255,255,.08);
        }
        .sidebar-header .logo-icon {
            width          : 60px;
            height         : 60px;
            background     : rgba(255,255,255,.13);
            border-radius  : 18px;
            display        : flex;
            align-items    : center;
            justify-content: center;
            margin         : 0 auto 14px;
            font-size      : 32px;
            backdrop-filter: blur(10px);
            border         : 1px solid rgba(255,255,255,.12);
        }
        .sidebar-header h4 {
            margin:0; font-size:20px; font-weight:700; letter-spacing:.4px;
        }
        .sidebar-header p {
            margin:4px 0 0; font-size:11px; opacity:.6;
            letter-spacing:1.2px; text-transform:uppercase;
        }

        /* Sidebar menu */
        .sidebar-menu {
            flex:1; padding:16px 14px; overflow-y:auto;
        }
        .sidebar-menu .menu-label {
            font-size:10px; text-transform:uppercase; letter-spacing:1.5px;
            opacity:.45; padding:18px 14px 8px; font-weight:700;
        }
        .sidebar a {
            display       : flex;
            align-items   : center;
            gap           : 13px;
            color         : rgba(255,255,255,.72);
            text-decoration: none;
            padding       : 13px 16px;
            border-radius : 16px;
            margin-bottom : 4px;
            font-size     : 14px;
            font-weight   : 500;
            transition    : all .2s ease;
        }
        .sidebar a i {
            width:22px; font-size:20px; text-align:center;
        }
        .sidebar a:hover {
            background: rgba(255,255,255,.10);
            color     : #fff;
            transform : translateX(3px);
        }
        .sidebar a.active {
            background : linear-gradient(135deg, #2563EB, #3B82F6);
            color      : #fff;
            font-weight: 700;
            box-shadow : 0 6px 20px rgba(37,99,235,.35);
        }
        .sidebar a.active i { color:#fff; }

        /* Logout */
        .logout-form {
            padding: 14px;
            border-top: 1px solid rgba(255,255,255,.08);
        }
        .logout-btn {
            width       : 100%;
            border      : none;
            background  : rgba(255,255,255,.07);
            color       : rgba(255,255,255,.8);
            padding     : 12px 16px;
            border-radius: 14px;
            text-align  : left;
            transition  : all .2s ease;
            font-size   : 14px;
            font-weight : 500;
            display     : flex;
            align-items : center;
            gap         : 13px;
        }
        .logout-btn i { font-size:18px; }
        .logout-btn:hover {
            background: rgba(239,68,68,.18);
            color     : #FCA5A5;
        }

        /* ─── CONTENT ─── */
        .content {
            min-height    : 100vh;
            display       : flex;
            flex-direction: column;
        }

        /* ─── NAVBAR ─── */
        .navbar-custom {
            background    : rgba(255,255,255,.92);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-bottom : 1px solid var(--border-color);
            padding       : 14px 28px;
            position      : sticky;
            top           : 0;
            z-index       : 100;
            box-shadow    : 0 1px 12px rgba(15,23,42,.04);
        }
        .navbar-custom h5 {
            margin:0; font-size:17px; font-weight:700; color:#111827;
        }
        .navbar-custom .user-info {
            display:flex; align-items:center; gap:14px;
        }
        .navbar-custom .user-avatar {
            width          : 40px;
            height         : 40px;
            background     : linear-gradient(135deg, var(--primary), #3B82F6);
            border-radius  : 50%;
            display        : flex;
            align-items    : center;
            justify-content: center;
            color          : #fff;
            font-size      : 17px;
            font-weight    : 700;
            box-shadow     : 0 3px 10px rgba(37,99,235,.3);
        }
        .navbar-custom .user-name {
            font-size:14px; font-weight:700; color:#111827;
        }
        .navbar-custom .user-role {
            font-size:11px; color:#6B7280; font-weight:500;
        }

        .page-content {
            flex      : 1;
            padding   : 28px 32px;
            max-width : 1600px;
            width     : 100%;
            margin    : 0 auto;
        }

        footer {
            text-align : center;
            padding    : 18px;
            color      : #9CA3AF;
            font-size  : 13px;
            border-top : 1px solid var(--border-color);
            background : rgba(255,255,255,.6);
        }

        /* ── Mobile ── */
        @media (max-width: 767.98px) {
            .page-content { padding:16px 14px; }
            .navbar-custom { padding:12px 16px; }
            .btn:not(.btn-sm), .form-control, .form-select {
                min-height:44px; font-size:15px;
            }
        }
    </style>

    @stack('css')
</head>

<body>

@if(\App\Services\AttendanceTimeService::isTestingModeActive())
<div class="alert alert-warning border-0 rounded-0 shadow-sm text-center py-2.5 px-3 mb-0 sticky-top d-flex align-items-center justify-content-center gap-2" style="z-index: 1045; background: linear-gradient(90deg, #f59e0b, #d97706); color: #ffffff; font-weight: 700; font-size: 14px;">
    <span class="fs-5">🧪</span>
    <span><strong>MODE TESTING AKTIF</strong> &mdash; Sistem sedang dalam mode pengujian. Aturan waktu/lock tertentu sedang disimulasikan.</span>
</div>
@endif

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

<!-- SIDEBAR OFFCANVAS ON MOBILE / FIXED ON DESKTOP -->
<div class="sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">

    <div class="sidebar-header d-flex flex-column align-items-center position-relative">
        <button type="button" class="btn-close btn-close-white d-lg-none position-absolute top-0 end-0 m-3" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu" aria-label="Close"></button>
        <div class="logo-icon">
            <i class="bi bi-qr-code-scan"></i>
        </div>
        <h4 id="sidebarMenuLabel">SMKN 17</h4>
        <p>Jakarta</p>
    </div>

    <div class="sidebar-menu">
        <div class="menu-label">Menu Utama</div>

        <a href="{{ route('admin.dashboard') }}"
           class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>
            Dashboard
        </a>

        <a href="{{ route('admin.guru.index') }}"
           class="{{ request()->routeIs('admin.guru.*') ? 'active' : '' }}">
            <i class="bi bi-person-workspace"></i>
            Guru
        </a>

        <a href="{{ route('admin.siswa.index') }}"
           class="{{ request()->routeIs('admin.siswa.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i>
            Siswa
        </a>

        <a href="{{ route('admin.operator.index') }}"
           class="{{ request()->routeIs('admin.operator.*') ? 'active' : '' }}">
            <i class="bi bi-person-workspace"></i>
            Operator
        </a>

        <a href="{{ route('admin.guru-piket.index') }}"
           class="{{ request()->routeIs('admin.guru-piket.*') ? 'active' : '' }}">
            <i class="bi bi-person-badge"></i>
            Guru Piket
        </a>

        <a href="{{ route('admin.qr-siswa.index') }}"
           class="{{ request()->routeIs('admin.qr-siswa.*') ? 'active' : '' }}">
            <i class="bi bi-qr-code"></i>
            QR Code Siswa
        </a>

        <a href="{{ route('admin.kelas.index') }}"
           class="{{ request()->routeIs('admin.kelas.*') ? 'active' : '' }}">
            <i class="bi bi-building"></i>
            Kelas
        </a>

        <a href="{{ route('admin.academic-calendar.index') }}"
           class="{{ request()->routeIs('admin.academic-calendar.*') ? 'active' : '' }}">
            <i class="bi bi-calendar2-week-fill"></i>
            Kalender Akademik
        </a>

        <div class="menu-label">Laporan & Security</div>

        <a href="{{ route('admin.laporan.index') }}"
           class="{{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-bar-graph"></i>
            Laporan Absensi
        </a>

        <a href="{{ route('admin.mailbox.index') }}"
           class="{{ request()->routeIs('admin.mailbox.*') ? 'active' : '' }}">
            <i class="bi bi-envelope-paper"></i>
            Kotak Surat Siswa
        </a>

        <a href="{{ route('admin.login-history.index') }}"
           class="{{ request()->routeIs('admin.login-history.*') ? 'active' : '' }}">
            <i class="bi bi-clock-history"></i>
            Riwayat Login
        </a>

        <a href="{{ route('admin.activity-log.index') }}"
           class="{{ request()->routeIs('admin.activity-log.*') ? 'active' : '' }}">
            <i class="bi bi-shield-check"></i>
            Riwayat Aktivitas
        </a>

        <a href="{{ route('admin.emergency-audit.index') }}"
           class="{{ request()->routeIs('admin.emergency-audit.*') ? 'active' : '' }}">
            <i class="bi bi-shield-exclamation"></i>
            Audit Absensi Darurat
        </a>

        <a href="{{ route('admin.security-center.index') }}"
           class="{{ request()->routeIs('admin.security-center.*') || request()->routeIs('admin.blocked-ips.*') ? 'active' : '' }}">
            <i class="bi bi-shield-lock-fill"></i>
            Keamanan Sistem
        </a>
    </div>

    <div class="logout-form">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="logout-btn">
                <i class="bi bi-box-arrow-right"></i>
                Keluar
            </button>
        </form>
    </div>

</div>

<!-- CONTENT -->
<div class="content">

    <nav class="navbar navbar-custom d-flex align-items-center">
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-outline-dark border-0 btn-sm p-2 d-lg-none rounded-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" title="Buka Menu">
                <i class="bi bi-list fs-4"></i>
            </button>
            <h5 class="mb-0 text-truncate">@yield('title')</h5>
        </div>

        <div class="ms-auto user-info">
            <div class="text-end d-none d-sm-block">
                <div class="user-name">{{ Auth::user()->name ?? 'Administrator' }}</div>
                <div class="user-role">Administrator</div>
            </div>
            <div class="user-avatar">
                {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
            </div>
        </div>
    </nav>

    <div class="page-content">
        @yield('content')
    </div>

    <footer>
        © {{ date('Y') }} <strong>SMKN 17 JAKARTA</strong> &mdash; Sistem Absensi QR Code
    </footer>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Modal Lifecycle Cleanup Handler
    document.addEventListener('hidden.bs.modal', function () {
        if (!document.querySelector('.modal.show')) {
            document.querySelectorAll('.modal-backdrop').forEach(function (el) { el.remove(); });
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
        }
    });

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
