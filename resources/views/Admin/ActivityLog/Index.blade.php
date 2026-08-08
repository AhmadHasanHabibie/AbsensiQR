@extends('Layouts.LayoutAdmin')

@section('title', 'Riwayat Aktivitas')

@section('content')

<div class="container-fluid py-2">

    {{-- Page Header --}}
    <div class="mb-4">
        <h3 class="fw-bold mb-1">Riwayat Aktivitas</h3>
        <p class="text-muted mb-0">Monitoring log aktivitas seluruh pengguna.</p>
    </div>

    {{-- 4 Summary Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:52px;height:52px;background:linear-gradient(135deg,#0d6efd,#0dcaf0);border-radius:16px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:24px;">
                        <i class="bi bi-activity"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Aktivitas Hari Ini</div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $aktivitasHariIni }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:52px;height:52px;background:linear-gradient(135deg,#198754,#20c997);border-radius:16px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:24px;">
                        <i class="bi bi-calendar-week"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Aktivitas Minggu Ini</div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $aktivitasMingguIni }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:52px;height:52px;background:linear-gradient(135deg,#ffc107,#ffb300);border-radius:16px;display:flex;align-items:center;justify-content:center;color:#000;font-size:24px;">
                        <i class="bi bi-calendar-month"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Aktivitas Bulan Ini</div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $aktivitasBulanIni }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:52px;height:52px;background:linear-gradient(135deg,#6f42c1,#a88beb);border-radius:16px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:24px;">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Aktivitas</div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $totalAktivitas }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter & Search Form Card --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('admin.activity-log.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Pencarian</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Cari nama, aktivitas, modul..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold">Filter Peran</label>
                    <select name="role" class="form-select">
                        <option value="">Semua Peran</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                        <option value="teacher" {{ request('role') == 'teacher' ? 'selected' : '' }}>Guru</option>
                        <option value="operator" {{ request('role') == 'operator' ? 'selected' : '' }}>Operator</option>
                        <option value="piket" {{ request('role') == 'piket' || request('role') == 'guru_piket' ? 'selected' : '' }}>Guru Piket</option>
                        <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>Siswa</option>
                        <option value="super_admin" {{ request('role') == 'super_admin' ? 'selected' : '' }}>SuperAdministrator</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Filter Modul</label>
                    <select name="module" class="form-select">
                        <option value="">Semua Modul</option>
                        <option value="Attendance" {{ request('module') == 'Attendance' ? 'selected' : '' }}>Attendance</option>
                        <option value="Mailbox" {{ request('module') == 'Mailbox' ? 'selected' : '' }}>Mailbox</option>
                        <option value="Authentication" {{ request('module') == 'Authentication' ? 'selected' : '' }}>Authentication</option>
                        <option value="Security" {{ request('module') == 'Security' ? 'selected' : '' }}>Security</option>
                        <option value="Users" {{ request('module') == 'Users' ? 'selected' : '' }}>Users</option>
                        <option value="Report" {{ request('module') == 'Report' ? 'selected' : '' }}>Report</option>
                        <option value="Export" {{ request('module') == 'Export' ? 'selected' : '' }}>Export</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold">Filter Waktu</label>
                    <select name="date_range" id="date_range_select" class="form-select">
                        <option value="all" {{ request('date_range', 'all') == 'all' ? 'selected' : '' }}>Semua Waktu</option>
                        <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Hari Ini</option>
                        <option value="7days" {{ request('date_range') == '7days' ? 'selected' : '' }}>7 Hari Terakhir</option>
                        <option value="30days" {{ request('date_range') == '30days' ? 'selected' : '' }}>30 Hari Terakhir</option>
                        <option value="custom" {{ request('date_range') == 'custom' ? 'selected' : '' }}>Rentang Custom</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.activity-log.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>

                {{-- Custom Date Inputs --}}
                <div class="col-md-6 mt-2 {{ request('date_range') == 'custom' ? '' : 'd-none' }}" id="custom_date_container">
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Mulai Tanggal</label>
                            <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Sampai Tanggal</label>
                            <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Data Table Card --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-dark text-white p-3">
            <h5 class="mb-0 fw-semibold fs-6">
                <i class="bi bi-shield-check me-2"></i> Audit Log Aktivitas Pengguna
            </h5>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" width="4%">No</th>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Nama User</th>
                            <th>Peran</th>
                            <th>Modul</th>
                            <th>Aktivitas</th>
                            <th>Alamat IP</th>
                            <th>Perangkat</th>
                            <th class="pe-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activityLogs as $log)
                            <tr>
                                <td class="ps-4 fw-semibold text-muted">
                                    {{ $activityLogs->firstItem() + $loop->index }}
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">
                                        {{ $log->created_at ? $log->created_at->isoFormat('DD MMMM YYYY') : '-' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <i class="bi bi-clock me-1"></i>{{ $log->created_at ? $log->created_at->format('H:i:s') : '-' }} WIB
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ optional($log->user)->name ?? 'User System' }}</div>
                                </td>
                                <td>
                                    @switch(strtolower($log->role ?? ''))
                                        @case('super_admin')
                                            <span class="badge bg-dark text-white">SuperAdministrator</span>
                                            @break
                                        @case('admin')
                                            <span class="badge text-white" style="background:#6f42c1;">Administrator</span>
                                            @break
                                        @case('teacher')
                                            <span class="badge bg-success">Guru</span>
                                            @break
                                        @case('operator')
                                            <span class="badge bg-warning text-dark">Operator</span>
                                            @break
                                        @case('piket')
                                        @case('guru_piket')
                                            <span class="badge bg-info text-dark">Guru Piket</span>
                                            @break
                                        @case('student')
                                            <span class="badge bg-primary text-white">Siswa</span>
                                            @break
                                        @default
                                            <span class="badge {{ $log->role_badge_class }}">{{ $log->role_label }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    <span class="badge bg-outline-primary border border-primary text-primary px-2 py-1">
                                        <i class="bi bi-layers me-1"></i>{{ $log->module }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">{{ $log->activity }}</span>
                                </td>
                                <td><code class="text-dark">{{ $log->ip_address ?? '-' }}</code></td>
                                <td>
                                    @if(strtolower($log->device) === 'mobile')
                                        <i class="bi bi-phone me-1 text-primary"></i> Mobile
                                    @elseif(strtolower($log->device) === 'tablet')
                                        <i class="bi bi-tablet me-1 text-info"></i> Tablet
                                    @else
                                        <i class="bi bi-display me-1 text-secondary"></i> Desktop
                                    @endif
                                </td>
                                <td class="pe-4 text-center">
                                    <button class="btn btn-info btn-sm text-white" data-bs-toggle="modal" data-bs-target="#detailModal-{{ $log->id }}">
                                        <i class="bi bi-eye me-1"></i> Lihat Detail
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-5">
                                    <i class="bi bi-shield-check display-1 text-secondary opacity-50 d-block mb-3"></i>
                                    <h5 class="fw-bold text-dark">Belum ada riwayat Aktivitas.</h5>
                                    <p class="text-muted small mb-0">Tidak ditemukan log aktivitas berdasarkan kriteria pencarian.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
        @if($activityLogs->hasPages())
            <div class="p-3 bg-light border-top rounded-bottom-4">
                {{ $activityLogs->links() }}
            </div>
        @endif
    </div>

</div>

{{-- Modals Detail Activity Log --}}
@foreach($activityLogs as $log)
<div class="modal fade" id="detailModal-{{ $log->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white p-3">
                <h5 class="modal-title fw-semibold">
                    <i class="bi bi-shield-check me-2"></i> Detail Riwayat Aktivitas
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-4 fw-bold text-dark">Nama User</div>
                    <div class="col-8">{{ optional($log->user)->name ?? 'System' }}</div>

                    <div class="col-4 fw-bold text-dark">Username</div>
                    <div class="col-8">{{ optional($log->user)->username ?? '-' }}</div>

                    <div class="col-4 fw-bold text-dark">Peran</div>
                    <div class="col-8">{{ $log->role_label }}</div>

                    <hr class="my-2">

                    <div class="col-4 fw-bold text-dark">Modul</div>
                    <div class="col-8"><span class="badge bg-primary">{{ $log->module }}</span></div>

                    <div class="col-4 fw-bold text-dark">Aktivitas</div>
                    <div class="col-8 fw-semibold text-dark">{{ $log->activity }}</div>

                    <div class="col-4 fw-bold text-dark">Deskripsi</div>
                    <div class="col-8">{{ $log->description ?: '-' }}</div>

                    <hr class="my-2">

                    <div class="col-4 fw-bold text-dark">Alamat IP</div>
                    <div class="col-8"><code>{{ $log->ip_address ?? '-' }}</code></div>

                    <div class="col-4 fw-bold text-dark">Peramban</div>
                    <div class="col-8">{{ $log->browser ?? '-' }}</div>

                    <div class="col-4 fw-bold text-dark">Sistem Operasi</div>
                    <div class="col-8">{{ $log->platform ?? '-' }}</div>

                    <div class="col-4 fw-bold text-dark">Perangkat</div>
                    <div class="col-8">{{ $log->device ?? 'Desktop' }}</div>

                    <div class="col-4 fw-bold text-dark">Waktu</div>
                    <div class="col-8">{{ $log->created_at ? $log->created_at->isoFormat('DD MMMM YYYY, HH:mm:ss') . ' WIB' : '-' }}</div>
                </div>
            </div>
            <div class="modal-footer bg-light p-3">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const select = document.getElementById('date_range_select');
        const customContainer = document.getElementById('custom_date_container');

        if (select) {
            select.addEventListener('change', function () {
                if (this.value === 'custom') {
                    customContainer.classList.remove('d-none');
                } else {
                    customContainer.classList.add('d-none');
                }
            });
        }
    });
</script>
@endpush

@endsection
