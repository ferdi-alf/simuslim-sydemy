<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JadwalKajianUstadz extends Model
{
    use HasFactory;

    protected $table = 'jadwal_kajian_ustadz';

    protected $fillable = [
        'jadwal_kajian_id',
        'ustadz_id',
    ];
}
