@extends('Layouts.LayoutGuruPiket')

@section('title', 'Riwayat Login Guru Piket')

@section('content')

<div class="container-fluid py-2">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Riwayat Login Guru Piket</h3>
            <p class="text-muted mb-0">Catatan aktivitas masuk dan keluar sistem Guru Piket.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 bg-white">
        <div class="card-header bg-primary text-white rounded-top-4 p-3">
            <h5 class="fw-bold mb-0"><i class="bi bi-clock-history me-2"></i> Log Riwayat Login</h5>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Waktu Login</th>
                            <th>Waktu Logout</th>
                            <th>IP Address</th>
                            <th>Browser & Perangkat</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($histories as $history)
                            <tr>
                                <td class="fw-semibold text-muted">{{ $loop->iteration + ($histories->currentPage() - 1) * $histories->perPage() }}</td>
                                <td><span class="badge bg-light text-dark border">{{ \Carbon\Carbon::parse($history->login_at)->isoFormat('D MMM YYYY, HH:mm:ss') }}</span></td>
                                <td>
                                    @if ($history->logout_at)
                                        <span class="badge bg-light text-dark border">{{ \Carbon\Carbon::parse($history->logout_at)->isoFormat('D MMM YYYY, HH:mm:ss') }}</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Sesi Aktif</span>
                                    @endif
                                </td>
                                <td><code>{{ $history->ip_address }}</code></td>
                                <td><small class="text-muted">{{ $history->browser }} ({{ $history->platform }})</small></td>
                                <td>
                                    @if ($history->login_status === 'success')
                                        <span class="badge bg-success">Berhasil</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($history->login_status) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="bi bi-clock-history fs-1 d-block mb-2"></i>
                                    <p class="mb-0">Belum ada riwayat login.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($histories->hasPages())
                <div class="mt-3 d-flex justify-content-center">
                    {{ $histories->links() }}
                </div>
            @endif
        </div>
    </div>

</div>

@endsection
