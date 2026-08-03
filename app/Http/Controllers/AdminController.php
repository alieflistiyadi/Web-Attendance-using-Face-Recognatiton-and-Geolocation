<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    public function setting()
    {
        $admin = Auth::user();

        return view('layouts.admin.setting', compact('admin'));
    }

    public function updateSetting(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . Auth::guard('user')->id(),
            'password' => [
                'nullable',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/'
            ]
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'updated_at' => now(),
        ];

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
            $data['must_change_password'] = false;   // <-- TAMBAHKAN INI
        }

        DB::table('users')
            ->where('id', Auth::guard('user')->id())
            ->update($data);

        return redirect('/panel/setting')->with('success', 'Data berhasil diupdate');
    }
}