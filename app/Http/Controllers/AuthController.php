<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //
    public function proseslogin(Request $request)
    {
        if (Auth::guard('siswa')->attempt(['nis' => $request->nis, 'password' => $request->password])) {
            return redirect()->intended('/dashboard');
        } else {
            return redirect('/')->with(['warning' => 'NIS atau Password salah!']);
        }
    }
    public function proseslogout()
    {
        if (Auth::guard('siswa')->check()) {
            Auth::guard('siswa')->logout();
            return redirect('/');
        }
        
    }

    public function proseslogoutadmin()
    {
        if (Auth::guard('user')->check()) {
            Auth::guard('user')->logout();
            return redirect('/panel');
        }
        
    }

    public function prosesloginadmin(Request $request)
    {
        if (Auth::guard('user')->attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect()->intended('/dashboardadmin');
        } else {
            return redirect('/panel')->with(['warning' => 'Username atau Password salah!']);
        }
    }

    
}

