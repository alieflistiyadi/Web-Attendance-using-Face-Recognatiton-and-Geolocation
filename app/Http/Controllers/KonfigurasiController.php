<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class KonfigurasiController extends Controller
{
    public function lokasisekolah()
    {
        $lok_sekolah = DB::table('konfigurasi_lokasi')->where('id', 1)->first();
        return view('konfigurasi.lokasisekolah', compact('lok_sekolah'));
    }

    public function updatelokasisekolah(Request $request)
    {
        $lokasi_sekolah = $request->lokasi_sekolah;
        $radius = $request->radius;

        $update = DB::table('konfigurasi_lokasi')->where('id', 1)->update([
            'lokasi_sekolah' => $lokasi_sekolah,
            'radius' => $radius
        ]);

        if ($update) {
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate']);
        }
    }
}
