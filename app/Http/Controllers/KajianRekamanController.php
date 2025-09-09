<?php

namespace App\Http\Controllers;

use App\Helpers\AlertHelper;
use App\Models\JadwalKajian;
use App\Models\KajianPoster;
use App\Models\KajianRekaman;
use App\Models\Ustadz;
use Illuminate\Console\View\Components\Alert;
use Illuminate\Http\Request;

class KajianRekamanController extends Controller
{
    public function index()
    {
        // Data kajian rekaman dari kajian yang tidak diarsipkan
        $data = KajianRekaman::with(['ustadzs', 'jadwalKajian.kajianPoster'])
            ->whereHas('jadwalKajian.kajianPoster', function($query) {
                $query->where('is_archive', false);
            })
            ->get();

        $ustadzOptions = Ustadz::all()->map(function ($ustadz) {
            return [
                'value' => $ustadz->id,
                'label' => $ustadz->nama_lengkap
            ];
        });

        // Hanya kajian poster yang tidak diarsipkan
        $kajianPosters = KajianPoster::with(['jadwalKajians' => function($query) {
            $query->where('status', JadwalKajian::STATUS_SELESAI);
        }])
        ->where('is_archive', false)
        ->get();

        return view('dashboard.rekaman-kajian', compact('data', 'ustadzOptions', 'kajianPosters'));
    }

    public function getJadwalByKajian($kajianId)
    {
        try {
            $kajianPoster = KajianPoster::where('id', $kajianId)
                ->where('is_archive', false)
                ->first();

            if (!$kajianPoster) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kajian poster tidak ditemukan atau sudah diarsipkan'
                ], 404);
            }

            $jadwalKajians = JadwalKajian::where('kajian_id', $kajianId)
                ->where('status', JadwalKajian::STATUS_SELESAI)
                ->select('id', 'tanggal', 'jam_mulai', 'jam_selesai', 'hari', 'diperuntukan')
                ->get()
                ->map(function($jadwal) {
                    return [
                        'id' => $jadwal->id,
                        'label' => $jadwal->tanggal . ' - ' . $jadwal->hari . ' (' . $jadwal->jam_mulai . ' - ' . $jadwal->jam_selesai . ') - ' . $jadwal->diperuntukan,
                        'value' => $jadwal->id
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $jadwalKajians
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil jadwal kajian',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAllKajianRekaman()
    {
        try {
            $kajianRekaman = KajianRekaman::with([
                'ustadzs:id,nama_lengkap',
                'jadwalKajian.kajianPoster:id,judul,is_archive'
            ])
            ->whereHas('jadwalKajian.kajianPoster', function($query) {
                $query->where('is_archive', false);
            })
            ->select('id', 'judul', 'link as video_url', 'kategori', 'jadwal_kajian_id')
            ->get()
            ->map(function($rekaman) {
                return [
                    'id' => $rekaman->id,
                    'judul' => $rekaman->judul,
                    'video_url' => $rekaman->video_url,
                    'kategori' => $rekaman->kategori,
                    'jadwal_kajian' => $rekaman->jadwalKajian ? [
                        'id' => $rekaman->jadwalKajian->id,
                        'tanggal' => $rekaman->jadwalKajian->tanggal,
                        'kajian_poster' => $rekaman->jadwalKajian->kajianPoster->judul ?? null
                    ] : null,
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
            'jadwal_kajian_id' => 'required|exists:jadwal_kajians,id',
            'ustadz_ids' => 'required|array|min:1',
        ]);

        $jadwalKajian = JadwalKajian::with('kajianPoster')
            ->where('id', $request->jadwal_kajian_id)
            ->first();

        if (!$jadwalKajian || $jadwalKajian->kajianPoster->is_archive) {
            return redirect()->back()
                ->withErrors(['jadwal_kajian_id' => 'Jadwal kajian tidak valid atau berasal dari kajian yang sudah diarsipkan'])
                ->withInput();
        }

        $kajianRekaman = KajianRekaman::create([
            'judul' => $request->judul,
            'kitab' => $request->kitab,
            'kategori' => $request->kategori,
            'link' => $request->link,
            'jadwal_kajian_id' => $request->jadwal_kajian_id,
        ]);

        if ($request->ustadz_ids) {
            $ustadzIds = [];
            foreach ($request->ustadz_ids as $ustadzData) {
                $ustadzData = trim($ustadzData);
                if (is_numeric($ustadzData) && Ustadz::find($ustadzData)) {
                    $ustadzIds[] = $ustadzData;
                } else {
                    $existingUstadz = Ustadz::where('nama_lengkap', $ustadzData)->first();
                    if ($existingUstadz) {
                        $ustadzIds[] = $existingUstadz->id;
                    } else {
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
            'jadwal_kajian_id' => 'required|exists:jadwal_kajians,id',
            'ustadz_ids' => 'required|array|min:1',
        ]);

        $jadwalKajian = JadwalKajian::with('kajianPoster')
            ->where('id', $request->jadwal_kajian_id)
            ->first();

        if (!$jadwalKajian || $jadwalKajian->kajianPoster->is_archive) {
            return redirect()->back()
                ->withErrors(['jadwal_kajian_id' => 'Jadwal kajian tidak valid atau berasal dari kajian yang sudah diarsipkan'])
                ->withInput();
        }

        $kajianRekaman = KajianRekaman::findOrFail($id);
        
        $kajianRekaman->update([
            'judul' => $request->judul,
            'kitab' => $request->kitab,
            'kategori' => $request->kategori,
            'link' => $request->link,
            'jadwal_kajian_id' => $request->jadwal_kajian_id,
        ]);

        if ($request->ustadz_ids) {
            $ustadzIds = [];
            foreach ($request->ustadz_ids as $ustadzData) {
                $ustadzData = trim($ustadzData);
                if (is_numeric($ustadzData) && Ustadz::find($ustadzData)) {
                    $ustadzIds[] = $ustadzData;
                } else {
                    $existingUstadz = Ustadz::where('nama_lengkap', $ustadzData)->first();
                    if ($existingUstadz) {
                        $ustadzIds[] = $existingUstadz->id;
                    } else {
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
        $kajianRekaman->ustadzs()->detach(); 
        $kajianRekaman->delete();

        return redirect()->route('kajian-rekaman.index')->with(AlertHelper::success('Kajian rekaman berhasil dihapus', 'Success'));
    }
}