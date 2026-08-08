@extends('Layouts.LayoutAdmin')

@section('title', 'Detail Kelas')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="fw-bold mb-1">
                Detail Kelas
            </h3>

            <p class="text-muted mb-0">
                Informasi lengkap data kelas.
            </p>

        </div>

        <a
            href="{{ route('admin.kelas.index') }}"
            class="btn btn-secondary">

            <i class="bi bi-arrow-left me-1"></i>

            Kembali

        </a>

    </div>


    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-primary text-white">

            <h5 class="mb-0">

                <i class="bi bi-building me-2"></i>

                Informasi Kelas

            </h5>

        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-6 mb-3">

                    <label class="fw-semibold text-muted">

                        Nama Kelas

                    </label>

                    <div>

                        {{ $class->name }}

                    </div>

                </div>


                <div class="col-md-6 mb-3">

                    <label class="fw-semibold text-muted">

                        Wali Kelas

                    </label>

                    <div>

                        {{ $class->teacher?->name ?? '-' }}

                    </div>

                </div>


                <div class="col-md-6 mb-3">

                    <label class="fw-semibold text-muted">

                        NIP Wali Kelas

                    </label>

                    <div>

                        {{ $class->teacher?->nip ?? '-' }}

                    </div>

                </div>


                <div class="col-md-6 mb-3">

                    <label class="fw-semibold text-muted">

                        Jumlah Siswa

                    </label>

                    <div>

                        {{ $class->students->count() }} Siswa

                    </div>

                </div>


                <div class="col-md-6 mb-3">

                    <label class="fw-semibold text-muted">

                        Status

                    </label>

                    <div>

                        @if($class->status)

                            <span class="badge bg-success">

                                Aktif

                            </span>

                        @else

                            <span class="badge bg-danger">

                                Nonaktif

                            </span>

                        @endif

                    </div>

                </div>

            </div>

        </div>

    </div>


    <div class="card border-0 shadow-sm">

        <div class="card-header bg-primary text-white">

            <h5 class="mb-0">

                <i class="bi bi-people me-2"></i>

                Daftar Siswa

            </h5>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-light">

                        <tr>

                            <th width="70">
                                No
                            </th>

                            <th>
                                NIS
                            </th>

                            <th>
                                Nama
                            </th>

                            <th>
                                Username
                            </th>

                            <th class="text-center">
                                Status
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($class->students as $student)

                            <tr>

                                <td>

                                    {{ $loop->iteration }}

                                </td>

                                <td>

                                    {{ $student->nis }}

                                </td>

                                <td>

                                    {{ $student->name }}

                                </td>

                                <td>

                                    {{ $student->username }}

                                </td>

                                <td class="text-center">

                                    @if($student->status)

                                        <span class="badge bg-success">

                                            Aktif

                                        </span>

                                    @else

                                        <span class="badge bg-danger">

                                            Nonaktif

                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="5"
                                    class="text-center text-muted py-4">

                                    Belum ada siswa pada kelas ini.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection