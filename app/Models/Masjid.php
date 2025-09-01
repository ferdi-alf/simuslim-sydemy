<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Masjid extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'alamat',
        'maps',
        'channels',
    ];

    public function kajianPosters()
    {
        return $this->hasMany(KajianPoster::class);
    }
}
