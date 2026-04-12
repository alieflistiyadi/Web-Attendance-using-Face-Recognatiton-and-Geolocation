<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class DashboardController extends Controller
{
    //
    public function index()
    {
        $hariini = date('Y-m-d');
        $bulanini = date('m') * 1;
        $tahunini = date('Y');
        $nis = Auth::guard('siswa')->user()->nis;
        $attendancehariini = \DB::table('attendance')->where('nis', $nis)->where('tgl_presensi', $hariini)->first();
        $historibulanini = \DB::table('attendance')->where('nis', $nis)->whereRaw('MONTH(tgl_presensi) = "' . $bulanini . '"')->whereRaw('YEAR(tgl_presensi) = "' . $tahunini . '"')->orderBy('tgl_presensi')->get();
        $rekapattendance = \DB::table('attendance')->selectRaw('COUNT(nis) as jmlhadir, SUM(IF(jam_in > "07:00", 1, 0)) as jmlterlambat')->where('nis', $nis)->whereRaw('MONTH(tgl_presensi) = "' . $bulanini . '"')->whereRaw('YEAR(tgl_presensi) = "' . $tahunini . '"')->first();
        $leaderboard= \DB::table('attendance')->join('siswa', 'attendance.nis', '=', 'siswa.nis')->where('tgl_presensi', $hariini)->orderBy('jam_in')->get();
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        return view('dashboard.dashboard', compact('attendancehariini', 'historibulanini', 'namabulan', 'bulanini', 'tahunini', 'rekapattendance', 'leaderboard'));
    }
}
