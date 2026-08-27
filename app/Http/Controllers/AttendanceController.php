<?php

namespace App\Http\Controllers;

use App\Models\Pengajuanizin;
use App\Models\PengajuanIzinDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpKernel\HttpCache\Store;
use App\Models\MataPelajaran;


class AttendanceController extends Controller
{
    public function create()
    {
        $hari_ini = date('Y-m-d');
        $hari = (int) date('N');
        $jam_sekarang = date('H:i:s');

        $nis = Auth::guard('siswa')->user()->nis;

        // Ambil data siswa yang sedang login
        $siswa = DB::table('siswa')
            ->where('nis', $nis)
            ->first();

        $lok_sekolah = DB::table('konfigurasi_lokasi')
            ->where('id', 1)
            ->first();

        $libur = false;
        $pesan_libur = null;

        // Jadwal yang sedang dipakai untuk halaman attendance
        $jadwalAktif = null;

        // modeAbsensi:
        // none = tidak bisa absen
        // in   = absen masuk
        // out  = absen pulang
        $modeAbsensi = 'none';

        if (!$siswa) {
            $libur = true;
            $pesan_libur = 'Data siswa tidak ditemukan. Silakan hubungi admin.';
        } elseif ($hari >= 6) {
            // Sabtu & Minggu
            $libur = true;
            $pesan_libur =
                'Hari ini adalah hari libur (Sabtu/Minggu). Absensi hanya dapat dilakukan pada hari Senin sampai Jumat.';
        } else {

            /*
            |--------------------------------------------------------------------------
            | Ambil semua jadwal siswa hari ini
            |--------------------------------------------------------------------------
            | jadwal_pelajaran.penugasan_id mengarah ke guru_mata_pelajaran.id.
            | Kelas siswa dicocokkan melalui kelas.nama_kelas + kelas.kode_jurusan.
            |--------------------------------------------------------------------------
            */
            $jadwalHariIni = DB::table('jadwal_pelajaran')
                ->join(
                    'kelas',
                    'jadwal_pelajaran.kelas_id',
                    '=',
                    'kelas.id'
                )
                ->join(
                    'guru_mata_pelajaran',
                    'jadwal_pelajaran.penugasan_id',
                    '=',
                    'guru_mata_pelajaran.id'
                )
                ->join(
                    'mata_pelajaran',
                    'guru_mata_pelajaran.mata_pelajaran_id',
                    '=',
                    'mata_pelajaran.id'
                )
                ->select(
                    'jadwal_pelajaran.*',
                    'kelas.tingkat',
                    'kelas.nama_kelas',
                    'kelas.kode_jurusan',
                    'mata_pelajaran.nama_mapel'
                )
                ->where('jadwal_pelajaran.hari', $hari)
                ->where('jadwal_pelajaran.status', 1)
                ->where('kelas.nama_kelas', $siswa->kelas)
                ->where('kelas.kode_jurusan', $siswa->kode_jurusan)
                ->orderBy('jadwal_pelajaran.jam_mulai')
                ->get();

            if ($jadwalHariIni->isEmpty()) {

                $libur = true;
                $pesan_libur =
                    'Tidak ada jadwal pelajaran untuk kelas Anda hari ini. Silakan hubungi admin.';

            } else {

                /*
                |--------------------------------------------------------------------------
                | 1. Cari jadwal yang sedang berlangsung
                |--------------------------------------------------------------------------
                | Untuk absen MASUK:
                | jam_mulai_absen <= jam sekarang <= jam_selesai
                |--------------------------------------------------------------------------
                */
                $jadwalBerlangsung = $jadwalHariIni
                    ->filter(function ($item) use ($jam_sekarang) {
                        return $jam_sekarang >= $item->jam_mulai_absen
                            && $jam_sekarang <= $item->jam_selesai;
                    })
                    ->sortBy('jam_mulai')
                    ->first();

                if ($jadwalBerlangsung) {

                    $jadwalAktif = $jadwalBerlangsung;

                    // Cek attendance untuk mata pelajaran/penugasan ini.
                    $attendance = DB::table('attendance')
                        ->where('nis', $nis)
                        ->where('tgl_presensi', $hari_ini)
                        ->where('penugasan_id', $jadwalAktif->penugasan_id)
                        ->first();

                    if (!$attendance) {

                        // Belum pernah absen masuk untuk jadwal ini.
                        $modeAbsensi = 'in';

                    } elseif (empty($attendance->jam_out)) {

                        /*
                        | Attendance masuk sudah ada.
                        | Sebelum jam selesai, siswa belum boleh absen pulang.
                        */
                        if ($jam_sekarang >= $jadwalAktif->jam_selesai) {
                            $modeAbsensi = 'out';
                        } else {
                            $libur = true;
                            $pesan_libur =
                                'Anda sudah melakukan absensi masuk untuk '
                                . $jadwalAktif->nama_mapel
                                . '. Absensi pulang dapat dilakukan setelah jam pelajaran selesai '
                                . date('H:i', strtotime($jadwalAktif->jam_selesai)) . '.';
                        }

                    } else {

                        $libur = true;
                        $pesan_libur =
                            'Absensi untuk '
                            . $jadwalAktif->nama_mapel
                            . ' hari ini sudah lengkap.';
                    }

                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | 2. Tidak ada jadwal yang sedang berlangsung.
                    | Cari attendance masuk yang belum memiliki jam_out.
                    | Ini memungkinkan siswa melakukan absen pulang setelah
                    | jam_selesai walaupun halaman dibuka beberapa menit kemudian.
                    |--------------------------------------------------------------------------
                    */
                    $jadwalBelumPulang = DB::table('attendance')
                        ->join(
                            'jadwal_pelajaran',
                            'attendance.penugasan_id',
                            '=',
                            'jadwal_pelajaran.penugasan_id'
                        )
                        ->join(
                            'kelas',
                            'jadwal_pelajaran.kelas_id',
                            '=',
                            'kelas.id'
                        )
                        ->join(
                            'guru_mata_pelajaran',
                            'jadwal_pelajaran.penugasan_id',
                            '=',
                            'guru_mata_pelajaran.id'
                        )
                        ->join(
                            'mata_pelajaran',
                            'guru_mata_pelajaran.mata_pelajaran_id',
                            '=',
                            'mata_pelajaran.id'
                        )
                        ->select(
                            'jadwal_pelajaran.*',
                            'attendance.id as attendance_id',
                            'attendance.jam_in',
                            'attendance.jam_out',
                            'kelas.tingkat',
                            'kelas.nama_kelas',
                            'kelas.kode_jurusan',
                            'mata_pelajaran.nama_mapel'
                        )
                        ->where('attendance.nis', $nis)
                        ->where('attendance.tgl_presensi', $hari_ini)
                        ->whereNull('attendance.jam_out')
                        ->where('jadwal_pelajaran.hari', $hari)
                        ->where('jadwal_pelajaran.status', 1)
                        ->where('kelas.nama_kelas', $siswa->kelas)
                        ->where('kelas.kode_jurusan', $siswa->kode_jurusan)
                        ->where('jadwal_pelajaran.jam_selesai', '<=', $jam_sekarang)
                        ->orderByDesc('jadwal_pelajaran.jam_selesai')
                        ->first();

                    if ($jadwalBelumPulang) {

                        $jadwalAktif = $jadwalBelumPulang;
                        $modeAbsensi = 'out';

                    } else {

                        /*
                        |--------------------------------------------------------------------------
                        | 3. Belum masuk waktu jadwal.
                        |--------------------------------------------------------------------------
                        */
                        $jadwalBerikutnya = $jadwalHariIni
                            ->filter(function ($item) use ($jam_sekarang) {
                                return $item->jam_mulai_absen > $jam_sekarang;
                            })
                            ->sortBy('jam_mulai_absen')
                            ->first();

                        if ($jadwalBerikutnya) {

                            $libur = true;
                            $pesan_libur =
                                'Belum ada jadwal absensi yang aktif. '
                                . 'Jadwal berikutnya adalah '
                                . $jadwalBerikutnya->nama_mapel
                                . ' pada pukul '
                                . date('H:i', strtotime($jadwalBerikutnya->jam_mulai_absen))
                                . '.';

                        } else {

                            $libur = true;
                            $pesan_libur =
                                'Tidak ada jadwal absensi yang aktif saat ini. '
                                . 'Silakan kembali sesuai jadwal pelajaran Anda.';
                        }
                    }
                }
            }
        }

        return view(
            'attendance.create',
            compact(
                'lok_sekolah',
                'jadwalAktif',
                'modeAbsensi',
                'libur',
                'pesan_libur'
            )
        );
    }

