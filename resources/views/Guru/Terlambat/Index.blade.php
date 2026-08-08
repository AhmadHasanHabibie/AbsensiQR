@extends('Layouts.LayoutGuru')

@section('title', 'Data Terlambat')

@section('content')
<div class="container-fluid">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-primary mb-1">Data Siswa Terlambat</h3>
            <p class="text-muted mb-0">Kelas: <strong>{{ $class->name }}</strong> — {{ now()->translatedFormat('d F Y') }}</p>
        </div>
        <a href="{{ route('guru.terlambat.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Tambah Terlambat
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-bottom rounded-top-4"><h5 class="fw-bold mb-0"><i class="bi bi-alarm me-2 text-danger"></i>Daftar Terlambat Hari Ini</h5></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th class="ps-4">No</th><th>Nama</th><th>Kelas</th><th>Jam Masuk</th><th>Jam Datang</th><th>Total Terlambat</th><th>Alasan</th><th class="pe-4">Aksi</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($lateAttendances as $attendance)
                            <tr>
                                <td class="ps-4">{{ $loop->iteration }}</td>
                                <td class="fw-semibold">{{ $attendance->student->name }}</td>
                                <td>{{ $attendance->student->schoolClass?->name ?? '-' }}</td>
                                <td><span class="badge bg-light text-dark border"><i class="bi bi-clock me-1"></i>{{ $attendance->jam_masuk }}</span></td>
                                <td>
                                    @if ($attendance->late_time || $attendance->check_in)
                                        <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($attendance->late_time ?? $attendance->check_in)->format('H:i') }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if ($attendance->total_terlambat_formatted !== '-')
                                        <span class="badge bg-warning text-dark"><i class="bi bi-clock-history me-1"></i>{{ $attendance->total_terlambat_formatted }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $attendance->late_note ?: '-' }}</td>
                                <td class="pe-4"><span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Hadir Terlambat</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-5"><i class="bi bi-alarm fs-1 d-block mb-2"></i>Belum ada siswa yang ditandai terlambat hari ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
