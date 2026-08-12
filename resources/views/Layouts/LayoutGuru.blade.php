<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Guru SMKN 17</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* Hide native Edge browser password reveal button */
        input::-ms-reveal,
        input::-ms-clear {
            display: none !important;
        }

        :root {
            --primary: #198754;
            --primary-dark: #0f5132;
            --sidebar-width: 260px;
            --transition-speed: 0.3s;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f8f9fa;
            overflow-x: hidden;
            color: #1e293b;
        }

        /* Global Page Scrollbar */
        html, body, .content, div:not(.sidebar):not(.sidebar-menu):not(.offcanvas-body) {
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

        /* Dedicated Sidebar Custom Scrollbar (Dark Theme Integration) */
        .sidebar,
        .sidebar-menu,
        .offcanvas-body,
        .sidebar-wrapper,
        .sidebar * {
            scrollbar-width: thin;
            scrollbar-color: #355C9A transparent;
        }

        .sidebar::-webkit-scrollbar,
        .sidebar-menu::-webkit-scrollbar,
        .offcanvas-body::-webkit-scrollbar,
        .sidebar-wrapper::-webkit-scrollbar,
        .sidebar *::-webkit-scrollbar {
            width: 6px !important;
            height: 6px !important;
        }

        .sidebar::-webkit-scrollbar-track,
        .sidebar-menu::-webkit-scrollbar-track,
        .offcanvas-body::-webkit-scrollbar-track,
        .sidebar-wrapper::-webkit-scrollbar-track,
        .sidebar *::-webkit-scrollbar-track {
            background: transparent !important;
        }

        .sidebar::-webkit-scrollbar-thumb,
        .sidebar-menu::-webkit-scrollbar-thumb,
        .offcanvas-body::-webkit-scrollbar-thumb,
        .sidebar-wrapper::-webkit-scrollbar-thumb,
        .sidebar *::-webkit-scrollbar-thumb {
            background: #355C9A !important;
            border-radius: 20px !important;
            border: none !important;
        }

        .sidebar::-webkit-scrollbar-thumb:hover,
        .sidebar-menu::-webkit-scrollbar-thumb:hover,
        .offcanvas-body::-webkit-scrollbar-thumb:hover,
        .sidebar-wrapper::-webkit-scrollbar-thumb:hover,
        .sidebar *::-webkit-scrollbar-thumb:hover {
            background: #4B74B8 !important;
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

        .btn { transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
        .btn:hover:not(:disabled) { transform: translateY(-1px); }

        /* SIDEBAR (OFFCANVAS ON MOBILE) */
        .sidebar {
            background: linear-gradient(180deg, #0f5132 0%, #198754 100%);
            color: #fff;
            display: flex;
            flex-direction: column;
            z-index: 1045;
            transition: all var(--transition-speed) ease;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
        }

        @media (min-width: 992px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                width: var(--sidebar-width);
                height: 100vh;
            }
            .content {
                margin-left: var(--sidebar-width);
            }
        }

        @media (max-width: 991.98px) {
            .content {
                margin-left: 0 !important;
            }
            .sidebar.offcanvas-lg {
                width: 280px;
                max-width: 80%;
            }
        }

        .sidebar-header {
            text-align: center;
            padding: 24px 20px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-header .logo-icon {
            width: 56px;
            height: 56px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            font-size: 30px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-header h4 { margin: 0; font-size: 20px; font-weight: 700; letter-spacing: 0.5px; }
        .sidebar-header p { margin: 4px 0 0; font-size: 11px; opacity: 0.7; letter-spacing: 1px; text-transform: uppercase; }

        .sidebar-menu { flex: 1; padding: 12px 12px; overflow-y: auto; }
        .sidebar-menu .menu-label { font-size: 10px; text-transform: uppercase; letter-spacing: 1.5px; opacity: 0.5; padding: 16px 16px 8px; font-weight: 600; }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 4px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .sidebar a i { width: 20px; font-size: 18px; text-align: center; }
        .sidebar a:hover { background: rgba(255, 255, 255, 0.15); color: #fff; transform: translateX(4px); }
        .sidebar a.active { background: rgba(255, 255, 255, 0.95); color: var(--primary-dark); font-weight: 600; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15); }
        .sidebar a.active i { color: var(--primary); }

        .logout-form { padding: 12px; border-top: 1px solid rgba(255, 255, 255, 0.1); }
        .logout-btn {
            width: 100%; border: none; background: rgba(255, 255, 255, 0.08); color: rgba(255, 255, 255, 0.85);
            padding: 11px 16px; border-radius: 10px; text-align: left; transition: all 0.2s ease; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 12px;
        }
        .logout-btn:hover { background: rgba(255, 255, 255, 0.18); color: #fff; }

        .content { min-height: 100vh; display: flex; flex-direction: column; }

        .navbar-custom {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 14px 24px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar-custom h5 { margin: 0; font-size: 18px; font-weight: 700; color: #1a1a2e; }
        .navbar-custom .user-info { display: flex; align-items: center; gap: 12px; }
        .navbar-custom .user-avatar {
            width: 36px; height: 36px; background: linear-gradient(135deg, var(--primary), #2ecc71);
            border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 16px; font-weight: 600;
        }
        .navbar-custom .user-name { font-size: 14px; font-weight: 600; color: #1a1a2e; }
        .navbar-custom .user-role { font-size: 11px; color: #6c757d; }

        .page-content { flex: 1; padding: 24px 28px; max-width: 1600px; width: 100%; margin: 0 auto; }
        footer { text-align: center; padding: 18px; color: #6c757d; font-size: 13px; border-top: 1px solid rgba(0, 0, 0, 0.05); background: rgba(255, 255, 255, 0.5); }

        @media (max-width: 767.98px) {
            .page-content { padding: 16px 12px; }
            .navbar-custom { padding: 12px 16px; }
            .btn:not(.btn-sm), .form-control, .form-select { min-height: 44px; font-size: 15px; }
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

        <a href="{{ route('guru.dashboard') }}"
           class="{{ request()->routeIs('guru.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <a href="{{ route('guru.scan.index') }}"
           class="{{ request()->routeIs('guru.scan.*') ? 'active' : '' }}">
            <i class="bi bi-qr-code-scan"></i> Scan QR
        </a>

        <a href="{{ route('guru.attendance.index') }}"
           class="{{ request()->routeIs('guru.attendance.*') ? 'active' : '' }}">
            <i class="bi bi-clipboard-check"></i> Rekap Absensi
        </a>

        <a href="{{ route('guru.terlambat.index') }}"
           class="{{ request()->routeIs('guru.terlambat.*') ? 'active' : '' }}">
            <i class="bi bi-alarm-fill"></i> Terlambat
        </a>

        @php
            $unreadTeacherMailboxCount = \App\Models\TeacherMailbox::where('receiver_id', Auth::id())
                ->where('status', 'unread')
                ->count();
        @endphp

        <a href="{{ route('guru.mailbox.index') }}"
           class="{{ request()->routeIs('guru.mailbox.*') ? 'active' : '' }}">
            <i class="bi bi-envelope"></i> Kotak Surat
            @if ($unreadTeacherMailboxCount > 0)
                <span class="badge bg-danger rounded-pill ms-auto">{{ $unreadTeacherMailboxCount }}</span>
            @endif
        </a>

        <a href="{{ route('guru.laporan.index') }}"
           class="{{ request()->routeIs('guru.laporan.*') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-bar-graph"></i> Laporan Absensi
        </a>

        <a href="{{ route('guru.academic-calendar.index') }}"
           class="{{ request()->routeIs('guru.academic-calendar.*') ? 'active' : '' }}">
            <i class="bi bi-calendar2-week-fill"></i> Kalender Akademik
        </a>

        <a href="{{ route('guru.profil.index') }}"
           class="{{ request()->routeIs('guru.profil.*') ? 'active' : '' }}">
            <i class="bi bi-person-circle"></i> Profil
        </a>

        <a href="{{ route('guru.login-history.index') }}"
           class="{{ request()->routeIs('guru.login-history.*') ? 'active' : '' }}">
            <i class="bi bi-clock-history"></i> Riwayat Login
        </a>
    </div>

    <div class="logout-form">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="logout-btn">
                <i class="bi bi-box-arrow-right"></i> Keluar
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
                <div class="user-name">{{ Auth::user()->name }}</div>
                <div class="user-role">Guru Wali Kelas</div>
            </div>
            <div class="user-avatar">
                {{ substr(Auth::user()->name, 0, 1) }}
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
