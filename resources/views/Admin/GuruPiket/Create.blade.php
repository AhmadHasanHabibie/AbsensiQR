@extends('Layouts.LayoutAdmin')

@section('title', 'Tambah Guru Piket')

@section('content')

<div class="container-fluid">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Tambah Guru Piket</h3>
            <p class="text-muted mb-0">Buat akun baru untuk petugas Guru Piket.</p>
        </div>
        <a href="{{ route('admin.guru-piket.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-person-plus text-primary me-2"></i>Formulir Tambah Guru Piket</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.guru-piket.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="nip" class="form-label fw-semibold">NIP <span class="text-danger">*</span></label>
                            <input type="text" name="nip" id="nip" class="form-control @error('nip') is-invalid @enderror" value="{{ old('nip') }}" required>
                            @error('nip')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="username" class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" id="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required minlength="6">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="status" class="form-label fw-semibold">Status Akun <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.guru-piket.index') }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i>Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
