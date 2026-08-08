@extends('Layouts.LayoutAdmin')

@section('title', 'Tambah Siswa')

@section('content')

<div class="container-fluid">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Tambah Siswa</h3>
            <p class="text-muted mb-0">Tambahkan data siswa baru ke dalam sistem.</p>
        </div>
        <a href="{{ route('admin.siswa.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="card border-0 rounded-4 shadow-sm">
        <div class="card-header bg-white py-3 px-4 border-bottom">
            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-person-plus text-primary me-2"></i>Formulir Data Siswa Baru</h5>
        </div>
        <div class="card-body p-4">

            <form action="{{ route('admin.siswa.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row g-4">

                    {{-- Foto Siswa --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Foto Siswa</label>
                        <div class="border rounded-4 p-4 text-center" style="background:#f8f9fa;">
                            <div class="mb-3">
                                <div id="photoPreview" style="width:120px;height:120px;margin:0 auto;border-radius:50%;background:#e9ecef;display:flex;align-items:center;justify-content:center;overflow:hidden;">
                                    <i class="bi bi-person-plus text-secondary" style="font-size:48px;"></i>
                                </div>
                            </div>
                            <input type="file" name="photo" id="photoInput"
                                   class="form-control @error('photo') is-invalid @enderror" accept="image/*">
                            <small class="text-muted">Format: JPG, JPEG, PNG. Maksimal 2MB.</small>
                            @error('photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Nama --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nama Siswa <span class="text-danger">*</span></label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" placeholder="Masukkan nama siswa">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- NIS --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">NIS <span class="text-danger">*</span></label>
                        <input type="text" name="nis"
                               class="form-control @error('nis') is-invalid @enderror"
                               value="{{ old('nis') }}" placeholder="Masukkan NIS">
                        @error('nis')
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
                                   value="{{ old('username') }}" placeholder="Masukkan username">
                        </div>
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Kelas --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Kelas <span class="text-danger">*</span></label>
                        <select name="class_id" class="form-select @error('class_id') is-invalid @enderror">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('class_id')
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
                                   placeholder="Masukkan password">
                        </div>
                        <small class="text-muted">Minimal 6 karakter.</small>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Konfirmasi Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" name="password_confirmation"
                                   class="form-control" placeholder="Ulangi password">
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="1" {{ old('status','1') == '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Nonaktif</option>
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
                    <a href="{{ route('admin.siswa.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Simpan Data
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

@endsection

@push('js')
<script>
    document.getElementById('photoInput').addEventListener('change', function(e) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('photoPreview');
            preview.innerHTML = '<img src="'+e.target.result+'" style="width:100%;height:100%;object-fit:cover;">';
        }
        if(this.files[0]) reader.readAsDataURL(this.files[0]);
    });
</script>
@endpush
