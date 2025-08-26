<?php

namespace App\Http\Controllers;

use App\Helpers\AlertHelper;
use App\Models\Donasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DonasiController extends Controller
{

    public function index()
    {
        $donasi = Donasi::all();
        return view('dashboard.donasi', ['data' => $donasi]);
    }

    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'poster' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'nama_pic' => 'required|string|max:255',
            'keperluan' => 'required|string|max:255',
            'nominal' => 'nullable|numeric|min:0',
            'no_rekening' => 'required|string|max:255',
            'keterangan' => 'required|string',
        ]);
        

        if ($request->hasFile('poster')) {
            $file = $request->file('poster');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/poster'), $filename);


            $validated['poster'] = $filename;
            Donasi::create($validated);
        }

        return redirect()->route('donasi.index')->with(AlertHelper::success('Donasi berhasil di tambahkan', 'Success'));
    }

  
    public function update(Request $request, $id)
    {
        $donasi = Donasi::findOrFail($id);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'nama_pic' => 'required|string|max:255',
            'keperluan' => 'required|string|max:255',
            'nominal' => 'nullable|numeric|min:0',
            'no_rekening' => 'required|string|max:255',
            'keterangan' => 'required|string',
        ]);

        if ($request->hasFile('poster')) {
            if ($donasi->poster && Storage::exists('public/uploads/donasi/' . $donasi->poster)) {
                Storage::delete('public/uploads/donasi/' . $donasi->poster);
            }

            $file = $request->file('poster');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/poster'), $filename);

            $validated['poster'] = $filename;
        } else {
            $validated['poster'] = $donasi->poster; 
        }

        $donasi->update($validated);

        return redirect()->route('donasi.index')->with(AlertHelper::success('Donasi berhasil diperbarui', 'Success'));
    }

   
    public function destroy($id)
    {
        $donasi = Donasi::findOrFail($id);

        if ($donasi->poster && Storage::exists('public/uploads/donasi/' . $donasi->poster)) {
            Storage::delete('public/uploads/donasi/' . $donasi->poster);
        }

        $donasi->delete();

        return redirect()->route('donasi.index')->with(AlertHelper::success('Banner berhasil dihapus', 'Success'));
    }
}
