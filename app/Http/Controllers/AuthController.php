<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\PasswordResetRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

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
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        $remember = $request->has('remember');

        if (Auth::guard('user')->attempt($credentials, $remember)) {

            $request->session()->regenerate();

            return redirect()->intended('/panel/dashboardadmin');
        }

        return back()->with('warning', 'Email atau Password salah!');
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

    public function checkForgotPassword(Request $request)
    {
        $request->validate([
            'nis' => 'required'
        ]);

        $siswa = Siswa::where('nis', $request->nis)->first();

        if (!$siswa) {
            return response()->json([
                'success' => false,
                'message' => 'NIS tidak ditemukan.'
            ]);
        }

        $phone=$siswa->no_hp;

        $maskedPhone=
            substr($phone,0,4).
            str_repeat('*',max(strlen($phone)-8,0)).
            substr($phone,-4);

        return response()->json([
            'success'=>true,
            'phone'=>$maskedPhone
        ]);
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'nis' => 'required'
        ]);

        $siswa = Siswa::where('nis', $request->nis)->first();

        if (!$siswa) {
            return response()->json([
                'success' => false,
                'message' => 'NIS tidak ditemukan.'
            ]);
        }

        // Generate OTP 6 digit
        $otp = rand(100000, 999999);

        // Simpan ke database
        DB::table('password_otps')->updateOrInsert(
            ['nis' => $siswa->nis],
            [
                'no_hp' => $siswa->no_hp,
                'otp_code' => $otp,
                'expires_at' => now()->addMinutes(5),
                'is_verified' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Kirim WhatsApp via Fonnte
        $response = Http::withHeaders([
            'Authorization' => env('FONNTE_TOKEN')
        ])->post('https://api.fonnte.com/send', [
            'target' => $siswa->no_hp,
            'message' => "SMK SMART\n\nKode OTP Anda adalah: {$otp}\n\nKode ini berlaku selama 5 menit.\nJangan berikan kode ini kepada siapa pun."
        ]);

        if (!$response->successful()) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim OTP ke WhatsApp.'
            ]);
        }
        
        session([
            'forgot_nis' => $siswa->nis
        ]);

        return response()->json([
            'success' => true,
            'message' => 'OTP berhasil dikirim.',
            'redirect' => route('verify.otp')
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required'
        ]);

        $nis = session('forgot_nis');

        $otp = DB::table('password_otps')
            ->where('nis', $nis)
            ->where('otp_code', $request->otp)
            ->where('is_verified', 0)
            ->first();

        if (!$otp) {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP tidak valid.'
            ]);
        }

        if (now()->gt($otp->expires_at)) {

            return response()->json([
                'success' => false,
                'message' => 'Kode OTP telah kedaluwarsa.'
            ]);

        }

        DB::table('password_otps')
            ->where('id', $otp->id)
            ->update([
                'is_verified' => 1
            ]);

        session([
            'reset_nis' => $otp->nis
        ]);

        return response()->json([
            'success' => true,
            'redirect' => route('reset.password')
        ]);
    }

    public function showResetPassword()
    {
        if (!session()->has('reset_nis')) {
            return redirect()->route('forgot-password');
        }

        return view('auth.reset-password');
    }

    public function showVerifyOtp()
    {
        return view('auth.verify-otp');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ], [
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.'
        ]);

        $nis = session('reset_nis');

        if (!$nis) {
            return redirect()->route('forgot-password');
        }

        $siswa = Siswa::where('nis', $nis)->first();

        if (!$siswa) {
            return redirect()->route('forgot-password');
        }

        $siswa->password = Hash::make($request->password);
        $siswa->save();

        // Hapus OTP
        DB::table('password_otps')
            ->where('nis', $nis)
            ->delete();

        // Hapus session
        session()->forget([
            'forgot_nis',
            'reset_nis'
        ]);

        return redirect()->route('login')->with('success', 'Password berhasil diubah. Silakan login.');
    }

    public function resendOtp(Request $request)
    {
        $nis = session('forgot_nis');

        if (!$nis) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi telah berakhir. Silakan ulangi proses lupa password.'
            ], 400);
        }

        $siswa = Siswa::where('nis', $nis)->first();

        if (!$siswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data siswa tidak ditemukan.'
            ], 404);
        }

        // Generate OTP baru
        $otp = rand(100000, 999999);

        // Update OTP
        DB::table('password_otps')
            ->where('nis', $nis)
            ->update([
                'otp_code' => $otp,
                'expires_at' => now()->addMinutes(5),
                'is_verified' => 0,
                'updated_at' => now()
            ]);

        // Kirim WhatsApp
        $response = Http::withHeaders([
            'Authorization' => env('FONNTE_TOKEN')
        ])->post('https://api.fonnte.com/send', [
            'target' => $siswa->no_hp,
            'message' => "SMK SMART\n\nKode OTP baru Anda adalah: {$otp}\n\nKode ini berlaku selama 5 menit.\nJangan berikan kode ini kepada siapa pun."
        ]);

        if (!$response->successful()) {

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim ulang OTP.'
            ], 500);

        }

        return response()->json([
            'success' => true,
            'message' => 'OTP baru berhasil dikirim ke WhatsApp.'
        ]);
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