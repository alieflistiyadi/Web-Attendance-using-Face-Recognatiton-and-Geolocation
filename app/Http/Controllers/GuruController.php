<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\MataPelajaran;

class GuruController extends Controller
{
    public function index(Request $request)
    {
        $mapel = MataPelajaran::orderBy('nama_mapel')->get();

        $guru = User::with([
            'guruMataPelajaran.mataPelajaran'
        ])
            ->whereIn('role', ['guru', 'superadmin'])
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
                    if (!str_ends_with(strtolower($value), '@gmail.com')) {
                        $fail('Email harus menggunakan domain @gmail.com.');
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

        return redirect('/guru')->with('success', 'Akun berhasil ditambahkan');
    }

    public function edit(Request $request)
    {
        $guru = DB::table('users')
            ->where('id', $request->id)
            ->whereIn('role', ['guru', 'superadmin'])
            ->first();

        // Ambil semua mata pelajaran
        $mapel = DB::table('mata_pelajaran')
            ->orderBy('nama_mapel')
            ->get();

        // Ambil mata pelajaran yang sudah dimiliki guru
        $mapelGuru = DB::table('guru_mata_pelajaran')
            ->where('guru_id', $request->id)
            ->pluck('mata_pelajaran_id')
            ->unique()
            ->values()
            ->toArray();

        return view('guru.edit', compact(
            'guru',
            'mapel',
            'mapelGuru'
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
                    if (!str_ends_with(strtolower($value), '@gmail.com')) {
                        $fail('Email harus menggunakan domain @gmail.com.');
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

        // Proteksi: superadmin tidak boleh menurunkan role akunnya sendiri
        $isSelf = (int) $id === (int) Auth::guard('user')->id();

        if ($isSelf) {
            $currentUser = DB::table('users')->where('id', $id)->first();

            if ($currentUser->role === 'superadmin' && $request->role !== 'superadmin') {
                return redirect('/guru')
                    ->with('error', 'Anda tidak bisa mengubah role akun Anda sendiri dari Superadmin');
            }
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'updated_at' => now()
        ];

        if ($request->password != "") {
            $data['password'] = Hash::make($request->password);
        }

        DB::table('users')
            ->where('id', $id)
            ->whereIn('role', ['guru', 'superadmin'])
            ->update($data);

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