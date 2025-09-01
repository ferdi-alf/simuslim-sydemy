<?php

namespace App\Http\Controllers;

use App\Helpers\AlertHelper;
use App\Models\Masjid;
use Illuminate\Http\Request;

class MasjidController extends Controller
{
    public function index() {
        $data = Masjid::select('id', 'nama', 'alamat', 'maps')->get();
        return view('dashboard.masjid', compact('data'));
    }

    public function getAllMasjid()
    {
        try {
            $masjid = Masjid::with(['kajianPosters' => function($query) {
                $query->select('id', 'masjid_id', 'judul', 'poster');
            }])
            ->select('id', 'nama', 'alamat', 'maps', 'channels')
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'nama' => $item->nama,
                    'alamat' => $item->alamat,
                    'maps' => $item->maps,
                    'channels' => $item->channels,
                    'total_kajian' => $item->kajianPosters->count(),
                    'kajian_posters' => $item->kajianPosters->map(function($kajian) {
                        return [
                            'id' => $kajian->id,
                            'judul' => $kajian->judul,
                            'poster_url' => $kajian->poster ? asset('uploads/kajian-poster/' . $kajian->poster) : null,
                            // 'deskripsi' => $kajian->deskripsi
                        ];
                    })
                ];
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Data masjid berhasil diambil',
                'data' => $masjid
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data masjid',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'maps' => 'required|string|max:500',
        ]);

        try {
            Masjid::create([
                'nama'   => $request->nama,
                'alamat' => $request->alamat,
                'maps'   => $request->maps,
            ]);

            AlertHelper::success('Berhasil menambahkan masjid ' . $request->nama, 'Success');
        } catch (\Throwable $th) {
            AlertHelper::error('Gagal menambahkan data: ' . $th->getMessage(), 'Error');
        }

        return redirect()->route('masjid.index');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'maps' => 'required|string|max:500',
        ]);

        try {
            $masjid = Masjid::findOrFail($id);

            $masjid->update([
                'nama'   => $request->nama,
                'alamat' => $request->alamat,
                'maps'   => $request->maps,
            ]);

            AlertHelper::success('Berhasil mengupdate masjid ' . $request->nama, 'Success');
        } catch (\Throwable $th) {
            AlertHelper::error('Gagal mengupdate data: ' . $th->getMessage(), 'Error');
        }

        return redirect()->route('masjid.index');
    }

    public function destroy($id)
    {
        try {
            $masjid = Masjid::findOrFail($id);
            $masjid->delete();

            AlertHelper::success('Berhasil menghapus masjid ' . $masjid->nama, 'Success');
        } catch (\Throwable $th) {
            AlertHelper::error('Gagal menghapus data: ' . $th->getMessage(), 'Error');
        }

        return redirect()->route('masjid.index');
    }
}
