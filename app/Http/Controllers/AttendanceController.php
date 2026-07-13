<?php

namespace App\Http\Controllers;

use App\Models\Pengajuanizin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpKernel\HttpCache\Store;


class AttendanceController extends Controller
{
    public function create()
    {
        $hari_ini = date('Y-m-d');
        $nis = Auth::guard('siswa')->user()->nis;
        $cek = DB::table('attendance')->where('nis', $nis)->where('tgl_presensi', $hari_ini)->count();
        $lok_sekolah = DB::table('konfigurasi_lokasi')->where('id', 1)->first();
        return view('attendance.create', compact('cek', 'lok_sekolah'));
    }

    public function store(Request $request)
    {
        // Tambahkan sementara di method store(), baris pertama

        $nis = Auth::guard('siswa')->user()->nis;
        $tgl_presensi = date('Y-m-d');
        $jam = date('H:i:s');
        $lok_sekolah = DB::table('konfigurasi_lokasi')->where('id', 1)->first();
        $lok = explode(",", $lok_sekolah->lokasi_sekolah);
        $latitudesekolah = $lok[0];
        $longitudesekolah = $lok[1];
        $lokasi = $request->lokasi;
        $lokasiuser = explode(",", $lokasi);
        $latitudeuser = $lokasiuser[0];
        $longitudeuser = $lokasiuser[1];

        $jarak = $this->distance($latitudesekolah, $longitudesekolah, $latitudeuser, $longitudeuser);
        $radius = round($jarak["meters"]);

        $cek = DB::table('attendance')->where('nis', $nis)->where('tgl_presensi', $tgl_presensi)->count();

        if ($cek > 0) {
            $ket = "out";
        } else {
            $ket = "in";
        }
        $image = $request->image;
        $folderPath = "public/uploads/absensi/";
        $format_name = $nis . '-' . $tgl_presensi . '-' . $ket;
        $image_parts = explode(";base64,", $image);
        $image_base64 = base64_decode($image_parts[1]);
        $filename = $format_name . '.png';
        $file = $folderPath . $filename;
        Storage::put($file, $image_base64);

        if ($radius > $lok_sekolah->radius) {
            echo "error|maaf anda berada diluar radius, jarak anda " . $radius . " meter dari sekolah|radius";
        } else {


            if ($cek > 0) {
                $data_pulang = [
                    'jam_out' => $jam,
                    'foto_out' => $filename,
                    'location_out' => $lokasi,
                ];
                $update = DB::table('attendance')->where('tgl_presensi', $tgl_presensi)->where('nis', $nis)->update($data_pulang);
                if ($update) {
                    echo "success|Terimakasih Hati-Hati Dijalan|out";
                    Storage::put($file, $image_base64);
                } else {
                    echo "error|Maaf Gagal Absen, Silakan Hubungi Admin|out";
                }

            } else {
                $data = [
                    'nis' => $nis,
                    'tgl_presensi' => $tgl_presensi,
                    'jam_in' => $jam,
                    'foto_in' => $filename,
                    'location_in' => $lokasi,
                ];
                $simpan = DB::table('attendance')->insert($data);
                if ($simpan) {
                    echo "success|Terimakasih Selamat Datang|in";
                    Storage::put($file, $image_base64);
                } else {
                    echo "error|Maaf Gagal Absen, Silakan Hubungi Admin|out";
                }
            }
        }
    }

    // ✅ Simpan face descriptor dari halaman profil
    public function saveDescriptor(Request $request)
    {
        $nis = Auth::guard('siswa')->user()->nis;
        DB::table('siswa')->where('nis', $nis)->update([
            'face_descriptor' => $request->descriptor // JSON string array 128 angka
        ]);
        return response()->json(['status' => 'success']);
    }

