<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KajianRekaman extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'kitab',
        'ustadz_id',
        'kategori',
        'link',
    ];

    public function ustadz()
    {
        return $this->belongsTo(Ustadz::class);
    }
}
