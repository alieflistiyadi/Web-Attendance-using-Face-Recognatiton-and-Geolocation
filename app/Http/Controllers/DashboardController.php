<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $hariini = date('Y-m-d');
        $bulanini = (int) date('m');
        $tahunini = (int) date('Y');

        $siswa = Auth::guard('siswa')->user();
        $nis = $siswa->nis;

        /*
        |--------------------------------------------------------------------------
        | DATA SISWA
        |--------------------------------------------------------------------------
        */
        $kelasSiswa = $siswa->kelas;
        $jurusanSiswa = $siswa->kode_jurusan;

        $hariIso = now()->dayOfWeekIso; // Senin=1 ... Minggu=7

        /*
        |--------------------------------------------------------------------------
        | PRESENSI HARI INI - PER SUBJECT
        |--------------------------------------------------------------------------
        |
        | Setiap record attendance dianggap sebagai satu sesi/mata pelajaran.
        | Subject dicari dari penugasan/jadwal yang terkait dengan attendance.
        |
        | Prioritas:
        | 1. attendance.penugasan_id
        | 2. attendance.jadwal_id
        | 3. fallback berdasarkan kelas + hari + jam attendance
        |
        | Dengan demikian kode tetap dapat berjalan pada database lama,
        | tetapi untuk hasil paling akurat disarankan attendance menyimpan
        | penugasan_id atau jadwal_id.
        |
        |--------------------------------------------------------------------------
        */

        $attendanceHariIni = DB::table('attendance')
            ->where('nis', $nis)
            ->whereDate('tgl_presensi', $hariini)
            ->orderBy('jam_in')
            ->get();

        $attendanceHariIni = $this->attachSubjectToAttendance(
            $attendanceHariIni,
            $siswa,
            $hariIso
        );

        /*
        |--------------------------------------------------------------------------
        | HISTORI BULAN INI - PER SUBJECT
        |--------------------------------------------------------------------------
        */
        $historibulanini = DB::table('attendance')
            ->where('nis', $nis)
            ->whereMonth('tgl_presensi', $bulanini)
            ->whereYear('tgl_presensi', $tahunini)
            ->orderByDesc('tgl_presensi')
            ->orderBy('jam_in')
            ->get();

        $historibulanini = $this->attachSubjectToAttendanceCollection(
            $historibulanini,
            $siswa
        );

        /*
        |--------------------------------------------------------------------------
        | REKAP ATTENDANCE BULAN INI
        |--------------------------------------------------------------------------
        |
        | Hadir = jumlah sesi/mata pelajaran yang berhasil dihadiri.
        | Telat = jumlah sesi yang jam masuknya melewati batas telat
        |         dari jadwal masing-masing subject.
        |
        /*
        |--------------------------------------------------------------------------
        | REKAP ATTENDANCE BULAN INI
        |--------------------------------------------------------------------------
        |
        | Hadir = jumlah HARI unik yang memiliki minimal 1 attendance.
        | Telat = jumlah SESI/SUBJECT yang terlambat.
        |
        | Contoh 14 Agustus:
        |   Daprog = Hadir
        |   PAI    = Hadir
        |
        | Hasil: Hadir = 1 hari.
        | Jika salah satu subject terlambat, Telat tetap dihitung per sesi.
        |--------------------------------------------------------------------------
        */

        $tanggalMulaiBulan = date('Y-m-01');

        // Hadir dihitung berdasarkan tanggal unik, bukan jumlah subject.
        $tanggalAttendance = DB::table('attendance')
            ->where('nis', $nis)
            ->whereBetween('tgl_presensi', [$tanggalMulaiBulan, $hariini])
            ->select('tgl_presensi')
            ->distinct()
            ->pluck('tgl_presensi')
            ->map(function ($tanggal) {
                return date('Y-m-d', strtotime($tanggal));
            })
            ->unique()
            ->values();

        $jmlhadir = $tanggalAttendance->count();

        // Telat tetap dihitung per attendance/session/subject.
        $jmlterlambat = $historibulanini->filter(function ($item) {
            return $this->isLate($item);
        })->count();

        $rekapattendance = (object) [
            'jmlhadir' => $jmlhadir,
            'jmlterlambat' => $jmlterlambat,
        ];

        /*
        |--------------------------------------------------------------------------
        | REKAP PER MATA PELAJARAN
        |--------------------------------------------------------------------------
        */
        $rekapPerSubject = $historibulanini
            ->groupBy(function ($item) {
                return $item->mata_pelajaran_id ?? 'unknown';
            })
            ->map(function ($items) {
                $first = $items->first();

                return (object) [
                    'mata_pelajaran_id' => $first->mata_pelajaran_id ?? null,
                    'nama_mapel' => $first->nama_mapel ?? 'Mata Pelajaran',
                    'nama_guru' => $first->nama_guru ?? 'Guru',
                    'jumlah_hadir' => $items->count(),
                    'jumlah_telat' => $items->filter(function ($item) {
                        return $this->isLate($item);
                    })->count(),
                ];
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | IZIN & SAKIT BULAN INI
        |--------------------------------------------------------------------------
        */
        /*
        |--------------------------------------------------------------------------
        | IZIN & SAKIT BULAN INI - PER HARI
        |--------------------------------------------------------------------------
        | Izin dan sakit dihitung berdasarkan tanggal unik.
        | Jika pada tanggal yang sama sudah ada attendance, attendance menjadi
        | prioritas sehingga tanggal tersebut tetap dihitung sebagai hadir.
        |--------------------------------------------------------------------------
        */

        $tanggalIzin = DB::table('pengajuan_izin')
            ->where('nis', $nis)
            ->whereBetween('tanggal_izin', [$tanggalMulaiBulan, $hariini])
            ->where('status', 'i')
            ->where('status_approved', 1)
            ->select('tanggal_izin')
            ->distinct()
            ->pluck('tanggal_izin')
            ->map(function ($tanggal) {
                return date('Y-m-d', strtotime($tanggal));
            })
            ->unique()
            ->values();

        $tanggalSakit = DB::table('pengajuan_izin')
            ->where('nis', $nis)
            ->whereBetween('tanggal_izin', [$tanggalMulaiBulan, $hariini])
            ->where('status', 's')
            ->where('status_approved', 1)
            ->select('tanggal_izin')
            ->distinct()
            ->pluck('tanggal_izin')
            ->map(function ($tanggal) {
                return date('Y-m-d', strtotime($tanggal));
            })
            ->unique()
            ->values();

        $tanggalIzin = $tanggalIzin->diff($tanggalAttendance)->values();
        $tanggalSakit = $tanggalSakit->diff($tanggalAttendance)->values();

        $jmlizin = $tanggalIzin->count();
        $jmlsakit = $tanggalSakit->count();

        $rekapizin = (object) [
            'jmlizin' => $jmlizin,
            'jmlsakit' => $jmlsakit,
        ];

        /*
        |--------------------------------------------------------------------------
        | ALPA
        |--------------------------------------------------------------------------
        |
        | Karena attendance sekarang bersifat per-subject, alpa tidak boleh
        | dihitung dengan:
        |
        |   hari sekolah - jumlah attendance
        |
        | karena satu hari dapat memiliki banyak attendance.
        |
        | Alpa dihitung berdasarkan HARI unik:
        |   hari sekolah sampai kemarin
        |   - hari unik yang punya attendance
        |   - hari unik yang punya izin/sakit approved
        |--------------------------------------------------------------------------
        */
        // Gunakan tanggal unik yang sudah dihitung di atas.
        $hariAttendanceUnik = $tanggalAttendance;

        // Gabungkan izin dan sakit approved sebagai hari yang ter-cover.
        $tanggalIzinSakit = $tanggalIzin
            ->merge($tanggalSakit)
            ->unique()
            ->values();

        $hariSekolahSampaiKemarin = 0;
        $hariTercover = 0;

        $hariIniAngka = (int) date('d');

        for ($i = 1; $i < $hariIniAngka; $i++) {
            $tanggal = date('Y-m-') . str_pad($i, 2, '0', STR_PAD_LEFT);
            $hari = (int) date('N', strtotime($tanggal));

            if ($hari <= 5) {
                $hariSekolahSampaiKemarin++;

                if (
                    $hariAttendanceUnik->contains($tanggal) ||
                    $tanggalIzinSakit->contains($tanggal)
                ) {
                    $hariTercover++;
                }
            }
        }

        $alpa = max(
            0,
            $hariSekolahSampaiKemarin - $hariTercover
        );

        /*
        |--------------------------------------------------------------------------
        | LEADERBOARD
        |--------------------------------------------------------------------------
        |
        | Karena satu siswa dapat mempunyai beberapa subject pada hari yang
        | sama, leaderboard menggunakan JAM MASUK TERCEPAT siswa hari ini.
        | Jadi siswa tidak muncul dua kali.
        |--------------------------------------------------------------------------
        */

        $leaderboard = DB::table('attendance')
            ->join('siswa', 'attendance.nis', '=', 'siswa.nis')
            ->whereDate('attendance.tgl_presensi', $hariini)
            ->select(
                'attendance.nis',
                'siswa.nama_lengkap',
                'siswa.kelas',
                'siswa.kode_jurusan',
                DB::raw('MIN(attendance.jam_in) as jam_in')
            )
            ->groupBy(
                'attendance.nis',
                'siswa.nama_lengkap',
                'siswa.kelas',
                'siswa.kode_jurusan'
            )
            ->orderBy('jam_in')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | NAMA BULAN
        |--------------------------------------------------------------------------
        */
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

        return view('dashboard.dashboard', compact(
            'attendanceHariIni',
            'historibulanini',
            'namabulan',
            'bulanini',
            'tahunini',
            'rekapattendance',
            'rekapizin',
            'leaderboard',
            'alpa',
            'rekapPerSubject'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | ATTACH SUBJECT KE ATTENDANCE
    |--------------------------------------------------------------------------
    */
    private function attachSubjectToAttendanceCollection($collection, $siswa)
    {
        $hasil = collect();

        foreach ($collection as $attendance) {
            $hari = (int) date(
                'N',
                strtotime($attendance->tgl_presensi)
            );

            $item = $this->attachSubjectToAttendance(
                collect([$attendance]),
                $siswa,
                $hari
            )->first();

            $hasil->push($item);
        }

        return $hasil;
    }

    private function attachSubjectToAttendance($collection, $siswa, $hari)
    {
        $hasPenugasanId = Schema::hasColumn(
            'attendance',
            'penugasan_id'
        );

        $hasJadwalId = Schema::hasColumn(
            'attendance',
            'jadwal_id'
        );

        foreach ($collection as $attendance) {

            $penugasan = null;
            $jadwal = null;

            /*
            |--------------------------------------------------------------------------
            | PRIORITAS 1: attendance.penugasan_id
            |--------------------------------------------------------------------------
            */
            if (
                $hasPenugasanId &&
                !empty($attendance->penugasan_id)
            ) {
                $penugasan = DB::table('guru_mata_pelajaran as gmp')
                    ->leftJoin(
                        'mata_pelajaran as mp',
                        'gmp.mata_pelajaran_id',
                        '=',
                        'mp.id'
                    )
                    ->leftJoin(
                        'users as guru',
                        'gmp.guru_id',
                        '=',
                        'guru.id'
                    )
                    ->leftJoin(
                        'kelas',
                        'gmp.kelas_id',
                        '=',
                        'kelas.id'
                    )
                    ->where('gmp.id', $attendance->penugasan_id)
                    ->select(
                        'gmp.id as penugasan_id',
                        'gmp.mata_pelajaran_id',
                        'gmp.guru_id',
                        'gmp.kelas_id',
                        'mp.nama_mapel',
                        'guru.name as nama_guru',
                        'kelas.nama_kelas',
                        'kelas.kode_jurusan'
                    )
                    ->first();

                if ($penugasan) {
                    $jadwal = DB::table('jadwal_pelajaran')
                        ->where('penugasan_id', $attendance->penugasan_id)
                        ->where('hari', $hari)
                        ->orderBy('jam_mulai')
                        ->first();
                }
            }

            /*
            |--------------------------------------------------------------------------
            | PRIORITAS 2: attendance.jadwal_id
            |--------------------------------------------------------------------------
            */
            if (
                !$penugasan &&
                $hasJadwalId &&
                !empty($attendance->jadwal_id)
            ) {
                $jadwal = DB::table('jadwal_pelajaran')
                    ->where('id', $attendance->jadwal_id)
                    ->first();

                if ($jadwal) {
                    $penugasan = DB::table('guru_mata_pelajaran as gmp')
                        ->leftJoin(
                            'mata_pelajaran as mp',
                            'gmp.mata_pelajaran_id',
                            '=',
                            'mp.id'
                        )
                        ->leftJoin(
                            'users as guru',
                            'gmp.guru_id',
                            '=',
                            'guru.id'
                        )
                        ->leftJoin(
                            'kelas',
                            'gmp.kelas_id',
                            '=',
                            'kelas.id'
                        )
                        ->where('gmp.id', $jadwal->penugasan_id)
                        ->select(
                            'gmp.id as penugasan_id',
                            'gmp.mata_pelajaran_id',
                            'gmp.guru_id',
                            'gmp.kelas_id',
                            'mp.nama_mapel',
                            'guru.name as nama_guru',
                            'kelas.nama_kelas',
                            'kelas.kode_jurusan'
                        )
                        ->first();
                }
            }

            /*
            |--------------------------------------------------------------------------
            | PRIORITAS 3: DATABASE LAMA
            |--------------------------------------------------------------------------
            |
            | Jika attendance belum mempunyai penugasan_id/jadwal_id,
            | subject dicari berdasarkan:
            |
            | siswa -> kelas -> jadwal
            | tanggal -> hari
            | jam_in -> rentang jam pelajaran
            |--------------------------------------------------------------------------
            */
            if (!$penugasan) {

                $jamIn = $attendance->jam_in;

                $jadwalCandidates = DB::table('jadwal_pelajaran as jp')
                    ->join(
                        'guru_mata_pelajaran as gmp',
                        'jp.penugasan_id',
                        '=',
                        'gmp.id'
                    )
                    ->join(
                        'mata_pelajaran as mp',
                        'gmp.mata_pelajaran_id',
                        '=',
                        'mp.id'
                    )
                    ->join(
                        'users as guru',
                        'gmp.guru_id',
                        '=',
                        'guru.id'
                    )
                    ->join(
                        'kelas',
                        'gmp.kelas_id',
                        '=',
                        'kelas.id'
                    )
                    ->where('jp.hari', $hari)
                    ->where('kelas.nama_kelas', $siswa->kelas)
                    ->where('kelas.kode_jurusan', $siswa->kode_jurusan)
                    ->where('jp.status', 1)
                    ->whereTime('jp.jam_mulai', '<=', $jamIn)
                    ->whereTime('jp.jam_selesai', '>=', $jamIn)
                    ->orderBy('jp.jam_mulai')
                    ->select(
                        'jp.*',
                        'gmp.mata_pelajaran_id',
                        'gmp.guru_id',
                        'gmp.kelas_id',
                        'mp.nama_mapel',
                        'guru.name as nama_guru',
                        'kelas.nama_kelas',
                        'kelas.kode_jurusan'
                    )
                    ->get();

                $jadwal = $jadwalCandidates->first();

                if ($jadwal) {
                    $penugasan = $jadwal;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | DEFAULT VALUE
            |--------------------------------------------------------------------------
            */
            $attendance->penugasan_id =
                $attendance->penugasan_id
                ?? ($penugasan->penugasan_id ?? null);

            $attendance->jadwal_id =
                $attendance->jadwal_id
                ?? ($jadwal->id ?? null);

            $attendance->mata_pelajaran_id =
                $penugasan->mata_pelajaran_id ?? null;

            $attendance->nama_mapel =
                $penugasan->nama_mapel
                ?? 'Mata Pelajaran';

            $attendance->nama_guru =
                $penugasan->nama_guru
                ?? 'Guru';

            $attendance->nama_kelas =
                $penugasan->nama_kelas
                ?? $siswa->kelas;

            $attendance->kode_jurusan =
                $penugasan->kode_jurusan
                ?? $siswa->kode_jurusan;

            /*
            | Batas telat sekarang mengambil dari jadwal subject.
            */
            $attendance->batas_telat =
                $jadwal->batas_telat
                ?? null;
        }

        return $collection;
    }

    /*
    |--------------------------------------------------------------------------
    | CEK TELAT
    |--------------------------------------------------------------------------
    */
    private function isLate($attendance)
    {
        if (
            empty($attendance->jam_in) ||
            empty($attendance->batas_telat)
        ) {
            return false;
        }

        return strtotime($attendance->jam_in)
            > strtotime($attendance->batas_telat);
    }

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD ADMIN
    |--------------------------------------------------------------------------
    |
    | Bagian dashboard admin tetap dipertahankan dari kode sebelumnya.
    |--------------------------------------------------------------------------
    */
    public function dashboardadmin()
    {
        $hariini = date('Y-m-d');
        $hari = now()->dayOfWeekIso;

        $waktu = DB::table('konfigurasi_waktu')
            ->where('hari', $hari)
            ->first();

        $batasTelat = $waktu?->batas_telat;

        if ($batasTelat) {
            $rekapattendance = DB::table('attendance')
                ->selectRaw("
                    COUNT(nis) as jmlhadir,
                    SUM(IF(jam_in > ?,1,0)) as jmlterlambat
                ", [$batasTelat])
                ->whereDate('tgl_presensi', $hariini)
                ->first();
        } else {
            $rekapattendance = DB::table('attendance')
                ->selectRaw("
                    COUNT(nis) as jmlhadir,
                    0 as jmlterlambat
                ")
                ->whereDate('tgl_presensi', $hariini)
                ->first();
        }

        $rekapizin = DB::table('pengajuan_izin')
            ->selectRaw('
                SUM(IF(status="i",1,0)) as jmlizin,
                SUM(IF(status="s",1,0)) as jmlsakit
            ')
            ->whereDate('tanggal_izin', $hariini)
            ->where('status_approved', 1)
            ->first();

        $jmlhadir = $rekapattendance->jmlhadir ?? 0;
        $jmlterlambat = $rekapattendance->jmlterlambat ?? 0;
        $jmlizin = $rekapizin->jmlizin ?? 0;
        $jmlsakit = $rekapizin->jmlsakit ?? 0;

        $totalSiswa = DB::table('siswa')->count();

        $alpa = max(
            0,
            $totalSiswa - ($jmlhadir + $jmlizin + $jmlsakit)
        );

        $persentaseKehadiran = $totalSiswa > 0
            ? round(($jmlhadir / $totalSiswa) * 100, 1)
            : 0;

        $pendingIzin = DB::table('pengajuan_izin')
            ->where('status_approved', 0)
            ->count();

        $aktivitasTerbaru = DB::table('attendance')
            ->join(
                'siswa',
                'attendance.nis',
                '=',
                'siswa.nis'
            )
            ->select(
                'attendance.*',
                'siswa.nama_lengkap'
            )
            ->orderByDesc('attendance.tgl_presensi')
            ->orderByDesc('attendance.jam_in')
            ->limit(10)
            ->get();

        $topTerlambat = DB::table('attendance')
            ->join(
                'siswa',
                'attendance.nis',
                '=',
                'siswa.nis'
            )
            ->whereMonth('tgl_presensi', date('m'))
            ->whereYear('tgl_presensi', date('Y'));

        if ($batasTelat) {
            $topTerlambat->where(
                'jam_in',
                '>',
                $batasTelat
            );
        }

        $topTerlambat = $topTerlambat
            ->selectRaw("
                siswa.nama_lengkap,
                COUNT(*) as total
            ")
            ->groupBy('siswa.nama_lengkap')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $jurusanDashboard = DB::table('jurusan')
            ->leftJoin(
                'siswa',
                'jurusan.kode_jurusan',
                '=',
                'siswa.kode_jurusan'
            )
            ->select(
                'jurusan.kode_jurusan',
                'jurusan.nama_jurusan',
                DB::raw('COUNT(siswa.nis) as total')
            )
            ->groupBy(
                'jurusan.kode_jurusan',
                'jurusan.nama_jurusan'
            )
            ->orderBy('jurusan.kode_jurusan')
            ->get();

        return view(
            'dashboard.dashboardadmin',
            compact(
                'rekapattendance',
                'rekapizin',
                'jmlizin',
                'jmlsakit',
                'jmlhadir',
                'jmlterlambat',
                'totalSiswa',
                'alpa',
                'persentaseKehadiran',
                'pendingIzin',
                'aktivitasTerbaru',
                'topTerlambat',
                'jurusanDashboard'
            )
        );
    }

    public function kelas($kode)
    {
        $kelas = DB::table('siswa')
            ->where('kode_jurusan', $kode)
            ->select('kelas')
            ->groupBy('kelas')
            ->get();

        return view(
            'layouts.admin.kelas',
            compact('kelas', 'kode')
        );
    }

    public function rekapBulanan(
        Request $request,
        $kode,
        $kelas,
        $bulan,
        $tahun
    ) {
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

                $tanggal = $tahun . '-'
                    . str_pad($bulan, 2, '0', STR_PAD_LEFT)
                    . '-'
                    . str_pad($i, 2, '0', STR_PAD_LEFT);

                $hari = date(
                    'N',
                    strtotime($tanggal)
                );

                if ($hari == 6 || $hari == 7) {
                    $row[$i] = '-';
                    continue;
                }

                $absen = DB::table('attendance')
                    ->where('nis', $s->nis)
                    ->whereDate(
                        'tgl_presensi',
                        $tanggal
                    )
                    ->first();

                $izin = DB::table('pengajuan_izin')
                    ->where('nis', $s->nis)
                    ->where(
                        'tanggal_izin',
                        $tanggal
                    )
                    ->where(
                        'status_approved',
                        1
                    )
                    ->first();

                if ($tanggal > $today) {
                    $row[$i] = '';
                } elseif ($absen) {
                    $row[$i] = 'H';
                } elseif ($izin) {
                    $row[$i] =
                        $izin->status == 'i'
                        ? 'I'
                        : 'S';
                } else {
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

        return view(
            'layouts.admin.rekap_bulanan',
            compact(
                'data',
                'bulan',
                'tahun',
                'kelas',
                'kode',
                'listKelas'
            )
        );
    }
}