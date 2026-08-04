<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ChatbotController extends Controller
{
    // ==========================================================
    // Guard sesuai routes/web.php project ini
    // ==========================================================
    private const SISWA_GUARD = 'siswa'; // dipakai di middleware auth:siswa
    private const ADMIN_GUARD = 'user';  // dipakai di middleware auth:user

    /* =========================================================
     * ENDPOINT UNTUK SISWA -> POST /chatbot
     * Semua data dikunci ke NIS siswa yang sedang login (privasi)
     * ========================================================= */
    public function chatSiswa(Request $request)
    {
        $siswaUser = Auth::guard(self::SISWA_GUARD)->user();

        if (!$siswaUser || empty($siswaUser->nis)) {
            return response()->json([
                'reply' => 'Sesi kamu tidak ditemukan. Silakan login ulang untuk menggunakan chatbot.'
            ], 401);
        }

        $nis = $siswaUser->nis;

        try {
            $message = strtolower($request->message);
            $originalMessage = $request->message;

            $dbContext = $this->getDbContextSiswa($message, $nis);
            $systemPrompt = $this->buildSystemPromptSiswa($dbContext);

            $data = $this->callGroq($systemPrompt, $originalMessage);

            if ($data['ok']) {
                return response()->json(['reply' => $data['reply']]);
            }

            \Log::error('Groq Error (siswa)', $data);
            return response()->json(['reply' => 'Maaf, terjadi kendala saat memproses pertanyaan kamu. Coba lagi sebentar ya.'], 500);

        } catch (\Exception $e) {
            \Log::error('Chatbot siswa error: ' . $e->getMessage());
            return response()->json(['reply' => 'Koneksi gagal: ' . $e->getMessage()], 500);
        }
    }

    /* =========================================================
     * ENDPOINT UNTUK ADMIN/GURU -> POST /panel/chatbot
     * Bisa akses data semua siswa + panduan fitur admin
     * ========================================================= */
    public function chatAdmin(Request $request)
    {
        $adminUser = Auth::guard(self::ADMIN_GUARD)->user();

        if (!$adminUser) {
            return response()->json([
                'reply' => 'Sesi admin tidak ditemukan. Silakan login ulang.'
            ], 401);
        }

        try {
            $message = strtolower($request->message);
            $originalMessage = $request->message;

            $dbContext = $this->getDbContextAdmin($message, $originalMessage);
            $systemPrompt = $this->buildSystemPromptAdmin($dbContext);

            $data = $this->callGroq($systemPrompt, $originalMessage);

            if ($data['ok']) {
                return response()->json(['reply' => $data['reply']]);
            }

            \Log::error('Groq Error (admin)', $data);
            return response()->json(['reply' => 'Maaf, terjadi kendala saat memproses pertanyaan.'], 500);

        } catch (\Exception $e) {
            \Log::error('Chatbot admin error: ' . $e->getMessage());
            return response()->json(['reply' => 'Koneksi gagal: ' . $e->getMessage()], 500);
        }
    }

    /* =========================================================
     * PANGGIL GROQ API (dipakai bareng oleh siswa & admin)
     * ========================================================= */
    private function callGroq($systemPrompt, $originalMessage)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.groq.api_key'),
            'Content-Type' => 'application/json',
        ])->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => 'llama-3.3-70b-versatile',
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $originalMessage],
                    ],
                    'max_tokens' => 1024,
                ]);

        $data = $response->json();

        if ($response->successful() && isset($data['choices'][0]['message']['content'])) {
            return ['ok' => true, 'reply' => $data['choices'][0]['message']['content']];
        }

        return ['ok' => false, 'status' => $response->status(), 'body' => $response->body()];
    }

    /* =========================================================
     * KONTEKS DATABASE - SISWA (hanya data milik siswa itu sendiri)
     * ========================================================= */
    private function getDbContextSiswa($message, $nis)
    {
        $context = [];
        $today = date('Y-m-d');
        $todayFormatted = date('d-m-Y');

        // Data profil siswa (dipakai di hampir semua jawaban sebagai konteks dasar)
        $profil = DB::table('siswa')
            ->join('jurusan', 'siswa.kode_jurusan', '=', 'jurusan.kode_jurusan')
            ->where('siswa.nis', $nis)
            ->select('siswa.nis', 'siswa.nama_lengkap', 'siswa.kelas', 'jurusan.nama_jurusan')
            ->first();

        $context['profil_saya'] = $profil;

        // ============ ABSENSI SAYA HARI INI ============
        if ($this->contains($message, ['hari ini', 'today', 'sekarang', 'sudah absen', 'absen belum', 'jam berapa'])) {
            $absenHariIni = DB::table('attendance')
                ->where('nis', $nis)
                ->where('tgl_presensi', $today)
                ->first();

            $context['absensi_hari_ini'] = [
                'tanggal' => $todayFormatted,
                'sudah_absen' => (bool) $absenHariIni,
                'jam_in' => $absenHariIni->jam_in ?? null,
                'jam_out' => $absenHariIni->jam_out ?? null,
            ];
        }

        // ============ RIWAYAT ABSENSI SAYA ============
        if ($this->contains($message, ['riwayat', 'history', 'absensi saya', 'minggu ini', 'minggu lalu', 'kemarin'])) {
            $riwayat = DB::table('attendance')
                ->where('nis', $nis)
                ->orderByDesc('tgl_presensi')
                ->take(14)
                ->get(['tgl_presensi', 'jam_in', 'jam_out']);

            $context['riwayat_absensi'] = $riwayat->toArray();
        }

        // ============ KENAPA SAYA TERLAMBAT / ALPA ============
        if ($this->contains($message, ['terlambat', 'telat', 'alpa', 'tidak hadir', 'kenapa saya'])) {
            $jamMasuk = '07:00:00';

            $terlambatSaya = DB::table('attendance')
                ->where('nis', $nis)
                ->where('jam_in', '>', $jamMasuk)
                ->orderByDesc('tgl_presensi')
                ->take(10)
                ->get(['tgl_presensi', 'jam_in']);

            $absenBulanIni = DB::table('attendance')
                ->where('nis', $nis)
                ->whereRaw('MONTH(tgl_presensi) = MONTH(CURDATE())')
                ->whereRaw('YEAR(tgl_presensi) = YEAR(CURDATE())')
                ->count();

            $hariKerjaBulanIni = DB::table('attendance')
                ->whereRaw('MONTH(tgl_presensi) = MONTH(CURDATE())')
                ->whereRaw('YEAR(tgl_presensi) = YEAR(CURDATE())')
                ->distinct('tgl_presensi')
                ->count('tgl_presensi');

            $context['keterlambatan_saya'] = [
                'jam_masuk_normal' => '07:00',
                'riwayat_terlambat' => $terlambatSaya->toArray(),
                'total_hadir_bulan_ini' => $absenBulanIni,
                'total_hari_kerja_bulan_ini' => $hariKerjaBulanIni,
                'total_alpa_bulan_ini' => max($hariKerjaBulanIni - $absenBulanIni, 0),
            ];
        }

        // ============ IZIN / SAKIT SAYA ============
        if ($this->contains($message, ['izin', 'sakit', 'surat', 'pengajuan', 'cara izin', 'ajukan'])) {
            $izinSaya = DB::table('pengajuan_izin')
                ->where('nis', $nis)
                ->orderByDesc('tanggal_izin')
                ->take(10)
                ->get(['tanggal_izin', 'status', 'keterangan', 'status_approved'])
                ->map(function ($item) {
                    $item->status_label = $item->status == 'i' ? 'Izin' : 'Sakit';
                    $item->approved_label = match ((int) $item->status_approved) {
                        1 => 'Disetujui',
                        2 => 'Ditolak',
                        default => 'Menunggu'
                    };
                    return $item;
                });

            $context['izin_sakit_saya'] = $izinSaya->toArray();
        }

        // ============ REKAP BULANAN SAYA ============
        if ($this->contains($message, ['bulan', 'bulanan', 'rekap saya', 'bulan ini'])) {
            $bulan = date('m');
            $tahun = date('Y');
            $namaBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

            $totalHadirBulan = DB::table('attendance')
                ->where('nis', $nis)
                ->whereRaw('MONTH(tgl_presensi) = ?', [$bulan])
                ->whereRaw('YEAR(tgl_presensi) = ?', [$tahun])
                ->count();

            $totalHariKerja = DB::table('attendance')
                ->whereRaw('MONTH(tgl_presensi) = ?', [$bulan])
                ->whereRaw('YEAR(tgl_presensi) = ?', [$tahun])
                ->distinct('tgl_presensi')
                ->count('tgl_presensi');

            $context['rekap_bulanan_saya'] = [
                'bulan' => $namaBulan[(int) $bulan],
                'tahun' => $tahun,
                'total_hadir' => $totalHadirBulan,
                'total_hari_kerja' => $totalHariKerja,
                'total_alpa' => max($totalHariKerja - $totalHadirBulan, 0),
            ];
        }

        return $context;
    }

    private function buildSystemPromptSiswa($dbContext)
    {
        $today = date('d-m-Y');
        $time = date('H:i');
        $namaSiswa = $dbContext['profil_saya']->nama_lengkap ?? 'Siswa';

        $prompt = "Kamu adalah asisten virtual pribadi untuk siswa di SMK SMART, sebuah sistem absensi digital sekolah. ";
        $prompt .= "Kamu sedang berbicara dengan siswa bernama {$namaSiswa}. ";
        $prompt .= "Hari ini tanggal {$today}, pukul {$time}. ";
        $prompt .= "Jawab dengan ramah, sopan, singkat, dan personal (seolah bicara langsung ke siswa ini) dalam Bahasa Indonesia.\n\n";

        $prompt .= "=== DATA PRIBADI SISWA INI ===\n";
        $prompt .= json_encode($dbContext, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $prompt .= "\n\n";

        $prompt .= "=== PANDUAN MENJAWAB ===\n";
        $prompt .= "- Kamu HANYA boleh membahas data absensi milik siswa ini sendiri. Jangan pernah memberi data siswa lain.\n";
        $prompt .= "- Jam masuk normal sekolah adalah 07:00 WIB, di atas jam itu dianggap terlambat.\n";
        $prompt .= "- Jika siswa tanya cara mengajukan izin/sakit, jelaskan bahwa mereka bisa mengajukan lewat menu 'Pengajuan Izin' di aplikasi, lalu menunggu persetujuan guru/wali kelas.\n";
        $prompt .= "- Jika siswa tanya kenapa dianggap alpa padahal merasa hadir, sarankan untuk menghubungi wali kelas/TU karena mungkin ada kendala absen wajah (face recognition).\n";
        $prompt .= "- Jika data yang dibutuhkan tidak tersedia di atas, jawab secara umum dan sarankan cek langsung di menu terkait pada aplikasi.\n";
        $prompt .= "- Jangan menjawab pertanyaan di luar topik sekolah/absensi.\n";

        return $prompt;
    }

    /* =========================================================
     * KONTEKS DATABASE - ADMIN/GURU (data global semua siswa)
     * ========================================================= */
    private function getDbContextAdmin($message, $originalMessage = '')
    {
        $context = [];
        $today = date('Y-m-d');
        $todayFormatted = date('d-m-Y');

        // ============ REKAP ABSENSI HARI INI ============
        if ($this->contains($message, ['hari ini', 'today', 'sekarang', 'absen hari', 'hadir hari'])) {
            $rekapHariIni = DB::table('attendance')
                ->join('siswa', 'attendance.nis', '=', 'siswa.nis')
                ->join('jurusan', 'siswa.kode_jurusan', '=', 'jurusan.kode_jurusan')
                ->where('attendance.tgl_presensi', $today)
                ->select('siswa.nama_lengkap', 'siswa.kelas', 'jurusan.nama_jurusan', 'attendance.jam_in', 'attendance.jam_out')
                ->orderBy('attendance.jam_in')
                ->get();

            $totalSiswa = DB::table('siswa')->count();
            $totalHadir = $rekapHariIni->count();

            $context['rekap_hari_ini'] = [
                'tanggal' => $todayFormatted,
                'total_siswa' => $totalSiswa,
                'total_hadir' => $totalHadir,
                'total_alpa' => $totalSiswa - $totalHadir,
                'daftar_hadir' => $rekapHariIni->take(20)->toArray()
            ];
        }

        // ============ SISWA TERLAMBAT ============
        if ($this->contains($message, ['terlambat', 'telat', 'late', 'keterlambatan'])) {
            $jamMasuk = '07:00:00';

            $terlambat = DB::table('attendance')
                ->join('siswa', 'attendance.nis', '=', 'siswa.nis')
                ->join('jurusan', 'siswa.kode_jurusan', '=', 'jurusan.kode_jurusan')
                ->where('attendance.tgl_presensi', $today)
                ->where('attendance.jam_in', '>', $jamMasuk)
                ->select('siswa.nama_lengkap', 'siswa.kelas', 'jurusan.nama_jurusan', 'attendance.jam_in')
                ->orderBy('attendance.jam_in')
                ->get();

            $context['terlambat'] = [
                'tanggal' => $todayFormatted,
                'jam_masuk_normal' => '07:00',
                'total_terlambat' => $terlambat->count(),
                'daftar' => $terlambat->toArray()
            ];
        }

        // ============ SISWA ALPA / TIDAK HADIR ============
        if ($this->contains($message, ['alpa', 'tidak hadir', 'alpha', 'bolos'])) {
            $hadirNis = DB::table('attendance')->where('tgl_presensi', $today)->pluck('nis')->toArray();

            $alpa = DB::table('siswa')
                ->join('jurusan', 'siswa.kode_jurusan', '=', 'jurusan.kode_jurusan')
                ->whereNotIn('siswa.nis', $hadirNis)
                ->select('siswa.nis', 'siswa.nama_lengkap', 'siswa.kelas', 'jurusan.nama_jurusan')
                ->orderBy('siswa.kelas')
                ->orderBy('siswa.nama_lengkap')
                ->get();

            $context['alpa'] = [
                'tanggal' => $todayFormatted,
                'total_alpa' => $alpa->count(),
                'daftar' => $alpa->take(20)->toArray()
            ];
        }

        // ============ REKAP ABSENSI PER KELAS ============
        if ($this->contains($message, ['kelas 10', 'kelas 11', 'kelas 12', 'rekap kelas'])) {
            $kelas = null;
            if (str_contains($message, 'kelas 10') || str_contains($message, 'kelas10'))
                $kelas = '10';
            if (str_contains($message, 'kelas 11') || str_contains($message, 'kelas11'))
                $kelas = '11';
            if (str_contains($message, 'kelas 12') || str_contains($message, 'kelas12'))
                $kelas = '12';

            if ($kelas) {
                $rekapKelas = DB::table('attendance')
                    ->join('siswa', 'attendance.nis', '=', 'siswa.nis')
                    ->join('jurusan', 'siswa.kode_jurusan', '=', 'jurusan.kode_jurusan')
                    ->where('attendance.tgl_presensi', $today)
                    ->where('siswa.kelas', $kelas)
                    ->select('siswa.nama_lengkap', 'siswa.kelas', 'jurusan.nama_jurusan', 'attendance.jam_in', 'attendance.jam_out')
                    ->get();

                $totalKelas = DB::table('siswa')->where('kelas', $kelas)->count();

                $context['rekap_kelas'] = [
                    'kelas' => $kelas,
                    'tanggal' => $todayFormatted,
                    'total_siswa_kelas' => $totalKelas,
                    'total_hadir' => $rekapKelas->count(),
                    'total_alpa' => $totalKelas - $rekapKelas->count(),
                    'daftar' => $rekapKelas->toArray()
                ];
            }
        }

        // ============ DATA IZIN / SAKIT (SEMUA SISWA) ============
        if ($this->contains($message, ['izin', 'sakit', 'surat', 'pengajuan', 'approve', 'setuju', 'tolak'])) {
            $izinHariIni = DB::table('pengajuan_izin')
                ->join('siswa', 'pengajuan_izin.nis', '=', 'siswa.nis')
                ->join('jurusan', 'siswa.kode_jurusan', '=', 'jurusan.kode_jurusan')
                ->where('pengajuan_izin.tanggal_izin', $today)
                ->select('siswa.nama_lengkap', 'siswa.kelas', 'jurusan.nama_jurusan', 'pengajuan_izin.status', 'pengajuan_izin.keterangan', 'pengajuan_izin.status_approved')
                ->get()
                ->map(function ($item) {
                    $item->status_label = $item->status == 'i' ? 'Izin' : 'Sakit';
                    $item->approved_label = match ((int) $item->status_approved) {
                        1 => 'Disetujui',
                        2 => 'Ditolak',
                        default => 'Menunggu'
                    };
                    return $item;
                });

            $context['izin_sakit'] = [
                'tanggal' => $todayFormatted,
                'total' => $izinHariIni->count(),
                'menunggu_approval' => $izinHariIni->where('status_approved', 0)->count(),
                'daftar' => $izinHariIni->toArray()
            ];
        }

        // ============ REKAP BULANAN ============
        if ($this->contains($message, ['bulan', 'bulanan', 'rekap bulan', 'bulan ini'])) {
            $bulan = date('m');
            $tahun = date('Y');
            $namaBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

            $rekapBulan = DB::table('attendance')
                ->join('siswa', 'attendance.nis', '=', 'siswa.nis')
                ->whereRaw('MONTH(tgl_presensi) = ?', [$bulan])
                ->whereRaw('YEAR(tgl_presensi) = ?', [$tahun])
                ->select('siswa.nama_lengkap', 'siswa.kelas', DB::raw('COUNT(*) as total_hadir'))
                ->groupBy('attendance.nis', 'siswa.nama_lengkap', 'siswa.kelas')
                ->orderBy('siswa.kelas')
                ->orderByDesc('total_hadir')
                ->get();

            $totalHariKerja = DB::table('attendance')
                ->whereRaw('MONTH(tgl_presensi) = ?', [$bulan])
                ->whereRaw('YEAR(tgl_presensi) = ?', [$tahun])
                ->distinct('tgl_presensi')
                ->count('tgl_presensi');

            $context['rekap_bulanan'] = [
                'bulan' => $namaBulan[(int) $bulan],
                'tahun' => $tahun,
                'total_hari_masuk' => $totalHariKerja,
                'total_siswa' => DB::table('siswa')->count(),
                'daftar' => $rekapBulan->take(20)->toArray()
            ];
        }

        // ============ STATISTIK UMUM ============
        if ($this->contains($message, ['statistik', 'total siswa', 'jumlah siswa', 'data siswa', 'berapa siswa'])) {
            $totalSiswa = DB::table('siswa')->count();
            $siswaPerKelas = DB::table('siswa')->select('kelas', DB::raw('COUNT(*) as jumlah'))->groupBy('kelas')->orderBy('kelas')->get();
            $siswaPerJurusan = DB::table('siswa')->join('jurusan', 'siswa.kode_jurusan', '=', 'jurusan.kode_jurusan')->select('jurusan.nama_jurusan', DB::raw('COUNT(*) as jumlah'))->groupBy('jurusan.nama_jurusan')->get();

            $context['statistik'] = [
                'total_siswa' => $totalSiswa,
                'per_kelas' => $siswaPerKelas->toArray(),
                'per_jurusan' => $siswaPerJurusan->toArray()
            ];
        }

        // ============ CARI SISWA TERTENTU ============
        if ($this->contains($message, ['siswa', 'murid', 'absensi']) && !$this->contains($message, ['total', 'jumlah', 'rekap', 'semua'])) {
            $keywords = ['absensi', 'data', 'siswa', 'murid', 'cari', 'info', 'rekap'];
            $namaCari = $originalMessage;
            foreach ($keywords as $kw) {
                $namaCari = str_ireplace($kw, '', $namaCari);
            }
            $namaCari = trim($namaCari);

            if (strlen($namaCari) > 2) {
                $cariSiswa = DB::table('siswa')
                    ->join('jurusan', 'siswa.kode_jurusan', '=', 'jurusan.kode_jurusan')
                    ->where(function ($q) use ($namaCari) {
                        $q->where('siswa.nama_lengkap', 'like', '%' . $namaCari . '%')
                            ->orWhere('siswa.nis', 'like', '%' . $namaCari . '%');
                    })
                    ->select('siswa.nis', 'siswa.nama_lengkap', 'siswa.kelas', 'jurusan.nama_jurusan', 'siswa.no_hp')
                    ->first();

                if ($cariSiswa) {
                    $absensiTerakhir = DB::table('attendance')->where('nis', $cariSiswa->nis)->orderByDesc('tgl_presensi')->take(5)->get();
                    $context['data_siswa'] = ['info' => $cariSiswa, 'absensi_terakhir' => $absensiTerakhir->toArray()];
                }
            }
        }

        // ============ INFO JURUSAN ============
        if ($this->contains($message, ['jurusan', 'program keahlian', 'program studi'])) {
            $context['jurusan'] = DB::table('jurusan')
                ->select('kode_jurusan', 'nama_jurusan', DB::raw('(SELECT COUNT(*) FROM siswa WHERE siswa.kode_jurusan = jurusan.kode_jurusan) as total_siswa'))
                ->get()
                ->toArray();
        }

        return $context;
    }

    private function buildSystemPromptAdmin($dbContext)
    {
        $today = date('d-m-Y');
        $time = date('H:i');

        $prompt = "Kamu adalah asisten virtual untuk ADMIN/GURU di SMK SMART, sistem absensi digital sekolah. ";
        $prompt .= "Kamu berbicara dengan guru/staff admin yang mengelola data absensi seluruh siswa. ";
        $prompt .= "Hari ini tanggal {$today}, pukul {$time}. ";
        $prompt .= "Jawab dengan ramah, profesional, dan singkat dalam Bahasa Indonesia.\n\n";

        if (!empty($dbContext)) {
            $prompt .= "=== DATA DARI DATABASE ===\n";
            $prompt .= json_encode($dbContext, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            $prompt .= "\n\n";
        }

        $prompt .= "=== PANDUAN MENJAWAB DATA ===\n";
        $prompt .= "- Jika ada data dari database di atas, gunakan data tersebut untuk menjawab.\n";
        $prompt .= "- Untuk rekap absensi, tampilkan dalam format yang mudah dibaca (bisa pakai list/tabel teks).\n";
        $prompt .= "- Untuk daftar siswa terlambat/alpa, sebutkan nama dan kelasnya.\n";
        $prompt .= "- Jam masuk normal sekolah adalah 07:00 WIB.\n";
        $prompt .= "- Jika ditanya hal yang tidak ada datanya, jawab secara umum tentang sistem absensi SMK SMART.\n\n";

        $prompt .= "=== PANDUAN FITUR ADMIN (jawab jika guru bertanya cara pakai) ===\n";
        $prompt .= "- Approve/tolak izin siswa: buka menu 'Pengajuan Izin', pilih pengajuan yang berstatus Menunggu, lalu klik Setujui atau Tolak.\n";
        $prompt .= "- Tambah data siswa baru: buka menu 'Data Siswa' > 'Tambah Siswa', isi NIS, nama, kelas, jurusan, dan nomor HP.\n";
        $prompt .= "- Reset akun/absensi siswa yang error: buka menu 'Data Siswa', cari siswa terkait, gunakan opsi edit/reset pada baris siswa tersebut.\n";
        $prompt .= "- Melihat rekap bulanan/laporan: buka menu 'Laporan' atau 'Rekap Bulanan', bisa difilter per kelas/jurusan lalu diekspor.\n";
        $prompt .= "- Mengatur jam masuk/toleransi keterlambatan: cek menu 'Pengaturan' > 'Jam Absensi'.\n";
        $prompt .= "- Fitur absensi wajah (face recognition): siswa absen otomatis lewat kamera di titik absen yang sudah ditentukan; jika gagal, guru bisa input manual lewat menu 'Absensi Manual'.\n";
        $prompt .= "- Jika guru bertanya cara pakai fitur yang tidak tercantum di atas, jawab secara umum dan arahkan untuk mengecek menu terkait di dashboard admin.\n";
        $prompt .= "- Jangan jawab hal di luar konteks sekolah, absensi, dan pengelolaan sistem SMK SMART.\n";

        return $prompt;
    }

    private function contains($message, $keywords)
    {
        foreach ($keywords as $keyword) {
            if (str_contains($message, $keyword)) {
                return true;
            }
        }
        return false;
    }
}