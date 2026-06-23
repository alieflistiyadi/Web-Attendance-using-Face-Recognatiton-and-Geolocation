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
        'email' => 'required|email',

        'password' => [
            'nullable',
            Password::min(8)
                ->mixedCase()
                ->numbers()
                ->symbols()
        ]
    ]);
    
        $id = Auth::user()->id;

        $data = [
            'name'  => $request->name,
            'email' => $request->email
        ];

        // kalau password diisi, baru update password
        if (!empty($request->password)) {
            $data['password'] = Hash::make($request->password);
        }

        // update ke database
        DB::table('users')
            ->where('id', $id)
            ->update($data);

        // balik ke dashboard admin
        return redirect('/panel/dashboardadmin')
            ->with('success', 'Setting berhasil diupdate');
    }
}