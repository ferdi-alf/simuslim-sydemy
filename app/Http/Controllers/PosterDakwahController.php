<?php

namespace App\Http\Controllers;

use App\Models\PosterDakwah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PosterDakwahController extends Controller
{
    public function index()
    {
        $data = PosterDakwah::latest()->get();
        return view('dashboard.poster', compact('data'));
    }

    public function getAllPosters()
    {
        try {
            $posters = PosterDakwah::select('id', 'judul', 'poster')
                ->get()
                ->map(function ($poster) {
                    return [
                        'id' => $poster->id,
                        'judul' => $poster->judul,
                        'poster_url' => $poster->poster
                            ? asset('uploads/posters/' . $poster->poster)
                            : asset('images/default-poster.jpg'),
                        'poster_html' => $poster->getPosterHtmlAttribute(),
                    ];
                });

            return response()->json([
                'status' => 'success',
                'message' => 'Data poster berhasil diambil',
                'data' => $posters
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data poster',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'poster' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $fileName = time() . '_' . $request->poster->getClientOriginalName();
        $request->poster->move(public_path('uploads/posters'), $fileName);

        PosterDakwah::create([
            'judul' => $request->judul,
            'poster' => $fileName,
        ]);

        return redirect()->back()->with('success', 'Poster berhasil ditambahkan!');
    }

    public function update(Request $request, PosterDakwah $posterDakwah)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = ['judul' => $request->judul];

        if ($request->hasFile('poster')) {
            // hapus file lama
            $oldFile = public_path('uploads/posters/' . $posterDakwah->poster);
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }

            $fileName = time() . '_' . $request->poster->getClientOriginalName();
            $request->poster->move(public_path('uploads/posters'), $fileName);
            $data['poster'] = $fileName;
        }

        $posterDakwah->update($data);

        return redirect()->back()->with('success', 'Poster berhasil diupdate!');
    }

    public function destroy(PosterDakwah $posterDakwah)
    {
        $filePath = public_path('uploads/posters/' . $posterDakwah->poster);
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $posterDakwah->delete();

        return redirect()->back()->with('success', 'Poster berhasil dihapus!');
    }
}
