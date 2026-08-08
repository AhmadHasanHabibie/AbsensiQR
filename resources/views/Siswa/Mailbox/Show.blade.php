@extends('Layouts.LayoutSiswa')

@section('title', 'Detail Surat Pemanggilan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-primary mb-1">Detail Surat Pemanggilan</h3>
            <p class="text-muted mb-0">Surat Resmi Pemanggilan Orang Tua / Wali Siswa SMKN 17 Jakarta.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('siswa.mailbox.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Kotak Surat
            </a>
            <a href="{{ route('siswa.mailbox.download', $mailbox->id) }}" class="btn btn-danger">
                <i class="bi bi-download me-1"></i> Unduh PDF
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- INFORMASI SURAT --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-file-earmark-text me-2"></i>Informasi Surat</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Kategori Surat</small>
                        {!! $mailbox->mail_type_badge !!}
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Judul Surat</small>
                        <strong class="fs-6 text-dark">{{ $mailbox->title }}</strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Guru Pengirim</small>
                        <strong class="text-dark">{{ $mailbox->teacher->name ?? '-' }}</strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Tanggal Surat</small>
                        <strong class="text-dark">{{ $mailbox->created_at->translatedFormat('d F Y H:i') }} WIB</strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Periode Absensi</small>
                        <strong class="text-dark">{{ $mailbox->week_start->translatedFormat('d M') }} - {{ $mailbox->week_end->translatedFormat('d M Y') }}</strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Total Catatan Minggu Ini</small>
                        <span class="badge {{ $mailbox->mail_type === 'late' ? 'bg-warning text-dark' : ($mailbox->mail_type === 'permission' ? 'bg-info' : 'bg-danger') }} fs-6">{{ $mailbox->alpha_total }} Kali</span>
                    </div>

                    <div class="mb-0">
                        <small class="text-muted d-block">Status Dibaca</small>
                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Sudah Dibaca</span>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-chat-text me-2"></i>Isi Pesan / Penjelasan</h5>
                </div>
                <div class="card-body p-4">
                    <p class="text-secondary mb-0" style="white-space: pre-line;">{{ $mailbox->description }}</p>
                </div>
            </div>
        </div>

        {{-- PRATINJAU DOKUMEN PDF --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-file-earmark-pdf me-2 text-danger"></i>Pratinjau Dokumen Resmi PDF</h5>
                    <a href="{{ route('siswa.mailbox.download', $mailbox->id) }}" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-download me-1"></i> Unduh File
                    </a>
                </div>
                <div class="card-body p-2" style="min-height: 600px;">
                    @if (Storage::disk('public')->exists($mailbox->pdf_file))
                        <iframe src="{{ asset('storage/' . $mailbox->pdf_file) }}" width="100%" height="650px" style="border: none; border-radius: 12px;"></iframe>
                    @else
                        <div class="alert alert-warning m-4">
                            File PDF tidak ditemukan di storage aplikasi. Silakan hubungi wali kelas Anda.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
