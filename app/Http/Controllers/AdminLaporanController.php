<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminLaporanController extends Controller
{
    public function index(Request $request)
    {
        $year = (int) $request->input('year', date('Y'));
        $month = (int) $request->input('month', date('n'));
        if ($year < 2000 || $year > 2100) $year = (int) date('Y');
        if ($month < 1 || $month > 12) $month = (int) date('n');

        $startDate = sprintf('%04d-%02d-01', $year, $month);
        $endDate = date('Y-m-t', strtotime($startDate));
        $yearStart = sprintf('%04d-01-01', $year);
        $yearEnd = sprintf('%04d-12-31', $year);

        $ruanganId = (int) $request->input('ruangan_id', 0);
        $statusId = (int) $request->input('status_id', 0);
        $sortBy = (string) $request->input('sort_by', 'tanggal');
        $sortDir = strtolower((string) $request->input('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        $allowedSorts = [
            'tanggal' => 'p.tanggal',
            'jam' => 'p.jam_mulai',
            'peminjam' => 'u.nama',
            'prodi' => 'u.prodi',
            'kegiatan' => 'p.nama_kegiatan',
            'peserta' => 'p.jumlah_peserta',
            'status' => 'sp.nama_status',
        ];
        if (!array_key_exists($sortBy, $allowedSorts)) {
            $sortBy = 'tanggal';
        }
        $sortColumn = $allowedSorts[$sortBy];

        $reportWhere = 'p.tanggal BETWEEN ? AND ?';
        $reportParams = [$startDate, $endDate];
        if ($ruanganId > 0) {
            $reportWhere .= ' AND p.ruangan_id = ?';
            $reportParams[] = $ruanganId;
        }
        if ($statusId > 0) {
            $reportWhere .= ' AND p.status_id = ?';
            $reportParams[] = $statusId;
        }

        $ruanganList = DB::select("
            SELECT r.id, r.nama_ruangan, g.nama_gedung AS gedung 
            FROM ruangan r 
            LEFT JOIN lantai l ON l.id = r.lantai_id 
            LEFT JOIN gedung g ON g.id = l.gedung_id 
            ORDER BY g.nama_gedung, r.nama_ruangan
        ");
        $statusList = DB::select('SELECT id, nama_status FROM status_peminjaman ORDER BY id');

        $totalRequestsObj = collect(DB::select("SELECT COUNT(*) AS total FROM peminjaman p WHERE $reportWhere", $reportParams))->first();
        $totalRequests = (int) ($totalRequestsObj->total ?? 0);

        $statusCountsRaw = DB::select("
            SELECT sp.id, sp.nama_status, COUNT(p.id) AS jumlah 
            FROM status_peminjaman sp 
            LEFT JOIN peminjaman p ON p.status_id = sp.id AND $reportWhere 
            GROUP BY sp.id, sp.nama_status 
            ORDER BY sp.id
        ", $reportParams);

        $statusCounts = [];
        foreach ($statusCountsRaw as $row) {
            $statusCounts[(int) $row->id] = ['nama' => $row->nama_status, 'jumlah' => (int) $row->jumlah];
        }

        $approvedCount = ($statusCounts[2]['jumlah'] ?? 0) + ($statusCounts[4]['jumlah'] ?? 0);
        $rejectedCount = $statusCounts[3]['jumlah'] ?? 0;
        $approvalRate = $totalRequests > 0 ? round(($approvedCount / $totalRequests) * 100, 1) : 0.0;
        $rejectionRate = $totalRequests > 0 ? round(($rejectedCount / $totalRequests) * 100, 1) : 0.0;

        $durationWhere = $reportWhere . ' AND p.status_id IN (2,4)';
        
        $durasiObj = collect(DB::select("
            SELECT COALESCE(SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(p.jam_selesai, p.jam_mulai)))), '00:00:00') AS total_jam,
                   COALESCE(SEC_TO_TIME(AVG(TIME_TO_SEC(TIMEDIFF(p.jam_selesai, p.jam_mulai)))), '00:00:00') AS avg_durasi
            FROM peminjaman p WHERE $durationWhere
        ", $reportParams))->first();

        $totalJam = preg_replace('/\.\d+$/', '', $durasiObj->total_jam ?? '00:00:00');
        $avgDurasi = preg_replace('/\.\d+$/', '', $durasiObj->avg_durasi ?? '00:00:00');

        $dailyActivity = DB::select("
            SELECT p.tanggal, COUNT(*) AS total_pengajuan 
            FROM peminjaman p WHERE $reportWhere 
            GROUP BY p.tanggal ORDER BY p.tanggal ASC
        ", $reportParams);

        $yearTrendWhere = 'p.tanggal BETWEEN ? AND ?';
        $yearTrendParams = [$yearStart, $yearEnd];
        if ($ruanganId > 0) {
            $yearTrendWhere .= ' AND p.ruangan_id = ?';
            $yearTrendParams[] = $ruanganId;
        }
        if ($statusId > 0) {
            $yearTrendWhere .= ' AND p.status_id = ?';
            $yearTrendParams[] = $statusId;
        }

        $yearlyTrendRaw = DB::select("
            SELECT MONTH(p.tanggal) AS month_num, COUNT(*) AS total_pengajuan, 
                   SUM(p.status_id = 2) AS disetujui, SUM(p.status_id = 3) AS ditolak 
            FROM peminjaman p WHERE $yearTrendWhere 
            GROUP BY MONTH(p.tanggal) ORDER BY MONTH(p.tanggal)
        ", $yearTrendParams);

        $monthlyVisitsRaw = DB::select("
            SELECT MONTH(p.tanggal) AS month_num, COUNT(DISTINCT p.user_id) AS total_kunjungan 
            FROM peminjaman p WHERE p.tanggal BETWEEN ? AND ? 
            GROUP BY MONTH(p.tanggal) ORDER BY MONTH(p.tanggal)
        ", [$yearStart, $yearEnd]);

        $topRuangan = DB::select("
            SELECT r.id, g.nama_gedung AS gedung, r.nama_ruangan, COUNT(p.id) AS jumlah_booking, 
                   COALESCE(SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(p.jam_selesai, p.jam_mulai)))), '00:00:00') AS total_jam 
            FROM peminjaman p 
            JOIN ruangan r ON r.id = p.ruangan_id 
            LEFT JOIN lantai l ON l.id = r.lantai_id 
            LEFT JOIN gedung g ON g.id = l.gedung_id 
            WHERE $durationWhere 
            GROUP BY r.id, g.nama_gedung, r.nama_ruangan 
            ORDER BY jumlah_booking DESC LIMIT 10
        ", $reportParams);

        foreach ($topRuangan as &$room) {
            $room->total_jam = preg_replace('/\.\d+$/', '', $room->total_jam ?? '00:00:00');
        }
        unset($room);

        $userDistributionObj = collect(DB::select("
            SELECT SUM(role = 'admin') AS admin_total, SUM(role = 'mahasiswa') AS mahasiswa_total FROM users
        "))->first();

        $hoursUsedSecondsObj = collect(DB::select("
            SELECT COALESCE(SUM(TIME_TO_SEC(TIMEDIFF(p.jam_selesai, p.jam_mulai))), 0) AS total_detik 
            FROM peminjaman p WHERE $durationWhere
        ", $reportParams))->first();
        $hoursUsedSeconds = (int) ($hoursUsedSecondsObj->total_detik ?? 0);

        $roomCountObj = collect(DB::select('SELECT COUNT(*) AS total FROM ruangan'))->first();
        $roomCount = $ruanganId > 0 ? 1 : (int) ($roomCountObj->total ?? 0);
        
        $capacityHours = max(1.0, $roomCount * ((int) date('t', strtotime($startDate))) * 12.0);
        $usedHours = round($hoursUsedSeconds / 3600, 2);
        $utilizationRate = round(min(100, ($usedHours / $capacityHours) * 100), 1);

        $detail = DB::select("
            SELECT p.id, p.tanggal, p.jam_mulai, p.jam_selesai, p.nama_kegiatan, p.jumlah_peserta, p.catatan_admin, 
                   sp.nama_status, u.nama AS nama_peminjam, u.prodi, g.nama_gedung AS gedung, r.nama_ruangan 
            FROM peminjaman p 
            JOIN users u ON u.id = p.user_id 
            JOIN ruangan r ON r.id = p.ruangan_id 
            LEFT JOIN lantai l ON l.id = r.lantai_id 
            LEFT JOIN gedung g ON g.id = l.gedung_id 
            JOIN status_peminjaman sp ON sp.id = p.status_id 
            WHERE $reportWhere 
            ORDER BY $sortColumn " . strtoupper($sortDir) . ",
                     p.tanggal DESC, p.jam_mulai DESC, p.id DESC
        ", $reportParams);

        if ($request->input('export') === 'csv') {
            return response()->streamDownload(function() use ($detail) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['ID', 'Tanggal', 'Jam Mulai', 'Jam Selesai', 'Durasi (menit)', 'Ruangan', 'Peminjam', 'Prodi', 'Kegiatan', 'Peserta', 'Status', 'Catatan']);
                foreach ($detail as $d) {
                    $durMin = 0;
                    if (!empty($d->jam_mulai) && !empty($d->jam_selesai)) {
                        $durMin = (int) round((strtotime($d->tanggal . ' ' . $d->jam_selesai) - strtotime($d->tanggal . ' ' . $d->jam_mulai)) / 60);
                    }
                    fputcsv($out, [
                        $d->id, $d->tanggal, substr($d->jam_mulai, 0, 5), substr($d->jam_selesai, 0, 5), 
                        $durMin, ($d->gedung ?? '-') . ' - ' . $d->nama_ruangan, $d->nama_peminjam, $d->prodi ?? '-', 
                        $d->nama_kegiatan, $d->jumlah_peserta ?? '', $d->nama_status, $d->catatan_admin ?? ''
                    ]);
                }
                fclose($out);
            }, 'laporan_' . $year . '_' . sprintf('%02d', $month) . '.csv', [
                'Content-Type' => 'text/csv; charset=utf-8',
            ]);
        }

        $yearlyTrendMap = [];
        foreach ($yearlyTrendRaw as $row) $yearlyTrendMap[(int) $row->month_num] = $row;
        $monthlyVisitMap = [];
        foreach ($monthlyVisitsRaw as $row) $monthlyVisitMap[(int) $row->month_num] = $row;

        $monthLabels = [];
        $trendTotalData = [];
        $trendApprovedData = [];
        $trendRejectedData = [];
        $visitData = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthLabels[] = date('M', mktime(0, 0, 0, $m, 1));
            $trendRow = $yearlyTrendMap[$m] ?? null;
            $visitRow = $monthlyVisitMap[$m] ?? null;
            $trendTotalData[] = (int) ($trendRow->total_pengajuan ?? 0);
            $trendApprovedData[] = (int) ($trendRow->disetujui ?? 0);
            $trendRejectedData[] = (int) ($trendRow->ditolak ?? 0);
            $visitData[] = (int) ($visitRow->total_kunjungan ?? 0);
        }

        $dailyLabels = [];
        $dailyTotals = [];
        foreach ($dailyActivity as $d) {
            $dailyLabels[] = date('d M', strtotime($d->tanggal));
            $dailyTotals[] = (int) ($d->total_pengajuan ?? 0);
        }

        return view('admin.laporan', compact(
            'year', 'month', 'startDate', 'ruanganId', 'statusId',
            'sortBy', 'sortDir',
            'ruanganList', 'statusList', 'totalRequests', 'approvalRate', 'rejectionRate',
            'avgDurasi', 'totalJam', 'usedHours', 'capacityHours', 'utilizationRate',
            'topRuangan', 'detail', 'monthLabels', 'trendTotalData', 'trendApprovedData', 'trendRejectedData',
            'dailyLabels', 'dailyTotals', 'visitData', 'userDistributionObj'
        ));
    }
}
