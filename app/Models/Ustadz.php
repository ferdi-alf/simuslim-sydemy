<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public function kajianRekaman()
    {
        return $this->hasMany(KajianRekaman::class);
    }

    public function jadwalKajians()
    {
        return $this->belongsToMany(JadwalKajian::class, 'jadwal_kajian_ustadz');
    }
}
