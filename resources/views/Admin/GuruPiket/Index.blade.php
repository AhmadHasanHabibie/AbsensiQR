@extends('Layouts.LayoutAdmin')

@section('title', 'Data Guru Piket')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Data Guru Piket</h3>
            <p class="text-muted mb-0">Kelola akun dan petugas Guru Piket sekolah.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.guru-piket.import.form') }}" class="btn btn-success">
                <i class="bi bi-file-earmark-excel me-2"></i> Import Excel
            </a>
            <a href="{{ route('admin.guru-piket.template') }}" class="btn btn-outline-success">
                <i class="bi bi-download me-2"></i> Template
            </a>
            <a href="{{ route('admin.guru-piket.create') }}" class="btn btn-primary">
                <i class="bi bi-person-plus me-2"></i> Tambah Guru Piket
            </a>
        </div>
    </div>

    {{-- Alert Success --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center">
            <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Card Table --}}
    <div class="card border-0 rounded-4 shadow-sm">
        <div class="card-header bg-white py-3 px-4 border-bottom">
            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-person-badge text-warning me-2"></i>Daftar Petugas Guru Piket</h5>
        </div>
        <div class="card-body p-4">

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Guru Piket</th>
                            <th>NIP</th>
                            <th>Username</th>
                            <th>Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($guruPikets as $piket)
                        <tr>
                            <td class="fw-semibold text-muted">{{ $loop->iteration + ($guruPikets->currentPage() - 1) * $guruPikets->perPage() }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width:36px;height:36px;background:linear-gradient(135deg,#0d6efd,#0dcaf0);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;font-weight:600;">
                                        {{ substr($piket->name, 0, 1) }}
                                    </div>
                                    <strong>{{ $piket->name }}</strong>
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $piket->nip }}</span></td>
                            <td>{{ $piket->username }}</td>
                            <td>
                                @if($piket->status)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.guru-piket.show', $piket->id) }}"
                                       class="btn btn-info btn-sm text-white" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.guru-piket.edit', $piket->id) }}"
                                       class="btn btn-warning btn-sm" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="bi bi-person-badge fs-1 d-block mb-2"></i>
                                <p class="mb-0">Belum ada data Guru Piket.</p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

        </div>
        @if($guruPikets->hasPages())
            <div class="p-3 bg-light border-top rounded-bottom-4">
                {{ $guruPikets->links() }}
            </div>
        @endif
    </div>

</div>

@if(session('importResult'))
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function() {
    const result = @json(json_decode(session('importResult'), true));
    const total  = result.success + result.failed;

    let errHtml = '';
    if (result.errors && result.errors.length > 0) {
        errHtml = '<div class="text-start mt-3" style="max-height:220px;overflow-y:auto;font-size:0.85rem;">';
        result.errors.forEach(function(e) {
            errHtml += '<div class="mb-2 p-2 bg-danger bg-opacity-10 rounded border border-danger border-opacity-25">';
            errHtml += '<strong>Baris ' + e.row + '</strong> &mdash; ' + (e.name || '-') + '<br>';
            errHtml += '<span class="text-danger">' + e.error + '</span>';
            errHtml += '</div>';
        });
        errHtml += '</div>';
    }

    const icon  = result.failed === 0 ? 'success' : (result.success === 0 ? 'error' : 'warning');
    const title = result.failed === 0 ? 'Import Berhasil' : (result.success === 0 ? 'Import Gagal' : 'Import Selesai');

    Swal.fire({
        icon       : icon,
        title      : title,
        html       : '<div class="text-center mb-2">' +
                     '<table class="table table-sm table-bordered mx-auto" style="width:auto;min-width:200px">' +
                     '<tr><td class="text-start fw-semibold">Total Data</td><td class="fw-bold">' + total + '</td></tr>' +
                     '<tr><td class="text-start fw-semibold text-success">Berhasil</td><td class="fw-bold text-success">' + result.success + '</td></tr>' +
                     '<tr><td class="text-start fw-semibold text-danger">Gagal</td><td class="fw-bold text-danger">' + result.failed + '</td></tr>' +
                     '</table></div>' + errHtml,
        confirmButtonText  : 'Tutup',
        confirmButtonColor : '#0d6efd',
        width              : 560,
        customClass        : { htmlContainer: 'text-start' },
    });
})();
</script>
@endpush
@endif

@endsection
