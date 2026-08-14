<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalPelajaran extends Model
{
    protected $table = 'jadwal_pelajaran';

    protected $fillable = [
        'kelas_id',
        'penugasan_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'jam_mulai_absen',
        'batas_telat',
        'status',
    ];

    public function penugasan()
    {
        return $this->belongsTo(
            GuruMataPelajaran::class,
            'penugasan_id'
        );
    }

    public function kelas()
    {
        return $this->belongsTo(
            Kelas::class,
            'kelas_id'
        );
    }
}
// jadwalpelajaran.php