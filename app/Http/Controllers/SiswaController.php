<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Siswa;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;

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

    public function guruDataSiswaKelas($kelasId, $penugasanId)
    {
        /*
        |--------------------------------------------------------------------------
        | GURU LOGIN
        |--------------------------------------------------------------------------
        */
        $guruId = Auth::guard('user')->id();

        if (!$guruId) {
            abort(403, 'Akun guru tidak ditemukan.');
        }


        /*
        |--------------------------------------------------------------------------
        | PASTIKAN PENUGASAN MILIK GURU LOGIN
        |--------------------------------------------------------------------------
        */
        $penugasan = DB::table('guru_mata_pelajaran as gmp')
            ->join(
                'kelas as k',
                'gmp.kelas_id',
                '=',
                'k.id'
            )
            ->join(
                'mata_pelajaran as mp',
                'gmp.mata_pelajaran_id',
                '=',
                'mp.id'
            )
            ->where(
                'gmp.id',
                $penugasanId
            )
            ->where(
                'gmp.guru_id',
                $guruId
            )
            ->where(
                'gmp.kelas_id',
                $kelasId
            )
            ->select(
                'gmp.id as penugasan_id',
                'gmp.kelas_id',
                'k.nama_kelas',
                'k.kode_jurusan',
                'k.tingkat',
                'mp.nama_mapel'
            )
            ->first();


        /*
        |--------------------------------------------------------------------------
        | JIKA TIDAK PUNYA AKSES
        |--------------------------------------------------------------------------
        */
        if (!$penugasan) {
            abort(
                403,
                'Anda tidak memiliki akses ke kelas atau mata pelajaran tersebut.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | AMBIL DATA SISWA
        |--------------------------------------------------------------------------
        |
        | Siswa diambil berdasarkan kelas.id.
        |
        */
        $query = DB::table('siswa as s')
            ->leftJoin(
                'jurusan as j',
                's.kode_jurusan',
                '=',
                'j.kode_jurusan'
            )
            ->where(
                's.kelas',
                $penugasan->nama_kelas
            )
            ->where(
                's.kode_jurusan',
                $penugasan->kode_jurusan
            )
            ->select(
                's.nis',
                's.nama_lengkap',
                's.kelas',
                's.kode_jurusan',
                's.no_hp',
                'j.nama_jurusan'
            );


        /*
        |--------------------------------------------------------------------------
        | SEARCH NAMA SISWA
        |--------------------------------------------------------------------------
        */
        if (!empty(request('nama_lengkap'))) {

            $query->where(
                's.nama_lengkap',
                'like',
                '%' . request('nama_lengkap') . '%'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */
        $siswa = $query
            ->orderBy('s.nama_lengkap')
            ->paginate(10)
            ->appends(request()->query());


        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */
        return view(
            'siswa.kelas_guru',
            compact(
                'siswa',
                'penugasan'
            )
        );
    }
    public function guruDataSiswa()
    {
        /*
        |--------------------------------------------------------------------------
        | GURU YANG SEDANG LOGIN
        |--------------------------------------------------------------------------
        */
        $guruId = Auth::guard('user')->id();

        if (!$guruId) {
            abort(403, 'Akun guru tidak ditemukan.');
        }


        /*
        |--------------------------------------------------------------------------
        | AMBIL SEMUA PENUGASAN GURU
        |--------------------------------------------------------------------------
        |
        | Hanya kelas + mata pelajaran yang memang
        | ditugaskan kepada guru yang login.
        |
        */
        $penugasan = DB::table('guru_mata_pelajaran as gmp')
            ->join(
                'kelas as k',
                'gmp.kelas_id',
                '=',
                'k.id'
            )
            ->join(
                'mata_pelajaran as mp',
                'gmp.mata_pelajaran_id',
                '=',
                'mp.id'
            )
            ->where(
                'gmp.guru_id',
                $guruId
            )
            ->select(
                'gmp.id as penugasan_id',
                'gmp.kelas_id',
                'gmp.mata_pelajaran_id',
                'k.nama_kelas',
                'k.kode_jurusan',
                'k.tingkat',
                'mp.nama_mapel'
            )
            ->orderBy('k.tingkat')
            ->orderBy('k.nama_kelas')
            ->orderBy('mp.nama_mapel')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | DAFTAR KELAS
        |--------------------------------------------------------------------------
        */
        $kelasList = $penugasan
            ->unique('kelas_id')
            ->values();


        /*
        |--------------------------------------------------------------------------
        | PENUGASAN PER KELAS
        |--------------------------------------------------------------------------
        */
        $penugasanByKelas = $penugasan
            ->groupBy('kelas_id')
            ->map(function ($items) {

                return $items
                    ->values()
                    ->map(function ($item) {

                        return [
                            'penugasan_id' => $item->penugasan_id,
                            'kelas_id' => $item->kelas_id,
                            'mata_pelajaran_id' => $item->mata_pelajaran_id,
                            'nama_mapel' => $item->nama_mapel,
                        ];

                    });

            });


        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */
        return view(
            'siswa.guru',
            compact(
                'kelasList',
                'penugasanByKelas'
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

    public function storeGuru(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | GURU LOGIN
        |--------------------------------------------------------------------------
        */

        $guruId = Auth::guard('user')->id();

        if (!$guruId) {
            abort(403, 'Akun guru tidak ditemukan.');
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDASI INPUT DASAR
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'nis' => 'required|unique:siswa,nis',
            'nama_lengkap' => [
                'required',
                'regex:/^[A-Za-z\s]+$/'
            ],
            'no_hp' => 'required',
            'penugasan_id' => 'required|integer',
            'kelas_id' => 'required|integer',
        ], [
            'nis.required' => 'NIS harus diisi.',
            'nis.unique' => 'NIS sudah terdaftar, tidak bisa ditambahkan!',
            'nama_lengkap.required' => 'Nama siswa wajib diisi.',
            'nama_lengkap.regex' => 'Nama hanya boleh berisi huruf dan spasi.',
            'no_hp.required' => 'No. HP harus diisi.',
            'penugasan_id.required' => 'Penugasan guru tidak ditemukan.',
            'kelas_id.required' => 'Kelas tidak ditemukan.',
        ]);


        /*
        |--------------------------------------------------------------------------
        | CEK PENUGASAN GURU
        |--------------------------------------------------------------------------
        |
        | Guru hanya boleh menambahkan siswa ke kelas
        | yang memang ditugaskan kepadanya.
        |
        */

        $penugasan = DB::table('guru_mata_pelajaran as gmp')
            ->join(
                'kelas as k',
                'gmp.kelas_id',
                '=',
                'k.id'
            )
            ->join(
                'mata_pelajaran as mp',
                'gmp.mata_pelajaran_id',
                '=',
                'mp.id'
            )
            ->where(
                'gmp.id',
                $request->penugasan_id
            )
            ->where(
                'gmp.guru_id',
                $guruId
            )
            ->where(
                'gmp.kelas_id',
                $request->kelas_id
            )
            ->select(
                'gmp.id as penugasan_id',
                'gmp.kelas_id',
                'k.nama_kelas',
                'k.kode_jurusan',
                'k.tingkat',
                'mp.nama_mapel'
            )
            ->first();


        /*
        |--------------------------------------------------------------------------
        | JIKA PENUGASAN TIDAK SESUAI
        |--------------------------------------------------------------------------
        */

        if (!$penugasan) {

            abort(
                403,
                'Anda tidak memiliki akses untuk menambahkan siswa ke kelas ini.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | PASSWORD DEFAULT SISWA
        |--------------------------------------------------------------------------
        */

        $password = \Illuminate\Support\Facades\Hash::make('User123!');


        /*
        |--------------------------------------------------------------------------
        | SIMPAN SISWA
        |--------------------------------------------------------------------------
        |
        | Kelas dan jurusan diambil dari database berdasarkan
        | penugasan guru, BUKAN dari input hidden form.
        |
        */

        try {

            Siswa::create([
                'nis' => $request->nis,
                'nama_lengkap' => $request->nama_lengkap,
                'kelas' => $penugasan->nama_kelas,
                'no_hp' => $request->no_hp,
                'kode_jurusan' => $penugasan->kode_jurusan,
                'password' => $password,
                'is_default_password' => 1,
            ]);


            /*
            |--------------------------------------------------------------------------
            | KEMBALI KE HALAMAN DATA SISWA GURU
            |--------------------------------------------------------------------------
            */

            return redirect()
                ->route(
                    'siswa.guru.kelas',
                    [
                        'kelasId' => $penugasan->kelas_id,
                        'penugasanId' => $penugasan->penugasan_id,
                    ]
                )
                ->with(
                    'success',
                    'Data siswa berhasil ditambahkan ke kelas ' .
                    $penugasan->nama_kelas .
                    '.'
                );

        } catch (\Exception $e) {

            return back()
                ->withInput()
                ->with(
                    'warning',
                    'Data gagal ditambahkan: ' . $e->getMessage()
                );
        }
    }
    public function editGuru(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | GURU LOGIN
        |--------------------------------------------------------------------------
        */

        $guruId = Auth::guard('user')->id();

        if (!$guruId) {
            abort(403, 'Akun guru tidak ditemukan.');
        }


        /*
        |--------------------------------------------------------------------------
        | AMBIL DATA SISWA
        |--------------------------------------------------------------------------
        */

        $siswa = DB::table('siswa')
            ->where('nis', $request->nis)
            ->first();

        if (!$siswa) {
            abort(404, 'Data siswa tidak ditemukan.');
        }


        /*
        |--------------------------------------------------------------------------
        | CEK APAKAH SISWA ADA DI KELAS YANG DITUGASKAN KE GURU
        |--------------------------------------------------------------------------
        */

        $penugasan = DB::table('guru_mata_pelajaran as gmp')
            ->join(
                'kelas as k',
                'gmp.kelas_id',
                '=',
                'k.id'
            )
            ->join(
                'mata_pelajaran as mp',
                'gmp.mata_pelajaran_id',
                '=',
                'mp.id'
            )
            ->where(
                'gmp.guru_id',
                $guruId
            )
            ->where(
                'k.nama_kelas',
                $siswa->kelas
            )
            ->where(
                'k.kode_jurusan',
                $siswa->kode_jurusan
            )
            ->select(
                'gmp.id as penugasan_id',
                'gmp.kelas_id',
                'k.nama_kelas',
                'k.kode_jurusan',
                'mp.nama_mapel'
            )
            ->first();


        if (!$penugasan) {
            abort(
                403,
                'Anda tidak memiliki akses untuk mengedit siswa ini.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | DATA UNTUK FORM EDIT
        |--------------------------------------------------------------------------
        */

        $jurusan = DB::table('jurusan')->get();

        $redirect_kelas = $request->redirect_kelas;

        return view(
            'siswa.edit',
            compact(
                'jurusan',
                'siswa',
                'redirect_kelas',
                'penugasan'
            )
        );
    }
    public function updateGuru(Request $request, $nis)
    {
        /*
        |--------------------------------------------------------------------------
        | GURU LOGIN
        |--------------------------------------------------------------------------
        */

        $guruId = Auth::guard('user')->id();

        if (!$guruId) {
            abort(403, 'Akun guru tidak ditemukan.');
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDASI
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'nama_lengkap' => [
                'required',
                'regex:/^[A-Za-z\s]+$/'
            ],
            'no_hp' => 'required',
        ], [
            'nama_lengkap.required' => 'Nama siswa wajib diisi.',
            'nama_lengkap.regex' => 'Nama hanya boleh berisi huruf dan spasi.',
            'no_hp.required' => 'No. HP wajib diisi.',
        ]);


        /*
        |--------------------------------------------------------------------------
        | AMBIL SISWA
        |--------------------------------------------------------------------------
        */

        $siswa = DB::table('siswa')
            ->where('nis', $nis)
            ->first();

        if (!$siswa) {
            abort(404, 'Data siswa tidak ditemukan.');
        }


        /*
        |--------------------------------------------------------------------------
        | CEK AKSES GURU
        |--------------------------------------------------------------------------
        */

        $penugasan = DB::table('guru_mata_pelajaran as gmp')
            ->join(
                'kelas as k',
                'gmp.kelas_id',
                '=',
                'k.id'
            )
            ->where(
                'gmp.guru_id',
                $guruId
            )
            ->where(
                'k.nama_kelas',
                $siswa->kelas
            )
            ->where(
                'k.kode_jurusan',
                $siswa->kode_jurusan
            )
            ->select(
                'gmp.kelas_id',
                'k.nama_kelas',
                'k.kode_jurusan'
            )
            ->first();


        if (!$penugasan) {
            abort(
                403,
                'Anda tidak memiliki akses untuk mengubah siswa ini.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE
        |--------------------------------------------------------------------------
        |
        | Guru hanya boleh mengubah nama dan nomor HP.
        | Kelas dan jurusan tidak boleh dipindahkan oleh guru.
        |
        */

        DB::table('siswa')
            ->where('nis', $nis)
            ->update([
                'nama_lengkap' => $request->nama_lengkap,
                'no_hp' => $request->no_hp,
            ]);


        /*
        |--------------------------------------------------------------------------
        | KEMBALI KE HALAMAN GURU
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'siswa.guru.kelas',
                [
                    'kelasId' => $penugasan->kelas_id,
                    'penugasanId' => $request->penugasan_id,
                ]
            )
            ->with(
                'success',
                'Data siswa berhasil diupdate.'
            );
    }
    public function deleteGuru(Request $request, $nis)
    {
        /*
        |--------------------------------------------------------------------------
        | GURU LOGIN
        |--------------------------------------------------------------------------
        */

        $guruId = Auth::guard('user')->id();

        if (!$guruId) {
            abort(403, 'Akun guru tidak ditemukan.');
        }


        /*
        |--------------------------------------------------------------------------
        | AMBIL SISWA
        |--------------------------------------------------------------------------
        */

        $siswa = DB::table('siswa')
            ->where('nis', $nis)
            ->first();

        if (!$siswa) {
            return back()
                ->with('warning', 'Data siswa tidak ditemukan.');
        }


        /*
        |--------------------------------------------------------------------------
        | CEK AKSES GURU
        |--------------------------------------------------------------------------
        */

        $penugasan = DB::table('guru_mata_pelajaran as gmp')
            ->join(
                'kelas as k',
                'gmp.kelas_id',
                '=',
                'k.id'
            )
            ->where(
                'gmp.guru_id',
                $guruId
            )
            ->where(
                'k.nama_kelas',
                $siswa->kelas
            )
            ->where(
                'k.kode_jurusan',
                $siswa->kode_jurusan
            )
            ->select(
                'gmp.id as penugasan_id',
                'gmp.kelas_id',
                'k.nama_kelas',
                'k.kode_jurusan'
            )
            ->first();


        if (!$penugasan) {
            abort(
                403,
                'Anda tidak memiliki akses untuk menghapus siswa ini.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | DELETE
        |--------------------------------------------------------------------------
        */

        DB::table('siswa')
            ->where('nis', $nis)
            ->delete();


        /*
        |--------------------------------------------------------------------------
        | KEMBALI KE HALAMAN GURU
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'siswa.guru.kelas',
                [
                    'kelasId' => $penugasan->kelas_id,
                    'penugasanId' => $penugasan->penugasan_id,
                ]
            )
            ->with(
                'success',
                'Data siswa berhasil dihapus.'
            );
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
// ini SiswaController.php