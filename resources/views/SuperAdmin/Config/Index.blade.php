@extends('Layouts.LayoutSuperAdmin')

@section('title', 'Konfigurasi Sistem')

@section('content')
<div class="container-fluid px-0">

    {{-- PAGE HEADER --}}
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1" style="font-size:12px;">
                <li class="breadcrumb-item text-muted">Super Admin</li>
                <li class="breadcrumb-item active text-primary fw-semibold">Konfigurasi Sistem</li>
            </ol>
        </nav>
        <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-sliders text-primary me-2"></i> Konfigurasi Environment Sistem
        </h4>
        <p class="text-muted mb-0 fs-13">Inspeksi nilai konfigurasi environment (.env) — Status:
            <span class="badge bg-warning-subtle text-warning border border-warning-subtle font-monospace ms-1">READONLY</span>
        </p>
    </div>

    {{-- READONLY NOTICE --}}
    <div class="card border-0 rounded-4 shadow-sm mb-4" style="border-left: 4px solid #2563eb !important;">
        <div class="card-body py-3 px-4">
            <div class="d-flex align-items-center gap-3">
                <div class="p-2 bg-info bg-opacity-10 text-info rounded-3 flex-shrink-0">
                    <i class="bi bi-shield-lock-fill fs-4"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark fs-14">Mode Readonly — Audit &amp; Pemeliharaan</div>
                    <div class="text-muted small mt-1">
                        Parameter konfigurasi disajikan dalam mode <strong>Readonly</strong> untuk keperluan audit teknis System Owner.
                        Pengubahan langsung melalui dashboard dinonaktifkan demi keamanan sistem.
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CONFIG GRID --}}
    <div class="card border-0 rounded-4 shadow-sm">
        <div class="card-header bg-white py-3 px-4 border-bottom rounded-top-4">
            <div class="d-flex align-items-center gap-2">
                <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-3">
                    <i class="bi bi-file-earmark-code fs-5"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark">Parameter Key Environment (.env)</div>
                    <div class="text-muted fs-12">Konfigurasi aktif yang digunakan aplikasi</div>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            @if(!empty($configs))
            <div class="row g-3">
                @php
                    $configIcons = [
                        'APP_NAME'   => ['bi-tag-fill',        'primary'],
                        'APP_ENV'    => ['bi-layers',           'info'],
                        'APP_URL'    => ['bi-link-45deg',       'success'],
                        'TIMEZONE'   => ['bi-clock-fill',       'warning'],
                        'MAIL'       => ['bi-envelope-fill',    'danger'],
                        'CACHE'      => ['bi-lightning-fill',   'warning'],
                        'SESSION'    => ['bi-key-fill',         'info'],
                        'QUEUE'      => ['bi-collection-fill',  'success'],
                        'Storage'    => ['bi-hdd-fill',         'secondary'],
                    ];
                @endphp
                @foreach($configs as $key => $val)
                @php
                    $iconData = $configIcons[$key] ?? ['bi-gear-fill', 'primary'];
                @endphp
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="p-3 bg-light rounded-3 border h-100">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi {{ $iconData[0] }} text-{{ $iconData[1] }} fs-6"></i>
                            <label class="form-label text-muted small font-monospace fw-semibold mb-0 text-uppercase fs-11">
                                {{ $key }}
                            </label>
                        </div>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border text-primary border-end-0">
                                <i class="bi bi-lock-fill fs-12"></i>
                            </span>
                            <input type="text"
                                   class="form-control bg-white text-dark font-monospace fw-semibold border border-start-0 rounded-end-2"
                                   value="{{ $val ?? 'Tidak tersedia' }}"
                                   readonly>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="sa-empty-state">
                <div class="sa-empty-icon">
                    <i class="bi bi-sliders"></i>
                </div>
                <div class="sa-empty-title">Tidak Ada Data Konfigurasi</div>
                <div class="sa-empty-desc">Data konfigurasi sistem tidak dapat dimuat.</div>
            </div>
            @endif
        </div>
    </div>

</div>
@endsection
