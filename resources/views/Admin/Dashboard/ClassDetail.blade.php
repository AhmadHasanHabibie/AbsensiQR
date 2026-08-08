@extends('Layouts.LayoutAdmin')

@section('title', 'Detail Statistik Kelas')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">{{ $class->name }}</h3>
            <p class="text-muted mb-0">Detail absensi kelas untuk periode {{ $periodLabel }}.</p>
        </div>
        <a href="{{ route('admin.dashboard', request()->except(['search', 'sort'])) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Kembali ke Dashboard
        </a>
    </div>

    <div class="card border-0 rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="row g-4 align-items-center">
                <div class="col-12 col-lg-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-building me-2 text-primary"></i>Informasi Kelas</h5>
                    <p class="mb-2"><span class="text-muted">Nama Kelas:</span> <strong>{{ $class->name }}</strong></p>
                    <p class="mb-2"><span class="text-muted">Wali Kelas:</span> <strong>{{ $class->teacher?->name ?? '-' }}</strong></p>
                    <p class="mb-0"><span class="text-muted">Jumlah Siswa:</span> <strong>{{ $class->students->count() }}</strong></p>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="mx-auto" style="max-width: 230px;">
                        <canvas id="classDetailChart" aria-label="Grafik statistik kelas {{ $class->name }}"></canvas>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="row g-2 text-center">
                        <div class="col-6"><div class="p-3 rounded-3" style="background:#e9f7ef;"><small class="text-muted d-block">Total Hadir</small><h4 class="text-success fw-bold mb-0">{{ $classStatistics['hadir'] }}</h4></div></div>
                        <div class="col-6"><div class="p-3 rounded-3" style="background:#fff8db;"><small class="text-muted d-block">Total Izin</small><h4 class="text-warning fw-bold mb-0">{{ $classStatistics['izin'] }}</h4></div></div>
                        <div class="col-6"><div class="p-3 rounded-3" style="background:#e7f7fb;"><small class="text-muted d-block">Total Sakit</small><h4 class="text-info fw-bold mb-0">{{ $classStatistics['sakit'] }}</h4></div></div>
                        <div class="col-6"><div class="p-3 rounded-3" style="background:#fff0f0;"><small class="text-muted d-block">Total Alpa</small><h4 class="text-danger fw-bold mb-0">{{ $classStatistics['alpa'] }}</h4></div></div>
                        <div class="col-12"><div class="p-3 rounded-3" style="background:#f1f3f5;"><small class="text-muted d-block">Total Terlambat</small><h4 class="text-secondary fw-bold mb-0">{{ $classLateCount }}</h4></div></div>
                        <div class="col-12"><div class="p-3 rounded-3 bg-light"><small class="text-muted d-block">Persentase Kehadiran Kelas</small><h4 class="text-success fw-bold mb-0">{{ $attendancePercentage }}%</h4></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 rounded-4 mb-4">
        <div class="card-body p-4">
            <form method="GET" class="row g-3">
                @foreach (request()->only(['period', 'date', 'month', 'quarter', 'semester', 'year']) as $name => $value)
                    <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                @endforeach
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold">Cari Siswa</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="search" name="search" class="form-control" value="{{ $search }}" placeholder="Nama atau NIS siswa">
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Urutkan</label>
                    <select name="sort" class="form-select">
                        <option value="name_asc" {{ $sort === 'name_asc' ? 'selected' : '' }}>Nama A-Z</option>
                        <option value="name_desc" {{ $sort === 'name_desc' ? 'selected' : '' }}>Nama Z-A</option>
                        <option value="attendance_high" {{ $sort === 'attendance_high' ? 'selected' : '' }}>Persentase Kehadiran Tertinggi</option>
                        <option value="attendance_low" {{ $sort === 'attendance_low' ? 'selected' : '' }}>Persentase Kehadiran Terendah</option>
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel me-2"></i>Terapkan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0"><i class="bi bi-people me-2 text-primary"></i>Daftar Siswa</h4>
        <span class="badge bg-primary rounded-pill">{{ $students->count() }} Siswa Ditampilkan</span>
    </div>

    <div class="row g-4">
        @forelse ($students as $student)
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card border-0 rounded-4 h-100">
                    <div class="card-body p-4 text-center d-flex flex-column">
                        <div class="mx-auto mb-3" style="width:88px;height:88px;border-radius:50%;overflow:hidden;background:#e9ecef;display:flex;align-items:center;justify-content:center;">
                            @if ($student->photo)
                                <img src="{{ asset('storage/' . $student->photo) }}" alt="Foto {{ $student->name }}" style="width:100%;height:100%;object-fit:cover;">
                            @else
                                <i class="bi bi-person-fill text-secondary" style="font-size:42px;"></i>
                            @endif
                        </div>
                        <h5 class="fw-bold mb-1">{{ $student->name }}</h5>
                        <p class="text-muted small mb-3">NIS: {{ $student->nis ?? '-' }}</p>
                        <div class="mx-auto mb-3" style="max-width:180px;width:100%;">
                            <canvas id="studentChart{{ $student->id }}" aria-label="Grafik absensi {{ $student->name }}"></canvas>
                        </div>
                        <div class="p-2 rounded-3 bg-light mb-3"><small class="text-muted d-block">Persentase Kehadiran</small><strong class="text-success">{{ $student->attendance_percentage }}%</strong></div>
                        <div class="p-2 rounded-3 mb-3" style="background:#f1f3f5;"><small class="text-muted d-block">Terlambat</small><strong class="text-secondary">{{ $student->late_count }}</strong></div>
                        <div class="row g-2 mt-auto small">
                            <div class="col-6"><div class="rounded-3 py-2" style="background:#e9f7ef;"><span class="d-block text-muted">Hadir</span><strong class="text-success">{{ $student->attendance_statistics['hadir'] }}</strong></div></div>
                            <div class="col-6"><div class="rounded-3 py-2" style="background:#fff8db;"><span class="d-block text-muted">Izin</span><strong class="text-warning">{{ $student->attendance_statistics['izin'] }}</strong></div></div>
                            <div class="col-6"><div class="rounded-3 py-2" style="background:#e7f7fb;"><span class="d-block text-muted">Sakit</span><strong class="text-info">{{ $student->attendance_statistics['sakit'] }}</strong></div></div>
                            <div class="col-6"><div class="rounded-3 py-2" style="background:#fff0f0;"><span class="d-block text-muted">Alpa</span><strong class="text-danger">{{ $student->attendance_statistics['alpa'] }}</strong></div></div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12"><div class="alert alert-info mb-0">Siswa dengan kata kunci tersebut tidak ditemukan.</div></div>
        @endforelse
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    (function () {
        const chartOptions = {
            responsive: true,
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 10 } } },
            cutout: '65%',
        };

        new Chart(document.getElementById('classDetailChart'), {
            type: 'doughnut',
            data: {
                labels: ['Hadir', 'Izin', 'Sakit', 'Alpa'],
                datasets: [{ data: @json(array_values($classStatistics)), backgroundColor: ['#198754', '#ffc107', '#0dcaf0', '#dc3545'], borderWidth: 0 }],
            },
            options: chartOptions,
        });

        @foreach ($students as $student)
            new Chart(document.getElementById('studentChart{{ $student->id }}'), {
                type: 'doughnut',
                data: {
                    labels: ['Hadir', 'Izin', 'Sakit', 'Alpa'],
                    datasets: [{ data: @json(array_values($student->attendance_statistics)), backgroundColor: ['#198754', '#ffc107', '#0dcaf0', '#dc3545'], borderWidth: 0 }],
                },
                options: chartOptions,
            });
        @endforeach
    })();
</script>
@endpush
