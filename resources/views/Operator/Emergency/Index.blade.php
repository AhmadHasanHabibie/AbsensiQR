@extends('Layouts.LayoutOperator')

@section('title', 'Absensi Darurat')

@section('content')

{{-- ============================================================ --}}
{{-- HOLIDAY LOCK MODAL (TAHAP 4) --}}
{{-- ============================================================ --}}
@if ($dailyStatus['is_holiday'])
<div class="modal fade" id="holidayLockModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="holidayLockModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-danger text-white rounded-top-4 p-3">
                <h5 class="modal-title fw-bold mb-0" id="holidayLockModalLabel">
                    <i class="bi bi-calendar-x-fill me-2"></i> Maaf Sedang Libur
                </h5>
                <button type="button" id="holidayModalCloseBtn" class="btn-close btn-close-white" aria-label="Kembali ke Dashboard"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <div style="width:72px;height:72px;background:rgba(220,53,69,0.1);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                        <i class="bi bi-moon-stars-fill text-danger" style="font-size:32px;"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Absensi Darurat Tidak Tersedia</h5>
                    <p class="text-muted mb-1">Hari ini adalah <strong class="text-danger">{{ $dailyStatus['status'] }}</strong>.</p>
                    <p class="text-muted small mb-0">Prosedur absensi darurat hanya dapat dilakukan pada hari belajar.</p>
                </div>
                <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">
                    <i class="bi bi-info-circle me-1"></i> {{ \Carbon\Carbon::now('Asia/Jakarta')->isoFormat('dddd, D MMMM YYYY') }}
                </span>
            </div>
            <div class="modal-footer bg-light rounded-bottom-4 p-3 d-flex justify-content-center">
                <a href="{{ route('operator.dashboard') }}" class="btn btn-danger px-5 fw-semibold rounded-pill">
                    <i class="bi bi-arrow-left me-2"></i>Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var holidayModal = new bootstrap.Modal(document.getElementById('holidayLockModal'));
        holidayModal.show();
        document.getElementById('holidayModalCloseBtn').addEventListener('click', function () {
            window.location.href = '{{ route("operator.dashboard") }}';
        });
    });
</script>
@endif

