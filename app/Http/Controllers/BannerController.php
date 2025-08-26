<?php

namespace App\Http\Controllers;

use App\Helpers\AlertHelper;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{

    public function index()
    {
        $banners = Banner::all();
        return view('dashboard.banner', ['data' => $banners]);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|in:kajian akbar/dauroh,kajian rutin,event,promosi,poster islami,social,donasi',
            'banners' => 'required|image|mimes:jpeg,png,jpg,gif|max:5048',
        ]);

        if ($request->hasFile('banners')) {
            $file = $request->file('banners');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/banners'), $filename);

            Banner::create([
                'judul' => $validated['judul'],
                'kategori' => $validated['kategori'],
                'banners' => $filename,
            ]);
        }

        return redirect()->route('banner.index')->with(AlertHelper::success('Banner berhasil ditambahkan', 'Success'));
    }


    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|in:kajian akbar/dauroh,kajian rutin,event,promosi,poster islami,social,donasi',
            'banners' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5048',
        ]);

        $data = [
            'judul' => $validated['judul'],
            'kategori' => $validated['kategori'],
        ];

        if ($request->hasFile('banners')) {
            if ($banner->banners && Storage::exists('public/uploads/banner/' . $banner->banners)) {
                Storage::delete('public/uploads/banner/' . $banner->banners);
            }

            $file = $request->file('banners');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/banners'), $filename);

            $data['banners'] = $filename;
        }

        $banner->update($data);

        return redirect()->route('banner.index')->with(AlertHelper::success('Banner berhasil diperbarui', 'Success'));
    }

    
    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);

        if ($banner->banners && Storage::exists('public/uploads/banner/' . $banner->banners)) {
            Storage::delete('public/uploads/banner/' . $banner->banners);
        }

        $banner->delete();

        return redirect()->route('banner.index')->with(AlertHelper::success('Banner berhasil dihapus', 'Success'));
    }
}