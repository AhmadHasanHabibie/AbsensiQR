@extends('Layouts.LayoutGuru')

@section('title', 'Rekap Absensi')

@section('content')

<div class="container-fluid">

    {{-- Alert --}}
    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            <i class="bi bi-check-circle-fill me-2"></i>

            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>

        </div>

    @endif

    @if(session('error'))

        <div class="alert alert-danger alert-dismissible fade show">

            <i class="bi bi-x-circle-fill me-2"></i>

            {{ session('error') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="fw-bold text-primary mb-1">

                Rekap Absensi Hari Ini

            </h3>

            <p class="text-muted mb-0">

                Kelas :
                <strong>{{ $class->name }}</strong>

            </p>

        </div>

        <div class="text-end">

            <span class="badge bg-primary fs-6">

                {{ now()->translatedFormat('d F Y') }}

            </span>

            <br>

            @if($isLocked)

                <span class="badge bg-success mt-2">

                    <i class="bi bi-lock-fill me-1"></i>

                    Absensi Sudah Dikonfirmasi

                </span>

            @else

                <span class="badge bg-warning text-dark mt-2">

                    <i class="bi bi-unlock-fill me-1"></i>

                    Menunggu Konfirmasi

                </span>

            @endif

        </div>

    </div>


    {{-- Alert Warning Absensi Darurat --}}
    @if(isset($hadirManual) && $hadirManual > 0)
        <div class="alert alert-warning border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center gap-3 p-3">
            <div style="width:40px;height:40px;background:rgba(255,193,7,0.25);border-radius:12px;display:flex;align-items:center;justify-content:center;color:#856404;font-size:22px;flex-shrink:0;">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-1 text-dark">Informasi Absensi Darurat ({{ $hadirManual }} Siswa)</h6>
                <p class="mb-0 text-dark small"><strong>Hadir Manual</strong> menandakan siswa diinput melalui Absensi Darurat oleh Operator dan menunggu validasi Guru. Jika Anda tidak mengubah statusnya, sistem akan otomatis menyetujuinya menjadi <strong>Hadir</strong> resmi saat dikonfirmasi.</p>
            </div>
        </div>
    @endif

    {{-- Statistik --}}
    <div class="row g-3 mb-4">

        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-3">
                    <i class="bi bi-check-circle-fill text-success fs-3"></i>
                    <h6 class="mt-2 mb-1 small text-muted">Hadir</h6>
                    <h3 class="fw-bold text-success mb-0">{{ $hadir }}</h3>
                </div>
            </div>
        </div>

        @if(isset($hadirManual) && $hadirManual > 0)
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100 border-warning">
                <div class="card-body text-center p-3">
                    <i class="bi bi-hand-index-thumb-fill text-warning fs-3"></i>
                    <h6 class="mt-2 mb-1 small text-dark fw-bold">Hadir Manual</h6>
                    <h3 class="fw-bold text-warning mb-0">{{ $hadirManual }}</h3>
                </div>
            </div>
        </div>
        @endif

        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-3">
                    <i class="bi bi-envelope-fill text-warning fs-3"></i>
                    <h6 class="mt-2 mb-1 small text-muted">Izin</h6>
                    <h3 class="fw-bold text-warning mb-0">{{ $izin }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-3">
                    <i class="bi bi-heart-pulse-fill text-info fs-3"></i>
                    <h6 class="mt-2 mb-1 small text-muted">Sakit</h6>
                    <h3 class="fw-bold text-info mb-0">{{ $sakit }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-3">
                    <i class="bi bi-x-circle-fill text-danger fs-3"></i>
                    <h6 class="mt-2 mb-1 small text-muted">Alpa</h6>
                    <h3 class="fw-bold text-danger mb-0">{{ $alpa }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-3">
                    <i class="bi bi-person-dash-fill text-secondary fs-3"></i>
                    <h6 class="mt-2 mb-1 small text-muted">Belum Hadir</h6>
                    <h3 class="fw-bold text-secondary mb-0">{{ $belumHadir }}</h3>
                </div>
            </div>
        </div>

    </div>


    {{-- Tabel --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-primary text-white">

            <h5 class="mb-0">
                <i class="bi bi-table me-2"></i> Daftar Kehadiran Siswa
            </h5>

        </div>

        <form
            id="attendanceForm"
            action="{{ route('guru.attendance.confirm') }}"
            method="POST">

            @csrf

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>

                            <th width="60" class="text-center">No</th>
                            <th>Nama Siswa</th>
                            <th width="140">NIS</th>
                            <th width="320" class="text-center">Status Kehadiran</th>

                        </tr>

                    </thead>

                    <tbody>
                        @forelse($students as $student)

                            <tr>

                                <td class="text-center">
                                    {{ $loop->iteration }}
                                </td>

                                <td>
                                    <strong>{{ $student->name }}</strong>
                                </td>

                                <td>
                                    <span class="badge bg-light text-dark border">{{ $student->nis }}</span>
                                </td>

                                <td class="text-center">

                                    @switch($student->attendance_status)

                                        @case('hadir')

                                            <span class="badge bg-success px-3 py-2">
                                                <i class="bi bi-check-circle-fill me-1"></i> Hadir
                                            </span>

                                            @break

                                        @case('hadir_manual')

                                            <div class="d-inline-flex align-items-center gap-2 flex-wrap justify-content-center">
                                                <span class="badge bg-warning text-dark px-3 py-2">
                                                    <i class="bi bi-hand-index-thumb me-1"></i> Hadir Manual
                                                </span>

                                                @if($student->attendance && $student->attendance->is_emergency)
                                                    <button type="button" class="btn btn-sm btn-outline-dark px-2 py-1 fs-7" data-bs-toggle="modal" data-bs-target="#emergencyModal{{ $student->id }}">
                                                        <i class="bi bi-info-circle me-1"></i> Sumber Data
                                                    </button>
                                                @endif

                                                @if(!$isLocked)
                                                    <select
                                                        name="status[{{ $student->id }}]"
                                                        class="form-select form-select-sm"
                                                        style="width:130px">
                                                        <option value="hadir" selected>Setujui (Hadir)</option>
                                                        <option value="izin">Izin</option>
                                                        <option value="sakit">Sakit</option>
                                                        <option value="alpa">Alpa</option>
                                                        <option value="terlambat">Terlambat</option>
                                                    </select>
                                                @endif
                                            </div>

                                            {{-- Modal Sumber Data Absensi Darurat --}}
                                            @if($student->attendance && $student->attendance->is_emergency)
                                                <div class="modal fade" id="emergencyModal{{ $student->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content border-0 shadow-lg rounded-4 text-start">
                                                            <div class="modal-header bg-warning text-dark p-3 rounded-top-4">
                                                                <h5 class="modal-title mb-0 fw-bold">
                                                                    <i class="bi bi-shield-exclamation me-2"></i> Sumber Data Absensi Darurat
                                                                </h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body p-4">
                                                                <div class="p-3 bg-light rounded-3 border mb-3">
                                                                    <div class="mb-2"><strong>Nama Siswa:</strong> {{ $student->name }}</div>
                                                                    <div class="mb-2"><strong>NIS:</strong> {{ $student->nis ?? '-' }}</div>
                                                                    <div class="mb-2"><strong>Diinput Oleh:</strong> <span class="text-primary fw-bold">{{ optional($student->attendance->operator)->name ?? 'Operator Lapangan' }}</span></div>
                                                                    <div class="mb-2"><strong>Tanggal Input:</strong> {{ optional($student->attendance->attendance_date)->isoFormat('D MMMM YYYY') }}</div>
                                                                    <div class="mb-2"><strong>Jam Datang:</strong> {{ $student->attendance->check_in ? \Carbon\Carbon::parse($student->attendance->check_in)->format('H:i') . ' WIB' : '-' }}</div>
                                                                    <div class="mb-2"><strong>Alasan Darurat:</strong> <span class="badge bg-warning text-dark fs-6">{{ $student->attendance->emergency_reason }}</span></div>
                                                                    <div><strong>Keterangan:</strong> {{ $student->attendance->emergency_note ?? 'Tidak ada keterangan tambahan.' }}</div>
                                                                </div>

                                                                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-clock-history me-1"></i> Timeline Histori Validasi:</h6>
                                                                <div class="ps-2 border-start border-2 border-warning ms-2">
                                                                    <div class="mb-3 ps-3">
                                                                        <div class="text-muted small">{{ $student->attendance->emergencyAudit && $student->attendance->emergencyAudit->input_at ? $student->attendance->emergencyAudit->input_at->format('H:i') . ' WIB' : ($student->attendance->check_in ? \Carbon\Carbon::parse($student->attendance->check_in)->format('H:i') . ' WIB' : '-') }}</div>
                                                                        <div class="fw-bold text-dark"><i class="bi bi-person-workspace me-1"></i>{{ optional($student->attendance->operator)->name ?? 'Operator Lapangan' }}</div>
                                                                        <div class="text-muted small">Membuat Absensi Darurat (Alasan: {{ $student->attendance->emergency_reason }})</div>
                                                                    </div>
                                                                    @if($isLocked && $student->attendance->emergencyAudit && $student->attendance->emergencyAudit->validated_at)
                                                                        <div class="mb-3 ps-3">
                                                                            <div class="text-muted small">{{ $student->attendance->emergencyAudit->validated_at->format('H:i') . ' WIB' }}</div>
                                                                            <div class="fw-bold text-success"><i class="bi bi-person-check-fill me-1"></i>{{ optional($student->attendance->emergencyAudit->teacher)->name ?? 'Guru Wali Kelas' }}</div>
                                                                            <div class="text-muted small">Mengonfirmasi Kehadiran (Validasi: {{ $student->attendance->emergencyAudit->validation_type === 'automatic' ? 'Disetujui Otomatis' : 'Diubah Manual' }})</div>
                                                                        </div>
                                                                    @endif
                                                                    <div class="ps-3">
                                                                        <span class="text-muted small d-block mb-1">Status Final:</span>
                                                                        <span class="badge bg-{{ $student->attendance->status === 'hadir' ? 'success' : ($student->attendance->status === 'terlambat' ? 'secondary' : ($student->attendance->status === 'izin' ? 'warning text-dark' : ($student->attendance->status === 'sakit' ? 'info' : 'danger'))) }} fs-6">
                                                                            {{ ucfirst($student->attendance->status) }}
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer bg-light p-3 rounded-bottom-4">
                                                                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            @break

                                        @case('izin')

                                            <span class="badge bg-warning text-dark px-3 py-2">
                                                Izin
                                            </span>

                                            @break

                                        @case('sakit')

                                            <span class="badge bg-info px-3 py-2">
                                                Sakit
                                            </span>

                                            @break

                                        @case('alpa')

                                            <span class="badge bg-danger px-3 py-2">
                                                Alpa
                                            </span>

                                            @break

                                        @default

                                            <div class="d-flex justify-content-center align-items-center gap-2">

                                                <span class="badge bg-secondary">
                                                    Belum Hadir
                                                </span>

                                                <select
                                                    name="status[{{ $student->id }}]"
                                                    class="form-select form-select-sm"
                                                    style="width:120px"
                                                    {{ $isLocked ? 'disabled' : '' }}>

                                                    <option value="alpa" selected>
                                                        Alpa
                                                    </option>

                                                    <option value="izin">
                                                        Izin
                                                    </option>

                                                    <option value="sakit">
                                                        Sakit
                                                    </option>

                                                </select>

                                            </div>
                                    @endswitch

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="4"
                                    class="text-center py-5">

                                    <i class="bi bi-people display-4 text-muted"></i>

                                    <h5 class="mt-3">

                                        Tidak ada siswa.

                                    </h5>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            <div class="card-footer bg-white d-flex justify-content-between align-items-center">

                <small class="text-muted">

                    Total Siswa :
                    <strong>{{ $students->count() }}</strong>

                </small>

                @if($isLocked)

                    <button
                        type="button"
                        class="btn btn-secondary px-4"
                        disabled>

                        <i class="bi bi-lock-fill me-2"></i>

                        Absensi Sudah Dikonfirmasi

                    </button>

                @else

                    <button
                        type="button"
                        class="btn btn-success px-4"
                        data-bs-toggle="modal"
                        data-bs-target="#confirmModal"
                        {{ $students->count() == 0 ? 'disabled' : '' }}>

                        <i class="bi bi-check-circle me-2"></i>

                        Konfirmasi Absensi Hari Ini

                    </button>

                @endif

            </div>

        </form>

    </div>

</div>

@if(!$isLocked)

<div
    class="modal fade"
    id="confirmModal"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">

                    Konfirmasi Absensi

                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                Apakah Anda yakin ingin mengonfirmasi absensi hari ini?

                <br><br>

                <strong class="text-danger">

                    Setelah dikonfirmasi, absensi tidak dapat diubah lagi.

                </strong>

            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">

                    Batal

                </button>

                <button
                    type="submit"
                    form="attendanceForm"
                    class="btn btn-success">

                    <i class="bi bi-check-circle me-1"></i>

                    Ya, Konfirmasi

                </button>

            </div>

        </div>

    </div>

</div>

@endif

@endsection