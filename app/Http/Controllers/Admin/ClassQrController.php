<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClassQrController extends Controller
{
    /**
     * Halaman QR Code Siswa Per Kelas.
     */
    public function index(Request $request)
    {
        $classes = SchoolClass::where('status', true)
            ->orderBy('name')
            ->get();

        $selectedClassId = $request->input('class_id');
        $selectedClass   = null;
        $students        = collect();

        if ($selectedClassId) {
            $selectedClass = SchoolClass::find($selectedClassId);

            if ($selectedClass) {
                $students = User::with('schoolClass')
                    ->where('role', 'student')
                    ->where('class_id', $selectedClassId)
                    ->orderBy('name')
                    ->get();
            }
        }

        return view('Admin.QrSiswa.Index', compact(
            'classes',
            'selectedClassId',
            'selectedClass',
            'students'
        ));
    }

    /**
     * Download PDF seluruh QR Code Siswa pada kelas yang dipilih.
     */
    public function exportPdf(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:school_classes,id',
        ]);

        $class = SchoolClass::findOrFail($request->class_id);

        $students = User::with('schoolClass')
            ->where('role', 'student')
            ->where('class_id', $class->id)
            ->orderBy('name')
            ->get();

        if ($students->isEmpty()) {
            return redirect()
                ->route('admin.qr-siswa.index', ['class_id' => $class->id])
                ->with('error', 'Belum terdapat data siswa pada kelas ini.');
        }

        $pdf = Pdf::loadView('Admin.QrSiswa.ExportPdf', [
            'class'     => $class,
            'students'  => $students,
            'printDate' => now(),
        ]);

        $pdf->setPaper('A4', 'portrait');

        $fileName = 'QR_Code_Siswa_' . Str::slug($class->name) . '_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($fileName);
    }
}
