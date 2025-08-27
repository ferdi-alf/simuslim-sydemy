<?php

namespace App\Http\Controllers;

use App\Models\Ustadz;
use Illuminate\Http\Request;
use App\Helpers\AlertHelper;

class UstadzController extends Controller
{
    public function index()
    {
        $data = Ustadz::all();
        return view('dashboard.ustadz', compact('data'));
    }

    public function getAllUstadz()
    {
        try {
            $ustadz = Ustadz::withCount(['kajianRekamans', 'jadwalKajians'])
                ->select('id', 'nama_lengkap', 'alamat', 'riwayat_pendidikan', 'youtube', 'instagram', 'tiktok')
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'nama_lengkap' => $item->nama_lengkap,
                        'alamat' => $item->alamat,
                        'riwayat_pendidikan' => $item->riwayat_pendidikan,
                        'social_media' => [
                            'youtube' => $item->youtube,
                            'instagram' => $item->instagram,
                            'tiktok' => $item->tiktok
                        ],
                        'statistics' => [
                            'total_kajian_rekaman' => $item->kajian_rekamans_count,
                            'total_jadwal_kajian' => $item->jadwal_kajians_count
                        ]
                    ];
                });

            return response()->json([
                'status' => 'success',
                'message' => 'Data ustadz berhasil diambil',
                'data' => $ustadz
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data ustadz',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap'       => 'required|string|max:255',
            'alamat'             => 'nullable|string|max:255',
            'riwayat_pendidikan' => 'nullable|string|max:500',
            'youtube'            => 'nullable|url',
            'instagram'          => 'nullable|url',
            'tiktok'             => 'nullable|url',
        ]);

        try {
            Ustadz::create($request->all());
            AlertHelper::success('Berhasil menambahkan ustadz ' . $request->nama_lengkap, 'Success');
        } catch (\Throwable $th) {
            AlertHelper::error('Gagal menambahkan ustadz: ' . $th->getMessage(), 'Error');
        }

        return redirect()->route('ustadz.index');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_lengkap'       => 'required|string|max:255',
            'alamat'             => 'nullable|string|max:255',
            'riwayat_pendidikan' => 'nullable|string|max:500',
            'youtube'            => 'nullable|url',
            'instagram'          => 'nullable|url',
            'tiktok'             => 'nullable|url',
        ]);

        try {
            $ustadz = Ustadz::findOrFail($id);
            $ustadz->update($request->all());

            AlertHelper::success('Berhasil mengupdate ustadz ' . $request->nama_lengkap, 'Success');
        } catch (\Throwable $th) {
            AlertHelper::error('Gagal mengupdate ustadz: ' . $th->getMessage(), 'Error');
        }

        return redirect()->route('ustadz.index');
    }

    public function destroy($id)
    {
        try {
            $ustadz = Ustadz::findOrFail($id);
            $ustadz->delete();

            AlertHelper::success('Berhasil menghapus ustadz ' . $ustadz->nama_lengkap, 'Success');
        } catch (\Throwable $th) {
            AlertHelper::error('Gagal menghapus ustadz: ' . $th->getMessage(), 'Error');
        }

        return redirect()->route('ustadz.index');
    }
}
