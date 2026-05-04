<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gedung;
use App\Models\Lantai;
use App\Models\Ruangan;
use Illuminate\Support\Facades\DB;

class AdminGedungController extends Controller
{
    public function index()
    {
        $gedungs = Gedung::query()
            ->select('gedung.id', 'gedung.nama_gedung')
            ->withCount('lantai as jumlah_lantai')
            ->selectSub(function ($query) {
                $query->from('ruangan as r')
                    ->join('lantai as l', 'l.id', '=', 'r.lantai_id')
                    ->whereColumn('l.gedung_id', 'gedung.id')
                    ->selectRaw('COUNT(*)');
            }, 'jumlah_ruangan')
            ->orderBy('gedung.nama_gedung')
            ->get();

        return view('admin.gedung', compact('gedungs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_gedung' => ['required', 'string', 'max:100', 'unique:gedung,nama_gedung'],
            'jumlah_lantai' => ['required', 'integer', 'min:1'],
        ], [
            'nama_gedung.unique' => 'Nama gedung sudah terdaftar!',
            'jumlah_lantai.min' => 'Gedung minimal harus memiliki 1 lantai.'
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $gedung = Gedung::create([
                    'nama_gedung' => $validated['nama_gedung']
                ]);

                for ($i = 1; $i <= $validated['jumlah_lantai']; $i++) {
                    Lantai::create([
                        'gedung_id' => $gedung->id,
                        'nomor' => $i
                    ]);
                }
            });

            return redirect()->route('admin.gedung.index')->with('success', 'add');
        } catch (\Exception $e) {
            return redirect()->route('admin.gedung.index')->with('error', 'Gagal menyimpan data gedung.');
        }
    }

    public function update(Request $request)
    {
        $id = $request->id;
        $validated = $request->validate([
            'id' => ['required', 'exists:gedung,id'],
            'nama_gedung' => ['required', 'string', 'max:100', 'unique:gedung,nama_gedung,' . $id],
            'jumlah_lantai' => ['required', 'integer', 'min:1'],
        ], [
            'nama_gedung.unique' => 'Nama gedung sudah digunakan oleh data lain!',
            'jumlah_lantai.min' => 'Gedung minimal harus memiliki 1 lantai.'
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $gedung = Gedung::findOrFail($validated['id']);
                $gedung->update([
                    'nama_gedung' => $validated['nama_gedung']
                ]);

                $currentMaxLantai = Lantai::where('gedung_id', $validated['id'])->max('nomor') ?? 0;
                $newJumlahLantai = $validated['jumlah_lantai'];

                if ($newJumlahLantai > $currentMaxLantai) {
                    for ($i = $currentMaxLantai + 1; $i <= $newJumlahLantai; $i++) {
                        Lantai::create([
                            'gedung_id' => $validated['id'],
                            'nomor' => $i
                        ]);
                    }
                } elseif ($newJumlahLantai < $currentMaxLantai) {
                    $ruanganAtasCount = Ruangan::query()
                        ->whereIn('lantai_id', function ($query) use ($validated, $newJumlahLantai) {
                            $query->select('id')
                                ->from('lantai')
                                ->where('gedung_id', $validated['id'])
                                ->where('nomor', '>', $newJumlahLantai);
                        })->count();

                    if ($ruanganAtasCount > 0) {
                        throw new \RuntimeException('Tidak dapat mengurangi jumlah lantai karena lantai atas masih memiliki data ruangan.');
                    }

                    Lantai::where('gedung_id', $validated['id'])->where('nomor', '>', $newJumlahLantai)->delete();
                }
            });

            return redirect()->route('admin.gedung.index')->with('success', 'edit');
        } catch (\RuntimeException $e) {
            return redirect()->route('admin.gedung.index')->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->route('admin.gedung.index')->with('error', 'Gagal memperbarui data gedung.');
        }
    }

    public function destroy($id)
    {
        $gedung = Gedung::findOrFail($id);

        $jumlahRuangan = Ruangan::query()
            ->from('ruangan as r')
            ->join('lantai as l', 'l.id', '=', 'r.lantai_id')
            ->where('l.gedung_id', $id)
            ->count();

        if ($jumlahRuangan > 0) {
            return redirect()->route('admin.gedung.index')->with('error', "Gedung tidak dapat dihapus karena masih memiliki {$jumlahRuangan} ruangan.");
        }

        try {
            DB::transaction(function () use ($gedung, $id) {
                Lantai::where('gedung_id', $id)->delete();
                $gedung->delete();
            });

            return redirect()->route('admin.gedung.index')->with('success', 'delete');
        } catch (\Exception $e) {
            return redirect()->route('admin.gedung.index')->with('error', 'Gagal menghapus gedung.');
        }
    }
}
