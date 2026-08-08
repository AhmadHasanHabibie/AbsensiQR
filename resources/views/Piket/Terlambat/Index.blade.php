@extends('Layouts.LayoutGuruPiket')

@section('title', 'Monitoring Keterlambatan Siswa')

@section('content')

<div class="container-fluid py-2">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Monitoring Keterlambatan Siswa</h3>
            <p class="text-muted mb-0">Data keterlambatan siswa yang dicatat langsung oleh Operator lapangan.</p>
        </div>
        <div class="text-end d-none d-sm-block">
            <div class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($dateStr)->isoFormat('D MMMM YYYY') }}</div>
            <div id="realtime-clock" class="text-muted small">{{ \Carbon\Carbon::now()->format('H:i:s') }} WIB</div>
        </div>
    </div>

    {{-- Ringkasan Stat Card --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:48px;height:48px;background:linear-gradient(135deg,#ffc107,#fbbf24);border-radius:14px;display:flex;align-items:center;justify-content:center;color:#000;font-size:22px;">
                        <i class="bi bi-alarm-fill"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Terlambat (Tanggal Ini)</div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $totalTerlambatHariIni }} <span class="fs-6 text-muted fw-normal">Siswa</span></h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:48px;height:48px;background:linear-gradient(135deg,#0284c7,#38bdf8);border-radius:14px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:22px;">
                        <i class="bi bi-person-workspace"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Sumber Data</div>
                        <h5 class="fw-bold mb-0 text-dark">Modul Operator Lapangan</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:48px;height:48px;background:linear-gradient(135deg,#10b981,#34d399);border-radius:14px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:22px;">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Hak Akses Modul</div>
                        <h5 class="fw-bold mb-0 text-dark">Read-Only Monitoring</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter, Search & Sort Toolbar --}}
    <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-funnel me-2 text-primary"></i>Filter, Pencarian & Pengurutan</h5>
        </div>
        <div class="card-body p-4">
            <form method="GET" action="{{ route('piket.terlambat.index') }}" class="row g-3">

                {{-- Tanggal --}}
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label fw-semibold small text-muted">Tanggal Keterlambatan</label>
                    <input type="date" name="date" class="form-control" value="{{ $dateStr }}">
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

                {{-- Search --}}
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label fw-semibold small text-muted">Cari Nama / NIS</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Nama / NIS..." value="{{ request('search') }}">
                    </div>
                </div>

                {{-- Sort By & Direction --}}
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label fw-semibold small text-muted">Urutkan Berdasarkan</label>
                    <select name="sort" class="form-select">
                        <option value="name" {{ request('sort', 'name') === 'name' ? 'selected' : '' }}>Nama Siswa</option>
                        <option value="jam_datang" {{ request('sort') === 'jam_datang' ? 'selected' : '' }}>Jam Datang</option>
                        <option value="class" {{ request('sort') === 'class' ? 'selected' : '' }}>Kelas</option>
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label fw-semibold small text-muted">Arah Urutan</label>
                    <select name="direction" class="form-select">
                        <option value="asc" {{ request('direction', 'asc') === 'asc' ? 'selected' : '' }}>Ascending (A-Z / Lama)</option>
                        <option value="desc" {{ request('direction') === 'desc' ? 'selected' : '' }}>Descending (Z-A / Baru)</option>
                    </select>
                </div>

                <div class="col-12 col-md-6 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-filter me-1"></i> Terapkan Filter
                    </button>
                    <a href="{{ route('piket.terlambat.index') }}" class="btn btn-outline-secondary px-3">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </a>
                </div>

            </form>
        </div>
    </div>

    {{-- Tabel Monitoring Keterlambatan --}}
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
        <div class="card-header bg-warning text-dark p-3 d-flex align-items-center justify-content-between">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-alarm me-2"></i> Daftar Keterlambatan Siswa
            </h5>
            <span class="badge bg-dark text-white fw-bold px-3 py-2">
                {{ $totalTerlambatHariIni }} Siswa Terlambat
            </span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" width="4%">No</th>
                            <th>Nama Siswa</th>
                            <th>NIS</th>
                            <th>Kelas</th>
                            <th>Jam Masuk</th>
                            <th>Jam Datang</th>
                            <th>Total Keterlambatan</th>
                            <th>Alasan</th>
                            <th>Operator</th>
                            <th>Sumber Presensi</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th class="pe-4 text-center" width="8%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($lateAttendances as $item)
                            <tr>
                                <td class="ps-4 text-muted fw-semibold">
                                    {{ $loop->iteration + ($lateAttendances->currentPage() - 1) * $lateAttendances->perPage() }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width:32px;height:32px;background:linear-gradient(135deg,#ffc107,#fbbf24);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#000;font-size:13px;font-weight:600;">
                                            {{ substr($item->student_name, 0, 1) }}
                                        </div>
                                        <strong class="text-dark">{{ $item->student_name }}</strong>
                                    </div>
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ $item->student_nis }}</span></td>
                                <td><strong class="text-primary">{{ $item->class_name }}</strong></td>
                                <td><span class="badge bg-light text-muted border">{{ $item->jam_masuk }}</span></td>
                                <td><span class="badge bg-danger">{{ $item->jam_datang }}</span></td>
                                <td>
                                    <span class="badge bg-warning text-dark px-2 py-1">
                                        <i class="bi bi-exclamation-circle me-1"></i>{{ $item->total_late_count }}x Keterlambatan
                                    </span>
                                </td>
                                <td><small class="text-muted">{{ Str::limit($item->late_note, 25) }}</small></td>
                                <td><small class="text-muted"><i class="bi bi-person-workspace me-1"></i>{{ $item->operator_name }}</small></td>
                                <td>
                                    @if($item->is_emergency)
                                        <span class="badge bg-warning text-dark border border-warning">
                                            <i class="bi bi-circle-fill me-1 small text-warning"></i>🟡 Hadir Manual
                                        </span>
                                    @else
                                        <span class="badge bg-success text-white border border-success">
                                            <i class="bi bi-circle-fill me-1 small text-light"></i>🟢 Scan QR
                                        </span>
                                    @endif
                                </td>
                                <td><small class="text-muted">{{ \Carbon\Carbon::parse($item->attendance_date)->isoFormat('D MMM YYYY') }}</small></td>
                                <td>
                                    <span class="badge bg-warning text-dark px-2 py-1">
                                        Terlambat
                                    </span>
                                </td>
                                <td class="pe-4 text-center">
                                    <button type="button" class="btn btn-info btn-sm text-white px-2 btn-detail-late" data-id="{{ $item->id }}">
                                        <i class="bi bi-eye me-1"></i> Detail
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13" class="text-center text-muted py-5">
                                    <i class="bi bi-check-circle fs-1 d-block mb-2 text-success opacity-50"></i>
                                    <p class="mb-0">Tidak ada data siswa terlambat untuk filter ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($lateAttendances->hasPages())
            <div class="card-footer bg-white border-0 p-3 d-flex justify-content-center">
                {{ $lateAttendances->links() }}
            </div>
        @endif
    </div>

</div>

{{-- DETAIL MODAL READ-ONLY --}}
<div class="modal fade" id="lateDetailModal" tabindex="-1" aria-labelledby="lateDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-warning text-dark p-3 rounded-top-4">
                <h5 class="modal-title mb-0 fw-bold" id="lateDetailModalLabel">
                    <i class="bi bi-alarm-fill me-2"></i> Detail Keterlambatan Siswa
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="lateModalBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-warning" role="status">
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

    const modalEl = document.getElementById('lateDetailModal');
    const lateModal = new bootstrap.Modal(modalEl);
    const modalBody = document.getElementById('lateModalBody');

    document.querySelectorAll('.btn-detail-late').forEach(function (button) {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');

            modalBody.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-warning" role="status">
                        <span class="visually-hidden">Memuat...</span>
                    </div>
                </div>
            `;

            lateModal.show();

            fetch(`{{ url('piket/terlambat') }}/${id}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    modalBody.innerHTML = `<div class="alert alert-danger mb-0">Gagal memuat detail keterlambatan.</div>`;
                    return;
                }

                modalBody.innerHTML = `
                    <div class="d-flex align-items-center gap-3 mb-4 p-3 bg-light rounded-3 border">
                        <div style="width:52px;height:52px;background:linear-gradient(135deg,#ffc107,#fbbf24);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#000;font-size:22px;font-weight:700;">
                            ${data.student_name.charAt(0)}
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-0">${data.student_name}</h5>
                            <span class="text-muted small">NIS: ${data.student_nis} | Kelas: ${data.class_name}</span>
                        </div>
                    </div>

                    <table class="table table-borderless mb-3">
                        <tr>
                            <td class="text-muted" width="40%">Tanggal Presensi</td>
                            <td class="fw-bold">: ${data.attendance_date}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Jam Masuk Ketentuan</td>
                            <td>: <span class="badge bg-light text-dark border">${data.jam_masuk}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Jam Datang Siswa</td>
                            <td>: <span class="badge bg-danger fs-6">${data.jam_datang}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Total Keterlambatan Siswa</td>
                            <td>: <span class="badge bg-warning text-dark font-bold">${data.total_late_count}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Petugas Operator</td>
                            <td>: <span class="text-dark fw-semibold">${data.operator_name}</span></td>
                        </tr>
                    </table>

                    <div class="p-3 border rounded-3 bg-white">
                        <label class="fw-bold text-dark mb-1 small d-block">Alasan / Catatan Keterlambatan:</label>
                        <p class="mb-0 text-dark lh-relaxed" style="white-space: pre-wrap;">${data.late_note}</p>
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
