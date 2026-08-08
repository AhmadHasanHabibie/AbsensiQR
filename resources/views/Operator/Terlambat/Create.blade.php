@extends('Layouts.LayoutOperator')

@section('title', 'Tambah Siswa Terlambat')

@section('content')

@php
    $allActiveClasses = \App\Models\SchoolClass::where('status', true)->get();
    
    // Urutkan kelas berdasarkan Tingkat (X, XI, XII / 10, 11, 12) -> Nama Kelas
    $classes = $allActiveClasses->sortBy(function($c) {
        $name = trim($c->name);
        if (preg_match('/^(XII|XI|X|12|11|10|[0-9]+)\b/i', $name, $matches)) {
            $val = strtoupper($matches[1]);
            $levelOrder = [
                'X' => 10, '10' => 10,
                'XI' => 11, '11' => 11,
                'XII' => 12, '12' => 12,
            ];
            $rank = $levelOrder[$val] ?? (is_numeric($val) ? (int)$val : 99);
        } else {
            $rank = 99;
        }
        return sprintf('%03d_%s', $rank, $name);
    })->values();

    $studentList = [];
    foreach ($students as $s) {
        $studentList[] = [
            'id'       => $s->id,
            'name'     => $s->name,
            'nis'      => $s->nis ?? '-',
            'class_id' => $s->class_id ?? optional($s->schoolClass)->id,
        ];
    }
@endphp

<div class="container-fluid">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Tambah Siswa Terlambat</h3>
            <p class="text-muted mb-0">Input data keterlambatan siswa setelah Scan QR ditutup.</p>
        </div>
        <a href="{{ route('operator.terlambat.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i> Kembali
        </a>
    </div>

    {{-- Smart Processing Counter Banner --}}
    <div class="alert alert-light border shadow-sm rounded-4 p-3 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <div style="width:44px;height:44px;background:linear-gradient(135deg,#0d6efd,#0dcaf0);border-radius:12px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:22px;">
                <i class="bi bi-cpu-fill"></i>
            </div>
            <div>
                <h6 class="fw-bold text-dark mb-0">Daftar Belum Scan Hari Ini</h6>
                <p class="text-muted small mb-0">Status sementara: <span class="badge bg-secondary opacity-75">Belum Diproses</span></p>
            </div>
        </div>
        <div>
            <span class="badge bg-primary fs-6 px-3 py-2 rounded-3 shadow-sm" id="total-unscanned-badge">
                <i class="bi bi-person-exclamation me-1"></i> <span id="total-unscanned-count">{{ count($studentList) }}</span> Siswa Belum Scan
            </span>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-primary text-white rounded-top-4 p-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-plus-circle me-2"></i> Form Kehadiran Terlambat
                    </h5>
                    <span id="class-unscanned-counter" class="badge bg-light text-primary fw-bold d-none">
                        Belum Scan di Kelas Ini: <span id="class-count-num">0</span> Siswa
                    </span>
                </div>

                <div class="card-body p-4">

                    @if (empty($studentList))
                        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-0 p-4 text-center">
                            <i class="bi bi-check-circle-fill fs-1 text-success d-block mb-2"></i>
                            <h5 class="fw-bold text-dark mb-1">Seluruh Siswa Telah Melakukan Absensi</h5>
                            <p class="text-muted mb-0">Tidak ada siswa alpa / belum scan yang perlu ditandai terlambat hari ini.</p>
                        </div>
                    @else
                        <form method="POST" action="{{ route('operator.terlambat.store') }}" class="needs-validation" novalidate>
                            @csrf

                            <div class="row g-4">

                                {{-- 1. Pilih Kelas --}}
                                <div class="col-12 col-md-6">
                                    <label for="class_id" class="form-label fw-semibold">Kelas <span class="text-danger">*</span></label>
                                    <select id="class_id" name="class_id" class="form-select @error('class_id') is-invalid @enderror" required>
                                        <option value="">Pilih Kelas</option>
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">
                                        Kelas wajib dipilih.
                                    </div>
                                    @error('class_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- 2. Pilih Nama Siswa --}}
                                <div class="col-12 col-md-6">
                                    <label for="student_id" class="form-label fw-semibold">Nama Siswa <span class="text-danger">*</span></label>
                                    <select id="student_id" name="student_id" class="form-select @error('student_id') is-invalid @enderror" disabled required>
                                        <option value="">Pilih kelas terlebih dahulu.</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        Nama siswa wajib dipilih.
                                    </div>
                                    @error('student_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Empty State Alert per Kelas --}}
                                <div class="col-12 d-none" id="empty-class-alert-col">
                                    <div class="alert alert-success border-0 shadow-sm rounded-3 mb-0 d-flex align-items-center gap-2">
                                        <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                                        <span class="fw-semibold">Seluruh siswa pada kelas ini telah selesai diproses.</span>
                                    </div>
                                </div>

                                {{-- 3. Jam Datang --}}
                                <div class="col-12 col-md-6">
                                    <label for="late_time" class="form-label fw-semibold">Jam Datang <span class="text-danger">*</span></label>
                                    <input type="time" id="late_time" name="late_time" class="form-control @error('late_time') is-invalid @enderror" value="{{ old('late_time', now()->format('H:i')) }}" required>
                                    <div class="invalid-feedback">
                                        Jam datang wajib diisi.
                                    </div>
                                    @error('late_time')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- 4. Alasan Terlambat --}}
                                <div class="col-12">
                                    <label for="late_note" class="form-label fw-semibold">Alasan Terlambat</label>
                                    <textarea id="late_note" name="late_note" rows="4" class="form-control @error('late_note') is-invalid @enderror" placeholder="Contoh: Terlambat bangun, macet, kendaraan rusak, mengantar orang tua.">{{ old('late_note') }}</textarea>
                                    @error('late_note')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- 5. Simpan --}}
                                <div class="col-12 d-flex justify-content-between pt-2">
                                    <a href="{{ route('operator.terlambat.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left me-2"></i> Batal
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="btn-save-late">
                                        <i class="bi bi-save me-2"></i> Simpan
                                    </button>
                                </div>

                            </div>
                        </form>
                    @endif

                </div>
            </div>
        </div>
    </div>

