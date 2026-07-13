<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function prosesLogin(Request $request)
    {
        $credentials = [
            'nis' => $request->nis,
            'password' => $request->password
        ];

        if (Auth::guard('siswa')->attempt($credentials)) {
            return redirect('/dashboard');
        }

        return back()->with('error', 'NIS atau Password salah');
    }
    public function proseslogout()
    {
        if (Auth::guard('siswa')->check()) {
            Auth::guard('siswa')->logout();
            return redirect('/');
        }

    }

    public function proseslogoutadmin(Request $request)
    {
        Auth::guard('user')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/panel');
    }

    public function prosesloginadmin(Request $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        $remember = $request->has('remember');

        if (Auth::guard('user')->attempt($credentials, $remember)) {

            $request->session()->regenerate();

            return redirect()->intended('/panel/dashboardadmin');
        }

        return redirect('/panel')
            ->with('warning', 'Username atau Password salah!');
    }


}

