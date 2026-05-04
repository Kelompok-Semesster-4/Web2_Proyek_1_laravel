<?php

namespace App\Http\Controllers;

use App\Models\LogStatus;
use App\Models\Peminjaman;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard()
    {
        $today = now()->toDateString();
        $currentTime = now()->format('H:i:s');

        $ruanganTerpakai = Ruangan::query()
            ->from('ruangan as r')
            ->whereExists(function ($query) use ($today, $currentTime) {
                $query->selectRaw('1')
                    ->from('peminjaman as p')
                    ->whereColumn('p.ruangan_id', 'r.id')
                    ->where('p.status_id', 2)
                    ->whereDate('p.tanggal', $today)
                    ->where('p.jam_mulai', '<=', $currentTime)
                    ->where('p.jam_selesai', '>=', $currentTime);
            })
            ->count();

        $totalRuangan = Ruangan::count();
        $ruanganTersedia = max(0, $totalRuangan - $ruanganTerpakai);

        $bookingHariIni = Peminjaman::whereDate('tanggal', $today)->count();
        $pendingHariIni = Peminjaman::whereDate('tanggal', $today)->where('status_id', 1)->count();
        $disetujuiHariIni = Peminjaman::whereDate('tanggal', $today)->where('status_id', 2)->count();
        $ditolakHariIni = Peminjaman::whereDate('tanggal', $today)->where('status_id', 3)->count();
        $pendingTotal = Peminjaman::where('status_id', 1)->count();

        $jadwalHariIni = Peminjaman::query()
            ->from('peminjaman as p')
            ->select(
                'p.jam_mulai',
                'p.jam_selesai',
                'p.nama_kegiatan',
                'sp.nama_status',
                'u.nama as nama_peminjam',
                'r.nama_ruangan',
                'g.nama_gedung as gedung'
            )
            ->join('status_peminjaman as sp', 'sp.id', '=', 'p.status_id')
            ->join('users as u', 'u.id', '=', 'p.user_id')
            ->join('ruangan as r', 'r.id', '=', 'p.ruangan_id')
            ->leftJoin('lantai as l', 'l.id', '=', 'r.lantai_id')
            ->leftJoin('gedung as g', 'g.id', '=', 'l.gedung_id')
            ->whereDate('p.tanggal', $today)
            ->orderBy('p.jam_mulai')
            ->orderBy('p.id')
            ->get();

        $pendingList = Peminjaman::query()
            ->from('peminjaman as p')
            ->select(
                'p.id',
                'p.tanggal',
                'p.jam_mulai',
                'p.jam_selesai',
                'p.nama_kegiatan',
                'u.nama as nama_user',
                'u.prodi',
                'r.nama_ruangan',
                'g.nama_gedung as gedung'
            )
            ->join('users as u', 'u.id', '=', 'p.user_id')
            ->join('ruangan as r', 'r.id', '=', 'p.ruangan_id')
            ->leftJoin('lantai as l', 'l.id', '=', 'r.lantai_id')
            ->leftJoin('gedung as g', 'g.id', '=', 'l.gedung_id')
            ->where('p.status_id', 1)
            ->orderByRaw("
                CASE
                    WHEN p.tanggal = ? THEN 0
                    WHEN p.tanggal > ? THEN 1
                    ELSE 2
                END ASC
            ", [$today, $today])
            ->orderByRaw("
                CASE
                    WHEN p.tanggal = ? THEN ABS(TIMESTAMPDIFF(MINUTE, ?, p.jam_mulai))
                    ELSE 0
                END ASC
            ", [$today, $currentTime])
            ->orderBy('p.tanggal')
            ->orderBy('p.jam_mulai')
            ->orderBy('p.id')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'pendingTotal', 'ruanganTerpakai', 'ruanganTersedia',
            'bookingHariIni', 'pendingHariIni', 'disetujuiHariIni', 'ditolakHariIni',
            'jadwalHariIni', 'pendingList'
        ));
    }

    public function persetujuan()
    {
        $pending = Peminjaman::query()
            ->from('peminjaman as p')
            ->select(
                'p.id',
                'p.nama_kegiatan',
                'p.tanggal',
                'p.jam_mulai',
                'p.jam_selesai',
                'p.jumlah_peserta',
                'p.surat',
                'r.nama_ruangan',
                'g.nama_gedung as gedung',
                'u.nama as nama_user',
                'u.username as username_user',
                'u.prodi as prodi_user'
            )
            ->join('ruangan as r', 'r.id', '=', 'p.ruangan_id')
            ->leftJoin('lantai as l', 'l.id', '=', 'r.lantai_id')
            ->leftJoin('gedung as g', 'g.id', '=', 'l.gedung_id')
            ->join('users as u', 'u.id', '=', 'p.user_id')
            ->where('p.status_id', 1)
            ->orderBy('p.tanggal')
            ->orderBy('p.jam_mulai')
            ->orderBy('p.id')
            ->get();

        return view('admin.persetujuan', compact('pending'));
    }

    public function processApproval(Request $request)
    {
        $validated = $request->validate([
            'action' => ['required', 'in:approve,reject'],
            'peminjaman_id' => ['required', 'integer', 'exists:peminjaman,id'],
            'catatan_admin' => ['nullable', 'string', 'max:1000'],
        ]);

        $action = $validated['action'];
        $id = (int) $validated['peminjaman_id'];
        $catatan = trim($validated['catatan_admin'] ?? '');
        $adminId = Auth::id();

        DB::beginTransaction();
        try {
            $target = Peminjaman::where('id', $id)->lockForUpdate()->first();

            if (!$target) {
                throw new \Exception('Data peminjaman tidak ditemukan.');
            } elseif ((int) $target->status_id !== 1) {
                throw new \Exception('Pengajuan ini sudah diproses.');
            } elseif ($action === 'approve') {
                $target->update([
                    'status_id' => 2,
                    'catatan_admin' => $catatan
                ]);
                $noteApprove = $catatan !== '' ? $catatan : 'Disetujui dari dashboard admin';
                LogStatus::create([
                    'peminjaman_id' => $id, 'status_id' => 2, 'diubah_oleh' => $adminId, 'catatan' => $noteApprove
                ]);

                $conflicts = Peminjaman::query()
                    ->select('id', 'catatan_admin')
                    ->where('status_id', 1)
                    ->where('id', '<>', $id)
                    ->where('ruangan_id', $target->ruangan_id)
                    ->whereDate('tanggal', $target->tanggal)
                    ->whereNot(function ($query) use ($target) {
                        $query->where('jam_mulai', '>=', $target->jam_selesai)
                            ->orWhere('jam_selesai', '<=', $target->jam_mulai);
                    })
                    ->get();

                foreach ($conflicts as $conflict) {
                    $conflict->update([
                        'status_id' => 3,
                        'catatan_admin' => $conflict->catatan_admin ?: 'Auto-ditolak: bentrok jadwal',
                    ]);

                    LogStatus::create([
                        'peminjaman_id' => $conflict->id, 'status_id' => 3, 'diubah_oleh' => $adminId, 'catatan' => 'Auto-ditolak karena bentrok jadwal'
                    ]);
                }

                DB::commit();
                return back()->with('flash_success', 'Pengajuan disetujui. Pengajuan bentrok otomatis ditolak.');

            } elseif ($action === 'reject') {
                $target->update([
                    'status_id' => 3,
                    'catatan_admin' => $catatan
                ]);
                $noteReject = $catatan !== '' ? $catatan : 'Ditolak dari dashboard admin';
                LogStatus::create([
                    'peminjaman_id' => $id, 'status_id' => 3, 'diubah_oleh' => $adminId, 'catatan' => $noteReject
                ]);
                DB::commit();
                return back()->with('flash_success', 'Pengajuan berhasil ditolak.');
            } else {
                throw new \Exception('Aksi tidak dikenal.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('flash_error', $e->getMessage());
        }
    }
}
