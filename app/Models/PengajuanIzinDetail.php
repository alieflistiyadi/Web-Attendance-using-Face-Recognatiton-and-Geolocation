<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanIzinDetail extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_izin_detail';

    protected $fillable = [
        'pengajuan_izin_id',
        'jadwal_pelajaran_id',
        'guru_id',
        'mata_pelajaran_id',
        'status_approved',
        'catatan',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];
}