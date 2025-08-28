<?php
// app/Models/KajianPoster.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KajianPoster extends Model
{
    use HasFactory;

    protected $fillable = [
        'masjid_id',
        'judul',
        'jenis',
        'poster',
        'penyelenggara',
        'alamat_manual',
    ];

    public function masjid()
    {
        return $this->belongsTo(Masjid::class);
    }

    public function jadwalKajians()
    {
        return $this->hasMany(JadwalKajian::class, 'kajian_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'kajian_poster_categories');
    }

    public function getLokasiAttribute()
    {
        return $this->masjid ? $this->masjid->nama : $this->alamat_manual;
    }

    public function getCategoriesNamesAttribute()
    {
        return $this->categories->pluck('nama')->join(', ');
    }
}