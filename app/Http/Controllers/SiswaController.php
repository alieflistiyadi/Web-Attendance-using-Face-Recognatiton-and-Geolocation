<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Siswa;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = Siswa::query();
        $query->select('siswa.*', 'nama_jurusan');
        $query->join('jurusan', 'siswa.kode_jurusan', '=', 'jurusan.kode_jurusan');
        $query->orderBy('nama_lengkap');
        if (!empty($request->nama_lengkap)) {
            $query->where('nama_lengkap', 'like', '%' . $request->nama_lengkap . '%');
        }

        if (!empty($request->kode_jurusan)) {
            $query->where('siswa.kode_jurusan', $request->kode_jurusan);
        }
        $siswa = $query->paginate(10);

        $jurusan = DB::table('jurusan')->get();
        return view('siswa.index', compact('siswa', 'jurusan'));
    }

    public function store(Request $request)
    {
        // VALIDASI
        $request->validate([
            'nis' => 'required|unique:siswa,nis',
            'nama_lengkap' => 'required',
            'kelas' => 'required',
            'no_hp' => 'required',
            'kode_jurusan' => 'required'
        ]);

        // default password
        $password = \Illuminate\Support\Facades\Hash::make('12345678');

        $foto = null;

        // upload foto
        if ($request->hasFile('foto')) {
            $foto = $request->nis . '.' . $request->file('foto')->getClientOriginalExtension();
            $request->file('foto')->storeAs('public/uploads/siswa', $foto);
        }

        try {
            Siswa::create([
                'nis' => $request->nis,
                'nama_lengkap' => $request->nama_lengkap,
                'kelas' => $request->kelas,
                'no_hp' => $request->no_hp,
                'kode_jurusan' => $request->kode_jurusan,
                'foto' => $foto,
                'password' => $password,
                'is_default_password' => 1
            ]);

            return redirect('/siswa')->with('success', 'Data berhasil ditambahkan');

        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
    public function edit(Request $request)
    {
        $nis = $request->nis;
        $jurusan = DB::table('jurusan')->get();
        $siswa = DB::table('siswa')->where('nis', $nis)->first();
        return view('siswa.edit', compact('jurusan', 'siswa'));
    }

    public function update(Request $request, $nis)
    {
        $nama_lengkap = $request->nama_lengkap;
        $kelas = $request->kelas;
        $no_hp = $request->no_hp;
        $kode_jurusan = $request->kode_jurusan;
        $old_foto = $request->old_foto;

        if ($request->hasFile('foto')) {
            $foto = $nis . '.' . $request->file('foto')->getClientOriginalExtension();
        } else {
            $foto = $old_foto;
        }

        try {
            $data = [
                'nama_lengkap' => $nama_lengkap,
                'kelas' => $kelas,
                'no_hp' => $no_hp,
                'kode_jurusan' => $kode_jurusan,
                'foto' => $foto,
            ];
            $update = DB::table('siswa')->where('nis', $nis)->update($data);
            if ($update) {
                if ($request->hasFile('foto')) {
                    $folderPath = 'public/uploads/siswa';
                    $folderOld = 'public/uploads/siswa' . $old_foto;
                    Storage::delete($folderOld);
                    $request->file('foto')->storeAs($folderPath, $foto);
                }
                return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
            }
        } catch (\Exception $e) {
            dd($e->getMessage());
            //return Redirect::back()->with(['warning' => 'Data Gagal Diupdate']);
        }
    }
    public function delete($nis)
    {
        $delete = DB::table('siswa')->where('nis', $nis)->delete();
        if ($delete) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }
}