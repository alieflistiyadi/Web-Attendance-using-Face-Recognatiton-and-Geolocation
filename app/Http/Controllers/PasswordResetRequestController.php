<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class PasswordResetRequestController extends Controller
{
    // Password default yang di-set saat superadmin approve
    const DEFAULT_PASSWORD = 'Guru123!';

    /**
     * Tampilkan form "Lupa Password" (guest, sebelum login)
     */
    public function showForm()
    {
        return view('auth.forgot-password-admin');
    }

    /**
     * Guru submit form lupa password -> buat permintaan reset (status pending)
     */
    public function submit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = DB::table('users')
            ->where('email', $request->email)
            ->whereIn('role', ['guru', 'superadmin'])
            ->first();

        if (!$user) {
            return back()->with('warning', 'Email tidak ditemukan di sistem.');
        }

        // Cegah duplikasi: kalau masih ada request pending untuk user ini, jangan buat baru
        $existingPending = DB::table('password_reset_requests')
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->exists();

        if ($existingPending) {
            return back()->with('warning', 'Permintaan reset password Anda sudah tercatat, mohon tunggu persetujuan Superadmin.');
        }

        DB::table('password_reset_requests')->insert([
            'user_id' => $user->id,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Permintaan reset password berhasil dikirim. Mohon tunggu persetujuan Superadmin.');
    }

    /**
     * Superadmin: lihat daftar semua permintaan reset password
     */
    public function index()
    {
        $requests = DB::table('password_reset_requests')
            ->join('users', 'users.id', '=', 'password_reset_requests.user_id')
            ->select(
                'password_reset_requests.*',
                'users.name',
                'users.email',
                'users.role'
            )
            ->orderBy('password_reset_requests.status') // pending duluan
            ->orderByDesc('password_reset_requests.created_at')
            ->paginate(10);

        return view('konfigurasi.resetpasswordrequest', compact('requests'));
    }

    /**
     * Superadmin: approve permintaan -> reset password ke default & set must_change_password
     */
    public function approve($id)
    {
        $resetRequest = DB::table('password_reset_requests')->where('id', $id)->first();

        if (!$resetRequest) {
            return back()->with('warning', 'Permintaan tidak ditemukan.');
        }

        if ($resetRequest->status === 'approved') {
            return back()->with('warning', 'Permintaan ini sudah pernah disetujui sebelumnya.');
        }

        // Reset password user ke default & paksa ganti password saat login berikutnya
        DB::table('users')
            ->where('id', $resetRequest->user_id)
            ->update([
                'password' => Hash::make(self::DEFAULT_PASSWORD),
                'must_change_password' => true,
                'updated_at' => now(),
            ]);

        DB::table('password_reset_requests')
            ->where('id', $id)
            ->update([
                'status' => 'approved',
                'approved_by' => Auth::guard('user')->id(),
                'approved_at' => now(),
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Password berhasil direset ke default (' . self::DEFAULT_PASSWORD . '). Guru wajib mengganti password saat login berikutnya.');
    }
}