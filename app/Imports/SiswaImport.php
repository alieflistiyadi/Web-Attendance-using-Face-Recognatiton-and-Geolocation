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

            // ===========================
            // Format Nomor HP
            // ===========================

            // Ambil angka saja
            $hp = preg_replace('/\D/', '', (string) $row[4]);

            // Jika diawali 620 -> ubah menjadi 62
            if (str_starts_with($hp, '620')) {
                $hp = '62' . substr($hp, 3);
            }

            // Jika diawali 0 -> ubah menjadi 62
            elseif (str_starts_with($hp, '0')) {
                $hp = '62' . substr($hp, 1);
            }

            // Jika belum diawali 62 -> tambahkan 62
            elseif (!str_starts_with($hp, '62')) {
                $hp = '62' . $hp;
            }

            // Validasi nomor HP
            if (!preg_match('/^62\d{9,13}$/', $hp)) {
                $this->failed++;
                $this->errors[] = "Nomor HP {$row[4]} tidak valid.";
                continue;
            }

            // Tambahkan tanda +
            $hp = '+' . $hp;

            // ===========================
            // Simpan Data
            // ===========================
            Siswa::create([
                'nis'                  => $row[0],
                'nama_lengkap'         => $row[1],
                'kelas'                => $row[2],
                'kode_jurusan'         => $row[3],
                'no_hp'                => $hp,
                'password'             => Hash::make('12345678'),
                'is_default_password'  => 1,
            ]);

            $this->success++;
        }
    }
}