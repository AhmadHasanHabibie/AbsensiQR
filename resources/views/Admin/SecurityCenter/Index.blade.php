@extends('Layouts.LayoutAdmin')

@section('title', 'Keamanan Sistem')

@section('content')

<div class="container-fluid py-2">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-1">
                <i class="bi bi-shield-lock-fill text-primary me-2"></i> Keamanan Sistem Terpadu
            </h3>
            <p class="text-muted mb-0">Pusat pemantauan keamanan, deteksi ancaman, analisis audit, dan blokir IP</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.activity-log.index') }}" class="btn btn-outline-primary rounded-pill btn-sm font-monospace fw-bold">
                <i class="bi bi-shield-check me-1"></i> Riwayat Aktivitas
            </a>
            <a href="{{ route('admin.login-history.index') }}" class="btn btn-outline-secondary rounded-pill btn-sm font-monospace fw-bold">
                <i class="bi bi-clock-history me-1"></i> Riwayat Login
            </a>
        </div>
    </div>

    {{-- NAV TABS INTERFACE --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom p-2">
            <ul class="nav nav-pills nav-fill gap-2" id="securityTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active rounded-3 font-monospace fw-semibold py-2.5" id="dashboard-tab" data-bs-toggle="tab" data-bs-target="#dashboard-pane" type="button" role="tab">
                        <i class="bi bi-speedometer2 me-1"></i> Dashboard Keamanan
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-3 font-monospace fw-semibold py-2.5" id="failed-tab" data-bs-toggle="tab" data-bs-target="#failed-pane" type="button" role="tab">
                        <i class="bi bi-person-x me-1"></i> Login Gagal
                        @if($loginGagal > 0)
                            <span class="badge bg-warning text-dark ms-1 font-monospace">{{ $loginGagal }}</span>
                        @endif
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-3 font-monospace fw-semibold py-2.5" id="blocked-tab" data-bs-toggle="tab" data-bs-target="#blocked-pane" type="button" role="tab">
                        <i class="bi bi-ban me-1"></i> Daftar IP Diblokir
                        @if($blockedIpTotal > 0)
                            <span class="badge bg-secondary ms-1 font-monospace">{{ $blockedIpTotal }}</span>
                        @endif
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-3 font-monospace fw-semibold py-2.5" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings-pane" type="button" role="tab">
                        <i class="bi bi-sliders me-1"></i> Pengaturan
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-4">
            <div class="tab-content" id="securityTabsContent">

                {{-- TAB 1: DASHBOARD KEAMANAN --}}
                <div class="tab-pane fade show active" id="dashboard-pane" role="tabpanel" tabindex="0">
                    
                    {{-- Status Banner --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-light">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width:56px;height:56px;border-radius:16px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:26px;" class="{{ $statusBadgeClass }}">
                                        <i class="bi bi-shield-lock-fill"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small fw-semibold">Status Keamanan Sistem Saat Ini</div>
                                        <h4 class="fw-bold mb-0">
                                            <span class="badge {{ $statusBadgeClass }} px-3 py-2 font-monospace">{{ $systemStatus }}</span>
                                        </h4>
                                    </div>
                                </div>
                                <div class="text-muted small max-w-lg">
                                    <i class="bi bi-info-circle me-1"></i> {{ $statusDescription }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Metrics Grid --}}
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-3">
                            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100 border-start border-4 border-primary">
                                <div class="text-muted small font-monospace">Total Login</div>
                                <h4 class="fw-bold mb-0 text-dark font-monospace">{{ $totalLoginHariIni }}</h4>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100 border-start border-4 border-success">
                                <div class="text-muted small font-monospace">Login Berhasil</div>
                                <h4 class="fw-bold mb-0 text-success font-monospace">{{ $loginBerhasil }}</h4>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100 border-start border-4 border-danger">
                                <div class="text-muted small font-monospace">Login Gagal</div>
                                <h4 class="fw-bold mb-0 text-danger font-monospace">{{ $loginGagal }}</h4>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100 border-start border-4 border-secondary">
                                <div class="text-muted small font-monospace">Total IP Diblokir</div>
                                <h4 class="fw-bold mb-0 text-secondary font-monospace">{{ $blockedIpTotal }}</h4>
                            </div>
                        </div>
                    </div>

                    {{-- Recent Audit Table --}}
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white py-3 px-4 border-bottom">
                            <span class="fw-bold text-dark"><i class="bi bi-clock-history text-primary me-2"></i> 10 Aktivitas Sistem Terbaru</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light text-muted small text-uppercase">
                                        <tr>
                                            <th class="ps-4">Waktu</th>
                                            <th>Pengguna</th>
                                            <th>Role</th>
                                            <th>Aktivitas</th>
                                            <th>Modul</th>
                                            <th>IP & Perangkat</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentActivities as $act)
                                        <tr>
                                            <td class="ps-4 font-monospace small text-muted">{{ $act['time'] }}</td>
                                            <td class="fw-bold text-dark">{{ $act['user_name'] }}</td>
                                            <td><span class="badge bg-info-subtle text-info border border-info-subtle font-monospace px-2 py-1">{{ $act['role'] ?? 'System' }}</span></td>
                                            <td class="fw-semibold text-primary">{{ $act['activity'] }}</td>
                                            <td class="font-monospace small text-muted">{{ $act['module'] }}</td>
                                            <td class="font-monospace small text-secondary">{{ $act['ip_address'] ?? '127.0.0.1' }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">Belum ada aktivitas terekam.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- TAB 2: LOGIN GAGAL --}}
                <div class="tab-pane fade" id="failed-pane" role="tabpanel" tabindex="0">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-person-x text-warning me-2"></i> Log Riwayat Percobaan Login Gagal</h6>
                        <span class="badge bg-secondary font-monospace">Total Gagal: {{ $failedLoginsList->total() }} Record</span>
                    </div>

                    <div class="table-responsive border rounded-4">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th class="ps-4">Tanggal & Jam</th>
                                    <th>Username / NIS</th>
                                    <th>IP Address</th>
                                    <th>User Agent / Browser</th>
                                    <th>Status Log</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($failedLoginsList as $failed)
                                <tr>
                                    <td class="ps-4 font-monospace small text-muted">{{ $failed->login_at ? $failed->login_at->translatedFormat('d M Y, H:i:s') : '-' }}</td>
                                    <td class="fw-bold text-danger font-monospace">{{ $failed->username ?? 'N/A' }}</td>
                                    <td class="font-monospace small text-dark">{{ $failed->ip_address ?? '127.0.0.1' }}</td>
                                    <td class="text-muted small font-monospace">{{ Str::limit($failed->user_agent ?? 'Browser', 40) }}</td>
                                    <td><span class="badge bg-danger-subtle text-danger border border-danger-subtle font-monospace px-2.5 py-1">LOGIN FAILED</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Tidak ada data login gagal yang terekam.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($failedLoginsList->hasPages())
                    <div class="p-3 bg-light border-top rounded-bottom-4 mt-3">
                        {{ $failedLoginsList->links('pagination::bootstrap-5') }}
                    </div>
                    @endif
                </div>

                {{-- TAB 3: DAFTAR IP DIBLOKIR --}}
                <div class="tab-pane fade" id="blocked-pane" role="tabpanel" tabindex="0">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-ban text-danger me-2"></i> Daftar Alamat IP Yang Diblokir Sistem</h6>
                        <span class="badge bg-secondary font-monospace">Total: {{ $blockedIpsList->total() }} IP</span>
                    </div>

                    <div class="table-responsive border rounded-4">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th class="ps-4">Alamat IP</th>
                                    <th>Alasan Pemblokiran</th>
                                    <th>Status Blokir</th>
                                    <th>Waktu Diblokir</th>
                                    <th>Waktu Berakhir</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($blockedIpsList as $bip)
                                <tr>
                                    <td class="ps-4 font-monospace fw-bold text-dark">{{ $bip->ip_address }}</td>
                                    <td class="text-danger fw-semibold small">{{ $bip->reason }}</td>
                                    <td>
                                        @if($bip->is_permanent)
                                            <span class="badge bg-danger">PERMANEN</span>
                                        @elseif($bip->blocked_until && $bip->blocked_until->isFuture())
                                            <span class="badge bg-warning text-dark">SEMENTARA</span>
                                        @else
                                            <span class="badge bg-success">KEDALUWARSA</span>
                                        @endif
                                    </td>
                                    <td class="font-monospace small text-muted">{{ $bip->created_at ? $bip->created_at->translatedFormat('d M Y H:i') : '-' }}</td>
                                    <td class="font-monospace small text-muted">
                                        @if($bip->is_permanent)
                                            <span class="text-danger fw-bold">Selamanya</span>
                                        @else
                                            {{ $bip->blocked_until ? $bip->blocked_until->translatedFormat('d M Y H:i') : '-' }}
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Tidak ada alamat IP yang sedang diblokir.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($blockedIpsList->hasPages())
                    <div class="p-3 bg-light border-top rounded-bottom-4 mt-3">
                        {{ $blockedIpsList->links('pagination::bootstrap-5') }}
                    </div>
                    @endif
                </div>

                {{-- TAB 4: PENGATURAN KEAMANAN (READ ONLY) --}}
                <div class="tab-pane fade" id="settings-pane" role="tabpanel" tabindex="0">
                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-shield-check text-success me-2"></i> Status Proteksi & Parameter Keamanan Sekolah (Ringkasan)</h6>
                    
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <div class="p-3 bg-light rounded-4 border">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="fw-bold text-dark"><i class="bi bi-key-fill text-primary me-2"></i> Keamanan Login & Sesi Pengguna</span>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace px-2.5 py-1">AKTIF (TERPROTEKSI)</span>
                                </div>
                                <div class="text-muted small">Proteksi otentikasi kata sandi dengan batasan percobaan login gagal dan pembatasan durasi sesi login pengguna.</div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="p-3 bg-light rounded-4 border">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="fw-bold text-dark"><i class="bi bi-shield-x text-danger me-2"></i> Proteksi Otomatis Pemblokiran IP</span>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace px-2.5 py-1">AKTIF</span>
                                </div>
                                <div class="text-muted small">Pemblokiran otomatis alamat IP secara sementara atau permanen saat mendeteksi percobaan akses tidak sah berulang.</div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="p-3 bg-light rounded-4 border">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="fw-bold text-dark"><i class="bi bi-lock-fill text-info me-2"></i> Proteksi Keamanan Formulir Data</span>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace px-2.5 py-1">TERPROTEKSI</span>
                                </div>
                                <div class="text-muted small">Validasi enkripsi keamanan otomatis pada setiap penyerahan formulir untuk menjaga integritas data absensi sekolah.</div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="p-3 bg-light rounded-4 border">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="fw-bold text-dark"><i class="bi bi-database-check text-primary me-2"></i> Riwayat Aktivitas & Audit Sekolah</span>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace px-2.5 py-1">PENCATATAN AKTIF</span>
                                </div>
                                <div class="text-muted small">Pencatatan mutlak IP, peramban, perangkat, dan timestamp setiap aktivitas operasional pengguna di lingkungan sekolah.</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

@endsection

