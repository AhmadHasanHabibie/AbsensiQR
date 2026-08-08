<?php

namespace App\Http\Controllers\Admin;

use App\Exports\GuruPiketTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\GuruPiketImport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class GuruPiketController extends Controller
{
    /**
     * Menampilkan semua data guru piket
     */
    public function index()
    {
        $guruPikets = User::where('role', 'piket')
            ->latest()
            ->paginate(10);

        return view('Admin.GuruPiket.Index', compact('guruPikets'));
    }

    /**
     * Form tambah guru piket
     */
    public function create()
    {
        return view('Admin.GuruPiket.Create');
    }

    /**
     * Simpan guru piket baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'nip'      => ['required', 'string', 'unique:users,nip'],
            'username' => ['required', 'string', 'unique:users,username'],
            'password' => ['required', 'string', 'min:6'],
            'status'   => ['required'],
        ]);

        User::create([
            'name'     => $request->name,
            'nip'      => $request->nip,
            'nis'      => null,
            'qr_code'  => null,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role'     => 'piket',
            'class_id' => null,
            'status'   => $request->status,
        ]);

        return redirect()->route('admin.guru-piket.index')->with('success', 'Data Guru Piket berhasil ditambahkan.');
    }

    /**
     * Detail guru piket
     */
    public function show(string $id)
    {
        $guruPiket = User::where('role', 'piket')->findOrFail($id);

        return view('Admin.GuruPiket.Show', compact('guruPiket'));
    }

    /**
     * Form edit guru piket
     */
    public function edit(string $id)
    {
        $guruPiket = User::where('role', 'piket')->findOrFail($id);

        return view('Admin.GuruPiket.Edit', compact('guruPiket'));
    }

    /**
     * Update data guru piket
     */
    public function update(Request $request, string $id)
    {
        $guruPiket = User::where('role', 'piket')->findOrFail($id);

        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'nip'      => ['required', 'string', 'unique:users,nip,' . $guruPiket->id],
            'username' => ['required', 'string', 'unique:users,username,' . $guruPiket->id],
            'status'   => ['required'],
        ]);

        $data = [
            'name'     => $request->name,
            'nip'      => $request->nip,
            'username' => $request->username,
            'status'   => $request->status,
        ];

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['string', 'min:6'],
            ]);

            $data['password'] = Hash::make($request->password);
        }

        $guruPiket->update($data);

        return redirect()->route('admin.guru-piket.index')->with('success', 'Data Guru Piket berhasil diperbarui.');
    }

    /**
     * Hapus guru piket — DINONAKTIFKAN.
     * Fitur delete dihapus untuk menjaga integritas data absensi.
     */
    public function destroy(string $id): never
    {
        abort(403, 'Fitur hapus data Guru Piket telah dinonaktifkan.');
    }

    /**
     * Form Import Guru Piket
     */
    public function importForm()
    {
        return view('Admin.GuruPiket.Import');
    }

    /**
     * Import Data Guru Piket dari Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
        ]);

        $import = new GuruPiketImport();
        Excel::import($import, $request->file('file'));

        $result = [
            'success' => $import->success,
            'failed'  => count($import->failed),
            'errors'  => $import->failed,
        ];

        return redirect()->route('admin.guru-piket.index')->with('importResult', json_encode($result));
    }

    /**
     * Download Template Excel Guru Piket
     */
    public function downloadTemplate()
    {
        return Excel::download(new GuruPiketTemplateExport(), 'template_guru_piket.xlsx');
    }
}