    public function store(Request $request)
    {
        $nis = Auth::guard('siswa')->user()->nis;
        $tgl_presensi = date('Y-m-d');
        $jam = date('H:i:s');
        $hari = (int) date('N');

        /*
        |--------------------------------------------------------------------------
        | VALIDASI JADWAL
        |--------------------------------------------------------------------------
        */
        $jadwalId = $request->jadwal_id;

        if (!$jadwalId) {
            echo "error|Jadwal absensi tidak ditemukan. Silakan refresh halaman.|jadwal";
            return;
        }

        $siswa = DB::table('siswa')
            ->where('nis', $nis)
            ->first();

        if (!$siswa) {
            echo "error|Data siswa tidak ditemukan.|siswa";
            return;
        }

        // Jadwal harus benar-benar milik kelas siswa yang sedang login.
        $jadwal = DB::table('jadwal_pelajaran')
            ->join(
                'kelas',
                'jadwal_pelajaran.kelas_id',
                '=',
                'kelas.id'
            )
            ->join(
                'guru_mata_pelajaran',
                'jadwal_pelajaran.penugasan_id',
                '=',
                'guru_mata_pelajaran.id'
            )
            ->join(
                'mata_pelajaran',
                'guru_mata_pelajaran.mata_pelajaran_id',
                '=',
                'mata_pelajaran.id'
            )
            ->select(
                'jadwal_pelajaran.*',
                'kelas.tingkat',
                'kelas.nama_kelas',
                'kelas.kode_jurusan',
                'mata_pelajaran.nama_mapel'
            )
            ->where('jadwal_pelajaran.id', $jadwalId)
            ->where('jadwal_pelajaran.hari', $hari)
            ->where('jadwal_pelajaran.status', 1)
            ->where('kelas.nama_kelas', $siswa->kelas)
            ->where('kelas.kode_jurusan', $siswa->kode_jurusan)
            ->first();

        if (!$jadwal) {
            echo "error|Jadwal tidak valid atau tidak sesuai dengan kelas Anda.|jadwal";
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | BLOK SABTU & MINGGU
        |--------------------------------------------------------------------------
        */
        if ($hari >= 6) {
            echo "error|Hari ini adalah hari libur. Absensi hanya dapat dilakukan pada hari Senin sampai Jumat.|libur";
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDASI FACE RECOGNITION
        |--------------------------------------------------------------------------
        */
        $detected_nis = $request->detected_nis;

        if (empty($detected_nis)) {
            echo "error|Wajah tidak terdeteksi atau tidak cocok dengan data manapun. Silakan coba lagi.|wajah";
            return;
        }

        if ($detected_nis != $nis) {
            echo "error|Wajah yang terdeteksi tidak sesuai dengan akun Anda yang sedang login.|wajah";
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDASI LIVENESS
        |--------------------------------------------------------------------------
        */
        if ((int) $request->liveness_passed !== 1) {
            echo "error|Verifikasi liveness belum berhasil. Silakan ulangi verifikasi wajah.|wajah";
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDASI LOKASI
        |--------------------------------------------------------------------------
        */
        $lok_sekolah = DB::table('konfigurasi_lokasi')
            ->where('id', 1)
            ->first();

        if (!$lok_sekolah) {
            echo "error|Konfigurasi lokasi sekolah belum tersedia. Hubungi admin.|lokasi";
            return;
        }

        $lokasi = trim((string) $request->lokasi);

        if (empty($lokasi) || !str_contains($lokasi, ',')) {
            echo "error|Lokasi Anda tidak berhasil dibaca. Pastikan GPS aktif lalu coba lagi.|lokasi";
            return;
        }

        $lokasiuser = array_map('trim', explode(',', $lokasi));

        if (count($lokasiuser) < 2 || !is_numeric($lokasiuser[0]) || !is_numeric($lokasiuser[1])) {
            echo "error|Koordinat lokasi tidak valid. Pastikan GPS aktif lalu coba lagi.|lokasi";
            return;
        }

        $latitudeuser = (float) $lokasiuser[0];
        $longitudeuser = (float) $lokasiuser[1];

        /*
        |--------------------------------------------------------------------------
        | HITUNG JARAK SISWA KE SEKOLAH
        |--------------------------------------------------------------------------
        */
        $jarak = $this->distance(
            $lok_sekolah->latitude,
            $lok_sekolah->longitude,
            $latitudeuser,
            $longitudeuser
        );

        $radius = round($jarak["meters"]);

        if ($radius > $lok_sekolah->radius) {
            echo "error|Maaf Anda berada di luar radius. Jarak Anda "
                . $radius
                . " meter dari sekolah.|radius";
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | CEK ATTENDANCE BERDASARKAN PENUGASAN
        |--------------------------------------------------------------------------
        | Satu siswa boleh memiliki banyak attendance dalam satu hari,
        | karena setiap mata pelajaran mempunyai penugasan_id sendiri.
        |--------------------------------------------------------------------------
        */
        $attendance = DB::table('attendance')
            ->where('nis', $nis)
            ->where('tgl_presensi', $tgl_presensi)
            ->where('penugasan_id', $jadwal->penugasan_id)
            ->first();

        /*
        |--------------------------------------------------------------------------
        | TENTUKAN MASUK / PULANG BERDASARKAN JADWAL
        |--------------------------------------------------------------------------
        */
        if ($attendance) {

            // Sudah masuk dan sudah pulang.
            if (!empty($attendance->jam_out)) {
                echo "error|Absensi untuk "
                    . $jadwal->nama_mapel
                    . " hari ini sudah lengkap.|selesai";
                return;
            }

            // Pulang hanya boleh setelah jam pelajaran selesai.
            if ($jam < $jadwal->jam_selesai) {
                echo "error|Belum waktunya absensi pulang. Absensi pulang dapat dilakukan setelah pukul "
                    . date('H:i', strtotime($jadwal->jam_selesai))
                    . ".|jam";
                return;
            }

            $ket = "out";

        } else {

            // Belum masuk waktu mulai absensi.
            if ($jam < $jadwal->jam_mulai_absen) {
                echo "error|Belum waktunya absensi untuk "
                    . $jadwal->nama_mapel
                    . ". Absensi dibuka mulai pukul "
                    . date('H:i', strtotime($jadwal->jam_mulai_absen))
                    . ".|jam";
                return;
            }

            // Jangan membuat attendance baru setelah pelajaran selesai.
            if ($jam > $jadwal->jam_selesai) {
                echo "error|Waktu absensi masuk untuk "
                    . $jadwal->nama_mapel
                    . " sudah berakhir pada pukul "
                    . date('H:i', strtotime($jadwal->jam_selesai))
                    . ".|jam";
                return;
            }

            $ket = "in";
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDASI & SIMPAN FOTO
        |--------------------------------------------------------------------------
        */
        $image = $request->image;

        if (empty($image) || !str_contains($image, ';base64,')) {
            echo "error|Foto absensi tidak valid. Silakan ulangi verifikasi wajah.|foto";
            return;
        }

        $image_parts = explode(";base64,", $image, 2);

        if (count($image_parts) !== 2) {
            echo "error|Format foto absensi tidak valid.|foto";
            return;
        }

        $image_base64 = base64_decode($image_parts[1], true);

        if ($image_base64 === false) {
            echo "error|Foto absensi tidak dapat diproses.|foto";
            return;
        }

        $folderPath = "public/uploads/absensi/";
        $format_name = $nis . '-' . $tgl_presensi . '-' . $jadwal->penugasan_id . '-' . $ket;
        $filename = $format_name . '.png';
        $file = $folderPath . $filename;

        Storage::put($file, $image_base64);

        /*
        |--------------------------------------------------------------------------
        | ABSEN PULANG
        |--------------------------------------------------------------------------
        */
        if ($ket === "out") {

            $data_pulang = [
                'jam_out' => $jam,
                'foto_out' => $filename,
                'location_out' => $lokasi,
            ];

            $update = DB::table('attendance')
                ->where('tgl_presensi', $tgl_presensi)
                ->where('nis', $nis)
                ->where('penugasan_id', $jadwal->penugasan_id)
                ->whereNull('jam_out')
                ->update($data_pulang);

            if ($update) {

                echo "success|Terima kasih. Absensi pulang "
                    . $jadwal->nama_mapel
                    . " berhasil.|out";

            } else {

                // Jika update gagal, hapus foto yang baru dibuat.
                Storage::delete($file);

                echo "error|Maaf gagal menyimpan absensi pulang. Silakan hubungi admin.|out";
            }

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | ABSEN MASUK
        |--------------------------------------------------------------------------
        */
        $data = [
            'nis' => $nis,
            'penugasan_id' => $jadwal->penugasan_id,
            'tgl_presensi' => $tgl_presensi,
            'jam_in' => $jam,
            'foto_in' => $filename,
            'location_in' => $lokasi,
        ];

        $simpan = DB::table('attendance')->insert($data);

        if ($simpan) {

            $statusMasuk = ($jam <= $jadwal->batas_telat)
                ? 'Tepat waktu'
                : 'Terlambat';

            if ($statusMasuk === 'Tepat waktu') {

                echo "success|Terima kasih. Absensi masuk "
                    . $jadwal->nama_mapel
                    . " berhasil. Status: Tepat waktu.|in";

            } else {

                $menitTerlambat = floor(
                    (strtotime($jam) - strtotime($jadwal->batas_telat)) / 60
                );

                echo "success|Absensi masuk "
                    . $jadwal->nama_mapel
                    . " berhasil. Status: Terlambat "
                    . $menitTerlambat
                    . " menit.|in";
            }

        } else {

            Storage::delete($file);

            echo "error|Maaf gagal menyimpan absensi masuk. Silakan hubungi admin.|in";
        }
    }

    // ✅ Simpan face descriptor dari halaman profil
    // ✅ Simpan face descriptor dari halaman profil
// Mengecek dulu apakah wajah yang sama sudah terdaftar di akun siswa LAIN
// sebelum menyimpan, supaya 1 wajah tidak bisa dipakai untuk >1 akun.
    public function saveDescriptor(Request $request)
    {
        $nis = Auth::guard('siswa')->user()->nis;

        $newDescriptor = json_decode($request->descriptor, true);

        if (!is_array($newDescriptor) || count($newDescriptor) !== 128) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data wajah tidak valid. Silakan coba scan ulang.'
            ], 422);
        }

        // Ambil semua descriptor siswa LAIN (bukan diri sendiri) untuk dicek kemiripan
        $existing = DB::table('siswa')
            ->whereNotNull('face_descriptor')
            ->where('face_descriptor', '!=', '')
            ->where('nis', '!=', $nis)
            ->select('nis', 'nama_lengkap', 'face_descriptor')
            ->get();

        // Threshold sama seperti FaceMatcher(labeledDescriptors, 0.5) di attendance/create.blade.php
        $threshold = 0.5;

        foreach ($existing as $s) {
            $desc = json_decode($s->face_descriptor, true);
            if (!is_array($desc) || count($desc) !== 128) {
                continue;
            }

            $distance = $this->euclideanDistance($newDescriptor, $desc);

            if ($distance < $threshold) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Wajah ini sudah terdaftar pada akun lain (NIS: {$s->nis} - {$s->nama_lengkap}). Satu wajah hanya boleh didaftarkan untuk satu akun."
                ], 409);
            }
        }

        DB::table('siswa')->where('nis', $nis)->update([
            'face_descriptor' => json_encode($newDescriptor)
        ]);

        return response()->json(['status' => 'success']);
    }

    // ✅ Hitung euclidean distance antar 2 face descriptor (128 dimensi)
    private function euclideanDistance(array $a, array $b)
    {
        $sum = 0.0;
        $count = min(count($a), count($b));

        for ($i = 0; $i < $count; $i++) {
            $diff = $a[$i] - $b[$i];
            $sum += $diff * $diff;
        }

        return sqrt($sum);
    }

    // ✅ Ambil semua descriptor untuk matching di frontend
    public function getFaceDescriptors()
    {
        $siswas = DB::table('siswa')
            ->whereNotNull('face_descriptor')
            ->where('face_descriptor', '!=', '')
            ->select('nis', 'nama_lengkap', 'face_descriptor')
            ->get()
            ->map(function ($s) {
                return [
                    'nis' => $s->nis,
                    'nama' => $s->nama_lengkap,
                    'face_descriptor' => json_decode($s->face_descriptor), // array 128 float
                ];
            });

        return response()->json($siswas);
    }

    function distance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;
        $feet = $miles * 5280;
        $yards = $feet / 3;
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;
        return compact('meters');
    }
    public function editprofile()
    {
        $nis = Auth::guard('siswa')->user()->nis;
        $siswa = DB::table('siswa')->where('nis', $nis)->first();
        return view('attendance.editprofile', compact('siswa'));
    }

