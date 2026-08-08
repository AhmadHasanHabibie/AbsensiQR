@extends('Layouts.LayoutOperator')

@section('title', 'Profil Operator')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-primary mb-1">Profil Operator</h3>
            <p class="text-muted mb-0">Informasi akun operator.</p>
        </div>
    </div>

    <div class="row">

        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body text-center py-5">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Operator') }}&background=0D6EFD&color=fff&size=150"
                        class="rounded-circle mb-3"
                        width="130"
                        height="130"
                        alt="Foto Profil">

                    <h4 class="fw-bold mb-1">
                        {{ Auth::user()->name ?? 'Operator' }}
                    </h4>

                    <p class="text-muted mb-0">
                        Operator
                    </p>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Informasi Akun</h5>
                </div>

                <div class="card-body">

                    <div class="row mb-3">
                        <label class="col-md-3 fw-semibold">Nama</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" value="{{ Auth::user()->name ?? '-' }}" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-md-3 fw-semibold">Username</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" value="{{ Auth::user()->username ?? '-' }}" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-md-3 fw-semibold">NIP</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" value="{{ Auth::user()->nip ?? '-' }}" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-md-3 fw-semibold">Role</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" value="Operator" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-md-3 fw-semibold">Status</label>
                        <div class="col-md-9">
                            @if(Auth::check() && Auth::user()->status)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-danger">Tidak Aktif</span>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

</div>

@endsection
