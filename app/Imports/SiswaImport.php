<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\Jurusan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Hash;

class SiswaImport implements ToCollection
{
    public $success = 0;
    public $failed = 0;
    public $errors = [];

    public function collection(Collection $rows)
    {
        foreach ($rows->skip(1) as $row) {

            // Cek apakah jumlah kolom lengkap
            if (count($row) < 5) {
                $this->failed++;
                $this->errors[] = "Format data tidak lengkap.";
                continue;
            }

            // Lewati baris kosong
            if (empty($row[0])) {
                continue;
            }

            // Cek apakah NIS sudah ada
            if (Siswa::where('nis', $row[0])->exists()) {
                $this->failed++;
                $this->errors[] = "NIS {$row[0]} sudah terdaftar.";
                continue;
            }

            // Cek jurusan
            $jurusan = Jurusan::where('kode_jurusan', $row[3])->first();

            if (!$jurusan) {
                $this->failed++;
                $this->errors[] = "Kode jurusan {$row[3]} tidak ditemukan.";
                continue;
            }

            // Nomor HP
            $hp = preg_replace('/[^0-9]/', '', (string) $row[4]);

            if (!preg_match('/^62\d{9,13}$/', $hp)) {
                $this->failed++;
                $this->errors[] = "Nomor HP {$row[4]} tidak valid.";
                continue;
            }

            // Simpan data
            Siswa::create([
                'nis'           => $row[0],
                'nama_lengkap'  => $row[1],
                'kelas'         => $row[2],
                'kode_jurusan'  => $row[3],
                'no_hp'         => $hp,
                'password'      => Hash::make($row[0]), // password default = NIS
            ]);

            $this->success++;
        }
    }
}