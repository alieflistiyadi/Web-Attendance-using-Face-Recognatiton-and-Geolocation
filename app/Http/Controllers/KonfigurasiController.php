<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class KonfigurasiController extends Controller
{
    public function lokasisekolah()
    {
        $lok_sekolah = DB::table('konfigurasi_lokasi')
            ->where('id', 1)
            ->first();

        return view('konfigurasi.lokasisekolah', compact('lok_sekolah'));
    }

    public function updatelokasisekolah(Request $request)
    {
        $request->validate([
            'latitude' => 'required',
            'longitude' => 'required',
            'radius' => 'required|numeric'
        ]);

        $update = DB::table('konfigurasi_lokasi')
            ->where('id', 1)
            ->update([
                'latitude'  => $request->latitude,
                'longitude' => $request->longitude,
                'radius'    => $request->radius
            ]);

        if ($update) {
            return back()->with('success', 'Lokasi berhasil diperbarui.');
        }

        return back()->with('warning', 'Lokasi gagal diperbarui.');
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
// ini kode konfigurasi controller  