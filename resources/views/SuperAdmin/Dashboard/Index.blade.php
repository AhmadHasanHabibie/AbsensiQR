@extends('Layouts.LayoutSuperAdmin')

@section('title', 'Dashboard Super Administrator')

@section('content')
<div class="container-fluid px-0">

    {{-- SECTION 1: HERO BANNER --}}
    <div class="card border-0 rounded-4 overflow-hidden shadow-sm mb-4"
         style="background: linear-gradient(135deg, #0284c7 0%, #0c4a6e 100%);">
        <div class="card-body p-4 p-md-5 text-white">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-none d-sm-flex flex-shrink-0"
                         style="width:64px;height:64px;background:rgba(255,255,255,0.15);border-radius:18px;align-items:center;justify-content:center;backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,0.2);">
                        <i class="bi bi-shield-lock-fill text-white" style="font-size:30px;"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-white text-primary font-monospace fw-bold px-3 py-1 rounded-pill fs-12">System Owner Center</span>
                            <span class="badge bg-white bg-opacity-20 text-white font-monospace px-2 py-1 rounded-pill fs-12">
                                {{ $versionInfo['version'] ?? 'v1.0.0' }}
                            </span>
                        </div>
                        <h3 class="fw-bold mb-1 text-white">Dashboard Super Administrator</h3>
                        <p class="mb-0 opacity-75 fs-14">Pusat Monitoring, Audit, Backup, Maintenance, dan Pengelolaan Sistem.</p>
                    </div>
                </div>
                <div class="text-md-end flex-shrink-0">
                    <div class="opacity-75 fs-12 font-monospace"><i class="bi bi-clock me-1"></i> Waktu Sistem</div>
                    <div class="fw-bold font-monospace fs-14 text-white">
                        {{ now()->translatedFormat('l, d F Y') }}
                    </div>
                    <div class="fw-bold font-monospace fs-15 text-white">
                        {{ now()->translatedFormat('H:i') }} WIB
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION 2: QUICK ACTIONS (6 CARDS) --}}
    <div class="mb-4">
        <div class="d-flex align-items-center gap-2 mb-3">
            <i class="bi bi-lightning-charge-fill text-warning fs-5"></i>
            <h6 class="fw-bold text-dark mb-0">Pintasan Cepat (Quick Action)</h6>
        </div>
        <div class="row g-3">
            @php
                $quickLinks = [
                    [route('superadmin.monitoring.index'),   'bi-activity',       'info',      'Monitoring',   'Diagnostic App'],
                    [route('superadmin.backup.index'),       'bi-database-down',  'primary',   'Backup DB',    'Dump SQL'],
                    [route('superadmin.activity-log.index'), 'bi-journal-text',   'warning',   'Audit Log',    'Timeline Aktivitas'],
                    [route('superadmin.config.index'),       'bi-sliders',        'success',   'Konfigurasi',  'Env Readonly'],
                    [route('superadmin.maintenance.index'),  'bi-tools',          'danger',    'Maintenance',  'Kunci Aplikasi'],
                    [route('superadmin.about.index'),        'bi-info-circle',    'secondary', 'Tentang',      'App Metadata'],
                ];
            @endphp
            @foreach($quickLinks as $ql)
            <div class="col-6 col-md-4 col-xl-2">
                <a href="{{ $ql[0] }}" class="text-decoration-none">
                    <div class="card border-0 rounded-4 shadow-sm text-center p-3 h-100 border-hover transition-all">
                        <div class="p-3 bg-{{ $ql[2] }}-subtle text-{{ $ql[2] }} rounded-3 mb-2 d-inline-flex mx-auto align-items-center justify-content-center" style="width:52px;height:52px;">
                            <i class="bi {{ $ql[1] }} fs-3"></i>
                        </div>
                        <div class="fw-bold text-dark fs-14 mb-1">{{ $ql[3] }}</div>
                        <div class="text-muted fs-12">{{ $ql[4] }}</div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>

    {{-- SECTION 3: SYSTEM HEALTH (8 BADGES) --}}
    <div class="mb-4">
        <div class="d-flex align-items-center gap-2 mb-3">
            <i class="bi bi-heart-pulse-fill text-danger fs-5"></i>
            <h6 class="fw-bold text-dark mb-0">System Health Status</h6>
            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle font-monospace fs-11 px-2 py-1">
                {{ count($systemHealth) }} Diagnostics
            </span>
        </div>
        <div class="row g-3">
            @foreach($systemHealth as $health)
            @php
                $badgeColor = $health['badge'] ?? $health['color'] ?? 'info';
                $compName   = $health['component'] ?? 'Component';
                $compStatus = $health['status'] ?? $health['label'] ?? 'Normal';
                $compDesc   = $health['desc'] ?? 'System Component';
            @endphp
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 rounded-4 shadow-sm p-3 h-100"
                     style="border-left: 3px solid var(--bs-{{ $badgeColor }}) !important;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-bold text-dark font-monospace fs-13">{{ $compName }}</span>
                        <span class="badge bg-{{ $badgeColor }}-subtle text-{{ $badgeColor }} border border-{{ $badgeColor }}-subtle font-monospace px-2 py-1 fs-11">
                            {{ $compStatus }}
                        </span>
                    </div>
                    <div class="text-muted fs-12">
                        <i class="bi bi-info-circle me-1"></i>{{ $compDesc }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- SECTION 4: SYSTEM STATISTICS (9 METRICS) --}}
    <div class="mb-4">
        <div class="d-flex align-items-center gap-2 mb-3">
            <i class="bi bi-bar-chart-line-fill text-primary fs-5"></i>
            <h6 class="fw-bold text-dark mb-0">Statistik Pengguna &amp; Sistem</h6>
        </div>
        <div class="row g-3">
            @php
                $statCards = [
                    ['Admin Sekolah',    $stats['admin_count']         ?? 0, 'Manajer Sekolah',     'bi-person-badge-fill',   'primary'],
                    ['Total Guru',       $stats['guru_count']          ?? 0, 'Tenaga Pengajar',     'bi-person-workspace',    'info'],
                    ['Total Operator',   $stats['operator_count']      ?? 0, 'Operator QR',         'bi-qr-code-scan',        'success'],
                    ['Guru Piket',       $stats['piket_count']         ?? 0, 'Petugas Piket',       'bi-shield-check',        'warning'],
                    ['Total Siswa',      $stats['siswa_count']         ?? 0, 'Siswa Terdaftar',     'bi-people-fill',         'secondary'],
                    ['Total Kelas',      $stats['kelas_count']         ?? 0, 'Rombongan Belajar',   'bi-building',            'primary'],
                    ['Absensi Hari Ini', $stats['absensi_today_count'] ?? 0, 'Record QR Absen',     'bi-check2-circle',       'success'],
                    ['Login Hari Ini',   $stats['login_today_count']   ?? 0, 'Sesi Pengguna',       'bi-box-arrow-in-right',  'info'],
                    ['Audit Hari Ini',   $stats['audit_today_count']   ?? 0, 'Activity Recorded',   'bi-card-checklist',      'warning'],
                ];
            @endphp
            @foreach($statCards as $sc)
            <div class="col-6 col-md-4 col-lg-3 col-xl-auto" style="min-width:0;flex:1 1 160px;">
                <div class="card border-0 rounded-4 shadow-sm p-3 h-100">
                    <div class="d-flex align-items-start justify-content-between gap-2">
                        <div class="min-w-0">
                            <div class="text-muted fs-12 fw-semibold mb-1">{{ $sc[0] }}</div>
                            <div class="fw-bold text-{{ $sc[4] }} fs-3 font-monospace leading-tight">{{ number_format($sc[1]) }}</div>
                            <div class="text-muted fs-11 mt-1">{{ $sc[2] }}</div>
                        </div>
                        <div class="p-2 bg-{{ $sc[4] }}-subtle text-{{ $sc[4] }} rounded-3 flex-shrink-0">
                            <i class="bi {{ $sc[3] }} fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

            {{-- Stat: Last Backup --}}
            <div class="col-12 col-md-6 col-lg-4 col-xl-auto" style="min-width:0;flex:1 1 180px;">
                <div class="card border-0 rounded-4 shadow-sm p-3 h-100"
                     style="border-left: 3px solid #dc2626 !important;">
                    <div class="d-flex align-items-start gap-3">
                        <div class="p-2 bg-danger-subtle text-danger rounded-3 flex-shrink-0">
                            <i class="bi bi-database-fill-down fs-4"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="text-muted fs-12 fw-semibold mb-1">Backup Terakhir</div>
                            <div class="fw-bold text-dark fs-13 font-monospace">{{ $lastBackup['time'] ?? 'Belum ada' }}</div>
                            <div class="text-muted fs-11">
                                @if(isset($lastBackup['size']) && !in_array($lastBackup['size'], ['—', '-', 'Tidak tersedia', 'File tidak tersedia']))
                                    <span class="fw-semibold text-success font-monospace">{{ $lastBackup['size'] }}</span>
                                @elseif(isset($lastBackup['time']) && $lastBackup['time'] !== 'Belum ada')
                                    <span class="text-muted">Ukuran tidak tersedia</span>
                                @else
                                    <span class="text-muted">Belum ada backup</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION 5: VERSION INFO + RECENT ACTIVITIES --}}
    <div class="row g-4">
        {{-- Version Info Card --}}
        <div class="col-12 col-lg-5">
            <div class="card border-0 rounded-4 shadow-sm h-100">
                <div class="card-header bg-white py-3 px-4 border-bottom rounded-top-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 bg-info bg-opacity-10 text-info rounded-3">
                            <i class="bi bi-cpu-fill fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">Version &amp; Platform Information</div>
                            <div class="text-muted fs-12">Spesifikasi teknis aplikasi aktif</div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <tbody>
                            @php
                                $versionRows = [
                                    ['Nama Aplikasi',          $versionInfo['app_name']    ?? 'Sistem Absensi QR Code', 'fw-bold text-dark'],
                                    ['Release Version',        $versionInfo['version']     ?? 'v1.0.0',                'badge-info'],
                                    ['Build / Tanggal',        ($versionInfo['build']      ?? 'Build-2026') . ' (' . ($versionInfo['build_date'] ?? '2026') . ')', 'fw-semibold text-dark fs-12'],
                                    ['Framework Engine',       $versionInfo['laravel']     ?? 'Laravel 10',            'fw-bold text-primary'],
                                    ['PHP Engine',             $versionInfo['php']         ?? PHP_VERSION,             'fw-bold text-info'],
                                    ['UI Component',           $versionInfo['bootstrap']   ?? 'Bootstrap 5',           'fw-semibold text-dark'],
                                    ['Database Driver',        $versionInfo['database']    ?? 'mysql',                 'fw-bold text-success'],
                                    ['Environment / Timezone', ($versionInfo['environment'] ?? 'production') . ' / ' . ($versionInfo['timezone'] ?? 'Asia/Jakarta'), 'fw-semibold text-dark fs-12'],
                                    ['Domain / Host',          $versionInfo['domain']      ?? 'localhost',             'fw-bold text-dark fs-12'],
                                    ['Developer / Owner',      $versionInfo['developer']   ?? 'System Owner',          'fw-bold text-primary fs-12'],
                                ];
                            @endphp
                            @foreach($versionRows as $row)
                            <tr>
                                <td class="ps-4 py-3 text-muted small" style="width:40%;white-space:nowrap;">{{ $row[0] }}</td>
                                <td class="pe-4 py-3 text-end font-monospace {{ !str_contains($row[2], 'badge') ? $row[2] : '' }}">
                                    @if(str_contains($row[2], 'badge'))
                                        <span class="badge bg-info-subtle text-info border border-info-subtle font-monospace">{{ $row[1] }}</span>
                                    @else
                                        {{ $row[1] }}
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Recent Activities Card --}}
        <div class="col-12 col-lg-7">
            <div class="card border-0 rounded-4 shadow-sm h-100">
                <div class="card-header bg-white py-3 px-4 border-bottom rounded-top-4 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-3">
                            <i class="bi bi-clock-history fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">Aktivitas Sistem Terakhir</div>
                            <div class="text-muted fs-12">5 log aktivitas terbaru</div>
                        </div>
                    </div>
                    <a href="{{ route('superadmin.activity-log.index') }}"
                       class="btn btn-outline-primary btn-sm rounded-3 fw-semibold px-3">
                        Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th class="ps-4 py-3">Waktu</th>
                                    <th class="py-3">Pengguna</th>
                                    <th class="py-3">Aktivitas</th>
                                    <th class="py-3">Modul</th>
                                    <th class="pe-4 py-3">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentActivities as $log)
                                <tr>
                                    <td class="ps-4 py-3" style="white-space: nowrap;">
                                        <div class="fw-bold text-primary font-monospace fs-13">
                                            {{ $log->created_at->translatedFormat('H:i:s') }}
                                        </div>
                                        <div class="text-muted fs-11 font-monospace">
                                            {{ $log->created_at->translatedFormat('d M Y') }}
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <div class="fw-semibold text-dark fs-13">
                                            {{ $log->user ? $log->user->name : 'System' }}
                                        </div>
                                        <div class="text-muted fs-11">{{ $log->role ?? 'super_admin' }}</div>
                                    </td>
                                    <td class="py-3">
                                        <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 font-monospace fs-11">
                                            {{ $log->activity }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <span class="text-secondary small font-monospace">{{ $log->module ?? '—' }}</span>
                                    </td>
                                    <td class="pe-4 py-3 text-muted small">
                                        {{ \Illuminate\Support\Str::limit($log->description ?? '', 45) }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="p-0">
                                        <div class="sa-empty-state py-5">
                                            <div class="sa-empty-icon">
                                                <i class="bi bi-journal-x"></i>
                                            </div>
                                            <div class="sa-empty-title">Belum Ada Aktivitas</div>
                                            <div class="sa-empty-desc">Belum ada aktivitas sistem yang tercatat.</div>
                                        </div>
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
