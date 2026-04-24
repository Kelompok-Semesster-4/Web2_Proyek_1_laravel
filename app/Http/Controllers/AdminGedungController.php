<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gedung;
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
            'nama_gedung' => 'required|string|max:100|unique:gedung,nama_gedung'
        ], [
            'nama_gedung.unique' => 'Nama gedung sudah terdaftar!'
        ]);

        Gedung::create([
            'nama_gedung' => $request->nama_gedung
        ]);

        return redirect()->route('admin.gedung.index')->with('success', 'add');
    }

    public function update(Request $request)
    {
        $id = $request->id;
        $request->validate([
            'nama_gedung' => 'required|string|max:100|unique:gedung,nama_gedung,' . $id
        ], [
            'nama_gedung.unique' => 'Nama gedung sudah digunakan oleh data lain!'
        ]);

        $gedung = Gedung::findOrFail($id);
        $gedung->update([
            'nama_gedung' => $request->nama_gedung
        ]);

        return redirect()->route('admin.gedung.index')->with('success', 'edit');
    }

    public function destroy($id)
    {
        $gedung = Gedung::findOrFail($id);

        $counts = DB::selectOne("
            SELECT 
                (SELECT COUNT(*) FROM lantai WHERE gedung_id = ?) AS lantai,
                (SELECT COUNT(*) FROM ruangan r INNER JOIN lantai l ON l.id = r.lantai_id WHERE l.gedung_id = ?) AS ruangan
        ", [$id, $id]);

        if ($counts->lantai > 0 || $counts->ruangan > 0) {
            return redirect()->route('admin.gedung.index')->with('error', "Gedung tidak dapat dihapus karena masih memiliki {$counts->lantai} lantai dan {$counts->ruangan} ruangan");
        }

        $gedung->delete();

        return redirect()->route('admin.gedung.index')->with('success', 'delete');
    }
}
