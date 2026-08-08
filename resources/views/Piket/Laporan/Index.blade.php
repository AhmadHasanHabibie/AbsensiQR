@extends('Layouts.LayoutGuruPiket')

@section('title', 'Laporan Absensi Sekolah')

@section('content')

<div class="container-fluid py-2">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Laporan Absensi Sekolah</h3>
            <p class="text-muted mb-0">Rekapitulasi dan laporan kehadiran siswa seluruh kelas.</p>
        </div>
        <div class="text-end d-none d-sm-block">
            <div class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($targetDate)->isoFormat('D MMMM YYYY') }}</div>
            <div id="realtime-clock" class="text-muted small">{{ \Carbon\Carbon::now()->format('H:i:s') }} WIB</div>
        </div>
    </div>

    {{-- Filter & Export Card --}}
    <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
        <div class="card-header bg-white py-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-funnel me-2 text-primary"></i>Filter Periode & Ekspor Laporan</h5>
            <div class="dropdown">
                <button class="btn btn-success dropdown-toggle fw-bold px-4" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-download me-1"></i> Ekspor Laporan
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                    <li>
                        <a class="dropdown-item py-2" href="{{ route('piket.laporan.pdf', request()->all()) }}">
                            <i class="bi bi-file-earmark-pdf text-danger me-2 fs-5"></i> Unduh PDF (Laporan Harian)
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2" href="{{ route('piket.laporan.excel', request()->all()) }}">
                            <i class="bi bi-file-earmark-excel text-success me-2 fs-5"></i> Unduh Excel (Rekap Spreadsheet)
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card-body p-4">
            <form method="GET" action="{{ route('piket.laporan.index') }}">
                <div class="row g-3">
                    <div class="col-12 col-sm-6 col-md-3">
                        <label class="form-label fw-semibold small text-muted">Periode Waktu</label>
                        <select name="period" id="periodSelect" class="form-select">
                            <option value="day" {{ request('period', 'day') === 'day' ? 'selected' : '' }}>Harian</option>
                            <option value="month" {{ request('period') === 'month' ? 'selected' : '' }}>Bulanan</option>
                            <option value="quarter" {{ request('period') === 'quarter' ? 'selected' : '' }}>Triwulan</option>
                            <option value="semester" {{ request('period') === 'semester' ? 'selected' : '' }}>Semester</option>
                            <option value="year" {{ request('period') === 'year' ? 'selected' : '' }}>Tahunan</option>
                        </select>
                    </div>

                    {{-- Tanggal (Day) --}}
                    <div class="col-12 col-sm-6 col-md-3 period-input period-day" style="{{ request('period', 'day') !== 'day' ? 'display:none;' : '' }}">
                        <label class="form-label fw-semibold small text-muted">Pilih Tanggal</label>
                        <input type="date" name="date" class="form-control" value="{{ request('date', now()->toDateString()) }}">
                    </div>

                    {{-- Bulan & Tahun --}}
                    <div class="col-12 col-sm-6 col-md-4 period-input period-month" style="{{ request('period') !== 'month' ? 'display:none;' : '' }}">
                        <label class="form-label fw-semibold small text-muted">Bulan & Tahun</label>
                        <div class="d-flex gap-2">
                            <select name="month" class="form-select">
                                @foreach (range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ request('month', now()->month) == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::createFromDate(now()->year, $m, 1)->translatedFormat('F') }}</option>
                                @endforeach
                            </select>
                            <select name="year" class="form-select">
                                @foreach (range(now()->year - 4, now()->year + 1) as $y)
                                    <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Filter Kelas --}}
                    <div class="col-12 col-sm-6 col-md-3">
                        <label class="form-label fw-semibold small text-muted">Filter Kelas</label>
                        <select name="class_id" class="form-select">
                            <option value="">Seluruh Sekolah</option>
                            @foreach ($classList as $cls)
                                <option value="{{ $cls->id }}" {{ request('class_id') == $cls->id ? 'selected' : '' }}>{{ $cls->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Actions --}}
                    <div class="col-12 col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary px-4 w-100">
                            <i class="bi bi-filter me-1"></i> Tampilkan
                        </button>
                        <a href="{{ route('piket.laporan.index') }}" class="btn btn-outline-secondary px-3">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Ringkasan Statistik Absensi Card Grid --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 text-center h-100">
                <i class="bi bi-check-circle-fill text-success fs-2"></i>
                <small class="text-muted d-block mt-1">Hadir</small>
                <h3 class="fw-bold text-success mb-0">{{ $hadirCount }}</h3>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 text-center h-100">
                <i class="bi bi-alarm-fill text-secondary fs-2"></i>
                <small class="text-muted d-block mt-1">Terlambat</small>
                <h3 class="fw-bold text-secondary mb-0">{{ $terlambatCount }}</h3>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 text-center h-100">
                <i class="bi bi-envelope-fill text-warning fs-2"></i>
                <small class="text-muted d-block mt-1">Izin</small>
                <h3 class="fw-bold text-warning mb-0">{{ $izinCount }}</h3>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 text-center h-100">
                <i class="bi bi-heart-pulse-fill text-info fs-2"></i>
                <small class="text-muted d-block mt-1">Sakit</small>
                <h3 class="fw-bold text-info mb-0">{{ $sakitCount }}</h3>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 text-center h-100">
                <i class="bi bi-x-circle-fill text-danger fs-2"></i>
                <small class="text-muted d-block mt-1">Alpa</small>
                <h3 class="fw-bold text-danger mb-0">{{ $alpaCount }}</h3>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 text-center h-100">
                <i class="bi bi-pie-chart-fill text-primary fs-2"></i>
                <small class="text-muted d-block mt-1">Persentase Kehadiran</small>
                <h3 class="fw-bold text-primary mb-0">{{ $overallPercentage }}%</h3>
            </div>
        </div>
    </div>

    {{-- Main Tabel Rekap Kelas --}}
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
        <div class="card-header bg-primary text-white p-3 d-flex align-items-center justify-content-between">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-file-earmark-text me-2"></i> Rekapitulasi Laporan Absensi Per Kelas ({{ $dateLabel }})
            </h5>
            <span class="badge bg-light text-primary fw-bold px-3 py-2">
                {{ $classReports->count() }} Kelas
            </span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" width="4%">No</th>
                            <th>Kelas</th>
                            <th>Guru Wali Kelas</th>
                            <th class="text-center">Hadir</th>
                            <th class="text-center">Terlambat</th>
                            <th class="text-center">Izin</th>
                            <th class="text-center">Sakit</th>
                            <th class="text-center">Alpa</th>
                            <th>Status Konfirmasi</th>
                            <th>Tanggal</th>
                            <th class="pe-4 text-center" width="8%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($classReports as $item)
                            <tr>
                                <td class="ps-4 text-muted fw-semibold">{{ $loop->iteration }}</td>
                                <td><strong class="text-dark fs-6">{{ $item->class_name }}</strong></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width:28px;height:28px;background:linear-gradient(135deg,#0284c7,#38bdf8);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:12px;font-weight:600;">
                                            {{ substr($item->teacher_name, 0, 1) }}
                                        </div>
                                        <span>{{ $item->teacher_name }}</span>
                                    </div>
                                </td>
                                <td class="text-center"><span class="badge bg-success px-2 py-1">{{ $item->hadir }}</span></td>
                                <td class="text-center"><span class="badge bg-secondary px-2 py-1">{{ $item->terlambat }}</span></td>
                                <td class="text-center"><span class="badge bg-warning text-dark px-2 py-1">{{ $item->izin }}</span></td>
                                <td class="text-center"><span class="badge bg-info px-2 py-1">{{ $item->sakit }}</span></td>
                                <td class="text-center"><span class="badge bg-danger px-2 py-1">{{ $item->alpa }}</span></td>
                                <td>
                                    <span class="badge bg-{{ $item->status_badge }} px-2 py-1">
                                        {{ $item->status_label }}
                                    </span>
                                </td>
                                <td><small class="text-muted">{{ \Carbon\Carbon::parse($targetDate)->isoFormat('D MMM YYYY') }}</small></td>
                                <td class="pe-4 text-center">
                                    <button type="button" class="btn btn-info btn-sm text-white px-3 btn-detail-report" data-id="{{ $item->class_id }}" data-name="{{ $item->class_name }}">
                                        <i class="bi bi-eye me-1"></i> Detail
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted py-5">
                                    <i class="bi bi-file-earmark-x fs-1 d-block mb-2 opacity-50"></i>
                                    <p class="mb-0">Belum ada data rekap laporan kelas.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- MODAL DETAIL LAPORAN KELAS READ-ONLY --}}
