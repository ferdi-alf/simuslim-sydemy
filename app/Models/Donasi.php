<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Donasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'poster',
        'nama_pic',
        'keperluan',
        'nominal',
        'no_rekening',
        'keterangan',
    ];

     public function getPosterHtmlAttribute()
    {
        $defaultPoster = 'images/default-poster.jpg';
        $posterPath = $this->poster ? 'uploads/poster/' . $this->poster : $defaultPoster;

        $judulParts = explode(' ', trim($this->judul));
        $initials = '';
        foreach ($judulParts as $part) {
            if (!empty($part)) {
                $initials .= strtoupper(substr($part, 0, 1));
            }
            if (strlen($initials) >= 2) break;
        }
        if (empty($initials)) {
            $initials = 'D'; 
        }

        if ($this->poster && file_exists(public_path($posterPath))) {
            return '<div class="flex items-center" data-modal-target="poster-modal-' . $this->id . '"
                         data-modal-toggle="poster-modal-' . $this->id . '">
                        <div class="w-16 h-16 overflow-hidden bg-gray-300 flex group-hover:ring-indigo-500 items-center justify-center relative">
                            <img src="' . asset($posterPath) . '" 
                                 alt="' . htmlspecialchars($this->judul) . '" 
                                 class="w-full h-full object-cover "
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

      public function getFormattedHargaAttribute()
    {
        return 'Rp ' . number_format($this->nominal, 0, ',', '.');
    }
}
