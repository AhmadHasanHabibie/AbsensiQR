@extends('Layouts.LayoutAdmin')

@section('title', 'Data Kelas')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Data Kelas</h3>
            <p class="text-muted mb-0">Kelola seluruh data kelas SMKN 17 Jakarta.</p>
        </div>
        <a href="{{ route('admin.kelas.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Tambah Kelas
        </a>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center">
            <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center">
            <i class="bi bi-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Card Table --}}
    <div class="card border-0 rounded-4 shadow-sm">
        <div class="card-header bg-white py-3 px-4 border-bottom">
            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-building text-primary me-2"></i>Daftar Kelas (Rombongan Belajar)</h5>
        </div>
        <div class="card-body p-4">

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="70">No</th>
                            <th>Nama Kelas</th>
                            <th>Wali Kelas</th>
                            <th class="text-center">Jumlah Siswa</th>
                            <th class="text-center">Status</th>
                            <th width="130" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($classes as $class)
                        <tr>
                            <td class="fw-semibold text-muted">{{ $loop->iteration + ($classes->firstItem() - 1) }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width:36px;height:36px;background:linear-gradient(135deg,#ffc107,#fd7e14);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;font-weight:600;">
                                        {{ substr($class->name, 0, 1) }}
                                    </div>
                                    <strong>{{ $class->name }}</strong>
                                </div>
                            </td>
                            <td>{{ $class->teacher?->name ?? '-' }}</td>
                            <td class="text-center">
                                <span class="badge bg-info text-white">{{ $class->students()->count() }}</span>
                            </td>
                            <td class="text-center">
                                @if($class->status)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <a href="{{ route('admin.kelas.show', $class->id) }}"
                                       class="btn btn-info btn-sm text-white" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.kelas.edit', $class->id) }}"
                                       class="btn btn-warning btn-sm" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="bi bi-building fs-1 d-block mb-2"></i>
                                <p class="mb-0">Belum ada data kelas.</p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

        </div>
        @if($classes->hasPages())
            <div class="p-3 bg-light border-top rounded-bottom-4">
                {{ $classes->links() }}
            </div>
        @endif
    </div>

</div>

@endsection
