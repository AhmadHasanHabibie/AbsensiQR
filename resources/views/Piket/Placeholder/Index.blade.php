@extends('Layouts.LayoutGuruPiket')

@section('title', $pageTitle)

@section('content')

<div class="container-fluid py-4">

    <div class="card border-0 rounded-4 shadow-sm bg-white text-center p-5">
        <div class="card-body py-5">
            <div style="width:80px;height:80px;background:linear-gradient(135deg,#e0f2fe,#bae6fd);border-radius:24px;display:flex;align-items:center;justify-content:center;color:#0284c7;font-size:36px;" class="mx-auto mb-4">
                <i class="bi {{ $icon ?? 'bi-gear' }}"></i>
            </div>
            <h3 class="fw-bold text-dark mb-2">{{ $pageTitle }}</h3>
            <p class="text-muted max-w-md mx-auto mb-4 fs-6" style="max-width: 500px;">
                {{ $desc ?? 'Halaman ini sedang dalam tahap pengembangan untuk tahap berikutnya.' }}
            </p>
            <span class="badge bg-info text-dark px-3 py-2 fs-6 rounded-pill">
                <i class="bi bi-tools me-1"></i> Sedang Dalam Pengembangan
            </span>
            <div class="mt-4 pt-2">
                <a href="{{ route('piket.dashboard') }}" class="btn btn-outline-primary px-4 py-2 rounded-3">
                    <i class="bi bi-arrow-left me-2"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>

</div>

@endsection
