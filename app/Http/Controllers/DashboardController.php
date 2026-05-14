<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;



class DashboardController extends Controller
{
    //
    public function index()
    {
        $hariini = date('Y-m-d');
        $bulanini = date('m') * 1;
        $tahunini = date('Y');
        $nis = Auth::guard('siswa')->user()->nis;
        $attendancehariini = DB::table('attendance')->where('nis', $nis)->where('tgl_presensi', $hariini)->first();
        $historibulanini = DB::table('attendance')->whereRaw('MONTH(tgl_presensi) = "' . $bulanini . '"')
            ->whereRaw('YEAR(tgl_presensi) = "' . $tahunini . '"')
            ->orderBy('tgl_presensi')
            ->get();
        $rekapattendance = DB::table('attendance')
            ->selectRaw('COUNT(nis) as jmlhadir, SUM(IF(jam_in > "07:00", 1, 0)) as jmlterlambat')
            ->where('nis', $nis)
            ->whereRaw('MONTH(tgl_presensi) = "' . $bulanini . '"')
            ->whereRaw('YEAR(tgl_presensi) = "' . $tahunini . '"')
            ->first();

        $leaderboard = DB::table('attendance')
            ->leftJoin('siswa', 'attendance.nis', '=', 'siswa.nis')
            ->select(
                'attendance.jam_in',
                'siswa.nama_lengkap',
                'siswa.kelas',
                'siswa.kode_jurusan'
            )
            ->whereDate('attendance.tgl_presensi', $hariini) // 🔥 ini penting
            ->orderBy('attendance.jam_in')
            ->get();
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $rekapizin = DB::table('pengajuan_izin')->selectRaw('SUM(IF(status="i",1,0)) as jmlizin,SUM(IF(status="s",1,0)) as jmlsakit')->where('nis', $nis)->whereRaw('MONTH(tanggal_izin) = "' . $bulanini . '"')->whereRaw('YEAR(tanggal_izin) = "' . $tahunini . '"')->where('status_approved', 1)->first();
        return view('dashboard.dashboard', compact(
            'attendancehariini',
            'historibulanini',
            'namabulan',
            'bulanini',
            'tahunini',
            'rekapattendance',
            'rekapizin',
            'leaderboard'
        ));
    }

    public function dashboardadmin()
    {
        $hariini = date("Y-m-d");
        $rekapattendance = DB::table('attendance')
            ->selectRaw('COUNT(nis) as jmlhadir, SUM(IF(jam_in > "07:00", 1, 0)) as jmlterlambat')
            ->where('tgl_presensi', $hariini)
            ->first();
        return view('dashboard.dashboardadmin', compact('rekapattendance'));
    }
}
