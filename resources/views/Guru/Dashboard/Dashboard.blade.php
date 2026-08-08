@extends('Layouts.LayoutGuru')

@section('title', 'Dashboard Guru')

@push('css')
<style>
    .student-dashboard-card { transition: transform .2s ease, box-shadow .2s ease; }
    .student-dashboard-card:hover { transform: translateY(-4px); box-shadow: 0 .75rem 1.5rem rgba(0, 0, 0, .10) !important; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-primary mb-1">Dashboard Guru</h3>
            <p class="text-muted mb-0">Selamat datang, <strong>{{ Auth::user()->name }}</strong></p>
            <p class="text-muted mb-0">Wali Kelas: <strong>{{ $class->name }}</strong></p>
        </div>
        <span class="badge bg-primary fs-6">{{ now()->translatedFormat('d F Y') }}</span>
    </div>

    @php
        $unreadTeacherMailboxCount = \App\Models\TeacherMailbox::where('receiver_id', Auth::id())
            ->where('status', 'unread')
            ->count();
    @endphp

    {{-- Mailbox Card --}}
    <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
        <div class="card-body p-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div style="width:44px;height:44px;background:linear-gradient(135deg,{{ $unreadTeacherMailboxCount > 0 ? '#dc3545,#f87171' : '#6c757d,#495057' }});border-radius:12px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:20px;">
                    <i class="bi bi-envelope{{ $unreadTeacherMailboxCount > 0 ? '-exclamation' : '' }}"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-dark">Mailbox Internal Guru</h6>
                    @if ($unreadTeacherMailboxCount > 0)
                        <p class="text-danger small mb-0 fw-semibold">Anda memiliki <strong>{{ $unreadTeacherMailboxCount }} pesan baru</strong> yang belum dibaca.</p>
                    @else
                        <p class="text-muted small mb-0">Belum ada pesan baru.</p>
                    @endif
                </div>
            </div>
            <a href="{{ route('guru.mailbox.index') }}" class="btn btn-outline-primary btn-sm px-3 rounded-3">
                <i class="bi bi-inbox me-1"></i> Buka Mailbox
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white py-3"><h5 class="fw-bold mb-0"><i class="bi bi-funnel me-2 text-primary"></i>Periode Statistik</h5></div>
        <div class="card-body p-4">
            <form method="GET" action="{{ route('guru.dashboard') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Periode Statistik</label>
                        <select name="period" id="periodSelect" class="form-select">
                            <option value="day" {{ request('period', 'day') === 'day' ? 'selected' : '' }}>Harian</option>
                            <option value="month" {{ request('period') === 'month' ? 'selected' : '' }}>Bulanan</option>
                            <option value="quarter" {{ request('period') === 'quarter' ? 'selected' : '' }}>Triwulan</option>
                            <option value="semester" {{ request('period') === 'semester' ? 'selected' : '' }}>Semester</option>
                            <option value="year" {{ request('period') === 'year' ? 'selected' : '' }}>Tahunan</option>
                        </select>
                    </div>

                    <div class="col-md-3 period-input period-day" style="{{ request('period', 'day') !== 'day' ? 'display:none;' : '' }}">
                        <label class="form-label fw-semibold">Tanggal</label>
                        <input type="date" name="date" class="form-control" value="{{ request('date', now()->toDateString()) }}">
                    </div>

                    <div class="col-md-4 period-input period-month" style="{{ request('period') !== 'month' ? 'display:none;' : '' }}">
                        <label class="form-label fw-semibold">Bulan dan Tahun</label>
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

                    <div class="col-md-4 period-input period-quarter" style="{{ request('period') !== 'quarter' ? 'display:none;' : '' }}">
                        <label class="form-label fw-semibold">Triwulan dan Tahun</label>
                        <div class="d-flex gap-2">
                            <select name="quarter" class="form-select">
                                <option value="1" {{ request('quarter', 1) == 1 ? 'selected' : '' }}>Triwulan 1 (Jan - Mar)</option>
                                <option value="2" {{ request('quarter') == 2 ? 'selected' : '' }}>Triwulan 2 (Apr - Jun)</option>
                                <option value="3" {{ request('quarter') == 3 ? 'selected' : '' }}>Triwulan 3 (Jul - Sep)</option>
                                <option value="4" {{ request('quarter') == 4 ? 'selected' : '' }}>Triwulan 4 (Okt - Des)</option>
                            </select>
                            <select name="year" class="form-select">
                                @foreach (range(now()->year - 5, now()->year + 1) as $year)
                                    <option value="{{ $year }}" {{ request('year', now()->year) == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4 period-input period-semester" style="{{ request('period') !== 'semester' ? 'display:none;' : '' }}">
                        <label class="form-label fw-semibold">Semester dan Tahun</label>
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

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-check-circle me-2"></i>Tampilkan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md col-xl">
            <div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body text-center py-4"><i class="bi bi-people-fill text-primary fs-2"></i><small class="d-block text-muted mt-2">Total Siswa</small><h2 class="fw-bold text-primary mb-0">{{ $totalSiswa }}</h2></div></div>
        </div>
        <div class="col-6 col-md col-xl">
            <div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body text-center py-4"><i class="bi bi-check-circle-fill text-success fs-2"></i><small class="d-block text-muted mt-2">Total Hadir</small><h2 class="fw-bold text-success mb-0">{{ $hadirHariIni }}</h2><small class="text-muted">{{ $periodLabel }}</small></div></div>
        </div>
        <div class="col-6 col-md col-xl">
            <div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body text-center py-4"><i class="bi bi-envelope-fill text-warning fs-2"></i><small class="d-block text-muted mt-2">Total Izin</small><h2 class="fw-bold text-warning mb-0">{{ $izinHariIni }}</h2><small class="text-muted">{{ $periodLabel }}</small></div></div>
        </div>
        <div class="col-6 col-md col-xl">
            <div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body text-center py-4"><i class="bi bi-heart-pulse-fill text-info fs-2"></i><small class="d-block text-muted mt-2">Total Sakit</small><h2 class="fw-bold text-info mb-0">{{ $sakitHariIni }}</h2><small class="text-muted">{{ $periodLabel }}</small></div></div>
        </div>
        <div class="col-12 col-md col-xl">
            <div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body text-center py-4"><i class="bi bi-x-circle-fill text-danger fs-2"></i><small class="d-block text-muted mt-2">Total Alfa</small><h2 class="fw-bold text-danger mb-0">{{ $alpaHariIni }}</h2><small class="text-muted">{{ $periodLabel }}</small></div></div>
        </div>
        <div class="col-12 col-md col-xl">
            <div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body text-center py-4"><i class="bi bi-alarm-fill text-secondary fs-2"></i><small class="d-block text-muted mt-2">Terlambat</small><h2 class="fw-bold text-secondary mb-0">{{ $terlambatPeriode }}</h2><small class="text-muted">{{ $periodLabel }}</small></div></div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white py-3"><h5 class="fw-bold mb-0"><i class="bi bi-pie-chart-fill text-primary me-2"></i>Statistik Kelas {{ $class->name }} — {{ $periodLabel }}</h5></div>
        <div class="card-body p-4">
            <div class="row align-items-center g-4">
                <div class="col-12 col-md-5"><div class="mx-auto" style="max-width:300px;"><canvas id="classAttendanceChart"></canvas></div></div>
                <div class="col-12 col-md-7">
                    <h5 class="fw-bold">Persentase Kehadiran Kelas: <span class="text-success">{{ $classAttendancePercentage }}%</span></h5>
                    <p class="text-muted mb-3">Grafik dihitung dari absensi siswa aktif pada periode yang dipilih.</p>
                    <div class="row g-2">
                        <div class="col-6"><div class="p-3 rounded-3" style="background:#e9f7ef;"><small>Hadir</small><strong class="d-block text-success fs-4">{{ $classStatistics['hadir'] }}</strong></div></div>
                        <div class="col-6"><div class="p-3 rounded-3" style="background:#fff8db;"><small>Izin</small><strong class="d-block text-warning fs-4">{{ $classStatistics['izin'] }}</strong></div></div>
                        <div class="col-6"><div class="p-3 rounded-3" style="background:#e7f7fb;"><small>Sakit</small><strong class="d-block text-info fs-4">{{ $classStatistics['sakit'] }}</strong></div></div>
                        <div class="col-6"><div class="p-3 rounded-3" style="background:#fff0f0;"><small>Alfa</small><strong class="d-block text-danger fs-4">{{ $classStatistics['alpa'] }}</strong></div></div>
                        <div class="col-12"><div class="p-3 rounded-3 bg-light d-flex justify-content-between align-items-center"><span><i class="bi bi-alarm-fill text-secondary me-2"></i>Jumlah Terlambat</span><strong class="text-secondary fs-4">{{ $classLateCount }}</strong></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0"><i class="bi bi-people me-2 text-primary"></i>Statistik Siswa</h4>
        <span class="badge bg-primary rounded-pill">{{ $totalSiswa }} Siswa</span>
    </div>

    <div class="row g-4 mb-4">
        @forelse ($students as $student)
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card student-dashboard-card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4 text-center d-flex flex-column">
                        <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center mx-auto mb-3" style="width:64px;height:64px;"><i class="bi bi-person-fill fs-2"></i></div>
                        <h5 class="fw-bold mb-1">{{ $student->name }}</h5>
                        <p class="text-muted small mb-1">NIS: {{ $student->nis ?? '-' }}</p>
                        <p class="text-muted small mb-3">{{ $class->name }}</p>
                        <div class="mx-auto mb-3" style="max-width:180px;width:100%;"><canvas id="studentChart{{ $student->id }}"></canvas></div>
                        <div class="rounded-3 bg-light p-2 mb-3"><small class="text-muted d-block">Persentase Kehadiran</small><strong class="text-success">{{ $student->attendance_percentage }}%</strong></div>
                        <div class="rounded-3 p-2 mb-3" style="background:rgba(108,117,125,0.10);"><small class="text-muted d-block">Terlambat</small><strong class="text-secondary">{{ $student->late_count }}</strong></div>
                        <div class="row g-2 mt-auto small">
                            <div class="col-6"><div class="rounded-3 py-2" style="background:#e9f7ef;"><span class="d-block">Hadir</span><strong class="text-success">{{ $student->attendance_statistics['hadir'] }}</strong></div></div>
                            <div class="col-6"><div class="rounded-3 py-2" style="background:#fff8db;"><span class="d-block">Izin</span><strong class="text-warning">{{ $student->attendance_statistics['izin'] }}</strong></div></div>
                            <div class="col-6"><div class="rounded-3 py-2" style="background:#e7f7fb;"><span class="d-block">Sakit</span><strong class="text-info">{{ $student->attendance_statistics['sakit'] }}</strong></div></div>
                            <div class="col-6"><div class="rounded-3 py-2" style="background:#fff0f0;"><span class="d-block">Alfa</span><strong class="text-danger">{{ $student->attendance_statistics['alpa'] }}</strong></div></div>
                        </div>

                        <div class="mt-3 pt-2 border-top d-flex flex-column gap-2">
                            {{-- 1. ALFA LETTER (Pemanggilan) --}}
                            @if ($student->weekly_alpa < 3)
                                <span class="d-inline-block w-100" tabindex="0" data-bs-toggle="tooltip" data-bs-placement="top" title="Minimal 3 kali ALFA dalam 1 minggu.">
                                    <button type="button" class="btn btn-outline-secondary btn-sm w-100 disabled" style="opacity: 0.45; cursor: not-allowed;" disabled>
                                        <i class="bi bi-envelope-paper me-1"></i> Kirim Surat Pemanggilan
                                    </button>
                                </span>
                            @elseif ($student->has_alpha_mailbox)
                                <span class="d-inline-block w-100" tabindex="0" data-bs-toggle="tooltip" data-bs-placement="top" title="Surat untuk periode ini sudah pernah dikirim.">
                                    <button type="button" class="btn btn-secondary btn-sm w-100" style="cursor: not-allowed;" disabled>
                                        <i class="bi bi-envelope-paper me-1"></i> Surat Pemanggilan Sudah Dikirim
                                    </button>
                                </span>
                            @else
                                <a href="{{ route('guru.mailbox.create', ['student' => $student->id, 'mail_type' => 'alpha', 'week_start' => $weekStart, 'week_end' => $weekEnd]) }}" class="btn btn-danger btn-sm w-100 fw-bold">
                                    <i class="bi bi-envelope-paper me-1"></i> Kirim Surat Pemanggilan
                                </a>
                            @endif

                            {{-- 2. TERLAMBAT LETTER (Pembinaan) --}}
                            @if ($student->weekly_late < 3)
                                <span class="d-inline-block w-100" tabindex="0" data-bs-toggle="tooltip" data-bs-placement="top" title="Minimal 3 kali Terlambat dalam 1 minggu.">
                                    <button type="button" class="btn btn-outline-secondary btn-sm w-100 disabled" style="opacity: 0.45; cursor: not-allowed;" disabled>
                                        <i class="bi bi-envelope-paper me-1"></i> Kirim Surat Pembinaan
                                    </button>
                                </span>
                            @elseif ($student->has_late_mailbox)
                                <span class="d-inline-block w-100" tabindex="0" data-bs-toggle="tooltip" data-bs-placement="top" title="Surat untuk periode ini sudah pernah dikirim.">
                                    <button type="button" class="btn btn-secondary btn-sm w-100" style="cursor: not-allowed;" disabled>
                                        <i class="bi bi-envelope-paper me-1"></i> Surat Pembinaan Sudah Dikirim
                                    </button>
                                </span>
                            @else
                                <a href="{{ route('guru.mailbox.create', ['student' => $student->id, 'mail_type' => 'late', 'week_start' => $weekStart, 'week_end' => $weekEnd]) }}" class="btn btn-warning btn-sm w-100 fw-bold text-dark">
                                    <i class="bi bi-envelope-paper me-1"></i> Kirim Surat Pembinaan
                                </a>
                            @endif

                            {{-- 3. IZIN LETTER (Klarifikasi) --}}
                            @if ($student->weekly_permission < 3)
                                <span class="d-inline-block w-100" tabindex="0" data-bs-toggle="tooltip" data-bs-placement="top" title="Minimal 3 kali Izin dalam 1 minggu.">
                                    <button type="button" class="btn btn-outline-secondary btn-sm w-100 disabled" style="opacity: 0.45; cursor: not-allowed;" disabled>
                                        <i class="bi bi-envelope-paper me-1"></i> Kirim Surat Klarifikasi
                                    </button>
                                </span>
                            @elseif ($student->has_permission_mailbox)
                                <span class="d-inline-block w-100" tabindex="0" data-bs-toggle="tooltip" data-bs-placement="top" title="Surat untuk periode ini sudah pernah dikirim.">
                                    <button type="button" class="btn btn-secondary btn-sm w-100" style="cursor: not-allowed;" disabled>
                                        <i class="bi bi-envelope-paper me-1"></i> Surat Klarifikasi Sudah Dikirim
                                    </button>
                                </span>
                            @else
                                <a href="{{ route('guru.mailbox.create', ['student' => $student->id, 'mail_type' => 'permission', 'week_start' => $weekStart, 'week_end' => $weekEnd]) }}" class="btn btn-info btn-sm w-100 fw-bold text-white">
                                    <i class="bi bi-envelope-paper me-1"></i> Kirim Surat Klarifikasi
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12"><div class="alert alert-info">Belum ada siswa aktif pada kelas ini.</div></div>
        @endforelse
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white py-3"><h5 class="fw-bold mb-0">Menu Cepat</h5></div>
        <div class="card-body"><div class="row g-3">
            <div class="col-md-4"><a href="{{ route('guru.scan.index') }}" class="btn btn-primary w-100 py-3 rounded-3"><i class="bi bi-qr-code-scan me-2"></i>Scan QR Siswa</a></div>
            <div class="col-md-4"><a href="{{ route('guru.attendance.index') }}" class="btn btn-success w-100 py-3 rounded-3"><i class="bi bi-calendar-check me-2"></i>Rekap Absensi</a></div>
            <div class="col-md-4"><a href="{{ route('guru.laporan.index') }}" class="btn btn-outline-primary w-100 py-3 rounded-3"><i class="bi bi-file-earmark-bar-graph me-2"></i>Laporan Absensi</a></div>
        </div></div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    (function () {
        const colors = ['#198754', '#ffc107', '#0dcaf0', '#dc3545'];
        const options = { responsive: true, plugins: { legend: { position: 'bottom', labels: { boxWidth: 10 } } }, cutout: '65%' };
        const periodSelect = document.getElementById('periodSelect');
        const periodInputs = document.querySelectorAll('.period-input');

        function updatePeriodInput() {
            periodInputs.forEach((input) => input.style.display = 'none');
            document.querySelectorAll('.period-' + periodSelect.value).forEach((input) => input.style.display = 'block');
        }

        periodSelect.addEventListener('change', updatePeriodInput);
        updatePeriodInput();

        new Chart(document.getElementById('classAttendanceChart'), {
            type: 'doughnut',
            data: { labels: ['Hadir', 'Izin', 'Sakit', 'Alfa'], datasets: [{ data: @json(array_values($classStatistics)), backgroundColor: colors, borderWidth: 0 }] },
            options: options,
        });

        @foreach ($students as $student)
            new Chart(document.getElementById('studentChart{{ $student->id }}'), {
                type: 'doughnut',
                data: { labels: ['Hadir', 'Izin', 'Sakit', 'Alfa'], datasets: [{ data: @json(array_values($student->attendance_statistics)), backgroundColor: colors, borderWidth: 0 }] },
                options: options,
            });
        @endforeach

        // Initialize Bootstrap Tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    })();
</script>
@endpush
