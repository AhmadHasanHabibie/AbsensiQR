@extends('Layouts.LayoutAdmin')

@section('title', 'Detail Siswa')

@section('content')

<div class="container-fluid">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Detail Siswa</h3>
            <p class="text-muted mb-0">Informasi lengkap data siswa.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.siswa.edit', $siswa->id) }}" class="btn btn-warning">
                <i class="bi bi-pencil-square me-1"></i> Edit
            </a>
            <a href="{{ route('admin.siswa.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row g-4">

        {{-- DATA SISWA --}}
        <div class="col-lg-8">
            <div class="card border-0 rounded-4">
                <div class="card-header bg-primary text-white rounded-top-4">
                    <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Data Siswa</h5>
                </div>
                <div class="card-body p-4">

                    <div class="d-flex flex-wrap gap-4 mb-4 pb-3 border-bottom">
                        <div class="flex-shrink-0">
                            @if ($siswa->photo)
                                <img src="{{ asset('storage/' . $siswa->photo) }}" class="rounded-3 border"
                                     width="130" height="130" style="object-fit:cover;">
                            @else
                                <div class="rounded-3 border d-flex align-items-center justify-content-center"
                                     style="width:130px;height:130px;background:#f8f9fa;">
                                    <i class="bi bi-person display-4 text-secondary"></i>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h4 class="fw-bold mb-1">{{ $siswa->name }}</h4>
                            <span class="badge bg-primary">{{ $siswa->schoolClass->name ?? '-' }}</span>
                            <div class="mt-2">
                                @if ($siswa->status)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Nonaktif</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <table class="table table-borderless align-middle mb-0">
                        <tr>
                            <th width="180" style="color:#6c757d;font-size:13px;">Nama Siswa</th>
                            <td><strong>{{ $siswa->name }}</strong></td>
                        </tr>
                        <tr>
                            <th style="color:#6c757d;font-size:13px;">NIS</th>
                            <td><span class="badge bg-light text-dark">{{ $siswa->nis }}</span></td>
                        </tr>
                        <tr>
                            <th style="color:#6c757d;font-size:13px;">Username</th>
                            <td>{{ $siswa->username }}</td>
                        </tr>
                        <tr>
                            <th style="color:#6c757d;font-size:13px;">Kelas</th>
                            <td>{{ $siswa->schoolClass->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th style="color:#6c757d;font-size:13px;">Wali Kelas</th>
                            <td>{{ $siswa->schoolClass->teacher->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th style="color:#6c757d;font-size:13px;">Dibuat</th>
                            <td>{{ $siswa->created_at->format('d F Y H:i') }}</td>
                        </tr>
                    </table>

                </div>
            </div>
        </div>

        {{-- QR CODE --}}
        <div class="col-lg-4">
            <div class="card border-0 rounded-4">
                <div class="card-header bg-success text-white rounded-top-4">
                    <h5 class="mb-0"><i class="bi bi-qr-code me-2"></i>QR Code Absensi</h5>
                </div>
                <div class="card-body text-center p-4">

                    @if ($siswa->qr_code)
                        <img src="{{ asset('storage/' . $siswa->qr_code) }}"
                             class="img-fluid border rounded-3 p-2 mb-3"
                             style="max-width:240px;background:#fff;">

                        <div class="mb-3">
                            <span class="badge bg-success">QR Code Aktif</span>
                        </div>

                        <a href="{{ route('admin.siswa.downloadQr', $siswa->id) }}"
                           class="btn btn-danger w-100">
                            <i class="bi bi-file-earmark-pdf-fill me-1"></i> Download QR PDF
                        </a>
                    @else
                        <div class="py-4">
                            <i class="bi bi-qr-code display-1 text-secondary"></i>
                            <h6 class="mt-3 fw-bold">QR Belum Dibuat</h6>
                            <p class="text-muted small">QR otomatis dibuat ketika akun siswa dibuat.</p>
                        </div>
                    @endif

                </div>
            </div>

            <div class="card border-0 rounded-4 mt-3">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-2 text-primary"></i>Informasi QR</h6>
                    <ul class="mb-0 small">
                        <li class="mb-1">QR Code bersifat permanen.</li>
                        <li class="mb-1">Setiap siswa hanya memiliki satu QR.</li>
                        <li class="mb-1">QR digunakan untuk absensi sekolah.</li>
                        <li>QR dapat dicetak melalui PDF.</li>
                    </ul>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection
