@extends('Layouts.LayoutGuru')

@section('title', 'Detail Pesan Mailbox')

@section('content')

<div class="container-fluid py-2">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Detail Pesan Mailbox</h3>
            <p class="text-muted mb-0">Informasi detail pesan internal.</p>
        </div>
        <a href="{{ route('guru.mailbox.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i> Kembali ke Kotak Masuk
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 bg-white">
                <div class="card-header bg-primary text-white rounded-top-4 p-4 d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-envelope-open me-2"></i> {{ $message->title }}
                    </h5>
                    <span class="badge bg-light text-primary fw-bold">
                        <i class="bi bi-clock me-1"></i> {{ $message->created_at->isoFormat('D MMMM YYYY, HH:mm') }} WIB
                    </span>
                </div>

                <div class="card-body p-4">
                    <div class="p-3 bg-light rounded-3 mb-4 border">
                        <div class="row g-2 text-sm">
                            <div class="col-3 text-muted">Pengirim:</div>
                            <div class="col-9 fw-bold text-dark">{{ optional($message->sender)->name ?? 'Sistem Sekolah' }} ({{ optional($message->sender)->role ?? 'System' }})</div>

                            <div class="col-3 text-muted">Penerima:</div>
                            <div class="col-9 fw-bold text-dark">{{ optional($message->receiver)->name }}</div>

                            <div class="col-3 text-muted">Status:</div>
                            <div class="col-9">
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i> Sudah Dibaca
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="message-body p-3 border rounded-3 bg-white min-vh-25">
                        <h6 class="fw-bold text-dark mb-3">Isi Pesan:</h6>
                        <div class="lh-relaxed text-dark" style="white-space: pre-wrap;">{{ $message->content }}</div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <a href="{{ route('guru.mailbox.index') }}" class="btn btn-secondary px-4">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>

                        <a href="{{ route('guru.attendance.index') }}" class="btn btn-success px-4 fw-bold shadow-sm">
                            <i class="bi bi-clipboard-check me-1"></i> Konfirmasi Absensi Sekarang
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
