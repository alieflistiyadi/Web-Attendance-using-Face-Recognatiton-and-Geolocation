<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\PasswordResetRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Password default yang diberikan ke siswa saat admin approve reset
    const DEFAULT_RESET_PASSWORD = '12345678';

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

    // ===================== FORGOT PASSWORD (SISWA) =====================

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function submitForgotPassword(Request $request)
    {
        $request->validate([
            'nis' => 'required|string',
            'no_hp' => 'required|string',
        ]);

        $siswa = Siswa::where('nis', $request->nis)
            ->where('no_hp', $request->no_hp)
            ->first();

        if (!$siswa) {
            return back()
                ->withInput()
                ->with('warning', 'NIS dan No HP tidak cocok dengan data kami. Silahkan hubungi admin sekolah.');
        }

        $existing = PasswordResetRequest::where('nis', $siswa->nis)
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return back()->with('warning', 'Anda sudah pernah mengajukan reset password. Mohon tunggu persetujuan admin.');
        }

        PasswordResetRequest::create([
            'nis' => $siswa->nis,
            'no_hp' => $siswa->no_hp,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Permintaan reset password berhasil dikirim. Silahkan tunggu persetujuan admin sekolah.');
    }

    // ===================== FORGOT PASSWORD (ADMIN) =====================

    public function listResetRequests()
    {
        $requests = PasswordResetRequest::with('siswa')
            ->orderByRaw("status = 'pending' desc")
            ->latest()
            ->get();

        return view('konfigurasi.reset-password-siswa', compact('requests'));
    }

    public function approveResetRequest($id)
    {
        $resetRequest = PasswordResetRequest::findOrFail($id);

        $siswa = Siswa::where('nis', $resetRequest->nis)->first();

        if (!$siswa) {
            return back()->with('warning', 'Data siswa tidak ditemukan.');
        }

        $siswa->password = Hash::make(self::DEFAULT_RESET_PASSWORD);
        $siswa->is_default_password = 1;
        $siswa->save();

        $resetRequest->status = 'approved';
        $resetRequest->approved_at = now();
        $resetRequest->save();

        return back()->with('success', 'Password siswa NIS ' . $siswa->nis . ' berhasil direset ke password default: ' . self::DEFAULT_RESET_PASSWORD);
    }

    public function rejectResetRequest(Request $request, $id)
    {
        $resetRequest = PasswordResetRequest::findOrFail($id);
        $resetRequest->status = 'rejected';
        $resetRequest->catatan_admin = $request->catatan_admin;
        $resetRequest->save();

        return back()->with('warning', 'Permintaan reset password telah ditolak.');
    }
}

// ini kode AuthController.php