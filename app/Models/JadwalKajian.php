<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JadwalKajian extends Model
{
    use HasFactory;

    protected $guarded = [];

    const STATUS_BELUM_DIMULAI = 'belum dimulai';
    const STATUS_BERJALAN = 'Sedang berjalan';
    const STATUS_SELESAI = 'selesai';
    const STATUS_DILIBURKAN = 'Diliburkan';

    protected $fillable = [
        'kajian_id',
        'jam_mulai',
        'jam_selesai',
        'tanggal',
        'hari',
        'status',
        'diperuntukan',
        'link',
        'position'
    ];

    public function kajian()
    {
        return $this->belongsTo(KajianPoster::class, 'kajian_id');
    }

    public function kajianRekamans()
    {
        return $this->hasMany(KajianRekaman::class);
    }

    public function ustadzs()
    {
        return $this->belongsToMany(Ustadz::class, 'jadwal_kajian_ustadz');
    }

    public function kajianPoster()
    {
        return $this->belongsTo(KajianPoster::class, 'kajian_id');
    }

    public static function statusOptions()
    {
        return [
            self::STATUS_BELUM_DIMULAI => 'Belum Dimulai',
            self::STATUS_BERJALAN => 'Sedang Berjalan',
            self::STATUS_SELESAI => 'Selesai',
            self::STATUS_DILIBURKAN => 'Diliburkan',
        ];
    }
}
