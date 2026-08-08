@extends('Layouts.LayoutGuruPiket')

@section('title', 'Profil Guru Piket')

@section('content')

<div class="container-fluid py-2">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Profil Guru Piket</h3>
            <p class="text-muted mb-0">Informasi akun terautentikasi Guru Piket.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 bg-white">
                <div class="card-header bg-info text-dark rounded-top-4 p-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-person-circle me-2"></i> Data Akun</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div style="width:64px;height:64px;background:linear-gradient(135deg,#0284c7,#38bdf8);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:24px;font-weight:700;">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0">{{ Auth::user()->name }}</h4>
                            <span class="badge bg-info text-dark px-3 py-1">Guru Piket</span>
                        </div>
                    </div>

                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted" width="35%">NIP</td>
                            <td class="fw-bold">: {{ Auth::user()->nip }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Username</td>
                            <td class="fw-bold">: {{ Auth::user()->username }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Role User</td>
                            <td class="fw-bold">: Guru Piket Operasional</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status Akun</td>
                            <td>: <span class="badge bg-success">Aktif</span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
