@extends('Layouts.LayoutGuru')

@section('title', 'Laporan Absensi')

@section('content')
<div class="container-fluid">
    <div class="card border-0 rounded-4 overflow-hidden mb-4">
        <div class="card-body p-4" style="background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);">
            <div class="d-flex align-items-center">
                <div style="width:56px;height:56px;background:rgba(255,255,255,0.15);border-radius:14px;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-file-earmark-text text-white" style="font-size:28px;"></i>
                </div>
                <div class="ms-3 text-white">
                    <h4 class="fw-bold mb-1">Laporan Absensi</h4>
                    <p class="mb-0 opacity-75">Rekap absensi kelas <strong>{{ $class->name }}</strong> berdasarkan periode dan status kehadiran.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 rounded-4 mb-4">
        <div class="card-header bg-primary text-white rounded-top-4"><h5 class="mb-0"><i class="bi bi-filter me-2"></i>Filter Laporan</h5></div>
        <div class="card-body p-4">
            <form method="GET" action="{{ route('guru.laporan.index') }}">
                <div class="row g-3">
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

                    <div class="col-md-3 period-input period-day" style="{{ request('period', 'day') !== 'day' ? 'display:none;' : '' }}">
                        <label class="form-label fw-semibold">Tanggal</label>
                        <input type="date" name="date" class="form-control" value="{{ request('date', now()->toDateString()) }}">
                    </div>

                    <div class="col-md-3 period-input period-month" style="{{ request('period') !== 'month' ? 'display:none;' : '' }}">
                        <label class="form-label fw-semibold">Bulan</label>
                        <div class="d-flex gap-2">
                            <select name="month" class="form-select">
                                @foreach (range(1, 12) as $month)
                                    <option value="{{ $month }}" {{ request('month', now()->month) == $month ? 'selected' : '' }}>{{ \Carbon\Carbon::createFromDate(now()->year, $month, 1)->translatedFormat('F') }}</option>
                                @endforeach
                            </select>
                            <select name="year" class="form-select">
                                @foreach (range(now()->year - 5, now()->year + 1) as $year)
                                    <option value="{{ $year }}" {{ request('year', now()->year) == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3 period-input period-quarter" style="{{ request('period') !== 'quarter' ? 'display:none;' : '' }}">
                        <label class="form-label fw-semibold">Triwulan</label>
                        <div class="d-flex gap-2">
                            <select name="quarter" class="form-select">
                                @foreach (range(1, 4) as $quarter)
                                    <option value="{{ $quarter }}" {{ request('quarter', 1) == $quarter ? 'selected' : '' }}>Triwulan {{ $quarter }}</option>
                                @endforeach
                            </select>
                            <select name="year" class="form-select">
                                @foreach (range(now()->year - 5, now()->year + 1) as $year)
                                    <option value="{{ $year }}" {{ request('year', now()->year) == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3 period-input period-semester" style="{{ request('period') !== 'semester' ? 'display:none;' : '' }}">
                        <label class="form-label fw-semibold">Semester</label>
                        <div class="d-flex gap-2">
                            <select name="semester" class="form-select">
                                <option value="1" {{ request('semester', 1) == 1 ? 'selected' : '' }}>Semester 1 (Juli - Desember)</option>
                                <option value="2" {{ request('semester') == 2 ? 'selected' : '' }}>Semester 2 (Januari - Juni)</option>
                            </select>
                            <select name="year" class="form-select">
                                @foreach (range(now()->year - 5, now()->year + 1) as $year)
                                    <option value="{{ $year }}" {{ request('year', now()->year) == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3 period-input period-year" style="{{ request('period') !== 'year' ? 'display:none;' : '' }}">
                        <label class="form-label fw-semibold">Tahun</label>
                        <select name="year" class="form-select">
                            @foreach (range(now()->year - 5, now()->year + 1) as $year)
                                <option value="{{ $year }}" {{ request('year', now()->year) == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Status Kehadiran</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                            <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>Izin</option>
                            <option value="sakit" {{ request('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                            <option value="alpa" {{ request('status') == 'alpa' ? 'selected' : '' }}>Alfa</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Cari Nama / NIS</label>
                        <input type="text" name="search" class="form-control" placeholder="Nama atau NIS" value="{{ request('search') }}">
                    </div>

                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary flex-fill"><i class="bi bi-check-circle me-1"></i>Tampilkan</button>
                        <a href="{{ route('guru.laporan.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i></a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md"><div class="card border-0 rounded-4 h-100"><div class="card-body text-center"><small class="text-muted d-block">Jumlah Hadir</small><h3 class="text-success fw-bold mb-0">{{ $statistics['hadir'] }}</h3></div></div></div>
        <div class="col-6 col-md"><div class="card border-0 rounded-4 h-100"><div class="card-body text-center"><small class="text-muted d-block">Jumlah Terlambat</small><h3 class="text-secondary fw-bold mb-0">{{ $statistics['terlambat'] }}</h3></div></div></div>
        <div class="col-6 col-md"><div class="card border-0 rounded-4 h-100"><div class="card-body text-center"><small class="text-muted d-block">Jumlah Izin</small><h3 class="text-warning fw-bold mb-0">{{ $statistics['izin'] }}</h3></div></div></div>
        <div class="col-6 col-md"><div class="card border-0 rounded-4 h-100"><div class="card-body text-center"><small class="text-muted d-block">Jumlah Sakit</small><h3 class="text-info fw-bold mb-0">{{ $statistics['sakit'] }}</h3></div></div></div>
        <div class="col-6 col-md"><div class="card border-0 rounded-4 h-100"><div class="card-body text-center"><small class="text-muted d-block">Jumlah Alfa</small><h3 class="text-danger fw-bold mb-0">{{ $statistics['alpa'] }}</h3></div></div></div>
        <div class="col-12 col-md"><div class="card border-0 rounded-4 h-100"><div class="card-body text-center"><small class="text-muted d-block">Persentase Kehadiran</small><h3 class="text-primary fw-bold mb-0">{{ $classGroup['percentage'] ?? 0 }}%</h3></div></div></div>
    </div>

    <div class="card border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-bottom rounded-top-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div><h5 class="fw-bold mb-0"><i class="bi bi-download me-2 text-primary"></i>Export Laporan</h5><small class="text-muted">Periode: {{ $dateLabel }}</small></div>
            <div class="d-flex gap-2">
                <a href="{{ route('guru.laporan.pdf', request()->query()) }}" class="btn btn-danger"><i class="bi bi-file-earmark-pdf-fill me-1"></i>Export PDF</a>
                <a href="{{ route('guru.laporan.excel', request()->query()) }}" class="btn btn-success"><i class="bi bi-file-earmark-excel-fill me-1"></i>Export Excel</a>
            </div>
        </div>
    </div>

    <div class="card border-0 rounded-4">
        <div class="card-header bg-primary text-white rounded-top-4"><h5 class="mb-0"><i class="bi bi-table me-2"></i>Rekap Siswa Kelas {{ $class->name }}</h5></div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle mb-0">
                <thead><tr><th>No</th><th>Nama Siswa</th><th>NIS</th><th>Kelas</th><th>Hadir</th><th>Terlambat</th><th>Izin</th><th>Sakit</th><th>Alfa</th><th>Persentase Kehadiran</th></tr></thead>
                <tbody>
                    @forelse (($classGroup['students'] ?? []) as $student)
                        <tr>
                            <td>{{ $loop->iteration }}</td><td>{{ $student['name'] }}</td><td>{{ $student['nis'] }}</td><td>{{ $class->name }}</td>
                            <td>{{ $student['hadir'] }}</td><td>{{ $student['terlambat'] }}</td><td>{{ $student['izin'] }}</td><td>{{ $student['sakit'] }}</td><td>{{ $student['alpa'] }}</td><td>{{ $student['percentage'] }}%</td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="text-center py-4">Tidak ada data absensi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    (function () {
        const periodSelect = document.getElementById('periodSelect');
        const periodInputs = document.querySelectorAll('.period-input');

        function updatePeriodInput() {
            periodInputs.forEach((input) => input.style.display = 'none');
            document.querySelectorAll('.period-' + periodSelect.value).forEach((input) => input.style.display = 'block');
        }

        periodSelect.addEventListener('change', updatePeriodInput);
        updatePeriodInput();
    })();
</script>
@endpush
