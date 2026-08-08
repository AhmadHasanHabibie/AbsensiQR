@extends('Layouts.LayoutAdmin')

@section('title', 'Edit Guru')

@section('content')

<div class="container-fluid">


    <div class="row">

        <div class="col-md-8">


            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-pencil-square text-warning me-2"></i> Edit Data Guru
                    </h5>
                </div>



                <div class="card-body">


                    <form action="{{ route('admin.guru.update', $guru->id) }}"
                          method="POST">


                        @csrf

                        @method('PUT')



                        {{-- Nama Guru --}}

                        <div class="mb-3">


                            <label class="form-label fw-semibold">
                                Nama Guru
                            </label>


                            <input type="text"
                                   name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $guru->name) }}">


                            @error('name')

                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>

                            @enderror


                        </div>




                        {{-- NIP --}}

                        <div class="mb-3">


                            <label class="form-label fw-semibold">
                                NIP
                            </label>


                            <input type="text"
                                   name="nip"
                                   class="form-control @error('nip') is-invalid @enderror"
                                   value="{{ old('nip', $guru->nip) }}">


                            @error('nip')

                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>

                            @enderror


                        </div>




                        {{-- Username --}}

                        <div class="mb-3">


                            <label class="form-label fw-semibold">
                                Username
                            </label>


                            <input type="text"
                                   name="username"
                                   class="form-control @error('username') is-invalid @enderror"
                                   value="{{ old('username', $guru->username) }}">


                            @error('username')

                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>

                            @enderror


                        </div>




                        {{-- Password Baru --}}

                        <div class="mb-3">


                            <label class="form-label fw-semibold">

                                Password Baru

                                <small class="text-muted">
                                    (kosongkan jika tidak ingin mengganti)
                                </small>

                            </label>


                            <input type="password"
                                   name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Masukkan password baru">


                            @error('password')

                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>

                            @enderror


                        </div>




                        {{-- Status --}}

                        <div class="mb-3">


                            <label class="form-label fw-semibold">
                                Status
                            </label>


                            <select name="status"
                                    class="form-select @error('status') is-invalid @enderror">


                                <option value="1"
                                    {{ old('status', $guru->status) == 1 ? 'selected' : '' }}>

                                    Aktif

                                </option>


                                <option value="0"
                                    {{ old('status', $guru->status) == 0 ? 'selected' : '' }}>

                                    Tidak Aktif

                                </option>


                            </select>


                            @error('status')

                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>

                            @enderror


                        </div>




                        <div class="d-flex justify-content-between">


                            <a href="{{ route('admin.guru.index') }}"
                               class="btn btn-secondary">


                                <i class="bi bi-arrow-left me-2"></i>

                                Kembali


                            </a>





                            <button type="submit"
                                    class="btn btn-warning">


                                <i class="bi bi-save me-2"></i>

                                Update Guru


                            </button>


                        </div>



                    </form>


                </div>


            </div>


        </div>


    </div>


</div>


@endsection