<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{

    /**
     * Menampilkan riwayat absensi siswa
     */
    public function index(Request $request)
    {

        // Ambil siswa login
        $siswa = Auth::user();


        // Query absensi siswa
        $query = $siswa
            ->attendances();



        // Jika ada filter tanggal
        if ($request->date) {

            $query->whereDate(
                'attendance_date',
                $request->date
            );

        }



        // Ambil data
        $riwayat = $query
            ->latest('attendance_date')
            ->latest('check_in')
            ->paginate(10)
            ->withQueryString();



        return view(
            'Siswa.Riwayat.Index',
            compact(
                'siswa',
                'riwayat'
            )
        );

    }

}