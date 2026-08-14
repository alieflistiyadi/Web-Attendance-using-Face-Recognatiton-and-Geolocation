<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Siswa;
use Illuminate\Support\Facades\Redirect;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = Siswa::query();
        $query->select('siswa.*', 'nama_jurusan');
        $query->join('jurusan', 'siswa.kode_jurusan', '=', 'jurusan.kode_jurusan');
        $query->orderBy('nama_lengkap', 'asc');

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

    // Method baru untuk per kelas
    public function indexKelas(Request $request, $kelas)
    {
        $query = Siswa::query();

        $query->select(
            'siswa.*',
            'jurusan.nama_jurusan'
        );

        $query->join(
            'jurusan',
            'siswa.kode_jurusan',
            '=',
            'jurusan.kode_jurusan'
        );

        $query->where(
            'siswa.kelas',
            $kelas
        );

        if (!empty($request->nama_lengkap)) {
            $query->where(
                'siswa.nama_lengkap',
                'like',
                '%' . $request->nama_lengkap . '%'
            );
        }

        if (!empty($request->kode_jurusan)) {
            $query->where(
                'siswa.kode_jurusan',
                $request->kode_jurusan
            );
        }

        $query->orderBy(
            'siswa.nama_lengkap',
            'asc'
        );

        $siswa = $query
            ->paginate(10)
            ->appends($request->all());

        $jurusan = DB::table('jurusan')->get();

        return view(
            'siswa.index_kelas',
            compact(
                'siswa',
                'jurusan',
                'kelas'
            )
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'nis' => 'required|unique:siswa,nis',
            'nama_lengkap' => [
                'required',
                'regex:/^[A-Za-z\s]+$/'
            ],
            'kelas' => 'required',
            'no_hp' => 'required',
            'kode_jurusan' => 'required'
        ], [
            'nis.unique' => 'NIS sudah terdaftar, tidak bisa ditambahkan!',
            'nama_lengkap.required' => 'Nama siswa wajib diisi.',
            'nama_lengkap.regex' => 'Nama hanya boleh berisi huruf dan spasi.'
        ]);

        $password = \Illuminate\Support\Facades\Hash::make('User123!'); // Password default untuk siswa baru

        $mappingJurusan = [
            'X TJKT 1' => 'TJKT',
            'X TJKT 2' => 'TJKT',
            'X TM' => 'TM',

            'XI TJKT' => 'TJKT',
            'XI TM' => 'TM',

            'XII TJKT' => 'TJKT',
            'XII TM' => 'TM',
        ];

        $kodeJurusan = $mappingJurusan[$request->kelas] ?? null;

        if (!$kodeJurusan) {
            return back()
                ->withInput()
                ->with('warning', 'Kelas yang dipilih tidak memiliki mapping jurusan.');
        }

        try {
            Siswa::create([
                'nis' => $request->nis,
                'nama_lengkap' => $request->nama_lengkap,
                'kelas' => $request->kelas,
                'no_hp' => $request->no_hp,
                'kode_jurusan' => $kodeJurusan,
                'password' => $password,
                'is_default_password' => 1
            ]);

            // Redirect kembali ke halaman kelas jika dari halaman per kelas
            if ($request->redirect_kelas) {
                return redirect('/siswa/kelas/' . $request->redirect_kelas)
                    ->with('success', 'Data berhasil ditambahkan');
            }

            return redirect('/siswa')->with('success', 'Data berhasil ditambahkan');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with(
                    'warning',
                    'Data gagal ditambahkan: ' . $e->getMessage()
                );
        }
    }

    public function edit(Request $request)
    {
        $nis = $request->nis;
        $jurusan = DB::table('jurusan')->get();
        $siswa = DB::table('siswa')->where('nis', $nis)->first();
        $redirect_kelas = $request->redirect_kelas;
        return view('siswa.edit', compact('jurusan', 'siswa', 'redirect_kelas'));
    }

    public function update(Request $request, $nis)
    {
        $request->validate([
            'nama_lengkap' => [
                'required',
                'regex:/^[A-Za-z\s]+$/'
            ],
            'kelas' => 'required',
            'no_hp' => 'required',
            'kode_jurusan' => 'required'
        ], [
            'nama_lengkap.required' => 'Nama siswa wajib diisi.',
            'nama_lengkap.regex' => 'Nama hanya boleh berisi huruf dan spasi.'
        ]);

        $nama_lengkap = $request->nama_lengkap;
        $kelas = $request->kelas;
        $no_hp = $request->no_hp;

        $mappingJurusan = [
            'X TJKT 1' => 'TJKT',
            'X TJKT 2' => 'TJKT',
            'X TM' => 'TM',

            'XI TJKT' => 'TJKT',
            'XI TM' => 'TM',

            'XII TJKT' => 'TJKT',
            'XII TM' => 'TM',
        ];

        $kode_jurusan = $mappingJurusan[$kelas] ?? null;

        if (!$kode_jurusan) {
            return back()
                ->withInput()
                ->with('warning', 'Kelas yang dipilih tidak memiliki mapping jurusan.');
        }

        try {
            $data = [
                'nama_lengkap' => $nama_lengkap,
                'kelas' => $kelas,
                'no_hp' => $no_hp,
                'kode_jurusan' => $kode_jurusan,

            ];

            $update = DB::table('siswa')->where('nis', $nis)->update($data);

            if ($update) {

                if ($request->redirect_kelas) {
                    return redirect('/siswa/kelas/' . $request->redirect_kelas)
                        ->with('success', 'Data Berhasil Diupdate');
                }

                return Redirect::back()->with([
                    'success' => 'Data Berhasil Diupdate'
                ]);
            }

        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function delete(Request $request, $nis)
    {
        $redirect_kelas = $request->redirect_kelas;
        $delete = DB::table('siswa')->where('nis', $nis)->delete();

        if ($delete) {
            if ($redirect_kelas) {
                return redirect('/siswa/kelas/' . $redirect_kelas)
                    ->with('success', 'Data Berhasil Dihapus');
            }
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }

    public function jurusan()
    {
        $jurusan = DB::table('jurusan')->get();
        return view('siswa.jurusan', compact('jurusan'));
    }

    public function kelas($kode_jurusan)
    {
        $kelas = DB::table('siswa')
            ->where('kode_jurusan', $kode_jurusan)
            ->select('kelas', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('kelas')
            ->get();

        return view('siswa.kelas', compact('kelas', 'kode_jurusan'));
    }

    public function listSiswa(Request $request, $kode_jurusan, $kelas)
    {
        $query = Siswa::query();
        $query->select('siswa.*', 'nama_jurusan');
        $query->join('jurusan', 'siswa.kode_jurusan', '=', 'jurusan.kode_jurusan');
        $query->where('siswa.kode_jurusan', $kode_jurusan);
        $query->where('siswa.kelas', $kelas);

        if (!empty($request->nama_lengkap)) {
            $query->where('nama_lengkap', 'like', '%' . $request->nama_lengkap . '%');
        }

        $query->orderBy('nama_lengkap', 'asc');
        $siswa = $query->paginate(10);
        $jurusan = DB::table('jurusan')->get();

        return view('siswa.list', compact('siswa', 'jurusan', 'kode_jurusan', 'kelas'));
    }
}