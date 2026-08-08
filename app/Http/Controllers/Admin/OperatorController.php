<?php

namespace App\Http\Controllers\Admin;

use App\Exports\OperatorTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\OperatorImport;
use App\Models\User;
use App\Services\DuplicateAccountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class OperatorController extends Controller
{
    /**
     * Menampilkan semua data operator
     */
    public function index()
    {
        $operators = User::where('role', 'operator')
            ->latest()
            ->paginate(10);

        return view(
            'Admin.Operator.Index',
            compact('operators')
        );
    }

    /**
     * Form tambah operator
     */
    public function create()
    {
        return view('Admin.Operator.Create');
    }

    /**
     * Simpan operator baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255'
            ],
            'nip' => [
                'required',
                'string',
                'unique:users,nip'
            ],
            'username' => [
                'required',
                'string',
                'unique:users,username'
            ],
            'password' => [
                'required',
                'string',
                'min:6'
            ],
            'status' => [
                'required'
            ],
        ]);

        User::create([
            'name'      => $request->name,
            'nip'       => $request->nip,
            'nis'       => null,
            'qr_code'   => null,
            'username'  => $request->username,
            'password'  => Hash::make($request->password),
            'role'      => 'operator',
            'class_id'  => null,
            'status'    => $request->status,
        ]);

        return redirect()
            ->route('admin.operator.index')
            ->with('success', 'Data operator berhasil ditambahkan.');
    }

    /**
     * Detail operator
     */
    public function show(string $id)
    {
        $operator = User::where('role', 'operator')
            ->findOrFail($id);

        return view('Admin.Operator.Show', compact('operator'));
    }

    /**
     * Form edit operator
     */
    public function edit(string $id)
    {
        $operator = User::where('role', 'operator')
            ->findOrFail($id);

        return view('Admin.Operator.Edit', compact('operator'));
    }

    /**
     * Update data operator
     */
    public function update(Request $request, string $id)
    {
        $operator = User::where('role', 'operator')
            ->findOrFail($id);

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255'
            ],
            'nip' => [
                'required',
                'string',
                'unique:users,nip,' . $operator->id
            ],
            'username' => [
                'required',
                'string',
                'unique:users,username,' . $operator->id
            ],
            'status' => [
                'required'
            ],
        ]);

        $data = [
            'name'     => $request->name,
            'nip'      => $request->nip,
            'username' => $request->username,
            'status'   => $request->status,
        ];

        if ($request->filled('password')) {
            $request->validate([
                'password' => [
                    'string',
                    'min:6'
                ],
            ]);

            $data['password'] = Hash::make($request->password);
        }

        $operator->update($data);

        return redirect()
            ->route('admin.operator.index')
            ->with('success', 'Data operator berhasil diperbarui.');
    }

    /**
     * Hapus operator — DINONAKTIFKAN.
     * Fitur delete dihapus untuk menjaga integritas data absensi.
     */
    public function destroy(string $id): never
    {
        abort(403, 'Fitur hapus data operator telah dinonaktifkan.');
    }

    /**
     * Form Import Operator
     */
    public function importForm()
    {
        return view('Admin.Operator.Import');
    }

    /**
     * Import Data Operator dari Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
        ]);

        $import = new OperatorImport();

        Excel::import($import, $request->file('file'));

        $result = [
            'success' => $import->success,
            'failed'  => count($import->failed),
            'errors'  => $import->failed,
        ];

        return redirect()
            ->route('admin.operator.index')
            ->with('importResult', json_encode($result));
    }

    /**
     * Download Template Excel Operator
     */
    public function downloadTemplate()
    {
        return Excel::download(
            new OperatorTemplateExport(),
            'template_operator.xlsx'
        );
    }
}
