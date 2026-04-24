<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ruangan;
use App\Models\Lantai;
use App\Models\Gedung;
use App\Models\Fasilitas;
use App\Models\RuanganFoto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminRuanganController extends Controller
{
    public function index()
    {
        $ruangans = Ruangan::select('ruangan.*', 'lantai.nomor as Lantai', 'lantai.gedung_id', 'gedung.nama_gedung as gedung')
            ->leftJoin('lantai', 'lantai.id', '=', 'ruangan.lantai_id')
            ->leftJoin('gedung', 'gedung.id', '=', 'lantai.gedung_id')
            ->orderBy('ruangan.nama_ruangan', 'asc')
            ->get();

        foreach ($ruangans as $ruangan) {
            $cover = RuanganFoto::where('ruangan_id', $ruangan->id)->where('tipe', 'cover')->orderBy('id', 'desc')->first();
            $ruangan->cover_foto = $cover ? $cover->nama_file : null;
            $ruangan->detail_count = RuanganFoto::where('ruangan_id', $ruangan->id)->where('tipe', 'detail')->count();
        }

        $gedungList = Gedung::orderBy('nama_gedung', 'asc')->get();
        $lantaiRows = Lantai::orderBy('gedung_id', 'asc')->orderBy('nomor', 'asc')->get();
        
        $lantaiMapByGedung = [];
        foreach ($lantaiRows as $row) {
            $lantaiMapByGedung[$row->gedung_id][] = [
                'id' => $row->id,
                'nomor' => $row->nomor,
            ];
        }

        $fotoRows = RuanganFoto::orderBy('id', 'desc')->get();
        $ruanganPhotos = [];
        foreach ($fotoRows as $row) {
            if (!isset($ruanganPhotos[$row->ruangan_id])) {
                $ruanganPhotos[$row->ruangan_id] = ['cover' => [], 'detail' => []];
            }
            $ruanganPhotos[$row->ruangan_id][$row->tipe][] = [
                'id' => $row->id,
                'nama_file' => $row->nama_file
            ];
        }

        $fasilitasList = Fasilitas::orderBy('id', 'asc')->get();
        $fasilitasNameMap = [];
        foreach ($fasilitasList as $f) {
            $fasilitasNameMap[$f->id] = $f->nama_fasilitas;
        }

        $rfRows = DB::table('ruangan_fasilitas')->orderBy('ruangan_id')->orderBy('fasilitas_id')->get();
        $ruanganFasilitasMap = [];
        foreach ($rfRows as $row) {
            if (!isset($ruanganFasilitasMap[$row->ruangan_id])) {
                $ruanganFasilitasMap[$row->ruangan_id] = [];
            }
            $ruanganFasilitasMap[$row->ruangan_id][] = $row->fasilitas_id;
        }

        return view('admin.ruangan', compact(
            'ruangans', 
            'gedungList', 
            'lantaiMapByGedung', 
            'ruanganPhotos', 
            'fasilitasList', 
            'fasilitasNameMap', 
            'ruanganFasilitasMap'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_ruangan' => 'required|string|max:100',
            'lantai_id' => 'required|exists:lantai,id',
            'kapasitas' => 'required|integer|min:1',
            'deskripsi' => 'nullable|string',
            'foto_cover' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'foto_detail.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $exists = Ruangan::where('nama_ruangan', $request->nama_ruangan)
            ->where('lantai_id', $request->lantai_id)
            ->exists();

        if ($exists) {
            return redirect()->route('admin.ruangan.index')->with('error', 'Ruangan dengan nama tersebut sudah ada di lantai ini!');
        }

        DB::beginTransaction();
        try {
            $ruangan = Ruangan::create([
                'lantai_id' => $request->lantai_id,
                'nama_ruangan' => $request->nama_ruangan,
                'kapasitas' => $request->kapasitas,
                'deskripsi' => $request->deskripsi
            ]);

            if ($request->has('fasilitas_ids') && is_array($request->fasilitas_ids)) {
                $ruangan->fasilitas()->sync($request->fasilitas_ids);
            }

            if ($request->hasFile('foto_cover')) {
                $file = $request->file('foto_cover');
                $filename = 'ruangan_' . $ruangan->id . '_' . time() . '_' . substr(md5(rand()), 0, 12) . '.' . $file->getClientOriginalExtension();
                $file->storeAs('uploads/ruangan', $filename, 'public');
                RuanganFoto::create([
                    'ruangan_id' => $ruangan->id,
                    'nama_file' => $filename,
                    'tipe' => 'cover'
                ]);
            }

            if ($request->hasFile('foto_detail')) {
                foreach ($request->file('foto_detail') as $file) {
                    $filename = 'ruangan_' . $ruangan->id . '_' . time() . '_' . substr(md5(rand()), 0, 12) . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('uploads/ruangan', $filename, 'public');
                    RuanganFoto::create([
                        'ruangan_id' => $ruangan->id,
                        'nama_file' => $filename,
                        'tipe' => 'detail'
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.ruangan.index')->with('success', 'add');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.ruangan.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:ruangan,id',
            'nama_ruangan' => 'required|string|max:100',
            'lantai_id' => 'required|exists:lantai,id',
            'kapasitas' => 'required|integer|min:1',
            'deskripsi' => 'nullable|string',
            'foto_cover' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'foto_detail.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $exists = Ruangan::where('nama_ruangan', $request->nama_ruangan)
            ->where('lantai_id', $request->lantai_id)
            ->where('id', '!=', $request->id)
            ->exists();

        if ($exists) {
            return redirect()->route('admin.ruangan.index')->with('error', 'Ruangan dengan nama tersebut sudah ada di lantai ini!');
        }

        DB::beginTransaction();
        try {
            $ruangan = Ruangan::findOrFail($request->id);
            $ruangan->update([
                'lantai_id' => $request->lantai_id,
                'nama_ruangan' => $request->nama_ruangan,
                'kapasitas' => $request->kapasitas,
                'deskripsi' => $request->deskripsi
            ]);

            $fasilitasIds = $request->has('fasilitas_ids') && is_array($request->fasilitas_ids) ? $request->fasilitas_ids : [];
            $ruangan->fasilitas()->sync($fasilitasIds);

            if ($request->has('delete_foto') && is_array($request->delete_foto)) {
                foreach ($request->delete_foto as $fotoId) {
                    $foto = RuanganFoto::find($fotoId);
                    if ($foto && $foto->ruangan_id == $ruangan->id) {
                        Storage::disk('public')->delete('uploads/ruangan/' . $foto->nama_file);
                        $foto->delete();
                    }
                }
            }

            if ($request->hasFile('foto_cover')) {
                // Remove existing cover if any to maintain single cover logic? The old code allowed multiple covers if logic fails, but let's just append or replace.
                // Old code adds new cover and orders by id DESC. Let's maintain old behavior.
                $file = $request->file('foto_cover');
                $filename = 'ruangan_' . $ruangan->id . '_' . time() . '_' . substr(md5(rand()), 0, 12) . '.' . $file->getClientOriginalExtension();
                $file->storeAs('uploads/ruangan', $filename, 'public');
                RuanganFoto::create([
                    'ruangan_id' => $ruangan->id,
                    'nama_file' => $filename,
                    'tipe' => 'cover'
                ]);
            }

            if ($request->hasFile('foto_detail')) {
                foreach ($request->file('foto_detail') as $file) {
                    $filename = 'ruangan_' . $ruangan->id . '_' . time() . '_' . substr(md5(rand()), 0, 12) . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('uploads/ruangan', $filename, 'public');
                    RuanganFoto::create([
                        'ruangan_id' => $ruangan->id,
                        'nama_file' => $filename,
                        'tipe' => 'detail'
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.ruangan.index')->with('success', 'edit');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.ruangan.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $ruangan = Ruangan::findOrFail($id);

        $peminjamanCount = DB::table('peminjaman')->where('ruangan_id', $id)->count();

        if ($peminjamanCount > 0) {
            return redirect()->route('admin.ruangan.index')->with('error', "Ruangan tidak dapat dihapus karena pernah dipinjam ({$peminjamanCount} transaksi).");
        }

        DB::beginTransaction();
        try {
            $fotos = RuanganFoto::where('ruangan_id', $id)->get();
            foreach ($fotos as $foto) {
                Storage::disk('public')->delete('uploads/ruangan/' . $foto->nama_file);
                $foto->delete();
            }

            $ruangan->fasilitas()->detach();
            $ruangan->delete();

            DB::commit();
            return redirect()->route('admin.ruangan.index')->with('success', 'delete');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.ruangan.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
