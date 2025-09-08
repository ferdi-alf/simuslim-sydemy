<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class PosterDakwah extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'poster',
    ];

    public function getPosterHtmlAttribute()
    {
        $posterPath = $this->poster ? 'uploads/posters/' . $this->poster : null;

        if ($posterPath && file_exists(public_path($posterPath))) {
            return new HtmlString(
                '<div class="flex items-center" data-modal-target="poster-modal-' . $this->id . '"
                      data-modal-toggle="poster-modal-' . $this->id . '">
                 <div class="w-16 h-16 overflow-hidden bg-gray-200 flex items-center justify-center relative">
                     <img src="' . asset($posterPath) . '" 
                          alt="' . htmlspecialchars($this->judul) . '" 
                          class="w-full h-full object-contain cursor-pointer transition-all duration-300">
                 </div>
             </div>'
            );
        }

        return new HtmlString(
            '<div class="flex items-center">
             <div class="w-16 h-16 overflow-hidden bg-gray-400 text-white text-xs font-semibold flex items-center justify-center">
                 ' . strtoupper(substr($this->judul, 0, 2)) . '
             </div>
         </div>'
        );
    }
}
