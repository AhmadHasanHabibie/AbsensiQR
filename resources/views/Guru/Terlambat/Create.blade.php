@extends('Layouts.LayoutGuru')

@section('title', 'Tambah Terlambat')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-primary mb-1">Tambah Siswa Terlambat</h3>
            <p class="text-muted mb-0">Kelas: <strong>{{ $class->name }}</strong></p>
        </div>
        <a href="{{ route('guru.terlambat.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Kembali</a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-bottom rounded-top-4"><h5 class="fw-bold mb-0"><i class="bi bi-alarm-fill me-2 text-danger"></i>Form Kehadiran Terlambat</h5></div>
        <div class="card-body p-4">
            @if ($students->isEmpty())
                <div class="alert alert-info mb-0"><i class="bi bi-info-circle me-2"></i>Tidak ada siswa alpa yang dapat ditandai terlambat hari ini.</div>
            @else
                <form method="POST" action="{{ route('guru.terlambat.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nama Siswa</label>
                            <select name="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
                                <option value="">Pilih siswa yang terlambat</option>
                                @foreach ($students as $student)
                                    <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>{{ $student->name }} — {{ $student->nis ?? '-' }}</option>
                                @endforeach
                            </select>
                            @error('student_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jam Datang</label>
                            <input type="time" name="late_time" class="form-control @error('late_time') is-invalid @enderror" value="{{ old('late_time') }}" required>
                            @error('late_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Alasan</label>
                            <textarea name="late_note" rows="4" class="form-control @error('late_note') is-invalid @enderror" placeholder="Masukkan alasan keterlambatan (opsional)">{{ old('late_note') }}</textarea>
                            @error('late_note')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i>Simpan</button>
                            <a href="{{ route('guru.terlambat.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
