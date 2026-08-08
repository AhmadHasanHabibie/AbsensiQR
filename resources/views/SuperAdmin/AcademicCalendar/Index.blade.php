@extends($layout ?? (Auth::user()?->role === 'admin' ? 'Layouts.LayoutAdmin' : (Auth::user()?->role === 'teacher' ? 'Layouts.LayoutGuru' : (in_array(Auth::user()?->role, ['piket', 'guru_piket']) ? 'Layouts.LayoutGuruPiket' : 'Layouts.LayoutSuperAdmin'))))

@section('title', 'Kalender Akademik')

@push('styles')
<style>
    /* ====================== STATS CARDS ====================== */
    .cal-stat-card {
        background: #fff;
        border-radius: 16px;
        padding: 20px 22px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 8px rgba(0,0,0,0.05);
        transition: all 0.25s ease;
        height: 100%;
    }
    .cal-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.10);
    }
    .cal-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }
    .cal-stat-number {
        font-size: 28px;
        font-weight: 800;
        line-height: 1.1;
        color: #0f172a;
    }
    .cal-stat-label {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.8px;
    }

    /* ====================== FILTER BAR ====================== */
    .cal-filter-bar {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        padding: 16px 20px;
        box-shadow: 0 1px 6px rgba(0,0,0,0.04);
    }

    /* ====================== TABLE ====================== */
    .cal-table-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 8px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .cal-table thead th {
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #64748b;
        padding: 12px 14px;
        white-space: nowrap;
    }
    .cal-table tbody td {
        font-size: 13px;
        padding: 11px 14px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
    }
    .cal-table tbody tr:last-child td {
        border-bottom: none;
    }
    .cal-table tbody tr:hover td {
        background: #f8fafc;
    }

    /* ====================== YEAR SUMMARY CARDS ====================== */
    .year-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px 16px;
        transition: all 0.2s ease;
    }
    .year-card.active-year {
        background: linear-gradient(135deg, rgba(2,132,199,0.08) 0%, rgba(3,105,161,0.05) 100%);
        border-color: #0284c7;
    }
    .year-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); }

    /* ====================== BADGE OVERRIDES ====================== */
    .badge-school-day    { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .badge-national-hol  { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    .badge-school-hol    { background: #fef9c3; color: #854d0e; border: 1px solid #fef08a; }
    .badge-weekend       { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
    .badge-substitute    { background: #e0f2fe; color: #075985; border: 1px solid #bae6fd; }
    .badge-mpls          { background: #ede9fe; color: #5b21b6; border: 1px solid #ddd6fe; }
    .badge-exam          { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .badge-report-card   { background: #fce7f3; color: #9d174d; border: 1px solid #fbcfe8; }
    .badge-other         { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }

    /* ====================== MODAL IMPORT ====================== */
    .import-dropzone {
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        padding: 32px 20px;
        text-align: center;
        transition: all 0.2s ease;
        cursor: pointer;
        background: #f8fafc;
    }
    .import-dropzone:hover, .import-dropzone.drag-over {
        border-color: #0284c7;
        background: rgba(2,132,199,0.05);
    }
    .import-dropzone input[type="file"] { display: none; }

    /* ====================== DETAIL MODAL ====================== */
    .detail-field-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #94a3b8;
        margin-bottom: 3px;
    }
    .detail-field-value {
        font-size: 14px;
        font-weight: 600;
        color: #1e293b;
    }

    /* ====================== BOOL ICON ====================== */
    .bool-yes { color: #16a34a; font-size: 18px; }
    .bool-no  { color: #dc2626; font-size: 18px; }

    /* ====================== SA MODAL SYSTEM OVERLAY FALLBACK ====================== */
    .sa-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.6);
        z-index: 99999;
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
    .sa-loading-ring {
        display: inline-block;
        border: 3px solid #f3f3f3;
        border-top: 3px solid #2563eb;
        border-radius: 50%;
        animation: sa-spin 0.8s linear infinite;
    }
    @keyframes sa-spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-0">

    {{-- ============================================================ --}}
    {{-- PAGE HEADER                                                   --}}
    {{-- ============================================================ --}}
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1" style="font-size:12px;">
                <li class="breadcrumb-item text-muted">{{ Auth::user()?->role_label ?? 'User' }}</li>
                <li class="breadcrumb-item active text-primary fw-semibold">Kalender Akademik</li>
            </ol>
        </nav>
        <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
            <div>
                <h4 class="fw-bold text-dark mb-1">
                    <i class="bi bi-calendar2-week-fill text-primary me-2"></i>Kalender Akademik
                </h4>
                <p class="text-muted mb-0 fs-13">
                    @if($isSuperAdmin ?? (Auth::user()?->role === 'super_admin'))
                        Kelola kalender pendidikan sekolah.
                    @else
                        Lihat kalender pendidikan sekolah.
                    @endif
                </p>
            </div>
            @if($isSuperAdmin ?? (Auth::user()?->role === 'super_admin'))
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('superadmin.academic-calendar.template') }}"
                   id="btn-download-template"
                   class="btn btn-sm btn-outline-primary fw-semibold rounded-3 px-3">
                    <i class="bi bi-file-earmark-arrow-down me-1"></i> Download Template
                </a>
                <button type="button"
                        id="btn-open-import"
                        class="btn btn-sm btn-primary fw-semibold rounded-3 px-3"
                        onclick="openImportModal()">
                    <i class="bi bi-file-earmark-excel me-1"></i> Import Kalender
                </button>
            </div>
            @endif
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- FLASH MESSAGES — Import Success                               --}}
    {{-- ============================================================ --}}
    @if(session('import_success'))
    <div id="flash-import-success"
         data-count="{{ session('import_count') }}"
         data-year="{{ session('import_year') }}"
         class="d-none"></div>
    @endif

    @if(session('import_errors'))
    <div id="flash-import-errors" class="d-none" data-errors="{{ json_encode(session('import_errors')) }}"></div>
    @endif

    {{-- ============================================================ --}}
    {{-- STATS CARDS                                                   --}}
    {{-- ============================================================ --}}
    <div class="row g-3 mb-4">

        {{-- Tahun Aktif --}}
        <div class="col-6 col-lg-3">
            <div class="cal-stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="cal-stat-icon" style="background:rgba(2,132,199,0.1);">
                        <i class="bi bi-mortarboard-fill text-primary"></i>
                    </div>
                    <div>
                        <div class="cal-stat-label">Tahun Aktif</div>
                        @if($stats['has_data'] && $stats['academic_year'])
                        <div class="cal-stat-number" style="font-size:18px;">{{ $stats['academic_year'] }}</div>
                        @else
                        <div class="cal-stat-number text-muted" style="font-size:16px;">—</div>
                        <div class="text-muted" style="font-size:11px;">Belum ada data</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Hari Efektif --}}
        <div class="col-6 col-lg-3">
            <div class="cal-stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="cal-stat-icon" style="background:rgba(22,163,74,0.1);">
                        <i class="bi bi-book-fill text-success"></i>
                    </div>
                    <div>
                        <div class="cal-stat-label">Hari Efektif</div>
                        @if($stats['has_data'])
                        <div class="cal-stat-number text-success">{{ number_format($stats['school_days']) }}</div>
                        @else
                        <div class="cal-stat-number text-muted">—</div>
                        <div class="text-muted" style="font-size:11px;">Belum ada data</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Hari Libur --}}
        <div class="col-6 col-lg-3">
            <div class="cal-stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="cal-stat-icon" style="background:rgba(220,38,38,0.1);">
                        <i class="bi bi-calendar-x-fill text-danger"></i>
                    </div>
                    <div>
                        <div class="cal-stat-label">Hari Libur</div>
                        @if($stats['has_data'])
                        <div class="cal-stat-number text-danger">{{ number_format($stats['holidays']) }}</div>
                        @else
                        <div class="cal-stat-number text-muted">—</div>
                        <div class="text-muted" style="font-size:11px;">Belum ada data</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Hari Ini --}}
        <div class="col-6 col-lg-3">
            <div class="cal-stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="cal-stat-icon" style="background:rgba(234,179,8,0.1);">
                        <i class="bi bi-calendar-day-fill text-warning"></i>
                    </div>
                    <div>
                        <div class="cal-stat-label">Hari Ini</div>
                        @if(isset($stats['today_info']))
                        @php $todayInf = $stats['today_info']; @endphp
                        <div class="fw-bold text-dark" style="font-size:13px;">
                            {{ $todayInf['day_name'] }}, {{ \Carbon\Carbon::parse($todayInf['date'])->translatedFormat('d M Y') }}
                        </div>
                        <div class="mt-1">
                            <span class="badge {{ $todayInf['badge_class'] ?? 'bg-secondary' }} fs-11">{{ $todayInf['status'] }}</span>
                        </div>
                        @elseif($stats['today'])
                        <div class="fw-bold text-dark" style="font-size:13px;">{{ $stats['today']->day_name }}</div>
                        <div class="mt-1">
                            @php $todayBadge = $stats['today']->status_badge_class; @endphp
                            <span class="badge {{ $todayBadge }} fs-11">{{ $stats['today']->status }}</span>
                        </div>
                        @else
                        <div class="cal-stat-number text-muted">—</div>
                        <div class="text-muted" style="font-size:11px;">Tidak ada data hari ini</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- YEAR SUMMARY                                                  --}}
    {{-- ============================================================ --}}
    @if(!empty($yearSummary))
    <div class="card border-0 rounded-4 shadow-sm mb-4">
        <div class="card-header bg-white py-3 px-4 border-bottom rounded-top-4">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-3">
                        <i class="bi bi-calendar-range fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark">Daftar Tahun Ajaran</div>
                        <div class="text-muted fs-12">
                            @if($isSuperAdmin ?? (Auth::user()?->role === 'super_admin'))
                                Klik "Jadikan Aktif" untuk mengubah tahun ajaran yang aktif
                            @else
                                Ringkasan statistik per tahun ajaran kalender akademik
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                @foreach($yearSummary as $ys)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="year-card {{ $ys['is_active'] ? 'active-year' : '' }}">
                        <div class="d-flex align-items-start justify-content-between gap-2">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="fw-bold text-dark fs-14">{{ $ys['academic_year'] }}</span>
                                    @if($ys['is_active'])
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-10">
                                        <i class="bi bi-check-circle-fill me-1"></i>AKTIF
                                    </span>
                                    @endif
                                </div>
                                <div class="d-flex gap-3 mt-1">
                                    <div class="text-muted fs-12">
                                        <i class="bi bi-calendar3 me-1"></i>{{ number_format($ys['total_days']) }} hari
                                    </div>
                                    <div class="text-success fs-12">
                                        <i class="bi bi-book me-1"></i>{{ number_format($ys['school_days']) }} efektif
                                    </div>
                                    <div class="text-danger fs-12">
                                        <i class="bi bi-x-circle me-1"></i>{{ number_format($ys['holiday_days']) }} libur
                                    </div>
                                </div>
                            </div>
                            @if($ys['is_active'])
                            <span class="badge bg-success-subtle text-success border border-success-subtle align-self-start">
                                <i class="bi bi-shield-check me-1"></i>Aktif
                            </span>
                            @elseif($isSuperAdmin ?? (Auth::user()?->role === 'super_admin'))
                            <button type="button"
                                    class="btn btn-sm btn-outline-primary rounded-3 flex-shrink-0 fw-semibold"
                                    onclick="confirmActivateYear('{{ $ys['academic_year'] }}')"
                                    style="font-size:11px; white-space:nowrap;">
                                <i class="bi bi-check2-circle me-1"></i>Jadikan Aktif
                            </button>
                            @else
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle align-self-start fs-11">
                                Nonaktif
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- ============================================================ --}}
    {{-- FILTER BAR                                                    --}}
    {{-- ============================================================ --}}
    <div class="cal-filter-bar mb-4">
        <form method="GET" action="{{ request()->url() }}" id="filterForm">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-sm-6 col-lg-3">
                    <label class="form-label text-muted fw-semibold mb-1" style="font-size:11px;">CARI</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted fs-12"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0 rounded-end-2"
                               placeholder="Kegiatan, deskripsi..."
                               value="{{ $search }}">
                    </div>
                </div>
                <div class="col-6 col-sm-3 col-lg-2">
                    <label class="form-label text-muted fw-semibold mb-1" style="font-size:11px;">TAHUN AJARAN</label>
                    <select name="academic_year" class="form-select form-select-sm rounded-2">
                        <option value="">Semua</option>
                        @foreach($yearSummary as $ys)
                        <option value="{{ $ys['academic_year'] }}" {{ $academicYear == $ys['academic_year'] ? 'selected' : '' }}>
                            {{ $ys['academic_year'] }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-sm-3 col-lg-2">
                    <label class="form-label text-muted fw-semibold mb-1" style="font-size:11px;">SEMESTER</label>
                    <select name="semester" class="form-select form-select-sm rounded-2">
                        <option value="">Semua</option>
                        <option value="Ganjil" {{ $semester === 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                        <option value="Genap" {{ $semester === 'Genap' ? 'selected' : '' }}>Genap</option>
                    </select>
                </div>
                <div class="col-6 col-sm-3 col-lg-2">
                    <label class="form-label text-muted fw-semibold mb-1" style="font-size:11px;">STATUS</label>
                    <select name="status" class="form-select form-select-sm rounded-2">
                        <option value="">Semua</option>
                        @foreach(\App\Models\AcademicCalendar::STATUSES as $s)
                        <option value="{{ $s }}" {{ $status === $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-sm-3 col-lg-2">
                    <label class="form-label text-muted fw-semibold mb-1" style="font-size:11px;">KATEGORI</label>
                    <select name="category" class="form-select form-select-sm rounded-2">
                        <option value="">Semua</option>
                        @foreach(\App\Models\AcademicCalendar::CATEGORIES as $c)
                        <option value="{{ $c }}" {{ $category === $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-auto col-lg-1">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary rounded-2 fw-semibold w-100">
                            <i class="bi bi-funnel me-1"></i>Filter
                        </button>
                        <a href="{{ request()->url() }}"
                           class="btn btn-sm btn-outline-secondary rounded-2">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Date Range Row --}}
            <div class="row g-2 mt-1">
                <div class="col-6 col-lg-2">
                    <label class="form-label text-muted fw-semibold mb-1" style="font-size:11px;">DARI TANGGAL</label>
                    <input type="date" name="date_from" class="form-control form-control-sm rounded-2"
                           value="{{ $dateFrom }}">
                </div>
                <div class="col-6 col-lg-2">
                    <label class="form-label text-muted fw-semibold mb-1" style="font-size:11px;">SAMPAI TANGGAL</label>
                    <input type="date" name="date_to" class="form-control form-control-sm rounded-2"
                           value="{{ $dateTo }}">
                </div>
            </div>
        </form>
    </div>

    {{-- ============================================================ --}}
    {{-- DATA TABLE                                                    --}}
    {{-- ============================================================ --}}
    <div class="cal-table-card">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-3">
                    <i class="bi bi-table fs-5"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark">Data Kalender Akademik</div>
                    <div class="text-muted fs-12">
                        Total {{ $calendars->total() }} data
                        @if($academicYear) — Tahun {{ $academicYear }} @endif
                    </div>
                </div>
            </div>
        </div>

        @if($calendars->isEmpty())
        {{-- EMPTY STATE --}}
        <div class="sa-empty-state">
            <div class="sa-empty-icon">
                <i class="bi bi-calendar2-x"></i>
            </div>
            <div class="sa-empty-title">Belum Ada Data Kalender</div>
            <div class="sa-empty-desc">
                @if($search || $semester || $status || $category || $academicYear || $dateFrom || $dateTo)
                    Tidak ada data yang cocok dengan filter yang dipilih.
                    <a href="{{ request()->url() }}" class="d-block mt-2 text-primary fw-semibold">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Reset Filter
                    </a>
                @else
                    @if($isSuperAdmin ?? (Auth::user()?->role === 'super_admin'))
                        Import data kalender menggunakan tombol <strong>Import Kalender</strong> di atas.
                    @else
                        Belum ada data kalender akademik yang di-import oleh Super Administrator.
                    @endif
                @endif
            </div>
        </div>
        @else
        @php
            $isPatchedRole = in_array(Auth::user()?->role, ['admin', 'teacher', 'piket', 'guru_piket']);
        @endphp
        <div class="table-responsive">
            <table class="table cal-table mb-0">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Tanggal</th>
                        <th>Hari</th>
                        <th>Sem.</th>
                        @if(!$isPatchedRole)
                        <th>Status</th>
                        @endif
                        <th>Kategori</th>
                        <th>Kegiatan</th>
                        <th width="60" class="text-center">QR</th>
                        @if(!$isPatchedRole)
                        <th width="90" class="text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($calendars as $i => $cal)
                    <tr>
                        <td class="text-muted fs-12">{{ $calendars->firstItem() + $i }}</td>
                        <td>
                            <div class="fw-semibold text-dark fs-13">{{ $cal->date->format('d M Y') }}</div>
                            <div class="text-muted fs-11">{{ $cal->academic_year }}</div>
                        </td>
                        <td class="fw-semibold fs-13">{{ $cal->day_name }}</td>
                        <td>
                            <span class="badge rounded-pill {{ $cal->semester === 'Ganjil' ? 'bg-info-subtle text-info border border-info-subtle' : 'bg-warning-subtle text-warning border border-warning-subtle' }} fs-11">
                                {{ $cal->semester }}
                            </span>
                        </td>
                        @if(!$isPatchedRole)
                        <td>
                            @php
                                $statusClass = match($cal->status) {
                                    'Hari Belajar'      => 'badge-school-day',
                                    'Libur Nasional'    => 'badge-national-hol',
                                    'Libur Sekolah'     => 'badge-school-hol',
                                    'Libur Akhir Pekan' => 'badge-weekend',
                                    'Hari Pengganti'    => 'badge-substitute',
                                    'MPLS'              => 'badge-mpls',
                                    'Ujian'             => 'badge-exam',
                                    'Pembagian Rapor'   => 'badge-report-card',
                                    default             => 'badge-other',
                                };
                            @endphp
                            <span class="badge {{ $statusClass }} rounded-pill fs-11">{{ $cal->status }}</span>
                        </td>
                        @endif
                        <td>
                            <span class="badge {{ $cal->category_badge_class }} rounded-pill fs-11">{{ $cal->category }}</span>
                        </td>
                        <td class="text-muted fs-12">{{ $cal->activity ?: '—' }}</td>
                        <td class="text-center">
                            @if($cal->qr_status)
                            <i class="bi bi-qr-code-scan text-success fs-5" title="QR Aktif"></i>
                            @else
                            <i class="bi bi-slash-circle text-muted fs-5" title="QR Nonaktif"></i>
                            @endif
                        </td>
                        @if(!$isPatchedRole)
                        <td class="text-center">
                            <button type="button"
                                    class="btn btn-sm btn-outline-primary rounded-2 fw-semibold"
                                    onclick="showDetail({{ $cal->id }})"
                                    title="Lihat Detail"
                                    style="font-size:11px; padding: 4px 10px;">
                                <i class="bi bi-eye me-1"></i>Detail
                            </button>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($calendars->hasPages())
        <div class="px-4 py-3 border-top bg-white d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="text-muted fs-12">
                Menampilkan {{ $calendars->firstItem() }}–{{ $calendars->lastItem() }} dari {{ $calendars->total() }} data
            </div>
            <div class="d-flex align-items-center gap-1">
                @if($calendars->onFirstPage())
                <button class="btn btn-sm btn-outline-secondary rounded-2 disabled" style="font-size:12px;">
                    <i class="bi bi-chevron-left"></i>
                </button>
                @else
                <a href="{{ $calendars->previousPageUrl() }}" class="btn btn-sm btn-outline-secondary rounded-2" style="font-size:12px;">
                    <i class="bi bi-chevron-left"></i>
                </a>
                @endif

                @foreach($calendars->getUrlRange(max(1, $calendars->currentPage() - 2), min($calendars->lastPage(), $calendars->currentPage() + 2)) as $page => $url)
                <a href="{{ $url }}"
                   class="btn btn-sm rounded-2 fw-semibold {{ $page == $calendars->currentPage() ? 'btn-primary' : 'btn-outline-secondary' }}"
                   style="font-size:12px; min-width:32px;">
                    {{ $page }}
                </a>
                @endforeach

                @if($calendars->hasMorePages())
                <a href="{{ $calendars->nextPageUrl() }}" class="btn btn-sm btn-outline-secondary rounded-2" style="font-size:12px;">
                    <i class="bi bi-chevron-right"></i>
                </a>
                @else
                <button class="btn btn-sm btn-outline-secondary rounded-2 disabled" style="font-size:12px;">
                    <i class="bi bi-chevron-right"></i>
                </button>
                @endif
            </div>
        </div>
        @endif
        @endif
    </div>

</div>

@if($isSuperAdmin ?? (Auth::user()?->role === 'super_admin'))
{{-- ============================================================ --}}
{{-- MODAL: IMPORT KALENDER                                        --}}
{{-- ============================================================ --}}
<div class="sa-modal-overlay" id="modalImport">
    <div class="sa-modal-box" style="max-width:520px; text-align:left;">
        <div class="d-flex align-items-center gap-3 mb-4">
            <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-3 flex-shrink-0">
                <i class="bi bi-file-earmark-excel fs-4"></i>
            </div>
            <div>
                <div class="fw-bold text-dark fs-15">Import Kalender Akademik</div>
                <div class="text-muted fs-12">Upload file Excel sesuai template yang disediakan</div>
            </div>
            <button type="button" class="btn btn-sm ms-auto btn-outline-secondary rounded-2"
                    onclick="saModalClose('modalImport')">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form action="{{ route('superadmin.academic-calendar.import') }}"
              method="POST"
              enctype="multipart/form-data"
              id="importForm">
            @csrf
            <div class="import-dropzone mb-3" id="importDropzone" onclick="document.getElementById('importFileInput').click()">
                <input type="file" name="file" id="importFileInput" accept=".xlsx,.xls" onchange="onFileSelected(this)">
                <div id="dropzoneDefault">
                    <i class="bi bi-cloud-arrow-up text-primary" style="font-size:36px;"></i>
                    <div class="fw-semibold text-dark mt-2 fs-14">Klik untuk memilih file</div>
                    <div class="text-muted fs-12 mt-1">Format: .xlsx atau .xls · Maks. 10 MB</div>
                </div>
                <div id="dropzoneSelected" class="d-none">
                    <i class="bi bi-file-earmark-spreadsheet text-success" style="font-size:36px;"></i>
                    <div class="fw-semibold text-dark mt-2 fs-14" id="selectedFileName">—</div>
                    <div class="text-muted fs-12 mt-1">Klik untuk mengganti file</div>
                </div>
            </div>

            <div class="alert alert-info bg-info bg-opacity-10 border-info small rounded-3 mb-3 py-2">
                <i class="bi bi-info-circle me-1 text-info"></i>
                Seluruh data dalam file akan divalidasi terlebih dahulu. Jika ada error,
                <strong>tidak ada data yang tersimpan</strong>.
            </div>

            <div class="d-flex gap-2 justify-content-end mt-3">
                <button type="button" class="sa-modal-btn sa-modal-btn-cancel"
                        onclick="saModalClose('modalImport')">
                    <i class="bi bi-x-circle me-1"></i> Batal
                </button>
                <button type="submit"
                        id="btnImportSubmit"
                        class="sa-modal-btn sa-modal-btn-confirm success"
                        onclick="onImportSubmit(event)">
                    <i class="bi bi-upload me-1"></i> Proses Import
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@if(!in_array(Auth::user()?->role, ['admin', 'teacher', 'piket', 'guru_piket']))
{{-- ============================================================ --}}
{{-- MODAL: DETAIL KALENDER                                        --}}
{{-- ============================================================ --}}
<div class="sa-modal-overlay" id="modalDetail">
    <div class="sa-modal-box" style="max-width:680px; text-align:left; padding:28px;">
        <div class="d-flex align-items-center gap-3 mb-4">
            <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-3 flex-shrink-0">
                <i class="bi bi-calendar-event fs-4"></i>
            </div>
            <div>
                <div class="fw-bold text-dark fs-15">Detail Kalender Akademik</div>
                <div class="text-muted fs-12" id="detailSubtitle">—</div>
            </div>
            <button type="button" class="btn btn-sm ms-auto btn-outline-secondary rounded-2"
                    onclick="saModalClose('modalDetail')">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div id="detailLoadingState" class="text-center py-4">
            <div class="sa-loading-ring mx-auto mb-2" style="width:32px;height:32px;border-width:3px;"></div>
            <div class="text-muted fs-13">Memuat data...</div>
        </div>

        <div id="detailContent" class="d-none">
            <div class="row g-3 mb-3">
                <div class="col-6 col-md-4">
                    <div class="detail-field-label">Tanggal</div>
                    <div class="detail-field-value" id="dDate">—</div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="detail-field-label">Hari</div>
                    <div class="detail-field-value" id="dDayName">—</div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="detail-field-label">Tahun Ajaran</div>
                    <div class="detail-field-value" id="dAcademicYear">—</div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="detail-field-label">Semester</div>
                    <div class="detail-field-value" id="dSemester">—</div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="detail-field-label">Status</div>
                    <div id="dStatus">—</div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="detail-field-label">Kategori</div>
                    <div id="dCategory">—</div>
                </div>
            </div>

            <div class="bg-light rounded-3 p-3 mb-3">
                <div class="detail-field-label mb-2">Kegiatan</div>
                <div class="detail-field-value" id="dActivity">—</div>
            </div>

            <div class="bg-light rounded-3 p-3 mb-3">
                <div class="detail-field-label mb-2">Keterangan</div>
                <div class="text-muted fs-13" id="dDescription">—</div>
            </div>



            <div class="row g-2">
                <div class="col-6">
                    <div class="detail-field-label">Dibuat</div>
                    <div class="text-muted fs-12" id="dCreatedAt">—</div>
                </div>
                <div class="col-6">
                    <div class="detail-field-label">Diperbarui</div>
                    <div class="text-muted fs-12" id="dUpdatedAt">—</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@if($isSuperAdmin ?? (Auth::user()?->role === 'super_admin'))
{{-- ============================================================ --}}
{{-- MODAL: AKTIVASI TAHUN AJARAN                                  --}}
{{-- ============================================================ --}}
<div class="sa-modal-overlay" id="modalActivateYear">
    <div class="sa-modal-box">
        <div class="sa-modal-icon-wrap info">
            <i class="bi bi-check2-circle"></i>
        </div>
        <div class="sa-modal-title">Konfirmasi Aktivasi</div>
        <div class="sa-modal-desc" id="activateYearDesc">
            Apakah Anda yakin ingin mengaktifkan tahun ajaran ini?<br>
            Tahun ajaran sebelumnya akan otomatis <strong>dinonaktifkan</strong>.
        </div>
        <form action="{{ route('superadmin.academic-calendar.activate-year') }}"
              method="POST"
              id="activateYearForm">
            @csrf
            <input type="hidden" name="academic_year" id="activateYearInput">
            <div class="sa-modal-actions">
                <button type="button" class="sa-modal-btn sa-modal-btn-cancel"
                        onclick="saModalClose('modalActivateYear')">
                    <i class="bi bi-x-circle me-1"></i> Batal
                </button>
                <button type="submit"
                        class="sa-modal-btn sa-modal-btn-confirm info"
                        onclick="saLoadingShow('Mengaktifkan tahun ajaran...')">
                    <i class="bi bi-check2-circle me-1"></i> Ya, Aktifkan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL: IMPORT ERRORS                                          --}}
{{-- ============================================================ --}}
<div class="sa-modal-overlay" id="modalImportErrors">
    <div class="sa-modal-box" style="max-width:600px; text-align:left; padding:28px;">
        <div class="d-flex align-items-center gap-3 mb-4">
            <div class="sa-modal-icon-wrap danger" style="margin:0; width:48px; height:48px; font-size:22px; flex-shrink:0;">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
            <div>
                <div class="fw-bold text-dark fs-15">Import Gagal</div>
                <div class="text-muted fs-12">Ditemukan error berikut — tidak ada data yang tersimpan</div>
            </div>
            <button type="button" class="btn btn-sm ms-auto btn-outline-secondary rounded-2"
                    onclick="saModalClose('modalImportErrors')">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div id="errorList"
             style="max-height:320px; overflow-y:auto; background:#fef2f2; border-radius:10px; padding:14px;">
        </div>
        <div class="d-flex justify-content-end mt-3">
            <button type="button" class="sa-modal-btn sa-modal-btn-cancel"
                    onclick="saModalClose('modalImportErrors')">
                Tutup
            </button>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL: IMPORT SUCCESS                                         --}}
{{-- ============================================================ --}}
<div class="sa-modal-overlay" id="modalImportSuccess">
    <div class="sa-modal-box">
        <div class="sa-modal-icon-wrap success">
            <i class="bi bi-check-circle-fill"></i>
        </div>
        <div class="sa-modal-title">Import Kalender Berhasil</div>
        <div class="sa-modal-desc" id="importSuccessMsg">
            Data kalender berhasil diimport.
        </div>
        <div class="sa-modal-actions">
            <button type="button" class="sa-modal-btn sa-modal-btn-confirm success"
                    onclick="saModalClose('modalImportSuccess')">
                <i class="bi bi-check me-1"></i> OK
            </button>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
if (typeof saModalOpen !== 'function') {
    window.saModalOpen = function(id) {
        const overlay = document.getElementById(id);
        if (overlay) {
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    };
}
if (typeof saModalClose !== 'function') {
    window.saModalClose = function(id) {
        const overlay = document.getElementById(id);
        if (overlay) {
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    };
}
if (typeof saShowToast !== 'function') {
    window.saShowToast = function(type, title, message) {
        if (typeof saToast === 'function') {
            saToast(message, type, title);
        } else {
            console.warn((title ? title + ': ' : '') + message);
        }
    };
}
/* ============================================================
 * IMPORT MODAL
 * ============================================================ */
function openImportModal() {
    // Reset form
    document.getElementById('importForm').reset();
    document.getElementById('dropzoneDefault').classList.remove('d-none');
    document.getElementById('dropzoneSelected').classList.add('d-none');
    saModalOpen('modalImport');
}

function onFileSelected(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        document.getElementById('selectedFileName').textContent = file.name;
        document.getElementById('dropzoneDefault').classList.add('d-none');
        document.getElementById('dropzoneSelected').classList.remove('d-none');
    }
}

function onImportSubmit(e) {
    const fileInput = document.getElementById('importFileInput');
    if (!fileInput.files || !fileInput.files[0]) {
        e.preventDefault();
        saShowToast('warning', 'File Belum Dipilih', 'Silakan pilih file Excel terlebih dahulu.');
        return;
    }
    saModalClose('modalImport');
    saLoadingShow('Sedang memproses import kalender...');
}

/* Drag & Drop support */
const dropzone = document.getElementById('importDropzone');
if (dropzone) {
    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.classList.add('drag-over');
    });
    dropzone.addEventListener('dragleave', () => dropzone.classList.remove('drag-over'));
    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.classList.remove('drag-over');
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            const fileInput = document.getElementById('importFileInput');
            fileInput.files = files;
            onFileSelected(fileInput);
        }
    });
}

