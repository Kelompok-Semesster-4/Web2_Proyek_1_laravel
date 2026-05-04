<?php

namespace App\Http\Controllers;

use App\Models\Fasilitas;
use App\Models\Gedung;
use App\Models\LogStatus;
use App\Models\Peminjaman;
use App\Models\Ruangan;
use App\Models\RuanganFoto;
use App\Models\StatusPeminjaman;
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
        $validated = $request->validate([
            'tgl_awal' => ['nullable', 'date_format:Y-m-d'],
            'tgl_akhir' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:tgl_awal'],
            'gedung' => ['nullable', 'string', 'max:100'],
        ]);

        $heroImages = RuanganFoto::query()
            ->whereNotNull('nama_file')
            ->where('nama_file', '!=', '')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        $tgl_awal = $validated['tgl_awal'] ?? null;
        $tgl_akhir = $validated['tgl_akhir'] ?? null;
        $gedung = $validated['gedung'] ?? null;

        $query = Ruangan::query()
            ->from('ruangan as r')
            ->select('r.*', 'g.nama_gedung as gedung', 'l.nomor as Lantai')
            ->leftJoin('lantai as l', 'l.id', '=', 'r.lantai_id')
            ->leftJoin('gedung as g', 'g.id', '=', 'l.gedung_id')
            ->orderBy('r.nama_ruangan');

        if ($gedung) {
            $query->where('g.nama_gedung', $gedung);
        }

        if ($tgl_awal && $tgl_akhir) {
            $query->whereNotExists(function ($q) use ($tgl_awal, $tgl_akhir) {
                $q->selectRaw('1')
                    ->from('peminjaman')
                    ->whereColumn('peminjaman.ruangan_id', 'r.id')
                    ->whereBetween('tanggal', [$tgl_awal, $tgl_akhir])
                    ->where('status_id', 2); // Hanya memfilter bentrok dengan status disetujui (opsional, disesuaikan query asli)
            });
        }

        $ruangan = $query->get();

        $fotoMap = RuanganFoto::query()
            ->whereIn('ruangan_id', $ruangan->pluck('id'))
            ->orderByDesc('id')
            ->get()
            ->groupBy('ruangan_id')
            ->map(fn ($items) => $items->first()->nama_file);

        foreach ($ruangan as $room) {
            $room->foto_utama = $fotoMap[$room->id] ?? null;
        }

        $gedungList = Gedung::orderBy('id')->get();

        return view('mahasiswa.dashboard', compact('heroImages', 'ruangan', 'gedungList', 'tgl_awal', 'tgl_akhir', 'gedung'));
    }

    /**
     * Halaman Ruangan Grid
     */
    public function ruangan()
    {
        $heroImg = RuanganFoto::query()
            ->whereNotNull('nama_file')
            ->where('nama_file', '!=', '')
            ->orderByDesc('id')
            ->value('nama_file');

        $ruangans = Ruangan::query()
            ->from('ruangan as r')
            ->select('r.*', 'g.nama_gedung as gedung', 'l.nomor as Lantai')
            ->leftJoin('lantai as l', 'l.id', '=', 'r.lantai_id')
            ->leftJoin('gedung as g', 'g.id', '=', 'l.gedung_id')
            ->orderBy('r.nama_ruangan')
            ->get();

        $fotoMap = RuanganFoto::query()
            ->whereIn('ruangan_id', $ruangans->pluck('id'))
            ->orderByDesc('id')
            ->get()
            ->groupBy('ruangan_id')
            ->map(fn ($items) => $items->first()->nama_file);

        foreach ($ruangans as $room) {
            $room->foto_utama = $fotoMap[$room->id] ?? null;
        }

        return view('mahasiswa.ruangan', compact('heroImg', 'ruangans'));
    }

    /**
     * Halaman Detail Ruangan
     */
    public function detailRuangan(Ruangan $ruangan)
    {
        $id = $ruangan->getKey();

        $ruangan = Ruangan::query()
            ->from('ruangan as r')
            ->select('r.*', 'g.nama_gedung as gedung', 'l.nomor as Lantai')
            ->leftJoin('lantai as l', 'l.id', '=', 'r.lantai_id')
            ->leftJoin('gedung as g', 'g.id', '=', 'l.gedung_id')
            ->where('r.id', $id)
            ->firstOrFail();

        $cover = RuanganFoto::query()
            ->where('ruangan_id', $id)
            ->where('tipe', 'cover')
            ->orderByDesc('id')
            ->value('nama_file');

        $fotos = RuanganFoto::query()
            ->where('ruangan_id', $id)
            ->where('tipe', 'detail')
            ->orderByDesc('id')
            ->pluck('nama_file')
            ->toArray();

        $fasilitas = Fasilitas::query()
            ->from('fasilitas as f')
            ->select('f.*')
            ->join('ruangan_fasilitas as rf', 'f.id', '=', 'rf.fasilitas_id')
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

        $ruanganList = Ruangan::query()
            ->from('ruangan as r')
            ->select('r.id', 'r.nama_ruangan', 'r.kapasitas', 'g.nama_gedung as gedung')
            ->leftJoin('lantai as l', 'l.id', '=', 'r.lantai_id')
            ->leftJoin('gedung as g', 'g.id', '=', 'l.gedung_id')
            ->orderBy('g.nama_gedung')
            ->orderBy('r.nama_ruangan')
            ->get();

        $riwayat = Peminjaman::query()
            ->from('peminjaman as p')
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
        $validated = $request->validate([
            'ruangan_id' => ['required', 'integer', 'exists:ruangan,id'],
            'nama_kegiatan' => ['required', 'string', 'max:255'],
            'tanggal' => ['required', 'date_format:Y-m-d'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'jumlah_peserta' => ['required', 'integer', 'min:1'],
            'surat' => ['nullable', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ], [
            'jam_selesai.after' => 'Jam selesai harus lebih besar dari jam mulai.',
            'jumlah_peserta.required' => 'Jumlah peserta wajib diisi.',
            'surat.mimes' => 'Format surat harus PDF/JPG/PNG.',
        ]);

        $userId = Auth::id();
        $ruangan = Ruangan::query()
            ->select('id', 'nama_ruangan', 'kapasitas')
            ->where('id', $validated['ruangan_id'])
            ->first();

        if (!is_null($ruangan->kapasitas) && (int) $validated['jumlah_peserta'] > (int) $ruangan->kapasitas) {
            return back()->with(
                'error',
                'Jumlah peserta melebihi kapasitas ruangan. Kapasitas ' . $ruangan->nama_ruangan . ' hanya ' . (int) $ruangan->kapasitas . ' orang.'
            )->withInput();
        }

        $conflict = Peminjaman::query()
            ->where('ruangan_id', $validated['ruangan_id'])
            ->where('tanggal', $validated['tanggal'])
            ->where('status_id', 2)
            ->where(function ($query) use ($validated) {
                $query->whereNot(function ($q) use ($validated) {
                    $q->where('jam_selesai', '<=', $validated['jam_mulai'])
                        ->orWhere('jam_mulai', '>=', $validated['jam_selesai']);
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

        DB::transaction(function () use ($validated, $suratFilename, $userId) {
            $peminjaman = Peminjaman::create([
                'user_id' => $userId,
                'ruangan_id' => $validated['ruangan_id'],
                'nama_kegiatan' => $validated['nama_kegiatan'],
                'tanggal' => $validated['tanggal'],
                'jam_mulai' => $validated['jam_mulai'],
                'jam_selesai' => $validated['jam_selesai'],
                'jumlah_peserta' => $validated['jumlah_peserta'],
                'surat' => $suratFilename,
                'status_id' => 1,
                'created_at' => now(),
            ]);

            LogStatus::create([
                'peminjaman_id' => $peminjaman->id,
                'status_id' => 1,
                'diubah_oleh' => $userId,
                'catatan' => 'Pengajuan dibuat oleh mahasiswa',
            ]);
        });

        return redirect()->route('mahasiswa.peminjaman')->with('success', 'Pengajuan berhasil dibuat dan masuk antrian (Menunggu).');
    }

    /**
     * Batalkan Peminjaman
     */
    public function cancelPeminjaman(Request $request)
    {
        $validated = $request->validate([
            'peminjaman_id' => ['required', 'integer', 'exists:peminjaman,id'],
        ]);

        $id = $validated['peminjaman_id'];
        $userId = Auth::id();

        $statusRows = StatusPeminjaman::all();
        $cancelStatusRow = $statusRows->first(function ($status) {
            return strtolower($status->nama_status) === 'dibatalkan';
        }) ?? $statusRows->first(function ($status) {
            return strtolower($status->nama_status) === 'ditolak';
        });

        $cancelStatusId = $cancelStatusRow ? $cancelStatusRow->id : 3;

        $peminjaman = Peminjaman::query()
            ->where('id', $id)
            ->where('user_id', $userId)
            ->where('status_id', 1)
            ->first();

        if (!$peminjaman) {
            return back()->with('error', 'Gagal membatalkan. Pastikan status masih Menunggu dan milik Anda.');
        }

        $peminjaman->update([
            'status_id' => $cancelStatusId,
            'catatan_admin' => $peminjaman->catatan_admin ?: 'Dibatalkan oleh mahasiswa',
        ]);

        LogStatus::create([
            'peminjaman_id' => $id,
            'status_id' => $cancelStatusId,
            'diubah_oleh' => $userId,
            'catatan' => 'Dibatalkan oleh mahasiswa',
        ]);

        return back()->with('success', 'Pengajuan berhasil dibatalkan.');
    }

    // Pengaturan Akun
    public function profil()
    {
        $user = Auth::user();

        return view('mahasiswa.profil', compact('user'));
    }

    public function ubahProfil(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($user->getKey(), 'id')],
            'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->getKey(), 'id')],
            'foto_profil' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'current_password' => ['required_with:password', 'nullable', 'current_password'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->nama = $validated['nama'];
        $user->username = $validated['username'];
        $user->email = $validated['email'] ?? null;

        if ($request->hasFile('foto_profil')) {
            if (!empty($user->foto_profil) && !filter_var($user->foto_profil, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete('uploads/profil/' . $user->foto_profil);
            }

            $fotoProfil = $request->file('foto_profil');
            $fotoProfilName = 'profil_' . $user->id . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $fotoProfil->getClientOriginalExtension();
            $fotoProfil->storeAs('uploads/profil', $fotoProfilName, 'public');
            $user->foto_profil = $fotoProfilName;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();
        return back()->with('success', 'Profil sukses diubah');
    }
}
