@extends('Layouts.LayoutAdmin')

@section('title', 'Data Guru')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Data Guru</h3>
            <p class="text-muted mb-0">Kelola akun guru dan wali kelas.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.guru.import.form') }}" class="btn btn-success">
                <i class="bi bi-file-earmark-excel me-2"></i> Import Excel
            </a>
            <a href="{{ route('admin.guru.template') }}" class="btn btn-outline-success">
                <i class="bi bi-download me-2"></i> Template
            </a>
            <a href="{{ route('admin.guru.create') }}" class="btn btn-primary">
                <i class="bi bi-person-plus me-2"></i> Tambah Guru
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
            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-person-workspace text-success me-2"></i>Daftar Tenaga Pengajar (Guru)</h5>
        </div>
        <div class="card-body p-4">

            {{-- Search Bar --}}
            <form action="{{ route('admin.guru.index') }}" method="GET" class="mb-4">
                <div class="row g-2">
                    <div class="col-md-6 col-lg-5">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0"
                                   value="{{ request('search') }}" placeholder="Cari Nama, NIP, atau Username Guru...">
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <button type="submit" class="btn btn-primary w-100 fw-semibold">
                            <i class="bi bi-search me-1"></i> Cari
                        </button>
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <a href="{{ route('admin.guru.index') }}" class="btn btn-outline-secondary w-100 fw-semibold">
                            <i class="bi bi-arrow-clockwise me-1"></i> Refresh
                        </a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Guru</th>
                            <th>NIP</th>
                            <th>Username</th>
                            <th>Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($gurus as $guru)
                        <tr>
                            <td class="fw-semibold text-muted">{{ $loop->iteration + ($gurus->currentPage() - 1) * $gurus->perPage() }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width:36px;height:36px;background:linear-gradient(135deg,#198754,#20c997);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;font-weight:600;">
                                        {{ substr($guru->name, 0, 1) }}
                                    </div>
                                    <strong>{{ $guru->name }}</strong>
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark">{{ $guru->nip }}</span></td>
                            <td>{{ $guru->username }}</td>
                            <td>
                                @if($guru->status)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.guru.show',$guru->id) }}"
                                       class="btn btn-info btn-sm text-white" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.guru.edit',$guru->id) }}"
                                       class="btn btn-warning btn-sm" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="bi bi-person-workspace fs-1 d-block mb-2"></i>
                                <p class="mb-0">
                                    @if(request('search'))
                                        Tidak ada data yang cocok dengan pencarian "<strong>{{ request('search') }}</strong>".
                                    @else
                                        Belum ada data guru.
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

        </div>
        @if($gurus->hasPages())
            <div class="p-3 bg-light border-top rounded-bottom-4">
                {{ $gurus->links() }}
            </div>
        @endif
    </div>

</div>

@endsection
