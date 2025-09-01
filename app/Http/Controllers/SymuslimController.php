<?php

namespace App\Http\Controllers;

use App\Models\Bacaan;
use App\Models\BacaanDetail;
use Illuminate\Http\Request;

class SymuslimController extends Controller
{
    public function index()
    {
        $bacaans = Bacaan::with('details')->get(); 
        return view('dashboard.symuslim', compact('bacaans'));
    }

    public function getAllBacaan()
    {
        try {
            $bacaan = Bacaan::with(['details:id,bacaan_id,arab,latin,terjemahan,sumber'])
                ->select('id', 'judul', 'type', 'deskripsi')
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'judul' => $item->judul,
                        'type' => $item->type,
                        'deskripsi' => $item->deskripsi,
                        'total_details' => $item->details->count(),
                        'details' => $item->details->map(function($detail) {
                            return [
                                'id' => $detail->id,
                                'arab' => $detail->arab,
                                'latin' => $detail->latin,
                                'terjemahan' => $detail->terjemahan,
                                'sumber' => $detail->sumber
                            ];
                        })
                    ];
                });

            return response()->json([
                'status' => 'success',
                'message' => 'Data bacaan berhasil diambil',
                'data' => $bacaan
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data bacaan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Method terpisah untuk bacaan detail
    public function getAllBacaanDetails()
    {
        try {
            $bacaanDetails = BacaanDetail::with(['bacaan:id,judul,type'])
                ->select('id', 'bacaan_id', 'arab', 'latin', 'terjemahan', 'sumber')
                ->get()
                ->map(function($detail) {
                    return [
                        'id' => $detail->id,
                        'arab' => $detail->arab,
                        'latin' => $detail->latin,
                        'terjemahan' => $detail->terjemahan,
                        'sumber' => $detail->sumber,
                        'bacaan' => [
                            'id' => $detail->bacaan->id ?? null,
                            'judul' => $detail->bacaan->judul ?? null,
                            'type' => $detail->bacaan->type ?? null
                        ]
                    ];
                });

            return response()->json([
                'status' => 'success',
                'message' => 'Data bacaan detail berhasil diambil',
                'data' => $bacaanDetails
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data bacaan detail',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'type' => 'required|in:doa,hadits,dzikir',
            'deskripsi' => 'nullable|string',
        ]);

        $bacaan = Bacaan::create($request->only(['judul', 'type', 'deskripsi']));

        return redirect()->route('symuslim.index', $bacaan->id)->with('success', 'Bacaan ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $bacaan = Bacaan::findOrFail($id);

        $request->validate([
            'judul' => 'required|string|max:255',
            'type' => 'required|in:doa,hadits,dzikir',
            'deskripsi' => 'nullable|string',
        ]);

        $bacaan->update($request->only(['judul', 'type', 'deskripsi']));

        return redirect()->route('symuslim.index', $bacaan->id)->with('success', 'Bacaan diupdate');
    }

    public function destroy($id)
    {
        Bacaan::findOrFail($id)->delete();
        return redirect()->route('symuslim.index')->with('success', 'Bacaan dihapus');
    }

    public function storeDetail(Request $request, $bacaanId)
    {
        $request->validate([
            'arab' => 'nullable|string',
            'latin' => 'nullable|string',
            'terjemahan' => 'nullable|string',
            'sumber' => 'nullable|string',
        ]);

        $bacaan = Bacaan::findOrFail($bacaanId);
        $bacaan->details()->create($request->only(['arab', 'latin', 'terjemahan', 'sumber']));

        return redirect()->route('symuslim.index', $bacaanId)->with('success', 'Detail bacaan ditambahkan');
    }

    public function updateDetail(Request $request, $bacaanId, $detailId)
    {
        $request->validate([
            'arab' => 'nullable|string',
            'latin' => 'nullable|string',
            'terjemahan' => 'nullable|string',
            'sumber' => 'nullable|string',
        ]);

        $detail = BacaanDetail::where('bacaan_id', $bacaanId)->findOrFail($detailId);
        $detail->update($request->only(['arab', 'latin', 'terjemahan', 'sumber']));

        return redirect()->route('symuslim.index', $bacaanId)->with('success', 'Detail bacaan diupdate');
    }

    public function destroyDetail($bacaanId, $detailId)
    {
        BacaanDetail::where('bacaan_id', $bacaanId)->findOrFail($detailId)->delete();
        return redirect()->route('symuslim.index', $bacaanId)->with('success', 'Detail bacaan dihapus');
    }
}