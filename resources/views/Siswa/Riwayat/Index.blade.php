@extends('Layouts.LayoutSiswa')

@section('title', 'Riwayat Absensi')

@section('content')

<div class="container-fluid">


<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h3 class="fw-bold text-primary mb-1">

            Riwayat Absensi

        </h3>


        <p class="text-muted mb-0">

            Lihat riwayat kehadiran berdasarkan tanggal.

        </p>


    </div>


</div>





{{-- FILTER TANGGAL --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">


<div class="card-body">


<form method="GET">


<div class="row g-3">


<div class="col-md-5">


<label class="fw-semibold">

Tanggal Absensi

</label>


<input
type="date"
name="date"
class="form-control"
value="{{ request('date') }}"
>


</div>





<div class="col-md-2 d-flex align-items-end">


<button
type="submit"
class="btn btn-primary w-100"
>


<i class="bi bi-search me-2"></i>

Cari


</button>


</div>



</div>


</form>


</div>


</div>








{{-- DATA RIWAYAT --}}
<div class="card border-0 shadow-sm rounded-4">


<div class="card-header bg-success text-white">


<h5 class="mb-0">


<i class="bi bi-clock-history me-2"></i>


Data Riwayat Absensi


</h5>


</div>





<div class="card-body p-0">


<div class="table-responsive">


<table class="table table-hover align-middle mb-0">


<thead class="table-light">


<tr>

<th width="60">
No
</th>


<th>
Tanggal
</th>


<th>
Jam Masuk
</th>


<th>
Terlambat
</th>


<th>
Status
</th>


</tr>


</thead>




<tbody>


@forelse($riwayat as $absen)


<tr>


<td>

{{ $loop->iteration }}

</td>


<td>

{{ $absen->attendance_date?->format('d-m-Y') ?? '-' }}

</td>


<td>

{{ $absen->check_in?->format('H:i:s') ?? '-' }}

</td>


<td>

@if($absen->is_late || $absen->late_time)

<span class="badge bg-warning text-dark">

Terlambat

</span>

@if($absen->late_time)

<small class="d-block text-muted mt-1">

{{ \Carbon\Carbon::parse($absen->late_time)->format('H:i') }}

</small>

@endif

@if($absen->late_note)

<small class="d-block text-muted mt-1">

{{ $absen->late_note }}

</small>

@endif

@else

<span class="badge bg-success">

Tidak Terlambat

</span>

@endif

</td>


<td>


@switch($absen->status)


@case('hadir')

<span class="badge bg-success">

Hadir

</span>

@break



@case('izin')

<span class="badge bg-warning text-dark">

Izin

</span>

@break



@case('sakit')

<span class="badge bg-info">

Sakit

</span>

@break



@default

<span class="badge bg-danger">

Alpa

</span>


@endswitch


</td>


</tr>



@empty


<tr>

<td colspan="5"
class="text-center text-muted py-4">


Belum ada data absensi.


</td>

</tr>


@endforelse



</tbody>


</table>


</div>


</div>





@if($riwayat->hasPages())

<div class="card-footer">

{{ $riwayat->links() }}

</div>

@endif



</div>



</div>


@endsection
