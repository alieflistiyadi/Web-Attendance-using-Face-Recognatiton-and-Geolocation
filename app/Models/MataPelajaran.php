<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class MataPelajaran extends Model
{
    use HasFactory;

    protected $table = 'mata_pelajaran';

    protected $fillable = [
        'kode_mapel',
        'nama_mapel',
        'kode_jurusan',
    ];

    public function guruMataPelajaran()
    {
        return $this->hasMany(
            GuruMataPelajaran::class,
            'mata_pelajaran_id'
        );
    }
}
// MataPelajaran.php