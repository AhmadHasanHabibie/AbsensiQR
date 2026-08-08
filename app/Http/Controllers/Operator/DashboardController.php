<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Menampilkan Dashboard Operator
     */
    public function index()
    {
        $dailyStatus = \App\Services\AcademicCalendarService::getDailyStatus();
        return view('Operator.Dashboard.Dashboard', compact('dailyStatus'));
    }

    /**
     * Menampilkan Halaman Profil Operator
     */
    public function profil()
    {
        return view('Operator.Profil.Index');
    }
}
