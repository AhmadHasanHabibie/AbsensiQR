@extends('Layouts.LayoutOperator')

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
                    <p class="text-muted small mb-0">Input dan pencatatan keterlambatan hanya aktif pada hari belajar.</p>
                </div>
                <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">
                    <i class="bi bi-info-circle me-1"></i> {{ \Carbon\Carbon::now('Asia/Jakarta')->isoFormat('dddd, D MMMM YYYY') }}
                </span>
            </div>
            <div class="modal-footer bg-light rounded-bottom-4 p-3 d-flex justify-content-center">
                <a href="{{ route('operator.dashboard') }}" class="btn btn-danger px-5 fw-semibold rounded-pill">
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
            window.location.href = '{{ route("operator.dashboard") }}';
        });
    });
</script>
@endif

<div class="container-fluid">

    {{-- Alerts --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($isScanOpen)
        <div class="alert alert-info border-0 shadow-sm d-flex align-items-center gap-2 mb-4 rounded-4">
            <i class="bi bi-clock-history fs-5 text-info"></i>
            <span>Scan QR Absensi sedang berlangsung otomatis (06:00 – 06:30 WIB). Input Keterlambatan akan aktif otomatis pada pukul 06:31:00 WIB.</span>
        </div>
    @endif

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Data Terlambat</h3>
            <p class="text-muted mb-0">Pencatatan siswa yang hadir terlambat oleh Operator.</p>
        </div>

        <div>
            @if ($isScanOpen)
                <button class="btn btn-primary d-flex align-items-center gap-2 disabled" title="Scan QR sedang berlangsung. Keterlambatan dapat diinput setelah pukul 06:31 WIB.">
                    <i class="bi bi-plus-circle fs-5"></i>
                    Tambah Data Terlambat
                </button>
            @else
                <a href="{{ route('operator.terlambat.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
                    <i class="bi bi-plus-circle fs-5"></i>
                    Tambah Data Terlambat
                </a>
            @endif
        </div>
    </div>

    @php
        $excludedStudentIds = \App\Models\Attendance::whereDate('attendance_date', today())
            ->where(function ($query) {
                $query->whereIn('status', ['hadir', 'izin', 'sakit'])
                    ->orWhere('is_late', true);
            })
            ->pluck('student_id');

        $unscannedCount = \App\Models\User::where('role', 'student')
            ->where('status', true)
            ->whereNotIn('id', $excludedStudentIds)
            ->count();
    @endphp

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:48px;height:48px;background:linear-gradient(135deg,#6c757d,#495057);border-radius:14px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:22px;">
                        <i class="bi bi-person-exclamation"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Belum Scan Hari Ini</div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $unscannedCount }} <span class="fs-6 text-muted fw-normal">Siswa</span></h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:48px;height:48px;background:linear-gradient(135deg,#ffc107,#ffb300);border-radius:14px;display:flex;align-items:center;justify-content:center;color:#000;font-size:22px;">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Sudah Diproses Terlambat</div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $totalTerlambat }} <span class="fs-6 text-muted fw-normal">Siswa</span></h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:48px;height:48px;background:linear-gradient(135deg,#0d6efd,#0dcaf0);border-radius:14px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:22px;">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Tanggal Hari Ini</div>
                        <h6 class="fw-bold mb-0 text-dark">{{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY') }}</h6>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:48px;height:48px;background:linear-gradient(135deg,{{ $isScanOpen ? '#198754,#20c997' : '#dc3545,#c82333' }});border-radius:14px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:22px;">
                        <i class="bi {{ $isScanOpen ? 'bi-qr-code-scan' : 'bi-slash-circle' }}"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Status Scan QR</div>
                        <h6 class="mb-0">
                            <span class="badge {{ $isScanOpen ? 'bg-success' : 'bg-danger' }}">
                                {{ $isScanOpen ? 'SCAN DIBUKA' : 'SCAN DITUTUP' }}
                            </span>
                        </h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $sortedAttendances = $lateAttendances->sortBy(function($a) {
            $className = optional(optional($a->student)->schoolClass)->name ?? 'ZZZ';
            $studentName = optional($a->student)->name ?? 'ZZZ';
            return sprintf('%s_%s', $className, $studentName);
        });

        $allClasses = \App\Models\SchoolClass::where('status', true)->get()->sortBy(function($c) {
            $name = trim($c->name);
            if (preg_match('/^(XII|XI|X|12|11|10|[0-9]+)\b/i', $name, $matches)) {
                $val = strtoupper($matches[1]);
                $levelOrder = ['X' => 10, '10' => 10, 'XI' => 11, '11' => 11, 'XII' => 12, '12' => 12];
                $rank = $levelOrder[$val] ?? (is_numeric($val) ? (int)$val : 99);
            } else {
                $rank = 99;
            }
            return sprintf('%03d_%s', $rank, $name);
        });
    @endphp

    {{-- Empty State All Done Banner --}}
    @if ($unscannedCount === 0 && !$isScanOpen)
        <div class="alert alert-success border-0 shadow-sm rounded-4 p-4 text-center mb-4">
            <i class="bi bi-patch-check-fill fs-1 text-success d-block mb-2"></i>
            <h5 class="fw-bold text-dark mb-1">Seluruh Siswa yang Belum Scan QR Telah Diproses</h5>
            <p class="text-muted mb-2">Tidak ada lagi siswa yang alpa / belum scan untuk hari ini.</p>
            <span class="badge bg-success fs-6 px-3 py-2">
                <i class="bi bi-check-circle-fill me-1"></i> Semua Selesai
            </span>
        </div>
    @endif

    {{-- Data Table Card --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-primary text-white p-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-alarm me-2"></i> Daftar Keterlambatan Hari Ini
            </h5>
            <span class="badge bg-light text-primary fw-bold fs-6">
                Total: {{ $sortedAttendances->count() }} Data
            </span>
        </div>

        <div class="card-body p-3 bg-light border-bottom">
            <div class="row g-3">
                <div class="col-12 col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="search-input" class="form-control border-start-0" placeholder="Cari Nama Siswa, NIS, atau Kelas...">
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <select id="filter-class" class="form-select">
                        <option value="">Semua Kelas</option>
                        @foreach ($allClasses as $c)
                            <option value="{{ strtolower($c->name) }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <input type="date" id="filter-date" class="form-control" value="{{ $dateStr ?? now()->toDateString() }}" title="Filter Tanggal" onchange="window.location.href='{{ route('operator.terlambat.index') }}?date=' + this.value">
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0" id="late-table">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" width="4%">No</th>
                            <th>Nama Siswa</th>
                            <th>NIS</th>
                            <th>Kelas</th>
                            <th>Jam Masuk</th>
                            <th>Jam Datang</th>
                            <th>Total Terlambat</th>
                            <th>Alasan</th>
                            <th>Status</th>
                            <th>Operator</th>
                            <th>Sumber Presensi</th>
                            <th class="pe-4 text-center" width="8%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="late-table-body">
                        @forelse ($sortedAttendances as $attendance)
                            @php
                                $reasonText = $attendance->late_note ?: ($attendance->emergency_reason ?: ($attendance->emergency_note ?: '-'));
                                $operatorName = optional($attendance->operator)->name ?? ($attendance->is_emergency ? 'Operator Lapangan' : 'System (Scan QR)');
                            @endphp
                            <tr class="late-row" data-name="{{ strtolower(optional($attendance->student)->name ?? '') }}" data-nis="{{ strtolower(optional($attendance->student)->nis ?? '') }}" data-class="{{ strtolower(optional(optional($attendance->student)->schoolClass)->name ?? '') }}" data-status="terlambat sudah diproses">
                                <td class="ps-4 fw-semibold text-muted row-no">{{ $loop->iteration }}</td>
                                <td class="fw-bold text-dark">{{ optional($attendance->student)->name }}</td>
                                <td><span class="badge bg-light text-dark border">{{ optional($attendance->student)->nis ?? '-' }}</span></td>
                                <td class="fw-semibold text-primary">{{ optional(optional($attendance->student)->schoolClass)->name ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <i class="bi bi-clock me-1"></i>{{ $attendance->jam_masuk }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($attendance->late_time ?? $attendance->check_in)->format('H:i') }}
                                    </span>
                                </td>
                                <td>
                                    @if ($attendance->total_terlambat_formatted !== '-')
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-clock-history me-1"></i>{{ $attendance->total_terlambat_formatted }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $reasonText }}</td>
                                <td>
                                    <span class="badge bg-warning text-dark me-1">
                                        <i class="bi bi-clock-history me-1"></i>Terlambat
                                    </span>
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>Sudah Diproses
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary text-white">
                                        <i class="bi bi-person me-1"></i>{{ $operatorName }}
                                    </span>
                                </td>
                                <td>
                                    @if($attendance->is_emergency)
                                        <span class="badge bg-warning text-dark border border-warning">
                                            <i class="bi bi-circle-fill me-1 small text-warning"></i>🟡 Hadir Manual
                                        </span>
                                    @else
                                        <span class="badge bg-success text-white border border-success">
                                            <i class="bi bi-circle-fill me-1 small text-light"></i>🟢 Scan QR
                                        </span>
                                    @endif
                                </td>
                                <td class="pe-4 text-center">
                                    <a href="{{ route('operator.terlambat.show', $attendance->id) }}" class="btn btn-info btn-sm text-white" title="Lihat Detail">
                                        <i class="bi bi-eye me-1"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr id="empty-row">
                                <td colspan="12" class="text-center text-muted py-5">
                                    <i class="bi bi-alarm fs-1 d-block mb-2"></i>
                                    <p class="mb-0">Belum ada siswa yang ditandai terlambat hari ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search-input');
    const filterClass = document.getElementById('filter-class');
    const filterStatus = document.getElementById('filter-status');
    const tableRows = document.querySelectorAll('.late-row');

    function filterTable() {
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const selectedClass = filterClass ? filterClass.value.toLowerCase().trim() : '';
        const selectedStatus = filterStatus ? filterStatus.value.toLowerCase().trim() : '';

        let visibleCount = 0;

        tableRows.forEach(row => {
            const name = row.dataset.name || '';
            const nis = row.dataset.nis || '';
            const className = row.dataset.class || '';
            const status = row.dataset.status || '';

            const matchQuery = !query || name.includes(query) || nis.includes(query) || className.includes(query);
            const matchClass = !selectedClass || className === selectedClass;
            const matchStatus = !selectedStatus || status.includes(selectedStatus);

            if (matchQuery && matchClass && matchStatus) {
                row.style.display = '';
                visibleCount++;
                const noCell = row.querySelector('.row-no');
                if (noCell) noCell.textContent = visibleCount;
            } else {
                row.style.display = 'none';
            }
        });
    }

    if (searchInput) searchInput.addEventListener('input', filterTable);
    if (filterClass) filterClass.addEventListener('change', filterTable);
    if (filterStatus) filterStatus.addEventListener('change', filterTable);
});
</script>
@endpush
