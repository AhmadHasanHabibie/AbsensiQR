@extends('Layouts.LayoutGuru')

@section('title', 'Absensi')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-primary mb-1">
                Absensi
            </h3>
            <p class="text-muted mb-0">
                Kelola QR Code absensi siswa.
            </p>
        </div>
    </div>

    <div class="row g-4 mb-4">

        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4">

                <div class="card-body text-center py-5">

                    <div class="mb-3">
                        <i class="bi bi-box-arrow-in-right fs-1 text-primary"></i>
                    </div>

                    <h4 class="fw-bold">
                        Absensi Datang
                    </h4>

                    <p class="text-muted">
                        Buat QR Code untuk absensi masuk.
                    </p>

                    <a href="#" class="btn btn-primary px-4">
                        <i class="bi bi-qr-code me-2"></i>
                        Generate QR Datang
                    </a>

                </div>

            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4">

                <div class="card-body text-center py-5">

                    <div class="mb-3">
                        <i class="bi bi-box-arrow-right fs-1 text-success"></i>
                    </div>

                    <h4 class="fw-bold">
                        Absensi Pulang
                    </h4>

                    <p class="text-muted">
                        Buat QR Code untuk absensi pulang.
                    </p>

                    <a href="#" class="btn btn-success px-4">
                        <i class="bi bi-qr-code me-2"></i>
                        Generate QR Pulang
                    </a>

                </div>

            </div>
        </div>

    </div>


    <div class="card border-0 shadow-sm rounded-4">

        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold">
                Riwayat QR Hari Ini
            </h5>
        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>
                            <th>No</th>
                            <th>Jenis</th>
                            <th>Token</th>
                            <th>Kedaluwarsa</th>
                            <th>Status</th>
                        </tr>

                    </thead>

                    <tbody>

                        <tr>

                            <td colspan="5" class="text-center text-muted py-5">
                                Belum ada QR Code yang dibuat.
                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection