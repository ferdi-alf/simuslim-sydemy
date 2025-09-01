<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KajianRekamanUstadz extends Model
{
    use HasFactory;

    protected $table = 'kajian_rekaman_ustadz';

    protected $fillable = [
        'kajian_rekaman_id',
        'ustadz_id',
    ];

    public function kajianRekaman()
    {
        return $this->belongsTo(KajianRekaman::class);
    }

    public function ustadz()
    {
        return $this->belongsTo(Ustadz::class);
    }
}