<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ForcedPasswordController extends Controller
{
    public function edit()
    {
        return view('auth.ubahpasswordwajib');
    }

    public function update(Request $request)
    {
        $request->validate([
            'password' => [
                'required',
                'min:8',
                'confirmed',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
            ],
        ], [
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, angka, dan simbol.',
        ]);

        $userId = Auth::guard('user')->id();

        DB::table('users')
            ->where('id', $userId)
            ->update([
                'password' => Hash::make($request->password),
                'must_change_password' => false,
                'updated_at' => now(),
            ]);

        return redirect()->route('dashboardadmin')->with('success', 'Password berhasil diubah.');
    }
}