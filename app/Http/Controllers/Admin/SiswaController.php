<?php

namespace App\Http\Controllers\Admin;

use App\Exports\SiswaTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\SiswaImport;
use App\Models\SchoolClass;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SiswaController extends Controller
{
    /**
     * Menampilkan semua data siswa.
     */
    public function index(Request $request)
    {
        $search = $request->search;

        $siswas = User::with('schoolClass')
            ->where('role', 'student')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10);

        return view('Admin.Siswa.Index', compact('siswas', 'search'));
    }

    /**
     * Form tambah siswa.
     */
    public function create()
    {
        $classes = SchoolClass::where('status', true)
            ->orderBy('name')
            ->get();

        return view('Admin.Siswa.Create', compact('classes'));
    }

    /**
     * Simpan siswa baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'nis'      => ['required', 'string', 'max:50', 'unique:users,nis'],
            'username' => ['required', 'string', 'max:100', 'unique:users,username'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'class_id' => ['required', 'exists:school_classes,id'],
            'status'   => ['required', 'boolean'],
            'photo'    => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('students', 'public');
        }

        $qrToken  = Str::uuid()->toString();
        $fileName = 'student_' . time() . '.svg';

        Storage::disk('public')->put(
            'qrcodes/' . $fileName,
            QrCode::format('svg')->size(300)->margin(2)->generate($qrToken)
        );

        User::create([
            'name'     => $request->name,
            'nip'      => null,
            'nis'      => $request->nis,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role'     => 'student',
            'class_id' => $request->class_id,
            'qr_token' => $qrToken,
            'qr_code'  => 'qrcodes/' . $fileName,
            'photo'    => $photoPath,
            'status'   => $request->status,
        ]);

        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil ditambahkan.');
    }

    /**
     * Detail siswa.
     */
    public function show(string $id)
    {
        $siswa = User::with('schoolClass')
            ->where('role', 'student')
            ->findOrFail($id);

        return view('Admin.Siswa.Show', compact('siswa'));
    }

    /**
     * Download QR Code PDF.
     */
    public function downloadQr(string $id)
    {
        $siswa = User::with('schoolClass')
            ->where('role', 'student')
            ->findOrFail($id);

        $pdf = Pdf::loadView('Admin.Siswa.QrPdf', compact('siswa'))
            ->setPaper('A4', 'portrait');

        return $pdf->download('QR_' . $siswa->nis . '.pdf');
    }

    /**
     * Form edit siswa.
     */
    public function edit(string $id)
    {
        $siswa = User::where('role', 'student')->findOrFail($id);

        $classes = SchoolClass::where('status', true)
            ->orderBy('name')
            ->get();

        return view('Admin.Siswa.Edit', compact('siswa', 'classes'));
    }

    /**
     * Update data siswa.
     */
    public function update(Request $request, string $id)
    {
        $siswa = User::where('role', 'student')->findOrFail($id);

        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'nis'      => ['required', 'string', 'max:50', 'unique:users,nis,' . $siswa->id],
            'username' => ['required', 'string', 'max:100', 'unique:users,username,' . $siswa->id],
            'class_id' => ['required', 'exists:school_classes,id'],
            'status'   => ['required', 'boolean'],
            'photo'    => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $data = [
            'name'     => $request->name,
            'nis'      => $request->nis,
            'username' => $request->username,
            'class_id' => $request->class_id,
            'status'   => $request->status,
        ];

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['required', 'string', 'min:6', 'confirmed'],
            ]);
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('photo')) {
            if ($siswa->photo && Storage::disk('public')->exists($siswa->photo)) {
                Storage::disk('public')->delete($siswa->photo);
            }
            $data['photo'] = $request->file('photo')->store('students', 'public');
        }

        $siswa->update($data);

        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Hapus siswa — DINONAKTIFKAN.
     * Fitur delete dihapus untuk menjaga integritas data absensi.
     */
    public function destroy(string $id): never
    {
        abort(403, 'Fitur hapus data siswa telah dinonaktifkan.');
    }

    /**
     * Form Import Siswa.
     */
    public function importForm()
    {
        return view('Admin.Siswa.Import');
    }

    /**
     * Import Data Siswa dari Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
        ]);

        $import = new SiswaImport();
        Excel::import($import, $request->file('file'));

        if (count($import->failed) > 0) {
            return redirect()->route('admin.siswa.index')->with([
                'warning'    => 'Import selesai. ' . $import->success . ' data berhasil, ' . count($import->failed) . ' data gagal.',
                'failedRows' => $import->failed,
            ]);
        }

        return redirect()->route('admin.siswa.index')->with('success', 'Berhasil mengimpor ' . $import->success . ' data siswa.');
    }

    /**
     * Download Template Excel Siswa.
     */
    public function downloadTemplate()
    {
        return Excel::download(new SiswaTemplateExport(), 'template_siswa.xlsx');
    }
}
