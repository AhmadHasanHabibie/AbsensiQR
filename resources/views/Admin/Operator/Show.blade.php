@extends('Layouts.LayoutAdmin')

@section('title', 'Detail Operator')

@section('content')

<div class="container-fluid">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Detail Operator</h3>
            <p class="text-muted mb-0">Detail informasi akun operator.</p>
        </div>
        <a href="{{ route('admin.operator.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 rounded-4">
                <div class="card-header bg-info text-white rounded-top-4">
                    <h5 class="mb-0"><i class="bi bi-person-vcard me-2"></i>Detail Data Operator</h5>
                </div>

                <div class="card-body p-4">

                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Nama Operator</div>
                        <div class="col-md-8">{{ $operator->name }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">NIP</div>
                        <div class="col-md-8">{{ $operator->nip }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Username</div>
                        <div class="col-md-8">{{ $operator->username }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Role</div>
                        <div class="col-md-8">
                            <span class="badge bg-primary">Operator</span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Status Akun</div>
                        <div class="col-md-8">
                            @if($operator->status)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-danger">Tidak Aktif</span>
                            @endif
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.operator.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i> Kembali
                        </a>
                        <a href="{{ route('admin.operator.edit', $operator->id) }}" class="btn btn-warning">
                            <i class="bi bi-pencil me-2"></i> Edit
                        </a>
                    </div>

                </div>

            </div>
        </div>
    </div>

</div>

@endsection
