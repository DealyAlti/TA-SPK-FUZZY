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
            ->editColumn('level', function ($user) {
                return match ((int) $user->level) {
                    0 => 'Owner',
                    1 => 'Kepala Produksi',
                    2 => 'Admin',
                    default => 'Tidak Diketahui',
                };
            })
            ->addColumn('aksi', function ($user) {
                return '
                    <div class="btn-group">
                        <button type="button" onclick="editForm(`' . route('user.show', $user->id) . '`)" class="btn btn-xs btn-info btn-flat">
                            <i class="fa fa-pencil"></i>
                        </button>
                        <button type="button" onclick="deleteData(`' . route('user.destroy', $user->id) . '`)" class="btn btn-xs btn-danger btn-flat">
                            <i class="fa fa-trash"></i>
                        </button>
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
            'level'    => ['required', Rule::in([0, 1, 2])],
        ], [
            // required -> "tidak boleh kosong"
            'name.required'     => 'Nama tidak boleh kosong',
            'email.required'    => 'Email tidak boleh kosong',
            'password.required' => 'Password tidak boleh kosong',
            'level.required'    => 'Level tidak boleh kosong',

            // detail rules
            'name.max'          => 'Nama maksimal 255 karakter',

            'email.email'       => 'Format email tidak valid',
            'email.unique'      => 'Email sudah terdaftar',

            'password.min'      => 'Password minimal 6 karakter',
            'password.confirmed'=> 'Konfirmasi password tidak cocok',

            'level.in'          => 'Level tidak valid',
        ]);

        $user = new User();
        $user->name     = $validated['name'];
        $user->email    = $validated['email'];
        $user->password = bcrypt($validated['password']);
        $user->level    = $validated['level'];
        $user->save();

        return redirect()
            ->route('user.index')
            ->with('success', 'Pengguna berhasil ditambahkan');
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
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'level' => ['required', Rule::in([0, 1, 2])],
        ];

        // Password cuma dicek kalau diisi (opsional saat edit)
        if ($request->filled('password')) {
            $rules['password'] = 'string|min:6|confirmed';
        }

        $validated = $request->validate($rules, [
            // required -> "tidak boleh kosong"
            'name.required'  => 'Nama tidak boleh kosong',
            'email.required' => 'Email tidak boleh kosong',
            'level.required' => 'Level tidak boleh kosong',

            // detail rules
            'name.max'       => 'Nama maksimal 255 karakter',

            'email.email'    => 'Format email tidak valid',
            'email.unique'   => 'Email sudah terdaftar',

            'password.min'       => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',

            'level.in'       => 'Level tidak valid',
        ]);

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

        // kalau delete via ajax, 200 lebih aman karena ada body text
        return response()->json('Data berhasil dihapus', 200);
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
