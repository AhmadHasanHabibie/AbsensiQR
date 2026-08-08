@extends('Layouts.LayoutAdmin')

@section('title', 'Download QR Code Siswa')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-primary mb-1">Download QR Code Siswa</h3>
            <p class="text-muted mb-0">Cetak dan unduh QR Code siswa permanen berbasis kelas untuk absensi sekolah.</p>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger rounded-4 mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        </div>
    @endif

    {{-- CARD FILTER --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-funnel me-2"></i>Filter Kelas</h5>
        </div>
        <div class="card-body p-4">
            <form method="GET" action="{{ route('admin.qr-siswa.index') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-6 col-lg-5">
                        <label class="form-label fw-semibold">Pilih Kelas <span class="text-danger">*</span></label>
                        <select name="class_id" class="form-select" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach ($classes as $c)
                                <option value="{{ $c->id }}" {{ (string) $selectedClassId === (string) $c->id ? 'selected' : '' }}>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 col-lg-7 d-flex flex-wrap gap-2">
                        <button type="submit" class="btn btn-primary px-4 py-2">
                            <i class="bi bi-search me-1"></i> Lihat Data
                        </button>

                        @if ($selectedClassId && $students->isNotEmpty())
                            <a href="{{ route('admin.qr-siswa.pdf', ['class_id' => $selectedClassId]) }}" class="btn btn-danger px-4 py-2">
                                <i class="bi bi-file-earmark-pdf-fill me-1"></i> Download PDF QR
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- TABEL SISWA PER KELAS --}}
    @if ($selectedClassId)
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-primary">
                    <i class="bi bi-qr-code me-2"></i>Daftar QR Code Siswa — {{ $selectedClass->name ?? '' }}
                </h5>
                <span class="badge bg-primary fs-6">{{ $students->count() }} Siswa</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3">No</th>
                                <th class="py-3 text-center">Foto</th>
                                <th class="py-3">Nama</th>
                                <th class="py-3">NIS</th>
                                <th class="py-3">Kelas</th>
                                <th class="py-3 text-center">QR Code</th>
                                <th class="px-4 py-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($students as $student)
                                <tr>
                                    <td class="px-4 fw-bold">{{ $loop->iteration }}</td>
                                    <td class="text-center">
                                        @if ($student->photo)
                                            <img src="{{ asset('storage/' . $student->photo) }}" class="rounded-circle border" width="40" height="40" style="object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center fw-bold" style="width:40px; height:40px;">
                                                {{ substr($student->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td><strong class="text-dark">{{ $student->name }}</strong></td>
                                    <td>{{ $student->nis ?? '-' }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $student->schoolClass->name ?? '-' }}</span></td>
                                    <td class="text-center py-2">
                                        @if ($student->qr_code)
                                            <img src="{{ asset('storage/' . $student->qr_code) }}" class="border rounded p-1" width="64" height="64" style="background:#fff;">
                                        @else
                                            <span class="text-muted small">QR Tidak Tersedia</span>
                                        @endif
                                    </td>
                                    <td class="px-4 text-center">
                                        @if ($student->status)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger">Nonaktif</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        <div class="alert alert-warning border-0 rounded-4 m-3 p-4">
                                            <i class="bi bi-exclamation-circle fs-1 d-block mb-2 text-warning"></i>
                                            <h6 class="fw-bold mb-1">Belum terdapat data siswa pada kelas ini.</h6>
                                            <p class="mb-0 small text-muted">Silakan pilih kelas lain atau tambahkan data siswa pada kelas ini terlebih dahulu.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm rounded-4 text-center py-5">
            <div class="card-body">
                <i class="bi bi-qr-code-scan text-muted display-3 d-block mb-3"></i>
                <h5 class="fw-bold">Pilih Kelas Terlebih Dahulu</h5>
                <p class="text-muted mb-0">Gunakan filter kelas di atas untuk melihat dan mengunduh PDF QR Code siswa.</p>
            </div>
        </div>
    @endif
</div>
@endsection
