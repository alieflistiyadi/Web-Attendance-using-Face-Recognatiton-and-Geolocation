<?php

namespace App\Http\Controllers;

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
        return view('attendance.create', compact('cek'));
    }

    public function store(Request $request)
    {
        // Tambahkan sementara di method store(), baris pertama

        $nis = Auth::guard('siswa')->user()->nis;
        $tgl_presensi = date('Y-m-d');
        $jam = date('H:i:s');
        $latitudesekolah = -6.31992184833979;
        $longitudesekolah = 106.79465867539258;
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

        if ($radius > 500) {
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
        $nama_lengkap = $request->nama_lengkap;
        $no_hp = $request->no_hp;
        $password = Hash::make($request->password);
        $siswa = DB::table('siswa')->where('nis', $nis)->first();
        if ($request->hasFile('foto')) {
            $foto = $nis . '.' . $request->file('foto')->getClientOriginalExtension();
        } else {
            $foto = $siswa->foto;
        }
        if (empty($request->password)) {
            $data = [
                'nama_lengkap' => $nama_lengkap,
                'no_hp' => $no_hp,
                'foto' => $foto,

            ];
        } else {
            $data = [
                'nama_lengkap' => $nama_lengkap,
                'no_hp' => $no_hp,
                'password' => $password,
                'foto' => $foto
            ];
        }

        $update = DB::table('siswa')->where('nis', $nis)->update($data);
        if ($update) {
            if ($request->hasFile('foto')) {
                $folderPath = 'public/uploads/siswa';
                $request->file('foto')->storeAs($folderPath, $foto);
            }
            return Redirect::back()->with(['success' => 'Profile berhasil diupdate']);
        } else {
            return Redirect::back()->with(['error' => 'Gagal mengupdate profile']);
        }
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
    public function monitoring()
    {
        return view('attendance.monitoring');
    }

    public function getattendance(Request $request)
    {
        $tanggal = date('Y-m-d', strtotime($request->tanggal));
        $presensi = DB::table('attendance')
            ->select('attendance.*', 'nama_lengkap', 'nama_jurusan')
            ->join('siswa', 'attendance.nis', '=', 'siswa.nis')
            ->join('jurusan', 'siswa.kode_jurusan', '=', 'jurusan.kode_jurusan')
            ->where('attendance.tgl_presensi', $tanggal)
            ->get();

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
}

