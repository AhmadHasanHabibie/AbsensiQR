@extends('Layouts.LayoutSuperAdmin')

@section('title', 'Tentang Aplikasi')

@section('content')
<div class="container-fluid px-0">

    {{-- PAGE HEADER --}}
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1" style="font-size:12px;">
                <li class="breadcrumb-item text-muted">Super Admin</li>
                <li class="breadcrumb-item active text-primary fw-semibold">Tentang Aplikasi</li>
            </ol>
        </nav>
        <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-info-circle text-primary me-2"></i> Identitas Perangkat Lunak &amp; Informasi Sistem
        </h4>
        <p class="text-muted mb-0 fs-13">Rincian spesifikasi aplikasi, tim pengembang, dan modul pendukung</p>
    </div>

    {{-- MAIN IDENTITY HERO CARD --}}
    <div class="card border-0 rounded-4 shadow-sm mb-4 overflow-hidden">
        <div class="card-body p-4 p-md-5">
            <div class="d-flex flex-column flex-md-row align-items-center gap-4 text-center text-md-start">
                <div class="flex-shrink-0">
                    <div class="p-4 bg-primary bg-opacity-10 text-primary rounded-4 border border-primary border-opacity-25 shadow-sm">
                        <i class="bi bi-qr-code-scan display-4"></i>
                    </div>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2 justify-content-center justify-content-md-start">
                        <span class="badge bg-info-subtle text-info border border-info-subtle font-monospace px-3 py-2 rounded-pill">
                            RELEASE VERSION {{ $appInfo['version'] ?? 'v1.0.0-PROD' }}
                        </span>
                        <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace px-3 py-2 rounded-pill">
                            PRODUCTION READY
                        </span>
                    </div>
                    <h2 class="fw-bold text-dark mb-2">Sistem Absensi QR Code</h2>
                    <p class="text-muted mb-3 fs-15">
                        Aplikasi Manajemen Kehadiran &amp; Absensi Berbasis QR Code Terintegrasi<br>
                        untuk Sekolah Menengah Kejuruan (SMK)
                    </p>
                    <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-md-start">
                        <span class="badge bg-light text-dark border font-monospace px-3 py-2">
                            <i class="bi bi-code-slash text-primary me-1"></i> Laravel {{ $appInfo['laravel'] ?? app()->version() }}
                        </span>
                        <span class="badge bg-light text-dark border font-monospace px-3 py-2">
                            <i class="bi bi-cpu text-info me-1"></i> PHP {{ $appInfo['php'] ?? PHP_VERSION }}
                        </span>
                        <span class="badge bg-light text-dark border font-monospace px-3 py-2">
                            <i class="bi bi-bootstrap text-primary me-1"></i> Bootstrap {{ $appInfo['bootstrap'] ?? '5.3.7' }}
                        </span>
                        <span class="badge bg-light text-dark border font-monospace px-3 py-2">
                            <i class="bi bi-database text-success me-1"></i> MySQL Engine
                        </span>
                        <span class="badge bg-light text-dark border font-monospace px-3 py-2">
                            <i class="bi bi-calendar-check text-warning me-1"></i> {{ $appInfo['build_date'] ?? '2026' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- DETAILS GRID --}}
    <div class="row g-4 mb-4">
        {{-- MODULE ROLES --}}
        <div class="col-12 col-lg-6">
            <div class="card border-0 rounded-4 shadow-sm h-100">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-3">
                            <i class="bi bi-shield-check fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">Modul Role Sistem Sekolah</div>
                            <div class="text-muted fs-12">Daftar role pengguna yang tersedia</div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        @php
                            $roles = [
                                ['Super Administrator', 'System Owner &amp; Maintenance',       'bi-shield-lock-fill', 'primary'],
                                ['Admin Sekolah',       'Manajemen Data Master',                 'bi-person-badge-fill', 'info'],
                                ['Guru Pengajar',       'Materi &amp; Absensi Kelas',           'bi-person-workspace', 'success'],
                                ['Operator QR',         'Pemindaian QR Kiosk',                   'bi-qr-code-scan',     'warning'],
                                ['Guru Piket',          'Penanganan Keterlambatan',               'bi-shield-check',     'danger'],
                                ['Siswa Sekolah',       'Monitoring QR &amp; Kartu',            'bi-people-fill',      'secondary'],
                            ];
                        @endphp
                        @foreach($roles as $role)
                        <div class="col-6">
                            <div class="p-3 bg-{{ $role[3] }}-subtle border border-{{ $role[3] }}-subtle rounded-3 h-100">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="bi {{ $role[2] }} text-{{ $role[3] }} fs-6"></i>
                                    <span class="fw-bold text-dark fs-13">{{ $role[0] }}</span>
                                </div>
                                <div class="text-muted fs-11">{!! $role[1] !!}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- DEVELOPER & CREDITS --}}
        <div class="col-12 col-lg-6">
            <div class="card border-0 rounded-4 shadow-sm h-100">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 bg-info bg-opacity-10 text-info rounded-3">
                            <i class="bi bi-code-square fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">Pengembang &amp; Lisensi Aplikasi</div>
                            <div class="text-muted fs-12">Informasi tim dan hak cipta</div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <tbody>
                            @php
                                $credits = [
                                    ['bi-people-fill text-primary',       'Tim Pengembang',      $appInfo['developer'] ?? 'Development &amp; Agentic Engineering Team'],
                                    ['bi-shield-lock-fill text-danger',   'Status Lisensi',      'PROPRIETARY / AUTHORIZED ONLY'],
                                    ['bi-person-fill-gear text-info',     'Owner Role',          'System Owner / Developer Root'],
                                    ['bi-fingerprint text-success',       'Security Layer',      '2-Stage PIN Authentication (2FA)'],
                                    ['bi-calendar-check text-warning',    'Build Date',          $appInfo['build_date'] ?? '2026'],
                                    ['bi-hash text-secondary',            'Build Number',        $appInfo['build'] ?? 'Build-2026'],
                                    ['bi-c-circle-fill text-muted',       'Hak Cipta',           $appInfo['copyright'] ?? '© 2026 System Owner. All Rights Reserved.'],
                                ];
                            @endphp
                            @foreach($credits as $credit)
                            <tr>
                                <td class="ps-4 py-3 text-muted small" style="width:40%;">
                                    <i class="bi {{ $credit[0] }} me-1"></i> {{ $credit[1] }}
                                </td>
                                <td class="pe-4 py-3 fw-semibold text-dark text-end font-monospace fs-12">
                                    {!! $credit[2] !!}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
