<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Ruangan;
use App\Models\StatusPeminjaman;
use App\Models\User;
use Illuminate\Http\Request;

class AdminLaporanController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'year' => ['nullable', 'integer', 'between:2000,2100'],
            'month' => ['nullable', 'integer', 'between:1,12'],
            'ruangan_id' => ['nullable', 'integer'],
            'status_id' => ['nullable', 'integer'],
            'sort_by' => ['nullable', 'string'],
            'sort_dir' => ['nullable', 'in:asc,desc'],
            'export' => ['nullable', 'in:csv'],
        ]);

        $year = (int) ($validated['year'] ?? date('Y'));
        $month = (int) ($validated['month'] ?? date('n'));
        if ($year < 2000 || $year > 2100) $year = (int) date('Y');
        if ($month < 1 || $month > 12) $month = (int) date('n');

        $startDate = sprintf('%04d-%02d-01', $year, $month);
        $endDate = date('Y-m-t', strtotime($startDate));
        $yearStart = sprintf('%04d-01-01', $year);
        $yearEnd = sprintf('%04d-12-31', $year);

        $ruanganId = (int) ($validated['ruangan_id'] ?? 0);
        $statusId = (int) ($validated['status_id'] ?? 0);
        $sortBy = (string) ($validated['sort_by'] ?? 'tanggal');
        $sortDir = strtolower((string) ($validated['sort_dir'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';

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

        $reportQuery = function () use ($startDate, $endDate, $ruanganId, $statusId) {
            return Peminjaman::query()
                ->from('peminjaman as p')
                ->whereBetween('p.tanggal', [$startDate, $endDate])
                ->when($ruanganId > 0, fn ($query) => $query->where('p.ruangan_id', $ruanganId))
                ->when($statusId > 0, fn ($query) => $query->where('p.status_id', $statusId));
        };

        $approvedReportQuery = function () use ($reportQuery) {
            return $reportQuery()->whereIn('p.status_id', [2, 4]);
        };

        $ruanganList = Ruangan::query()
            ->from('ruangan as r')
            ->select('r.id', 'r.nama_ruangan', 'g.nama_gedung as gedung')
            ->leftJoin('lantai as l', 'l.id', '=', 'r.lantai_id')
            ->leftJoin('gedung as g', 'g.id', '=', 'l.gedung_id')
            ->orderBy('g.nama_gedung')
            ->orderBy('r.nama_ruangan')
            ->get();

        $statusList = StatusPeminjaman::query()
            ->select('id', 'nama_status')
            ->orderBy('id')
            ->get();

        $totalRequests = $reportQuery()->count();

        $statusCountsRaw = StatusPeminjaman::query()
            ->from('status_peminjaman as sp')
            ->select('sp.id', 'sp.nama_status')
            ->selectRaw('COUNT(p.id) as jumlah')
            ->leftJoin('peminjaman as p', function ($join) use ($startDate, $endDate, $ruanganId, $statusId) {
                $join->on('p.status_id', '=', 'sp.id')
                    ->whereBetween('p.tanggal', [$startDate, $endDate]);

                if ($ruanganId > 0) {
                    $join->where('p.ruangan_id', $ruanganId);
                }

                if ($statusId > 0) {
                    $join->where('p.status_id', $statusId);
                }
            })
            ->groupBy('sp.id', 'sp.nama_status')
            ->orderBy('sp.id')
            ->get();

        $statusCounts = [];
        foreach ($statusCountsRaw as $row) {
            $statusCounts[(int) $row->id] = ['nama' => $row->nama_status, 'jumlah' => (int) $row->jumlah];
        }

        $approvedCount = ($statusCounts[2]['jumlah'] ?? 0) + ($statusCounts[4]['jumlah'] ?? 0);
        $rejectedCount = $statusCounts[3]['jumlah'] ?? 0;
        $approvalRate = $totalRequests > 0 ? round(($approvedCount / $totalRequests) * 100, 1) : 0.0;
        $rejectionRate = $totalRequests > 0 ? round(($rejectedCount / $totalRequests) * 100, 1) : 0.0;

        $durasiObj = $approvedReportQuery()
            ->selectRaw("COALESCE(SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(p.jam_selesai, p.jam_mulai)))), '00:00:00') as total_jam")
            ->selectRaw("COALESCE(SEC_TO_TIME(AVG(TIME_TO_SEC(TIMEDIFF(p.jam_selesai, p.jam_mulai)))), '00:00:00') as avg_durasi")
            ->first();

        $totalJam = preg_replace('/\.\d+$/', '', $durasiObj->total_jam ?? '00:00:00');
        $avgDurasi = preg_replace('/\.\d+$/', '', $durasiObj->avg_durasi ?? '00:00:00');

        $dailyActivity = $reportQuery()
            ->select('p.tanggal')
            ->selectRaw('COUNT(*) as total_pengajuan')
            ->groupBy('p.tanggal')
            ->orderBy('p.tanggal')
            ->get();

        $yearlyTrendRaw = Peminjaman::query()
            ->from('peminjaman as p')
            ->selectRaw('MONTH(p.tanggal) as month_num')
            ->selectRaw('COUNT(*) as total_pengajuan')
            ->selectRaw('SUM(CASE WHEN p.status_id = 2 THEN 1 ELSE 0 END) as disetujui')
            ->selectRaw('SUM(CASE WHEN p.status_id = 3 THEN 1 ELSE 0 END) as ditolak')
            ->whereBetween('p.tanggal', [$yearStart, $yearEnd])
            ->when($ruanganId > 0, fn ($query) => $query->where('p.ruangan_id', $ruanganId))
            ->when($statusId > 0, fn ($query) => $query->where('p.status_id', $statusId))
            ->groupByRaw('MONTH(p.tanggal)')
            ->orderByRaw('MONTH(p.tanggal)')
            ->get();

        $monthlyVisitsRaw = Peminjaman::query()
            ->from('peminjaman as p')
            ->selectRaw('MONTH(p.tanggal) as month_num')
            ->selectRaw('COUNT(DISTINCT p.user_id) as total_kunjungan')
            ->whereBetween('p.tanggal', [$yearStart, $yearEnd])
            ->groupByRaw('MONTH(p.tanggal)')
            ->orderByRaw('MONTH(p.tanggal)')
            ->get();

        $topRuangan = $approvedReportQuery()
            ->select('r.id', 'g.nama_gedung as gedung', 'r.nama_ruangan')
            ->selectRaw('COUNT(p.id) as jumlah_booking')
            ->selectRaw("COALESCE(SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(p.jam_selesai, p.jam_mulai)))), '00:00:00') as total_jam")
            ->join('ruangan as r', 'r.id', '=', 'p.ruangan_id')
            ->leftJoin('lantai as l', 'l.id', '=', 'r.lantai_id')
            ->leftJoin('gedung as g', 'g.id', '=', 'l.gedung_id')
            ->groupBy('r.id', 'g.nama_gedung', 'r.nama_ruangan')
            ->orderByDesc('jumlah_booking')
            ->limit(10)
            ->get();

        foreach ($topRuangan as &$room) {
            $room->total_jam = preg_replace('/\.\d+$/', '', $room->total_jam ?? '00:00:00');
        }
        unset($room);

        $userDistributionObj = User::query()
            ->selectRaw("SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as admin_total")
            ->selectRaw("SUM(CASE WHEN role = 'mahasiswa' THEN 1 ELSE 0 END) as mahasiswa_total")
            ->first();

        $hoursUsedSecondsObj = $approvedReportQuery()
            ->selectRaw('COALESCE(SUM(TIME_TO_SEC(TIMEDIFF(p.jam_selesai, p.jam_mulai))), 0) as total_detik')
            ->first();
        $hoursUsedSeconds = (int) ($hoursUsedSecondsObj->total_detik ?? 0);

        $roomCount = $ruanganId > 0 ? 1 : Ruangan::count();
        
        $capacityHours = max(1.0, $roomCount * ((int) date('t', strtotime($startDate))) * 12.0);
        $usedHours = round($hoursUsedSeconds / 3600, 2);
        $utilizationRate = round(min(100, ($usedHours / $capacityHours) * 100), 1);

        $detail = $reportQuery()
            ->select(
                'p.id',
                'p.tanggal',
                'p.jam_mulai',
                'p.jam_selesai',
                'p.nama_kegiatan',
                'p.jumlah_peserta',
                'p.catatan_admin',
                'sp.nama_status',
                'u.nama as nama_peminjam',
                'u.prodi',
                'g.nama_gedung as gedung',
                'r.nama_ruangan'
            )
            ->join('users as u', 'u.id', '=', 'p.user_id')
            ->join('ruangan as r', 'r.id', '=', 'p.ruangan_id')
            ->leftJoin('lantai as l', 'l.id', '=', 'r.lantai_id')
            ->leftJoin('gedung as g', 'g.id', '=', 'l.gedung_id')
            ->join('status_peminjaman as sp', 'sp.id', '=', 'p.status_id')
            ->orderBy($sortColumn, $sortDir)
            ->orderByDesc('p.tanggal')
            ->orderByDesc('p.jam_mulai')
            ->orderByDesc('p.id')
            ->get();

        if (($validated['export'] ?? null) === 'csv') {
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
