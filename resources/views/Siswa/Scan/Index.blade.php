@extends('Layouts.LayoutSiswa')

@section('title', 'Scan QR Absensi')

@section('content')

@php
    $isScanOpen  = \App\Services\AttendanceTimeService::isAttendanceOpen();
    $isPastLimit = \App\Services\AttendanceTimeService::isAttendanceExpired();
@endphp

<div class="container-fluid py-2">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-primary mb-1">Scan QR Absensi Siswa</h3>
            <p class="text-muted mb-0">Halaman pemindaian QR Code kehadiran tepat waktu (03:00 - 06:30 WIB).</p>
        </div>
        <span class="badge fs-6 px-3 py-2 rounded-3 {{ $isScanOpen ? 'bg-success' : 'bg-danger' }}">
            <i class="bi {{ $isScanOpen ? 'bi-check-circle-fill' : 'bi-clock-history' }} me-1"></i>
            {{ $isScanOpen ? 'SCAN DIBUKA (03:00 - 06:30 WIB)' : 'SCAN DITUTUP (Pukul 06:31 WIB)' }}
        </span>
    </div>

    @if(!$isScanOpen)
        {{-- EMPTY STATE APABILA DITUTUP / PAST LIMIT --}}
        <div class="row justify-content-center my-4">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 text-center p-4 p-md-5 bg-white">
                    <div class="card-body">
                        <i class="bi bi-clock-history text-secondary display-1 d-block mb-3 opacity-50"></i>
                        <h4 class="fw-bold text-dark mb-2">Maaf, waktu absensi QR hari ini telah berakhir.</h4>
                        <p class="text-muted mb-4 small">Sistem absensi QR tepat waktu beroperasi mulai pukul <strong>03:00:00 WIB</strong> dan ditutup otomatis pada pukul <strong>06:31:00 WIB</strong>. Apabila Anda terlambat hadir di sekolah, silakan melapor kepada <strong>Petugas Operator</strong> di gerbang utama.</p>
                        <a href="{{ route('siswa.dashboard') }}" class="btn btn-primary px-4 py-2.5 rounded-pill shadow-sm fw-semibold">
                            <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard Siswa
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- ACTIVE SCANNER SETUP APABILA DALAM JENDELA WAKTU 03:00 - 06:30 WIB --}}
        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-camera-video me-2"></i> Kamera Scanner Siswa</h5>
                    </div>
                    <div class="card-body">
                        <div id="reader" class="border rounded-4 d-flex justify-content-center align-items-center" style="height: 420px; background:#f8f9fa;">
                            <div class="text-center">
                                <i class="bi bi-camera-fill display-1 text-primary"></i>
                                <h5 class="mt-3 fw-bold">Kamera Belum Aktif</h5>
                                <p class="text-muted mb-4">Tekan tombol di bawah untuk mulai melakukan scan QR.</p>
                                <button id="startScanner" class="btn btn-primary px-4 py-2 rounded-pill">
                                    <i class="bi bi-camera-video me-2"></i> Mulai Scan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">Status Scan</h5>
                    </div>
                    <div class="card-body text-center py-4">
                        <i class="bi bi-qr-code-scan display-3 text-secondary mb-3"></i>
                        <h5 class="fw-bold">Belum Ada QR</h5>
                        <p class="text-muted mb-0 small">Hasil scan QR akan muncul di sini.</p>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">Petunjuk Absensi</h5>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0 text-muted small">
                            <li class="mb-2">Arahkan kamera tepat ke QR Code.</li>
                            <li class="mb-2">Waktu tutup otomatis adalah pukul 06:31:00 WIB.</li>
                            <li>Tunggu konfirmasi absensi berhasil dari layar.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>

@endsection