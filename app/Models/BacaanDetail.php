<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BacaanDetail extends Model
{
    use HasFactory;

    protected $fillable = ['bacaan_id', 'arab', 'latin', 'terjemahan', 'sumber'];

    public function bacaan()
    {
        return $this->belongsTo(Bacaan::class);
    }
}