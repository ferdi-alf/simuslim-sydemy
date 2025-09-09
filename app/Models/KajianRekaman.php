<?php

// KajianRekaman Model
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KajianRekaman extends Model
{
    protected $table = "kajian_rekamans";
    use HasFactory;

     protected $fillable = [
        'judul',
        'kitab',
        'kategori',
        'link',
        'jadwal_kajian_id'
    ];

    public function ustadzs()
    {
        return $this->belongsToMany(Ustadz::class, 'kajian_rekaman_ustadz');
    }

    public function jadwalKajian()
    {
        return $this->belongsTo(JadwalKajian::class);
    }
}