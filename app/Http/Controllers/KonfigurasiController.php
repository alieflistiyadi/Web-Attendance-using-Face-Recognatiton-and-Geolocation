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
            ->orderBy('id')
            ->get();

        return view(
            'konfigurasi.konfigurasi_waktu',
            compact('waktu')
        );
    }
    public function updateWaktu(Request $request)
    {
        foreach ($request->id as $i => $id) {

            // Validasi urutan waktu tiap hari
            if (
                !(
                    $request->jam_mulai_masuk[$i] <= $request->batas_telat[$i] &&
                    $request->batas_telat[$i] <= $request->batas_masuk[$i] &&
                    $request->jam_mulai_pulang[$i] <= $request->batas_pulang[$i]
                )
            ) {
                return back()->with(
                    'warning',
                    'Urutan waktu pada hari ' . $request->hari[$i] . ' tidak valid.'
                );
            }

            DB::table('konfigurasi_waktu')
                ->where('id', $id)
                ->update([
                    'jam_mulai_masuk'  => $request->jam_mulai_masuk[$i],
                    'batas_telat'      => $request->batas_telat[$i],
                    'batas_masuk'      => $request->batas_masuk[$i],
                    'jam_mulai_pulang' => $request->jam_mulai_pulang[$i],
                    'batas_pulang'     => $request->batas_pulang[$i],
                    'updated_at'       => now(),
                ]);
        }

        return back()->with('success', 'Konfigurasi waktu berhasil diperbarui.');
    }
}
