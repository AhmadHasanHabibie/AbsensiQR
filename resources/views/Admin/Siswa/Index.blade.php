@extends('Layouts.LayoutAdmin')

@section('title', 'Data Siswa')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Data Siswa</h3>
            <p class="text-muted mb-0">Kelola seluruh data siswa SMKN 17 Jakarta.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.siswa.import.form') }}" class="btn btn-success">
                <i class="bi bi-file-earmark-excel me-1"></i> Import Excel
            </a>
            <a href="{{ route('admin.siswa.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Tambah Siswa
            </a>
        </div>
    </div>

    {{-- Alert Success --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Card Table --}}
    <div class="card border-0 rounded-4 shadow-sm">
        <div class="card-header bg-white py-3 px-4 border-bottom">
            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-people text-primary me-2"></i>Daftar Siswa Terdaftar</h5>
        </div>
        <div class="card-body p-4">

            {{-- Search --}}
            <form action="{{ route('admin.siswa.index') }}" method="GET" class="mb-4">
                <div class="row g-2">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0"
                                   value="{{ request('search') }}" placeholder="Cari nama, NIS atau username...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search me-1"></i> Cari
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('admin.siswa.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-arrow-clockwise me-1"></i> Reset
                        </a>
                    </div>
                </div>
            </form>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="60">No</th>
                            <th>Nama</th>
                            <th>NIS</th>
                            <th>Username</th>
                            <th>Kelas</th>
                            <th>Status</th>
                            <th width="130" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($siswas as $siswa)
                        <tr>
                            <td class="fw-semibold text-muted">{{ ($siswas->currentPage() - 1) * $siswas->perPage() + $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width:36px;height:36px;background:linear-gradient(135deg,#0d6efd,#6610f2);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;font-weight:600;">
                                        {{ substr($siswa->name, 0, 1) }}
                                    </div>
                                    <strong>{{ $siswa->name }}</strong>
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark">{{ $siswa->nis }}</span></td>
                            <td>{{ $siswa->username }}</td>
                            <td>{{ $siswa->schoolClass->name ?? '-' }}</td>
                            <td>
                                @if($siswa->status)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <a href="{{ route('admin.siswa.show', $siswa->id) }}"
                                       class="btn btn-info btn-sm text-white" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.siswa.edit', $siswa->id) }}"
                                       class="btn btn-warning btn-sm" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="bi bi-people fs-1 d-block mb-2"></i>
                                <p class="mb-0">Belum ada data siswa.</p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

        </div>
        @if($siswas->hasPages())
            <div class="p-3 bg-light border-top rounded-bottom-4">
                {{ $siswas->withQueryString()->links() }}
            </div>
        @endif
    </div>

</div>

@endsection
