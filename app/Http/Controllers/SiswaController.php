<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Siswa;

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
    // validasi
    $request->validate([
        'nis' => 'required|unique:siswa,nis',
        'nama_lengkap' => 'required',
        'no_hp' => 'required',
        'kode_jurusan' => 'required'
    ]);

    $foto = null;

    // handle upload foto (kalau ada)
    if($request->hasFile('foto')){
        $foto = $request->file('foto')->store('uploads/absensi', 'public');
    }

    // simpan ke database
    Siswa::create([
        'nis' => $request->nis,
        'nama_lengkap' => $request->nama_lengkap,
        'kelas' => $request->jurusan,
        'no_hp' => $request->no_hp,
        'kode_jurusan' => $request->kode_jurusan,
        'foto' => $foto
    ]);

    return redirect('/siswa')->with('success', 'Data berhasil ditambahkan');
}
}
