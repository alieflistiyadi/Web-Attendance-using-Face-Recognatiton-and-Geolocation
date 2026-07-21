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
    public function konfigurasiWaktu()
    {
        $waktu = DB::table('konfigurasi_waktu')
            ->where('id', 1)
            ->first();

        return view(
            'konfigurasi.konfigurasi_waktu',
            compact('waktu')
        );
    }
    public function updateWaktu(Request $request)
    {
        $request->validate([
            'jam_mulai_masuk' => 'required',
            'batas_telat' => 'required',
            'batas_masuk' => 'required',
            'jam_mulai_pulang' => 'required',
            'batas_pulang' => 'required',
        ]);

        // Validasi urutan waktu
        if (
            !(
                $request->jam_mulai_masuk <= $request->batas_telat &&
                $request->batas_telat <= $request->batas_masuk &&
                $request->jam_mulai_pulang <= $request->batas_pulang
            )
        ) {
            return back()->with('warning', 'Urutan waktu tidak valid.');
        }

        DB::table('konfigurasi_waktu')
            ->where('id', 1)
            ->update([

                'jam_mulai_masuk' => $request->jam_mulai_masuk,
                'batas_telat' => $request->batas_telat,
                'batas_masuk' => $request->batas_masuk,

                'jam_mulai_pulang' => $request->jam_mulai_pulang,
                'batas_pulang' => $request->batas_pulang,

                'updated_at' => now()

            ]);

        return back()->with('success', 'Konfigurasi waktu berhasil diperbarui.');
    }
}
