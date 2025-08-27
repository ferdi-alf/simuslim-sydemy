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
    ];

    public function ustadzs()
    {
        return $this->belongsToMany(Ustadz::class, 'kajian_rekaman_ustadz');
    }
}