<?php

namespace App\Http\Controllers;

use App\Helpers\AlertHelper;
use App\Models\Donasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DonasiController extends Controller
{

    public function index()
    {
        $donasi = Donasi::all();
        return view('dashboard.donasi', ['data' => $donasi]);
    }

    public function getAllDonasi()
    {
        try {
            $donasi = Donasi::select('id', 'judul', 'poster', 'nama_pic', 'keperluan', 'nominal', 'no_rekening', 'keterangan', 'bank', 'nama_pemilik_rekening')
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'judul' => $item->judul,
                        'nama_pic' => $item->nama_pic,
                        'keperluan' => $item->keperluan,
                        'bank' => $item->bank,
                        'nama_pemilik_rekening' => $item->nama_pemilik_rekening,
                        'nominal' => $item->nominal,
                        'formatted_nominal' => $item->getFormattedHargaAttribute(),
                        'no_rekening' => $item->no_rekening,
                        'keterangan' => $item->keterangan,
                        'poster_url' => $item->poster ? asset('uploads/poster/' . $item->poster) : asset('images/default-poster.jpg'),
                        'poster_html' => $item->getPosterHtmlAttribute(),
                        'initials' => $this->generateInitials($item->judul)
                    ];
                });

            return response()->json([
                'status' => 'success',
                'message' => 'Data donasi berhasil diambil',
                'data' => $donasi
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data donasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function generateInitials($title)
    {
        $parts = explode(' ', trim($title));
        $initials = '';
        foreach ($parts as $part) {
            if (!empty($part)) {
                $initials .= strtoupper(substr($part, 0, 1));
            }
            if (strlen($initials) >= 2) break;
        }
        return empty($initials) ? 'D' : $initials;
    }



    private function handleFileUpload($file, $oldFileName = null)
    {
        if ($oldFileName && file_exists(public_path('uploads/poster/' . $oldFileName))) {
            unlink(public_path('uploads/poster/' . $oldFileName));
        }
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/poster'), $filename);
        
        return $filename;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'poster' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'nama_pic' => 'required|string|max:255',
            'bank' => 'required|string|max:255',
            'nama_pemilik_rekening' => 'required|string|max:255',
            'keperluan' => 'required|string|max:255',
            'nominal_numeric' => 'nullable|numeric|min:0', // Gunakan ini!
            'no_rekening' => 'required|string|max:255',
            'keterangan' => 'required|string',
        ]);

        $posterFileName = null;
        if ($request->hasFile('poster')) {
            $posterFileName = $this->handleFileUpload($request->file('poster'));
        }

        Donasi::create([
            'judul' => $validated['judul'],
            'poster' => $posterFileName,
            'nama_pic' => $validated['nama_pic'],
            'keperluan' => $validated['keperluan'],
            'nama_pemilik_rekening' => $validated['nama_pemilik_rekening'],
            'bank' => $validated['bank'],
            'nominal' => $request->nominal_numeric, // Gunakan yang numeric!
            'no_rekening' => $validated['no_rekening'],
            'keterangan' => $validated['keterangan'],
        ]);

        return redirect()->route('donasi.index')
            ->with(AlertHelper::success('Donasi berhasil ditambahkan', 'Success'));
    }

    public function update(Request $request, $id)
    {
        $donasi = Donasi::findOrFail($id);
        
        // LANGKAH 1: Deteksi perubahan dan ambil nilai yang benar
        $nominalDisplayValue = $request->input('nominal', ''); // "550.000"
        $nominalHiddenValue = $request->input('nominal_numeric', ''); // "150000"
        
        // LANGKAH 2: Bersihkan nilai display untuk mendapat angka murni
        $cleanDisplayValue = preg_replace('/[^\d]/', '', $nominalDisplayValue); // "550000"
        
        // LANGKAH 3: Gunakan display value jika berbeda dari hidden value
        // (artinya user sudah edit tapi hidden field belum terupdate)
        $finalNominalValue = $cleanDisplayValue;
        
        // Debug untuk memastikan
        Log::info('Nominal Update Debug', [
            'display_value' => $nominalDisplayValue,
            'hidden_value' => $nominalHiddenValue,
            'clean_display' => $cleanDisplayValue,
            'final_value' => $finalNominalValue,
            'old_database_value' => $donasi->nominal
        ]);
        
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'nama_pic' => 'required|string|max:255',
            'keperluan' => 'required|string|max:255',
            'bank' => 'required|string|max:255',
            'nama_pemilik_rekening' => 'required|string|max:255',
            'nominal' => 'nullable|string', // Validasi sebagai string
            'no_rekening' => 'required|string|max:255',
            'keterangan' => 'required|string',
        ]);

        $posterFileName = $donasi->poster;
        if ($request->hasFile('poster')) {
            $posterFileName = $this->handleFileUpload(
                $request->file('poster'),
                $donasi->poster
            );
        }

        $donasi->update([
            'judul' => $validated['judul'],
            'poster' => $posterFileName,
            'nama_pic' => $validated['nama_pic'],
            'bank' => $validated['bank'],
            'nama_pemilik_rekening' => $validated['nama_pemilik_rekening'],
            'keperluan' => $validated['keperluan'],
            'nominal' => $finalNominalValue, // Gunakan nilai yang sudah dibersihkan
            'no_rekening' => $validated['no_rekening'],
            'keterangan' => $validated['keterangan'],
        ]);

        return redirect()->route('donasi.index')
            ->with(AlertHelper::success('Donasi berhasil diperbarui', 'Success'));
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
