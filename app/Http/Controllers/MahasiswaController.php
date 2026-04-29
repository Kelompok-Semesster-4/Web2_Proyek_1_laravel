<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MahasiswaController extends Controller
{
    /**
     * Halaman Utama Mahasiswa (Dashboard / Home)
     */
    public function dashboard(Request $request)
    {
        // Admin diizinkan melihat dashboard mahasiswa untuk keperluan testing UI

        $heroImages = DB::table('ruangan_foto')
            ->whereNotNull('nama_file')
            ->where('nama_file', '!=', '')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        $tgl_awal = $request->input('tgl_awal');
        $tgl_akhir = $request->input('tgl_akhir');
        $gedung = $request->input('gedung');

        $query = DB::table('ruangan as r')
            ->select('r.*', 'g.nama_gedung as gedung', 'l.nomor as Lantai')
            ->leftJoin('lantai as l', 'l.id', '=', 'r.lantai_id')
            ->leftJoin('gedung as g', 'g.id', '=', 'l.gedung_id')
            ->orderBy('r.nama_ruangan');

        if ($gedung) {
            $query->where('g.nama_gedung', $gedung);
        }

        if ($tgl_awal && $tgl_akhir) {
            $query->whereNotExists(function ($q) use ($tgl_awal, $tgl_akhir) {
                $q->select(DB::raw(1))
                    ->from('peminjaman')
                    ->whereColumn('peminjaman.ruangan_id', 'r.id')
                    ->whereBetween('tanggal', [$tgl_awal, $tgl_akhir])
                    ->where('status_id', 2); // Hanya memfilter bentrok dengan status disetujui (opsional, disesuaikan query asli)
            });
        }

        $ruangan = $query->get();

        // Attach foto_utama manually since subquery is complex
        foreach ($ruangan as $r) {
            $r->foto_utama = DB::table('ruangan_foto')
                ->where('ruangan_id', $r->id)
                ->orderByDesc('id')
                ->value('nama_file');
        }

        $gedungList = DB::table('gedung')->orderBy('id')->get();

        return view('mahasiswa.dashboard', compact('heroImages', 'ruangan', 'gedungList', 'tgl_awal', 'tgl_akhir', 'gedung'));
    }

    /**
     * Halaman Ruangan Grid
     */
    public function ruangan()
    {
        $heroImg = DB::table('ruangan_foto')
            ->whereNotNull('nama_file')
            ->where('nama_file', '!=', '')
            ->orderByDesc('id')
            ->value('nama_file');

        $ruangans = DB::table('ruangan as r')
            ->select('r.*', 'g.nama_gedung as gedung', 'l.nomor as Lantai')
            ->leftJoin('lantai as l', 'l.id', '=', 'r.lantai_id')
            ->leftJoin('gedung as g', 'g.id', '=', 'l.gedung_id')
            ->orderBy('r.nama_ruangan')
            ->get();

        foreach ($ruangans as $r) {
            $r->foto_utama = DB::table('ruangan_foto')
                ->where('ruangan_id', $r->id)
                ->orderByDesc('id')
                ->value('nama_file');
        }

        return view('mahasiswa.ruangan', compact('heroImg', 'ruangans'));
    }

    /**
     * Halaman Detail Ruangan
     */
    public function detailRuangan($id)
    {
        $ruangan = DB::table('ruangan as r')
            ->select('r.*', 'g.nama_gedung as gedung', 'l.nomor as Lantai')
            ->leftJoin('lantai as l', 'l.id', '=', 'r.lantai_id')
            ->leftJoin('gedung as g', 'g.id', '=', 'l.gedung_id')
            ->where('r.id', $id)
            ->first();

        if (!$ruangan) {
            abort(404, 'Ruangan tidak ditemukan');
        }

        $cover = DB::table('ruangan_foto')
            ->where('ruangan_id', $id)
            ->where('tipe', 'cover')
            ->orderByDesc('id')
            ->value('nama_file');

        $fotos = DB::table('ruangan_foto')
            ->where('ruangan_id', $id)
            ->where('tipe', 'detail')
            ->orderByDesc('id')
            ->pluck('nama_file')
            ->toArray();

        $fasilitas = DB::table('ruangan_fasilitas as rf')
            ->join('fasilitas as f', 'f.id', '=', 'rf.fasilitas_id')
            ->where('rf.ruangan_id', $id)
            ->orderBy('f.nama_fasilitas')
            ->get();

        $images = [];
        if (!empty($cover)) {
            $images[] = asset('storage/uploads/ruangan/' . $cover);
        } elseif (!empty($ruangan->foto)) {
            $images[] = asset('storage/uploads/ruangan/' . $ruangan->foto);
        }
        foreach ($fotos as $f) {
            $images[] = asset('storage/uploads/ruangan/' . $f);
        }

        return view('mahasiswa.detail_ruangan', compact('ruangan', 'images', 'fasilitas'));
    }

    /**
     * Halaman Peminjaman (Form & Riwayat)
     */
    public function peminjaman(Request $request)
    {
        $preselectRuanganId = $request->query('ruangan_id', 0);
        $userId = Auth::id();

        $ruanganList = DB::table('ruangan as r')
            ->select('r.id', 'r.nama_ruangan', 'r.kapasitas', 'g.nama_gedung as gedung')
            ->leftJoin('lantai as l', 'l.id', '=', 'r.lantai_id')
            ->leftJoin('gedung as g', 'g.id', '=', 'l.gedung_id')
            ->orderBy('g.nama_gedung')
            ->orderBy('r.nama_ruangan')
            ->get();

        $riwayat = DB::table('peminjaman as p')
            ->select('p.*', 'r.nama_ruangan', 'g.nama_gedung as gedung', 'sp.nama_status')
            ->join('ruangan as r', 'r.id', '=', 'p.ruangan_id')
            ->leftJoin('lantai as l', 'l.id', '=', 'r.lantai_id')
            ->leftJoin('gedung as g', 'g.id', '=', 'l.gedung_id')
            ->join('status_peminjaman as sp', 'sp.id', '=', 'p.status_id')
            ->where('p.user_id', $userId)
            ->orderByDesc('p.created_at')
            ->get();

        return view('mahasiswa.peminjaman', compact('ruanganList', 'riwayat', 'preselectRuanganId'));
    }

    /**
     * Simpan Pengajuan Peminjaman
     */
    public function storePeminjaman(Request $request)
    {
        $request->validate([
            'ruangan_id' => 'required|integer',
            'nama_kegiatan' => 'required|string|max:255',
            'tanggal' => 'required|date_format:Y-m-d',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'jumlah_peserta' => 'required|integer|min:1',
            'surat' => 'nullable|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'jam_selesai.after' => 'Jam selesai harus lebih besar dari jam mulai.',
            'jumlah_peserta.required' => 'Jumlah peserta wajib diisi.',
            'surat.mimes' => 'Format surat harus PDF/JPG/PNG.',
        ]);

        $userId = Auth::id();
        $ruangan = DB::table('ruangan')
            ->select('id', 'nama_ruangan', 'kapasitas')
            ->where('id', $request->ruangan_id)
            ->first();

        if (!$ruangan) {
            return back()->with('error', 'Ruangan tidak ditemukan.')->withInput();
        }

        if (!is_null($ruangan->kapasitas) && (int) $request->jumlah_peserta > (int) $ruangan->kapasitas) {
            return back()->with(
                'error',
                'Jumlah peserta melebihi kapasitas ruangan. Kapasitas ' . $ruangan->nama_ruangan . ' hanya ' . (int) $ruangan->kapasitas . ' orang.'
            )->withInput();
        }

        // Cek Bentrok (dengan peminjaman yang sudah disetujui = status 2)
        $conflict = DB::table('peminjaman')
            ->where('ruangan_id', $request->ruangan_id)
            ->where('tanggal', $request->tanggal)
            ->where('status_id', 2)
            ->where(function ($query) use ($request) {
                $query->whereNot(function ($q) use ($request) {
                    $q->where('jam_selesai', '<=', $request->jam_mulai)
                      ->orWhere('jam_mulai', '>=', $request->jam_selesai);
                });
            })
            ->exists();

        if ($conflict) {
            return back()->with('error', 'Jadwal bentrok. Ruangan sudah dipakai pada rentang jam tersebut (sudah disetujui).')->withInput();
        }

        $suratFilename = null;
        if ($request->hasFile('surat')) {
            $file = $request->file('surat');
            $suratFilename = 'surat_' . $userId . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('uploads/surat', $suratFilename, 'public');
        }

        // Insert
        $peminjamanId = DB::table('peminjaman')->insertGetId([
            'user_id' => $userId,
            'ruangan_id' => $request->ruangan_id,
            'nama_kegiatan' => $request->nama_kegiatan,
            'tanggal' => $request->tanggal,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'jumlah_peserta' => $request->jumlah_peserta,
            'surat' => $suratFilename,
            'status_id' => 1, // 1 = Menunggu
            'created_at' => now(),
        ]);

        DB::table('log_status')->insert([
            'peminjaman_id' => $peminjamanId,
            'status_id' => 1,
            'diubah_oleh' => $userId,
            'catatan' => 'Pengajuan dibuat oleh mahasiswa',
        ]);

        return redirect()->route('mahasiswa.peminjaman')->with('success', 'Pengajuan berhasil dibuat dan masuk antrian (Menunggu).');
    }

    /**
     * Batalkan Peminjaman
     */
    public function cancelPeminjaman(Request $request)
    {
        $id = $request->input('peminjaman_id');
        $userId = Auth::id();

        // Cari ID untuk status "dibatalkan" atau fallback "ditolak"
        $cancelStatusRow = DB::table('status_peminjaman')
            ->whereIn(DB::raw('LOWER(nama_status)'), ['dibatalkan', 'ditolak'])
            ->orderByRaw("CASE LOWER(nama_status) WHEN 'dibatalkan' THEN 1 WHEN 'ditolak' THEN 2 ELSE 3 END")
            ->first();

        $cancelStatusId = $cancelStatusRow ? $cancelStatusRow->id : 3;

        $peminjaman = DB::table('peminjaman')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->where('status_id', 1)
            ->first();

        if (!$peminjaman) {
            return back()->with('error', 'Gagal membatalkan. Pastikan status masih Menunggu dan milik Anda.');
        }

        DB::table('peminjaman')->where('id', $id)->update([
            'status_id' => $cancelStatusId,
            'catatan_admin' => DB::raw("IFNULL(NULLIF(catatan_admin, ''), 'Dibatalkan oleh mahasiswa')"),
        ]);

        DB::table('log_status')->insert([
            'peminjaman_id' => $id,
            'status_id' => $cancelStatusId,
            'diubah_oleh' => $userId,
            'catatan' => 'Dibatalkan oleh mahasiswa',
        ]);

        return back()->with('success', 'Pengajuan berhasil dibatalkan.');
    }

    // Pengaturan Akun
    public function profil()
    {   $user = Auth::user();

        return view('mahasiswa.profil', compact('user'));
    }

    public function ubahProfil(Request $request)
    {   $user = Auth::user();

        $validated_request = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($user->getKey(), 'id')],
            'email' => ['nullable', 'string', 'max:255', Rule::unique('users', 'email')->ignore($user->getKey(), 'id')],
            'foto_profil' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'current_password' => ['required_with:password', 'nullable', 'current_password'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        $user->nama = $validated_request['nama'];
        $user->username = $validated_request['username'];
        $user->email = $validated_request['email'];

        if ($request->hasFile('foto_profil')) {
            if (!empty($user->foto_profil)) {
                Storage::disk('public')->delete('uploads/profil/' . $user->foto_profil);
            }

            $fotoProfil = $request->file('foto_profil');
            $fotoProfilName = 'profil_' . $user->id . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $fotoProfil->getClientOriginalExtension();
            $fotoProfil->storeAs('uploads/profil', $fotoProfilName, 'public');
            $user->foto_profil = $fotoProfilName;
        }

        if ($request->filled('password'))
        {   $user->password = Hash::make($validated_request['password']);
        }

        $user->save();
        return back()->with('success', 'Profil sukses diubah');
    }
}
