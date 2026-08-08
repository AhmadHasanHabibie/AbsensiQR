<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KelasController extends Controller
{
    /**
     * Menampilkan semua data kelas
     */
    public function index()
    {
        $classes = SchoolClass::with('teacher')
            ->latest()
            ->paginate(10);

        return view('Admin.Kelas.Index', compact('classes'));
    }

    /**
     * Form tambah kelas
     */
    public function create()
    {
        $teachers = User::where('role', 'teacher')
            ->where('status', true)
            ->whereDoesntHave('homeroomClass')
            ->orderBy('name')
            ->get();

        return view('Admin.Kelas.Create', compact('teachers'));
    }

    /**
     * Simpan kelas baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'       => ['required', 'string', 'max:100', 'unique:school_classes,name'],
            'teacher_id' => ['required', Rule::exists('users', 'id')->where('role', 'teacher')],
            'status'     => ['required', 'boolean'],
        ]);

        $teacherUsed = SchoolClass::where('teacher_id', $request->teacher_id)->exists();
        if ($teacherUsed) {
            return back()->withErrors(['teacher_id' => 'Guru tersebut sudah menjadi wali kelas.'])->withInput();
        }

        SchoolClass::create([
            'name'       => $request->name,
            'teacher_id' => $request->teacher_id,
            'status'     => $request->status,
        ]);

        return redirect()->route('admin.kelas.index')->with('success', 'Data kelas berhasil ditambahkan.');
    }

    /**
     * Detail kelas
     */
    public function show(string $id)
    {
        $class = SchoolClass::with(['teacher', 'students'])->findOrFail($id);

        return view('Admin.Kelas.Show', compact('class'));
    }

    /**
     * Form edit kelas
     */
    public function edit(string $id)
    {
        $class = SchoolClass::findOrFail($id);

        $teachers = User::where('role', 'teacher')
            ->where('status', true)
            ->where(function ($query) use ($class) {
                $query->whereDoesntHave('homeroomClass')
                    ->orWhere('id', $class->teacher_id);
            })
            ->orderBy('name')
            ->get();

        return view('Admin.Kelas.Edit', compact('class', 'teachers'));
    }

    /**
     * Update kelas
     */
    public function update(Request $request, string $id)
    {
        $class = SchoolClass::findOrFail($id);

        $request->validate([
            'name'       => ['required', 'string', 'max:100', Rule::unique('school_classes', 'name')->ignore($class->id)],
            'teacher_id' => ['required', Rule::exists('users', 'id')->where('role', 'teacher')],
            'status'     => ['required', 'boolean'],
        ]);

        $teacherUsed = SchoolClass::where('teacher_id', $request->teacher_id)
            ->where('id', '!=', $class->id)
            ->exists();

        if ($teacherUsed) {
            return back()->withErrors(['teacher_id' => 'Guru tersebut sudah menjadi wali kelas.'])->withInput();
        }

        $class->update([
            'name'       => $request->name,
            'teacher_id' => $request->teacher_id,
            'status'     => $request->status,
        ]);

        return redirect()->route('admin.kelas.index')->with('success', 'Data kelas berhasil diperbarui.');
    }

    /**
     * Hapus kelas — DINONAKTIFKAN.
     * Fitur delete dihapus untuk menjaga integritas data absensi.
     */
    public function destroy(string $id): never
    {
        abort(403, 'Fitur hapus data kelas telah dinonaktifkan.');
    }
}