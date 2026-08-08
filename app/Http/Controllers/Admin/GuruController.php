<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\GuruImport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class GuruController extends Controller
{
    /**
     * Menampilkan semua data guru
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'teacher');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $gurus = $query->latest()
            ->paginate(10)
            ->withQueryString();

        return view('Admin.Guru.Index', compact('gurus'));
    }

    /**
     * Form tambah guru
     */
    public function create()
    {
        return view('Admin.Guru.Create');
    }

    /**
     * Simpan guru baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'nip'      => ['required', 'string'],
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
            'role'     => 'teacher',
            'class_id' => null,
            'status'   => $request->status,
        ]);

        return redirect()->route('admin.guru.index')->with('success', 'Data guru berhasil ditambahkan.');
    }

    /**
     * Detail guru
     */
    public function show(string $id)
    {
        $guru = User::where('role', 'teacher')->findOrFail($id);

        return view('Admin.Guru.Show', compact('guru'));
    }

    /**
     * Form edit guru
     */
    public function edit(string $id)
    {
        $guru = User::where('role', 'teacher')->findOrFail($id);

        return view('Admin.Guru.Edit', compact('guru'));
    }

    /**
     * Update data guru
     */
    public function update(Request $request, string $id)
    {
        $guru = User::where('role', 'teacher')->findOrFail($id);

        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'nip'      => ['required', 'string'],
            'username' => ['required', 'string', 'unique:users,username,' . $guru->id],
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

        $guru->update($data);

        return redirect()->route('admin.guru.index')->with('success', 'Data guru berhasil diperbarui.');
    }

    /**
     * Download Template Excel Guru
     */
    public function downloadTemplate()
    {
        return response()->download(public_path('templates/template_guru.xlsx'));
    }

    /**
     * Hapus guru — DINONAKTIFKAN.
     * Fitur delete dihapus untuk menjaga integritas data absensi.
     */
    public function destroy(string $id): never
    {
        abort(403, 'Fitur hapus data guru telah dinonaktifkan.');
    }

    /**
     * Form Import Guru
     */
    public function importForm()
    {
        return view('Admin.Guru.Import');
    }

    /**
     * Import Data Guru dari Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
        ]);

        $import = new GuruImport();
        Excel::import($import, $request->file('file'));

        if (count($import->failed) > 0) {
            return redirect()->route('admin.guru.index')->with([
                'warning'    => 'Import selesai. ' . $import->success . ' data berhasil, ' . count($import->failed) . ' data gagal.',
                'failedRows' => $import->failed,
            ]);
        }

        return redirect()->route('admin.guru.index')->with('success', 'Berhasil mengimpor ' . $import->success . ' data guru.');
    }
}
