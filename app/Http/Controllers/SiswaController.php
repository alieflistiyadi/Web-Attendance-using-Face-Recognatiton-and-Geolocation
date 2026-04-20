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
}
