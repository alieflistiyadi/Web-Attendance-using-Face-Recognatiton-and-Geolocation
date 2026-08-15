<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\MataPelajaran;
use App\Models\GuruMataPelajaran;

class GuruController extends Controller
{
    public function index(Request $request)
    {
        $mapel = MataPelajaran::orderBy('nama_mapel')->get();

        $guru = User::with([
            'guruMataPelajaran.mataPelajaran'
        ])
            ->whereIn('role', ['guru', 'superadmin'])

            // Filter role
            ->when($request->role, function ($query) use ($request) {
                if (in_array($request->role, ['guru', 'superadmin'])) {
                    $query->where('role', $request->role);
                }
            })

            // Filter mata pelajaran
            ->when($request->mata_pelajaran_id, function ($query) use ($request) {
                $query->whereHas('guruMataPelajaran', function ($q) use ($request) {
                    $q->where('mata_pelajaran_id', $request->mata_pelajaran_id);
                });
            })

            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('guru.index', compact('guru', 'mapel'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|regex:/^[A-Za-z\s]+$/',
            'email' => [
            'required',
            'email',
            'unique:users,email',
            function ($attribute, $value, $fail) {
                $email = strtolower($value);

                if (
                    !str_ends_with($email, '@gmail.com') &&
                    !str_ends_with($email, '@smksmart.sch.id')
                ) {
                    $fail('Email harus menggunakan domain @gmail.com atau @smksmart.sch.id.');
                }
            },
        ],
            'password' => 'required|min:8',
            'role' => 'required|in:guru,superadmin',
        ], [
            'name.regex' => 'Nama hanya boleh berisi huruf dan spasi.',
        ]);

        DB::table('users')->insert([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect('/guru')
            ->with('success', 'Akun berhasil ditambahkan');
    }

    public function edit(Request $request)
    {
        $guru = DB::table('users')
            ->where('id', $request->id)
            ->whereIn('role', ['guru', 'superadmin'])
            ->first();

        return view('guru.edit', compact(
            'guru',
        ));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
        'name' => 'required|regex:/^[A-Za-z\s]+$/',
        'email' => [
            'required',
            'email',
            'unique:users,email,' . $id,
            function ($attribute, $value, $fail) {
                $email = strtolower($value);

                if (
                    !str_ends_with($email, '@gmail.com') &&
                    !str_ends_with($email, '@smksmart.sch.id')
                ) {
                    $fail('Email harus menggunakan domain @gmail.com atau @smksmart.sch.id.');
                }
            },
        ],
        'role' => 'required|in:guru,superadmin',
        'password' => [
            'nullable',
            'min:8',
            'regex:/[a-z]/',
            'regex:/[A-Z]/',
            'regex:/[0-9]/',
            'regex:/[@$!%*#?&]/'
        ]
    ], [
        'name.regex' => 'Nama hanya boleh berisi huruf dan spasi.',
        'password.min' => 'Password minimal 8 karakter.',
        'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, angka, dan simbol.'
    ]);

        // Proteksi: superadmin tidak boleh menurunkan role akun sendiri
        $isSelf = (int) $id === (int) Auth::guard('user')->id();

        if ($isSelf) {
            $currentUser = DB::table('users')
                ->where('id', $id)
                ->first();

            if ($currentUser->role === 'superadmin' && $request->role !== 'superadmin') {
                return redirect('/guru')
                    ->with('error', 'Anda tidak bisa mengubah role akun Anda sendiri dari Superadmin');
            }
        }

        // Data akun
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'updated_at' => now()
        ];

        // Kalau password diisi, update password
        if ($request->password != "") {
            $data['password'] = Hash::make($request->password);
        }

        // Update data user
        DB::table('users')
            ->where('id', $id)
            ->whereIn('role', ['guru', 'superadmin'])
            ->update($data);

        // Hapus mata pelajaran lama
        DB::table('guru_mata_pelajaran')
            ->where('guru_id', $id)
            ->delete();

        // Simpan mata pelajaran baru jika dipilih
        if ($request->filled('mata_pelajaran_id')) {
            DB::table('guru_mata_pelajaran')->insert([
                'guru_id' => $id,
                'mata_pelajaran_id' => $request->mata_pelajaran_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect('/guru')
            ->with('success', 'Data berhasil diupdate');
    }

    public function delete($id)
    {
        // Cegah superadmin menghapus akunnya sendiri
        if ((int) $id === (int) Auth::guard('user')->id()) {
            return redirect('/guru')
                ->with('error', 'Anda tidak bisa menghapus akun Anda sendiri');
        }

        DB::table('users')
            ->where('id', $id)
            ->whereIn('role', ['guru', 'superadmin'])
            ->delete();

        return redirect('/guru')
            ->with('success', 'Data berhasil dihapus');
    }
}