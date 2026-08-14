<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';

    protected $fillable = [
        'tingkat',
        'nama_kelas',
        'kode_jurusan',
    ];

    public function guruMataPelajaran()
    {
        return $this->hasMany(
            GuruMataPelajaran::class,
            'kelas_id'
        );
    }
}
// kelas.php