@extends('Layouts.LayoutAdmin')

@section('title', 'Edit Operator')

@section('content')

<div class="container-fluid">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Edit Operator</h3>
            <p class="text-muted mb-0">Perbarui data operator.</p>
        </div>
        <a href="{{ route('admin.operator.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 rounded-4">
                <div class="card-header bg-warning text-dark rounded-top-4">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Data Operator</h5>
                </div>
                <div class="card-body p-4">

                    <form action="{{ route('admin.operator.update', $operator->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">

                            {{-- Nama Operator --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nama Operator <span class="text-danger">*</span></label>
                                <input type="text" name="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $operator->name) }}" placeholder="Masukkan nama operator">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- NIP --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">NIP <span class="text-danger">*</span></label>
                                <input type="text" name="nip"
                                       class="form-control @error('nip') is-invalid @enderror"
                                       value="{{ old('nip', $operator->nip) }}" placeholder="Masukkan NIP operator">
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
                                           value="{{ old('username', $operator->username) }}" placeholder="Username untuk login">
                                </div>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Password Baru --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Password Baru
                                    <small class="text-muted">(kosongkan jika tidak ingin mengganti)</small>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" name="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           placeholder="Masukkan password baru">
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Status --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror">
                                    <option value="1" {{ old('status', $operator->status) == 1 ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ old('status', $operator->status) == 0 ? 'selected' : '' }}>Tidak Aktif</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.operator.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-2"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-save me-2"></i> Update Operator
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

</div>

@endsection
