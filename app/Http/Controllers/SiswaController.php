<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Siswa;
use Illuminate\Support\Facades\Redirect;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = Siswa::query();
        $query->select('siswa.*','nama_jurusan');
        $query->join('jurusan', 'siswa.kode_jurusan', '=', 'jurusan.kode_jurusan');
        $query->orderBy('nama_lengkap');
        if(!empty($request->nama_lengkap)){
            $query->where('nama_lengkap','like','%'.$request->nama_lengkap . '%');
        }

        if(!empty($request->kode_jurusan)){
            $query->where('siswa.kode_jurusan', $request->kode_jurusan);
        }
        $siswa = $query->paginate(2);

        $jurusan = DB::table('jurusan')->get();
        return view('siswa.index', compact('siswa', 'jurusan'));
    }

    public function store(Request $request)
{
    $nis = $request->nis;
    $nama_lengkap = $request->nama_lengkap;
    $kelas = $request->kelas;
    $no_hp = $request->no_hp;
    $kode_jurusan = $request->kode_jurusan;
    $password = \Illuminate\Support\Facades\Hash::make('1234');
    
    if ($request->hasFile('foto')) {
            $foto = $nis . '.' . $request->file('foto')->getClientOriginalExtension();
        } else {
            $foto = null;
        }

        try {
            $data = [
                'nis' => $nis,
                'nama_lengkap' => $nama_lengkap,
                'kelas' => $kelas,
                'no_hp' => $no_hp,
                'kode_jurusan' => $kode_jurusan,
                'foto' => $foto,
                'password' => $password
            ];
            $simpan = DB::table('siswa')->insert($data);
            if($simpan){
                if ($request->hasFile('foto')) {
                $folderPath = 'public/uploads/siswa';
                $request->file('foto')->storeAs($folderPath, $foto);
            }
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
            }
        } catch (\Exception $e){
            dd($e->getMessage());
            //return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
        }
}
}
