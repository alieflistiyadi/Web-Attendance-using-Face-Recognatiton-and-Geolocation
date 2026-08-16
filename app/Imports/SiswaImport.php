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

            // ===========================
            // Cek jumlah kolom
            // ===========================

            if (count($row) < 5) {
                $this->failed++;
                $this->errors[] = "Format data tidak lengkap.";
                continue;
            }

            // ===========================
            // Ambil data dari Excel
            // ===========================

            $nis = trim((string) $row[0]);
            $nama = trim((string) $row[1]);
            $kelas = trim((string) $row[2]);
            $kodeJurusan = strtoupper(trim((string) $row[3]));
            $noHpExcel = trim((string) $row[4]);

            // ===========================
            // Lewati baris kosong
            // ===========================

            if ($nis === '') {
                continue;
            }

            // ===========================
            // Format NIS
            // ===========================

            /*
             * Kalau Excel membaca:
             * 00123456
             *
             * menjadi:
             * 123456
             *
             * maka tambahkan kembali angka 0
             * sampai maksimal 8 digit.
             */

            if (ctype_digit($nis)) {
                $nis = str_pad($nis, 8, '0', STR_PAD_LEFT);
            }

            // Batasi maksimal 10 karakter
            if (strlen($nis) > 10) {
                $this->failed++;
                $this->errors[] = "NIS {$nis} maksimal 10 karakter.";
                continue;
            }

            // ===========================
            // Cek NIS sudah ada
            // ===========================

            if (Siswa::where('nis', $nis)->exists()) {
                $this->failed++;
                $this->errors[] = "NIS {$nis} sudah terdaftar.";
                continue;
            }

            // ===========================
            // Cek jurusan
            // ===========================

            $jurusan = Jurusan::where(
                'kode_jurusan',
                $kodeJurusan
            )->first();

            if (!$jurusan) {
                $this->failed++;
                $this->errors[] = "Kode jurusan {$kodeJurusan} tidak ditemukan.";
                continue;
            }

            // ===========================
            // Format Nomor HP
            // ===========================

            // Ambil angka saja
            $hp = preg_replace('/\D/', '', $noHpExcel);

            // Jika diawali 620
            if (str_starts_with($hp, '620')) {
                $hp = '62' . substr($hp, 3);
            }

            // Jika diawali 0
            elseif (str_starts_with($hp, '0')) {
                $hp = '62' . substr($hp, 1);
            }

            // Jika belum diawali 62
            elseif (!str_starts_with($hp, '62')) {
                $hp = '62' . $hp;
            }

            // Validasi nomor HP
            if (!preg_match('/^62\d{9,13}$/', $hp)) {
                $this->failed++;
                $this->errors[] = "Nomor HP {$noHpExcel} tidak valid.";
                continue;
            }

            // Tambahkan +
            $hp = '+' . $hp;

            // ===========================
            // Simpan Data
            // ===========================

            Siswa::create([
                'nis' => $nis,
                'nama_lengkap' => $nama,
                'kelas' => $kelas,
                'kode_jurusan' => $kodeJurusan,
                'no_hp' => $hp,
                'password' => Hash::make('12345678'),
                'is_default_password' => 1,
            ]);

            $this->success++;
        }
    }
}