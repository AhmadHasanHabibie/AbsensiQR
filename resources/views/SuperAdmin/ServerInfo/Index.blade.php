@extends('Layouts.LayoutSuperAdmin')

@section('title', 'Informasi Server')

@section('content')
<div class="container-fluid px-0">

    {{-- PAGE HEADER --}}
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1" style="font-size:12px;">
                    <li class="breadcrumb-item text-muted">Super Admin</li>
                    <li class="breadcrumb-item active text-primary fw-semibold">Informasi Server</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-cpu text-primary me-2"></i> Informasi Server &amp; Spesifikasi
            </h4>
            <p class="text-muted mb-0 fs-13">Metadata teknis server, PHP engine, versi Laravel, dan distribusi pengguna sistem</p>
        </div>
        <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-2 rounded-pill fw-semibold fs-13">
            <i class="bi bi-clock me-1"></i> {{ now()->translatedFormat('d M Y, H:i') }} WIB
        </span>
    </div>

    {{-- ROW 1: Server Info + Storage Info --}}
    <div class="row g-4 mb-4">

        {{-- SERVER METADATA CARD --}}
        <div class="col-12 col-lg-7">
            <div class="card border-0 rounded-4 shadow-sm h-100">
                <div class="card-header bg-white py-3 px-4 border-bottom rounded-top-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-3">
                            <i class="bi bi-hdd-network-fill fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">Spesifikasi Server Engine</div>
                            <div class="text-muted fs-12">Rincian teknis perangkat lunak server</div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <tbody>
                                @php
                                    $serverItems = [
                                        ['icon' => 'bi-code-slash text-primary',      'label' => 'Framework Laravel',       'value' => $serverInfo['laravel_version'] ?? 'Tidak tersedia'],
                                        ['icon' => 'bi-cpu text-info',                'label' => 'PHP Engine Version',      'value' => $serverInfo['php_version'] ?? 'Tidak tersedia'],
                                        ['icon' => 'bi-bootstrap text-purple',        'label' => 'Bootstrap UI Version',    'value' => $serverInfo['bootstrap_version'] ?? '5.3.7'],
                                        ['icon' => 'bi-clock-history text-warning',   'label' => 'Zona Waktu Server',       'value' => $serverInfo['timezone'] ?? 'Tidak tersedia'],
                                        ['icon' => 'bi-layers text-success',          'label' => 'Environment Mode',        'value' => $serverInfo['environment'] ?? 'Tidak tersedia'],
                                        ['icon' => 'bi-calendar-event text-info',     'label' => 'Waktu Server Saat Ini',   'value' => $serverInfo['server_time'] ?? now()->translatedFormat('l, d F Y - H:i:s T')],
                                        ['icon' => 'bi-tag-fill text-primary',        'label' => 'Versi Aplikasi',          'value' => $serverInfo['app_version'] ?? 'v1.0.0-PROD'],
                                        ['icon' => 'bi-hash text-secondary',          'label' => 'Build Number',            'value' => $serverInfo['build_number'] ?? 'Build-2026'],
                                    ];
                                @endphp
                                @foreach($serverItems as $item)
                                <tr>
                                    <td class="ps-4 py-3" style="width: 44%;">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi {{ $item['icon'] ?? 'bi-dot text-muted' }} fs-6 flex-shrink-0"></i>
                                            <span class="text-muted small fw-semibold">{{ $item['label'] }}</span>
                                        </div>
                                    </td>
                                    <td class="pe-4 py-3">
                                        <span class="fw-bold text-dark font-monospace fs-13">{{ $item['value'] }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- STORAGE + USER DISTRIBUTION --}}
        <div class="col-12 col-lg-5">
            <div class="d-flex flex-column gap-4 h-100">

                {{-- STORAGE INFO CARD --}}
                <div class="card border-0 rounded-4 shadow-sm">
                    <div class="card-header bg-white py-3 px-4 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <div class="p-2 bg-success bg-opacity-10 text-success rounded-3">
                                <i class="bi bi-hdd-fill fs-5"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark">Kapasitas Storage Server</div>
                                <div class="text-muted fs-12">Utilisasi penyimpanan lokal</div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        @php
                            $free  = $storageInfo['free']  ?? 'Tidak tersedia';
                            $total = $storageInfo['total'] ?? 'Tidak tersedia';
                            $used  = $storageInfo['used']  ?? 'Tidak tersedia';

                            // Hitung persentase untuk progress bar
                            $freeBytes  = @disk_free_space(base_path());
                            $totalBytes = @disk_total_space(base_path());
                            $usedPct    = ($totalBytes && $freeBytes) ? round((($totalBytes - $freeBytes) / $totalBytes) * 100, 1) : 0;
                            $barColor   = $usedPct > 90 ? 'danger' : ($usedPct > 75 ? 'warning' : 'success');
                        @endphp
                        <div class="row g-3 mb-3">
                            <div class="col-4 text-center">
                                <div class="p-3 bg-success bg-opacity-10 rounded-3 mb-1">
                                    <div class="fw-bold text-success fs-14 font-monospace">{{ $free }}</div>
                                </div>
                                <div class="text-muted fs-11 fw-semibold text-uppercase">Tersedia</div>
                            </div>
                            <div class="col-4 text-center">
                                <div class="p-3 bg-danger bg-opacity-10 rounded-3 mb-1">
                                    <div class="fw-bold text-danger fs-14 font-monospace">{{ $used }}</div>
                                </div>
                                <div class="text-muted fs-11 fw-semibold text-uppercase">Terpakai</div>
                            </div>
                            <div class="col-4 text-center">
                                <div class="p-3 bg-primary bg-opacity-10 rounded-3 mb-1">
                                    <div class="fw-bold text-primary fs-14 font-monospace">{{ $total }}</div>
                                </div>
                                <div class="text-muted fs-11 fw-semibold text-uppercase">Total</div>
                            </div>
                        </div>
                        @if($usedPct > 0)
                        <div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted fs-12">Utilisasi Disk</span>
                                <span class="fw-bold fs-12 text-{{ $barColor }}">{{ $usedPct }}%</span>
                            </div>
                            <div class="progress rounded-pill" style="height:8px;">
                                <div class="progress-bar bg-{{ $barColor }} rounded-pill"
                                     role="progressbar"
                                     style="width: {{ $usedPct }}%"
                                     aria-valuenow="{{ $usedPct }}"
                                     aria-valuemin="0"
                                     aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- USER DISTRIBUTION CARD --}}
                <div class="card border-0 rounded-4 shadow-sm flex-grow-1">
                    <div class="card-header bg-white py-3 px-4 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <div class="p-2 bg-info bg-opacity-10 text-info rounded-3">
                                <i class="bi bi-people-fill fs-5"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark">Distribusi Pengguna Sistem</div>
                                <div class="text-muted fs-12">Ringkasan per role aktif</div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @php
                            $roleLabels = [
                                'total_super_admin' => ['Super Administrator', 'bi-shield-lock-fill text-primary', 'primary'],
                                'total_admin'       => ['Admin Sekolah',       'bi-person-badge-fill text-info',   'info'],
                                'total_guru'        => ['Guru Pengajar',        'bi-person-workspace text-success', 'success'],
                                'total_operator'    => ['Operator QR',          'bi-qr-code-scan text-warning',    'warning'],
                                'total_piket'       => ['Guru Piket',           'bi-shield-check text-danger',     'danger'],
                                'total_siswa'       => ['Siswa Terdaftar',      'bi-people-fill text-secondary',   'secondary'],
                            ];
                        @endphp
                        <ul class="list-group list-group-flush border-0">
                            @forelse($userCounts as $key => $count)
                            @php
                                $roleData = $roleLabels[$key] ?? [$key, 'bi-person text-muted', 'secondary'];
                            @endphp
                            <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-bottom py-3 px-4">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi {{ $roleData[1] ?? 'bi-person text-muted' }} fs-6"></i>
                                    <span class="fw-semibold text-dark fs-13">{{ $roleData[0] ?? $key }}</span>
                                </div>
                                <span class="badge bg-{{ $roleData[2] ?? 'secondary' }}-subtle text-{{ $roleData[2] ?? 'secondary' }} border border-{{ $roleData[2] ?? 'secondary' }}-subtle font-monospace px-3 py-1 fs-12">
                                    {{ number_format($count) }}
                                </span>
                            </li>
                            @empty
                            <li class="list-group-item bg-transparent py-4 text-center">
                                <div class="sa-empty-icon mx-auto mb-2" style="width:48px;height:48px;font-size:22px;">
                                    <i class="bi bi-people text-muted"></i>
                                </div>
                                <div class="text-muted fs-13">Data pengguna tidak tersedia</div>
                            </li>
                            @endforelse
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection
