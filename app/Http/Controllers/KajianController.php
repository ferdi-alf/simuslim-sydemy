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
use Illuminate\Validation\Rule;

class KajianController extends Controller
{
    public function index() {
        $kajians = KajianPoster::with(['masjid', 'jadwalKajians.ustadzs', 'categories'])
            ->orderBy('created_at', 'desc')
            ->get();

        $masjids = Masjid::orderBy('nama')->get();
        $ustadzs = Ustadz::orderBy('nama_lengkap')->get();
        $ustadzOptions = $ustadzs->map(fn($u) => ['value'=>$u->id, 'label'=>$u->nama_lengkap])->toArray();

        $categories = Category::active()->orderBy('nama')->get();
        $categoriesOptions = $categories->map(fn($c) => ['value'=>$c->id, 'label'=>$c->nama])->toArray();

        return view('dashboard.kajian', compact('kajians','masjids','ustadzOptions','categoriesOptions'));
    }

    public function getAllKajianPosters()
    {
        try {
            $kajianPosters = KajianPoster::with([
                'masjid:id,nama,alamat,maps',
                'categories:id,nama', // ambil kategori
                'jadwalKajians' => fn($q) => $q->with(['ustadzs:id,nama_lengkap'])
                    ->select('id','kajian_id','jam_mulai','jam_selesai','tanggal','hari','status','diperuntukan')
                    ->orderBy('tanggal', 'asc') // urutkan berdasarkan tanggal
            ])
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
                        // 'link'=> $j->link,
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

    /**
     * Mendapatkan hari terdekat berdasarkan jadwal kajian
     */
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

    public function getAllJadwalKajian()
    {
        try {
            $jadwalKajian = JadwalKajian::with(['kajian:id,judul,poster','ustadzs:id,nama_lengkap'])
                ->select('id','kajian_id','jam_mulai','jam_selesai','tanggal','hari','status','diperuntukan','link')
                ->get()
                ->map(fn($j)=>[
                    'id'=>$j->id,
                    'jam_mulai'=>$j->jam_mulai,
                    'jam_selesai'=>$j->jam_selesai,
                    'tanggal'=>$j->tanggal,
                    'hari'=>$j->hari,
                    'status'=>$j->status,
                    'diperuntukan'=>$j->diperuntukan,
                    // 'link' => $j->link,
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

    public function store(Request $request) {
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

        if (empty($request->masjid_id) && empty($request->alamat_manual)) {
            return back()->withErrors(['lokasi'=>'Pilih salah satu lokasi: Masjid atau Alamat Manual']);
        }
        if (!empty($request->masjid_id) && !empty($request->alamat_manual)) {
            return back()->withErrors(['lokasi'=>'Pilih hanya salah satu lokasi']);
        }

        $posterFileName=null;
        if($request->hasFile('poster')){
            $file=$request->file('poster');
            $posterFileName=uniqid('poster_').'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/kajian-poster'),$posterFileName);
        }

        $kajian=KajianPoster::create([
            'masjid_id'=>$request->masjid_id,
            'judul'=>$request->judul,
            'keterangan'=>$request->keterangan,
            'jenis'=>$request->jenis,
            'poster'=>$posterFileName,
            'penyelenggara'=>$request->penyelenggara,
            'alamat_manual'=>$request->alamat_manual,
            'link'=> $request->link,
        ]);

        $this->syncCategories($kajian,$request->category_ids);

        return redirect()->route('kajian.index')->with(AlertHelper::success('Kajian berhasil ditambahkan','Success'));
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
            'status'=>['required',Rule::in(['belum dimulai','berjalan','selesai','liburkan'])],
            'diperuntukan'=>['required',Rule::in(['semua kaum muslim','ikhwan','akhwat'])],
            'ustadz_ids'=>'nullable|array',
            'ustadz_ids.*'=>'nullable|string',
            'link'=> 'nullable|url'
        ]);

        $dayMap=['Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu','Sunday'=>'Ahad'];
        $hari=$dayMap[Carbon::parse($request->tanggal)->format('l')];

        $jadwal=JadwalKajian::create([
            'kajian_id'=>$request->kajian_id,
            'jam_mulai'=>$request->jam_mulai,
            'jam_selesai'=>$request->jam_selesai,
            'tanggal'=>$request->tanggal,
            'hari'=>$hari,
            'status'=>$request->status,
            'diperuntukan'=>$request->diperuntukan,
            'link'=>$request->link,
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

        return back()->with(AlertHelper::success('Berhasil menambahkan jadwal kajian','Success'));
    }

    public function updateJadwal(Request $request,$id){
        $jadwal=JadwalKajian::findOrFail($id);

        $request->validate([
            'jam_mulai'=>'required|date_format:H:i',
            'jam_selesai'=>'required|date_format:H:i|after:jam_mulai',
            'tanggal'=>'required|date',
            'status'=>['required',Rule::in(['belum dimulai','berjalan','selesai','liburkan'])],
            'diperuntukan'=>['required',Rule::in(['semua kaum muslim','ikhwan','akhwat'])],
            'ustadz_ids'=>'nullable|array',
            'ustadz_ids.*'=>'nullable|string',
            'link'=> 'nullable|url',
        ]);

        $dayMap=['Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu','Sunday'=>'Minggu'];
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

    public function destroyJadwal($id){
        $jadwal=JadwalKajian::findOrFail($id);
        $jadwal->ustadzs()->detach();
        $jadwal->delete();
        return redirect()->route('kajian.index')->with(AlertHelper::success('Berhasil menghapus data jadwal','Success'));
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
