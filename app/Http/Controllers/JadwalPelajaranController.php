<?php

namespace App\Http\Controllers;

use App\Models\GuruMataPelajaran;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JadwalPelajaranController extends Controller
{
    public function index()
    {
        $guru = User::where('role', 'guru')
            ->orderBy('name')
            ->get();

        $mapel = MataPelajaran::orderBy('nama_mapel')
            ->get();

        $kelas = Kelas::orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->orderBy('kode_jurusan')
            ->get();

        $penugasan = GuruMataPelajaran::with([
            'guru',
            'mataPelajaran',
            'kelas'
        ])
            ->orderByDesc('id')
            ->get();

        $jadwal = JadwalPelajaran::with([
            'penugasan.guru',
            'penugasan.mataPelajaran',
            'penugasan.kelas'
        ])
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        $hari = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
        ];

        return view('jadwal.index', compact(
            'guru',
            'mapel',
            'kelas',
            'penugasan',
            'jadwal',
            'hari'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | PENUGASAN GURU + MAPEL + KELAS
    |--------------------------------------------------------------------------
    */

    public function storePenugasan(Request $request)
    {
        $request->validate([
            'guru_id' => 'required|exists:users,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'kelas_id' => 'required|exists:kelas,id',
        ]);

        $guru = User::findOrFail($request->guru_id);

        if ($guru->role !== 'guru') {
            return back()->with('error', 'User yang dipilih bukan akun guru.');
        }

        $sudahAda = GuruMataPelajaran::where('guru_id', $request->guru_id)
            ->where('mata_pelajaran_id', $request->mata_pelajaran_id)
            ->where('kelas_id', $request->kelas_id)
            ->exists();

        if ($sudahAda) {
            return back()->with(
                'error',
                'Penugasan guru, mata pelajaran dan kelas tersebut sudah ada.'
            );
        }

        GuruMataPelajaran::create([
            'guru_id' => $request->guru_id,
            'mata_pelajaran_id' => $request->mata_pelajaran_id,
            'kelas_id' => $request->kelas_id,
        ]);

        return back()->with(
            'success',
            'Penugasan guru dan mata pelajaran berhasil ditambahkan.'
        );
    }

    public function updatePenugasan(Request $request, $id)
    {
        $request->validate([
            'guru_id' => 'required|exists:users,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'kelas_id' => 'required|exists:kelas,id',
        ]);

        $penugasan = GuruMataPelajaran::findOrFail($id);

        $sudahAda = GuruMataPelajaran::where('guru_id', $request->guru_id)
            ->where('mata_pelajaran_id', $request->mata_pelajaran_id)
            ->where('kelas_id', $request->kelas_id)
            ->where('id', '!=', $id)
            ->exists();

        if ($sudahAda) {
            return back()->with(
                'error',
                'Penugasan tersebut sudah tersedia.'
            );
        }

        $penugasan->update([
            'guru_id' => $request->guru_id,
            'mata_pelajaran_id' => $request->mata_pelajaran_id,
            'kelas_id' => $request->kelas_id,
        ]);

        return back()->with(
            'success',
            'Penugasan berhasil diperbarui.'
        );
    }

    public function deletePenugasan($id)
    {
        $penugasan = GuruMataPelajaran::findOrFail($id);

        $penugasan->delete();

        return back()->with(
            'success',
            'Penugasan berhasil dihapus.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | JADWAL
    |--------------------------------------------------------------------------
    */

    public function storeJadwal(Request $request)
    {
        $request->validate([
            'penugasan_id' => 'required|exists:guru_mata_pelajaran,id',
            'hari' => 'required|integer|min:1|max:5',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'jam_mulai_absen' => 'required|date_format:H:i',
            'batas_telat' => 'required|date_format:H:i|after_or_equal:jam_mulai_absen',
        ]);

        // Ambil penugasan
        $penugasan = GuruMataPelajaran::findOrFail(
            $request->penugasan_id
        );

        /*
        |--------------------------------------------------------------------------
        | KELAS DIAMBIL OTOMATIS DARI PENUGASAN
        |--------------------------------------------------------------------------
        */

        $kelasId = $penugasan->kelas_id;

        /*
        |--------------------------------------------------------------------------
        | CEK BENTROK KELAS
        |--------------------------------------------------------------------------
        */

        $bentrokKelas = JadwalPelajaran::where('kelas_id', $kelasId)
            ->where('hari', $request->hari)
            ->where(function ($query) use ($request) {
                $query->where('jam_mulai', '<', $request->jam_selesai)
                    ->where('jam_selesai', '>', $request->jam_mulai);
            })
            ->exists();

        if ($bentrokKelas) {
            return back()->with(
                'error',
                'Jadwal bentrok dengan jadwal lain pada kelas tersebut.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | CEK BENTROK GURU
        |--------------------------------------------------------------------------
        */

        $bentrokGuru = JadwalPelajaran::whereHas('penugasan', function ($query) use ($penugasan) {
            $query->where('guru_id', $penugasan->guru_id);
        })
            ->where('hari', $request->hari)
            ->where(function ($query) use ($request) {
                $query->where('jam_mulai', '<', $request->jam_selesai)
                    ->where('jam_selesai', '>', $request->jam_mulai);
            })
            ->exists();

        if ($bentrokGuru) {
            return back()->with(
                'error',
                'Jadwal bentrok dengan jadwal guru tersebut.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | SIMPAN JADWAL
        |--------------------------------------------------------------------------
        */

        JadwalPelajaran::create([
            'kelas_id' => $penugasan->kelas_id,
            'penugasan_id' => $request->penugasan_id,
            'hari' => $request->hari,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'jam_mulai_absen' => $request->jam_mulai_absen,
            'batas_telat' => $request->batas_telat,
            'status' => true,
        ]);

        return back()->with(
            'success',
            'Jadwal pelajaran berhasil ditambahkan.'
        );
    }
    public function updateJadwal(Request $request, $id)
    {
        $request->validate([
            'penugasan_id' => 'required|exists:guru_mata_pelajaran,id',
            'hari' => 'required|integer|min:1|max:5',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'jam_mulai_absen' => 'required|date_format:H:i',
            'batas_telat' => 'required|date_format:H:i|after_or_equal:jam_mulai_absen',
        ]);

        $jadwal = JadwalPelajaran::findOrFail($id);

        $jadwal->update([
            'penugasan_id' => $request->penugasan_id,
            'hari' => $request->hari,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'jam_mulai_absen' => $request->jam_mulai_absen,
            'batas_telat' => $request->batas_telat,
        ]);

        return back()->with(
            'success',
            'Jadwal berhasil diperbarui.'
        );
    }

    public function deleteJadwal($id)
    {
        JadwalPelajaran::findOrFail($id)->delete();

        return back()->with(
            'success',
            'Jadwal berhasil dihapus.'
        );
    }
}
// ini kode jadwal pelajaran controller.php