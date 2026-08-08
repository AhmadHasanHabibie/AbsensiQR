@extends('Layouts.LayoutAdmin')

@section('title', 'Tambah Guru')

@section('content')

<div class="container-fluid">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Tambah Guru</h3>
            <p class="text-muted mb-0">Tambahkan data guru baru ke dalam sistem.</p>
        </div>
        <a href="{{ route('admin.guru.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 rounded-4 shadow-sm">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-person-plus text-primary me-2"></i>Formulir Tambah Data Guru</h5>
                </div>
                <div class="card-body p-4">

                    <form action="{{ route('admin.guru.store') }}" method="POST">
                        @csrf

                        <div class="row g-4">

                            {{-- Nama Guru --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nama Guru <span class="text-danger">*</span></label>
                                <input type="text" name="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name') }}" placeholder="Masukkan nama guru">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- NIP --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">NIP <span class="text-danger">*</span></label>
                                <input type="text" name="nip"
                                       class="form-control @error('nip') is-invalid @enderror"
                                       value="{{ old('nip') }}" placeholder="Masukkan NIP guru">
                                @error('nip')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Username --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" name="username"
                                           class="form-control @error('username') is-invalid @enderror"
                                           value="{{ old('username') }}" placeholder="Username untuk login">
                                </div>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Password --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" name="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           placeholder="Password akun">
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Status --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror">
                                    <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.guru.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-2"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i> Simpan Guru
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

</div>

@endsection
