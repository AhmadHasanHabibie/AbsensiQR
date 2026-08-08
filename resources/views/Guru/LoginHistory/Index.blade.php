@extends('Layouts.LayoutGuru')

@section('title', 'Riwayat Login')

@section('content')

<div class="container-fluid py-2">

    {{-- Page Header --}}
    <div class="mb-4">
        <h3 class="fw-bold mb-1">Riwayat Login</h3>
        <p class="text-muted mb-0">Riwayat aktivitas Login akun Anda.</p>
    </div>

    {{-- Ringkasan Cards --}}
    <div class="row g-3 mb-4">

        {{-- Login Hari Ini --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:48px;height:48px;background:linear-gradient(135deg,#0d6efd,#0dcaf0);border-radius:14px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:22px;">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Login Hari Ini</div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $loginHariIni }} Sesi</h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- Login Bulan Ini --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:48px;height:48px;background:linear-gradient(135deg,#ffc107,#ffb300);border-radius:14px;display:flex;align-items:center;justify-content:center;color:#000;font-size:22px;">
                        <i class="bi bi-calendar-month"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Login Bulan Ini</div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $loginBulanIni }} Sesi</h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card Login Terakhir --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:48px;height:48px;background:linear-gradient(135deg,#198754,#20c997);border-radius:14px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:22px;">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-muted small fw-semibold">Login Terakhir</div>
                        @if($lastLogin && $lastLogin->login_at)
                            <div class="fw-bold text-dark small text-truncate">{{ $lastLogin->login_at->isoFormat('DD MMM YYYY') }} — {{ $lastLogin->login_at->format('H:i') }} WIB</div>
                            <small class="text-muted text-truncate d-block">{{ $lastLogin->browser ?? 'Peramban' }} ({{ $lastLogin->platform ?? 'OS' }})</small>
                        @else
                            <div class="fw-bold text-dark small">-</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Login --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:48px;height:48px;background:linear-gradient(135deg,#6f42c1,#a88beb);border-radius:14px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:22px;">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Login</div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $totalLogin }} Sesi</h4>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Data Table Card --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-success text-white p-3">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-shield-lock me-2"></i> Log Aktivitas Login Akun Saya
            </h5>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" width="4%">No</th>
                            <th>Tanggal</th>
                            <th>Jam Login</th>
                            <th>Jam Logout</th>
                            <th>Durasi Login</th>
                            <th>Alamat IP</th>
                            <th>Peramban</th>
                            <th>Sistem Operasi</th>
                            <th>Perangkat</th>
                            <th class="text-center">Status</th>
                            <th class="pe-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loginHistories as $history)
                            <tr>
                                <td class="ps-4 fw-semibold text-muted">
                                    {{ $loginHistories->firstItem() + $loop->index }}
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">
                                        {{ $history->login_at ? $history->login_at->isoFormat('DD MMMM YYYY') : '-' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <i class="bi bi-clock me-1"></i>{{ $history->login_at ? $history->login_at->format('H:i') : '-' }} WIB
                                    </span>
                                </td>
                                <td>
                                    @if($history->logout_at)
                                        <span class="badge bg-light text-dark border">
                                            <i class="bi bi-clock me-1"></i>{{ $history->logout_at->format('H:i') }} WIB
                                        </span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!$history->logout_at)
                                        <span class="badge bg-success">
                                            <i class="bi bi-circle-fill me-1 small"></i> Sedang Aktif
                                        </span>
                                    @else
                                        <span class="badge bg-light text-dark border">
                                            <i class="bi bi-hourglass-split me-1"></i>{{ $history->formatted_duration }}
                                        </span>
                                    @endif
                                </td>
                                <td><code class="text-dark">{{ $history->ip_address ?? '-' }}</code></td>
                                <td>
                                    @if(str_contains(strtolower($history->browser), 'chrome'))
                                        <i class="bi bi-browser-chrome text-warning me-1"></i>
                                    @elseif(str_contains(strtolower($history->browser), 'edge'))
                                        <i class="bi bi-browser-edge text-primary me-1"></i>
                                    @elseif(str_contains(strtolower($history->browser), 'firefox'))
                                        <i class="bi bi-browser-firefox text-danger me-1"></i>
                                    @elseif(str_contains(strtolower($history->browser), 'safari'))
                                        <i class="bi bi-compass text-info me-1"></i>
                                    @else
                                        <i class="bi bi-globe me-1 text-secondary"></i>
                                    @endif
                                    {{ $history->browser ?? '-' }}
                                </td>
                                <td>
                                    @if(strtolower($history->platform) === 'windows')
                                        <span class="badge bg-primary"><i class="bi bi-windows me-1"></i> Windows</span>
                                    @elseif(strtolower($history->platform) === 'android')
                                        <span class="badge bg-success"><i class="bi bi-android2 me-1"></i> Android</span>
                                    @elseif(in_array(strtolower($history->platform), ['ios', 'macos']))
                                        <span class="badge bg-dark"><i class="bi bi-apple me-1"></i> {{ $history->platform }}</span>
                                    @elseif(strtolower($history->platform) === 'linux')
                                        <span class="badge bg-warning text-dark"><i class="bi bi-cpu me-1"></i> Linux</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $history->platform ?? '-' }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if(strtolower($history->device) === 'mobile')
                                        <i class="bi bi-phone me-1 text-primary"></i> Mobile
                                    @elseif(strtolower($history->device) === 'tablet')
                                        <i class="bi bi-tablet me-1 text-info"></i> Tablet
                                    @else
                                        <i class="bi bi-display me-1 text-secondary"></i> Desktop
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if(!$history->logout_at)
                                        <span class="badge bg-success">
                                            <i class="bi bi-circle-fill me-1 small"></i> Sedang Aktif
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-check-all me-1"></i> Selesai
                                        </span>
                                    @endif
                                </td>
                                <td class="pe-4 text-center">
                                    <button class="btn btn-info btn-sm text-white" data-bs-toggle="modal" data-bs-target="#detailModal-{{ $history->id }}">
                                        <i class="bi bi-eye me-1"></i> Lihat Detail
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted py-5">
                                    <i class="bi bi-clock-history display-1 text-secondary opacity-50 d-block mb-3"></i>
                                    <h5 class="fw-bold text-dark">Belum ada riwayat Login.</h5>
                                    <p class="text-muted small mb-0">Riwayat login akun Anda akan tercatat secara otomatis di sini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($loginHistories->hasPages())
                <div class="p-3 bg-light d-flex justify-content-end">
                    {{ $loginHistories->links() }}
                </div>
            @endif
        </div>
    </div>

</div>

{{-- Modals Detail Login --}}
@foreach($loginHistories as $history)
<div class="modal fade" id="detailModal-{{ $history->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-success text-white p-3">
                <h5 class="modal-title fw-semibold">
                    <i class="bi bi-shield-lock me-2"></i> Detail Sesi Login
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-4 fw-bold text-dark">Nama User</div>
                    <div class="col-8">{{ Auth::user()->name }}</div>

                    <div class="col-4 fw-bold text-dark">Peran</div>
                    <div class="col-8">Guru</div>

                    <hr class="my-2">

                    <div class="col-4 fw-bold text-dark">Tanggal Login</div>
                    <div class="col-8">{{ $history->login_at ? $history->login_at->isoFormat('DD MMMM YYYY') : '-' }}</div>

                    <div class="col-4 fw-bold text-dark">Jam Login</div>
                    <div class="col-8">{{ $history->login_at ? $history->login_at->format('H:i:s') : '-' }} WIB</div>

                    <div class="col-4 fw-bold text-dark">Jam Logout</div>
                    <div class="col-8">{{ $history->logout_at ? $history->logout_at->format('H:i:s') . ' WIB' : 'Belum Logout (Sedang Aktif)' }}</div>

                    <div class="col-4 fw-bold text-dark">Durasi Login</div>
                    <div class="col-8">{{ $history->formatted_duration }}</div>

                    <hr class="my-2">

                    <div class="col-4 fw-bold text-dark">Alamat IP</div>
                    <div class="col-8"><code>{{ $history->ip_address ?? '-' }}</code></div>

                    <div class="col-4 fw-bold text-dark">Peramban</div>
                    <div class="col-8">{{ $history->browser ?? '-' }}</div>

                    <div class="col-4 fw-bold text-dark">Sistem Operasi</div>
                    <div class="col-8">{{ $history->platform ?? '-' }}</div>

                    <div class="col-4 fw-bold text-dark">Perangkat</div>
                    <div class="col-8">{{ $history->device ?? 'Desktop' }}</div>

                    <div class="col-4 fw-bold text-dark">Status Sesi</div>
                    <div class="col-8">
                        @if(!$history->logout_at)
                            <span class="badge bg-success">Sedang Aktif</span>
                        @else
                            <span class="badge bg-secondary">Selesai</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light p-3">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection
