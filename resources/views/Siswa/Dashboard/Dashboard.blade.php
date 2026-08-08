@extends('Layouts.LayoutSiswa')

@section('title', 'Dashboard Saya')

@section('content')

    <div class="container-fluid">
        {{-- Welcome Card --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 rounded-4 overflow-hidden">
                    <div class="card-body p-4" style="background: linear-gradient(135deg, #6f42c1 0%, #a855f7 100%);">
                        <div class="d-flex align-items-center gap-3">
                            <div class="flex-shrink-0">
                                <div
                                    style="width:64px;height:64px;background:rgba(255,255,255,0.15);border-radius:16px;display:flex;align-items:center;justify-content:center;">
                                    <i class="bi bi-person-badge text-white" style="font-size:30px;"></i>
                                </div>
                            </div>
                            <div class="text-white">
                                <h3 class="fw-bold mb-1">Halo, {{ $siswa->name }}! 👋</h3>
                                <p class="mb-0 opacity-75">Selamat datang di Dashboard Siswa <strong>SMKN 17
                                        Jakarta</strong>.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Notifikasi Surat Pemanggilan Baru --}}
        @php
            $unreadMailboxCountDashboard = \App\Models\StudentMailbox::where('student_id', Auth::id())
                ->where('status', 'unread')
                ->count();
        @endphp

        @if ($unreadMailboxCountDashboard > 0)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="alert alert-danger border-0 rounded-4 d-flex flex-wrap align-items-center justify-content-between p-3 shadow-sm" style="background-color: #fee2e2; color: #991b1b;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-2 rounded-circle bg-danger text-white d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                <i class="bi bi-envelope-paper-fill fs-4"></i>
                            </div>
                            <div>
                                <strong class="d-block fs-6">Pemberitahuan Surat Pemanggilan</strong>
                                <span>Anda memiliki <strong>{{ $unreadMailboxCountDashboard }} Surat Pemanggilan</strong> baru.</span>
                            </div>
                        </div>
                        <a href="{{ route('siswa.mailbox.index') }}" class="btn btn-danger btn-sm rounded-3 text-white px-3 mt-2 mt-sm-0">Lihat Kotak Surat</a>
                    </div>
                </div>
            </div>
        @endif

        {{-- Filter Periode Statistik --}}
        <div class="card border-0 rounded-4 mb-4">
            <div class="card-header bg-white border-bottom rounded-top-4">
                <h5 class="mb-0 fw-bold"><i class="bi bi-funnel me-2 text-primary"></i>Periode Statistik</h5>
            </div>
            <div class="card-body p-4">
                <form method="GET" action="{{ route('siswa.dashboard') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Periode</label>
                            <select name="period" id="periodSelect" class="form-select">
                                <option value="day" {{ request('period', 'day') === 'day' ? 'selected' : '' }}>Harian
                                </option>
                                <option value="month" {{ request('period') === 'month' ? 'selected' : '' }}>Bulanan
                                </option>
                                <option value="quarter" {{ request('period') === 'quarter' ? 'selected' : '' }}>Triwulan
                                </option>
                                <option value="semester" {{ request('period') === 'semester' ? 'selected' : '' }}>Semester
                                </option>
                                <option value="year" {{ request('period') === 'year' ? 'selected' : '' }}>Tahunan</option>
                            </select>
                        </div>
                        <div class="col-md-3 period-input period-day"
                            style="{{ request('period', 'day') !== 'day' ? 'display:none;' : '' }}">
                            <label class="form-label fw-semibold">Tanggal</label>
                            <input type="date" name="date" class="form-control"
                                value="{{ request('date', now()->toDateString()) }}">
                        </div>
                        <div class="col-md-4 period-input period-month"
                            style="{{ request('period') !== 'month' ? 'display:none;' : '' }}">
                            <label class="form-label fw-semibold">Bulan dan Tahun</label>
                            <div class="d-flex gap-2"><select name="month" class="form-select">
                                    @foreach (range(1, 12) as $month)
                                        <option value="{{ $month }}"
                                            {{ request('month', now()->month) == $month ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::createFromDate(now()->year, $month, 1)->translatedFormat('F') }}
                                        </option>
                                    @endforeach
                                </select>
                                <select name="year" class="form-select">
                                    @foreach (range(now()->year - 5, now()->year + 1) as $year)
                                        <option value="{{ $year }}"
                                            {{ request('year', now()->year) == $year ? 'selected' : '' }}>
                                            {{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 period-input period-quarter"
                            style="{{ request('period') !== 'quarter' ? 'display:none;' : '' }}">
                            <label class="form-label fw-semibold">Triwulan dan Tahun</label>
                            <div class="d-flex gap-2"><select name="quarter" class="form-select">
                                    <option value="1" {{ request('quarter', 1) == 1 ? 'selected' : '' }}>Triwulan 1
                                        (Jan - Mar)</option>
                                    <option value="2" {{ request('quarter') == 2 ? 'selected' : '' }}>Triwulan 2 (Apr
                                        - Jun)</option>
                                    <option value="3" {{ request('quarter') == 3 ? 'selected' : '' }}>Triwulan 3 (Jul
                                        - Sep)</option>
                                    <option value="4" {{ request('quarter') == 4 ? 'selected' : '' }}>Triwulan 4 (Okt
                                        - Des)</option>
                                </select><select name="year" class="form-select">
                                    @foreach (range(now()->year - 5, now()->year + 1) as $year)
                                        <option value="{{ $year }}"
                                            {{ request('year', now()->year) == $year ? 'selected' : '' }}>
                                            {{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 period-input period-semester"
                            style="{{ request('period') !== 'semester' ? 'display:none;' : '' }}">
                            <label class="form-label fw-semibold">Semester dan Tahun</label>
                            <div class="d-flex gap-2"><select name="semester" class="form-select">
                                    <option value="1" {{ request('semester', 1) == 1 ? 'selected' : '' }}>Semester 1
                                        (Juli - Desember)</option>
                                    <option value="2" {{ request('semester') == 2 ? 'selected' : '' }}>Semester 2
                                        (Januari - Juni)</option>
                                </select><select name="year" class="form-select">
                                    @foreach (range(now()->year - 5, now()->year + 1) as $year)
                                        <option value="{{ $year }}"
                                            {{ request('year', now()->year) == $year ? 'selected' : '' }}>
                                            {{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 period-input period-year"
                            style="{{ request('period') !== 'year' ? 'display:none;' : '' }}">
                            <label class="form-label fw-semibold">Tahun</label><select name="year" class="form-select">
                                @foreach (range(now()->year - 5, now()->year + 1) as $year)
                                    <option value="{{ $year }}"
                                        {{ request('year', now()->year) == $year ? 'selected' : '' }}>{{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end"><button type="submit" class="btn btn-primary w-100"><i
                                    class="bi bi-check-circle me-2"></i>Tampilkan</button></div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Statistik Periode --}}
        <div class="card border-0 rounded-4 mb-4">
            <div class="card-header bg-white border-bottom rounded-top-4">
                <h5 class="mb-0 fw-bold"><i class="bi bi-pie-chart-fill me-2 text-primary"></i>Statistik Kehadiran —
                    {{ $periodLabel }}</h5>
            </div>
            <div class="card-body p-4">
                <div class="row align-items-center g-4">
                    <div class="col-12 col-md-4">
                        <div class="mx-auto" style="max-width:260px;"><canvas id="studentAttendanceChart"></canvas></div>
                    </div>
                    <div class="col-12 col-md-8">
                        <div class="row g-3">
                            <div class="col-6 col-lg">
                                <div class="p-3 text-center rounded-3" style="background:#e9f7ef;">
                                    <small>Hadir</small><strong
                                        class="d-block fs-4 text-success">{{ $attendanceStatistics['hadir'] }}</strong>
                                </div>
                            </div>
                            <div class="col-6 col-lg">
                                <div class="p-3 text-center rounded-3" style="background:#fff8db;">
                                    <small>Izin</small><strong
                                        class="d-block fs-4 text-warning">{{ $attendanceStatistics['izin'] }}</strong>
                                </div>
                            </div>
                            <div class="col-6 col-lg">
                                <div class="p-3 text-center rounded-3" style="background:#e7f7fb;">
                                    <small>Sakit</small><strong
                                        class="d-block fs-4 text-info">{{ $attendanceStatistics['sakit'] }}</strong>
                                </div>
                            </div>
                            <div class="col-6 col-lg">
                                <div class="p-3 text-center rounded-3" style="background:#fff0f0;">
                                    <small>Alfa</small><strong
                                        class="d-block fs-4 text-danger">{{ $attendanceStatistics['alpa'] }}</strong>
                                </div>
                            </div>
                            <div class="col-12 col-lg">
                                <div class="p-3 text-center rounded-3" style="background:#f1f3f5;">
                                    <small>Terlambat</small><strong
                                        class="d-block fs-4 text-secondary">{{ $attendanceStatistics['terlambat'] }}
                                        kali</strong>
                                </div>
                            </div>
                            <div class="col-12 col-lg">
                                <div class="p-3 text-center rounded-3 bg-light"><small>Persentase Hadir</small><strong
                                        class="d-block fs-4 text-primary">{{ $attendancePercentage }}%</strong><small
                                        class="text-muted">{{ $attendanceStatistics['total_hari'] }} Hari Tercatat</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">

            {{-- LEFT COLUMN: Profile + QR --}}
            <div class="col-lg-5">
                <div class="card border-0 rounded-4">
                    <div class="card-header bg-white border-bottom rounded-top-4">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-person-circle me-2 text-primary"></i>Profil Saya</h5>
                    </div>
                    <div class="card-body text-center p-4">

                        {{-- Avatar / Photo --}}
                        @if ($siswa->photo)
                            <img src="{{ asset('storage/' . $siswa->photo) }}" class="rounded-4 border mb-3"
                                width="130" height="130" style="object-fit:cover;">
                        @else
                            <div
                                style="width:130px;height:130px;margin:0 auto;background:linear-gradient(135deg,#6f42c1,#a855f7);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:48px;font-weight:700;color:#fff;border:4px solid rgba(111,66,193,0.2);">
                                {{ substr($siswa->name, 0, 1) }}
                            </div>
                        @endif

                        <h4 class="fw-bold mt-3 mb-1">{{ $siswa->name }}</h4>
                        <span class="badge bg-primary mb-3">{{ $siswa->schoolClass->name ?? '-' }}</span>

                        <div class="text-start mt-3 p-3 rounded-4" style="background:#f8f9fa;">
                            <div class="d-flex justify-content-between mb-2">
                                <small class="text-muted">NIS</small>
                                <strong>{{ $siswa->nis }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <small class="text-muted">Username</small>
                                <strong>{{ $siswa->username }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <small class="text-muted">Kelas</small>
                                <strong>{{ $siswa->schoolClass->name ?? '-' }}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">Status</small>
                                @if ($siswa->status)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Nonaktif</span>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>

                {{-- QR Code Card --}}
                <div class="card border-0 rounded-4 mt-4">
                    <div class="card-header bg-success text-white rounded-top-4">
                        <h5 class="mb-0"><i class="bi bi-qr-code me-2"></i>QR Code Absensi</h5>
                    </div>
                    <div class="card-body text-center p-4">

                        @if ($siswa->qr_code)
                            <img src="{{ asset('storage/' . $siswa->qr_code) }}"
                                class="img-fluid border rounded-3 p-3 mb-3" style="max-width:220px;background:#fff;">

                            <p class="text-muted small mb-2">Scan QR ini untuk melakukan absensi</p>
                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>QR Aktif</span>
                        @else
                            <div class="py-4">
                                <i class="bi bi-qr-code display-1 text-secondary"></i>
                                <h6 class="mt-3 fw-bold">QR Belum Tersedia</h6>
                                <p class="text-muted small">Hubungi administrator untuk mendapatkan QR Code.</p>
                            </div>
                        @endif

                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN: Stats + Riwayat --}}
            <div class="col-lg-7">

                {{-- Statistik Ringkas --}}
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <div class="card border-0 rounded-4">
                            <div class="card-body p-3 text-center">
                                <div
                                    style="width:44px;height:44px;margin:0 auto;background:rgba(25,135,84,0.1);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                                    <i class="bi bi-check-circle-fill text-success fs-4"></i>
                                </div>
                                <h5 class="fw-bold mt-2 mb-0 text-success">
                                    {{ $riwayat->where('status', 'hadir')->count() }}</h5>
                                <small class="text-muted">Hadir</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card border-0 rounded-4">
                            <div class="card-body p-3 text-center">
                                <div
                                    style="width:44px;height:44px;margin:0 auto;background:rgba(255,193,7,0.1);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                                    <i class="bi bi-envelope-fill text-warning fs-4"></i>
                                </div>
                                <h5 class="fw-bold mt-2 mb-0 text-warning">
                                    {{ $riwayat->where('status', 'izin')->count() }}
                                </h5>
                                <small class="text-muted">Izin</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card border-0 rounded-4">
                            <div class="card-body p-3 text-center">
                                <div
                                    style="width:44px;height:44px;margin:0 auto;background:rgba(13,202,240,0.1);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                                    <i class="bi bi-heart-pulse-fill text-info fs-4"></i>
                                </div>
                                <h5 class="fw-bold mt-2 mb-0 text-info">{{ $riwayat->where('status', 'sakit')->count() }}
                                </h5>
                                <small class="text-muted">Sakit</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card border-0 rounded-4">
                            <div class="card-body p-3 text-center">
                                <div
                                    style="width:44px;height:44px;margin:0 auto;background:rgba(220,53,69,0.1);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                                    <i class="bi bi-x-circle-fill text-danger fs-4"></i>
                                </div>
                                <h5 class="fw-bold mt-2 mb-0 text-danger">{{ $riwayat->where('status', 'alpa')->count() }}
                                </h5>
                                <small class="text-muted">Alpa</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Riwayat Absensi --}}
                <div class="card border-0 rounded-4">
                    <div
                        class="card-header bg-white border-bottom rounded-top-4 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Absensi
                            Terbaru</h5>
                        <a href="{{ route('siswa.riwayat.index') }}" class="btn btn-sm btn-outline-primary">
                            Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="px-4 py-3">Tanggal</th>
                                        <th class="py-3">Masuk</th>
                                        <th class="py-3">Keluar</th>
                                        <th class="px-4 py-3 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($riwayat as $absen)
                                        <tr>
                                            <td class="px-4 fw-semibold">
                                                <i class="bi bi-calendar3 me-1 text-muted"></i>
                                                {{ $absen->attendance_date?->format('d/m/Y') ?? '-' }}
                                            </td>
                                            <td>
                                                @if ($absen->check_in)
                                                    <span class="badge bg-light text-dark">
                                                        <i class="bi bi-door-open me-1"></i>
                                                        {{ $absen->check_in?->format('H:i') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($absen->check_out)
                                                    <span class="badge bg-light text-dark">
                                                        <i class="bi bi-door-closed me-1"></i>
                                                        {{ $absen->check_out?->format('H:i') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="px-4 text-center">
                                                @switch($absen->status)
                                                    @case('hadir')
                                                        <span class="badge bg-success"><i
                                                                class="bi bi-check-circle me-1"></i>Hadir</span>
                                                    @break

                                                    @case('izin')
                                                        <span class="badge bg-warning text-dark"><i
                                                                class="bi bi-envelope me-1"></i>Izin</span>
                                                    @break

                                                    @case('sakit')
                                                        <span class="badge bg-info"><i
                                                                class="bi bi-heart-pulse me-1"></i>Sakit</span>
                                                    @break

                                                    @default
                                                        <span class="badge bg-danger"><i
                                                                class="bi bi-x-circle me-1"></i>Alpa</span>
                                                @endswitch
                                            </td>
                                        </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-5">
                                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                                    <p class="mb-0">Belum ada riwayat absensi.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Info Card --}}
                    <div class="card border-0 rounded-4 mt-4">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center gap-3">
                                <div
                                    style="width:44px;height:44px;background:rgba(111,66,193,0.1);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="bi bi-info-circle text-primary fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">Informasi</h6>
                                    <p class="text-muted small mb-0">Gunakan QR Code Anda untuk melakukan absensi. Scan QR di
                                        perangkat yang disediakan oleh guru.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    @endsection

    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            (function() {
                const periodSelect = document.getElementById('periodSelect');
                const periodInputs = document.querySelectorAll('.period-input');

                function updatePeriodInput() {
                    periodInputs.forEach((input) => input.style.display = 'none');
                    document.querySelectorAll('.period-' + periodSelect.value).forEach((input) => input.style.display =
                        'block');
                }

                periodSelect.addEventListener('change', updatePeriodInput);
                updatePeriodInput();

                new Chart(document.getElementById('studentAttendanceChart'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Hadir', 'Izin', 'Sakit', 'Alfa'],
                        @php
                            $chartData = [$attendanceStatistics['hadir'], $attendanceStatistics['izin'], $attendanceStatistics['sakit'], $attendanceStatistics['alpa']];
                        @endphp

                        datasets: [{
                            data: @json($chartData),
                            backgroundColor: [
                                '#198754',
                                '#ffc107',
                                '#0dcaf0',
                                '#dc3545'
                            ],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        },
                        cutout: '65%'
                    },
                });
            })();
        </script>
    @endpush
