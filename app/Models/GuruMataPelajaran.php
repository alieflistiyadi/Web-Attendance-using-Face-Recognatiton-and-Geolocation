<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuruMataPelajaran extends Model
{
    protected $table = 'guru_mata_pelajaran';

    protected $fillable = [
        'guru_id',
        'mata_pelajaran_id',
        'kelas_id',
    ];

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function jadwal()
    {
        return $this->hasMany(JadwalPelajaran::class, 'penugasan_id');
    }
}

// ini kode GuruMataPelajaran.php