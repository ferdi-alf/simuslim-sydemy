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
