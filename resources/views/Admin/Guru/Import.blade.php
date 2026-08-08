@extends('Layouts.LayoutAdmin')

@section('title', 'Import Data Guru')

@section('content')

<div class="container-fluid">

    {{-- Alert Success --}}
    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            <i class="bi bi-check-circle-fill me-2"></i>

            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    {{-- Alert Error --}}
    @if(session('error'))

        <div class="alert alert-danger alert-dismissible fade show">

            <i class="bi bi-x-circle-fill me-2"></i>

            {{ session('error') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    {{-- Validation --}}
    @if($errors->any())

        <div class="alert alert-danger alert-dismissible fade show">

            <i class="bi bi-exclamation-triangle-fill me-2"></i>

            <strong>Terjadi Kesalahan :</strong>

            <ul class="mb-0 mt-2">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>

        </div>

    @endif



    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="fw-bold text-primary mb-1">

                Import Data Guru

            </h3>

            <p class="text-muted mb-0">

                Upload file Excel (.xlsx / .xls) untuk menambahkan data guru secara otomatis.

            </p>

        </div>

        <a
            href="{{ route('admin.guru.index') }}"
            class="btn btn-secondary">

            <i class="bi bi-arrow-left me-2"></i>

            Kembali

        </a>

    </div>



    <div class="row">

        <div class="col-lg-8">

            <div class="card border-0 shadow-sm">

                <div class="card-header bg-primary text-white">

                    <h5 class="mb-0">

                        <i class="bi bi-file-earmark-excel me-2"></i>

                        Upload File Excel

                    </h5>

                </div>

                <div class="card-body">

                    <form
                        action="{{ route('admin.guru.import') }}"
                        method="POST"
                        enctype="multipart/form-data">

                        @csrf

                        <div class="mb-4">

                            <label class="form-label fw-semibold">

                                File Excel

                            </label>

                            <input
                                type="file"
                                name="file"
                                class="form-control"
                                accept=".xlsx,.xls"
                                required>

                            <small class="text-muted">

                                Format yang didukung:
                                .xlsx dan .xls

                            </small>

                        </div>

                        <div class="d-flex gap-2">

                            <button
                                type="submit"
                                class="btn btn-success">

                                <i class="bi bi-upload me-2"></i>

                                Import Sekarang

                            </button>

                            <a
                                href="{{ asset('templates/template_guru.xlsx') }}"
                                class="btn btn-outline-primary">

                                <i class="bi bi-download me-2"></i>

                                Download Template

                            </a>

                        </div>

                    </form>

                </div>

            </div>

        </div>



        <div class="col-lg-4">

            <div class="card border-0 shadow-sm">

                <div class="card-header bg-info text-white">

                    <h5 class="mb-0">

                        <i class="bi bi-info-circle-fill me-2"></i>

                        Petunjuk

                    </h5>

                </div>

                <div class="card-body">

                    <ol class="mb-0">

                        <li class="mb-2">

                            Download template Excel.

                        </li>

                        <li class="mb-2">

                            Isi data guru sesuai format.

                        </li>

                        <li class="mb-2">

                            Jangan mengubah nama kolom.

                        </li>

                        <li class="mb-2">

                            Data yang valid akan otomatis disimpan.

                        </li>

                        <li class="mb-2">

                            Jika ada satu baris salah, hanya baris tersebut yang gagal.

                        </li>

                        <li>

                            Data lainnya tetap berhasil diimport.

                        </li>

                    </ol>

                </div>

            </div>



            <div class="card border-0 shadow-sm mt-3">

                <div class="card-header bg-warning">

                    <h5 class="mb-0">

                        <i class="bi bi-table me-2"></i>

                        Format Kolom

                    </h5>

                </div>

                <div class="card-body">

                    <ul class="mb-0">

                        <li>name</li>

                        <li>nip</li>

                        <li>username</li>

                        <li>password</li>

                        <li>status</li>

                    </ul>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection