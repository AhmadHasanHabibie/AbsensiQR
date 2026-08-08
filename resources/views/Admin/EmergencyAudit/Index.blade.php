@extends('Layouts.LayoutAdmin')

@section('title', 'Audit Center Absensi Darurat')

@section('content')

<div class="container-fluid py-2">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-shield-check text-warning me-2"></i>Audit Center Absensi Darurat</h3>
            <p class="text-muted mb-0">Pusat audit trail resmi, investigasi, dan rekam jejak histori perubahan presensi darurat sekolah.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.emergency-audit.pdf', request()->all()) }}" class="btn btn-outline-danger">
                <i class="bi bi-file-earmark-pdf me-1"></i> Unduh PDF
            </a>
            <a href="{{ route('admin.emergency-audit.excel', request()->all()) }}" class="btn btn-success fw-bold">
                <i class="bi bi-file-earmark-excel me-1"></i> Unduh Excel
            </a>
            <a href="{{ route('admin.emergency-audit.csv', request()->all()) }}" class="btn btn-primary fw-bold">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i> Unduh CSV
            </a>
        </div>
    </div>

    {{-- Ringkasan Audit Cards (Quick Metrics & Periods) --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-4 border-warning">
                <div class="card-body p-3 text-center">
                    <p class="text-muted small fw-semibold mb-1">Hari Ini</p>
                    <h3 class="fw-bold text-warning mb-0">{{ $totalHariIni ?? 0 }}</h3>
                    <small class="text-muted fs-7">Presensi Darurat</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-4 border-primary">
                <div class="card-body p-3 text-center">
                    <p class="text-muted small fw-semibold mb-1">7 Hari Terakhir</p>
                    <h3 class="fw-bold text-primary mb-0">{{ $total7Hari ?? 0 }}</h3>
                    <small class="text-muted fs-7">Presensi Darurat</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-4 border-dark">
                <div class="card-body p-3 text-center">
                    <p class="text-muted small fw-semibold mb-1">30 Hari Terakhir</p>
                    <h3 class="fw-bold text-dark mb-0">{{ $total30Hari ?? 0 }}</h3>
                    <small class="text-muted fs-7">Presensi Darurat</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-4 border-success">
                <div class="card-body p-3 text-center">
                    <p class="text-muted small fw-semibold mb-1">Disetujui Guru</p>
                    <h3 class="fw-bold text-success mb-0">{{ $disetujuiCount ?? 0 }}</h3>
                    <small class="text-muted fs-7">Hari Ini</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-4 border-info">
                <div class="card-body p-3 text-center">
                    <p class="text-muted small fw-semibold mb-1">Diubah Guru</p>
                    <h3 class="fw-bold text-info mb-0">{{ $diubahCount ?? 0 }}</h3>
                    <small class="text-muted fs-7">Hari Ini</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-4 border-danger">
                <div class="card-body p-3 text-center">
                    <p class="text-muted small fw-semibold mb-1">Ditolak (Alpa)</p>
                    <h3 class="fw-bold text-danger mb-0">{{ $ditolakCount ?? 0 }}</h3>
                    <small class="text-muted fs-7">Hari Ini</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-funnel me-2 text-primary"></i>Filter Audit Trail Lengkap</h5>
        </div>
        <div class="card-body p-4">
            <form method="GET" action="{{ route('admin.emergency-audit.index') }}" class="row g-3">
                {{-- Rentang Tanggal --}}
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label fw-semibold small text-muted">Mulai Tanggal</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label fw-semibold small text-muted">Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>

                {{-- Kelas --}}
                <div class="col-12 col-sm-6 col-md-2">
                    <label class="form-label fw-semibold small text-muted">Kelas</label>
                    <select name="class_id" class="form-select">
                        <option value="">Semua Kelas</option>
                        @foreach ($classes as $cls)
                            <option value="{{ $cls->id }}" {{ request('class_id') == $cls->id ? 'selected' : '' }}>{{ $cls->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Operator --}}
                <div class="col-12 col-sm-6 col-md-2">
                    <label class="form-label fw-semibold small text-muted">Operator Input</label>
                    <select name="operator_id" class="form-select">
                        <option value="">Semua Operator</option>
                        @foreach ($operators as $op)
                            <option value="{{ $op->id }}" {{ request('operator_id') == $op->id ? 'selected' : '' }}>{{ $op->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Guru --}}
                <div class="col-12 col-sm-6 col-md-2">
                    <label class="form-label fw-semibold small text-muted">Guru Validasi</label>
                    <select name="teacher_id" class="form-select">
                        <option value="">Semua Guru</option>
                        @foreach ($teachers as $tc)
                            <option value="{{ $tc->id }}" {{ request('teacher_id') == $tc->id ? 'selected' : '' }}>{{ $tc->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Status Validasi --}}
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label fw-semibold small text-muted">Status Validasi Guru</label>
                    <select name="validation_status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="disetujui" {{ request('validation_status') == 'disetujui' ? 'selected' : '' }}>Disetujui (Hadir)</option>
                        <option value="diubah" {{ request('validation_status') == 'diubah' ? 'selected' : '' }}>Diubah Guru (Izin/Sakit/Terlambat)</option>
                        <option value="ditolak" {{ request('validation_status') == 'ditolak' ? 'selected' : '' }}>Ditolak (Alpa)</option>
                        <option value="menunggu" {{ request('validation_status') == 'menunggu' ? 'selected' : '' }}>Menunggu Validasi</option>
                    </select>
                </div>

                {{-- Search --}}
                <div class="col-12 col-sm-6 col-md-5">
                    <label class="form-label fw-semibold small text-muted">Pencarian Universal (Siswa / NIS / Petugas)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Cari nama siswa, NIS, kelas, operator, atau guru..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-12 col-md-4 d-flex align-items-end justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary px-4 w-100">
                        <i class="bi bi-filter me-1"></i> Terapkan Filter
                    </button>
                    <a href="{{ route('admin.emergency-audit.index') }}" class="btn btn-outline-secondary px-3">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Main Audit Table --}}
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
        <div class="card-header bg-dark text-white p-3 d-flex align-items-center justify-content-between">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-shield-check me-2"></i> Log Audit Absensi Darurat (Read-Only)
            </h5>
            <span class="badge bg-warning text-dark fw-bold px-3 py-2">
                {{ $audits->total() }} Record Terverifikasi
            </span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" width="4%">No</th>
                            <th>Tanggal & Jam Input</th>
                            <th>Nama Siswa & NIS</th>
                            <th>Kelas</th>
                            <th>Operator Input</th>
                            <th>Guru Validator</th>
                            <th>Status Awal</th>
                            <th>Status Akhir</th>
                            <th>Status Validasi</th>
                            <th class="pe-4 text-center" width="8%">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($audits as $item)
                            @php
                                $finalLower = strtolower($item->final_status ?? '');
                                $valBadgeClass = 'bg-secondary';
                                $valBadgeLabel = 'Menunggu';

                                if ($finalLower === 'hadir') {
                                    $valBadgeClass = 'bg-success';
                                    $valBadgeLabel = 'Disetujui';
                                } elseif (in_array($finalLower, ['izin', 'sakit', 'terlambat'])) {
                                    $valBadgeClass = 'bg-warning text-dark';
                                    $valBadgeLabel = 'Diubah Guru';
                                } elseif ($finalLower === 'alpa') {
                                    $valBadgeClass = 'bg-danger';
                                    $valBadgeLabel = 'Ditolak';
                                }
                            @endphp
                            <tr>
                                <td class="ps-4 text-muted fw-semibold">
                                    {{ $loop->iteration + ($audits->currentPage() - 1) * $audits->perPage() }}
                                </td>
                                <td>
                                    <small class="text-muted fw-semibold d-block">
                                        <i class="bi bi-calendar-event me-1"></i>{{ optional($item->input_at)->isoFormat('D MMM YYYY') }}
                                    </small>
                                    <small class="text-dark fw-bold">
                                        <i class="bi bi-clock me-1"></i>{{ optional($item->input_at)->format('H:i:s') }} WIB
                                    </small>
                                </td>
                                <td>
                                    <strong class="text-dark">{{ optional($item->student)->name ?? '-' }}</strong>
                                    <div class="small text-muted">NIS: {{ optional($item->student)->nis ?? '-' }}</div>
                                </td>
                                <td><strong class="text-primary">{{ optional($item->schoolClass)->name ?? '-' }}</strong></td>
                                <td>
                                    <div class="small">
                                        <i class="bi bi-person-workspace me-1 text-primary"></i><strong>{{ optional($item->operator)->name ?? 'Operator' }}</strong>
                                        <div class="text-muted fs-7">IP: {{ $item->ip_address ?? '-' }}</div>
                                    </div>
                                </td>
                                <td>
                                    @if ($item->teacher)
                                        <div class="small">
                                            <i class="bi bi-person-check me-1 text-success"></i><strong>{{ $item->teacher->name }}</strong>
                                            <div class="text-muted fs-7">{{ optional($item->validated_at)->format('H:i') }} WIB</div>
                                        </div>
                                    @else
                                        <span class="text-muted italic small">&mdash; Belum Divalidasi</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-warning text-dark px-2 py-1">
                                        {{ $item->initial_status }}
                                    </span>
                                </td>
                                <td>
                                    @if ($item->final_status)
                                        <span class="badge bg-{{ $finalLower === 'hadir' ? 'success' : ($finalLower === 'terlambat' ? 'secondary' : ($finalLower === 'izin' ? 'warning text-dark' : ($finalLower === 'sakit' ? 'info' : 'danger'))) }} px-3 py-2 fs-6">
                                            {{ $item->final_status }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary px-2 py-1">Menunggu</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $valBadgeClass }} px-3 py-2">
                                        {{ $valBadgeLabel }}
                                    </span>
                                </td>
                                <td class="pe-4 text-center">
                                    <button type="button" class="btn btn-info btn-sm text-white px-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#auditDetailModal{{ $item->id }}">
                                        <i class="bi bi-eye me-1"></i> Detail
                                    </button>

                                    {{-- MODAL DETAIL AUDIT & TIMELINE HISTORI --}}
                                    <div class="modal fade" id="auditDetailModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                            <div class="modal-content border-0 shadow-lg rounded-4 text-start">
                                                <div class="modal-header bg-dark text-white p-3 rounded-top-4">
                                                    <h5 class="modal-title mb-0 fw-bold">
                                                        <i class="bi bi-shield-check text-warning me-2"></i> Audit Trail & Timeline Absensi Darurat
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4">

                                                    {{-- Info Ringkas --}}
                                                    <div class="p-3 bg-light rounded-3 border mb-4">
                                                        <div class="row g-2">
                                                            <div class="col-md-6"><strong>Nama Siswa:</strong> {{ optional($item->student)->name }}</div>
                                                            <div class="col-md-6"><strong>NIS / Kelas:</strong> {{ optional($item->student)->nis ?? '-' }} / {{ optional($item->schoolClass)->name ?? '-' }}</div>
                                                            <div class="col-md-6"><strong>Alasan Darurat:</strong> <span class="badge bg-warning text-dark">{{ $item->reason }}</span></div>
                                                            <div class="col-md-6"><strong>Keterangan:</strong> {{ $item->note ?? 'Tidak ada keterangan' }}</div>
                                                        </div>
                                                    </div>

                                                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-clock-history me-1 text-primary"></i> Activity Timeline & Riwayat Perubahan Status:</h6>

                                                    {{-- Timeline Container --}}
                                                    <div class="ps-3 border-start border-3 border-warning ms-2 mb-4">

                                                        {{-- Step 1: Operator Input --}}
                                                        <div class="mb-4 position-relative">
                                                            <div class="badge bg-primary mb-1">{{ optional($item->input_at)->format('H:i:s') }} WIB &bull; {{ optional($item->input_at)->isoFormat('D MMMM YYYY') }}</div>
                                                            <h6 class="fw-bold text-dark mb-1">
                                                                <i class="bi bi-person-workspace text-primary me-1"></i> {{ optional($item->operator)->name ?? 'Operator Lapangan' }} (Role: Operator)
                                                            </h6>
                                                            <p class="text-muted small mb-1">Membuka Form & Membuat Presensi Darurat (Status Awal: <strong>Hadir Manual</strong>).</p>
                                                            <div class="small bg-light p-2 rounded border">
                                                                <div><strong>Alasan:</strong> {{ $item->reason }}</div>
                                                                <div><strong>Keterangan:</strong> {{ $item->note ?? '-' }}</div>
                                                                <div class="text-muted fs-7 mt-1">IP Address: {{ $item->ip_address ?? '-' }} &bull; Device: {{ $item->device ?? 'Web Browser' }} &bull; User Agent: {{ Str::limit($item->user_agent ?? '-', 60) }}</div>
                                                            </div>
                                                        </div>

                                                        {{-- Step 2: Teacher Validation --}}
                                                        @if($item->validated_at)
                                                            <div class="mb-4 position-relative">
                                                                <div class="badge bg-success mb-1">{{ $item->validated_at->format('H:i:s') }} WIB &bull; {{ $item->validated_at->isoFormat('D MMMM YYYY') }}</div>
                                                                <h6 class="fw-bold text-dark mb-1">
                                                                    <i class="bi bi-person-check-fill text-success me-1"></i> {{ optional($item->teacher)->name ?? 'Guru Wali Kelas' }} (Role: Wali Kelas)
                                                                </h6>
                                                                <p class="text-muted small mb-1">Membuka Halaman Konfirmasi Kehadiran & Melakukan Validasi Akhir.</p>
                                                                <div class="small bg-light p-2 rounded border">
                                                                    <div><strong>Jenis Validasi:</strong> {{ $item->validation_type === 'automatic' ? 'Disetujui Otomatis (Hadir)' : 'Diubah Manual oleh Guru' }}</div>
                                                                    <div><strong>Perubahan Status:</strong> <span class="badge bg-warning text-dark">Hadir Manual</span> &rarr; <span class="badge bg-{{ $finalLower === 'hadir' ? 'success' : 'danger' }}">{{ $item->final_status }}</span></div>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <div class="mb-4 position-relative">
                                                                <div class="badge bg-secondary mb-1">Menunggu Validasi</div>
                                                                <h6 class="fw-bold text-muted mb-1"><i class="bi bi-hourglass-split me-1"></i> Guru Wali Kelas belum melakukan konfirmasi</h6>
                                                                <p class="text-muted small mb-0">Status saat ini masih bersifat sementara (<strong>Hadir Manual</strong>).</p>
                                                            </div>
                                                        @endif

                                                        {{-- Step 3: Final Status --}}
                                                        <div class="position-relative">
                                                            <div class="fw-bold text-dark mb-1">Status Akhir Resmi:</div>
                                                            <span class="badge bg-{{ $finalLower === 'hadir' ? 'success' : ($finalLower === 'terlambat' ? 'secondary' : ($finalLower === 'izin' ? 'warning text-dark' : ($finalLower === 'sakit' ? 'info' : 'danger'))) }} fs-5 px-3 py-2">
                                                                {{ $item->final_status ?? 'Menunggu Validasi Guru' }}
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

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-5">
                                    <i class="bi bi-shield-x fs-1 d-block mb-2 opacity-50"></i>
                                    <p class="mb-0">Belum ada data audit trail absensi darurat.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($audits->hasPages())
            <div class="p-3 bg-light border-top rounded-bottom-4">
                {{ $audits->links() }}
            </div>
        @endif
    </div>

</div>

@endsection
