@extends('Layouts.LayoutAdmin')

@section('title', 'Detail Guru')

@section('content')

<div class="container-fluid">


    <div class="row">


        <div class="col-md-8">


            <div class="card border-0 shadow-sm">


                <div class="card-header bg-info text-white">


                    <h5 class="mb-0">


                        <i class="bi bi-person-vcard me-2"></i>

                        Detail Data Guru


                    </h5>


                </div>




                <div class="card-body">


                    <div class="row mb-3">


                        <div class="col-md-4 fw-bold">
                            Nama Guru
                        </div>


                        <div class="col-md-8">
                            {{ $guru->name }}
                        </div>


                    </div>




                    <div class="row mb-3">


                        <div class="col-md-4 fw-bold">
                            NIP
                        </div>


                        <div class="col-md-8">
                            {{ $guru->nip }}
                        </div>


                    </div>




                    <div class="row mb-3">


                        <div class="col-md-4 fw-bold">
                            Username
                        </div>


                        <div class="col-md-8">
                            {{ $guru->username }}
                        </div>


                    </div>




                    <div class="row mb-3">


                        <div class="col-md-4 fw-bold">
                            Role
                        </div>


                        <div class="col-md-8">


                            <span class="badge bg-primary">

                                Guru

                            </span>


                        </div>


                    </div>




                    <div class="row mb-3">


                        <div class="col-md-4 fw-bold">
                            Status Akun
                        </div>


                        <div class="col-md-8">


                            @if($guru->status)


                                <span class="badge bg-success">

                                    Aktif

                                </span>


                            @else


                                <span class="badge bg-danger">

                                    Tidak Aktif

                                </span>


                            @endif


                        </div>


                    </div>




                    <hr>




                    <div class="row mb-3">


                        <div class="col-md-4 fw-bold">
                            Wali Kelas
                        </div>


                        <div class="col-md-8">


                            @if($guru->homeroomClass)


                                <span class="badge bg-warning text-dark">

                                    {{ $guru->homeroomClass->name }}

                                </span>


                            @else


                                <span class="text-muted">

                                    Belum menjadi wali kelas

                                </span>


                            @endif


                        </div>


                    </div>




                    <div class="mt-4 d-flex justify-content-between">


                        <a href="{{ route('admin.guru.index') }}"
                           class="btn btn-secondary">


                            <i class="bi bi-arrow-left me-2"></i>

                            Kembali


                        </a>





                        <a href="{{ route('admin.guru.edit', $guru->id) }}"
                           class="btn btn-warning">


                            <i class="bi bi-pencil me-2"></i>

                            Edit


                        </a>


                    </div>



                </div>


            </div>


        </div>


    </div>


</div>


@endsection