@extends('Layouts.LayoutAdmin')

@section('title', 'Import Data Siswa')

@section('content')

<div class="container-fluid">

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('warning') }}
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center">
            <i class="bi bi-x-circle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Terjadi Kesalahan :</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1 text-primary">Import Data Siswa</h3>
            <p class="text-muted mb-0">Upload file Excel (.xlsx / .xls) untuk menambahkan data siswa secara otomatis.</p>
        </div>
        <a href="{{ route('admin.siswa.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i> Kembali
        </a>
    </div>

    <div class="row g-4">

        <div class="col-lg-8">
            <div class="card border-0 rounded-4">
                <div class="card-header bg-primary text-white rounded-top-4">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-excel me-2"></i>Upload File Excel</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.siswa.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <div class="border-2 border-dashed rounded-4 p-5 text-center" style="background:#f8f9fa;border-style:dashed !important;border-color:#dee2e6 !important;">
                                <i class="bi bi-cloud-upload text-primary" style="font-size:48px;"></i>
                                <h5 class="mt-3 fw-bold">Upload File Excel</h5>
                                <p class="text-muted mb-3">Seret file ke sini atau klik untuk memilih</p>
                                <input type="file" name="file" class="form-control mb-2" accept=".xlsx,.xls" required>
                                <small class="text-muted">Format yang didukung: .xlsx dan .xls</small>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-upload me-2"></i> Import Sekarang
                            </button>
                            <a href="{{ route('admin.siswa.template') }}" class="btn btn-outline-primary">
                                <i class="bi bi-download me-2"></i> Download Template
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 rounded-4">
                <div class="card-header bg-info text-white rounded-top-4">
                    <h5 class="mb-0"><i class="bi bi-info-circle-fill me-2"></i>Petunjuk</h5>
                </div>
                <div class="card-body">
                    <ol class="mb-0 small">
                        <li class="mb-2">Download template Excel.</li>
                        <li class="mb-2">Isi data siswa sesuai format.</li>
                        <li class="mb-2">Jangan mengubah nama kolom.</li>
                        <li class="mb-2">Data yang valid akan otomatis disimpan lengkap dengan QR Code.</li>
                        <li class="mb-2">Jika ada satu baris salah, hanya baris tersebut yang gagal.</li>
                        <li>Data lainnya tetap berhasil diimport.</li>
                    </ol>
                </div>
            </div>

            <div class="card border-0 rounded-4 mt-3">
                <div class="card-header bg-warning rounded-top-4">
                    <h5 class="mb-0"><i class="bi bi-table me-2"></i>Format Kolom</h5>
                </div>
                <div class="card-body">
                    <ul class="mb-0 small">
                        <li class="mb-1"><strong>name</strong> &mdash; Nama lengkap siswa</li>
                        <li class="mb-1"><strong>nis</strong> &mdash; Nomor Induk Siswa</li>
                        <li class="mb-1"><strong>username</strong> &mdash; Username untuk login</li>
                        <li class="mb-1"><strong>password</strong> &mdash; Password (min. 6 karakter)</li>
                        <li class="mb-1"><strong>status</strong> &mdash; Aktif / Nonaktif (opsional)</li>
                        <li><strong>class_name</strong> &mdash; Nama kelas (opsional)</li>
                    </ul>
                </div>
            </div>
        </div>

    </div>

    {{-- Tabel Data Gagal --}}
    @if(session('failedRows'))
        <div class="card border-0 rounded-4 mt-4">
            <div class="card-header bg-danger text-white rounded-top-4">
                <h5 class="mb-0"><i class="bi bi-x-octagon-fill me-2"></i>Detail Data Gagal</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm mb-0">
                        <thead class="table-danger">
                            <tr>
                                <th class="p-3">Baris</th>
                                <th class="p-3">Nama</th>
                                <th class="p-3">Pesan Error</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(session('failedRows') as $fail)
                                <tr>
                                    <td class="p-3">{{ $fail['row'] }}</td>
                                    <td class="p-3">{{ $fail['name'] }}</td>
                                    <td class="p-3 text-danger">{{ $fail['error'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

</div>

@endsection