    public function updateprofile(Request $request)
    {
        $nis = Auth::guard('siswa')->user()->nis;

        $siswa = DB::table('siswa')
            ->where('nis', $nis)
            ->first();

        // =========================
        // VALIDASI GANTI PASSWORD
        // =========================
        if (!empty($request->password_baru)) {

            $request->validate([
                'password_lama' => 'required',
                'password_baru' => 'required|min:6|same:password_baru_confirmation',
                'password_baru_confirmation' => 'required'
            ], [
                'password_lama.required' => 'Password lama wajib diisi.',
                'password_baru.required' => 'Password baru wajib diisi.',
                'password_baru.min' => 'Password baru minimal 6 karakter.',
                'password_baru.same' => 'Konfirmasi password baru tidak sesuai.',
                'password_baru_confirmation.required' => 'Konfirmasi password wajib diisi.'
            ]);

            // CEK PASSWORD LAMA
            if (!Hash::check($request->password_lama, $siswa->password)) {

                return Redirect::back()->with([
                    'error' => 'Password lama yang Anda masukkan tidak sesuai.'
                ]);
            }
        }

        // =========================
        // DATA UPDATE
        // =========================
        // Hanya password yang bisa diubah lewat form ini.
        // nama_lengkap, no_hp, dan foto tidak lagi diedit di sini.
        if (empty($request->password_baru)) {
            return Redirect::back()->with([
                'success' => 'Tidak ada perubahan yang disimpan'
            ]);
        }

        $data = [
            'password' => Hash::make($request->password_baru),
            'is_default_password' => 0
        ];

        DB::table('siswa')
            ->where('nis', $nis)
            ->update($data);

        return Redirect::back()->with([
            'success' => 'Profil berhasil diupdate'
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password_baru' => 'required|min:8|confirmed'
        ]);

        $siswa = Auth::guard('siswa')->user();

        DB::table('siswa')
            ->where('nis', $siswa->nis)
            ->update([
                'password' => Hash::make($request->password_baru),
                'is_default_password' => 0
            ]);

        return redirect('/dashboard')
            ->with('success', 'Password berhasil diubah');
    }

    public function histori()
    {
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        return view('attendance.histori', compact('namabulan'));
    }

