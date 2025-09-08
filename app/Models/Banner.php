<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'banners',
        'judul',
        'kategori',
        'deskripsi',
    ];

    public function getBannerHtmlAttribute()
    {
        $defaultBanner = 'images/default-banner.jpg';
        $bannerPath = $this->banners ? 'uploads/banners/' . $this->banners : $defaultBanner;

        $judulParts = explode(' ', trim($this->judul));
        $initials = '';
        foreach ($judulParts as $part) {
            if (!empty($part)) {
                $initials .= strtoupper(substr($part, 0, 1));
            }
            if (strlen($initials) >= 2) break;
        }
        if (empty($initials)) {
            $initials = 'B';
        }

        if ($this->banners && file_exists(public_path($bannerPath))) {
            return '<div class="flex items-center" data-modal-target="poster-modal-' . $this->id . '"
                         data-modal-toggle="poster-modal-' . $this->id . '">
                        <div class="w-16 h-16 overflow-hidden  group-hover:ring-indigo-500 bg-gray-300 flex items-center justify-center relative">
                            <img src="' . asset($bannerPath) . '" 
                                 alt="' . htmlspecialchars($this->judul) . '" 
                                 class="w-full h-full object-cover cursor-pointer transition-all duration-300"
                                 onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'flex\';">
                            <div class="absolute inset-0 bg-blue-500 text-white text-xs font-semibold rounded-sm hidden items-center justify-center">
                                ' . $initials . '
                            </div>
                        </div>
                    </div>';
        } else {
            return '<div class="flex items-center">
                        <div class="w-16 h-16 overflow-hidden bg-blue-500 text-white text-xs font-semibold flex items-center justify-center">
                            ' . $initials . '
                        </div>
                    </div>';
        }
    }

    public function getShortDeskripsiAttribute()
    {
        if (strlen($this->deskripsi) > 50) {
            return '<span>' . substr($this->deskripsi, 0, 50) . '...</span> 
            <a href="#poster-modal-' . $this->id . '" 
               data-modal-target="poster-modal-' . $this->id . '" 
               data-modal-toggle="poster-modal-' . $this->id . '" 
               class="text-indigo-600 hover:underline">
               Read More
            </a>';
        }
        return $this->deskripsi;
    }
}
