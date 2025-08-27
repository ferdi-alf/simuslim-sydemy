<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bacaan extends Model
{
    use HasFactory;

    protected $fillable = ['judul', 'type', 'deskripsi'];

    public function details()
    {
        return $this->hasMany(BacaanDetail::class);
    }
}