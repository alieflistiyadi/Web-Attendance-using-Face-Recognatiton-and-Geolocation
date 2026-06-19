<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Jurusan;
use Illuminate\Support\Facades\Redirect;

class JurusanController extends Controller
{
    public function index(Request $request)
    {
        $nama_jurusan = $request->nama_jurusan;
        $query = Jurusan::query();
        $query->select('*');
        if (!empty($nama_jurusan)) {
            $query->where('nama_jurusan', 'like', '%' . $nama_jurusan . '%');
        }
        $jurusan = $query->get();
        // $jurusan = DB::table('jurusan')->orderBy('kode_jurusan')->get();

        return view('jurusan.index', compact('jurusan'));
    }

    public function store(Request $request)
    {
        $kode_jurusan = $request->kode_jurusan;
        $nama_jurusan = $request->nama_jurusan;
        $data = [
            'kode_jurusan' => $kode_jurusan,
            'nama_jurusan' => $nama_jurusan
        ];

        $simpan = DB::table('jurusan')->insert($data);
        if ($simpan) {
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
        }
    }

    public function edit(Request $request)
    {
        $kode_jurusan = $request->kode_jurusan;
        $jurusan = DB::table('jurusan')->where('kode_jurusan', $kode_jurusan)->first();
        return view('jurusan.edit', compact('jurusan'));
    }

    public function update(Request $request, $kode_jurusan)
    {
        $nama_jurusan = $request->nama_jurusan;
        $data = [
            'nama_jurusan' => $nama_jurusan
        ];

        $update = DB::table('jurusan')->where('kode_jurusan', $kode_jurusan)->update($data);
        if ($update) {
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate']);
        }
    }

    public function delete($kode_jurusan)
    {
        $delete = DB::table('jurusan')->where('kode_jurusan', $kode_jurusan)->delete();
        if ($delete) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }
}
