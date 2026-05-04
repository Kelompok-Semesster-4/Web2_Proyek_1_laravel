<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ruangan;
use App\Models\Lantai;
use App\Models\Gedung;
use App\Models\Fasilitas;
use App\Models\Peminjaman;
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

        $ruangans->load('fasilitas:id');
        $ruanganFasilitasMap = [];
        foreach ($ruangans as $ruangan) {
            $ruanganFasilitasMap[$ruangan->id] = $ruangan->fasilitas->pluck('id')->all();
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
        $validated = $request->validate([
            'nama_ruangan' => ['required', 'string', 'max:100'],
            'lantai_id' => ['required', 'exists:lantai,id'],
            'kapasitas' => ['required', 'integer', 'min:1'],
            'deskripsi' => ['nullable', 'string'],
            'fasilitas_ids' => ['nullable', 'array'],
            'fasilitas_ids.*' => ['integer', 'exists:fasilitas,id'],
            'foto_cover' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'foto_detail' => ['nullable', 'array'],
            'foto_detail.*' => ['image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        $exists = Ruangan::where('nama_ruangan', $validated['nama_ruangan'])
            ->where('lantai_id', $validated['lantai_id'])
            ->exists();

        if ($exists) {
            return redirect()->route('admin.ruangan.index')->with('error', 'Ruangan dengan nama tersebut sudah ada di lantai ini!');
        }

        try {
            DB::transaction(function () use ($request, $validated) {
                $ruangan = Ruangan::create([
                    'lantai_id' => $validated['lantai_id'],
                    'nama_ruangan' => $validated['nama_ruangan'],
                    'kapasitas' => $validated['kapasitas'],
                    'deskripsi' => $validated['deskripsi'] ?? null
                ]);

                $ruangan->fasilitas()->sync($validated['fasilitas_ids'] ?? []);

                if ($request->hasFile('foto_cover')) {
                    $file = $request->file('foto_cover');
                    $filename = $this->makeRuanganFilename($ruangan->id, $file->getClientOriginalExtension());
                    $file->storeAs('uploads/ruangan', $filename, 'public');
                    RuanganFoto::create([
                        'ruangan_id' => $ruangan->id,
                        'nama_file' => $filename,
                        'tipe' => 'cover'
                    ]);
                }

                if ($request->hasFile('foto_detail')) {
                    foreach ($request->file('foto_detail') as $file) {
                        $filename = $this->makeRuanganFilename($ruangan->id, $file->getClientOriginalExtension());
                        $file->storeAs('uploads/ruangan', $filename, 'public');
                        RuanganFoto::create([
                            'ruangan_id' => $ruangan->id,
                            'nama_file' => $filename,
                            'tipe' => 'detail'
                        ]);
                    }
                }
            });

            return redirect()->route('admin.ruangan.index')->with('success', 'add');
        } catch (\Exception $e) {
            return redirect()->route('admin.ruangan.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => ['required', 'exists:ruangan,id'],
            'nama_ruangan' => ['required', 'string', 'max:100'],
            'lantai_id' => ['required', 'exists:lantai,id'],
            'kapasitas' => ['required', 'integer', 'min:1'],
            'deskripsi' => ['nullable', 'string'],
            'fasilitas_ids' => ['nullable', 'array'],
            'fasilitas_ids.*' => ['integer', 'exists:fasilitas,id'],
            'delete_foto' => ['nullable', 'array'],
            'delete_foto.*' => ['integer', 'exists:ruangan_foto,id'],
            'foto_cover' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'foto_detail' => ['nullable', 'array'],
            'foto_detail.*' => ['image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        $exists = Ruangan::where('nama_ruangan', $validated['nama_ruangan'])
            ->where('lantai_id', $validated['lantai_id'])
            ->where('id', '!=', $validated['id'])
            ->exists();

        if ($exists) {
            return redirect()->route('admin.ruangan.index')->with('error', 'Ruangan dengan nama tersebut sudah ada di lantai ini!');
        }

        try {
            DB::transaction(function () use ($request, $validated) {
                $ruangan = Ruangan::findOrFail($validated['id']);
                $ruangan->update([
                    'lantai_id' => $validated['lantai_id'],
                    'nama_ruangan' => $validated['nama_ruangan'],
                    'kapasitas' => $validated['kapasitas'],
                    'deskripsi' => $validated['deskripsi'] ?? null
                ]);

                $ruangan->fasilitas()->sync($validated['fasilitas_ids'] ?? []);

                foreach ($validated['delete_foto'] ?? [] as $fotoId) {
                    $foto = RuanganFoto::where('ruangan_id', $ruangan->id)->find($fotoId);
                    if ($foto) {
                        $this->deleteRuanganFoto($foto);
                    }
                }

                if ($request->hasFile('foto_cover')) {
                    $file = $request->file('foto_cover');
                    $filename = $this->makeRuanganFilename($ruangan->id, $file->getClientOriginalExtension());
                    $file->storeAs('uploads/ruangan', $filename, 'public');
                    RuanganFoto::create([
                        'ruangan_id' => $ruangan->id,
                        'nama_file' => $filename,
                        'tipe' => 'cover'
                    ]);
                }

                if ($request->hasFile('foto_detail')) {
                    foreach ($request->file('foto_detail') as $file) {
                        $filename = $this->makeRuanganFilename($ruangan->id, $file->getClientOriginalExtension());
                        $file->storeAs('uploads/ruangan', $filename, 'public');
                        RuanganFoto::create([
                            'ruangan_id' => $ruangan->id,
                            'nama_file' => $filename,
                            'tipe' => 'detail'
                        ]);
                    }
                }
            });

            return redirect()->route('admin.ruangan.index')->with('success', 'edit');
        } catch (\Exception $e) {
            return redirect()->route('admin.ruangan.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $ruangan = Ruangan::findOrFail($id);

        $peminjamanCount = Peminjaman::where('ruangan_id', $id)->count();

        if ($peminjamanCount > 0) {
            return redirect()->route('admin.ruangan.index')->with('error', "Ruangan tidak dapat dihapus karena pernah dipinjam ({$peminjamanCount} transaksi).");
        }

        try {
            DB::transaction(function () use ($ruangan, $id) {
                $fotos = RuanganFoto::where('ruangan_id', $id)->get();
                foreach ($fotos as $foto) {
                    $this->deleteRuanganFoto($foto);
                }

                $ruangan->fasilitas()->detach();
                $ruangan->delete();
            });

            return redirect()->route('admin.ruangan.index')->with('success', 'delete');
        } catch (\Exception $e) {
            return redirect()->route('admin.ruangan.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function makeRuanganFilename(int $ruanganId, string $extension): string
    {
        return 'ruangan_' . $ruanganId . '_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . strtolower($extension);
    }

    private function deleteRuanganFoto(RuanganFoto $foto): void
    {
        Storage::disk('public')->delete('uploads/ruangan/' . $foto->nama_file);
        $foto->delete();
    }
}
