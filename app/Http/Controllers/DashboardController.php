<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $hariini = date('Y-m-d');
        $bulanini = date('m') * 1;
        $tahunini = date('Y');
        $nis = Auth::guard('siswa')->user()->nis;

        // Data attendance hari ini milik siswa ini
        $attendancehariini = DB::table('attendance')
            ->where('nis', $nis)
            ->where('tgl_presensi', $hariini)
            ->first();

        // ✅ FIX 1: tambah where('nis', $nis)
        $historibulanini = DB::table('attendance')
            ->where('nis', $nis)
            ->whereRaw('MONTH(tgl_presensi) = "' . $bulanini . '"')
            ->whereRaw('YEAR(tgl_presensi) = "' . $tahunini . '"')
            ->orderBy('tgl_presensi')
            ->get();

        // Rekap hadir & terlambat bulan ini
        $rekapattendance = DB::table('attendance')
            ->selectRaw('COUNT(nis) as jmlhadir, SUM(IF(jam_in > "07:00", 1, 0)) as jmlterlambat')
            ->where('nis', $nis)
            ->whereRaw('MONTH(tgl_presensi) = "' . $bulanini . '"')
            ->whereRaw('YEAR(tgl_presensi) = "' . $tahunini . '"')
            ->first();

        // Leaderboard: siapa yang masuk paling pagi hari ini (semua siswa, bukan filter nis)
        $leaderboard = DB::table('attendance')
            ->leftJoin('siswa', 'attendance.nis', '=', 'siswa.nis')
            ->select(
                'attendance.jam_in',
                'siswa.nama_lengkap',
                'siswa.kelas',
                'siswa.kode_jurusan'
            )
            ->whereDate('attendance.tgl_presensi', $hariini)
            ->orderBy('attendance.jam_in')
            ->get();

        $namabulan = [
            "",
            "Januari",
            "Februari",
            "Maret",
            "April",
            "Mei",
            "Juni",
            "Juli",
            "Agustus",
            "September",
            "Oktober",
            "November",
            "Desember"
        ];

        // Rekap izin & sakit bulan ini
        $rekapizin = DB::table('pengajuan_izin')
            ->selectRaw('SUM(IF(status="i",1,0)) as jmlizin, SUM(IF(status="s",1,0)) as jmlsakit')
            ->where('nis', $nis)
            ->whereRaw('MONTH(tanggal_izin) = "' . $bulanini . '"')
            ->whereRaw('YEAR(tanggal_izin) = "' . $tahunini . '"')
            ->where('status_approved', 1)
            ->first();

            $jumlahHariSekolah = 0;

        for ($i = 1; $i <= date('d'); $i++) {

            $tanggal = date('Y-m-') . str_pad($i, 2, '0', STR_PAD_LEFT);

            $hari = date('N', strtotime($tanggal));

            // Senin(1) - Jumat(5)
            if ($hari <= 5) {
                $jumlahHariSekolah++;
            }
        }

        $jmlhadir = $rekapattendance->jmlhadir ?? 0;
        $jmlizin = $rekapizin->jmlizin ?? 0;
        $jmlsakit = $rekapizin->jmlsakit ?? 0;

        $alpa = $jumlahHariSekolah - ($jmlhadir + $jmlizin + $jmlsakit);

        if ($alpa < 0) {
            $alpa = 0;
        }

        return view('dashboard.dashboard', compact(
            'attendancehariini',
            'historibulanini',
            'namabulan',
            'bulanini',
            'tahunini',
            'rekapattendance',
            'rekapizin',
            'leaderboard',
            'alpa'
        ));
    }

    public function dashboardadmin()
    {
        $jurusan = [
            ['kode' => 'TJKT', 'nama' => 'Teknik Jaringan Komputer dan Telekomunikasi'],
            ['kode' => 'MP', 'nama' => 'Manajemen Perkantoran'],
            ['kode' => 'TM', 'nama' => 'Teknik Mesin'],
        ];

        $notifIzin = DB::table('pengajuan_izin')
        ->where('status_approved', 0)
        ->count();
        
        return view('layouts.admin.jurusan', compact('jurusan', 'notifIzin'));
    }

    public function kelas($kode)
    {
        $kelas = DB::table('siswa')
            ->where('kode_jurusan', $kode)
            ->select('kelas')
            ->groupBy('kelas')
            ->get();

        return view('layouts.admin.kelas', compact('kelas', 'kode'));
    }

    public function rekapBulanan(Request $request, $kode, $kelas, $bulan, $tahun)
{
        $siswa = DB::table('siswa')
            ->where('kelas', $kelas)
            ->where('kode_jurusan', $kode)
            ->get();

        $data = [];

        $today = date('Y-m-d');

        foreach ($siswa as $s) {

            $row = [
                'nama' => $s->nama_lengkap
            ];

            for ($i = 1; $i <= 31; $i++) {

                if (!checkdate($bulan, $i, $tahun)) {
                    $row[$i] = null;
                    continue;
                }

                $tanggal = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);

                $hari = date('N', strtotime($tanggal));

                // weekend
                if ($hari == 6 || $hari == 7) {
                    $row[$i] = '-';
                    continue;
                }

                $absen = DB::table('attendance')
                    ->where('nis', $s->nis)
                    ->whereDate('tgl_presensi', $tanggal)
                    ->first();

                $izin = DB::table('pengajuan_izin')
                    ->where('nis', $s->nis)
                    ->where('tanggal_izin', $tanggal)
                    ->where('status_approved', 1)
                    ->first();

                if ($tanggal > $today) {
                    $row[$i] = '';
                }
                elseif ($absen) {
                    $row[$i] = 'H';
                }
                elseif ($izin) {
                    $row[$i] = $izin->status == 'i' ? 'I' : 'S';
                }
                else {
                    $row[$i] = 'A';
                }
            }

            $data[] = $row;
        }

        $listKelas = DB::table('siswa')
            ->where('kode_jurusan', $kode)
            ->select('kelas')
            ->groupBy('kelas')
            ->pluck('kelas');

        return view('layouts.admin.rekap_bulanan', compact(
            'data',
            'bulan',
            'tahun',
            'kelas',
            'kode',
            'listKelas'
        ));
    }
}