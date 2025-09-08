<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JadwalKajian extends Model
{
    use HasFactory;

    protected $fillable = [
        'kajian_id',
        'jam_mulai',
        'jam_selesai',
        'tanggal',
        'hari',
        'status',
        'diperuntukan',
        'link',
    ];

    public function kajian()
    {
        return $this->belongsTo(KajianPoster::class, 'kajian_id');
    }

    public function ustadzs()
    {
        return $this->belongsToMany(Ustadz::class, 'jadwal_kajian_ustadz');
    }
}
