@extends('Layouts.LayoutAdmin')

@section('title', 'Import Data Guru Piket')

@section('content')

<div class="container-fluid">

    {{-- Alert Validasi --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Terjadi Kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-warning mb-1">Import Data Guru Piket</h3>
            <p class="text-muted mb-0">Upload file Excel (.xlsx / .xls) untuk menambahkan data Guru Piket secara massal.</p>
        </div>
        <a href="{{ route('admin.guru-piket.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i> Kembali
        </a>
    </div>

    <div class="row">

        {{-- Form Upload --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-excel me-2"></i> Upload File Excel</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.guru-piket.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-semibold">File Excel</label>
                            <input type="file" name="file" class="form-control" accept=".xlsx,.xls" required>
                            <small class="text-muted">Format yang didukung: .xlsx dan .xls</small>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-upload me-2"></i> Import Sekarang
                            </button>
                            <a href="{{ route('admin.guru-piket.template') }}" class="btn btn-outline-warning">
                                <i class="bi bi-download me-2"></i> Download Template
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sidebar Info --}}
        <div class="col-lg-4">

            {{-- Petunjuk --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle-fill me-2"></i> Petunjuk</h5>
                </div>
                <div class="card-body">
                    <ol class="mb-0">
                        <li class="mb-2">Download template Excel terlebih dahulu.</li>
                        <li class="mb-2">Isi data Guru Piket sesuai format kolom.</li>
                        <li class="mb-2">Jangan mengubah nama kolom header.</li>
                        <li class="mb-2">Kolom Email dan No HP bersifat opsional.</li>
                        <li class="mb-2">Jika satu baris salah, hanya baris tersebut yang gagal.</li>
                        <li>Data lainnya tetap berhasil diimport.</li>
                    </ol>
                </div>
            </div>

            {{-- Format Kolom --}}
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="bi bi-table me-2"></i> Format Kolom</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Kolom</th>
                                <th>Wajib</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>A</td><td>Nama Guru Piket</td><td><span class="badge bg-danger">Ya</span></td></tr>
                            <tr><td>B</td><td>NIP</td><td><span class="badge bg-danger">Ya</span></td></tr>
                            <tr><td>C</td><td>Username</td><td><span class="badge bg-danger">Ya</span></td></tr>
                            <tr><td>D</td><td>Password</td><td><span class="badge bg-danger">Ya</span></td></tr>
                            <tr><td>E</td><td>Status</td><td><span class="badge bg-secondary">Opsional</span></td></tr>
                        </tbody>
                    </table>
                    <small class="text-muted mt-2 d-block">
                        Nilai Status: <strong>Aktif</strong> atau <strong>Nonaktif</strong>. Default: Aktif.
                    </small>
                </div>
            </div>

            {{-- Validasi --}}
            <div class="card border-0 shadow-sm mt-3 border-start border-4 border-danger">
                <div class="card-body">
                    <h6 class="fw-bold text-danger mb-2"><i class="bi bi-shield-exclamation me-1"></i> Aturan Validasi</h6>
                    <ul class="mb-0 small text-muted">
                        <li>Username harus unik di seluruh role.</li>
                        <li>NIP harus unik di seluruh role.</li>
                        <li>Password minimal 6 karakter.</li>
                        <li>Data identik dalam file akan ditolak.</li>
                    </ul>
                </div>
            </div>

        </div>
    </div>

</div>

@endsection
