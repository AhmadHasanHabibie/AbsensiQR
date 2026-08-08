@extends('Layouts.LayoutGuru')

@section('title', 'Data Terlambat')

@section('content')

{{-- ============================================================ --}}
{{-- HOLIDAY LOCK MODAL (TAHAP 4) --}}
{{-- ============================================================ --}}
@if ($dailyStatus['is_holiday'])
<div class="modal fade" id="holidayLockModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="holidayLockModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-danger text-white rounded-top-4 p-3">
                <h5 class="modal-title fw-bold mb-0" id="holidayLockModalLabel">
                    <i class="bi bi-calendar-x-fill me-2"></i> Maaf Sedang Libur
                </h5>
                <button type="button" id="holidayModalCloseBtn" class="btn-close btn-close-white" aria-label="Kembali ke Dashboard"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <div style="width:72px;height:72px;background:rgba(220,53,69,0.1);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                        <i class="bi bi-moon-stars-fill text-danger" style="font-size:32px;"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Data Terlambat Tidak Tersedia</h5>
                    <p class="text-muted mb-1">Hari ini adalah <strong class="text-danger">{{ $dailyStatus['status'] }}</strong>.</p>
                    <p class="text-muted small mb-0">Pencatatan keterlambatan hanya aktif pada hari belajar.</p>
                </div>
                <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">
                    <i class="bi bi-info-circle me-1"></i> {{ \Carbon\Carbon::now('Asia/Jakarta')->isoFormat('dddd, D MMMM YYYY') }}
                </span>
            </div>
            <div class="modal-footer bg-light rounded-bottom-4 p-3 d-flex justify-content-center">
                <a href="{{ route('guru.dashboard') }}" class="btn btn-danger px-5 fw-semibold rounded-pill">
                    <i class="bi bi-arrow-left me-2"></i>Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var holidayModal = new bootstrap.Modal(document.getElementById('holidayLockModal'));
        holidayModal.show();
        document.getElementById('holidayModalCloseBtn').addEventListener('click', function () {
            window.location.href = '{{ route("guru.dashboard") }}';
        });
    });
</script>
@endif

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
