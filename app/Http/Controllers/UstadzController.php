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
