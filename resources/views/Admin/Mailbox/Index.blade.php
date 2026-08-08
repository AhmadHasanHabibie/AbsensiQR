@extends('Layouts.LayoutAdmin')

@section('title', 'Kotak Surat Pemanggilan Siswa')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-primary mb-1">Kotak Surat Pemanggilan Siswa</h3>
            <p class="text-muted mb-0">Daftar seluruh Surat Pemanggilan Orang Tua / Wali yang diterbitkan oleh Guru Wali Kelas.</p>
        </div>
        <span class="badge bg-primary fs-6">{{ $mailboxes->total() }} Total Surat</span>
    </div>

    @if (session('error'))
        <div class="alert alert-danger rounded-4 mb-4">
            {{ session('error') }}
        </div>
    @endif

    {{-- FILTER SEARCH --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.mailbox.index') }}" class="row g-2">
                <div class="col-md-10">
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Cari nama siswa atau NIS...">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i> Cari</button>
                </div>
            </form>
        </div>
    </div>

    {{-- TABEL SURAT --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-envelope-paper me-2"></i>Riwayat Surat Pemanggilan</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3">No</th>
                            <th class="py-3">Jenis Surat</th>
                            <th class="py-3">Siswa & Kelas</th>
                            <th class="py-3">Guru Wali Kelas</th>
                            <th class="py-3">Perihal / Judul</th>
                            <th class="py-3 text-center">Catatan Minggu Ini</th>
                            <th class="py-3 text-center">Status Siswa</th>
                            <th class="py-3">Tanggal Dibuat</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mailboxes as $mailbox)
                            <tr>
                                <td class="px-4 fw-bold">{{ $loop->iteration + ($mailboxes->currentPage() - 1) * $mailboxes->perPage() }}</td>
                                <td>{!! $mailbox->mail_type_badge !!}</td>
                                <td>
                                    <strong class="d-block text-dark">{{ $mailbox->student->name ?? 'Siswa' }}</strong>
                                    <small class="text-muted">NIS: {{ $mailbox->student->nis ?? '-' }} | Kelas: {{ $mailbox->student->schoolClass->name ?? '-' }}</small>
                                </td>
                                <td>
                                    <strong class="text-dark">{{ $mailbox->teacher->name ?? '-' }}</strong>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">{{ $mailbox->title }}</span>
                                    <small class="d-block text-muted">Periode: {{ $mailbox->week_start->format('d/m/Y') }} - {{ $mailbox->week_end->format('d/m/Y') }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $mailbox->mail_type === 'late' ? 'bg-warning text-dark' : ($mailbox->mail_type === 'permission' ? 'bg-info' : 'bg-danger') }}">{{ $mailbox->alpha_total }} Kali</span>
                                </td>
                                <td class="text-center">
                                    @if ($mailbox->status === 'unread')
                                        <span class="badge bg-danger">Belum Dibaca</span>
                                    @else
                                        <span class="badge bg-success">Sudah Dibaca</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $mailbox->created_at->translatedFormat('d F Y H:i') }}
                                </td>
                                <td class="px-4 text-center">
                                    <a href="{{ route('admin.mailbox.download', $mailbox->id) }}" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-file-earmark-pdf me-1"></i> Unduh PDF
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Belum ada Surat Pemanggilan yang dikirimkan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($mailboxes->hasPages())
            <div class="p-3 bg-light border-top rounded-bottom-4">
                {{ $mailboxes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
