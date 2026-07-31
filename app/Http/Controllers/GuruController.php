<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class GuruController extends Controller
{
    public function index()
    {
        $guru = DB::table('users')->paginate(10);

        return view('guru.index', compact('guru'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
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
            'password' => 'required|min:8'
        ]);

        DB::table('users')->insert([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect('/guru')->with('success', 'Guru berhasil ditambahkan');
    }

    public function edit(Request $request)
    {
        $guru = DB::table('users')
            ->where('id', $request->id)
            ->first();

        return view('guru.edit', compact('guru'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
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
            'password' => [
                'required',
                'min:8',
                'regex:/[a-z]/',      // huruf kecil
                'regex:/[A-Z]/',      // huruf besar
                'regex:/[0-9]/',      // angka
                'regex:/[@$!%*#?&]/'  // simbol
            ]
        ],[
            'password.min' => 'Password minimal 8 karakter.',
            'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, angka, dan simbol.'
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'updated_at' => now()
        ];

        if ($request->password != "") {
            $data['password'] = Hash::make($request->password);
        }

        DB::table('users')
            ->where('id', $id)
            ->update($data);

        return redirect('/guru')
            ->with('success', 'Data guru berhasil diupdate');
    }

    public function delete($id)
    {
        DB::table('users')
            ->where('id', $id)
            ->delete();

        return redirect('/guru')
            ->with('success', 'Data guru berhasil dihapus');
    }

}