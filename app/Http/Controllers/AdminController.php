<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard()
    {
        // 1. Ruangan Status (Terpakai/Tersedia)
        $ruanganStatus = DB::selectOne("
            SELECT SUM(terpakai) AS ruangan_terpakai, SUM(1 - terpakai) AS ruangan_tersedia
            FROM (
               SELECT r.id, MAX(CASE WHEN p.status_id = 2 AND p.tanggal = CURDATE() AND CURTIME() BETWEEN p.jam_mulai AND p.jam_selesai THEN 1 ELSE 0 END) AS terpakai
               FROM ruangan r LEFT JOIN peminjaman p ON p.ruangan_id = r.id GROUP BY r.id
            ) t
        ");

        // 2. Summary Hari Ini
        $todaySummary = DB::selectOne("
            SELECT COUNT(*) AS booking_hari_ini, 
                   SUM(status_id = 1) AS pending_hari_ini, 
                   SUM(status_id = 2) AS disetujui_hari_ini, 
                   SUM(status_id = 3) AS ditolak_hari_ini 
            FROM peminjaman WHERE tanggal = CURDATE()
        ");

        // 3. Pending Total
        $pendingTotalObj = DB::selectOne("SELECT COUNT(*) AS total FROM peminjaman WHERE status_id = 1");
        $pendingTotal = (int) ($pendingTotalObj->total ?? 0);

        // 4. Jadwal Hari Ini
        $jadwalHariIni = DB::select("
            SELECT p.jam_mulai, p.jam_selesai, p.nama_kegiatan, sp.nama_status, u.nama AS nama_peminjam, r.nama_ruangan, g.nama_gedung AS gedung
            FROM peminjaman p
            JOIN status_peminjaman sp ON sp.id = p.status_id
            JOIN users u ON u.id = p.user_id
            JOIN ruangan r ON r.id = p.ruangan_id
            LEFT JOIN lantai l ON l.id = r.lantai_id
            LEFT JOIN gedung g ON g.id = l.gedung_id
            WHERE p.tanggal = CURDATE()
            ORDER BY p.jam_mulai ASC, p.id ASC
        ");

        // 5. Pending List
        $pendingList = DB::select("
            SELECT p.id, p.tanggal, p.jam_mulai, p.jam_selesai, p.nama_kegiatan, u.nama AS nama_user, u.prodi, r.nama_ruangan, g.nama_gedung AS gedung
            FROM peminjaman p
            JOIN users u ON u.id = p.user_id
            JOIN ruangan r ON r.id = p.ruangan_id
            LEFT JOIN lantai l ON l.id = r.lantai_id
            LEFT JOIN gedung g ON g.id = l.gedung_id
            WHERE p.status_id = 1
            ORDER BY
                CASE
                    WHEN p.tanggal = CURDATE() THEN 0
                    WHEN p.tanggal > CURDATE() THEN 1
                    ELSE 2
                END ASC,
                CASE
                    WHEN p.tanggal = CURDATE() THEN ABS(TIMESTAMPDIFF(MINUTE, CURTIME(), p.jam_mulai))
                    ELSE 0
                END ASC,
                p.tanggal ASC,
                p.jam_mulai ASC,
                p.id ASC
            LIMIT 5
        ");

        $ruanganTerpakai = (int) ($ruanganStatus->ruangan_terpakai ?? 0);
        $ruanganTersedia = (int) ($ruanganStatus->ruangan_tersedia ?? 0);
        $bookingHariIni = (int) ($todaySummary->booking_hari_ini ?? 0);
        $pendingHariIni = (int) ($todaySummary->pending_hari_ini ?? 0);
        $disetujuiHariIni = (int) ($todaySummary->disetujui_hari_ini ?? 0);
        $ditolakHariIni = (int) ($todaySummary->ditolak_hari_ini ?? 0);

        return view('admin.dashboard', compact(
            'pendingTotal', 'ruanganTerpakai', 'ruanganTersedia',
            'bookingHariIni', 'pendingHariIni', 'disetujuiHariIni', 'ditolakHariIni',
            'jadwalHariIni', 'pendingList'
        ));
    }

    public function persetujuan()
    {
        $pending = DB::select("
            SELECT p.id, p.nama_kegiatan, p.tanggal, p.jam_mulai, p.jam_selesai, p.jumlah_peserta, p.surat,
                   r.nama_ruangan, g.nama_gedung AS gedung,
                   u.nama AS nama_user, u.username AS username_user, u.prodi AS prodi_user
            FROM peminjaman p
            JOIN ruangan r ON r.id = p.ruangan_id
            LEFT JOIN lantai l ON l.id = r.lantai_id
            LEFT JOIN gedung g ON g.id = l.gedung_id
            JOIN users u ON u.id = p.user_id
            WHERE p.status_id = 1
            ORDER BY p.tanggal ASC, p.jam_mulai ASC, p.id ASC
        ");

        return view('admin.persetujuan', compact('pending'));
    }

    public function processApproval(Request $request)
    {
        $action = $request->input('action');
        $id = (int) $request->input('peminjaman_id');
        $catatan = trim($request->input('catatan_admin') ?? '');
        $adminId = Auth::id();

        if ($id <= 0) {
            return back()->with('flash_error', 'ID peminjaman tidak valid.');
        }

        DB::beginTransaction();
        try {
            $target = DB::table('peminjaman')->where('id', $id)->lockForUpdate()->first();

            if (!$target) {
                throw new \Exception('Data peminjaman tidak ditemukan.');
            } elseif ((int) $target->status_id !== 1) {
                throw new \Exception('Pengajuan ini sudah diproses.');
            } elseif ($action === 'approve') {
                DB::table('peminjaman')->where('id', $id)->where('status_id', 1)->update([
                    'status_id' => 2,
                    'catatan_admin' => $catatan
                ]);
                $noteApprove = $catatan !== '' ? $catatan : 'Disetujui dari dashboard admin';
                DB::table('log_status')->insert([
                    'peminjaman_id' => $id, 'status_id' => 2, 'diubah_oleh' => $adminId, 'catatan' => $noteApprove
                ]);

                $conflictIds = DB::select("
                    SELECT id FROM peminjaman
                    WHERE status_id = 1 AND id <> ? AND ruangan_id = ? AND tanggal = ?
                    AND NOT (? >= jam_selesai OR ? <= jam_mulai)
                ", [$id, $target->ruangan_id, $target->tanggal, $target->jam_mulai, $target->jam_selesai]);

                DB::statement("
                    UPDATE peminjaman
                    SET status_id = 3, catatan_admin = IFNULL(NULLIF(catatan_admin, ''), 'Auto-ditolak: bentrok jadwal')
                    WHERE status_id = 1 AND id <> ? AND ruangan_id = ? AND tanggal = ?
                    AND NOT (? >= jam_selesai OR ? <= jam_mulai)
                ", [$id, $target->ruangan_id, $target->tanggal, $target->jam_mulai, $target->jam_selesai]);

                foreach ($conflictIds as $conflict) {
                    DB::table('log_status')->insert([
                        'peminjaman_id' => $conflict->id, 'status_id' => 3, 'diubah_oleh' => $adminId, 'catatan' => 'Auto-ditolak karena bentrok jadwal'
                    ]);
                }

                DB::commit();
                return back()->with('flash_success', 'Pengajuan disetujui. Pengajuan bentrok otomatis ditolak.');

            } elseif ($action === 'reject') {
                DB::table('peminjaman')->where('id', $id)->where('status_id', 1)->update([
                    'status_id' => 3,
                    'catatan_admin' => $catatan
                ]);
                $noteReject = $catatan !== '' ? $catatan : 'Ditolak dari dashboard admin';
                DB::table('log_status')->insert([
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
