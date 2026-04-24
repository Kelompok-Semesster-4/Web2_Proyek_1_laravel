<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lantai;
use App\Models\Gedung;
use Illuminate\Support\Facades\DB;

class AdminLantaiController extends Controller
{
    public function index()
    {
        $lantais = DB::select("
            SELECT l.nomor,
                   COUNT(DISTINCT l.gedung_id) AS jumlah_gedung,
                   COUNT(r.id) AS jumlah_ruangan_total
            FROM lantai l
            LEFT JOIN ruangan r ON r.lantai_id = l.id
            GROUP BY l.nomor
            ORDER BY l.nomor ASC
        ");

        $detailRows = DB::select("
            SELECT l.id, l.nomor, l.gedung_id, g.nama_gedung,
                   COUNT(r.id) AS jumlah_ruangan
            FROM lantai l
            LEFT JOIN gedung g ON g.id = l.gedung_id
            LEFT JOIN ruangan r ON r.lantai_id = l.id
            GROUP BY l.id, l.nomor, l.gedung_id, g.nama_gedung
            ORDER BY l.nomor ASC, g.nama_gedung ASC
        ");

        $lantaiDetailMap = [];
        foreach ($detailRows as $row) {
            $nomorKey = (string)$row->nomor;
            if (!isset($lantaiDetailMap[$nomorKey])) {
                $lantaiDetailMap[$nomorKey] = [];
            }
            $lantaiDetailMap[$nomorKey][] = [
                'id' => (int)$row->id,
                'gedung_id' => (int)$row->gedung_id,
                'nama_gedung' => (string)$row->nama_gedung,
                'jumlah_ruangan' => (int)$row->jumlah_ruangan,
            ];
        }

        $gedungs = Gedung::orderBy('nama_gedung', 'asc')->get();

        return view('admin.lantai', compact('lantais', 'lantaiDetailMap', 'gedungs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'gedung_id' => 'required|exists:gedung,id',
            'nomor' => 'required|integer|min:1'
        ]);

        $exists = Lantai::where('gedung_id', $request->gedung_id)
            ->where('nomor', $request->nomor)
            ->exists();

        if ($exists) {
            return redirect()->route('admin.lantai.index')->with('error', 'Nomor lantai tersebut sudah ada di gedung yang dipilih!');
        }

        Lantai::create([
            'gedung_id' => $request->gedung_id,
            'nomor' => $request->nomor
        ]);

        return redirect()->route('admin.lantai.index')->with('success', 'add');
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:lantai,id',
            'gedung_id' => 'required|exists:gedung,id',
            'nomor' => 'required|integer|min:1'
        ]);

        $exists = Lantai::where('gedung_id', $request->gedung_id)
            ->where('nomor', $request->nomor)
            ->where('id', '!=', $request->id)
            ->exists();

        if ($exists) {
            return redirect()->route('admin.lantai.index')->with('error', 'Kombinasi gedung dan nomor lantai sudah digunakan oleh data lain!');
        }

        $lantai = Lantai::findOrFail($request->id);
        $lantai->update([
            'gedung_id' => $request->gedung_id,
            'nomor' => $request->nomor
        ]);

        return redirect()->route('admin.lantai.index')->with('success', 'edit');
    }

    public function destroy($id)
    {
        $lantai = Lantai::findOrFail($id);

        $ruanganCount = DB::table('ruangan')->where('lantai_id', $id)->count();

        if ($ruanganCount > 0) {
            return redirect()->route('admin.lantai.index')->with('error', "Lantai tidak dapat dihapus karena masih memiliki {$ruanganCount} ruangan");
        }

        $lantai->delete();

        return redirect()->route('admin.lantai.index')->with('success', 'delete');
    }
}
