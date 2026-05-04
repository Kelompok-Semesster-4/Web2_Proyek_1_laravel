<?php

namespace App\Http\Controllers;

use App\Models\Gedung;
use App\Models\Ruangan;
use App\Models\RuanganFoto;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'tgl_awal' => ['nullable', 'date_format:Y-m-d'],
            'tgl_akhir' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:tgl_awal'],
            'gedung' => ['nullable', 'string', 'max:100'],
        ]);

        $heroImages = RuanganFoto::query()
            ->select('nama_file as foto')
            ->whereNotNull('nama_file')
            ->where('nama_file', '!=', '')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        $tgl_awal = $validated['tgl_awal'] ?? '';
        $tgl_akhir = $validated['tgl_akhir'] ?? '';
        $gedung = $validated['gedung'] ?? '';

        $query = Ruangan::query()
            ->from('ruangan as r')
            ->select('r.*', 'g.nama_gedung as gedung', 'l.nomor as Lantai')
            ->selectSub(function ($query) {
                $query->from('ruangan_foto as rf')
                    ->select('rf.nama_file')
                    ->whereColumn('rf.ruangan_id', 'r.id')
                    ->orderByDesc('rf.id')
                    ->limit(1);
            }, 'foto_utama')
            ->leftJoin('lantai as l', 'l.id', '=', 'r.lantai_id')
            ->leftJoin('gedung as g', 'g.id', '=', 'l.gedung_id');

        if ($gedung) {
            $query->where('g.nama_gedung', $gedung);
        }

        if ($tgl_awal && $tgl_akhir) {
            $query->whereNotExists(function ($query) use ($tgl_awal, $tgl_akhir) {
                $query->selectRaw('1')
                      ->from('peminjaman')
                      ->whereColumn('peminjaman.ruangan_id', 'r.id')
                      ->whereBetween('tanggal', [$tgl_awal, $tgl_akhir]);
            });
        }

        $ruangan = $query->orderBy('r.nama_ruangan')->get();
        $gedungList = Gedung::orderBy('id')->get();

        return view('dashboard', compact('heroImages', 'ruangan', 'gedungList', 'tgl_awal', 'tgl_akhir', 'gedung'));
    }
}