    // ✅ Ambil semua descriptor untuk matching di frontend
    public function getFaceDescriptors()
    {
        $siswas = DB::table('siswa')
            ->whereNotNull('face_descriptor')
            ->where('face_descriptor', '!=', '')
            ->select('nis', 'nama_lengkap', 'foto', 'face_descriptor')
            ->get()
            ->map(function ($s) {
                return [
                    'nis' => $s->nis,
                    'nama' => $s->nama_lengkap,
                    'foto' => $s->foto ? asset('storage/uploads/siswa/' . $s->foto) : null,
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

        $nama_lengkap = $request->nama_lengkap;
        $no_hp = $request->no_hp;

        if ($request->hasFile('foto')) {

            $foto = $nis . '.' .
                $request->file('foto')->getClientOriginalExtension();

        } else {

            $foto = $siswa->foto;
        }

        // =========================
        // DATA UPDATE
        // =========================
        $data = [
            'nama_lengkap' => $nama_lengkap,
            'no_hp' => $no_hp,
            'foto' => $foto
        ];

        // Jika password baru diisi dan password lama benar
        if (!empty($request->password_baru)) {

            $data['password'] = Hash::make($request->password_baru);
            $data['is_default_password'] = 0;
        }

        DB::table('siswa')
            ->where('nis', $nis)
            ->update($data);

        if ($request->hasFile('foto')) {

            $folderPath = 'public/uploads/siswa';

            $request->file('foto')
                ->storeAs($folderPath, $foto);
        }

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
        $nis = Auth::guard('siswa')->user()->nis;

        $histori = DB::table('attendance')
            ->whereRaw('MONTH(tgl_presensi)="' . $bulan . '"')
            ->whereRaw('YEAR(tgl_presensi)="' . $tahun . '"')
            ->where('nis', $nis)
            ->orderBy('tgl_presensi')
            ->get();
        return view('attendance.gethistori', compact('histori'));

    }

    public function izin()
    {
        $nis = Auth::guard('siswa')->user()->nis;
        $dataizin = DB::table('pengajuan_izin')->where('nis', $nis)->get();
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

        // validasi upload surat sakit
        if ($status == "s" && !$request->hasFile('surat_sakit')) {
            return redirect()->back()->with([
                'error' => 'Surat sakit wajib diupload'
            ]);
        }

        $surat_sakit = null;

        if ($request->hasFile('surat_sakit')) {

            $file = $request->file('surat_sakit');

            $surat_sakit = time() . "_" . $file->getClientOriginalName();

            $file->storeAs('public/uploads/surat_sakit', $surat_sakit);
        }

        $data = [
            'nis' => $nis,
            'tanggal_izin' => $tanggal_izin,
            'status' => $status,
            'keterangan' => $keterangan,
            'surat_sakit' => $surat_sakit
        ];

        $simpan = DB::table('pengajuan_izin')->insert($data);

        if ($simpan) {
            return redirect('/attendance/izin')
                ->with(['success' => 'Data Berhasil Disimpan']);
        } else {
            return redirect('/attendance/izin')
                ->with(['error' => 'Data Gagal Disimpan']);
        }
    }
    public function monitoringKelas($kelas)
    {
        $jurusan = DB::table('jurusan')->get();

        return view('attendance.monitoring_kelas', compact(
            'kelas',
            'jurusan'
        ));
    }

    public function getattendancekelas(Request $request)
    {
        $tanggal = date('Y-m-d', strtotime($request->tanggal));
        $kelas = $request->kelas;
        $kode_jurusan = $request->kode_jurusan;

        $presensi = DB::table('attendance')
            ->select(
                'attendance.*',
                'nama_lengkap',
                'nama_jurusan',
                'siswa.kelas'
            )
            ->join('siswa', 'attendance.nis', '=', 'siswa.nis')
            ->join('jurusan', 'siswa.kode_jurusan', '=', 'jurusan.kode_jurusan')
            ->where('attendance.tgl_presensi', $tanggal)
            ->where('siswa.kelas', $kelas);

        if (!empty($kode_jurusan)) {
            $presensi->where('siswa.kode_jurusan', $kode_jurusan);
        }

        $presensi = $presensi->get();

        return view('attendance.getattendance', compact('presensi'));
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
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $siswa = DB::table('siswa')->orderBy('nama_lengkap')->get();
        return view('attendance.laporan', compact('namabulan', 'siswa'));
    }

    public function cetaklaporan(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $nis = $request->nis;
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $siswa = DB::table('siswa')->where('nis', $nis)
            ->join('jurusan', 'siswa.kode_jurusan', '=', 'jurusan.kode_jurusan')
            ->first();

        $attendance = DB::table('attendance')
            ->whereRaw('MONTH(tgl_presensi)="' . $bulan . '"')
            ->whereRaw('YEAR(tgl_presensi)="' . $tahun . '"')
            ->where('nis', $nis)
            ->get();
        return view('attendance.cetaklaporan', compact('bulan', 'tahun', 'namabulan', 'siswa', 'attendance'));
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

        // ambil jurusan dari database (kalau kamu punya tabel jurusan)
        $jurusan = DB::table('jurusan')->get();

        // ambil kelas unik dari siswa
        $kelas = DB::table('siswa')
            ->select('kelas')
            ->groupBy('kelas')
            ->orderBy('kelas')
            ->get();

        return view('attendance.rekap', compact('namabulan', 'jurusan', 'kelas'));
    }

    public function cetakrekap(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $rekap = DB::table('attendance')
            ->selectRaw('attendance.nis, nama_lengkap,
                MAX(IF(DAY(tgl_presensi) = 1, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"")) as tgl_1,
                MAX(IF(DAY(tgl_presensi) = 2, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"")) as tgl_2,
                MAX(IF(DAY(tgl_presensi) = 3, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"")) as tgl_3,
                MAX(IF(DAY(tgl_presensi) = 4, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"")) as tgl_4,
                MAX(IF(DAY(tgl_presensi) = 5, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"")) as tgl_5,
                MAX(IF(DAY(tgl_presensi) = 6, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"")) as tgl_6,
                MAX(IF(DAY(tgl_presensi) = 7, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"")) as tgl_7,
                MAX(IF(DAY(tgl_presensi) = 8, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"")) as tgl_8,
                MAX(IF(DAY(tgl_presensi) = 9, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"")) as tgl_9,
                MAX(IF(DAY(tgl_presensi) = 10, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"")) as tgl_10,
                MAX(IF(DAY(tgl_presensi) = 11, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"")) as tgl_11,
                MAX(IF(DAY(tgl_presensi) = 12, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"")) as tgl_12,
                MAX(IF(DAY(tgl_presensi) = 13, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"")) as tgl_13,
                MAX(IF(DAY(tgl_presensi) = 14, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"")) as tgl_14,
                MAX(IF(DAY(tgl_presensi) = 15, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"")) as tgl_15,
                MAX(IF(DAY(tgl_presensi) = 16, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"")) as tgl_16,
                MAX(IF(DAY(tgl_presensi) = 17, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"")) as tgl_17,
                MAX(IF(DAY(tgl_presensi) = 18, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"")) as tgl_18,
                MAX(IF(DAY(tgl_presensi) = 19, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"")) as tgl_19,
                MAX(IF(DAY(tgl_presensi) = 20, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"")) as tgl_20,
                MAX(IF(DAY(tgl_presensi) = 21, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"")) as tgl_21,
                MAX(IF(DAY(tgl_presensi) = 22, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"")) as tgl_22,
                MAX(IF(DAY(tgl_presensi) = 23, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"")) as tgl_23,
                MAX(IF(DAY(tgl_presensi) = 24, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"")) as tgl_24,
                MAX(IF(DAY(tgl_presensi) = 25, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"")) as tgl_25,
                MAX(IF(DAY(tgl_presensi) = 26, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"")) as tgl_26,
                MAX(IF(DAY(tgl_presensi) = 27, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"")) as tgl_27,
                MAX(IF(DAY(tgl_presensi) = 28, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"")) as tgl_28,
                MAX(IF(DAY(tgl_presensi) = 29, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"")) as tgl_29,
                MAX(IF(DAY(tgl_presensi) = 30, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"")) as tgl_30,
                MAX(IF(DAY(tgl_presensi) = 31, CONCAT(jam_in, "-", IFNULL(jam_out, "00:00:00")),"")) as tgl_31')
            ->join('siswa', 'attendance.nis', '=', 'siswa.nis')
            ->whereRaw('MONTH(tgl_presensi)="' . $bulan . '"')
            ->whereRaw('YEAR(tgl_presensi)="' . $tahun . '"')
            ->groupByRaw('attendance.nis, nama_lengkap')
            ->get();
        return view('attendance.cetakrekap', compact('bulan', 'tahun', 'namabulan', 'rekap'));

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
        // Ambil data jurusan untuk dropdown filter
        $jurusan = DB::table('jurusan')->get();

        $query = DB::table('pengajuan_izin')
            ->join('siswa', 'pengajuan_izin.nis', '=', 'siswa.nis')
            ->join('jurusan', 'siswa.kode_jurusan', '=', 'jurusan.kode_jurusan')
            ->select(
                'pengajuan_izin.*',
                'siswa.nama_lengkap',
                'siswa.kelas',
                'siswa.kode_jurusan',
                'jurusan.nama_jurusan'
            )
            ->where('siswa.kelas', $kelas);

        // Filter tanggal
        if ($request->filled('dari')) {
            $dari = date('Y-m-d', strtotime($request->dari));
            $query->whereDate('pengajuan_izin.tanggal_izin', '>=', $dari);
        }

        if ($request->filled('sampai')) {
            $sampai = date('Y-m-d', strtotime($request->sampai));
            $query->whereDate('pengajuan_izin.tanggal_izin', '<=', $sampai);
        }

        // Filter NIS
        if ($request->filled('nis')) {
            $query->where('pengajuan_izin.nis', 'like', '%' . $request->nis . '%');
        }

        // Filter Nama
        if ($request->filled('nama_lengkap')) {
            $query->where('siswa.nama_lengkap', 'like', '%' . $request->nama_lengkap . '%');
        }

        // Filter Jurusan
        if ($request->filled('kode_jurusan')) {
            $query->where('siswa.kode_jurusan', $request->kode_jurusan);
        }

        // Filter Status Approval
        if ($request->filled('status_approved')) {
            $query->where('pengajuan_izin.status_approved', $request->status_approved);
        }

        $izinsakit = $query
            ->orderBy('pengajuan_izin.tanggal_izin', 'desc')
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
        $status_approved = $request->status_approved;
        $id_izinsakit_form = $request->id_izinsakit_form;
        $update = DB::table('pengajuan_izin')->where('id', $id_izinsakit_form)->update(['status_approved' => $status_approved]);
        if ($update) {
            return redirect()->back()->with(['success' => 'Status berhasil diupdate']);
        } else {
            return redirect()->back()->with(['warning' => 'Status gagal diupdate']);
        }
    }

    public function batalkanizinsakit($id)
    {
        $update = DB::table('pengajuan_izin')->where('id', $id)->update(['status_approved' => 0]);
        if ($update) {
            return redirect()->back()->with(['success' => 'Izin/sakit berhasil dibatalkan']);
        } else {
            return redirect()->back()->with(['warning' => 'Gagal membatalkan izin/sakit']);
        }
    }

    public function cekpengajuanizin(Request $request)
    {
        $tanggal_izin = date('Y-m-d', strtotime($request->tanggal_izin));
        $nis = Auth::guard('siswa')->user()->nis;
        $cek = DB::table('pengajuan_izin')->where('nis', $nis)->where('tanggal_izin', $tanggal_izin)->count();
        return $cek;
    }
}
