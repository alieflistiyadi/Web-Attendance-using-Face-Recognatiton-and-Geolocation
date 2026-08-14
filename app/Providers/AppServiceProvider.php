<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('*', function ($view) {

            $notifIzin = 0;
            $listNotif = collect();

            $user = Auth::guard('user')->user();

            // ==========================================
            // HANYA GURU
            // ==========================================
            if ($user && $user->role === 'guru') {

                $guruId = $user->id;

                // ==========================================
                // JUMLAH PENGAJUAN YANG BELUM DIPROSES
                // ==========================================
                $notifIzin = DB::table('pengajuan_izin_detail')
                    ->where('guru_id', $guruId)
                    ->where('status_approved', 0)
                    ->count();

                // ==========================================
                // DATA NOTIFIKASI
                // ==========================================
                $listNotif = DB::table('pengajuan_izin_detail')
                    ->join(
                        'pengajuan_izin',
                        'pengajuan_izin_detail.pengajuan_izin_id',
                        '=',
                        'pengajuan_izin.id'
                    )
                    ->join(
                        'siswa',
                        'pengajuan_izin.nis',
                        '=',
                        'siswa.nis'
                    )
                    ->where(
                        'pengajuan_izin_detail.guru_id',
                        $guruId
                    )
                    ->where(
                        'pengajuan_izin_detail.status_approved',
                        0
                    )
                    ->select(
                        'pengajuan_izin.id',
                        'siswa.nama_lengkap',
                        'siswa.kelas',
                        'pengajuan_izin.status',
                        'pengajuan_izin.tanggal_izin'
                    )
                    ->orderBy(
                        'pengajuan_izin.tanggal_izin',
                        'desc'
                    )
                    ->take(5)
                    ->get();
            }

            // Kirim data ke view
            $view->with('notifIzin', $notifIzin);
            $view->with('listNotif', $listNotif);
        });
    }
}