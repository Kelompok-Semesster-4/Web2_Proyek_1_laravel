<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gedung;
use App\Models\Lantai;
use Illuminate\Support\Facades\DB;

class AdminGedungController extends Controller
{
    public function index()
    {
        $gedungs = DB::select("
            SELECT g.id, g.nama_gedung,
                (SELECT COUNT(*) FROM lantai l WHERE l.gedung_id = g.id) AS jumlah_lantai,
                (SELECT COUNT(*) FROM ruangan r INNER JOIN lantai l2 ON l2.id = r.lantai_id WHERE l2.gedung_id = g.id) AS jumlah_ruangan
            FROM gedung g
            ORDER BY g.nama_gedung ASC
        ");

        return view('admin.gedung', compact('gedungs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_gedung' => 'required|string|max:100|unique:gedung,nama_gedung',
            'jumlah_lantai' => 'required|integer|min:1'
        ], [
            'nama_gedung.unique' => 'Nama gedung sudah terdaftar!',
            'jumlah_lantai.min' => 'Gedung minimal harus memiliki 1 lantai.'
        ]);

        DB::beginTransaction();
        try {
            $gedung = Gedung::create([
                'nama_gedung' => $request->nama_gedung
            ]);

            for ($i = 1; $i <= $request->jumlah_lantai; $i++) {
                Lantai::create([
                    'gedung_id' => $gedung->id,
                    'nomor' => $i
                ]);
            }
            DB::commit();
            return redirect()->route('admin.gedung.index')->with('success', 'add');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.gedung.index')->with('error', 'Gagal menyimpan data gedung.');
        }
    }

    public function update(Request $request)
    {
        $id = $request->id;
        $request->validate([
            'nama_gedung' => 'required|string|max:100|unique:gedung,nama_gedung,' . $id,
            'jumlah_lantai' => 'required|integer|min:1'
        ], [
            'nama_gedung.unique' => 'Nama gedung sudah digunakan oleh data lain!',
            'jumlah_lantai.min' => 'Gedung minimal harus memiliki 1 lantai.'
        ]);

        DB::beginTransaction();
        try {
            $gedung = Gedung::findOrFail($id);
            $gedung->update([
                'nama_gedung' => $request->nama_gedung
            ]);

            $currentMaxLantai = Lantai::where('gedung_id', $id)->max('nomor') ?? 0;
            $newJumlahLantai = $request->jumlah_lantai;

            if ($newJumlahLantai > $currentMaxLantai) {
                // Tambah lantai atas
                for ($i = $currentMaxLantai + 1; $i <= $newJumlahLantai; $i++) {
                    Lantai::create([
                        'gedung_id' => $id,
                        'nomor' => $i
                    ]);
                }
            } elseif ($newJumlahLantai < $currentMaxLantai) {
                // Cek apakah ada ruangan di lantai atas yang akan dihapus
                $ruanganAtasCount = DB::table('ruangan')
                    ->whereIn('lantai_id', function ($query) use ($id, $newJumlahLantai) {
                        $query->select('id')
                              ->from('lantai')
                              ->where('gedung_id', $id)
                              ->where('nomor', '>', $newJumlahLantai);
                    })->count();

                if ($ruanganAtasCount > 0) {
                    DB::rollBack();
                    return redirect()->route('admin.gedung.index')->with('error', 'Tidak dapat mengurangi jumlah lantai karena lantai atas masih memiliki data ruangan.');
                }

                // Aman dihapus
                Lantai::where('gedung_id', $id)->where('nomor', '>', $newJumlahLantai)->delete();
            }

            DB::commit();
            return redirect()->route('admin.gedung.index')->with('success', 'edit');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.gedung.index')->with('error', 'Gagal memperbarui data gedung.');
        }
    }

    public function destroy($id)
    {
        $gedung = Gedung::findOrFail($id);

        $counts = DB::selectOne("
            SELECT 
                (SELECT COUNT(*) FROM lantai WHERE gedung_id = ?) AS lantai,
                (SELECT COUNT(*) FROM ruangan r INNER JOIN lantai l ON l.id = r.lantai_id WHERE l.gedung_id = ?) AS ruangan
        ", [$id, $id]);

        if ($counts->ruangan > 0) {
            return redirect()->route('admin.gedung.index')->with('error', "Gedung tidak dapat dihapus karena masih memiliki {$counts->ruangan} ruangan.");
        }

        DB::beginTransaction();
        try {
            // Hapus lantainya dulu karena ruangan sudah kosong
            Lantai::where('gedung_id', $id)->delete();
            $gedung->delete();
            DB::commit();
            return redirect()->route('admin.gedung.index')->with('success', 'delete');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.gedung.index')->with('error', 'Gagal menghapus gedung.');
        }
    }
}