    public function gethistori(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $status = $request->status ?? 'semua';
        $nis = Auth::guard('siswa')->user()->nis;

        /*
        |--------------------------------------------------------------------------
        | Ambil attendance berdasarkan penugasan
        |--------------------------------------------------------------------------
        | Tidak lagi keyBy tanggal, karena dalam satu hari siswa dapat
        | mempunyai lebih dari satu mata pelajaran.
        |--------------------------------------------------------------------------
        */
        $presensi = DB::table('attendance')
            ->leftJoin(
                'jadwal_pelajaran',
                function ($join) {
                    $join->on(
                        'attendance.penugasan_id',
                        '=',
                        'jadwal_pelajaran.penugasan_id'
                    )
                        ->whereRaw(
                            'jadwal_pelajaran.hari = WEEKDAY(attendance.tgl_presensi) + 1'
                        );
                }
            )
            ->leftJoin(
                'guru_mata_pelajaran',
                'attendance.penugasan_id',
                '=',
                'guru_mata_pelajaran.id'
            )
            ->leftJoin(
                'mata_pelajaran',
                'guru_mata_pelajaran.mata_pelajaran_id',
                '=',
                'mata_pelajaran.id'
            )
            ->whereMonth('attendance.tgl_presensi', $bulan)
            ->whereYear('attendance.tgl_presensi', $tahun)
            ->where('attendance.nis', $nis)
            ->select(
                'attendance.*',
                'mata_pelajaran.nama_mapel',
                'jadwal_pelajaran.jam_mulai',
                'jadwal_pelajaran.jam_selesai',
                'jadwal_pelajaran.jam_mulai_absen',
                'jadwal_pelajaran.batas_telat'
            )
            ->orderByDesc('attendance.tgl_presensi')
            ->orderBy('jadwal_pelajaran.jam_mulai')
            ->get();

        $presensiByTanggal = $presensi->groupBy('tgl_presensi');

        /*
        |--------------------------------------------------------------------------
        | Izin / Sakit yang disetujui
        |--------------------------------------------------------------------------
        */
        $izin = DB::table('pengajuan_izin')
            ->whereMonth('tanggal_izin', $bulan)
            ->whereYear('tanggal_izin', $tahun)
            ->where('nis', $nis)
            ->where('status_approved', 1)
            ->get()
            ->keyBy(function ($item) {
                return $item->tanggal_izin;
            });

        $histori = collect();

        $today = Carbon::today();
        $tanggalDipilih = Carbon::create($tahun, $bulan, 1);

        if ($tanggalDipilih->startOfMonth()->gt($today->copy()->startOfMonth())) {
            return view('attendance.gethistori', compact('histori'));
        }

        if ($bulan == $today->month && $tahun == $today->year) {
            $jumlahHari = $today->day;
        } else {
            $jumlahHari = Carbon::create($tahun, $bulan)->daysInMonth;
        }

        for ($i = 1; $i <= $jumlahHari; $i++) {

            $tanggal = Carbon::create($tahun, $bulan, $i)->format('Y-m-d');
            $hari = Carbon::create($tahun, $bulan, $i)->dayOfWeek;

            // Lewati Sabtu & Minggu.
            if ($hari == Carbon::SATURDAY || $hari == Carbon::SUNDAY) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Presensi
            |--------------------------------------------------------------------------
            */
            if ($presensiByTanggal->has($tanggal)) {

                foreach ($presensiByTanggal->get($tanggal) as $data) {

                    $data->tipe = 'presensi';

                    if (!empty($data->jam_in) && !empty($data->batas_telat)) {

                        $jamMasuk = Carbon::parse($data->jam_in);
                        $batasTelat = Carbon::parse($data->batas_telat);

                        if ($jamMasuk->lessThanOrEqualTo($batasTelat)) {

                            $data->tepat_waktu = true;
                            $data->terlambat = 0;
                            $data->status = 'Tepat Waktu';
                            $data->status_class = 'status-success';

                        } else {

                            $data->tepat_waktu = false;
                            $data->terlambat =
                                $batasTelat->diffInMinutes($jamMasuk);

                            $data->status =
                                'Terlambat ' . $data->terlambat . ' Menit';

                            $data->status_class = 'status-warning';
                        }

                    } else {

                        // Attendance lama yang belum mempunyai penugasan_id.
                        $data->tepat_waktu = true;
                        $data->terlambat = 0;
                        $data->status = 'Tepat Waktu';
                        $data->status_class = 'status-success';
                    }

                    $histori->push($data);
                }

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Izin / Sakit
            |--------------------------------------------------------------------------
            */
            if ($izin->has($tanggal)) {

                $data = $izin[$tanggal];

                $histori->push((object) [
                    'tgl_presensi' => $tanggal,
                    'jam_in' => null,
                    'jam_out' => null,
                    'foto_in' => null,
                    'foto_out' => null,
                    'penugasan_id' => null,
                    'nama_mapel' => null,
                    'jam_mulai' => null,
                    'jam_selesai' => null,
                    'tipe' => $data->status == 'i' ? 'izin' : 'sakit',
                    'status' => $data->status == 'i' ? 'Izin' : 'Sakit',
                    'status_class' => $data->status == 'i'
                        ? 'status-info'
                        : 'status-warning',
                ]);

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Alpa
            |--------------------------------------------------------------------------
            | Alpa masih dihitung satu kali per hari agar tetap kompatibel
            | dengan tampilan histori lama. Untuk rekap alpa per mata pelajaran,
            | perlu tabel/aturan khusus jadwal kehadiran yang akan kita buat
            | terpisah.
            |--------------------------------------------------------------------------
            */
            $histori->push((object) [
                'tgl_presensi' => $tanggal,
                'jam_in' => null,
                'jam_out' => null,
                'foto_in' => null,
                'foto_out' => null,
                'penugasan_id' => null,
                'nama_mapel' => null,
                'jam_mulai' => null,
                'jam_selesai' => null,
                'tipe' => 'alpa',
                'status' => 'Alpa',
                'status_class' => 'status-danger',
            ]);
        }

        $histori = $histori
            ->sortByDesc('tgl_presensi')
            ->values();

        if ($status != 'semua') {
            $histori = $histori
                ->where('tipe', $status)
                ->values();
        }

        return view('attendance.gethistori', compact('histori'));
    }

    public function izin(Request $request)
    {
        $nis = Auth::guard('siswa')->user()->nis;

        $query = DB::table('pengajuan_izin')
            ->where('nis', $nis);

        // Filter jenis
        if ($request->filled('jenis')) {
            $query->where('status', $request->jenis);
        }

        // Filter status approval
        if ($request->filled('approve')) {
            $query->where('status_approved', $request->approve);
        }

        $dataizin = $query
            ->orderBy('tanggal_izin', 'desc')
            ->get();

        return view('attendance.izin', compact('dataizin'));
    }

    public function buatizin()
    {

        return view('attendance.buatizin');
    }

    public function storeizin(Request $request)
    {
        $nis = Auth::guard('siswa')->user()->nis;
        $tanggal_izin = date('Y-m-d', strtotime($request->tanggal_izin));
        $status = $request->status;
        $keterangan = $request->keterangan;

        // =========================
        // VALIDASI SURAT IZIN
        // =========================
        if ($status == "i" && !$request->hasFile('surat_izin')) {
            return redirect()->back()->with([
                'error' => 'Surat izin wajib diupload'
            ]);
        }

        $surat_izin = null;

        if ($request->hasFile('surat_izin')) {
            $file = $request->file('surat_izin');

            $surat_izin = time() . "_" . $file->getClientOriginalName();

            $file->storeAs(
                'public/uploads/surat_izin',
                $surat_izin
            );
        }

        // =========================
        // VALIDASI SURAT SAKIT
        // =========================
        if ($status == "s" && !$request->hasFile('surat_sakit')) {
            return redirect()->back()->with([
                'error' => 'Surat sakit wajib diupload'
            ]);
        }

        $surat_sakit = null;

        if ($request->hasFile('surat_sakit')) {
            $file = $request->file('surat_sakit');

            $surat_sakit = time() . "_" . $file->getClientOriginalName();

            $file->storeAs(
                'public/uploads/surat_sakit',
                $surat_sakit
            );
        }

        // =========================
        // AMBIL DATA SISWA
        // =========================
        $siswa = DB::table('siswa')
            ->where('nis', $nis)
            ->first();

        if (!$siswa) {
            return redirect('/attendance/izin')
                ->with([
                    'error' => 'Data siswa tidak ditemukan'
                ]);
        }

        // =========================
        // CARI KELAS SISWA
        // =========================
        $kelas = DB::table('kelas')
            ->where('nama_kelas', $siswa->kelas)
            ->where('kode_jurusan', $siswa->kode_jurusan)
            ->first();

        if (!$kelas) {
            return redirect('/attendance/izin')
                ->with([
                    'error' => 'Kelas siswa tidak ditemukan'
                ]);
        }

        // =========================
        // CARI WALI KELAS
        // =========================
        $waliKelasId = $kelas->wali_kelas_id;

        if (!$waliKelasId) {
            return redirect('/attendance/izin')
                ->with([
                    'error' => 'Kelas Anda belum memiliki wali kelas. Silakan hubungi admin.'
                ]);
        }

        // =========================
        // SIMPAN PENGAJUAN + DETAIL
        // =========================
        DB::transaction(function () use ($nis, $tanggal_izin, $status, $keterangan, $surat_izin, $surat_sakit, $waliKelasId) {

            // Pengajuan utama
            $pengajuanId = DB::table('pengajuan_izin')
                ->insertGetId([
                    'nis' => $nis,
                    'tanggal_izin' => $tanggal_izin,
                    'status' => $status,
                    'keterangan' => $keterangan,
                    'status_approved' => 0,
                    'surat_izin' => $surat_izin,
                    'surat_sakit' => $surat_sakit,
                ]);

            // Approval untuk wali kelas
            DB::table('pengajuan_izin_detail')
                ->insert([
                    'pengajuan_izin_id' => $pengajuanId,

                    // Tidak menggunakan jadwal pelajaran
                    'jadwal_pelajaran_id' => null,

                    // ID guru yang menjadi wali kelas
                    'guru_id' => $waliKelasId,

                    // Tidak menggunakan mata pelajaran
                    'mata_pelajaran_id' => null,

                    // 0 = Menunggu
                    'status_approved' => 0,

                    'catatan' => null,
                    'approved_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
        });

        return redirect('/attendance/izin')
            ->with([
                'success' =>
                    'Pengajuan izin/sakit berhasil disimpan dan dikirim kepada wali kelas.'
            ]);
    }

    public function editizin($id)
    {
        $izin = DB::table('pengajuan_izin')
            ->where('id', $id)
            ->first();

        if (!$izin) {
            return redirect()->back()->with('error', 'Data pengajuan tidak ditemukan.');
        }

        if ($izin->status_approved != 0) {
            return redirect('/attendance/izin')
                ->with('error', 'Pengajuan sudah diproses.');
        }

        return view('attendance.editizin', compact('izin'));
    }

    public function updateizin(Request $request, $id)
    {
        $tanggal_izin = date("Y-m-d", strtotime($request->tanggal_izin));
        $status = $request->status;
        $keterangan = $request->keterangan;

        $izin = DB::table('pengajuan_izin')->where('id', $id)->first();

        if (!$izin) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $surat_izin = $izin->surat_izin;
        $surat_sakit = $izin->surat_sakit;

        // Upload surat izin
        if ($request->hasFile('surat_izin')) {

            if ($surat_izin != null) {
                Storage::delete('public/uploads/surat_izin/' . $surat_izin);
            }

            $file = $request->file('surat_izin');
            $surat_izin = time() . "_" . $file->getClientOriginalName();
            $file->storeAs('public/uploads/surat_izin', $surat_izin);
        }

        // Upload surat sakit
        if ($request->hasFile('surat_sakit')) {

            if ($surat_sakit != null) {
                Storage::delete('public/uploads/surat_sakit/' . $surat_sakit);
            }

            $file = $request->file('surat_sakit');
            $surat_sakit = time() . "_" . $file->getClientOriginalName();
            $file->storeAs('public/uploads/surat_sakit', $surat_sakit);
        }

        $data = [
            'tanggal_izin' => $tanggal_izin,
            'status' => $status,
            'keterangan' => $keterangan,
            'surat_izin' => $surat_izin,
            'surat_sakit' => $surat_sakit
        ];

        $update = DB::table('pengajuan_izin')
            ->where('id', $id)
            ->update($data);

        if ($update) {
            return redirect('/attendance/izin')
                ->with('success', 'Pengajuan berhasil diperbarui.');
        } else {
            return redirect('/attendance/izin')
                ->with('error', 'Pengajuan gagal diperbarui.');
        }
    }

    public function deleteizin($id)
    {
        $izin = DB::table('pengajuan_izin')
            ->where('id', $id)
            ->first();

        if (!$izin) {
            return redirect('/attendance/izin')
                ->with('error', 'Data tidak ditemukan.');
        }

        if ($izin->status_approved != 0) {
            return redirect('/attendance/izin')
                ->with('error', 'Pengajuan sudah diproses dan tidak dapat dihapus.');
        }

        // Hapus file surat izin
        if ($izin->surat_izin != null) {
            Storage::delete('public/uploads/surat_izin/' . $izin->surat_izin);
        }

        // Hapus file surat sakit
        if ($izin->surat_sakit != null) {
            Storage::delete('public/uploads/surat_sakit/' . $izin->surat_sakit);
        }

        DB::table('pengajuan_izin')
            ->where('id', $id)
            ->delete();

        return redirect('/attendance/izin')
            ->with('success', 'Pengajuan berhasil dihapus.');
    }

    public function monitoringKelas()
    {
        /*
        |--------------------------------------------------------------------------
        | GURU YANG SEDANG LOGIN
        |--------------------------------------------------------------------------
        */
        $guruId = Auth::guard('user')->id();

        if (!$guruId) {
            abort(403, 'Akun guru tidak ditemukan.');
        }


        /*
        |--------------------------------------------------------------------------
        | AMBIL SEMUA PENUGASAN GURU
        |--------------------------------------------------------------------------
        |
        | Sistem hanya mengambil kelas dan mata pelajaran
        | yang memang dimiliki oleh guru yang sedang login.
        |
        | Tidak lagi menggunakan parameter tingkat X, XI, XII
        | dari URL.
        |
        */
        $penugasan = DB::table('guru_mata_pelajaran as gmp')
            ->join(
                'kelas as k',
                'gmp.kelas_id',
                '=',
                'k.id'
            )
            ->join(
                'mata_pelajaran as mp',
                'gmp.mata_pelajaran_id',
                '=',
                'mp.id'
            )
            ->where(
                'gmp.guru_id',
                $guruId
            )
            ->select(
                'gmp.id as penugasan_id',
                'gmp.kelas_id',
                'gmp.mata_pelajaran_id',
                'k.nama_kelas',
                'k.kode_jurusan',
                'k.tingkat',
                'mp.nama_mapel'
            )
            ->orderBy('k.tingkat')
            ->orderBy('k.nama_kelas')
            ->orderBy('mp.nama_mapel')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | DAFTAR KELAS GURU
        |--------------------------------------------------------------------------
        |
        | Satu kelas hanya ditampilkan satu kali.
        |
        */
        $kelasList = $penugasan
            ->unique('kelas_id')
            ->values();


        /*
        |--------------------------------------------------------------------------
        | DATA PENUGASAN BERDASARKAN KELAS
        |--------------------------------------------------------------------------
        |
        | Digunakan oleh Blade untuk menampilkan
        | mata pelajaran sesuai kelas yang dipilih.
        |
        */
        $penugasanByKelas = $penugasan
            ->groupBy('kelas_id')
            ->map(function ($items) {

                return $items
                    ->values()
                    ->map(function ($item) {

                        return [
                            'penugasan_id' => $item->penugasan_id,
                            'kelas_id' => $item->kelas_id,
                            'mata_pelajaran_id' => $item->mata_pelajaran_id,
                            'nama_mapel' => $item->nama_mapel,
                        ];

                    });

            });


        /*
        |--------------------------------------------------------------------------
        | TINGKAT
        |--------------------------------------------------------------------------
        |
        | Tidak lagi dipakai sebagai parameter URL.
        | Variabel tetap dikirim ke Blade agar Blade lama
        | yang masih menggunakan $tingkat / $tingkatNama
        | tidak langsung error.
        |
        */
        $tingkat = null;
        $tingkatNama = null;


        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */
        return view(
            'attendance.monitoring_kelas',
            compact(
                'tingkat',
                'tingkatNama',
                'kelasList',
                'penugasanByKelas'
            )
        );
    }

    public function getattendancekelas(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDASI
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'tanggal' => 'required',
            'kelas_id' => 'required|integer',
            'penugasan_id' => 'required|integer',
        ]);


        /*
        |--------------------------------------------------------------------------
        | GURU LOGIN
        |--------------------------------------------------------------------------
        */

        $guruId = Auth::guard('user')->id();

        if (!$guruId) {

            return response(
                '<tr>
                <td colspan="10"
                    class="text-center text-danger py-5">
                    Akun guru tidak ditemukan.
                </td>
            </tr>',
                403
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FORMAT TANGGAL
        |--------------------------------------------------------------------------
        */

        try {

            $tanggal = Carbon::createFromFormat(
                'd-m-Y',
                $request->tanggal
            )->format('Y-m-d');

        } catch (\Exception $e) {

            return response(
                '<tr>
                <td colspan="10"
                    class="text-center text-danger py-4">
                    Format tanggal tidak valid.
                </td>
            </tr>',
                422
            );
        }


        $kelasId = (int) $request->kelas_id;

        $penugasanId = (int) $request->penugasan_id;


        /*
        |--------------------------------------------------------------------------
        | PASTIKAN PENUGASAN MILIK GURU LOGIN
        |--------------------------------------------------------------------------
        |
        | Ini penting supaya guru tidak bisa melihat
        | attendance guru lain hanya dengan mengubah request.
        |
        */

        $penugasan = DB::table('guru_mata_pelajaran as gmp')

            ->join(
                'kelas as k',
                'gmp.kelas_id',
                '=',
                'k.id'
            )

            ->join(
                'mata_pelajaran as mp',
                'gmp.mata_pelajaran_id',
                '=',
                'mp.id'
            )

            ->where(
                'gmp.id',
                $penugasanId
            )

            ->where(
                'gmp.guru_id',
                $guruId
            )

            ->where(
                'gmp.kelas_id',
                $kelasId
            )

            ->select(
                'gmp.id as penugasan_id',
                'gmp.kelas_id',
                'gmp.mata_pelajaran_id',
                'k.nama_kelas',
                'k.kode_jurusan',
                'k.tingkat',
                'mp.nama_mapel'
            )

            ->first();


        if (!$penugasan) {

            return response(
                '<tr>
                <td colspan="10"
                    class="text-center text-danger py-5">

                    Anda tidak memiliki akses untuk melihat
                    mata pelajaran atau kelas tersebut.

                </td>
            </tr>',
                403
            );
        }


        /*
        |--------------------------------------------------------------------------
        | HARI
        |--------------------------------------------------------------------------
        */

        $hari = Carbon::parse($tanggal)
            ->dayOfWeekIso;


        /*
        |--------------------------------------------------------------------------
        | AMBIL JADWAL UNTUK PENUGASAN TERSEBUT
        |--------------------------------------------------------------------------
        */

        $jadwal = DB::table('jadwal_pelajaran')

            ->where(
                'penugasan_id',
                $penugasanId
            )

            ->where(
                'hari',
                $hari
            )

            ->where(
                'status',
                1
            )

            ->orderBy('jam_mulai')

            ->first();


        /*
        |--------------------------------------------------------------------------
        | AMBIL SEMUA SISWA DALAM KELAS
        |--------------------------------------------------------------------------
        |
        | Bukan berdasarkan jurusan lagi.
        |
        | Kelas ditentukan dari:
        | kelas.id
        |
        */

        $presensi = DB::table('siswa as s')

            ->join(
                'kelas as k',
                function ($join) {

                    $join->on(
                        's.kelas',
                        '=',
                        'k.nama_kelas'
                    );

                    $join->on(
                        's.kode_jurusan',
                        '=',
                        'k.kode_jurusan'
                    );

                }
            )

            ->leftJoin(
                'jurusan as j',
                's.kode_jurusan',
                '=',
                'j.kode_jurusan'
            )

            ->leftJoin(
                'attendance as a',
                function ($join) use ($tanggal, $penugasanId) {

                    $join->on(
                        's.nis',
                        '=',
                        'a.nis'
                    );

                    $join->where(
                        'a.tgl_presensi',
                        '=',
                        $tanggal
                    );

                    $join->where(
                        'a.penugasan_id',
                        '=',
                        $penugasanId
                    );

                }
            )

            ->where(
                'k.id',
                $kelasId
            )

            ->select(
                'a.id',
                'a.nis as attendance_nis',
                'a.penugasan_id',
                'a.tgl_presensi',
                'a.jam_in',
                'a.jam_out',
                'a.foto_in',
                'a.foto_out',
                'a.location_in',
                'a.location_out',

                's.nis',
                's.nama_lengkap',
                's.kelas',
                's.kode_jurusan',

                'j.nama_jurusan'
            )

            ->orderBy(
                's.nama_lengkap'
            )

            ->get();


        /*
        |--------------------------------------------------------------------------
        | HITUNG STATUS KEHADIRAN
        |--------------------------------------------------------------------------
        */

        $presensi->transform(
            function ($item) use ($jadwal) {

                /*
                |--------------------------------------------------------------------------
                | BELUM ABSEN
                |--------------------------------------------------------------------------
                */

                if (empty($item->jam_in)) {

                    $item->tepat_waktu = false;

                    $item->terlambat = 0;

                    $item->status = 'Belum Absen';

                    $item->status_class = 'status-danger';

                    return $item;
                }


                /*
                |--------------------------------------------------------------------------
                | JADWAL TIDAK DITEMUKAN
                |--------------------------------------------------------------------------
                */

                if (
                    !$jadwal ||
                    empty($jadwal->batas_telat)
                ) {

                    $item->tepat_waktu = true;

                    $item->terlambat = 0;

                    $item->status = 'Tepat Waktu';

                    $item->status_class = 'status-success';

                    return $item;
                }


                /*
                |--------------------------------------------------------------------------
                | HITUNG TERLAMBAT
                |--------------------------------------------------------------------------
                */

                $jamMasuk = Carbon::parse(
                    $item->jam_in
                );

                $batasTelat = Carbon::parse(
                    $jadwal->batas_telat
                );


                if (
                    $jamMasuk->lessThanOrEqualTo(
                        $batasTelat
                    )
                ) {

                    $item->tepat_waktu = true;

                    $item->terlambat = 0;

                    $item->status = 'Tepat Waktu';

                    $item->status_class =
                        'status-success';

                } else {

                    $item->tepat_waktu = false;

                    $item->terlambat =
                        $batasTelat->diffInMinutes(
                            $jamMasuk
                        );

                    $item->status =
                        'Terlambat '
                        . $item->terlambat
                        . ' Menit';

                    $item->status_class =
                        'status-warning';
                }


                return $item;
            }
        );


        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW AJAX
        |--------------------------------------------------------------------------
        */

        return view(
            'attendance.getattendance',
            compact(
                'presensi'
            )
        );
    }

    public function tampilkanpeta(Request $request)
    {
        $id = $request->id;
        $attendance = DB::table('attendance')->where('id', $id)
            ->join('siswa', 'attendance.nis', '=', 'siswa.nis')
            ->first();
        return view('attendance.showmap', compact('attendance'));

    }

    public function halamanlaporan()
    {
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

        $siswa = DB::table('siswa')
            ->orderBy('nama_lengkap')
            ->get();

        $mapel = MataPelajaran::orderBy('nama_mapel')->get();

        return view('attendance.laporan', compact(
            'namabulan',
            'siswa',
            'mapel'
        ));
    }

    public function cetaklaporan(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $nis = $request->nis;
        $mataPelajaranId = $request->mata_pelajaran_id;

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

        // Data siswa
        $siswa = DB::table('siswa')
            ->where('nis', $nis)
            ->join(
                'jurusan',
                'siswa.kode_jurusan',
                '=',
                'jurusan.kode_jurusan'
            )
            ->first();

        // Data mata pelajaran yang dipilih
        $mataPelajaran = MataPelajaran::findOrFail($mataPelajaranId);

        // Data attendance berdasarkan siswa + bulan + tahun + mata pelajaran
        $attendance = DB::table('attendance')
            ->join(
                'guru_mata_pelajaran',
                'attendance.penugasan_id',
                '=',
                'guru_mata_pelajaran.id'
            )
            ->where(
                'guru_mata_pelajaran.mata_pelajaran_id',
                $mataPelajaranId
            )
            ->whereMonth('attendance.tgl_presensi', $bulan)
            ->whereYear('attendance.tgl_presensi', $tahun)
            ->where('attendance.nis', $nis)
            ->select('attendance.*')
            ->orderBy('attendance.tgl_presensi')
            ->get();

        return view(
            'attendance.cetaklaporan',
            compact(
                'bulan',
                'tahun',
                'namabulan',
                'siswa',
                'attendance',
                'mataPelajaran'
            )
        );
    }

    public function rekap()
    {
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

        $jurusan = DB::table('jurusan')
            ->orderBy('nama_jurusan')
            ->get();

        $kelasList = DB::table('kelas')
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        $mataPelajaran = MataPelajaran::orderBy('nama_mapel')
            ->get();

        return view('attendance.rekap', compact(
            'namabulan',
            'jurusan',
            'kelasList',
            'mataPelajaran'
        ));
    }

    public function cetakrekap(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $jurusan = $request->jurusan;
        $kelas = $request->kelas;
        $mataPelajaranId = $request->mata_pelajaran_id;

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

        // Ambil nama mata pelajaran
        $mataPelajaran = MataPelajaran::findOrFail($mataPelajaranId);

        $rekap = DB::table('attendance')
            ->selectRaw('
                attendance.nis,
                siswa.nama_lengkap,

                MAX(IF(DAY(tgl_presensi) = 1,
                    CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),
                    ""
                )) as tgl_1,

                MAX(IF(DAY(tgl_presensi) = 2,
                    CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),
                    ""
                )) as tgl_2,

                MAX(IF(DAY(tgl_presensi) = 3,
                    CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),
                    ""
                )) as tgl_3,

                MAX(IF(DAY(tgl_presensi) = 4,
                    CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),
                    ""
                )) as tgl_4,

                MAX(IF(DAY(tgl_presensi) = 5,
                    CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),
                    ""
                )) as tgl_5,

                MAX(IF(DAY(tgl_presensi) = 6,
                    CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),
                    ""
                )) as tgl_6,

