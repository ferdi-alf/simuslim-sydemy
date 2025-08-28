<?php
// app/Models/Category.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'deskripsi', 
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function kajianPosters()
    {
        return $this->belongsToMany(KajianPoster::class, 'kajian_poster_categories');
    }

    // Scope untuk kategori aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}