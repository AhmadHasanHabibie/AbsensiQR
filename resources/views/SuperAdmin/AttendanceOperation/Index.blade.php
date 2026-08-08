@extends('Layouts.LayoutSuperAdmin')

@section('title', 'Operasional Absensi & Emergency Override')

@section('content')
<div class="container-fluid px-0">

    {{-- PAGE HEADER --}}
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1" style="font-size:12px;">
                <li class="breadcrumb-item text-muted">Super Admin</li>
                <li class="breadcrumb-item text-muted">Konfigurasi</li>
                <li class="breadcrumb-item active text-primary fw-semibold">Operasional Absensi</li>
            </ol>
        </nav>
        <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-shield-exclamation text-primary me-2"></i> Operasional Absensi &amp; Emergency Override
        </h4>
        <p class="text-muted mb-0 fs-13">Pusat kendali operasional absensi sekolah — Aktifkan Libur Darurat untuk menghentikan seluruh proses absensi secara global jika terjadi kondisi khusus.</p>
    </div>

    <div class="row g-4">

        {{-- STATUS CARD HARI INI --}}
        <div class="col-12 col-lg-6">
            <div class="card border-0 rounded-4 shadow-sm h-100"
                 style="border-left: 4px solid {{ $dailyStatus['is_emergency'] ? '#dc2626' : ($dailyStatus['is_holiday'] ? '#eab308' : '#16a34a') }} !important;">
                <div class="card-header bg-white py-3 px-4 border-bottom rounded-top-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 bg-{{ $dailyStatus['is_emergency'] ? 'danger' : ($dailyStatus['is_holiday'] ? 'warning' : 'success') }} bg-opacity-10 text-{{ $dailyStatus['is_emergency'] ? 'danger' : ($dailyStatus['is_holiday'] ? 'warning' : 'success') }} rounded-3">
                            <i class="bi bi-{{ $dailyStatus['is_emergency'] ? 'exclamation-octagon-fill' : ($dailyStatus['is_holiday'] ? 'calendar-x-fill' : 'shield-check-fill') }} fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">Status Operasional Hari Ini</div>
                            <div class="text-muted fs-12">{{ \Carbon\Carbon::parse($todayStr)->isoFormat('dddd, D MMMM YYYY') }}</div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">

                    {{-- MAIN BANNER --}}
                    <div class="d-flex align-items-center gap-3 mb-4 p-3 bg-{{ $dailyStatus['is_emergency'] ? 'danger' : ($dailyStatus['is_holiday'] ? 'warning' : 'success') }} bg-opacity-10 rounded-3">
                        <div class="flex-shrink-0">
                            <div class="p-3 bg-{{ $dailyStatus['is_emergency'] ? 'danger' : ($dailyStatus['is_holiday'] ? 'warning' : 'success') }} bg-opacity-15 rounded-circle">
                                <i class="bi bi-{{ $dailyStatus['is_emergency'] ? 'exclamation-triangle-fill' : ($dailyStatus['is_holiday'] ? 'calendar-x-fill' : 'check-circle-fill') }} text-{{ $dailyStatus['is_emergency'] ? 'danger' : ($dailyStatus['is_holiday'] ? 'warning' : 'success') }}" style="font-size:36px;"></i>
                            </div>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase letter-spacing-1">Kondisi Sistem</div>
                            <div class="fs-4 fw-bold text-{{ $dailyStatus['is_emergency'] ? 'danger' : ($dailyStatus['is_holiday'] ? 'warning' : 'success') }}">
                                @if($dailyStatus['is_emergency'])
                                    🔴 LIBUR DARURAT (OVERRIDE)
                                @elseif($dailyStatus['is_holiday'])
                                    🟡 {{ strtoupper($dailyStatus['status']) }}
                                @else
                                    🟢 OPERASIONAL NORMAL (HARI BELAJAR)
                                @endif
                            </div>
                            <div class="text-dark small mt-1 fw-semibold">
                                Sumber Status: <span class="badge bg-white text-dark border ms-1">{{ $dailyStatus['source'] }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- DETAIL GRID --}}
                    <div class="bg-light p-3 rounded-3 border mb-4">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="text-muted fs-12 fw-semibold mb-1"><i class="bi bi-calendar3 me-1"></i> Tanggal:</div>
                                <div class="fw-bold text-dark fs-13 font-monospace">{{ $todayStr }}</div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted fs-12 fw-semibold mb-1"><i class="bi bi-person-badge me-1"></i> Diubah Oleh:</div>
                                <div class="fw-bold text-dark fs-13">{{ $dailyStatus['updated_by'] }}</div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted fs-12 fw-semibold mb-1"><i class="bi bi-clock-history me-1"></i> Waktu Perubahan:</div>
                                <div class="fw-semibold text-muted fs-12 font-monospace">{{ $dailyStatus['updated_at'] }}</div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted fs-12 fw-semibold mb-1"><i class="bi bi-qr-code-scan me-1"></i> QR Scan Status:</div>
                                <div class="fw-bold text-{{ $dailyStatus['allow_qr_scan'] ? 'success' : 'danger' }} fs-13">
                                    {{ $dailyStatus['allow_qr_scan'] ? 'Aktif' : 'Nonaktif' }}
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="text-muted fs-12 fw-semibold mb-1"><i class="bi bi-chat-text me-1"></i> Alasan / Kegiatan:</div>
                                <div class="fw-semibold text-dark fs-13 font-monospace bg-white p-2 border rounded-2">
                                    {{ $dailyStatus['reason'] ?: 'Operasional absensi berjalan sesuai jadwal kalender akademik.' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ACTION BUTTONS --}}
                    @if($dailyStatus['is_emergency'])
                        <button type="button"
                                class="btn btn-success fw-bold w-100 py-2 rounded-3"
                                onclick="saModalOpen('modalDisableEmergency')">
                            <i class="bi bi-play-circle-fill me-1"></i> Nonaktifkan Libur Darurat (Kembali Normal)
                        </button>
                    @else
                        <button type="button"
                                class="btn btn-danger fw-bold w-100 py-2 rounded-3"
                                onclick="saModalOpen('modalEnableEmergency')">
                            <i class="bi bi-exclamation-octagon-fill me-1"></i> Aktifkan Libur Darurat Hari Ini
                        </button>
                    @endif

                </div>
            </div>
        </div>

        {{-- INFORMASI ATURAN OVERRIDE --}}
        <div class="col-12 col-lg-6">
            <div class="card border-0 rounded-4 shadow-sm h-100">
                <div class="card-header bg-white py-3 px-4 border-bottom rounded-top-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 bg-info bg-opacity-10 text-info rounded-3">
                            <i class="bi bi-info-circle-fill fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">Ketentuan Emergency Override</div>
                            <div class="text-muted fs-12">Aturan kerja otomatisasi sistem</div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4 fs-13 text-muted">

                    <div class="d-flex gap-3 mb-3">
                        <div class="p-2 bg-danger bg-opacity-10 text-danger rounded-3 h-fit flex-shrink-0">
                            <i class="bi bi-1-circle-fill fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark fs-14">Prioritas Tertinggi (Overriding)</div>
                            <div class="mt-1">
                                Jika <strong>Libur Darurat</strong> diaktifkan, status ini mengalahkan Kalender Akademik. Seluruh proses absensi (QR Scan Guru/Operator, Konfirmasi Guru, Reminder Guru Piket, dan Pembuatan Alpa) akan <strong>dihentikan seketika</strong>.
                            </div>
                        </div>
                    </div>

                    <hr class="my-3">

                    <div class="d-flex gap-3 mb-3">
                        <div class="p-2 bg-warning bg-opacity-10 text-warning rounded-3 h-fit flex-shrink-0">
                            <i class="bi bi-2-circle-fill fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark fs-14">Auto Reset Pukul 00:01 WIB</div>
                            <div class="mt-1">
                                Override hanya berlaku pada hari yang bersangkutan. Tepat pukul <strong>00:01 WIB</strong>, sistem otomatis mengakhiri override kemarin dan kembali mengikuti Kalender Akademik.
                            </div>
                        </div>
                    </div>

                    <hr class="my-3">

                    <div class="d-flex gap-3">
                        <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-3 h-fit flex-shrink-0">
                            <i class="bi bi-3-circle-fill fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark fs-14">Audit Activity Log</div>
                            <div class="mt-1">
                                Seluruh aksi aktivasi, penonaktifan, maupun auto reset Libur Darurat dicatat secara permanen di Activity Log beserta IP Address dan nama Super Administrator.
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- RIWAYAT OVERRIDE --}}
        <div class="col-12">
            <div class="card border-0 rounded-4 shadow-sm">
                <div class="card-header bg-white py-3 px-4 border-bottom rounded-top-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 bg-secondary bg-opacity-10 text-secondary rounded-3">
                            <i class="bi bi-clock-history fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">Riwayat Emergency Override</div>
                            <div class="text-muted fs-12">Daftar tanggal yang pernah diberlakukan Libur Darurat</div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($overrideHistory->isEmpty())
                        <div class="sa-empty-state">
                            <div class="sa-empty-icon">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <div class="sa-empty-title">Belum Ada Riwayat Libur Darurat</div>
                            <div class="sa-empty-desc">Sistem belum pernah memberlakukan Emergency Override.</div>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table align-middle mb-0" style="font-size:13px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4" width="160">Tanggal</th>
                                        <th width="140">Status Override</th>
                                        <th>Alasan Libur Darurat</th>
                                        <th width="200">Diubah Oleh</th>
                                        <th class="pe-4" width="180">Waktu Perubahan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($overrideHistory as $oh)
                                        <tr>
                                            <td class="ps-4 fw-bold font-monospace text-dark">{{ $oh->date->format('d/m/Y') }}</td>
                                            <td>
                                                @if($oh->is_emergency_holiday)
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle font-monospace">LIBUR DARURAT</span>
                                                @else
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace">NORMAL</span>
                                                @endif
                                            </td>
                                            <td class="font-monospace text-dark">{{ $oh->reason ?: '-' }}</td>
                                            <td class="fw-semibold">{{ optional($oh->createdBy)->name ?? 'System' }}</td>
                                            <td class="pe-4 text-muted font-monospace fs-12">{{ $oh->updated_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($overrideHistory->hasPages())
                            <div class="px-4 py-3 border-top">
                                {{ $overrideHistory->links() }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

{{-- MODAL: AKTIFKAN LIBUR DARURAT --}}
<div class="sa-modal-overlay" id="modalEnableEmergency">
    <div class="sa-modal-box" style="max-width:480px; text-align:left;">
        <div class="sa-modal-icon-wrap danger mx-auto mb-3">
            <i class="bi bi-exclamation-triangle-fill"></i>
        </div>
        <div class="sa-modal-title text-center">Konfirmasi Libur Darurat</div>
        <div class="sa-modal-desc text-muted fs-13 mb-3">
            Seluruh proses absensi hari ini akan dihentikan.<br>
            • QR Scan ditutup.<br>
            • Guru tidak dapat konfirmasi.<br>
            • Operator tidak dapat scan &amp; input.<br>
            • Guru Piket tidak dapat kirim reminder.<br>
            • Seluruh dashboard &amp; laporan hari ini berstatus <strong>LIBUR DARURAT</strong>.
        </div>

        <form action="{{ route('superadmin.attendance-operation.toggle') }}" method="POST">
            @csrf
            <input type="hidden" name="action" value="enable">
            <div class="mb-3">
                <label class="form-label text-dark fw-bold fs-13">Alasan Libur Darurat (Opsional):</label>
                <textarea name="reason"
                          class="form-control form-control-sm font-monospace"
                          rows="3"
                          placeholder="Contoh: Bencana alam, Cuaca ekstrem, Pemadaman listrik massal..."></textarea>
            </div>

            <div class="sa-modal-actions">
                <button type="button" class="sa-modal-btn sa-modal-btn-cancel" onclick="saModalClose('modalEnableEmergency')">
                    <i class="bi bi-x-circle me-1"></i> Batal
                </button>
                <button type="submit" class="sa-modal-btn sa-modal-btn-confirm danger" onclick="saLoadingShow('Mengaktifkan Libur Darurat...')">
                    <i class="bi bi-exclamation-octagon-fill me-1"></i> Ya, Aktifkan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: NONAKTIFKAN LIBUR DARURAT --}}
<div class="sa-modal-overlay" id="modalDisableEmergency">
    <div class="sa-modal-box" style="max-width:440px;">
        <div class="sa-modal-icon-wrap success mx-auto mb-3">
            <i class="bi bi-play-circle-fill"></i>
        </div>
        <div class="sa-modal-title">Kembali ke Operasional Normal</div>
        <div class="sa-modal-desc text-muted fs-13 mb-3">
            Apakah Anda yakin ingin menonaktifkan Libur Darurat hari ini?<br>
            Sistem absensi akan kembali mengikuti jadwal <strong>Kalender Akademik</strong>.
        </div>

        <form action="{{ route('superadmin.attendance-operation.toggle') }}" method="POST">
            @csrf
            <input type="hidden" name="action" value="disable">
            <div class="sa-modal-actions">
                <button type="button" class="sa-modal-btn sa-modal-btn-cancel" onclick="saModalClose('modalDisableEmergency')">
                    <i class="bi bi-x-circle me-1"></i> Batal
                </button>
                <button type="submit" class="sa-modal-btn sa-modal-btn-confirm success" onclick="saLoadingShow('Menonaktifkan Libur Darurat...')">
                    <i class="bi bi-check-circle-fill me-1"></i> Ya, Kembalikan Normal
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
