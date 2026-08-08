@extends('Layouts.LayoutGuru')

@section('title', 'Mailbox Guru')

@section('content')

<div class="container-fluid py-2">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Mailbox Guru</h3>
            <p class="text-muted mb-0">Pusat pesan dan pemberitahuan internal sekolah.</p>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:48px;height:48px;background:linear-gradient(135deg,#0d6efd,#0dcaf0);border-radius:14px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:22px;">
                        <i class="bi bi-inbox"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Pesan</div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $messages->total() }} <span class="fs-6 text-muted fw-normal">Pesan</span></h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:48px;height:48px;background:linear-gradient(135deg,#dc3545,#f87171);border-radius:14px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:22px;">
                        <i class="bi bi-envelope-exclamation"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Belum Dibaca</div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $unreadCount }} <span class="fs-6 text-muted fw-normal">Pesan</span></h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:48px;height:48px;background:linear-gradient(135deg,#198754,#2ecc71);border-radius:14px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:22px;">
                        <i class="bi bi-envelope-check"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Sudah Dibaca</div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $readCount }} <span class="fs-6 text-muted fw-normal">Pesan</span></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="card-header bg-primary text-white p-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-envelope me-2"></i> Kotak Masuk Pesan Internal
            </h5>
            <div class="d-flex gap-2">
                <a href="{{ route('guru.mailbox.index') }}" class="btn btn-sm btn-light {{ !request('status') ? 'active fw-bold' : '' }}">Semua</a>
                <a href="{{ route('guru.mailbox.index', ['status' => 'unread']) }}" class="btn btn-sm btn-light {{ request('status') === 'unread' ? 'active fw-bold' : '' }}">Belum Dibaca</a>
                <a href="{{ route('guru.mailbox.index', ['status' => 'read']) }}" class="btn btn-sm btn-light {{ request('status') === 'read' ? 'active fw-bold' : '' }}">Sudah Dibaca</a>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" width="5%">No</th>
                            <th>Pengirim</th>
                            <th>Judul Pesan</th>
                            <th>Tanggal & Waktu</th>
                            <th>Status</th>
                            <th class="pe-4 text-center" width="12%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($messages as $msg)
                            <tr class="{{ $msg->status === 'unread' ? 'fw-bold bg-light' : '' }}">
                                <td class="ps-4 text-muted">{{ $loop->iteration + ($messages->currentPage() - 1) * $messages->perPage() }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width:34px;height:34px;background:linear-gradient(135deg,#198754,#2ecc71);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;font-weight:600;">
                                            {{ substr(optional($msg->sender)->name ?? 'S', 0, 1) }}
                                        </div>
                                        <span>{{ optional($msg->sender)->name ?? 'Sistem Sekolah' }}</span>
                                    </div>
                                </td>
                                <td>{{ $msg->title }}</td>
                                <td><span class="badge bg-light text-dark border"><i class="bi bi-clock me-1"></i>{{ $msg->created_at->isoFormat('D MMM YYYY, HH:mm') }}</span></td>
                                <td>
                                    @if ($msg->status === 'unread')
                                        <span class="badge bg-danger"><i class="bi bi-envelope-exclamation me-1"></i>Belum Dibaca</span>
                                    @else
                                        <span class="badge bg-success"><i class="bi bi-check-all me-1"></i>Sudah Dibaca</span>
                                    @endif
                                </td>
                                <td class="pe-4 text-center">
                                    <a href="{{ route('guru.mailbox.show', $msg->id) }}" class="btn btn-info btn-sm text-white px-3">
                                        <i class="bi bi-eye me-1"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                                    <p class="mb-0">Belum ada pesan internal di kotak masuk Anda.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($messages->hasPages())
            <div class="card-footer bg-white border-0 p-3 d-flex justify-content-center">
                {{ $messages->links() }}
            </div>
        @endif
    </div>

</div>

@endsection