<div class="modal fade" id="reportDetailModal" tabindex="-1" aria-labelledby="reportDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white p-3 rounded-top-4">
                <h5 class="modal-title mb-0 fw-bold" id="reportDetailModalLabel">
                    <i class="bi bi-file-earmark-text me-2"></i> Detail Laporan Siswa Kelas
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="reportModalBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Memuat...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light p-3 rounded-bottom-4">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const clockEl = document.getElementById('realtime-clock');
        if (clockEl) {
            clockEl.textContent = `${hours}:${minutes}:${seconds} WIB`;
        }
    }
    setInterval(updateClock, 1000);

    // Toggle Filter Inputs
    const periodSelect = document.getElementById('periodSelect');
    const periodInputs = document.querySelectorAll('.period-input');
    function updatePeriodInputs() {
        periodInputs.forEach(el => el.style.display = 'none');
        document.querySelectorAll('.period-' + periodSelect.value).forEach(el => el.style.display = 'block');
    }
    periodSelect.addEventListener('change', updatePeriodInputs);

    // Modal Handling
    const reportModalEl = document.getElementById('reportDetailModal');
    const reportModal = new bootstrap.Modal(reportModalEl);
    const modalBody = document.getElementById('reportModalBody');

    document.querySelectorAll('.btn-detail-report').forEach(function (button) {
        button.addEventListener('click', function () {
            const classId = this.getAttribute('data-id');
            const className = this.getAttribute('data-name');
            const currentDate = "{{ request('date', now()->toDateString()) }}";

            modalBody.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Memuat...</span>
                    </div>
                    <p class="mt-2 text-muted">Mengambil data laporan kelas ${className}...</p>
                </div>
            `;

            reportModal.show();

            fetch(`{{ url('piket/laporan/class') }}/${classId}?date=${currentDate}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    modalBody.innerHTML = `<div class="alert alert-danger mb-0">Gagal mengambil data laporan.</div>`;
                    return;
                }

                let rowsHtml = '';
                if (data.students.length === 0) {
                    rowsHtml = `<tr><td colspan="7" class="text-center text-muted py-4">Belum ada siswa di kelas ini.</td></tr>`;
                } else {
                    data.students.forEach((std, idx) => {
                        rowsHtml += `
                            <tr>
                                <td class="text-muted fw-semibold">${idx + 1}</td>
                                <td><strong>${std.name}</strong></td>
                                <td><span class="badge bg-light text-dark border">${std.nis}</span></td>
                                <td><span class="badge bg-light text-muted border">${std.jam_masuk}</span></td>
                                <td><small class="text-muted">${std.jam_datang}</small></td>
                                <td><span class="badge bg-warning text-dark">${std.total_late}x</span></td>
                                <td><span class="badge bg-${std.badge_class}">${std.status_label}</span></td>
                            </tr>
                        `;
                    });
                }

                modalBody.innerHTML = `
                    <div class="p-3 bg-light rounded-3 mb-4 border">
                        <div class="row g-2">
                            <div class="col-sm-6">
                                <span class="text-muted small d-block">Nama Kelas</span>
                                <h5 class="fw-bold text-dark mb-0">${data.class_name}</h5>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted small d-block">Guru Wali Kelas</span>
                                <h5 class="fw-bold text-dark mb-0">${data.teacher_name}</h5>
                            </div>
                            <div class="col-sm-12 mt-2">
                                <span class="text-muted small d-block">Tanggal Laporan</span>
                                <span class="fw-semibold text-dark">${data.date}</span>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-people me-1"></i> Daftar Presensi Siswa Kelas</h6>
                    <div class="table-responsive border rounded-3">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Nama Siswa</th>
                                    <th>NIS</th>
                                    <th>Jam Masuk</th>
                                    <th>Jam Datang</th>
                                    <th>Total Terlambat</th>
                                    <th>Status Absensi</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${rowsHtml}
                            </tbody>
                        </table>
                    </div>
                `;
            })
            .catch(() => {
                modalBody.innerHTML = `<div class="alert alert-danger mb-0">Terjadi kesalahan koneksi server.</div>`;
            });
        });
    });
});
</script>
@endpush
