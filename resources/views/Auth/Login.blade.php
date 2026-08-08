@extends('Layouts.LayoutAuth')

@section('title', 'Masuk Ke Sistem')

@section('content')

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif

<form action="{{ route('login') }}" method="POST">

    @csrf

    <div class="mb-3">

        <label class="form-label fw-semibold">
            Nama Pengguna (Username)
        </label>

        <div class="input-group">

            <span class="input-group-text">
                <i class="bi bi-person"></i>
            </span>

            <input
                type="text"
                name="username"
                class="form-control @error('username') is-invalid @enderror"
                placeholder="Masukkan username Anda"
                value="{{ old('username') }}"
                autofocus
            >

        </div>

        @error('username')
            <div class="text-danger small mt-1">
                {{ $message }}
            </div>
        @enderror

    </div>

    <div class="mb-4">

        <label class="form-label fw-semibold">
            Kata Sandi (Password)
        </label>

        <div class="input-group">

            <span class="input-group-text">
                <i class="bi bi-lock"></i>
            </span>

            <input
                type="password"
                name="password"
                id="password"
                class="form-control @error('password') is-invalid @enderror"
                placeholder="Masukkan kata sandi Anda"
            >

            <button
                class="btn btn-outline-secondary"
                type="button"
                id="togglePassword"
                title="Tampilkan / Sembunyikan Password">

                <i class="bi bi-eye" id="toggleIcon"></i>

            </button>

        </div>

        @error('password')
            <div class="text-danger small mt-1">
                {{ $message }}
            </div>
        @enderror

    </div>

    <div class="d-grid">

        <button type="submit" class="btn btn-primary">

            <i class="bi bi-box-arrow-in-right me-2"></i>

            Masuk Ke Sistem

        </button>

    </div>

</form>

@endsection

@push('js')

<script>

const password = document.getElementById('password');
const togglePassword = document.getElementById('togglePassword');
const toggleIcon = document.getElementById('toggleIcon');

togglePassword.addEventListener('click', function(){

    if(password.type === 'password'){

        password.type = 'text';

        toggleIcon.classList.remove('bi-eye');
        toggleIcon.classList.add('bi-eye-slash');

    }else{

        password.type = 'password';

        toggleIcon.classList.remove('bi-eye-slash');
        toggleIcon.classList.add('bi-eye');

    }

});

</script>

@endpush