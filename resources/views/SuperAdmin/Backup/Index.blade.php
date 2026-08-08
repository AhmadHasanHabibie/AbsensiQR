@extends('Layouts.LayoutSuperAdmin')

@section('title', 'Backup Database')

@section('content')
<div class="container-fluid px-0">

    {{-- PAGE HEADER --}}
    <div class="d-flex flex-column flex-md-row align-items-md-start justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1" style="font-size:12px;">
                    <li class="breadcrumb-item text-muted">Super Admin</li>
                    <li class="breadcrumb-item active text-primary fw-semibold">Backup Database</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-database-down text-primary me-2"></i> Manajemen Backup Database SQL
            </h4>
            <p class="text-muted mb-0 fs-13">Buat cadangan database instan dan unduh berkas SQL hasil dump</p>
        </div>
        {{-- Tombol tanpa confirm() browser — pakai custom modal --}}
        <button type="button"
                class="btn btn-primary fw-bold px-4 py-2 rounded-3 shadow-sm d-flex align-items-center gap-2 flex-shrink-0"
                onclick="saModalOpen('modalBackupCreate')">
            <i class="bi bi-database-add"></i>
            <span>Buat Backup SQL Baru</span>
        </button>
    </div>

    {{-- SUMMARY STATS --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4">
            <div class="card border-0 rounded-4 shadow-sm p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-3 flex-shrink-0">
                        <i class="bi bi-folder-fill fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted fs-12 fw-semibold">Total File Backup</div>
                        <div class="fw-bold text-dark fs-4 font-monospace">{{ $totalBackups ?? 0 }}</div>
                        <div class="text-muted fs-11">Berkas SQL tersimpan</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-0 rounded-4 shadow-sm p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-2 bg-success bg-opacity-10 text-success rounded-3 flex-shrink-0">
                        <i class="bi bi-hdd-fill fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted fs-12 fw-semibold">Total Ukuran</div>
                        <div class="fw-bold text-success fs-4 font-monospace">{{ $totalSizeFormatted ?? '—' }}</div>
                        <div class="text-muted fs-11">Kapasitas backup</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 rounded-4 shadow-sm p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-2 bg-info bg-opacity-10 text-info rounded-3 flex-shrink-0">
                        <i class="bi bi-clock-history fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted fs-12 fw-semibold">Backup Terakhir</div>
                        <div class="fw-bold text-dark fs-13 font-monospace">{{ $lastBackupDate ?? 'Belum ada' }}</div>
                        <div class="text-muted fs-11">Waktu pembuatan terakhir</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- BACKUP LIST CARD --}}
    <div class="card border-0 rounded-4 shadow-sm">
        <div class="card-header bg-white py-3 px-4 border-bottom rounded-top-4 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-3">
                    <i class="bi bi-folder-symlink-fill fs-5"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark">Riwayat &amp; Berkas Backup SQL</div>
                    <div class="text-muted fs-12">Semua dump database yang pernah dibuat</div>
                </div>
            </div>
            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle font-monospace px-3 py-2 fs-12">
                {{ $backups->total() }} Berkas
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3">Nama Berkas SQL</th>
                            <th class="py-3">Ukuran</th>
                            <th class="py-3">Status Dump</th>
                            <th class="py-3">Tanggal &amp; Waktu Buat</th>
                            <th class="pe-4 py-3 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($backups as $backup)
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-2 flex-shrink-0">
                                        <i class="bi bi-file-earmark-code"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-primary font-monospace fs-13">{{ $backup->filename }}</div>
                                        @if($backup->creator)
                                        <div class="text-muted fs-11">Dibuat oleh: {{ $backup->creator->name }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-3">
                                @php
                                    $sizeDisplay = $backup->formatted_size;
                                    $fileOk      = ($sizeDisplay !== 'Tidak tersedia' && $sizeDisplay !== 'File tidak tersedia');
                                @endphp
                                @if($fileOk)
                                <span class="fw-semibold text-dark font-monospace fs-13">{{ $sizeDisplay }}</span>
                                @else
                                <span class="text-danger small fw-semibold">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>File tidak tersedia
                                </span>
                                @endif
                            </td>
                            <td class="py-3">
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 font-monospace fs-12">
                                    {{ strtoupper($backup->status ?? 'DONE') }}
                                </span>
                            </td>
                            <td class="py-3">
                                <div class="font-monospace text-dark fs-13">{{ $backup->created_at->translatedFormat('d M Y') }}</div>
                                <div class="text-muted fs-11 font-monospace">{{ $backup->created_at->translatedFormat('H:i:s') }}</div>
                            </td>
                            <td class="pe-4 py-3 text-end">
                                <a href="{{ route('superadmin.backup.download', $backup->id) }}"
                                   class="btn btn-outline-primary btn-sm rounded-3 px-3 fw-semibold"
                                   onclick="saLoadingShow('Menyiapkan unduhan...')">
                                    <i class="bi bi-download me-1"></i> Unduh
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="p-0">
                                <div class="sa-empty-state">
                                    <div class="sa-empty-icon">
                                        <i class="bi bi-database-slash"></i>
                                    </div>
                                    <div class="sa-empty-title">Belum Ada Backup Database</div>
                                    <div class="sa-empty-desc">
                                        Belum ada berkas backup yang dibuat.<br>
                                        Klik <strong>Buat Backup SQL Baru</strong> untuk membuat cadangan pertama.
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($backups->hasPages())
            <div class="px-4 py-3 border-top">
                {{ $backups->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>

</div>

{{-- CUSTOM MODAL: Konfirmasi Buat Backup --}}
<div class="sa-modal-overlay" id="modalBackupCreate">
    <div class="sa-modal-box">
        <div class="sa-modal-icon-wrap info">
            <i class="bi bi-database-add"></i>
        </div>
        <div class="sa-modal-title">Konfirmasi Backup Database</div>
        <div class="sa-modal-desc">
            Apakah Anda yakin ingin membuat <strong>Backup Database SQL</strong> baru saat ini?<br>
            Proses ini akan men-dump seluruh tabel database ke dalam berkas <code>.sql</code>.
        </div>
        <div class="sa-modal-actions">
            <button type="button" class="sa-modal-btn sa-modal-btn-cancel" onclick="saModalClose('modalBackupCreate')">
                <i class="bi bi-x-circle me-1"></i> Batal
            </button>
            <button type="button" class="sa-modal-btn sa-modal-btn-confirm info" id="btnBackupConfirm"
                    onclick="doCreateBackup()">
                <i class="bi bi-database-add me-1"></i> Ya, Buat Backup
            </button>
        </div>
    </div>
</div>

<form id="formCreateBackup" action="{{ route('superadmin.backup.store') }}" method="POST" class="d-none">
    @csrf
</form>
@endsection

@push('scripts')
<script>
    function doCreateBackup() {
        const btn = document.getElementById('btnBackupConfirm');
        if (btn) {
            btn.innerHTML = '<span class="d-flex align-items-center gap-2"><span class="sa-loading-spinner"></span> Memproses...</span>';
            btn.disabled = true;
        }
        saLoadingShow('Sedang membuat backup database...');
        document.getElementById('formCreateBackup').submit();
    }
</script>
@endpush
