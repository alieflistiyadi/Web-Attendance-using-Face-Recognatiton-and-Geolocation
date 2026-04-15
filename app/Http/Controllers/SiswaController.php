<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function index()
    {
        $siswa = \DB::table('siswa')->orderBy('nama_lengkap')
        ->join('jurusan', 'siswa.kode_jurusan', '=', 'jurusan.kode_jurusan')
        ->get();
        return view('siswa.index', compact('siswa'));
    }
}
