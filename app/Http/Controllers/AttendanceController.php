<?php

namespace App\Http\Controllers;

use DB;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Redirect;


class AttendanceController extends Controller
{
    public function create()
    {
        $hari_ini = date('Y-m-d');
        $nis = Auth::guard('siswa')->user()->nis;
        $cek = \DB::table('attendance')->where('nis', $nis)->where('tgl_presensi', $hari_ini)->count();
        return view('attendance.create', compact('cek'));
    }

    public function store(Request $request)
    {

        $nis = Auth::guard('siswa')->user()->nis;
        $tgl_presensi = date('Y-m-d');
        $jam = date('H:i:s');
        $lokasi = $request->lokasi;
        $latitudesekolah = -6.269107996706856;
        $longitudesekolah = 106.91735464750927;
        $lokasiuser = explode(",", $lokasi);
        $latitudeuser = $lokasiuser[0];
        $longitudeuser = $lokasiuser[1];
        $jarak = $this->distance($latitudesekolah, $longitudesekolah, $latitudeuser, $longitudeuser);
        $radius = round($jarak['meters']);

        $cek = \DB::table('attendance')->where('nis', $nis)->where('tgl_presensi', $tgl_presensi)->count();

        if ($cek > 0) {
            $ket = "out";
        } else {
            $ket = "in";
        }
        $image = $request->image;
        $folderPath = "public/uploads/absensi/";
        $format_name = $nis . '_' . $tgl_presensi . '_' . $ket . '_' . $jam;
        $image_parts = explode(";base64,", $image);
        $image_base64 = base64_decode($image_parts[1]);
        $filename = $format_name . '.png';
        $file = $folderPath . $filename;

        if ($radius > 10) {
            echo "error|Anda Terlalu Jauh Dari Sekolah, Jarak Anda: " . $radius . " meter dari sekolah|";
        } else {
            if ($cek > 0) {
                $data_pulang = [
                    'jam_out' => $jam,
                    'foto_out' => $filename,
                    'location_out' => $lokasi
                ];
                $update = \DB::table('attendance')->where('nis', $nis)->where('tgl_presensi', $tgl_presensi)->update($data_pulang);
                if ($update) {
                    echo "success|Terimakasih, Hati-hati Di Jalan|out";
                    Storage::put($file, $image_base64);
                } else {
                    echo "error|Gagal melakukan presensi pulang|out";
                }
            } else {
                $data = [
                    'nis' => $nis,
                    'tgl_presensi' => $tgl_presensi,
                    'jam_in' => $jam,
                    'foto_in' => $filename,
                    'location_in' => $lokasi
                ];
                $simpan = \DB::table('attendance')->insert($data);
                if ($simpan) {
                    echo "success|Terimakasih, Selamat Datang|in";
                    Storage::put($file, $image_base64);
                } else {
                    echo "error|Gagal melakukan presensi masuk|out";
                }
            }
        }
    }

    //Menghitung Jarak
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
        $siswa = DB::table('siswa')->where('nis',$nis )->first();
        return view('attendance.editprofile', compact('siswa'));
    }

    public function updateprofile(Request $request)
    {
        $nis = Auth::guard('siswa')->user()->nis;
        $nama_lengkap = $request->nama_lengkap;
        $no_hp = $request->no_hp;
        $password = Hash::make($request->password);
        $siswa = DB::table('siswa')->where('nis',$nis )->first();
        if ($request->hasFile('foto')) {
            $foto = $nis . '_' . $request -> file('foto')->getClientOriginalExtension();
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
            if($request->hashFile('foto')){
                $folderPath = 'public/uploads/siswa';
                $request->file('foto')->storeAs($folderPath, $foto);
            }
            return Redirect::back()->with(['success' => 'Profile berhasil diupdate']);
        } else {
            return Redirect::back()->with(['error' => 'Gagal mengupdate profile']);
        }
    }
}