<div class="container-fluid py-2">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Absensi Darurat</h3>
            <p class="text-muted mb-0">Prosedur presensi darurat untuk siswa yang tidak dapat menggunakan Scan QR.</p>
        </div>
        <div class="text-end d-none d-sm-block">
            <div class="fw-semibold text-dark">{{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY') }}</div>
            <div id="realtime-clock" class="text-muted small">{{ \Carbon\Carbon::now()->format('H:i:s') }} WIB</div>
        </div>
    </div>

    {{-- Alert: Tampilkan Holiday Banner jika libur, WARNING jika bukan libur --}}
    @if ($dailyStatus['is_holiday'])
    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 p-3 d-flex align-items-center gap-3">
        <div style="width:48px;height:48px;background:rgba(220,53,69,0.15);border-radius:14px;display:flex;align-items:center;justify-content:center;color:#dc3545;font-size:24px;flex-shrink:0;">
            <i class="bi bi-calendar-x-fill"></i>
        </div>
        <div>
            <h6 class="fw-bold mb-1 text-dark">Hari Ini Sedang Libur</h6>
            <p class="mb-0 text-dark small">Absensi Darurat tidak tersedia karena hari ini adalah <strong>{{ $dailyStatus['status'] }}</strong>. Prosedur ini hanya aktif pada hari belajar.</p>
        </div>
    </div>
    @else
    {{-- Alert Warning Header --}}
    <div class="alert alert-warning border-0 shadow-sm rounded-4 mb-4 p-3 d-flex align-items-center gap-3">
        <div style="width:48px;height:48px;background:rgba(255,193,7,0.2);border-radius:14px;display:flex;align-items:center;justify-content:center;color:#856404;font-size:24px;flex-shrink:0;">
            <i class="bi bi-exclamation-triangle-fill"></i>
        </div>
        <div>
            <h6 class="fw-bold mb-1 text-dark">Prosedur Khusus Absensi Darurat</h6>
            <p class="mb-0 text-dark small">Presensi darurat mencatat data kehadiran dengan status sementara <span class="badge bg-warning text-dark">Hadir Manual</span>. Fitur ini hanya digunakan saat Scan QR tidak memungkinkan.</p>
        </div>
    </div>
    @endif

    @if (! $dailyStatus['is_holiday'])
    <div class="row g-4">

        {{-- Form Absensi Darurat --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 bg-white">
                <div class="card-header bg-warning text-dark p-3 rounded-top-4">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-person-fill-exclamation me-2"></i> Form Absensi Darurat
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('operator.emergency.store') }}" method="POST">
                        @csrf

                        {{-- 1. Dropdown Kelas --}}
                        <div class="mb-3">
                            <label for="classSelect" class="form-label fw-semibold text-dark">1. Pilih Kelas <span class="text-danger">*</span></label>
                            <select id="classSelect" class="form-select @error('class_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach ($classes as $cls)
                                    <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 2. Dropdown Nama Siswa --}}
                        <div class="mb-3">
                            <label for="studentSelect" class="form-label fw-semibold text-dark">2. Nama Siswa <span class="text-danger">*</span></label>
                            <select name="student_id" id="studentSelect" class="form-select @error('student_id') is-invalid @enderror" required disabled>
                                <option value="">-- Pilih Kelas Terlebih Dahulu --</option>
                                @foreach ($students as $std)
                                    <option value="{{ $std->id }}" data-class-id="{{ $std->class_id }}" class="student-option d-none">
                                        {{ $std->name }} (NIS: {{ $std->nis ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('student_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="noStudentWarning" class="text-danger small mt-1 d-none">
                                <i class="bi bi-info-circle me-1"></i> Tidak ada siswa yang tersedia di kelas ini (semua sudah presensi hari ini).
                            </div>
                        </div>

                        {{-- 3. Jam Datang --}}
                        <div class="mb-3">
                            <label for="checkInInput" class="form-label fw-semibold text-dark">3. Jam Datang <span class="text-danger">*</span></label>
                            <input type="time" name="check_in" id="checkInInput" class="form-control @error('check_in') is-invalid @enderror" value="{{ date('H:i') }}" required>
                            @error('check_in')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- 4. Alasan --}}
                        <div class="mb-3">
                            <label for="reasonSelect" class="form-label fw-semibold text-dark">4. Alasan Presensi Darurat <span class="text-danger">*</span></label>
                            <select name="reason_option" id="reasonSelect" class="form-select @error('reason_option') is-invalid @enderror" required>
                                <option value="">-- Pilih Alasan --</option>
                                <option value="HP Rusak" {{ old('reason_option') == 'HP Rusak' ? 'selected' : '' }}>HP Rusak</option>
                                <option value="HP Tertinggal" {{ old('reason_option') == 'HP Tertinggal' ? 'selected' : '' }}>HP Tertinggal</option>
                                <option value="QR Code Rusak" {{ old('reason_option') == 'QR Code Rusak' ? 'selected' : '' }}>QR Code Rusak</option>
                                <option value="QR Code Belum Dibagikan" {{ old('reason_option') == 'QR Code Belum Dibagikan' ? 'selected' : '' }}>QR Code Belum Dibagikan</option>
                                <option value="Kendala Sistem" {{ old('reason_option') == 'Kendala Sistem' ? 'selected' : '' }}>Kendala Sistem</option>
                                <option value="Lainnya" {{ old('reason_option') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            @error('reason_option')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Custom Reason Input (Tampil jika memilih Lainnya) --}}
                        <div class="mb-3 d-none" id="customReasonWrapper">
                            <label for="customReasonInput" class="form-label fw-semibold text-dark">Alasan Spesifik Lainnya <span class="text-danger">*</span></label>
                            <input type="text" name="reason_custom" id="customReasonInput" class="form-control @error('reason_custom') is-invalid @enderror" placeholder="Tuliskan alasan lengkap..." value="{{ old('reason_custom') }}">
                            @error('reason_custom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- 5. Keterangan Tambahan --}}
                        <div class="mb-4">
                            <label for="emergencyNote" class="form-label fw-semibold text-dark">5. Keterangan Tambahan <span class="text-muted font-normal">(Opsional)</span></label>
                            <textarea name="emergency_note" id="emergencyNote" class="form-control" rows="3" placeholder="Catatan opsional dari operator...">{{ old('emergency_note') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-warning text-dark fw-bold w-100 py-2 rounded-3">
                            <i class="bi bi-check-circle-fill me-1"></i> Simpan Absensi Darurat
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Tabel Absensi Darurat Hari Ini --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                <div class="card-header bg-dark text-white p-3 d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-clock-history me-2"></i> Presensi Darurat Hari Ini
                    </h5>
                    <span class="badge bg-warning text-dark fw-bold px-3 py-2">
                        {{ $totalEmergencyHariIni }} Data
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Nama Siswa</th>
                                    <th>NIS</th>
                                    <th>Kelas</th>
                                    <th>Jam Datang</th>
                                    <th>Alasan</th>
                                    <th>Status</th>
                                    <th>Operator</th>
                                    <th class="pe-4">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($emergencyAttendances as $att)
                                    <tr>
                                        <td class="ps-4">
                                            <strong class="text-dark">{{ optional($att->student)->name ?? '-' }}</strong>
                                        </td>
                                        <td><span class="badge bg-light text-dark border">{{ optional($att->student)->nis ?? '-' }}</span></td>
                                        <td><strong class="text-primary">{{ optional(optional($att->student)->schoolClass)->name ?? '-' }}</strong></td>
                                        <td><span class="badge bg-light text-muted border">{{ $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('H:i') : '-' }}</span></td>
                                        <td><small class="text-dark fw-semibold">{{ $att->emergency_reason }}</small></td>
                                        <td>
                                            <span class="badge bg-warning text-dark px-2 py-1">
                                                <i class="bi bi-hand-index-thumb me-1"></i> Hadir Manual
                                            </span>
                                        </td>
                                        <td><small class="text-muted"><i class="bi bi-person-workspace me-1"></i>{{ optional($att->operator)->name ?? 'Operator' }}</small></td>
                                        <td class="pe-4"><small class="text-muted">{{ $att->attendance_date->isoFormat('D MMM YYYY') }}</small></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-5">
                                            <i class="bi bi-shield-check fs-1 d-block mb-2 text-success opacity-50"></i>
                                            <p class="mb-0">Belum ada data presensi darurat yang dicatat hari ini.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @endif {{-- end @if (! $dailyStatus['is_holiday']) --}}

</div>

{{-- MODAL PERINGATAN ABSENSI DARURAT --}}
<div class="modal fade" id="peringatanModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="peringatanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-warning text-dark p-3 rounded-top-4">
                <h5 class="modal-title mb-0 fw-bold" id="peringatanModalLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Peringatan Absensi Darurat
                </h5>
            </div>
            <div class="modal-body p-4 text-center">
                <div style="width:64px;height:64px;background:rgba(255,193,7,0.2);border-radius:20px;display:flex;align-items:center;justify-content:center;color:#856404;font-size:32px;" class="mx-auto mb-3">
                    <i class="bi bi-shield-exclamation"></i>
                </div>

                <h6 class="fw-bold text-dark mb-2">Harap Dibaca Sebelum Melanjutkan</h6>

                <p class="text-dark fs-6 text-start mb-3">
                    Absensi Darurat hanya digunakan apabila siswa benar-benar tidak dapat melakukan Scan QR karena alasan yang sah.
                </p>

                <div class="p-3 bg-light rounded-3 border text-start mb-3 small">
                    <strong class="d-block mb-1 text-muted">Contoh Kondisi Sah:</strong>
                    <ul class="mb-0 ps-3 text-muted">
                        <li>HP Rusak</li>
                        <li>HP Tertinggal</li>
                        <li>QR Code Rusak</li>
                        <li>QR Code Belum Diterima</li>
                        <li>Kendala Sistem</li>
                    </ul>
                </div>

                <div class="alert alert-danger p-2 mb-0 small text-start">
                    <i class="bi bi-info-circle me-1"></i> Seluruh aktivitas akan dicatat pada Log Aktivitas. Penyalahgunaan fitur ini menjadi tanggung jawab Operator.
                </div>
            </div>
            <div class="modal-footer bg-light p-3 rounded-bottom-4 d-flex justify-content-between">
                <a href="{{ route('operator.dashboard') }}" class="btn btn-secondary px-4">
                    <i class="bi bi-arrow-left me-1"></i> Batal
                </a>
                <button type="button" class="btn btn-warning text-dark fw-bold px-4" data-bs-dismiss="modal">
                    <i class="bi bi-check-lg me-1"></i> Lanjutkan
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Realtime Clock
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const clockEl = document.getElementById('realtime-clock');
        if (clockEl) {
            clockEl.textContent = `${hours}:${minutes}:${seconds} WIB`;
        }
    }
    setInterval(updateClock, 1000);

    // Auto Show Peringatan Modal on Load
    const warningModalEl = document.getElementById('peringatanModal');
    if (warningModalEl) {
        const warningModal = new bootstrap.Modal(warningModalEl);
        warningModal.show();
    }

    // Dynamic Class -> Student Dropdown Filtering
    const classSelect = document.getElementById('classSelect');
    const studentSelect = document.getElementById('studentSelect');
    const studentOptions = document.querySelectorAll('.student-option');
    const noStudentWarning = document.getElementById('noStudentWarning');

    classSelect.addEventListener('change', function () {
        const selectedClassId = this.value;

        studentSelect.value = '';
        noStudentWarning.classList.add('d-none');

        if (!selectedClassId) {
            studentSelect.disabled = true;
            studentSelect.options[0].textContent = '-- Pilih Kelas Terlebih Dahulu --';
            studentOptions.forEach(opt => opt.classList.add('d-none'));
            return;
        }

        let visibleCount = 0;
        studentOptions.forEach(opt => {
            if (opt.getAttribute('data-class-id') === selectedClassId) {
                opt.classList.remove('d-none');
                visibleCount++;
            } else {
                opt.classList.add('d-none');
            }
        });

        if (visibleCount > 0) {
            studentSelect.disabled = false;
            studentSelect.options[0].textContent = '-- Pilih Nama Siswa --';
        } else {
            studentSelect.disabled = true;
            studentSelect.options[0].textContent = '-- Tidak Ada Siswa Tersedia --';
            noStudentWarning.classList.remove('d-none');
        }
    });

    // Custom Reason Textbox Toggle
    const reasonSelect = document.getElementById('reasonSelect');
    const customReasonWrapper = document.getElementById('customReasonWrapper');
    const customReasonInput = document.getElementById('customReasonInput');

    function toggleCustomReason() {
        if (reasonSelect.value === 'Lainnya') {
            customReasonWrapper.classList.remove('d-none');
            customReasonInput.setAttribute('required', 'required');
        } else {
            customReasonWrapper.classList.add('d-none');
            customReasonInput.removeAttribute('required');
        }
    }

    reasonSelect.addEventListener('change', toggleCustomReason);
    toggleCustomReason();
});
</script>
@endpush
