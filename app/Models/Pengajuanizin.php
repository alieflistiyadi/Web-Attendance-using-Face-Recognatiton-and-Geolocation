<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengajuanizin extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_izin';

    protected $fillable = [
        'nis',
        'tanggal_izin',
        'status',
        'keterangan',
        'status_approved',
        'surat_sakit',
        'surat_izin',
    ];

    public function details()
    {
        return $this->hasMany(
            PengajuanIzinDetail::class,
            'pengajuan_izin_id'
        );
    }
}