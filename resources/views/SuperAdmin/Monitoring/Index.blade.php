@extends('Layouts.LayoutSuperAdmin')

@section('title', 'Monitoring Sistem')

@section('content')
<div class="container-fluid px-0">

    {{-- PAGE HEADER --}}
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1" style="font-size:12px;">
                    <li class="breadcrumb-item text-muted">Super Admin</li>
                    <li class="breadcrumb-item active text-primary fw-semibold">Monitoring Sistem</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-activity text-primary me-2"></i> Monitoring Diagnostics &amp; System Metrics
            </h4>
            <p class="text-muted mb-0 fs-13">Inspeksi kesehatan aplikasi, koneksi infrastruktur, dan utilisasi storage secara real-time</p>
        </div>
        <button id="btnRefresh" onclick="refreshMonitoring()"
                class="btn btn-outline-primary rounded-3 px-4 fw-semibold d-flex align-items-center gap-2 flex-shrink-0">
            <i class="bi bi-arrow-clockwise" id="refreshIcon"></i>
            <span id="refreshText">Refresh Data</span>
        </button>
    </div>

    {{-- METRICS GRID --}}
    <div class="row g-3 mb-4">
        @php
            $iconMap = [
                'status_server'   => 'bi-server',
                'status_database' => 'bi-database',
                'status_storage'  => 'bi-hdd-fill',
                'status_cache'    => 'bi-lightning-fill',
                'status_session'  => 'bi-key-fill',
                'status_queue'    => 'bi-collection',
                'status_mail'     => 'bi-envelope-fill',
                'status_app_env'  => 'bi-layers',
                'status_app_debug'=> 'bi-bug-fill',
            ];
        @endphp
        @foreach(($metrics ?? $monitors ?? []) as $key => $metric)
        @php
            $metricBadge  = $metric['badge']  ?? $metric['color'] ?? 'info';
            $metricStatus = $metric['status'] ?? $metric['label'] ?? 'Normal';
            $metricDesc   = $metric['desc']   ?? $metric['details'] ?? '—';
            $metricTitle  = is_string($key) ? ucwords(str_replace('_', ' ', $key)) : ($metric['name'] ?? 'Metric');
            $metricIcon   = $iconMap[$key] ?? 'bi-activity';
        @endphp
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card border-0 rounded-4 shadow-sm h-100 p-3">
                <div class="d-flex align-items-start gap-3">
                    <div class="p-2 bg-{{ $metricBadge }}-subtle text-{{ $metricBadge }} rounded-3 flex-shrink-0">
                        <i class="bi {{ $metricIcon }} fs-5"></i>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                            <span class="text-muted small fw-semibold" style="font-size:11px;text-transform:uppercase;letter-spacing:0.5px;">
                                {{ $metricTitle }}
                            </span>
                            <span class="badge bg-{{ $metricBadge }}-subtle text-{{ $metricBadge }} border border-{{ $metricBadge }}-subtle font-monospace px-2 py-1 fs-11 flex-shrink-0">
                                {{ $metricStatus }}
                            </span>
                        </div>
                        <div class="text-muted small mt-1 fs-12" style="line-height:1.4;">
                            <i class="bi bi-info-circle me-1 opacity-75"></i>
                            {{ $metricDesc }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- DIAGNOSTIC DETAILS TABLE --}}
    <div class="card border-0 rounded-4 shadow-sm">
        <div class="card-header bg-white py-3 px-4 border-bottom rounded-top-4">
            <div class="d-flex align-items-center gap-2">
                <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-3">
                    <i class="bi bi-cpu-fill fs-5"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark">Detail Status Komponen Utama</div>
                    <div class="text-muted fs-12">Diagnostik teknis per komponen aplikasi</div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3">Komponen</th>
                            <th class="py-3">Parameter</th>
                            <th class="py-3">Status Operasional</th>
                            <th class="pe-4 py-3">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($monitors ?? []) as $monitor)
                        <tr>
                            <td class="ps-4 py-3">
                                <span class="fw-bold text-dark fs-13">{{ $monitor['name'] ?? $monitor['component'] ?? '—' }}</span>
                            </td>
                            <td class="py-3">
                                <span class="text-muted small font-monospace">{{ $monitor['details'] ?? $monitor['desc'] ?? '—' }}</span>
                            </td>
                            <td class="py-3">
                                @php $color = $monitor['color'] ?? $monitor['badge'] ?? 'success'; @endphp
                                <span class="badge bg-{{ $color }}-subtle text-{{ $color }} border border-{{ $color }}-subtle px-3 py-1 font-monospace fs-11">
                                    {{ strtoupper($monitor['label'] ?? $monitor['status'] ?? 'Normal') }}
                                </span>
                            </td>
                            <td class="pe-4 py-3 text-muted small">{{ $monitor['details'] ?? $monitor['desc'] ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="p-0">
                                <div class="sa-empty-state">
                                    <div class="sa-empty-icon">
                                        <i class="bi bi-activity"></i>
                                    </div>
                                    <div class="sa-empty-title">Tidak Ada Data Monitoring</div>
                                    <div class="sa-empty-desc">Data diagnostik sistem tidak tersedia saat ini.</div>
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
@endsection

@push('scripts')
<script>
    function refreshMonitoring() {
        const btn        = document.getElementById('btnRefresh');
        const icon       = document.getElementById('refreshIcon');
        const btnText    = document.getElementById('refreshText');

        // Show loading state
        icon.style.animation      = 'sa-spin 0.6s linear infinite';
        icon.style.display        = 'inline-block';
        btnText.textContent       = 'Memuat...';
        btn.disabled              = true;
        btn.classList.remove('btn-outline-primary');
        btn.classList.add('btn-outline-secondary');

        saLoadingShow('Menyegarkan data monitoring...');

        setTimeout(function() {
            window.location.reload();
        }, 600);
    }
</script>
@endpush
