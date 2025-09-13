<?php

namespace App\Http\Controllers;

use App\Helpers\AlertHelper;
use App\Models\Category;
use App\Models\KajianPoster;
use App\Models\JadwalKajian;
use App\Models\Ustadz;
use App\Models\Masjid;
use Carbon\Carbon;
use Illuminate\Console\View\Components\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class KajianController extends Controller
{
    public function index() 
    {
        $kajians = KajianPoster::with([
            'masjid', 
            'jadwalKajians' => fn($q) => $q->with('ustadzs')->orderBy('position', 'asc'),
            'categories'
        ])
        ->where('is_archive', false)
        ->where('is_draft', false) 
        ->orderBy('position', 'asc') 
        ->orderBy('created_at', 'desc')
        ->get();

        $kajianArchive = KajianPoster::with([
            'masjid', 
            'jadwalKajians' => fn($q) => $q->with('ustadzs')->orderBy('position', 'asc'),
            'categories'
        ])
        ->where('is_archive', true)
        ->orderBy('created_at', 'desc')
        ->get();

        $kajianDraft = KajianPoster::with([
            'masjid', 
            'jadwalKajians' => fn($q) => $q->with('ustadzs')->orderBy('position', 'asc'),
            'categories'
        ])
        ->where('is_draft', true)
        ->orderBy('created_at', 'desc')
        ->get();

        $masjids = Masjid::orderBy('nama')->get();
        $ustadzs = Ustadz::orderBy('nama_lengkap')->get();
        $ustadzOptions = $ustadzs->map(fn($u) => ['value'=>$u->id, 'label'=>$u->nama_lengkap])->toArray();

        $categories = Category::active()->orderBy('nama')->get();
        $categoriesOptions = $categories->map(fn($c) => ['value'=>$c->id, 'label'=>$c->nama])->toArray();

        return view('dashboard.kajian', compact(
            'kajians', 
            'kajianArchive', 
            'kajianDraft', 
            'masjids',
            'ustadzOptions',
            'categoriesOptions'
        ));
    }


    public function getAllKajianPosters()
    {
        try {
            $kajianPosters = KajianPoster::with([
                'masjid:id,nama,alamat,maps',
                'categories:id,nama', 
                'jadwalKajians' => fn($q) => $q->with(['ustadzs:id,nama_lengkap'])
                    ->select('id','kajian_id','jam_mulai','jam_selesai','tanggal','hari','status','diperuntukan', 'link','position')
                    ->orderBy('position', 'asc') 
            ])
            ->where('is_archive', false)
            ->where('is_draft', false) 
            ->orderBy('position', 'asc') 
            ->orderBy('created_at', 'desc')
            ->select('id','masjid_id','judul','jenis','poster','link','keterangan')
            ->get()

            ->map(function($kajian){
                $hariTerdekat = $this->getHariTerdekat($kajian->jadwalKajians);
                return [
                    'id' => $kajian->id,
                    'judul' => $kajian->judul,
                    'jenis' => $kajian->jenis,
                    'link' => $kajian->link,
                    'keterangan' => $kajian->keterangan,
                    'poster_url' => $kajian->poster ? asset('uploads/kajian-poster/'.$kajian->poster) : null,
                    'hari_terdekat' => $hariTerdekat, 
                    'masjid' => [
                        'id' => $kajian->masjid->id ?? null,
                        'nama' => $kajian->masjid->nama ?? null,
                        'alamat' => $kajian->masjid->alamat ?? null,
                        'maps' => $kajian->masjid->maps ?? null
                    ],
                    'kategori' => $kajian->categories->map(fn($c)=>[
                        'id' => $c->id,
                        'nama' => $c->nama
                    ]),
                    'total_jadwal' => $kajian->jadwalKajians->count(),
                    'jadwal_kajian' => $kajian->jadwalKajians->map(fn($j)=>[
                        'id'=>$j->id,
                        'jam_mulai'=>$j->jam_mulai,
                        'jam_selesai'=>$j->jam_selesai,
                        'tanggal'=>$j->tanggal,
                        'hari'=>$j->hari,
                        'status'=>$j->status,
                        'diperuntukan'=>$j->diperuntukan,
                        'link'=> $j->link,
                        'position'=> $j->position,
                        'ustadz'=>$j->ustadzs->map(fn($u)=>[
                            'id'=>$u->id,
                            'nama_lengkap'=>$u->nama_lengkap
                        ])
                    ])
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => $kajianPosters
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getAllJadwalKajian()
    {
        try {
            $jadwalKajian = JadwalKajian::with(['kajian:id,judul,poster','ustadzs:id,nama_lengkap'])
                ->whereHas('kajian', function($query) {
                    $query->where('is_archive', false); 
                    $query->where('is_draft', false); 
                })
                ->select('id','kajian_id','jam_mulai','jam_selesai','tanggal','hari','status','diperuntukan','link','position')
                ->orderBy('kajian_id', 'asc')
                ->orderBy('position', 'asc') 
                ->get()
                ->map(fn($j)=>[
                    'id'=>$j->id,
                    'jam_mulai'=>$j->jam_mulai,
                    'jam_selesai'=>$j->jam_selesai,
                    'tanggal'=>$j->tanggal,
                    'hari'=>$j->hari,
                    'status'=>$j->status,
                    'diperuntukan'=>$j->diperuntukan,
                    'link' => $j->link,
                    'position' => $j->position,
                    'kajian_poster'=>[
                        'id'=>$j->kajian->id ?? null,
                        'judul'=>$j->kajian->judul ?? null,
                        'poster_url'=>$j->kajian->poster ? asset('uploads/kajian-poster/'.$j->kajian->poster) : null
                    ],
                    'ustadz'=>$j->ustadzs->map(fn($u)=>['id'=>$u->id,'nama_lengkap'=>$u->nama_lengkap])
                ]);

            return response()->json(['status'=>'success','message'=>'Data jadwal kajian berhasil diambil','data'=>$jadwalKajian]);
        } catch (\Exception $e) {
            return response()->json(['status'=>'error','message'=>'Terjadi kesalahan','error'=>$e->getMessage()],500);
        }
    }


    public function updateKajianPositions(Request $request)
    {
        try {
            $kajianIds = $request->kajian_ids;
            
            foreach ($kajianIds as $index => $kajianId) {
                KajianPoster::where('id', $kajianId)->update(['position' => $index + 1]);
            }
            
            return response()->json(['success' => true, 'message' => 'Posisi berhasil diupdate']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateJadwalPositions(Request $request)
    {
        $request->validate([
            'kajian_id' => 'required|exists:kajian_posters,id',
            'positions' => 'required|array',
            'positions.*.id' => 'required|exists:jadwal_kajians,id',
            'positions.*.position' => 'required|integer|min:1'
        ]);

        try {
            DB::transaction(function() use ($request) {
                foreach ($request->positions as $item) {
                    JadwalKajian::where('id', $item['id'])
                        ->where('kajian_id', $request->kajian_id)
                        ->update(['position' => $item['position']]);
                }
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Urutan jadwal berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memperbarui urutan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function archive($id)
    {
        try {
            $kajianPoster = KajianPoster::findOrFail($id);

            // Validasi: jika draft, tidak boleh di-archive
            if ($kajianPoster->is_draft) {
                return redirect()->back()->with(AlertHelper::error(
                    'Kajian poster dalam draft tidak bisa diarsipkan',
                    'Error'
                ));
            }

            $kajianPoster->is_archive = true;
            $saveResult = $kajianPoster->save();

            Log::info("Hasil save archive", [
                'save_result' => $saveResult,
                'is_archive_after_save' => $kajianPoster->is_archive,
                'updated_data' => $kajianPoster->fresh()->toArray()
            ]);

            return redirect()->back()->with(AlertHelper::success(
                'Kajian poster berhasil diarsipkan',
                'Success'
            ));
        } catch (\Exception $e) {
            Log::error("Error saat archive", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with(AlertHelper::error(
                'Terjadi kesalahan saat mengarsipkan kajian poster',
                'Error'
            ));
        }
    }

    public function publish($id)
    {
        try {
            $kajianPoster = KajianPoster::findOrFail($id);

            // Paksa keduanya jadi false
            $kajianPoster->is_draft = false;
            $kajianPoster->is_archive = false;

            $saveResult = $kajianPoster->save();

            Log::info("Hasil save publish", [
                'save_result' => $saveResult,
                'is_draft_after_save' => $kajianPoster->is_draft,
                'is_archive_after_save' => $kajianPoster->is_archive,
                'updated_data' => $kajianPoster->fresh()->toArray()
            ]);

            return redirect()->back()->with(AlertHelper::success(
                'Kajian poster berhasil dipublish',
                'Success'
            ));
        } catch (\Exception $e) {
            Log::error("Error saat publish", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with(AlertHelper::error(
                'Terjadi kesalahan saat publish kajian poster',
                'Error'
            ));
        }
    }


    private function getHariTerdekat($jadwalKajians)
    {
        if ($jadwalKajians->isEmpty()) {
            return null;
        }

        $today = now()->startOfDay();
        
        $jadwalMendatang = $jadwalKajians
            ->filter(function($jadwal) use ($today) {
                return \Carbon\Carbon::parse($jadwal->tanggal)->startOfDay()->gte($today);
            })
            ->sortBy('tanggal');
        
        if ($jadwalMendatang->isNotEmpty()) {
            return $jadwalMendatang->first()->hari;
        }
        
        return null;
    }  


    public function store(Request $request)
    {
        $isDraft = $request->_draft == 1;

        if ($isDraft) {
            $request->validate([
                'judul' => 'required|string|max:255',
            ]);
        } else {
            $request->validate([
                'judul' => 'required|string|max:255',
                'keterangan' => 'required|string',
                'category_ids' => 'required|array|min:1',
                'category_ids.*' => 'string|max:255',
                'jenis' => ['required', Rule::in(['rutin', 'akbar/dauroh'])],
                'penyelenggara' => 'required|string|max:255',
                'masjid_id' => 'nullable|exists:masjids,id',
                'alamat_manual' => 'nullable|string',
                'link' => 'nullable|string|max:255',
            ]);

            if (empty($request->masjid_id) && empty($request->alamat_manual)) {
                return back()->withErrors(['lokasi' => 'Pilih salah satu lokasi: Masjid atau Alamat Manual']);
            }

            if (!empty($request->masjid_id) && !empty($request->alamat_manual)) {
                return back()->withErrors(['lokasi' => 'Pilih hanya salah satu lokasi']);
            }
        }

        // Upload poster (opsional di draft)
        $posterFileName = null;
        if ($request->hasFile('poster')) {
            $file = $request->file('poster');
            $posterFileName = uniqid('poster_') . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/kajian-poster'), $posterFileName);
        }

        $kajian = KajianPoster::create([
            'masjid_id' => $request->masjid_id,
            'judul' => $request->judul,
            'keterangan' => $request->keterangan,
            'jenis' => $request->jenis,
            'poster' => $posterFileName,
            'penyelenggara' => $request->penyelenggara,
            'alamat_manual' => $request->alamat_manual,
            'link' => $request->link,
            'is_draft' => $isDraft, 
        ]);

        if (!$isDraft) {
            $this->syncCategories($kajian, $request->category_ids);
        }

        return redirect()->route('kajian.index')->with(
            AlertHelper::success(
                $isDraft ? 'Kajian berhasil disimpan sebagai draft' : 'Kajian berhasil ditambahkan',
                'Success'
            )
        );
    }


    public function update(Request $request,$id){
        $kajian=KajianPoster::findOrFail($id);

        $request->validate([
            'judul'=>'required|string|max:255',
            'keterangan'=>'required|string',
            'category_ids'=>'required|array|min:1',
            'category_ids.*'=>'string|max:255',
            'jenis'=>['required',Rule::in(['rutin','akbar/dauroh'])],
            'penyelenggara'=>'required|string|max:255',
            'masjid_id'=>'nullable|exists:masjids,id',
            'alamat_manual'=>'nullable|string',
            'link'=>'nullable|string|max:255',
        ]);

        if(empty($request->masjid_id) && empty($request->alamat_manual)){
            return back()->withErrors(['lokasi'=>'Pilih salah satu lokasi']);
        }
        if(!empty($request->masjid_id) && !empty($request->alamat_manual)){
            return back()->withErrors(['lokasi'=>'Pilih hanya salah satu lokasi']);
        }

        $updateData=[
            'masjid_id'=>$request->masjid_id,
            'judul'=>$request->judul,
            'keterangan'=>$request->keterangan,
            'jenis'=>$request->jenis,
            'link' => $request->link,
            'penyelenggara'=>$request->penyelenggara,
            'alamat_manual'=>$request->alamat_manual,
        ];

        if($request->hasFile('poster')){
            if($kajian->poster && file_exists(public_path('uploads/kajian-poster/'.$kajian->poster))){
                unlink(public_path('uploads/kajian-poster/'.$kajian->poster));
            }
            $file=$request->file('poster');
            $posterFileName=uniqid('poster_').'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/kajian-poster'),$posterFileName);
            $updateData['poster']=$posterFileName;
        }

        $kajian->update($updateData);
        $this->syncCategories($kajian,$request->category_ids);

        return redirect()->route('kajian.index')->with(AlertHelper::success('Kajian berhasil diperbarui','Success'));
    }

    public function destroy($id){
        $kajian=KajianPoster::findOrFail($id);
        if($kajian->poster && file_exists(public_path('uploads/kajian-poster/'.$kajian->poster))){
            unlink(public_path('uploads/kajian-poster/'.$kajian->poster));
        }
        $kajian->delete();
        return redirect()->route('kajian.index')->with(AlertHelper::success('Kajian berhasil dihapus','Success'));
    }

    private function syncCategories($kajian,$categoryInputs){
        if(!$categoryInputs) return;
        $categoryIds=[];
        foreach($categoryInputs as $c){
            $c=trim($c);
            if(is_numeric($c) && Category::find($c)){
                $categoryIds[]=$c;
            } else {
                $existing=Category::where('nama',$c)->first();
                if($existing){
                    $categoryIds[]=$existing->id;
                } else {
                    $new=Category::create(['nama'=>$c,'deskripsi'=>null,'is_active'=>true]);
                    $categoryIds[]=$new->id;
                }
            }
        }
        $kajian->categories()->sync($categoryIds);
    }

    // ================= Jadwal =================

    public function storeJadwal(Request $request){
        $request->validate([
            'kajian_id'=>'required|exists:kajian_posters,id',
            'jam_mulai'=>'required|date_format:H:i',
            'jam_selesai'=>'required|date_format:H:i|after:jam_mulai',
            'tanggal'=>'required|date',
            'status' => ['required', Rule::in(array_keys(JadwalKajian::statusOptions()))],
            'diperuntukan'=>['required',Rule::in(['semua kaum muslim','ikhwan','akhwat'])],
            'ustadz_ids'=>'nullable|array',
            'ustadz_ids.*'=>'nullable|string',
            'link'=> 'nullable|url'
        ]);
        
        try {
            DB::transaction(function() use ($request) {
                $nextPosition = JadwalKajian::where('kajian_id', $request->kajian_id)
                    ->max('position') + 1;
                $dayMap=['Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu','Sunday'=>'Ahad'];
                $hari=$dayMap[Carbon::parse($request->tanggal)->format('l')];
                    
                    $jadwal = JadwalKajian::create([
                    'kajian_id'=>$request->kajian_id,
                    'jam_mulai'=>$request->jam_mulai,
                    'jam_selesai'=>$request->jam_selesai,
                    'tanggal'=>$request->tanggal,
                    'hari'=>$hari,
                    'status'=>$request->status,
                    'diperuntukan'=>$request->diperuntukan,
                    'link'=>$request->link,
                    'position' => $nextPosition
                ]);

                if($request->ustadz_ids){
                    $ustadzIds=[];
                    foreach($request->ustadz_ids as $u){
                        $u=trim($u);
                        if(is_numeric($u) && Ustadz::find($u)){
                            $ustadzIds[]=$u;
                        } else {
                            $existing=Ustadz::where('nama_lengkap',$u)->first();
                            if($existing){
                                $ustadzIds[]=$existing->id;
                            } else {
                                $new=Ustadz::create(['nama_lengkap'=>$u]);
                                $ustadzIds[]=$new->id;
                            }
                        }
                    }
                    $jadwal->ustadzs()->attach($ustadzIds);
                }
            });
            return back()->with(AlertHelper::success('Berhasil menambahkan jadwal kajian','Success'));
        
        } catch (\Throwable $th) {
            return back()->with(AlertHelper::success('terjadi kesalahan'. $th->getMessage(),'Error'));
        }
       
    }

    public function updateJadwal(Request $request,$id){
        $jadwal=JadwalKajian::findOrFail($id);

        $request->validate([
            'jam_mulai'=>'required|date_format:H:i',
            'jam_selesai'=>'required|date_format:H:i|after:jam_mulai',
            'tanggal'=>'required|date',
            'status' => ['required', Rule::in(array_keys(JadwalKajian::statusOptions()))],
            'diperuntukan'=>['required',Rule::in(['semua kaum muslim','ikhwan','akhwat'])],
            'ustadz_ids'=>'nullable|array',
            'ustadz_ids.*'=>'nullable|string',
            'link'=> 'nullable|url',
        ]);

        $dayMap=['Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu','Sunday'=>'Ahad'];
        $hari=$dayMap[Carbon::parse($request->tanggal)->format('l')];

        $jadwal->update([
            'jam_mulai'=>$request->jam_mulai,
            'jam_selesai'=>$request->jam_selesai,
            'tanggal'=>$request->tanggal,
            'hari'=>$hari,
            'status'=>$request->status,
            'diperuntukan'=>$request->diperuntukan,
            'link'=>$request->link,
        ]);

        $jadwal->ustadzs()->detach();
        if($request->ustadz_ids){
            $ustadzIds=[];
            foreach($request->ustadz_ids as $u){
                $u=trim($u);
                if(is_numeric($u) && Ustadz::find($u)){
                    $ustadzIds[]=$u;
                } else {
                    $existing=Ustadz::where('nama_lengkap',$u)->first();
                    $ustadzIds[]=$existing ? $existing->id : Ustadz::create(['nama_lengkap'=>$u])->id;
                }
            }
            $jadwal->ustadzs()->attach($ustadzIds);
        }

        return back()->with(AlertHelper::success('Berhasil memperbarui jadwal kajian','Success'));
    }

 public function destroyJadwal($id)
    {
        try {
            $jadwal = JadwalKajian::findOrFail($id);
            $kajianId = $jadwal->kajian_id;
            $deletedPosition = $jadwal->position;

            DB::transaction(function() use ($jadwal, $kajianId, $deletedPosition) {
                $jadwal->delete();
                JadwalKajian::where('kajian_id', $kajianId)
                    ->where('position', '>', $deletedPosition)
                    ->decrement('position');
            });

            return back()->with(AlertHelper::success('Berhasil menghapus data jadwal','Success'));

        } catch (\Exception $e) {
            return back()->with(AlertHelper::error('Terjadi Kesalahan' . $e->getMessage(),'Error'));

        }
    }

    public function searchUstadz(Request $request){
        $query=$request->get('q','');
        $ustadzs=Ustadz::where('nama_lengkap','like','%'.$query.'%')
            ->orderBy('nama_lengkap')
            ->limit(10)
            ->get()
            ->map(fn($u)=>['value'=>$u->id,'label'=>$u->nama_lengkap]);

        return response()->json($ustadzs);
    }
}