</div>

{{-- Bootstrap Modal Konfirmasi Data Terlambat --}}
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header bg-primary text-white rounded-top-4">
                <h5 class="modal-title fw-bold" id="confirmModalLabel">
                    <i class="bi bi-question-circle me-2"></i> Konfirmasi Data Terlambat
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="fw-semibold text-dark mb-3">Apakah data keterlambatan siswa ini sudah benar?</p>
                <div class="card border-0 bg-light rounded-3 p-3 mb-3">
                    <div class="row g-2 text-sm">
                        <div class="col-4 text-muted">Kelas:</div>
                        <div class="col-8 fw-bold text-dark" id="modal-class-name">-</div>
                        
                        <div class="col-4 text-muted">Nama Siswa:</div>
                        <div class="col-8 fw-bold text-dark" id="modal-student-name">-</div>
                        
                        <div class="col-4 text-muted">Jam Datang:</div>
                        <div class="col-8 fw-bold text-dark" id="modal-late-time">-</div>
                        
                        <div class="col-4 text-muted">Alasan:</div>
                        <div class="col-8 text-dark" id="modal-late-note">-</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Batal
                </button>
                <button type="button" class="btn btn-primary px-4" id="btn-confirm-submit">
                    <i class="bi bi-check-circle me-1"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const allStudents = @json($studentList);

    const classSelect = document.getElementById('class_id');
    const studentSelect = document.getElementById('student_id');
    const classUnscannedCounter = document.getElementById('class-unscanned-counter');
    const classCountNum = document.getElementById('class-count-num');
    const emptyClassAlertCol = document.getElementById('empty-class-alert-col');
    const btnSave = document.getElementById('btn-save-late');
    const oldStudentId = "{{ old('student_id') }}";

    const lateForm = document.querySelector('.needs-validation');
    const confirmModalEl = document.getElementById('confirmModal');
    let confirmModal = null;
    if (confirmModalEl && typeof bootstrap !== 'undefined') {
        confirmModal = new bootstrap.Modal(confirmModalEl);
    }

    function updateStudentDropdown() {
        if (!classSelect || !studentSelect) return;

        const selectedClassId = classSelect.value;
        studentSelect.innerHTML = '';

        if (!selectedClassId) {
            studentSelect.disabled = true;
            if (classUnscannedCounter) classUnscannedCounter.classList.add('d-none');
            if (emptyClassAlertCol) emptyClassAlertCol.classList.add('d-none');
            if (btnSave) btnSave.disabled = false;

            const defaultOpt = document.createElement('option');
            defaultOpt.value = '';
            defaultOpt.textContent = 'Pilih kelas terlebih dahulu.';
            studentSelect.appendChild(defaultOpt);
            return;
        }

        const filteredStudents = allStudents.filter(s => String(s.class_id) === String(selectedClassId));

        if (classUnscannedCounter && classCountNum) {
            classCountNum.textContent = filteredStudents.length;
            classUnscannedCounter.classList.remove('d-none');
        }

        if (filteredStudents.length === 0) {
            studentSelect.disabled = true;
            if (emptyClassAlertCol) emptyClassAlertCol.classList.remove('d-none');

            const defaultOpt = document.createElement('option');
            defaultOpt.value = '';
            defaultOpt.textContent = 'Seluruh siswa pada kelas ini telah selesai diproses.';
            studentSelect.appendChild(defaultOpt);
        } else {
            studentSelect.disabled = false;
            if (emptyClassAlertCol) emptyClassAlertCol.classList.add('d-none');

            const defaultOpt = document.createElement('option');
            defaultOpt.value = '';
            defaultOpt.textContent = 'Pilih siswa yang terlambat';
            studentSelect.appendChild(defaultOpt);

            filteredStudents.forEach(student => {
                const opt = document.createElement('option');
                opt.value = student.id;
                opt.textContent = `${student.name} — NIS : ${student.nis}`;
                if (oldStudentId && String(student.id) === String(oldStudentId)) {
                    opt.selected = true;
                }
                studentSelect.appendChild(opt);
            });
        }
    }

    if (classSelect) {
        classSelect.addEventListener('change', updateStudentDropdown);

        if (classSelect.value) {
            updateStudentDropdown();
        }
    }

    if (lateForm) {
        lateForm.addEventListener('submit', function (event) {
            if (!lateForm.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                lateForm.classList.add('was-validated');
                return;
            }

            if (!lateForm.dataset.confirmed) {
                event.preventDefault();

                const selectedClassOpt = classSelect.options[classSelect.selectedIndex];
                const selectedStudentOpt = studentSelect.options[studentSelect.selectedIndex];
                const lateTimeVal = document.getElementById('late_time').value;
                const lateNoteVal = document.getElementById('late_note').value;

                document.getElementById('modal-class-name').textContent = selectedClassOpt ? selectedClassOpt.text : '-';
                document.getElementById('modal-student-name').textContent = selectedStudentOpt ? selectedStudentOpt.text : '-';
                document.getElementById('modal-late-time').textContent = lateTimeVal || '-';
                document.getElementById('modal-late-note').textContent = lateNoteVal ? lateNoteVal : '(Tanpa Alasan)';

                if (confirmModal) {
                    confirmModal.show();
                }
            }
        });
    }

    const btnConfirmSubmit = document.getElementById('btn-confirm-submit');
    if (btnConfirmSubmit) {
        btnConfirmSubmit.addEventListener('click', function () {
            btnConfirmSubmit.disabled = true;
            btnConfirmSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Menyimpan...';

            if (lateForm) {
                lateForm.dataset.confirmed = 'true';
                lateForm.submit();
            }
        });
    }
});
</script>
@endpush