/* ============================================================
 * DETAIL MODAL
 * ============================================================ */
function showDetail(id) {
    // Reset & show loading
    document.getElementById('detailLoadingState').classList.remove('d-none');
    document.getElementById('detailContent').classList.add('d-none');
    document.getElementById('detailSubtitle').textContent = '—';
    saModalOpen('modalDetail');

    const detailUrl = "{{ url(request()->segment(1) . '/academic-calendar') }}/" + id;

    fetch(detailUrl, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(r => r.json())
    .then(json => {
        if (!json.success) throw new Error('Gagal memuat data.');

        const d = json.data;
        document.getElementById('detailSubtitle').textContent = `${d.date} · ${d.academic_year}`;
        document.getElementById('dDate').textContent         = d.date;
        document.getElementById('dDayName').textContent      = d.day_name;
        document.getElementById('dAcademicYear').textContent = d.academic_year;
        document.getElementById('dSemester').textContent     = d.semester;
        document.getElementById('dStatus').innerHTML         = `<span class="badge ${d.status_badge} rounded-pill fs-12">${d.status}</span>`;
        document.getElementById('dCategory').innerHTML       = `<span class="badge ${d.category_badge} rounded-pill fs-12">${d.category}</span>`;
        document.getElementById('dActivity').textContent     = d.activity || '—';
        document.getElementById('dDescription').textContent  = d.description || '—';
        document.getElementById('dCreatedAt').textContent    = d.created_at || '—';
        document.getElementById('dUpdatedAt').textContent    = d.updated_at || '—';



        document.getElementById('detailLoadingState').classList.add('d-none');
        document.getElementById('detailContent').classList.remove('d-none');
    })
    .catch(err => {
        saModalClose('modalDetail');
        saShowToast('error', 'Gagal Memuat', err.message || 'Terjadi kesalahan saat memuat detail.');
    });
}

/* ============================================================
 * ACTIVATE YEAR MODAL
 * ============================================================ */
function confirmActivateYear(year) {
    document.getElementById('activateYearInput').value = year;
    document.getElementById('activateYearDesc').innerHTML =
        `Apakah Anda yakin ingin mengaktifkan tahun ajaran <strong>${year}</strong>?<br>
         Tahun ajaran yang sedang aktif akan otomatis <strong>dinonaktifkan</strong>.`;
    saModalOpen('modalActivateYear');
}

/* ============================================================
 * FLASH: Import Success / Error (dari session)
 * ============================================================ */
document.addEventListener('DOMContentLoaded', function() {
    // Import success
    const successEl = document.getElementById('flash-import-success');
    if (successEl) {
        const count = successEl.dataset.count;
        const year  = successEl.dataset.year;
        document.getElementById('importSuccessMsg').innerHTML =
            `<strong>${count}</strong> data berhasil diimport.<br><br>
             <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
                 <i class="bi bi-mortarboard-fill me-1"></i>
                 Tahun Ajaran ${year || ''}
             </span>`;
        saModalOpen('modalImportSuccess');
    }

    // Import errors
    const errorsEl = document.getElementById('flash-import-errors');
    if (errorsEl) {
        let errors = [];
        try { errors = JSON.parse(errorsEl.dataset.errors); } catch(e) {}

        if (errors.length > 0) {
            let html = '';
            errors.forEach(err => {
                html += `<div class="d-flex gap-2 mb-2 p-2 bg-white rounded-2 border border-danger-subtle">
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle flex-shrink-0" style="height:fit-content;">
                        Baris ${err.row}
                    </span>
                    <div class="text-dark fs-13">${err.error}</div>
                </div>`;
            });
            document.getElementById('errorList').innerHTML = html;
            saModalOpen('modalImportErrors');
        }
    }

    // General flash error (from session 'error')
    @if(session('error'))
    saShowToast('error', 'Error', {{ Js::from(session('error')) }});
    @endif

    // General flash success (from session 'success')
    @if(session('success'))
    saShowToast('success', 'Berhasil', {{ Js::from(session('success')) }});
    @endif
});
</script>
@endpush
