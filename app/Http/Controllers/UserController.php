<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Halaman daftar user.
     */
    public function index()
    {
        return view('user.index');
    }

    /**
     * Data untuk DataTables (JSON).
     */
    public function data()
    {
        $users = User::orderBy('id', 'desc')->get();

        return datatables()
            ->of($users)
            ->addIndexColumn()
            ->addColumn('level', function ($user) {
                switch ($user->level) {
                    case 0: return 'Owner';
                    case 1: return 'Kepala Gudang';
                    case 2: return 'Kasir';
                    default: return 'Unknown';
                }
            })
            ->addColumn('aksi', function ($user) {
                return '
                    <div class="btn-group">
                        <button type="button" onclick="editForm(`'. route('user.show', $user->id) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>
                        <button type="button" onclick="deleteData(`'. route('user.destroy', $user->id) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                    </div>
                ';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    /**
     * Form create (kalau mau pakai halaman tersendiri).
     */
    public function create()
    {
        return view('user.create');
    }

    /**
     * Simpan user baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'level'    => ['required', Rule::in([0, 1, 2])], // kalau mau pakai role angka
        ]);

        $user = new User();
        $user->name     = $validated['name'];
        $user->email    = $validated['email'];
        $user->password = bcrypt($validated['password']);
        $user->level    = $validated['level'];
        $user->save();

        return redirect()->route('user.index')->with('success', 'Pengguna berhasil ditambahkan');
    }

    /**
     * Tampilkan satu user (JSON, untuk modal edit).
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    /**
     * Update user.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $rules = [
            'name'  => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'level' => ['required', Rule::in([0, 1, 2])],
        ];

        // Password cuma dicek kalau diisi
        if ($request->filled('password')) {
            $rules['password'] = 'string|min:6|confirmed';
        }

        $validated = $request->validate($rules);

        $user->name  = $validated['name'];
        $user->email = $validated['email'];
        $user->level = $validated['level'];

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return response()->json('Data berhasil diupdate', 200);
    }

    /**
     * Hapus user.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json('Data berhasil dihapus', 204);
    }

    /**
     * Halaman profil user login.
     */
    public function profil()
    {
        $user = auth()->user();
        return view('user.profil', compact('user'));
    }
}
