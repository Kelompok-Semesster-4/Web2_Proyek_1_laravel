<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $heroImages = DB::select("
            SELECT nama_file AS foto
            FROM ruangan_foto
            WHERE nama_file IS NOT NULL AND nama_file != ''
            ORDER BY id DESC
            LIMIT 10
        ");

        $tgl_awal = $request->input('tgl_awal', '');
        $tgl_akhir = $request->input('tgl_akhir', '');
        $gedung = $request->input('gedung', '');

        $query = DB::table('ruangan as r')
            ->select('r.*', 'g.nama_gedung as gedung', 'l.nomor as Lantai')
            ->addSelect(DB::raw('(
                SELECT rf.nama_file
                FROM ruangan_foto rf
                WHERE rf.ruangan_id = r.id
                ORDER BY rf.id DESC
                LIMIT 1
            ) as foto_utama'))
            ->leftJoin('lantai as l', 'l.id', '=', 'r.lantai_id')
            ->leftJoin('gedung as g', 'g.id', '=', 'l.gedung_id');

        if ($gedung) {
            $query->where('g.nama_gedung', $gedung);
        }

        if ($tgl_awal && $tgl_akhir) {
            $query->whereNotExists(function ($query) use ($tgl_awal, $tgl_akhir) {
                $query->select(DB::raw(1))
                      ->from('peminjaman')
                      ->whereColumn('peminjaman.ruangan_id', 'r.id')
                      ->whereBetween('tanggal', [$tgl_awal, $tgl_akhir]);
            });
        }

        $ruangan = $query->orderBy('r.nama_ruangan')->get();
        $gedungList = DB::table('gedung')->orderBy('id')->get();

        return view('dashboard', compact('heroImages', 'ruangan', 'gedungList', 'tgl_awal', 'tgl_akhir', 'gedung'));
    }
}
