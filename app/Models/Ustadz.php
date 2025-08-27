<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ustadz extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_lengkap',
        'alamat',
        'riwayat_pendidikan',
        'youtube',
        'instagram',
        'tiktok',
    ];

    public function kajianRekamans()
    {
        return $this->belongsToMany(KajianRekaman::class, 'kajian_rekaman_ustadz');
    }

    public function jadwalKajians()
    {
        return $this->belongsToMany(JadwalKajian::class, 'jadwal_kajian_ustadz');
    }
}