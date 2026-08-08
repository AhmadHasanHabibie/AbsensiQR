@extends('Layouts.LayoutGuru')

@php
    $pageTitle = match($mailType) {
        'late'       => 'Surat Pembinaan Keterlambatan',
        'permission' => 'Surat Klarifikasi Izin',
        default      => 'Surat Pemanggilan Orang Tua / Wali',
    };
@endphp

@section('title', $pageTitle)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-primary mb-1">{{ $pageTitle }}</h3>
            <p class="text-muted mb-0">Buat dan kirim surat resmi berbasis PDF melalui Kotak Surat internal sekolah.</p>
        </div>
        <a href="{{ route('guru.dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger rounded-4 mb-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        {{-- INFORMASI OTOMATIS --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-info-circle me-2"></i>Informasi Siswa & Periode</h5>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="rounded-circle {{ $mailType === 'late' ? 'bg-warning-subtle text-warning' : ($mailType === 'permission' ? 'bg-info-subtle text-info' : 'bg-danger-subtle text-danger') }} d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 72px; height: 72px;">
                            <i class="bi bi-person-exclamation fs-1"></i>
                        </div>
                        <h4 class="fw-bold mb-1">{{ $student->name }}</h4>
                        <span class="badge bg-primary fs-6">{{ $class->name }}</span>
                    </div>

                    <div class="list-group list-group-flush rounded-3 border">
                        <div class="list-group-item d-flex justify-content-between py-3">
                            <span class="text-muted">NIS</span>
                            <strong class="text-dark">{{ $student->nis ?? '-' }}</strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between py-3">
                            <span class="text-muted">Wali Kelas</span>
                            <strong class="text-dark">{{ $teacher->name }}</strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between py-3">
                            <span class="text-muted">{{ $countLabel }}</span>
                            <strong class="badge {{ $mailType === 'late' ? 'bg-warning text-dark' : ($mailType === 'permission' ? 'bg-info' : 'bg-danger') }} fs-6">{{ $categoryTotal }} Kali</strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between py-3">
                            <span class="text-muted">Periode Minggu</span>
                            <strong class="text-dark">{{ $weekStart->translatedFormat('d M') }} - {{ $weekEnd->translatedFormat('d M Y') }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- FORM ISIAN GURU --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-pencil-square me-2"></i>Formulir Isian Surat</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('guru.mailbox.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="student_id" value="{{ $student->id }}">
                        <input type="hidden" name="mail_type" value="{{ $mailType }}">
                        <input type="hidden" name="week_start" value="{{ $weekStart->toDateString() }}">
                        <input type="hidden" name="week_end" value="{{ $weekEnd->toDateString() }}">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Perihal <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $defaultTitle) }}" required placeholder="Masukkan perihal...">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Isi Surat / Penjelasan <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="4" required placeholder="Tuliskan alasan atau konteks tambahan...">{{ old('description', $defaultDesc) }}</textarea>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tanggal Pertemuan <span class="text-danger">*</span></label>
                                <input type="date" name="meeting_date" class="form-control" value="{{ old('meeting_date', now()->addDays(2)->toDateString()) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Jam Pertemuan <span class="text-danger">*</span></label>
                                <input type="text" name="meeting_time" class="form-control" value="{{ old('meeting_time', '09:00') }}" required placeholder="Contoh: 09:00">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Lokasi Pertemuan <span class="text-danger">*</span></label>
                            <input type="text" name="meeting_location" class="form-control" value="{{ old('meeting_location', 'Ruang Bimbingan Konseling (BK) / Ruang Wali Kelas SMKN 17 Jakarta') }}" required placeholder="Contoh: Ruang Wali Kelas">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Catatan (Opsional)</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Catatan tambahan bila ada (misal: harap membawa Kartu Identitas)...">{{ old('notes') }}</textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('guru.dashboard') }}" class="btn btn-light border px-4">Batal</a>
                            <button type="submit" class="btn btn-warning text-dark fw-bold px-4 py-2">
                                <i class="bi bi-file-earmark-pdf-fill me-2"></i>Buat & Kirim Surat
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
