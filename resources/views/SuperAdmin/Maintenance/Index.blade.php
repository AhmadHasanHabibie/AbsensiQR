@extends('Layouts.LayoutSuperAdmin')

@section('title', 'Pemeliharaan Sistem')

@section('content')
<div class="container-fluid px-0">

    {{-- PAGE HEADER --}}
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1" style="font-size:12px;">
                <li class="breadcrumb-item text-muted">Super Admin</li>
                <li class="breadcrumb-item active text-primary fw-semibold">Pemeliharaan Sistem</li>
            </ol>
        </nav>
        <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-tools text-primary me-2"></i> Pemeliharaan Sistem (Maintenance Mode)
        </h4>
        <p class="text-muted mb-0 fs-13">Kelola status pemeliharaan aplikasi secara terpusat — hanya Super Administrator yang tetap dapat mengakses sistem saat mode ini aktif</p>
    </div>

    <div class="row g-4">
        {{-- STATUS CARD --}}
        <div class="col-12 col-lg-6">
            <div class="card border-0 rounded-4 shadow-sm h-100"
                 style="border-left: 4px solid {{ $isMaintenance ? '#dc2626' : '#16a34a' }} !important;">
                <div class="card-header bg-white py-3 px-4 border-bottom rounded-top-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 bg-{{ $isMaintenance ? 'danger' : 'success' }} bg-opacity-10 text-{{ $isMaintenance ? 'danger' : 'success' }} rounded-3">
                            <i class="bi bi-{{ $isMaintenance ? 'exclamation-octagon-fill' : 'shield-check-fill' }} fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">Status Maintenance Mode</div>
                            <div class="text-muted fs-12">Kondisi sistem saat ini</div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-4 p-3 bg-{{ $isMaintenance ? 'danger' : 'success' }} bg-opacity-10 rounded-3">
                        <div class="flex-shrink-0">
                            <div class="p-3 bg-{{ $isMaintenance ? 'danger' : 'success' }} bg-opacity-15 rounded-circle">
                                <i class="bi bi-{{ $isMaintenance ? 'exclamation-octagon-fill' : 'check-circle-fill' }} text-{{ $isMaintenance ? 'danger' : 'success' }}" style="font-size:36px;"></i>
                            </div>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase letter-spacing-1">Status Mode</div>
                            <div class="fs-4 fw-bold text-{{ $isMaintenance ? 'danger' : 'success' }} font-monospace">
                                {{ $isMaintenance ? 'AKTIF' : 'NONAKTIF' }}
                            </div>
                            <div class="text-{{ $isMaintenance ? 'danger' : 'success' }} small mt-1 fw-semibold">
                                {{ $isMaintenance ? 'Sistem dikunci — pengguna biasa tidak dapat mengakses' : 'Seluruh modul sekolah berjalan normal' }}
                            </div>
                        </div>
                    </div>

                    @if($isMaintenance && !empty($maintenanceDetails))
                    <div class="bg-light p-3 rounded-3 border mb-3">
                        <div class="row g-2">
                            <div class="col-12">
                                <div class="text-muted fs-12 fw-semibold mb-1"><i class="bi bi-chat-text me-1"></i> Pesan untuk Pengguna:</div>
                                <div class="fw-semibold text-dark fs-13 font-monospace">{{ $maintenanceDetails['message'] ?? 'Sedang dalam pemeliharaan' }}</div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted fs-12 fw-semibold mb-1"><i class="bi bi-clock me-1"></i> Estimasi Selesai:</div>
                                <div class="fw-bold text-primary font-monospace fs-13">{{ $maintenanceDetails['estimate_completion'] ?? 'Segera' }}</div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted fs-12 fw-semibold mb-1"><i class="bi bi-calendar-event me-1"></i> Diaktifkan:</div>
                                <div class="fw-semibold text-dark font-monospace fs-12">
                                    {{ !empty($maintenanceDetails['activated_at']) ? \Carbon\Carbon::parse($maintenanceDetails['activated_at'])->translatedFormat('d M Y, H:i') : '—' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="alert alert-info bg-info bg-opacity-10 border-info small mb-0 rounded-3">
                        <i class="bi bi-shield-check me-1 text-info"></i>
                        <strong>Catatan Akses:</strong> Ketika Maintenance Aktif, Admin Sekolah, Guru, Operator, Piket, dan Siswa akan diarahkan ke halaman 503.
                        Super Administrator <strong>tetap dapat login dan beraktivitas</strong> secara normal.
                    </div>
                </div>
            </div>
        </div>

        {{-- CONTROL PANEL CARD --}}
        <div class="col-12 col-lg-6">
            <div class="card border-0 rounded-4 shadow-sm h-100">
                <div class="card-header bg-white py-3 px-4 border-bottom rounded-top-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-3">
                            <i class="bi bi-sliders fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">Control Panel Maintenance</div>
                            <div class="text-muted fs-12">Konfigurasi &amp; toggle status pemeliharaan</div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    {{-- Form hidden - di-submit via modal confirm --}}
                    <form id="maintenanceForm" action="{{ route('superadmin.maintenance.toggle') }}" method="POST">
                        @csrf
                        <input type="hidden" name="action" id="maintenanceAction" value="{{ $isMaintenance ? 'disable' : 'enable' }}">
                        <input type="hidden" name="message" id="maintenanceMsgHidden">
                        <input type="hidden" name="estimate_completion" id="maintenanceEstHidden">
                    </form>

                    <div class="mb-3">
                        <label for="maintenanceMsg" class="form-label text-muted small fw-semibold">
                            <i class="bi bi-chat-text me-1"></i> Pesan Pemeliharaan <span class="text-muted">(Opsional)</span>
                        </label>
                        <textarea id="maintenanceMsg" rows="3" class="form-control rounded-3"
                                  placeholder="Tuliskan pesan penjelasan pemeliharaan untuk pengguna..."></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="maintenanceEst" class="form-label text-muted small fw-semibold">
                            <i class="bi bi-clock me-1"></i> Estimasi Waktu Selesai <span class="text-muted">(Opsional)</span>
                        </label>
                        <input type="text" id="maintenanceEst" class="form-control rounded-3"
                               placeholder="Contoh: 30 Menit, 1 Jam, atau Pukul 12:00 WIB">
                    </div>

                    @if(!$isMaintenance)
                    <div class="d-grid">
                        <button type="button"
                                class="btn btn-danger fw-bold py-3 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2"
                                onclick="openMaintenanceModal('enable')">
                            <i class="bi bi-shield-slash-fill fs-5"></i>
                            <span>Aktifkan Maintenance Mode</span>
                        </button>
                    </div>
                    @else
                    <div class="d-grid">
                        <button type="button"
                                class="btn btn-success fw-bold py-3 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2"
                                onclick="openMaintenanceModal('disable')">
                            <i class="bi bi-shield-check fs-5"></i>
                            <span>Nonaktifkan Maintenance Mode</span>
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

{{-- CUSTOM MODAL: Aktifkan Maintenance --}}
<div class="sa-modal-overlay" id="modalMaintenanceEnable">
    <div class="sa-modal-box">
        <div class="sa-modal-icon-wrap danger">
            <i class="bi bi-shield-slash-fill"></i>
        </div>
        <div class="sa-modal-title">Aktifkan Maintenance Mode?</div>
        <div class="sa-modal-desc">
            Seluruh pengguna (Admin, Guru, Operator, Piket, Siswa) <strong>tidak dapat mengakses aplikasi</strong>
            dan akan diarahkan ke halaman pemeliharaan 503.<br><br>
            Super Administrator tetap dapat mengakses sistem secara normal.
        </div>
        <div class="sa-modal-actions">
            <button type="button" class="sa-modal-btn sa-modal-btn-cancel" onclick="saModalClose('modalMaintenanceEnable')">
                <i class="bi bi-x-circle me-1"></i> Batal
            </button>
            <button type="button" class="sa-modal-btn sa-modal-btn-confirm danger" id="btnEnableConfirm"
                    onclick="submitMaintenanceForm()">
                <i class="bi bi-shield-slash-fill me-1"></i> Ya, Aktifkan
            </button>
        </div>
    </div>
</div>

{{-- CUSTOM MODAL: Nonaktifkan Maintenance --}}
<div class="sa-modal-overlay" id="modalMaintenanceDisable">
    <div class="sa-modal-box">
        <div class="sa-modal-icon-wrap success">
            <i class="bi bi-shield-check-fill"></i>
        </div>
        <div class="sa-modal-title">Nonaktifkan Maintenance Mode?</div>
        <div class="sa-modal-desc">
            Akses pengguna (Admin, Guru, Operator, Piket, Siswa) akan <strong>dibuka kembali</strong>
            dan seluruh modul sekolah dapat digunakan secara normal.
        </div>
        <div class="sa-modal-actions">
            <button type="button" class="sa-modal-btn sa-modal-btn-cancel" onclick="saModalClose('modalMaintenanceDisable')">
                <i class="bi bi-x-circle me-1"></i> Batal
            </button>
            <button type="button" class="sa-modal-btn sa-modal-btn-confirm success" id="btnDisableConfirm"
                    onclick="submitMaintenanceForm()">
                <i class="bi bi-shield-check me-1"></i> Ya, Nonaktifkan
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openMaintenanceModal(action) {
        // Salin nilai input ke hidden form fields
        document.getElementById('maintenanceMsgHidden').value = document.getElementById('maintenanceMsg').value;
        document.getElementById('maintenanceEstHidden').value = document.getElementById('maintenanceEst').value;
        document.getElementById('maintenanceAction').value    = action;

        if (action === 'enable') {
            saModalOpen('modalMaintenanceEnable');
        } else {
            saModalOpen('modalMaintenanceDisable');
        }
    }

    function submitMaintenanceForm() {
        const actionVal = document.getElementById('maintenanceAction').value;
        const confirmBtnId = actionVal === 'enable' ? 'btnEnableConfirm' : 'btnDisableConfirm';
        const btn = document.getElementById(confirmBtnId);

        if (btn) {
            btn.innerHTML = '<span class="d-flex align-items-center gap-2"><span class="sa-loading-spinner"></span> Memproses...</span>';
            btn.disabled = true;
        }

        saLoadingShow('Mengubah status maintenance...');
        document.getElementById('maintenanceForm').submit();
    }
</script>
@endpush
