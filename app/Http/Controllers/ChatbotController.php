<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class ChatbotController extends Controller
{
    public function chat(Request $request)
    {
        try {
            $message = strtolower($request->message);
            $originalMessage = $request->message;

            // Kirim $originalMessage ke getDbContext
            $dbContext = $this->getDbContext($message, $originalMessage);

            $systemPrompt = $this->buildSystemPrompt($dbContext);
            \Log::info('Groq Key: ' . env('GROQ_API_KEY'));

            $response = Http::withHeaders([

                'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                        'model' => 'llama-3.3-70b-versatile',
                        'messages' => [
                            [
                                'role' => 'system',
                                'content' => $systemPrompt
                            ],
                            [
                                'role' => 'user',
                                'content' => $originalMessage
                            ]
                        ],
                        'max_tokens' => 1024,
                    ]);

            $data = $response->json();
            \Log::info('Groq Response', $data);
            if ($response->successful() && isset($data['choices'][0]['message']['content'])) {
                return response()->json([
                    'reply' => $data['choices'][0]['message']['content']
                ]);
            }

            return response()->json([
                'status' => $response->status(),
                'body' => $response->body(),
                'api_key' => env('GROQ_API_KEY')
            ]);

        } catch (\Exception $e) {
            return response()->json(['reply' => 'Koneksi gagal: ' . $e->getMessage()], 500);
        }
    }

    // Tambah parameter $originalMessage di sini
    private function getDbContext($message, $originalMessage = '')
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
                ->select(
                    'siswa.nama_lengkap',
                    'siswa.kelas',
                    'jurusan.nama_jurusan',
                    'attendance.jam_in',
                    'attendance.jam_out'
                )
                ->orderBy('attendance.jam_in')
                ->get();

            $totalSiswa = DB::table('siswa')->count();
            $totalHadir = $rekapHariIni->count();
            $totalAlpa = $totalSiswa - $totalHadir;

            $context['rekap_hari_ini'] = [
                'tanggal' => $todayFormatted,
                'total_siswa' => $totalSiswa,
                'total_hadir' => $totalHadir,
                'total_alpa' => $totalAlpa,
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
                ->select(
                    'siswa.nama_lengkap',
                    'siswa.kelas',
                    'jurusan.nama_jurusan',
                    'attendance.jam_in'
                )
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
            $hadirNis = DB::table('attendance')
                ->where('tgl_presensi', $today)
                ->pluck('nis')
                ->toArray();

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
                    ->select(
                        'siswa.nama_lengkap',
                        'siswa.kelas',
                        'jurusan.nama_jurusan',
                        'attendance.jam_in',
                        'attendance.jam_out'
                    )
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

        // ============ DATA IZIN / SAKIT ============
        if ($this->contains($message, ['izin', 'sakit', 'surat', 'pengajuan'])) {
            $izinHariIni = DB::table('pengajuan_izin')
                ->join('siswa', 'pengajuan_izin.nis', '=', 'siswa.nis')
                ->join('jurusan', 'siswa.kode_jurusan', '=', 'jurusan.kode_jurusan')
                ->where('pengajuan_izin.tanggal_izin', $today)
                ->select(
                    'siswa.nama_lengkap',
                    'siswa.kelas',
                    'jurusan.nama_jurusan',
                    'pengajuan_izin.status',
                    'pengajuan_izin.keterangan',
                    'pengajuan_izin.status_approved'
                )
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
                'daftar' => $izinHariIni->toArray()
            ];
        }

        // ============ REKAP BULANAN ============
        if ($this->contains($message, ['bulan', 'bulanan', 'rekap bulan', 'bulan ini'])) {
            $bulan = date('m');
            $tahun = date('Y');
            $namaBulan = [
                '',
                'Januari',
                'Februari',
                'Maret',
                'April',
                'Mei',
                'Juni',
                'Juli',
                'Agustus',
                'September',
                'Oktober',
                'November',
                'Desember'
            ];

            $rekapBulan = DB::table('attendance')
                ->join('siswa', 'attendance.nis', '=', 'siswa.nis')
                ->whereRaw('MONTH(tgl_presensi) = ?', [$bulan])
                ->whereRaw('YEAR(tgl_presensi) = ?', [$tahun])
                ->select(
                    'siswa.nama_lengkap',
                    'siswa.kelas',
                    DB::raw('COUNT(*) as total_hadir')
                )
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

            $siswaPerKelas = DB::table('siswa')
                ->select('kelas', DB::raw('COUNT(*) as jumlah'))
                ->groupBy('kelas')
                ->orderBy('kelas')
                ->get();

            $siswaPerJurusan = DB::table('siswa')
                ->join('jurusan', 'siswa.kode_jurusan', '=', 'jurusan.kode_jurusan')
                ->select('jurusan.nama_jurusan', DB::raw('COUNT(*) as jumlah'))
                ->groupBy('jurusan.nama_jurusan')
                ->get();

            $context['statistik'] = [
                'total_siswa' => $totalSiswa,
                'per_kelas' => $siswaPerKelas->toArray(),
                'per_jurusan' => $siswaPerJurusan->toArray()
            ];
        }

        // ============ CARI SISWA TERTENTU ============
        if (
            $this->contains($message, ['siswa', 'murid', 'absensi']) &&
            !$this->contains($message, ['total', 'jumlah', 'rekap', 'semua'])
        ) {
            $keywords = ['absensi', 'data', 'siswa', 'murid', 'cari', 'info', 'rekap'];
            $namaCari = $originalMessage; // sekarang sudah tersedia
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
                    ->select(
                        'siswa.nis',
                        'siswa.nama_lengkap',
                        'siswa.kelas',
                        'jurusan.nama_jurusan',
                        'siswa.no_hp'
                    )
                    ->first();

                if ($cariSiswa) {
                    $absensiTerakhir = DB::table('attendance')
                        ->where('nis', $cariSiswa->nis)
                        ->orderByDesc('tgl_presensi')
                        ->take(5)
                        ->get();

                    $context['data_siswa'] = [
                        'info' => $cariSiswa,
                        'absensi_terakhir' => $absensiTerakhir->toArray()
                    ];
                }
            }
        }

        // ============ INFO JURUSAN ============
        if ($this->contains($message, ['jurusan', 'program keahlian', 'program studi'])) {
            $jurusan = DB::table('jurusan')
                ->select(
                    'kode_jurusan',
                    'nama_jurusan',
                    DB::raw('(SELECT COUNT(*) FROM siswa WHERE siswa.kode_jurusan = jurusan.kode_jurusan) as total_siswa')
                )
                ->get();

            $context['jurusan'] = $jurusan->toArray();
        }

        return $context;
    }

    private function buildSystemPrompt($dbContext)
    {
        $today = date('d-m-Y');
        $time = date('H:i');

        $prompt = "Kamu adalah asisten virtual SMK SMART, sebuah sistem absensi digital untuk sekolah menengah kejuruan. ";
        $prompt .= "Hari ini tanggal {$today}, pukul {$time}. ";
        $prompt .= "Jawab dengan ramah, sopan, dan singkat dalam Bahasa Indonesia. ";
        $prompt .= "Gunakan data berikut untuk menjawab pertanyaan pengguna:\n\n";

        if (!empty($dbContext)) {
            $prompt .= "=== DATA DARI DATABASE ===\n";
            $prompt .= json_encode($dbContext, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            $prompt .= "\n\n";
        }

        $prompt .= "=== PANDUAN MENJAWAB ===\n";
        $prompt .= "- Jika ada data dari database di atas, gunakan data tersebut untuk menjawab.\n";
        $prompt .= "- Untuk rekap absensi, tampilkan dalam format yang mudah dibaca.\n";
        $prompt .= "- Untuk daftar siswa terlambat/alpa, sebutkan nama dan kelasnya.\n";
        $prompt .= "- Jam masuk normal sekolah adalah 07:00 WIB.\n";
        $prompt .= "- Jika ditanya hal yang tidak ada datanya, jawab secara umum tentang sistem absensi SMK SMART.\n";
        $prompt .= "- Fitur web: Absensi wajah (face recognition), monitoring per kelas, laporan izin/sakit, rekap bulanan.\n";
        $prompt .= "- Jangan jawab hal di luar konteks sekolah dan absensi.\n";

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