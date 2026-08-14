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
    // =========================
    // KONFIGURASI WALI KELAS
    // =========================

    public function waliKelas()
    {
        // Ambil semua kelas
        $kelas = DB::table('kelas')
            ->leftJoin('users', 'kelas.wali_kelas_id', '=', 'users.id')
            ->select(
                'kelas.id',
                'kelas.nama_kelas',
                'kelas.kode_jurusan',
                'kelas.tingkat',
                'kelas.wali_kelas_id',
                'users.name as nama_wali_kelas'
            )
            ->orderBy('kelas.tingkat')
            ->orderBy('kelas.nama_kelas')
            ->get();

        // Ambil semua guru
        $guru = DB::table('users')
            ->where('role', 'guru')
            ->orderBy('name')
            ->get();

        return view(
            'konfigurasi.wali_kelas',
            compact('kelas', 'guru')
        );
    }

    public function updateWaliKelas(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|integer|exists:kelas,id',
            'wali_kelas_id' => 'required|integer|exists:users,id',
        ]);

        // Pastikan user yang dipilih benar-benar guru
        $guru = DB::table('users')
            ->where('id', $request->wali_kelas_id)
            ->where('role', 'guru')
            ->first();

        if (!$guru) {
            return back()->with('warning', 'User yang dipilih bukan guru.');
        }

        // Pastikan satu guru tidak menjadi wali kelas
        // di lebih dari satu kelas
        $waliSudahDipakai = DB::table('kelas')
            ->where('wali_kelas_id', $request->wali_kelas_id)
            ->where('id', '!=', $request->kelas_id)
            ->exists();

        if ($waliSudahDipakai) {
            return back()->with(
                'warning',
                'Guru tersebut sudah menjadi wali kelas di kelas lain.'
            );
        }

        DB::table('kelas')
            ->where('id', $request->kelas_id)
            ->update([
                'wali_kelas_id' => $request->wali_kelas_id,
                'updated_at' => now(),
            ]);

        return back()->with(
            'success',
            'Wali kelas berhasil diperbarui.'
        );
    }
}
// ini kode konfigurasi controller  