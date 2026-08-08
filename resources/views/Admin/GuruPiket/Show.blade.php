@extends('Layouts.LayoutAdmin')

@section('title', 'Detail Guru Piket')

@section('content')

<div class="container-fluid">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Detail Guru Piket</h3>
            <p class="text-muted mb-0">Informasi profil dan akun Guru Piket.</p>
        </div>
        <a href="{{ route('admin.guru-piket.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-info text-white rounded-top-4 p-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-person-badge me-2"></i> Profil Guru Piket</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div style="width:64px;height:64px;background:linear-gradient(135deg,#0d6efd,#0dcaf0);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:24px;font-weight:700;">
                            {{ substr($guruPiket->name, 0, 1) }}
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0">{{ $guruPiket->name }}</h4>
                            <span class="badge bg-primary px-3 py-1">Role: Guru Piket</span>
                        </div>
                    </div>

                    <table class="table table-borderless">
                        <tr>
                            <td class="text-muted" width="35%">NIP</td>
                            <td class="fw-bold">: {{ $guruPiket->nip }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Username</td>
                            <td class="fw-bold">: {{ $guruPiket->username }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status Akun</td>
                            <td>: 
                                @if($guruPiket->status)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Tidak Aktif</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Dibuat Pada</td>
                            <td>: {{ $guruPiket->created_at ? $guruPiket->created_at->isoFormat('D MMMM YYYY, HH:mm') : '-' }}</td>
                        </tr>
                    </table>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('admin.guru-piket.edit', $guruPiket->id) }}" class="btn btn-warning">
                            <i class="bi bi-pencil me-1"></i> Edit Akun
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
