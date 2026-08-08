@extends('Layouts.LayoutSuperAdmin')

@section('title', 'Riwayat Aktivitas & Audit Log')

@section('content')
<div class="container-fluid px-0">

    {{-- PAGE HEADER --}}
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1" style="font-size:12px;">
                <li class="breadcrumb-item text-muted">Super Admin</li>
                <li class="breadcrumb-item active text-primary fw-semibold">Riwayat Aktivitas</li>
            </ol>
        </nav>
        <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-journal-text text-primary me-2"></i> Timeline Audit Aktivitas Sistem
        </h4>
        <p class="text-muted mb-0 fs-13">Catatan kronologis seluruh interaksi teknis, otentikasi, dan perubahan sistem</p>
    </div>

    {{-- FILTER BAR --}}
    <div class="card border-0 rounded-4 shadow-sm mb-4">
        <div class="card-body p-4">
            <form action="{{ route('superadmin.activity-log.index') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-4">
                        <label class="form-label text-muted small fw-semibold mb-1">
                            <i class="bi bi-search me-1"></i> Cari Aktivitas / Modul
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-search text-muted fs-13"></i>
                            </span>
                            <input type="text" name="activity" class="form-control border-start-0 rounded-end-3"
                                   placeholder="Cari aktivitas, modul, atau deskripsi..."
                                   value="{{ request('activity') }}">
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label text-muted small fw-semibold mb-1">
                            <i class="bi bi-calendar3 me-1"></i> Filter Tanggal
                        </label>
                        <input type="date" name="date" class="form-control rounded-3" value="{{ request('date') }}">
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label text-muted small fw-semibold mb-1">
                            <i class="bi bi-person-badge me-1"></i> Filter Role
                        </label>
                        <select name="role" class="form-select rounded-3">
                            <option value="">— Semua Role —</option>
                            @foreach($roles as $roleItem)
                            <option value="{{ $roleItem }}" {{ request('role') == $roleItem ? 'selected' : '' }}>
                                {{ $roleItem }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary fw-bold flex-fill rounded-3 d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-filter"></i> Filter
                        </button>
                        <a href="{{ route('superadmin.activity-log.index') }}" class="btn btn-outline-secondary rounded-3 px-3" title="Reset filter">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </div>
                @if(request('activity') || request('date') || request('role'))
                <div class="mt-3 d-flex align-items-center gap-2 flex-wrap">
                    <span class="text-muted small fw-semibold">Filter aktif:</span>
                    @if(request('activity'))
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3">
                        Aktivitas: {{ request('activity') }}
                    </span>
                    @endif
                    @if(request('date'))
                    <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-3">
                        Tanggal: {{ request('date') }}
                    </span>
                    @endif
                    @if(request('role'))
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3">
                        Role: {{ request('role') }}
                    </span>
                    @endif
                </div>
                @endif
            </form>
        </div>
    </div>

    {{-- AUDIT LOG TABLE --}}
    <div class="card border-0 rounded-4 shadow-sm">
        <div class="card-header bg-white py-3 px-4 border-bottom rounded-top-4 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-3">
                    <i class="bi bi-clock-history fs-5"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark">Timeline Riwayat Activity Log</div>
                    <div class="text-muted fs-12">Catatan seluruh aktivitas pengguna sistem</div>
                </div>
            </div>
            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle font-monospace px-3 py-2 fs-12">
                {{ $logs->total() }} Record
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3" style="white-space:nowrap;">Tanggal &amp; Jam</th>
                            <th class="py-3">Pengguna</th>
                            <th class="py-3">Role</th>
                            <th class="py-3">Aktivitas</th>
                            <th class="py-3">Modul</th>
                            <th class="py-3">Browser / IP</th>
                            <th class="pe-4 py-3">Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
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
                            </td>
                            <td class="py-3">
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 font-monospace fs-11">
                                    {{ $log->role ?? 'super_admin' }}
                                </span>
                            </td>
                            <td class="py-3">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 font-monospace fs-11">
                                    {{ $log->activity }}
                                </span>
                            </td>
                            <td class="py-3">
                                <span class="text-secondary small font-monospace">{{ $log->module ?? '—' }}</span>
                            </td>
                            <td class="py-3">
                                <div class="text-muted small font-monospace">
                                    <i class="bi bi-laptop me-1"></i> {{ $log->browser ?? 'N/A' }}
                                </div>
                                <div class="text-muted fs-11 font-monospace">
                                    <i class="bi bi-geo-alt me-1"></i> {{ $log->ip_address ?? '127.0.0.1' }}
                                </div>
                            </td>
                            <td class="pe-4 py-3 text-muted small">{{ $log->description ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="p-0">
                                <div class="sa-empty-state">
                                    <div class="sa-empty-icon">
                                        <i class="bi bi-journal-x"></i>
                                    </div>
                                    <div class="sa-empty-title">Tidak Ada Data Aktivitas</div>
                                    <div class="sa-empty-desc">
                                        Belum ada riwayat aktivitas yang sesuai dengan filter yang diterapkan.<br>
                                        Coba ubah atau reset filter pencarian.
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
            <div class="px-4 py-3 border-top">
                {{ $logs->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>

</div>
@endsection
