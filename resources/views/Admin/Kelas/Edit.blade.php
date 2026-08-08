@extends('Layouts.LayoutAdmin')

@section('title', 'Edit Kelas')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="fw-bold mb-1">
                Edit Kelas
            </h3>

            <p class="text-muted mb-0">
                Perbarui informasi kelas.
            </p>

        </div>

        <a href="{{ route('admin.kelas.index') }}" class="btn btn-secondary">

            <i class="bi bi-arrow-left me-1"></i>

            Kembali

        </a>

    </div>


    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 px-4 border-bottom">
            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-pencil-square text-warning me-2"></i>Formulir Edit Data Kelas</h5>
        </div>
        <div class="card-body p-4">

            <form
                action="{{ route('admin.kelas.update', $class->id) }}"
                method="POST">

                @csrf
                @method('PUT')

                <div class="row">

                    {{-- Nama Kelas --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold">
                            Nama Kelas
                        </label>

                        <input
                            type="text"
                            name="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $class->name) }}"
                            placeholder="Contoh : XII RPL 1">

                        @error('name')

                            <div class="invalid-feedback">

                                {{ $message }}

                            </div>

                        @enderror

                    </div>


                    {{-- Wali Kelas --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold">
                            Wali Kelas
                        </label>

                        <select
                            name="teacher_id"
                            class="form-select @error('teacher_id') is-invalid @enderror">

                            <option value="">
                                -- Pilih Wali Kelas --
                            </option>

                            @foreach($teachers as $teacher)

                                <option
                                    value="{{ $teacher->id }}"
                                    {{ old('teacher_id', $class->teacher_id) == $teacher->id ? 'selected' : '' }}>

                                    {{ $teacher->name }} ({{ $teacher->nip }})

                                </option>

                            @endforeach

                        </select>

                        @error('teacher_id')

                            <div class="invalid-feedback">

                                {{ $message }}

                            </div>

                        @enderror

                    </div>


                    {{-- Status --}}
                    <div class="col-md-6 mb-4">

                        <label class="form-label fw-semibold">
                            Status
                        </label>

                        <select
                            name="status"
                            class="form-select @error('status') is-invalid @enderror">

                            <option
                                value="1"
                                {{ old('status', $class->status) ? 'selected' : '' }}>

                                Aktif

                            </option>

                            <option
                                value="0"
                                {{ old('status', $class->status) == 0 ? 'selected' : '' }}>

                                Nonaktif

                            </option>

                        </select>

                        @error('status')

                            <div class="invalid-feedback">

                                {{ $message }}

                            </div>

                        @enderror

                    </div>

                </div>

                <hr>

                <div class="d-flex justify-content-end">

                    <a
                        href="{{ route('admin.kelas.index') }}"
                        class="btn btn-secondary me-2">

                        <i class="bi bi-arrow-left me-1"></i>

                        Kembali

                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary">

                        <i class="bi bi-check-circle me-1"></i>

                        Update

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection