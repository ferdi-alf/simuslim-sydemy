<?php

namespace App\Http\Controllers;

use App\Helpers\AlertHelper;
use App\Models\KajianRekaman;
use App\Models\Ustadz;
use Illuminate\Console\View\Components\Alert;
use Illuminate\Http\Request;

class KajianRekamanController extends Controller
{
    public function index()
    {
        $data = KajianRekaman::with('ustadzs')->get();
        $ustadzOptions = Ustadz::all()->map(function ($ustadz) {
            return [
                'value' => $ustadz->id,
                'label' => $ustadz->nama_lengkap
            ];
        });

        return view('dashboard.rekaman-kajian', compact('data', 'ustadzOptions'));
    }

    public function getAllKajianRekaman()
    {
        try {
            $kajianRekaman = KajianRekaman::with([
                'ustadzs:id,nama_lengkap'
            ])
            ->select('id', 'judul', 'link as video_url', 'kategori')
            ->get()
            ->map(function($rekaman) {
                return [
                    'id' => $rekaman->id,
                    'judul' => $rekaman->judul,
                    'video_url' => $rekaman->video_url,
                    // 'thumbnail_url' => $rekaman->thumbnail ? asset('uploads/thumbnails/' . $rekaman->thumbnail) : null,
                    // 'durasi' => $rekaman->durasi,
                    'kategori' => $rekaman->kategori,
                    'total_ustadz' => $rekaman->ustadzs->count(),
                    'ustadz' => $rekaman->ustadzs->map(function($ustadz) {
                        return [
                            'id' => $ustadz->id,
                            'nama_lengkap' => $ustadz->nama_lengkap
                        ];
                    })
                ];
            });


            return response()->json([
                'status' => 'success',
                'message' => 'Data kajian rekaman berhasil diambil',
                'data' => $kajianRekaman
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data kajian rekaman',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'kitab' => 'nullable|string|max:255',
            'kategori' => 'required|in:video,audio',
            'link' => 'required|string|max:255',
            'ustadz_ids' => 'required|array|min:1',
        ]);

        $kajianRekaman = KajianRekaman::create([
            'judul' => $request->judul,
            'kitab' => $request->kitab,
            'kategori' => $request->kategori,
            'link' => $request->link,
        ]);

        // Handle ustadz relationship
        if ($request->ustadz_ids) {
            $ustadzIds = [];
            foreach ($request->ustadz_ids as $ustadzData) {
                $ustadzData = trim($ustadzData);
                if (is_numeric($ustadzData) && Ustadz::find($ustadzData)) {
                    $ustadzIds[] = $ustadzData;
                } else {
                    // Check if an Ustadz with this name already exists
                    $existingUstadz = Ustadz::where('nama_lengkap', $ustadzData)->first();
                    if ($existingUstadz) {
                        $ustadzIds[] = $existingUstadz->id;
                    } else {
                        // Create new Ustadz if no existing match is found
                        $newUstadz = Ustadz::create([
                            'nama_lengkap' => $ustadzData,
                            'alamat' => null,
                            'riwayat_pendidikan' => null,
                            'youtube' => null,
                            'instagram' => null,
                            'tiktok' => null,
                        ]);
                        $ustadzIds[] = $newUstadz->id;
                    }
                }
            }
            $kajianRekaman->ustadzs()->attach($ustadzIds);
        }

        return redirect()->route('kajian-rekaman.index')->with(AlertHelper::success('Kajian rekaman berhasil ditambahkan', 'Success'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'kitab' => 'nullable|string|max:255',
            'kategori' => 'required|in:video,audio',
            'link' => 'required|string|max:255',
            'ustadz_ids' => 'required|array|min:1',
        ]);

        $kajianRekaman = KajianRekaman::findOrFail($id);
        
        $kajianRekaman->update([
            'judul' => $request->judul,
            'kitab' => $request->kitab,
            'kategori' => $request->kategori,
            'link' => $request->link,
        ]);

        // Handle ustadz relationship
        if ($request->ustadz_ids) {
            $ustadzIds = [];
            foreach ($request->ustadz_ids as $ustadzData) {
                $ustadzData = trim($ustadzData);
                if (is_numeric($ustadzData) && Ustadz::find($ustadzData)) {
                    $ustadzIds[] = $ustadzData;
                } else {
                    // Check if an Ustadz with this name already exists
                    $existingUstadz = Ustadz::where('nama_lengkap', $ustadzData)->first();
                    if ($existingUstadz) {
                        $ustadzIds[] = $existingUstadz->id;
                    } else {
                        // Create new Ustadz if no existing match is found
                        $newUstadz = Ustadz::create([
                            'nama_lengkap' => $ustadzData,
                            'alamat' => null,
                            'riwayat_pendidikan' => null,
                            'youtube' => null,
                            'instagram' => null,
                            'tiktok' => null,
                        ]);
                        $ustadzIds[] = $newUstadz->id;
                    }
                }
            }
            $kajianRekaman->ustadzs()->sync($ustadzIds);
        }

        return redirect()->route('kajian-rekaman.index')->with(AlertHelper::success('Kajian rekaman berhasil diperbarui', 'Success'));
    }

    public function destroy($id)
    {
        $kajianRekaman = KajianRekaman::findOrFail($id);
        $kajianRekaman->ustadzs()->detach(); // Remove all relationships
        $kajianRekaman->delete();

        return redirect()->route('kajian-rekaman.index')->with(AlertHelper::success('Kajian rekaman berhasil dihapus', 'Success'));
    }
}