                MAX(IF(DAY(tgl_presensi) = 7,
                    CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),
                    ""
                )) as tgl_7,

                MAX(IF(DAY(tgl_presensi) = 8,
                    CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),
                    ""
                )) as tgl_8,

                MAX(IF(DAY(tgl_presensi) = 9,
                    CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),
                    ""
                )) as tgl_9,

                MAX(IF(DAY(tgl_presensi) = 10,
                    CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),
                    ""
                )) as tgl_10,

                MAX(IF(DAY(tgl_presensi) = 11,
                    CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),
                    ""
                )) as tgl_11,

                MAX(IF(DAY(tgl_presensi) = 12,
                    CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),
                    ""
                )) as tgl_12,

                MAX(IF(DAY(tgl_presensi) = 13,
                    CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),
                    ""
                )) as tgl_13,

                MAX(IF(DAY(tgl_presensi) = 14,
                    CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),
                    ""
                )) as tgl_14,

                MAX(IF(DAY(tgl_presensi) = 15,
                    CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),
                    ""
                )) as tgl_15,

                MAX(IF(DAY(tgl_presensi) = 16,
                    CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),
                    ""
                )) as tgl_16,

                MAX(IF(DAY(tgl_presensi) = 17,
                    CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),
                    ""
                )) as tgl_17,

                MAX(IF(DAY(tgl_presensi) = 18,
                    CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),
                    ""
                )) as tgl_18,

                MAX(IF(DAY(tgl_presensi) = 19,
                    CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),
                    ""
                )) as tgl_19,

                MAX(IF(DAY(tgl_presensi) = 20,
                    CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),
                    ""
                )) as tgl_20,

                MAX(IF(DAY(tgl_presensi) = 21,
                    CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),
                    ""
                )) as tgl_21,

                MAX(IF(DAY(tgl_presensi) = 22,
                    CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),
                    ""
                )) as tgl_22,

                MAX(IF(DAY(tgl_presensi) = 23,
                    CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),
                    ""
                )) as tgl_23,

                MAX(IF(DAY(tgl_presensi) = 24,
                    CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),
                    ""
                )) as tgl_24,

                MAX(IF(DAY(tgl_presensi) = 25,
                    CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),
                    ""
                )) as tgl_25,

                MAX(IF(DAY(tgl_presensi) = 26,
                    CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),
                    ""
                )) as tgl_26,

                MAX(IF(DAY(tgl_presensi) = 27,
                    CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),
                    ""
                )) as tgl_27,

                MAX(IF(DAY(tgl_presensi) = 28,
                    CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),
                    ""
                )) as tgl_28,

                MAX(IF(DAY(tgl_presensi) = 29,
                    CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),
                    ""
                )) as tgl_29,

                MAX(IF(DAY(tgl_presensi) = 30,
                    CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),
                    ""
                )) as tgl_30,

                MAX(IF(DAY(tgl_presensi) = 31,
                    CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),
                    ""
                )) as tgl_31
            ')
            ->join(
                'siswa',
                'attendance.nis',
                '=',
                'siswa.nis'
            )
            ->join(
                'guru_mata_pelajaran',
                'attendance.penugasan_id',
                '=',
                'guru_mata_pelajaran.id'
            )
            ->where('siswa.kode_jurusan', $jurusan)
            ->where('siswa.kelas', $kelas)
            ->where(
                'guru_mata_pelajaran.mata_pelajaran_id',
                $mataPelajaranId
            )
            ->whereMonth('attendance.tgl_presensi', $bulan)
            ->whereYear('attendance.tgl_presensi', $tahun)
            ->groupBy(
                'attendance.nis',
                'siswa.nama_lengkap'
            )
            ->orderBy('siswa.nama_lengkap')
            ->get();

        return view(
            'attendance.cetakrekap',
            compact(
                'bulan',
                'tahun',
                'namabulan',
                'rekap',
                'jurusan',
                'kelas',
                'mataPelajaran'
            )
        );
    }

    public function izinsakit(Request $request)
    {
        $query = Pengajuanizin::query();
        $query->select(
            'id',
            'pengajuan_izin.nis',
            'tanggal_izin',
            'status',
            'keterangan',
            'status_approved',
            'nama_lengkap',
            'kelas',
            'kode_jurusan',
            'surat_sakit',
            'surat_izin',
            'jurusan'
        );
        $query->join('siswa', 'pengajuan_izin.nis', '=', 'siswa.nis');
        if (!empty($request->dari) && !empty($request->sampai)) {
            $dari = date('Y-m-d', strtotime($request->dari));
            $sampai = date('Y-m-d', strtotime($request->sampai));
            $query->whereBetween('tanggal_izin', [$dari, $sampai]);
        }
        if (!empty($request->nis)) {
            $query->where('pengajuan_izin.nis', $request->nis);
        }
        if (!empty($request->nama_lengkap)) {
            $query->where('nama_lengkap', 'like', '%' . $request->nama_lengkap . '%');
        }
        if ($request->status_approved == "0" || $request->status_approved == "1" || $request->status_approved == "2") {
            $query->where('status_approved', $request->status_approved);
        }
        $query->orderBy('tanggal_izin', 'desc');
        $izinsakit = $query->paginate(2);
        $izinsakit->appends($request->all());
        return view('attendance.izinsakit', compact('izinsakit'));
    }

    public function jurusanIzin()
    {
        $jurusan = DB::table('jurusan')->get();

        return view(
            'attendance.jurusan_izin',
            compact('jurusan')
        );
    }

    public function kelasIzin($kode_jurusan)
    {
        $kelas = DB::table('siswa')
            ->where('kode_jurusan', $kode_jurusan)
            ->select('kelas')
            ->groupBy('kelas')
            ->get();

        return view(
            'attendance.kelas_izin',
            compact('kelas', 'kode_jurusan')
        );
    }

    public function listIzinSakit(
        Request $request,
        $kode_jurusan,
        $kelas
    ) {
        $query = DB::table('pengajuan_izin')
            ->join('siswa', 'pengajuan_izin.nis', '=', 'siswa.nis')
            ->where('siswa.kode_jurusan', $kode_jurusan)
            ->where('siswa.kelas', $kelas);

        if (!empty($request->dari)) {
            $query->whereDate(
                'tanggal_izin',
                '>=',
                date('Y-m-d', strtotime($request->dari))
            );
        }

        if (!empty($request->sampai)) {
            $query->whereDate(
                'tanggal_izin',
                '<=',
                date('Y-m-d', strtotime($request->sampai))
            );
        }

        if (
            $request->status_approved === "0" ||
            $request->status_approved === "1" ||
            $request->status_approved === "2"
        ) {
            $query->where(
                'status_approved',
                $request->status_approved
            );
        }

        $izinsakit = $query
            ->select(
                'pengajuan_izin.*',
                'siswa.nama_lengkap',
                'siswa.kelas',
                'siswa.kode_jurusan'
            )
            ->orderBy('tanggal_izin', 'desc')
            ->paginate(10);

        return view(
            'attendance.izinsakit',
            compact(
                'izinsakit',
                'kode_jurusan',
                'kelas'
            )
        );
    }

    public function listIzinSakitKelas(Request $request, $kelas)
    {
        $guruId = Auth::guard('user')->id();

        // Ambil data jurusan untuk dropdown filter
        $jurusan = DB::table('jurusan')->get();

        // Mapping angka kelas ke tingkat
        $tingkat = [
            '10' => 'X',
            '11' => 'XI',
            '12' => 'XII',
        ];

        $tingkatKelas = $tingkat[$kelas] ?? $kelas;

        /*
        |--------------------------------------------------------------------------
        | Ambil kelas yang wali kelasnya adalah guru yang sedang login
        |--------------------------------------------------------------------------
        */
        $kelasWali = DB::table('kelas')
            ->where('wali_kelas_id', $guruId)
            ->where('tingkat', $tingkatKelas)
            ->pluck('nama_kelas');

        /*
        |--------------------------------------------------------------------------
        | Query pengajuan izin/sakit
        |--------------------------------------------------------------------------
        */
        $query = DB::table('pengajuan_izin')
            ->join(
                'siswa',
                'pengajuan_izin.nis',
                '=',
                'siswa.nis'
            )
            ->join(
                'jurusan',
                'siswa.kode_jurusan',
                '=',
                'jurusan.kode_jurusan'
            )
            ->join(
                'pengajuan_izin_detail',
                'pengajuan_izin_detail.pengajuan_izin_id',
                '=',
                'pengajuan_izin.id'
            )
            ->select(
                'pengajuan_izin.*',
                'siswa.nama_lengkap',
                'siswa.kelas',
                'siswa.kode_jurusan',
                'jurusan.nama_jurusan',
                'pengajuan_izin_detail.guru_id',
                'pengajuan_izin_detail.status_approved as detail_status_approved'
            )
            // Hanya pengajuan yang ditujukan kepada guru login
            ->where('pengajuan_izin_detail.guru_id', $guruId)
            // Hanya kelas yang merupakan wali kelas guru login
            ->whereIn('siswa.kelas', $kelasWali);

        /*
        |--------------------------------------------------------------------------
        | Filter tanggal
        |--------------------------------------------------------------------------
        */
        if ($request->filled('dari')) {
            $dari = date('Y-m-d', strtotime($request->dari));

            $query->whereDate(
                'pengajuan_izin.tanggal_izin',
                '>=',
                $dari
            );
        }

        if ($request->filled('sampai')) {
            $sampai = date('Y-m-d', strtotime($request->sampai));

            $query->whereDate(
                'pengajuan_izin.tanggal_izin',
                '<=',
                $sampai
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Nama
        |--------------------------------------------------------------------------
        */
        if ($request->filled('nama_lengkap')) {
            $query->where(
                'siswa.nama_lengkap',
                'like',
                '%' . $request->nama_lengkap . '%'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Jurusan
        |--------------------------------------------------------------------------
        */
        if ($request->filled('kode_jurusan')) {
            $query->where(
                'siswa.kode_jurusan',
                $request->kode_jurusan
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Status Approval
        |--------------------------------------------------------------------------
        */
        if ($request->filled('status_approved')) {
            $query->where(
                'pengajuan_izin_detail.status_approved',
                $request->status_approved
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Ambil data
        |--------------------------------------------------------------------------
        */
        $izinsakit = $query
            ->orderBy(
                'pengajuan_izin.tanggal_izin',
                'desc'
            )
            ->paginate(10)
            ->appends($request->all());

        return view(
            'attendance.list_izinsakit_kelas',
            compact(
                'izinsakit',
                'kelas',
                'jurusan'
            )
        );
    }
    public function approveizinsakit(Request $request)
    {
        $user = Auth::guard('user')->user();

        // Hanya guru yang boleh melakukan approval
        if (!$user || $user->role !== 'guru') {
            abort(403, 'Anda tidak memiliki hak untuk menyetujui pengajuan izin/sakit.');
        }

        $request->validate([
            'id_izinsakit_form' => 'required|integer',
            'status_approved' => 'required|in:1,2',
        ]);

        $guruId = $user->id;
        $pengajuanIzinId = $request->id_izinsakit_form;
        $statusApproved = $request->status_approved;

        // Pastikan pengajuan memang ditujukan kepada guru yang sedang login
        $detail = DB::table('pengajuan_izin_detail')
            ->where('pengajuan_izin_id', $pengajuanIzinId)
            ->where('guru_id', $guruId)
            ->first();

        if (!$detail) {
            return redirect()->back()->with([
                'warning' => 'Pengajuan ini bukan bagian dari tugas Anda.'
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Update approval wali kelas
        |--------------------------------------------------------------------------
        */
        $updateDetail = DB::table('pengajuan_izin_detail')
            ->where('pengajuan_izin_id', $pengajuanIzinId)
            ->where('guru_id', $guruId)
            ->update([
                'status_approved' => $statusApproved,
                'approved_at' => now(),
                'updated_at' => now(),
            ]);

        if (!$updateDetail) {
            return redirect()->back()->with([
                'warning' => 'Status pengajuan gagal diupdate.'
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Sinkronkan status pengajuan utama
        |--------------------------------------------------------------------------
        */
        DB::table('pengajuan_izin')
            ->where('id', $pengajuanIzinId)
            ->update([
                'status_approved' => $statusApproved,
            ]);

        /*
        |--------------------------------------------------------------------------
        | Pesan berdasarkan status
        |--------------------------------------------------------------------------
        */
        if ($statusApproved == 1) {

            return redirect()->back()->with([
                'success' => 'Pengajuan izin/sakit berhasil disetujui.'
            ]);

        } else {

            return redirect()->back()->with([
                'success' => 'Pengajuan izin/sakit berhasil ditolak.'
            ]);
        }
    }


    public function batalkanizinsakit($id)
    {
        $user = Auth::guard('user')->user();

        // Hanya guru yang boleh membatalkan approval
        if (!$user || $user->role !== 'guru') {
            abort(403, 'Anda tidak memiliki hak untuk membatalkan approval.');
        }

        $guruId = $user->id;

        /*
        |--------------------------------------------------------------------------
        | Pastikan pengajuan memang milik wali kelas yang login
        |--------------------------------------------------------------------------
        */
        $detail = DB::table('pengajuan_izin_detail')
            ->where('pengajuan_izin_id', $id)
            ->where('guru_id', $guruId)
            ->first();

        if (!$detail) {
            return redirect()->back()->with([
                'warning' => 'Anda tidak memiliki akses untuk membatalkan pengajuan ini.'
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Batalkan approval wali kelas
        |--------------------------------------------------------------------------
        */
        DB::table('pengajuan_izin_detail')
            ->where('pengajuan_izin_id', $id)
            ->where('guru_id', $guruId)
            ->update([
                'status_approved' => 0,
                'approved_at' => null,
                'updated_at' => now(),
            ]);

        /*
        |--------------------------------------------------------------------------
        | Sinkronkan status pengajuan utama
        |--------------------------------------------------------------------------
        */
        DB::table('pengajuan_izin')
            ->where('id', $id)
            ->update([
                'status_approved' => 0,
            ]);

        return redirect()->back()->with([
            'success' => 'Persetujuan izin/sakit berhasil dibatalkan.'
        ]);
    }

    public function cekpengajuanizin(Request $request)
    {
        $tanggal_izin = date('Y-m-d', strtotime($request->tanggal_izin));
        $nis = Auth::guard('siswa')->user()->nis;
        $cek = DB::table('pengajuan_izin')->where('nis', $nis)->where('tanggal_izin', $tanggal_izin)->count();
        return $cek;
    }
}

// ini kode attendacecontroller