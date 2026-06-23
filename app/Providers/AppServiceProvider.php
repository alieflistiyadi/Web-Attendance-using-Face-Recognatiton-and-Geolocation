<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
{
    $notifIzin = DB::table('pengajuan_izin')
        ->where('status_approved', 0)
        ->count();

    $listNotif = DB::table('pengajuan_izin')
        ->join('siswa', 'pengajuan_izin.nis', '=', 'siswa.nis')
        ->where('status_approved', 0)
        ->select(
            'siswa.nama_lengkap',
            'pengajuan_izin.status',
            'pengajuan_izin.tanggal_izin'
        )
        ->latest('tanggal_izin')
        ->take(5)
        ->get();

    View::share('notifIzin', $notifIzin);
    View::share('listNotif', $listNotif);
}
}