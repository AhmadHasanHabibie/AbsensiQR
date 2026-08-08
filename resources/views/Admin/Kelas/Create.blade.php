@extends('Layouts.LayoutAdmin')

@section('title', 'Tambah Kelas')

@section('content')

<div class="container-fluid">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Tambah Kelas</h3>
            <p class="text-muted mb-0">Tambahkan data kelas baru.</p>
        </div>
        <a href="{{ route('admin.kelas.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="card border-0 rounded-4 shadow-sm">
        <div class="card-header bg-white py-3 px-4 border-bottom">
            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-building text-primary me-2"></i>Formulir Tambah Data Kelas</h5>
        </div>
        <div class="card-body p-4">

            <form action="{{ route('admin.kelas.store') }}" method="POST">
                @csrf

                <div class="row g-4">

                    {{-- Nama Kelas --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nama Kelas <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-building"></i></span>
                            <input type="text" name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" placeholder="Contoh : XII RPL 1">
                        </div>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Wali Kelas --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Wali Kelas</label>
                        <select name="teacher_id" class="form-select @error('teacher_id') is-invalid @enderror">
                            <option value="">-- Pilih Wali Kelas --</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->name }} ({{ $teacher->nip }})
                                </option>
                            @endforeach
                        </select>
                        @error('teacher_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-end gap-2">
                    <button type="reset" class="btn btn-light">
                        <i class="bi bi-arrow-clockwise me-1"></i> Reset
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Simpan
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

@endsection
