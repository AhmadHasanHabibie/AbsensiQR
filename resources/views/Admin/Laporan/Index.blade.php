@extends('Layouts.LayoutAdmin')

@section('title', 'Laporan Absensi')

@section('content')

<div class="container-fluid">

    {{-- Welcome Card --}}
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 rounded-4 overflow-hidden shadow-sm">
                <div class="card-body p-4" style="background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div style="width:56px;height:56px;background:rgba(255,255,255,0.15);border-radius:14px;display:flex;align-items:center;justify-content:center;">
                                <i class="bi bi-file-earmark-text text-white" style="font-size:28px;"></i>
                            </div>
                        </div>
                        <div class="ms-3 text-white">
                            <h4 class="fw-bold mb-1">Laporan Absensi</h4>
                            <p class="mb-0 opacity-75">Download laporan absensi seluruh siswa berdasarkan tanggal dan status kehadiran.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="card border-0 rounded-4 shadow-sm mb-4">
        <div class="card-header bg-white py-3 px-4 border-bottom">
            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-funnel text-primary me-2"></i>Filter Laporan Absensi</h5>
        </div>
        <div class="card-body p-4">

            <form method="GET">
                <div class="row g-3">

                    {{-- Periode --}}
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Periode</label>
                        <select name="period" id="periodSelect" class="form-select">
                            <option value="day" {{ request('period', 'day') == 'day' ? 'selected' : '' }}>Harian</option>
                            <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>Bulanan</option>
                            <option value="quarter" {{ request('period') == 'quarter' ? 'selected' : '' }}>Triwulan</option>
                            <option value="semester" {{ request('period') == 'semester' ? 'selected' : '' }}>Semester</option>
                            <option value="year" {{ request('period') == 'year' ? 'selected' : '' }}>Tahunan</option>
                        </select>
                    </div>

                    {{-- Day --}}
                    <div class="col-md-3 period-input period-day" style="{{ request('period', 'day') !== 'day' ? 'display:none;' : '' }}">
                        <label class="form-label fw-semibold">Tanggal</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                            <input type="date" name="date" class="form-control" value="{{ request('date', now()->toDateString()) }}">
                        </div>
                    </div>

                    {{-- Month --}}
                    <div class="col-md-3 period-input period-month" style="{{ request('period') !== 'month' ? 'display:none;' : '' }}">
                        <label class="form-label fw-semibold">Bulan</label>
                        <div class="d-flex">
                            <select name="month" class="form-select me-2">
                                @foreach (range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ request('month', now()->month) == $m ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::createFromDate(now()->year, $m, 1)->translatedFormat('F') }}
                                    </option>
                                @endforeach
                            </select>
                            <select name="year" class="form-select">
                                @php $current = now()->year; @endphp
                                @foreach (range($current - 5, $current + 1) as $y)
                                    <option value="{{ $y }}" {{ request('year', $current) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Quarter --}}
                    <div class="col-md-3 period-input period-quarter" style="{{ request('period') !== 'quarter' ? 'display:none;' : '' }}">
                        <label class="form-label fw-semibold">Triwulan</label>
                        <div class="d-flex">
                            <select name="quarter" class="form-select me-2">
                                @for ($i = 1; $i <= 4; $i++)
                                    <option value="{{ $i }}" {{ request('quarter', 1) == $i ? 'selected' : '' }}>Triwulan {{ $i }}</option>
                                @endfor
                            </select>
                            <select name="year" class="form-select">
                                @php $current = now()->year; @endphp
                                @foreach (range($current - 5, $current + 1) as $y)
                                    <option value="{{ $y }}" {{ request('year', $current) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Semester --}}
                    <div class="col-md-3 period-input period-semester" style="{{ request('period') !== 'semester' ? 'display:none;' : '' }}">
                        <label class="form-label fw-semibold">Semester</label>
                        <div class="d-flex">
                            <select name="semester" class="form-select me-2">
                                <option value="1" {{ request('semester', 1) == 1 ? 'selected' : '' }}>Semester 1 (Juli - Desember)</option>
                                <option value="2" {{ request('semester') == 2 ? 'selected' : '' }}>Semester 2 (Januari - Juni)</option>
                            </select>
                            <select name="year" class="form-select">
                                @php $current = now()->year; @endphp
                                @foreach (range($current - 5, $current + 1) as $y)
                                    <option value="{{ $y }}" {{ request('year', $current) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Year --}}
                    <div class="col-md-3 period-input period-year" style="{{ request('period') !== 'year' ? 'display:none;' : '' }}">
                        <label class="form-label fw-semibold">Tahun</label>
                        <select name="year" class="form-select">
                            @php $current = now()->year; @endphp
                            @foreach (range($current - 5, $current + 1) as $y)
                                <option value="{{ $y }}" {{ request('year', $current) == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status --}}
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Status Kehadiran</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                            <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>Izin</option>
                            <option value="sakit" {{ request('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                            <option value="alpa" {{ request('status') == 'alpa' ? 'selected' : '' }}>Alpa</option>
                        </select>
                    </div>

                    {{-- Search --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Cari Nama / NIS</label>
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Nama atau NIS" value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                        </div>
                    </div>

                    {{-- Button --}}
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check-circle me-2"></i> Konfirmasi
                        </button>
                    </div>

                </div>
            </form>

        </div>
    </div>

    {{-- Download Card --}}
    <div class="card border-0 rounded-4 shadow-sm mb-4">
        <div class="card-header bg-white py-3 px-4 border-bottom">
            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-download me-2 text-primary"></i>Download Laporan</h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <a href="{{ route('admin.laporan.pdf', request()->query()) }}" class="btn btn-danger w-100 py-3 rounded-3 d-flex align-items-center justify-content-center gap-2 fw-bold">
                        <i class="bi bi-file-earmark-pdf-fill fs-4"></i>
                        <span>Download PDF</span>
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="{{ route('admin.laporan.excel', request()->query()) }}" class="btn btn-success w-100 py-3 rounded-3 d-flex align-items-center justify-content-center gap-2 fw-bold">
                        <i class="bi bi-file-earmark-excel-fill fs-4"></i>
                        <span>Download Excel</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistik --}}
    <div class="card border-0 rounded-4 shadow-sm mb-4">
        <div class="card-header bg-white py-3 px-4 border-bottom">
            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-pie-chart text-primary me-2"></i>Statistik Kehadiran Sesuai Filter</h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-2.4 col-6">
                    <div class="p-3 rounded-3 text-center bg-success bg-opacity-10 border border-success border-opacity-25">
                        <h6 class="mb-1 text-success fw-bold">Hadir</h6>
                        <h3 class="mb-0 fw-bold text-success">{{ $hadir ?? 0 }}</h3>
                    </div>
                </div>
                <div class="col-md-2.4 col-6">
                    <div class="p-3 rounded-3 text-center bg-warning bg-opacity-10 border border-warning border-opacity-25">
                        <h6 class="mb-1 text-warning fw-bold">Izin</h6>
                        <h3 class="mb-0 fw-bold text-warning">{{ $izin ?? 0 }}</h3>
                    </div>
                </div>
                <div class="col-md-2.4 col-6">
                    <div class="p-3 rounded-3 text-center bg-danger bg-opacity-10 border border-danger border-opacity-25">
                        <h6 class="mb-1 text-danger fw-bold">Sakit</h6>
                        <h3 class="mb-0 fw-bold text-danger">{{ $sakit ?? 0 }}</h3>
                    </div>
                </div>
                <div class="col-md-2.4 col-6">
                    <div class="p-3 rounded-3 text-center bg-primary bg-opacity-10 border border-primary border-opacity-25">
                        <h6 class="mb-1 text-primary fw-bold">Alpa</h6>
                        <h3 class="mb-0 fw-bold text-primary">{{ $alpa ?? 0 }}</h3>
                    </div>
                </div>
                <div class="col-md-2.4 col-6">
                    <div class="p-3 rounded-3 text-center bg-secondary bg-opacity-10 border border-secondary border-opacity-25">
                        <h6 class="mb-1 text-secondary fw-bold">Terlambat</h6>
                        <h3 class="mb-0 fw-bold text-secondary">{{ $terlambat ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Data --}}
    <div class="card border-0 rounded-4 shadow-sm mb-4">
        <div class="card-header bg-white py-3 px-4 border-bottom">
            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-table text-primary me-2"></i>Data Rincian Presensi Siswa</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4" width="60">No</th>
                            <th>Nama Siswa</th>
                            <th>NIS</th>
                            <th>Kelas</th>
                            <th>Tanggal</th>
                            <th>Jam Masuk</th>
                            <th>Status</th>
                            <th class="pe-4 text-center">Terlambat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances ?? [] as $i => $attendance)
                            <tr>
                                <td class="ps-4 fw-semibold text-muted">{{ $i + 1 }}</td>
                                <td><strong class="text-dark">{{ $attendance->student->name }}</strong></td>
                                <td>{{ $attendance->student->nis }}</td>
                                <td><span class="badge bg-light text-dark border">{{ optional($attendance->student->schoolClass)->name ?? '-' }}</span></td>
                                <td>{{ $attendance->attendance_date->format('d-m-Y') }}</td>
                                <td><span class="badge bg-light text-dark border"><i class="bi bi-clock me-1"></i>{{ $attendance->check_in ?? '-' }}</span></td>
                                <td>
                                    @php
                                        $statusClass = match(strtolower($attendance->status)) {
                                            'hadir' => 'bg-success',
                                            'izin'  => 'bg-warning text-dark',
                                            'sakit' => 'bg-danger',
                                            default => 'bg-primary'
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }} font-monospace text-uppercase px-2.5 py-1">{{ ucfirst($attendance->status) }}</span>
                                </td>
                                <td class="pe-4 text-center">
                                    @if ($attendance->is_late)
                                        <span class="badge bg-secondary font-monospace">Ya</span>
                                    @else
                                        <span class="badge bg-light text-dark border font-monospace">Tidak</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                    Tidak ada data presensi yang sesuai kriteria filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script>
    (function() {
        const periodSelect = document.getElementById('periodSelect');
        const inputs = document.querySelectorAll('.period-input');

        function update() {
            const val = periodSelect.value;
            inputs.forEach(el => el.style.display = 'none');
            document.querySelectorAll('.period-' + val).forEach(el => el.style.display = 'block');
        }

        if (periodSelect) {
            update();
            periodSelect.addEventListener('change', update);
        }
    })();
</script>

@endsection
