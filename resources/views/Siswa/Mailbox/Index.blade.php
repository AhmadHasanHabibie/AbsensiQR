@extends('Layouts.LayoutSiswa')

@section('title', 'Kotak Surat')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-primary mb-1">Kotak Surat</h3>
            <p class="text-muted mb-0">Daftar Surat Pemanggilan dan pemberitahuan resmi dari Wali Kelas Anda.</p>
        </div>
        <span class="badge bg-primary fs-6"><i class="bi bi-envelope-paper me-1"></i> {{ $mailboxes->total() }} Surat</span>
    </div>

    @if (session('success'))
        <div class="alert alert-success rounded-4 mb-4">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger rounded-4 mb-4">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
        </div>
    @endif

    <div class="row g-4">
        @forelse ($mailboxes as $mailbox)
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 position-relative {{ $mailbox->status === 'unread' ? 'border-start border-4 border-danger' : '' }}">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div class="rounded-3 p-3 text-danger" style="background: rgba(220, 53, 69, 0.1);">
                                <i class="bi bi-file-earmark-pdf-fill fs-2"></i>
                            </div>
                            <div class="d-flex flex-column align-items-end gap-1">
                                {!! $mailbox->mail_type_badge !!}
                                @if ($mailbox->status === 'unread')
                                    <span class="badge bg-danger"><i class="bi bi-circle-fill me-1 small"></i> Belum Dibaca</span>
                                @else
                                    <span class="badge bg-secondary"><i class="bi bi-check-all me-1"></i> Sudah Dibaca</span>
                                @endif
                            </div>
                        </div>

                        <h5 class="fw-bold mb-2 text-dark">{{ $mailbox->title }}</h5>
                        <p class="text-muted small mb-3 flex-grow-1">
                            {{ Str::limit($mailbox->description, 100) }}
                        </p>

                        <div class="rounded-3 p-3 bg-light mb-3 small">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted"><i class="bi bi-person me-1"></i> Pengirim:</span>
                                <strong>{{ $mailbox->teacher->name ?? 'Guru' }}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted"><i class="bi bi-calendar3 me-1"></i> Tanggal:</span>
                                <strong>{{ $mailbox->created_at->translatedFormat('d F Y') }}</strong>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-auto">
                            <a href="{{ route('siswa.mailbox.show', $mailbox->id) }}" class="btn btn-primary btn-sm flex-fill py-2">
                                <i class="bi bi-eye me-1"></i> Lihat Detail
                            </a>
                            <a href="{{ route('siswa.mailbox.download', $mailbox->id) }}" class="btn btn-outline-danger btn-sm flex-fill py-2">
                                <i class="bi bi-download me-1"></i> Unduh PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 text-center py-5">
                    <div class="card-body">
                        <i class="bi bi-inbox text-muted display-3 d-block mb-3"></i>
                        <h5 class="fw-bold">Belum ada Surat di Kotak Surat</h5>
                        <p class="text-muted mb-0">Surat Pemanggilan dari Wali Kelas akan muncul di halaman ini.</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $mailboxes->links() }}
    </div>
</div>
@endsection
