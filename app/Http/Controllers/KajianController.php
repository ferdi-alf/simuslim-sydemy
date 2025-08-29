<?php

namespace App\Http\Controllers;

use App\Helpers\AlertHelper;
use App\Models\Category;
use App\Models\KajianPoster;
use App\Models\JadwalKajian;
use App\Models\Ustadz;
use App\Models\Masjid;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class KajianController extends Controller
{
    public function index() {
    $kajians = KajianPoster::with(['masjid', 'jadwalKajians.ustadzs', 'categories'])
        ->orderBy('created_at', 'desc')
        ->get();

    $masjids = Masjid::orderBy('nama')->get();
    
    $ustadzs = Ustadz::orderBy('nama_lengkap')->get();
    $ustadzOptions = $ustadzs->map(function ($ustadz) {
        return [
            'value' => $ustadz->id,
            'label' => $ustadz->nama_lengkap
        ];
    })->toArray();

    $categories = Category::active()->orderBy('nama')->get();
     $categoriesOptions = $categories->map(function ($categorie) {
        return [
            'value' => $categorie->id,
            'label' => $categorie->nama
        ];
    })->toArray();

    return view('dashboard.kajian', compact('kajians', 'masjids', 'ustadzOptions', 'categoriesOptions'));
    }

    public function getAllKajianPosters()
    {
        try {
            $kajianPosters = KajianPoster::with([
                'masjid:id,nama,alamat',
                'jadwalKajians' => function($query) {
                    $query->with(['ustadzs:id,nama_lengkap'])
                        ->select('id', 'kajian_id', 'jam_mulai', 'jam_selesai', 'tanggal', 'hari', 'status', 'diperuntukan');
                }
            ])
            ->select('id', 'masjid_id', 'judul', 'poster')
            ->get()
            ->map(function($kajian) {
                return [
                    'id' => $kajian->id,
                    'judul' => $kajian->judul,
                    'poster_url' => $kajian->poster ? asset('uploads/poster/' . $kajian->poster) : null,
                    'masjid' => [
                        'id' => $kajian->masjid->id ?? null,
                        'nama' => $kajian->masjid->nama ?? null,
                        'alamat' => $kajian->masjid->alamat ?? null
                    ],
                    'total_jadwal' => $kajian->jadwalKajians->count(),
                    'jadwal_kajian' => $kajian->jadwalKajians->map(function($jadwal) {
                        return [
                            'id' => $jadwal->id,
                            'jam_mulai' => $jadwal->jam_mulai,
                            'jam_selesai' => $jadwal->jam_selesai,
                            'tanggal' => $jadwal->tanggal,
                            'hari' => $jadwal->hari,
                            'status' => $jadwal->status,
                            'diperuntukan' => $jadwal->diperuntukan,
                            'ustadz' => $jadwal->ustadzs->map(function($ustadz) {
                                return [
                                    'id' => $ustadz->id,
                                    'nama_lengkap' => $ustadz->nama_lengkap
                                ];
                            })
                        ];
                    })
                ];
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Data kajian poster berhasil diambil',
                'data' => $kajianPosters
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data kajian poster',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAllJadwalKajian()
    {
        try {
            $jadwalKajian = JadwalKajian::with([
                'kajian:id,judul,poster',
                'ustadzs:id,nama_lengkap'
            ])
            ->select('id', 'kajian_id', 'jam_mulai', 'jam_selesai', 'tanggal', 'hari', 'status', 'diperuntukan')
            ->get()
            ->map(function($jadwal) {
                return [
                    'id' => $jadwal->id,
                    'jam_mulai' => $jadwal->jam_mulai,
                    'jam_selesai' => $jadwal->jam_selesai,
                    'tanggal' => $jadwal->tanggal,
                    'hari' => $jadwal->hari,
                    'status' => $jadwal->status,
                    'diperuntukan' => $jadwal->diperuntukan,
                    'kajian_poster' => [
                        'id' => $jadwal->kajian->id ?? null,
                        'judul' => $jadwal->kajian->judul ?? null,
                        'poster_url' => $jadwal->kajian->poster ? asset('uploads/poster/' . $jadwal->kajian->poster) : null
                    ],
                    'ustadz' => $jadwal->ustadzs->map(function($ustadz) {
                        return [
                            'id' => $ustadz->id,
                            'nama_lengkap' => $ustadz->nama_lengkap
                        ];
                    })
                ];
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Data jadwal kajian berhasil diambil',
                'data' => $jadwalKajian
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data jadwal kajian',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function store(Request $request) {
        $request->validate([
            'judul' => 'required|string|max:255',
            'category_ids' => 'required|array|min:1', 
            'category_ids.*' => 'string|max:255', 
            'jenis' => ['required', Rule::in(['rutin', 'akbar/dauroh'])],
            'poster' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'penyelenggara' => 'required|string|max:255',
            'masjid_id' => 'nullable|exists:masjids,id',
            'alamat_manual' => 'nullable|string',
        ]);

        if (empty($request->masjid_id) && empty($request->alamat_manual)) {
            return back()->withErrors(['lokasi' => 'Pilih salah satu lokasi: Masjid atau Alamat Manual']);
        }

        if (!empty($request->masjid_id) && !empty($request->alamat_manual)) {
            return back()->withErrors(['lokasi' => 'Pilih hanya salah satu lokasi: Masjid atau Alamat Manual']);
        }

        $posterFileName = null;
        if ($request->hasFile('poster')) {
            $file = $request->file('poster');
            $posterFileName = uniqid('poster_') . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/poster'), $posterFileName);
        }

        $kajian = KajianPoster::create([
            'masjid_id' => $request->masjid_id,
            'judul' => $request->judul,
            'jenis' => $request->jenis,
            'poster' => $posterFileName,
            'penyelenggara' => $request->penyelenggara,
            'alamat_manual' => $request->alamat_manual,
        ]);

        if ($request->category_ids) {
            $categoryIds = [];
            foreach ($request->category_ids as $categoryData) {
                $categoryData = trim($categoryData);
                
                if (is_numeric($categoryData) && Category::find($categoryData)) {
                    $categoryIds[] = $categoryData;
                } else {
                    $existingCategory = Category::where('nama', $categoryData)->first();
                    if ($existingCategory) {
                        $categoryIds[] = $existingCategory->id;
                    } else {
                        $newCategory = Category::create([
                            'nama' => $categoryData,
                            'deskripsi' => null,
                            'is_active' => true,
                        ]);
                        $categoryIds[] = $newCategory->id;
                    }
                }
            }
            $kajian->categories()->attach($categoryIds);
        }

        return redirect()->route('kajian.index')->with(AlertHelper::success('Kajian berhasil ditambahkan', 'success'));
    }


    public function update(Request $request, $id) {
        $kajian = KajianPoster::findOrFail($id);

        $request->validate([
            'judul' => 'required|string|max:255',
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'string|max:255',
            'jenis' => ['required', Rule::in(['rutin', 'akbar/dauroh'])],
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'penyelenggara' => 'required|string|max:255',
            'masjid_id' => 'nullable|exists:masjids,id',
            'alamat_manual' => 'nullable|string',
        ]);

        if (empty($request->masjid_id) && empty($request->alamat_manual)) {
            return back()->withErrors(['lokasi' => 'Pilih salah satu lokasi: Masjid atau Alamat Manual']);
        }

        if (!empty($request->masjid_id) && !empty($request->alamat_manual)) {
            return back()->withErrors(['lokasi' => 'Pilih hanya salah satu lokasi: Masjid atau Alamat Manual']);
        }

        $updateData = [
            'masjid_id' => $request->masjid_id,
            'judul' => $request->judul,
            'jenis' => $request->jenis,
            'penyelenggara' => $request->penyelenggara,
            'alamat_manual' => $request->alamat_manual,
        ];

        if ($request->hasFile('poster')) {
            if ($kajian->poster && file_exists(public_path('uploads/poster/' . $kajian->poster))) {
                unlink(public_path('uploads/poster/' . $kajian->poster));
            }
            $file = $request->file('poster');
            $posterFileName = uniqid('poster_') . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/poster'), $posterFileName);
            $updateData['poster'] = $posterFileName;
        }

        $kajian->update($updateData);

        // Update categories
        if ($request->category_ids) {
            $categoryIds = [];
            foreach ($request->category_ids as $categoryData) {
                $categoryData = trim($categoryData);
                
                if (is_numeric($categoryData) && Category::find($categoryData)) {
                    $categoryIds[] = $categoryData;
                } else {
                    $existingCategory = Category::where('nama', $categoryData)->first();
                    if ($existingCategory) {
                        $categoryIds[] = $existingCategory->id;
                    } else {
                        $newCategory = Category::create([
                            'nama' => $categoryData,
                            'deskripsi' => null,
                            'is_active' => true,
                        ]);
                        $categoryIds[] = $newCategory->id;
                    }
                }
            }
            // Sync akan menghapus relasi lama dan menambah yang baru
            $kajian->categories()->sync($categoryIds);
        }

        return redirect()->route('kajian.index')->with(AlertHelper::success('Kajian berhasil diperbarui', 'Success'));
    }

    public function destroy($id) {
        $kajian = KajianPoster::findOrFail($id);

        if ($kajian->poster) {
            Storage::disk('public')->delete($kajian->poster);
        }

        $kajian->delete();

        return redirect()->route('kajian.index')->with('success', 'Kajian berhasil dihapus');
    }

    public function storeJadwal(Request $request) {
        $request->validate([
            'kajian_id' => 'required|exists:kajian_posters,id',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'tanggal' => 'required|date',
            'status' => ['required', Rule::in(['belum dimulai', 'berjalan', 'selesai', 'liburkan'])],
            'diperuntukan' => ['required', Rule::in(['semua kaum muslim', 'ikhwan', 'akhwat'])],
            'ustadz_ids' => 'nullable|array',
            'ustadz_ids.*' => 'nullable|string',
        ]);

        $dayMap = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu',
        ];

        // Get the day name from the tanggal input
        $tanggal = Carbon::parse($request->tanggal);
        $hari = $dayMap[$tanggal->format('l')];

        $jadwal = JadwalKajian::create([
            'kajian_id' => $request->kajian_id,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'tanggal' => $request->tanggal,
            'hari' => $hari,
            'status' => $request->status,
            'diperuntukan' => $request->diperuntukan,
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
            $jadwal->ustadzs()->attach($ustadzIds);
        }

        return redirect()->back()->with(AlertHelper::success('Berhasil menambahkan jadwal kajian', 'Success'));
    }

    public function updateJadwal(Request $request, $id) {
        $jadwal = JadwalKajian::findOrFail($id);

        $request->validate([
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'tanggal' => 'required|date',
            'status' => ['required', Rule::in(['belum dimulai', 'berjalan', 'selesai', 'liburkan'])],
            'diperuntukan' => ['required', Rule::in(['semua kaum muslim', 'ikhwan', 'akhwat'])],
            'ustadz_ids' => 'nullable|array',
            'ustadz_ids.*' => 'nullable|string',
        ]);
        $dayMap = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu',
        ];

        $tanggal = Carbon::parse($request->tanggal);
        $hari = $dayMap[$tanggal->format('l')];

        $jadwal->update([
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'tanggal' => $request->tanggal,
            'hari' => $hari,
            'status' => $request->status,
            'diperuntukan' => $request->diperuntukan,
        ]);

        $jadwal->ustadzs()->detach();

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
            $jadwal->ustadzs()->attach($ustadzIds);
        }

        return redirect()->back()->with(AlertHelper::success('Berhasil memperbarui jadwal kajian', 'Success'));
    }

    public function destroyJadwal($id) {
        $jadwal = JadwalKajian::findOrFail($id);
        $jadwal->ustadzs()->detach();
        $jadwal->delete();

        return redirect()->route('kajian.index')->with(AlertHelper::success('Berhasil mnghapus data kajian', 'Success'));
    }

    public function searchUstadz(Request $request) {
        $query = $request->get('q', '');
        
        $ustadzs = Ustadz::where('nama_lengkap', 'like', '%' . $query . '%')
            ->orderBy('nama_lengkap')
            ->limit(10)
            ->get()
            ->map(function ($ustadz) {
                return [
                    'value' => $ustadz->id,
                    'label' => $ustadz->nama_lengkap
                ];
            });

        return response()->json($ustadzs);
    }
}