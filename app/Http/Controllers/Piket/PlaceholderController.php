<?php

namespace App\Http\Controllers\Piket;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PlaceholderController extends Controller
{
    public function monitoring()
    {
        return view('Piket.Placeholder.Index', [
            'pageTitle' => 'Monitoring Absensi',
            'icon'      => 'bi-display',
            'desc'      => 'Fitur Monitoring Absensi Real-time Guru Piket sedang dalam pengembangan untuk tahap berikutnya.'
        ]);
    }

    public function terlambat()
    {
        return view('Piket.Placeholder.Index', [
            'pageTitle' => 'Data Terlambat',
            'icon'      => 'bi-alarm',
            'desc'      => 'Fitur Laporan & Rekap Keterlambatan Guru Piket sedang dalam pengembangan untuk tahap berikutnya.'
        ]);
    }

    public function laporan()
    {
        return view('Piket.Placeholder.Index', [
            'pageTitle' => 'Laporan Absensi',
            'icon'      => 'bi-file-earmark-bar-graph',
            'desc'      => 'Fitur Laporan Absensi Sekolah Guru Piket sedang dalam pengembangan untuk tahap berikutnya.'
        ]);
    }

    public function mailbox()
    {
        return view('Piket.Placeholder.Index', [
            'pageTitle' => 'Mailbox Guru Piket',
            'icon'      => 'bi-envelope',
            'desc'      => 'Fitur Pengiriman Pesan Pengingat Internal dari Guru Piket sedang dalam pengembangan untuk tahap berikutnya.'
        ]);
    }
}
