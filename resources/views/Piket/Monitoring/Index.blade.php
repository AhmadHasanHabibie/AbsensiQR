@extends('Layouts.LayoutGuruPiket')

@section('title', 'Monitoring Absensi Harian')

@section('content')

<div class="container-fluid py-2">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Monitoring Absensi</h3>
            <p class="text-muted mb-0">Tanggal Monitoring: <strong class="text-dark">{{ $dateLabel }}</strong></p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="dropdown">
                <button class="btn btn-success dropdown-toggle fw-bold px-4" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-download me-1"></i> Ekspor Laporan
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                    <li>
                        <a class="dropdown-item py-2" href="{{ route('piket.monitoring.pdf', request()->all()) }}">
                            <i class="bi bi-file-earmark-pdf text-danger me-2 fs-5"></i> Unduh PDF (Monitoring Harian)
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2" href="{{ route('piket.monitoring.excel', request()->all()) }}">
                            <i class="bi bi-file-earmark-excel text-success me-2 fs-5"></i> Unduh Excel (Rekap Spreadsheet)
                        </a>
                    </li>
                </ul>
            </div>
            <div class="text-end d-none d-sm-block">
                <div class="fw-semibold text-dark">{{ $dateLabel }}</div>
                @if ($isToday)
                    <div id="realtime-clock" class="text-muted small">{{ \Carbon\Carbon::now()->format('H:i:s') }} WIB</div>
                @else
                    <span class="badge bg-secondary">Arsip Data</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Ringkasan Statistics Card --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:48px;height:48px;background:linear-gradient(135deg,#0d6efd,#0dcaf0);border-radius:14px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:22px;">
                        <i class="bi bi-building"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Kelas {{ $isToday ? 'Hari Ini' : 'Tanggal Ini' }}</div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $totalClasses }} <span class="fs-6 text-muted fw-normal">Kelas</span></h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:48px;height:48px;background:linear-gradient(135deg,#198754,#2ecc71);border-radius:14px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:22px;">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Sudah Konfirmasi</div>
                        <h4 class="fw-bold mb-0 text-success">{{ $confirmedCount }} <span class="fs-6 text-muted fw-normal">Kelas</span></h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:48px;height:48px;background:linear-gradient(135deg,#ffc107,#fbbf24);border-radius:14px;display:flex;align-items:center;justify-content:center;color:#000;font-size:22px;">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Belum Konfirmasi</div>
                        <h4 class="fw-bold mb-0 text-warning">{{ $unconfirmedCount }} <span class="fs-6 text-muted fw-normal">Kelas</span></h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:48px;height:48px;background:linear-gradient(135deg,#0284c7,#38bdf8);border-radius:14px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:22px;">
                        <i class="bi bi-pie-chart-fill"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Persentase Konfirmasi</div>
                        <h4 class="fw-bold mb-0 text-info">{{ $percentage }}%</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter, Search, & Sorting Toolbar Card --}}
    <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-funnel me-2 text-primary"></i>Filter Tanggal & Pengurutan</h5>
        </div>
        <div class="card-body p-4">
            <form method="GET" action="{{ route('piket.monitoring.index') }}" class="row g-3">

                {{-- Tanggal Monitoring & Tombol Cepat Hari Ini --}}
                <div class="col-12 col-sm-6 col-md-4">
                    <label class="form-label fw-semibold small text-muted">Tanggal Monitoring</label>
                    <div class="d-flex gap-2 align-items-center">
                        <input type="date" name="date" class="form-control" value="{{ $dateStr }}">
                        <a href="{{ route('piket.monitoring.index') }}" class="btn btn-outline-primary btn-sm px-3 fw-semibold flex-shrink-0" title="Kembali ke Hari Ini">
                            <i class="bi bi-calendar-check me-1"></i> Hari Ini
                        </a>
                    </div>
                </div>

                {{-- Kelas --}}
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label fw-semibold small text-muted">Filter Kelas</label>
                    <select name="class_id" class="form-select">
                        <option value="">Semua Kelas</option>
                        @foreach ($classList as $cls)
                            <option value="{{ $cls->id }}" {{ request('class_id') == $cls->id ? 'selected' : '' }}>{{ $cls->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Status --}}
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label fw-semibold small text-muted">Status Konfirmasi</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="sudah_konfirmasi" {{ request('status') === 'sudah_konfirmasi' ? 'selected' : '' }}>Sudah Konfirmasi</option>
                        <option value="menunggu_konfirmasi" {{ request('status') === 'menunggu_konfirmasi' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                    </select>
                </div>

                {{-- Search --}}
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label fw-semibold small text-muted">Cari Kelas / Guru</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Kata kunci..." value="{{ request('search') }}">
                    </div>
                </div>

                {{-- Sort By & Direction --}}
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label fw-semibold small text-muted">Urutkan Berdasarkan</label>
                    <select name="sort" class="form-select">
                        <option value="name" {{ request('sort', 'name') === 'name' ? 'selected' : '' }}>Nama Kelas</option>
                        <option value="teacher_name" {{ request('sort') === 'teacher_name' ? 'selected' : '' }}>Nama Guru Wali</option>
                        <option value="status" {{ request('sort') === 'status' ? 'selected' : '' }}>Status Konfirmasi</option>
                        <option value="locked_at" {{ request('sort') === 'locked_at' ? 'selected' : '' }}>Waktu Konfirmasi</option>
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label fw-semibold small text-muted">Arah Urutan</label>
                    <select name="direction" class="form-select">
                        <option value="asc" {{ request('direction', 'asc') === 'asc' ? 'selected' : '' }}>Ascending (A-Z / Lama)</option>
                        <option value="desc" {{ request('direction') === 'desc' ? 'selected' : '' }}>Descending (Z-A / Baru)</option>
                    </select>
                </div>

                {{-- Actions --}}
                <div class="col-12 col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-filter me-1"></i> Terapkan
                    </button>
                    <a href="{{ route('piket.monitoring.index') }}" class="btn btn-outline-secondary px-3">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </a>
                </div>

            </form>
        </div>
    </div>

    {{-- Main Table Card --}}
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
        <div class="card-header bg-primary text-white p-3 d-flex align-items-center justify-content-between">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-display me-2"></i> Daftar Konfirmasi Absensi Kelas
            </h5>
            <span class="badge bg-light text-primary fw-bold px-3 py-2">
                {{ $isToday ? 'Real-time Status' : 'Arsip Absensi' }}
            </span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" width="5%">No</th>
                            <th>Kelas</th>
                            <th>Guru Wali Kelas</th>
                            <th>Jumlah Siswa</th>
                            <th>Status Konfirmasi</th>
                            <th>Waktu Konfirmasi</th>
                            <th>Status Scan QR</th>
                            <th class="pe-4 text-center" width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($paginated as $item)
                            <tr>
                                <td class="ps-4 text-muted fw-semibold">
                                    {{ $loop->iteration + ($paginated->currentPage() - 1) * $paginated->perPage() }}
                                </td>
                                <td>
                                    <strong class="text-dark fs-6">{{ $item->name }}</strong>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width:32px;height:32px;background:linear-gradient(135deg,#0284c7,#38bdf8);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;font-weight:600;">
                                            {{ substr($item->teacher_name, 0, 1) }}
                                        </div>
                                        <span>{{ $item->teacher_name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1">
                                        <i class="bi bi-people me-1"></i>{{ $item->students_count }} Siswa
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $item->status_badge }} px-3 py-2 fs-6">
                                        @if ($item->status_key === 'sudah_konfirmasi')
                                            <i class="bi bi-check-circle me-1"></i>
                                        @else
                                            <i class="bi bi-clock me-1"></i>
                                        @endif
                                        {{ $item->status_label }}
                                    </span>
                                </td>
                                <td>
                                    @if ($item->locked_at)
                                        <span class="badge bg-light text-dark border">
                                            <i class="bi bi-clock me-1 text-primary"></i>{{ $item->locked_at->isoFormat('HH:mm:ss, D MMM YYYY') }} WIB
                                        </span>
                                    @else
                                        <span class="text-muted fw-bold">&mdash;</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $item->scan_badge }} px-2 py-1">
                                        {{ $item->scan_status }}
                                    </span>
                                </td>
                                <td class="pe-4 text-center">
                                    <div class="d-flex gap-1 justify-content-center flex-wrap">
                                        <button type="button" class="btn btn-info btn-sm text-white px-2 btn-detail" data-id="{{ $item->id }}" data-name="{{ $item->name }}">
                                            <i class="bi bi-eye me-1"></i> Detail
                                        </button>

                                        @if ($item->status_key === 'sudah_konfirmasi')
                                            <span class="badge bg-secondary px-2 py-2 fs-6">
                                                <i class="bi bi-check2-all me-1"></i> Selesai
                                            </span>
                                        @elseif ($isToday)
                                            <form action="{{ route('piket.monitoring.send-reminder') }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="class_id" value="{{ $item->id }}">
                                                <input type="hidden" name="date" value="{{ $dateStr }}">
                                                <button type="submit" class="btn btn-warning btn-sm px-2 text-dark" title="Kirim Pengingat Internal ke Wali Kelas">
                                                    <i class="bi bi-bell me-1"></i> Pengingat
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" class="btn btn-secondary btn-sm px-2 disabled" style="opacity: 0.65; cursor: not-allowed;" disabled title="Reminder hanya dapat dikirim pada tanggal absensi yang sedang berlangsung.">
                                                <i class="bi bi-bell-slash me-1"></i> Reminder Ditutup
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <i class="bi bi-file-earmark-x fs-1 d-block mb-2 opacity-50"></i>
                                    <p class="mb-0">Tidak ada data monitoring konfirmasi absensi kelas pada tanggal ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($paginated->hasPages())
                <div class="p-3 bg-light border-top d-flex justify-content-center">
                    {{ $paginated->links() }}
                </div>
            @endif
        </div>
    </div>

</div>

{{-- MODAL DETAIL ABSENSI KELAS --}}
<div class="modal fade" id="modalDetailKelas" tabindex="-1" aria-labelledby="modalDetailKelasLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white p-3 rounded-top-4">
                <h5 class="modal-title mb-0 fw-bold" id="modalDetailKelasLabel">
                    <i class="bi bi-display me-2"></i> Detail Monitoring Absensi Kelas
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="modalDetailBody">
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
    @if ($isToday)
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
    @endif

    // Modal Handling
    const detailModalEl = document.getElementById('modalDetailKelas');
    const detailModal = new bootstrap.Modal(detailModalEl);
    const modalBody = document.getElementById('modalDetailBody');

    document.querySelectorAll('.btn-detail').forEach(function (button) {
        button.addEventListener('click', function () {
            const classId = this.getAttribute('data-id');
            const className = this.getAttribute('data-name');
            const currentDate = "{{ $dateStr }}";

            modalBody.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Memuat...</span>
                    </div>
                    <p class="mt-2 text-muted">Mengambil data absensi kelas ${className}...</p>
                </div>
            `;

            detailModal.show();

            fetch(`{{ url('piket/monitoring') }}/${classId}?date=${currentDate}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    modalBody.innerHTML = `<div class="alert alert-danger mb-0">Gagal mengambil data detail absensi.</div>`;
                    return;
                }

                let studentsHtml = '';
                if (data.students.length === 0) {
                    studentsHtml = `<tr><td colspan="5" class="text-center text-muted py-4">Belum ada siswa di kelas ini.</td></tr>`;
                } else {
                    data.students.forEach((std, idx) => {
                        studentsHtml += `
                            <tr>
                                <td class="text-muted fw-semibold">${idx + 1}</td>
                                <td><span class="badge bg-light text-dark border">${std.nis}</span></td>
                                <td><strong>${std.name}</strong></td>
                                <td><small class="text-muted">${std.check_in}</small></td>
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
                            <div class="col-sm-6 mt-3">
                                <span class="text-muted small d-block">Tanggal Monitoring</span>
                                <span class="fw-semibold text-dark">${data.date_formatted}</span>
                            </div>
                            <div class="col-sm-6 mt-3">
                                <span class="text-muted small d-block">Status Konfirmasi</span>
                                <span class="badge bg-${data.status_badge} fs-6">${data.status_label}</span>
                                <span class="text-muted small d-block mt-1">Waktu: ${data.locked_at}</span>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-bar-chart-steps me-1"></i> Ringkasan Absensi Kelas</h6>
                    <div class="row g-2 text-center mb-4">
                        <div class="col-4 col-sm-2"><div class="p-2 rounded-3" style="background:#e9f7ef;"><small class="d-block text-muted">Hadir</small><strong class="text-success fs-5">${data.stats.hadir}</strong></div></div>
                        <div class="col-4 col-sm-2"><div class="p-2 rounded-3" style="background:#f8f9fa;border:1px solid #dee2e6;"><small class="d-block text-muted">Terlambat</small><strong class="text-secondary fs-5">${data.stats.terlambat}</strong></div></div>
                        <div class="col-4 col-sm-2"><div class="p-2 rounded-3" style="background:#fff8db;"><small class="d-block text-muted">Izin</small><strong class="text-warning fs-5">${data.stats.izin}</strong></div></div>
                        <div class="col-4 col-sm-2"><div class="p-2 rounded-3" style="background:#e7f7fb;"><small class="d-block text-muted">Sakit</small><strong class="text-info fs-5">${data.stats.sakit}</strong></div></div>
                        <div class="col-4 col-sm-2"><div class="p-2 rounded-3" style="background:#fff0f0;"><small class="d-block text-muted">Alfa</small><strong class="text-danger fs-5">${data.stats.alpa}</strong></div></div>
                        <div class="col-4 col-sm-2"><div class="p-2 rounded-3" style="background:#f1f5f9;"><small class="d-block text-muted">Belum Diproses</small><strong class="text-dark fs-5">${data.stats.belum_hadir}</strong></div></div>
                    </div>

                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-people me-1"></i> Daftar Status Absensi Siswa</h6>
                    <div class="table-responsive border rounded-3">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>NIS</th>
                                    <th>Nama Siswa</th>
                                    <th>Jam Scan</th>
                                    <th>Status Absensi</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${studentsHtml}
                            </tbody>
                        </table>
                    </div>
                `;
            })
            .catch(() => {
                modalBody.innerHTML = `<div class="alert alert-danger mb-0">Terjadi kesalahan pada server saat mengambil data.</div>`;
            });
        });
    });
});
</script>
@endpush

