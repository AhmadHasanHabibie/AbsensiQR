@extends('Layouts.LayoutAdmin')

@section('title', 'IP Diblokir')

@section('content')

<div class="container-fluid py-2">

    {{-- Page Header --}}
    <div class="mb-4">
        <h3 class="fw-bold mb-1">IP Diblokir</h3>
        <p class="text-muted mb-0">Daftar alamat IP yang sedang diblokir (Active Defense & Blacklist).</p>
    </div>

    {{-- 4 Metric Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:52px;height:52px;background:linear-gradient(135deg,#6c757d,#495057);border-radius:16px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:24px;">
                        <i class="bi bi-ban"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total IP Diblokir</div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $totalBlocked }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:52px;height:52px;background:linear-gradient(135deg,#ffc107,#ffb300);border-radius:16px;display:flex;align-items:center;justify-content:center;color:#000;font-size:24px;">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Blok Sementara</div>
                        <h4 class="fw-bold mb-0 text-warning">{{ $temporaryCount }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:52px;height:52px;background:linear-gradient(135deg,#dc3545,#b02a37);border-radius:16px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:24px;">
                        <i class="bi bi-shield-x"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Blok Permanen</div>
                        <h4 class="fw-bold mb-0 text-danger">{{ $permanentCount }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:52px;height:52px;background:linear-gradient(135deg,#198754,#20c997);border-radius:16px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:24px;">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Kedaluwarsa / Di-unblock</div>
                        <h4 class="fw-bold mb-0 text-success">{{ $expiredCount }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter & Search Form Card --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('admin.blocked-ips.index') }}" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Pencarian</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Cari Alamat IP, alasan..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Filter Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status Blok</option>
                        <option value="temporary" {{ request('status') == 'temporary' ? 'selected' : '' }}>Blok Sementara</option>
                        <option value="permanent" {{ request('status') == 'permanent' ? 'selected' : '' }}>Blok Permanen</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Kedaluwarsa / Di-unblock</option>
                    </select>
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.blocked-ips.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Data Table Card --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-dark text-white p-3">
            <h5 class="mb-0 fw-semibold fs-6">
                <i class="bi bi-ban me-2"></i> Daftar IP Address Diblokir (Active Defense)
            </h5>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" width="4%">No</th>
                            <th>Alamat IP</th>
                            <th>Alasan Pemblokiran</th>
                            <th>Waktu Berakhir</th>
                            <th class="text-center">Status</th>
                            <th class="pe-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($blockedIps as $item)
                            <tr>
                                <td class="ps-4 fw-semibold text-muted">
                                    {{ $blockedIps->firstItem() + $loop->index }}
                                </td>
                                <td>
                                    <code class="fw-bold text-dark fs-6">{{ $item->ip_address }}</code>
                                </td>
                                <td>
                                    <span class="text-dark small">{{ $item->reason }}</span>
                                </td>
                                <td>
                                    @if($item->is_permanent)
                                        <span class="badge bg-danger">Selamanya (Permanen)</span>
                                    @elseif($item->blocked_until && $item->blocked_until->isFuture())
                                        <span class="badge bg-light text-dark border">
                                            <i class="bi bi-clock me-1"></i>{{ $item->blocked_until->isoFormat('DD MMM YYYY, HH:mm') }} WIB
                                        </span>
                                    @else
                                        <span class="text-muted small">Sudah Berakhir</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($item->is_permanent)
                                        <span class="badge bg-danger">Permanen</span>
                                    @elseif($item->blocked_until && $item->blocked_until->isFuture())
                                        <span class="badge bg-warning text-dark">Sementara</span>
                                    @else
                                        <span class="badge bg-success">Kedaluwarsa</span>
                                    @endif
                                </td>
                                <td class="pe-4 text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <button class="btn btn-info btn-sm text-white" data-bs-toggle="modal" data-bs-target="#detailModal-{{ $item->id }}">
                                            <i class="bi bi-eye me-1"></i> Lihat Detail
                                        </button>

                                        @if(($item->is_permanent || ($item->blocked_until && $item->blocked_until->isFuture())))
                                            <form method="POST" action="{{ route('admin.blocked-ips.unblock', $item->id) }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-success btn-sm" title="Buka Pemblokiran IP">
                                                    <i class="bi bi-unlock me-1"></i> Buka Blokir
                                                </button>
                                            </form>
                                        @endif

                                        @if(!$item->is_permanent)
                                            <form method="POST" action="{{ route('admin.blocked-ips.permanent', $item->id) }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Jadikan Permanent Blacklist">
                                                    <i class="bi bi-shield-lock me-1"></i> Jadikan Permanen
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="bi bi-shield-check display-1 text-success opacity-50 d-block mb-3"></i>
                                    <h5 class="fw-bold text-dark">Tidak ada IP Address yang diblokir.</h5>
                                    <p class="text-muted small mb-0">Sistem dalam keadaan aman dan belum ada pemblokiran IP aktif.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
        @if($blockedIps->hasPages())
            <div class="p-3 bg-light border-top rounded-bottom-4">
                {{ $blockedIps->links() }}
            </div>
        @endif
    </div>

</div>

{{-- Modals Detail Blocked IP --}}
@foreach($blockedIps as $item)
<div class="modal fade" id="detailModal-{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white p-3">
                <h5 class="modal-title fw-semibold">
                    <i class="bi bi-ban me-2"></i> Detail Pemblokiran Alamat IP
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-5 fw-bold text-dark">Alamat IP</div>
                    <div class="col-7"><code>{{ $item->ip_address }}</code></div>

                    <div class="col-5 fw-bold text-dark">Alasan Blok</div>
                    <div class="col-7 text-danger fw-semibold">{{ $item->reason }}</div>

                    <div class="col-5 fw-bold text-dark">Percobaan Login Gagal</div>
                    <div class="col-7">{{ $item->failed_logins ?? 0 }} kali</div>

                    <hr class="my-2">

                    <div class="col-5 fw-bold text-dark">Tanggal Mulai Blok</div>
                    <div class="col-7">{{ $item->created_at ? $item->created_at->isoFormat('DD MMMM YYYY, HH:mm:ss') . ' WIB' : '-' }}</div>

                    <div class="col-5 fw-bold text-dark">Tanggal Berakhir Blok</div>
                    <div class="col-7">
                        @if($item->is_permanent)
                            <span class="badge bg-danger">Selamanya (Permanen)</span>
                        @elseif($item->blocked_until)
                            {{ $item->blocked_until->isoFormat('DD MMMM YYYY, HH:mm:ss') }} WIB
                        @else
                            -
                        @endif
                    </div>

                    <div class="col-5 fw-bold text-dark">Status Pemblokiran</div>
                    <div class="col-7">
                        @if($item->is_permanent)
                            <span class="badge bg-danger">Permanen</span>
                        @elseif($item->blocked_until && $item->blocked_until->isFuture())
                            <span class="badge bg-warning text-dark">Sementara</span>
                        @else
                            <span class="badge bg-success">Kedaluwarsa</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light p-3">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection
