@extends('Layouts.LayoutOperator')

@section('title', 'Detail Siswa Terlambat')

@section('content')

<div class="container-fluid">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Detail Siswa Terlambat</h3>
            <p class="text-muted mb-0">Informasi detail keterlambatan siswa.</p>
        </div>
        <a href="{{ route('operator.terlambat.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-info text-white rounded-top-4 p-3">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-person-vcard me-2"></i> Detail Data Keterlambatan
                    </h5>
                </div>

                <div class="card-body p-4">

                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Nama Siswa</div>
                        <div class="col-md-8">{{ optional($attendance->student)->name }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">NIS</div>
                        <div class="col-md-8">{{ optional($attendance->student)->nis ?? '-' }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Kelas</div>
                        <div class="col-md-8">{{ optional(optional($attendance->student)->schoolClass)->name ?? '-' }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Tanggal Absensi</div>
                        <div class="col-md-8">{{ \Carbon\Carbon::parse($attendance->attendance_date)->isoFormat('D MMMM YYYY') }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Jam Masuk Sekolah</div>
                        <div class="col-md-8">
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-clock me-1"></i>{{ $attendance->jam_masuk }} WIB
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Jam Datang Siswa</div>
                        <div class="col-md-8">
                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($attendance->late_time ?? $attendance->check_in)->format('H:i') }} WIB
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Total Terlambat</div>
                        <div class="col-md-8">
                            @if ($attendance->total_terlambat_formatted !== '-')
                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-clock-history me-1"></i>{{ $attendance->total_terlambat_formatted }}
                                </span>
                            @else
                                -
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Alasan Terlambat</div>
                        <div class="col-md-8">{{ $attendance->late_note ?: ($attendance->emergency_reason ?: ($attendance->emergency_note ?: '-')) }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Status Absensi</div>
                        <div class="col-md-8">
                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-exclamation-circle me-1"></i>Hadir Terlambat
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Operator Pencatat</div>
                        <div class="col-md-8">
                            <span class="badge bg-secondary text-white">
                                <i class="bi bi-person me-1"></i>{{ optional($attendance->operator)->name ?? ($attendance->is_emergency ? 'Operator Lapangan' : 'System (Scan QR)') }}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Sumber Presensi</div>
                        <div class="col-md-8">
                            @if($attendance->is_emergency)
                                <span class="badge bg-warning text-dark border border-warning">
                                    <i class="bi bi-circle-fill me-1 small text-warning"></i>🟡 Hadir Manual
                                </span>
                            @else
                                <span class="badge bg-success text-white border border-success">
                                    <i class="bi bi-circle-fill me-1 small text-light"></i>🟢 Scan QR
                                </span>
                            @endif
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-start">
                        <a href="{{ route('operator.terlambat.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i> Kembali
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>

@endsection
