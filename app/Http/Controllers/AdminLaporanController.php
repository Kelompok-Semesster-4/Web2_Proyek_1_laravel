<?php

namespace App\Http\Controllers;

use App\Models\Gedung;
use App\Models\Peminjaman;
use App\Models\Ruangan;
use App\Models\StatusPeminjaman;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AdminLaporanController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'year' => ['nullable', 'integer', 'between:2000,2100'],
            'month' => ['nullable', 'integer', 'between:1,12'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'gedung_id' => ['nullable', 'integer'],
            'ruangan_id' => ['nullable', 'integer'],
            'status_id' => ['nullable', 'integer'],
            'prodi' => ['nullable', 'string', 'max:100'],
            'user_keyword' => ['nullable', 'string', 'max:100'],
            'keyword' => ['nullable', 'string', 'max:150'],
            'sort_by' => ['nullable', 'string'],
            'sort_dir' => ['nullable', 'in:asc,desc'],
            'export' => ['nullable', 'in:csv'],
        ]);

        $year = (int) ($validated['year'] ?? now()->year);
        $month = (int) ($validated['month'] ?? now()->month);
        if ($year < 2000 || $year > 2100) {
            $year = now()->year;
        }
        if ($month < 1 || $month > 12) {
            $month = now()->month;
        }

        $defaultStart = Carbon::create($year, $month, 1)->startOfDay();
        $defaultEnd = $defaultStart->copy()->endOfMonth();
        $start = !empty($validated['start_date']) ? Carbon::parse($validated['start_date'])->startOfDay() : $defaultStart;
        $end = !empty($validated['end_date']) ? Carbon::parse($validated['end_date'])->startOfDay() : $defaultEnd;
        if ($end->lt($start)) {
            [$start, $end] = [$end, $start];
        }

        $startDate = $start->toDateString();
        $endDate = $end->toDateString();
        $yearStart = Carbon::create($year, 1, 1)->toDateString();
        $yearEnd = Carbon::create($year, 12, 31)->toDateString();

        $gedungId = (int) ($validated['gedung_id'] ?? 0);
        $ruanganId = (int) ($validated['ruangan_id'] ?? 0);
        $statusId = (int) ($validated['status_id'] ?? 0);
        $prodi = trim((string) ($validated['prodi'] ?? ''));
        $userKeyword = trim((string) ($validated['user_keyword'] ?? ''));
        $keyword = trim((string) ($validated['keyword'] ?? ''));
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
        if (! array_key_exists($sortBy, $allowedSorts)) {
            $sortBy = 'tanggal';
        }
        $sortColumn = $allowedSorts[$sortBy];

        $withReportJoins = function ($query) {
            return $query
                ->join('users as u', 'u.id', '=', 'p.user_id')
                ->join('ruangan as r', 'r.id', '=', 'p.ruangan_id')
                ->leftJoin('lantai as l', 'l.id', '=', 'r.lantai_id')
                ->leftJoin('gedung as g', 'g.id', '=', 'l.gedung_id')
                ->join('status_peminjaman as sp', 'sp.id', '=', 'p.status_id');
        };

        $reportQuery = function () use (
            $startDate,
            $endDate,
            $gedungId,
            $ruanganId,
            $statusId,
            $prodi,
            $userKeyword,
            $keyword,
            $withReportJoins
        ) {
            $query = Peminjaman::query()
                ->from('peminjaman as p')
                ->whereBetween('p.tanggal', [$startDate, $endDate]);

            $withReportJoins($query);

            return $query
                ->when($gedungId > 0, fn ($query) => $query->where('g.id', $gedungId))
                ->when($ruanganId > 0, fn ($query) => $query->where('p.ruangan_id', $ruanganId))
                ->when($statusId > 0, fn ($query) => $query->where('p.status_id', $statusId))
                ->when($prodi !== '', fn ($query) => $query->where('u.prodi', $prodi))
                ->when($userKeyword !== '', function ($query) use ($userKeyword) {
                    $query->where(function ($query) use ($userKeyword) {
                        $query->where('u.nama', 'like', "%{$userKeyword}%")
                            ->orWhere('u.username', 'like', "%{$userKeyword}%");
                    });
                })
                ->when($keyword !== '', function ($query) use ($keyword) {
                    $query->where(function ($query) use ($keyword) {
                        $query->where('r.nama_ruangan', 'like', "%{$keyword}%")
                            ->orWhere('p.nama_kegiatan', 'like', "%{$keyword}%")
                            ->orWhere('p.catatan_admin', 'like', "%{$keyword}%");
                    });
                });
        };

        $approvedReportQuery = fn () => $reportQuery()->whereIn('p.status_id', [2, 4]);

        $gedungList = Gedung::query()
            ->select('id', 'nama_gedung')
            ->orderBy('nama_gedung')
            ->get();

        $ruanganList = Ruangan::query()
            ->from('ruangan as r')
            ->select('r.id', 'r.nama_ruangan', 'g.id as gedung_id', 'g.nama_gedung as gedung')
            ->leftJoin('lantai as l', 'l.id', '=', 'r.lantai_id')
            ->leftJoin('gedung as g', 'g.id', '=', 'l.gedung_id')
            ->when($gedungId > 0, fn ($query) => $query->where('g.id', $gedungId))
            ->orderBy('g.nama_gedung')
            ->orderBy('r.nama_ruangan')
            ->get();

        $statusList = StatusPeminjaman::query()
            ->select('id', 'nama_status')
            ->orderBy('id')
            ->get();

        $prodiList = User::query()
            ->whereNotNull('prodi')
            ->where('prodi', '<>', '')
            ->distinct()
            ->orderBy('prodi')
            ->pluck('prodi');

        $totalRequests = $reportQuery()->count('p.id');

        $statusCountsRaw = $reportQuery()
            ->select('sp.id', 'sp.nama_status')
            ->selectRaw('COUNT(p.id) as jumlah')
            ->groupBy('sp.id', 'sp.nama_status')
            ->orderBy('sp.id')
            ->get();

        $statusCounts = [];
        foreach ($statusList as $status) {
            $statusCounts[(int) $status->id] = ['nama' => $status->nama_status, 'jumlah' => 0, 'percent' => 0.0];
        }
        foreach ($statusCountsRaw as $row) {
            $percent = $totalRequests > 0 ? round(((int) $row->jumlah / $totalRequests) * 100, 1) : 0.0;
            $statusCounts[(int) $row->id] = [
                'nama' => $row->nama_status,
                'jumlah' => (int) $row->jumlah,
                'percent' => $percent,
            ];
        }

        $approvedCount = ($statusCounts[2]['jumlah'] ?? 0) + ($statusCounts[4]['jumlah'] ?? 0);
        $rejectedCount = $statusCounts[3]['jumlah'] ?? 0;
        $pendingCount = $statusCounts[1]['jumlah'] ?? 0;
        $approvalRate = $totalRequests > 0 ? round(($approvedCount / $totalRequests) * 100, 1) : 0.0;
        $rejectionRate = $totalRequests > 0 ? round(($rejectedCount / $totalRequests) * 100, 1) : 0.0;

        $durasiObj = $approvedReportQuery()
            ->selectRaw('COALESCE(SUM(TIME_TO_SEC(TIMEDIFF(p.jam_selesai, p.jam_mulai))), 0) as total_detik')
            ->selectRaw('COALESCE(AVG(TIME_TO_SEC(TIMEDIFF(p.jam_selesai, p.jam_mulai))), 0) as avg_detik')
            ->first();

        $totalSeconds = (int) ($durasiObj->total_detik ?? 0);
        $avgSeconds = (int) round((float) ($durasiObj->avg_detik ?? 0));
        $usedHours = round($totalSeconds / 3600, 2);
        $avgDurationHours = round($avgSeconds / 3600, 1);
        $totalJam = $this->formatDuration($totalSeconds);
        $avgDurasi = $this->formatDuration($avgSeconds);

        $totalPeserta = (int) $reportQuery()->sum('p.jumlah_peserta');

        $yearlyTrendQuery = Peminjaman::query()
            ->from('peminjaman as p')
            ->selectRaw('MONTH(p.tanggal) as month_num')
            ->selectRaw('COUNT(*) as total_pengajuan')
            ->selectRaw('SUM(CASE WHEN p.status_id IN (2, 4) THEN 1 ELSE 0 END) as disetujui')
            ->selectRaw('SUM(CASE WHEN p.status_id = 3 THEN 1 ELSE 0 END) as ditolak')
            ->selectRaw('SUM(CASE WHEN p.status_id = 1 THEN 1 ELSE 0 END) as pending')
            ->whereBetween('p.tanggal', [$yearStart, $yearEnd]);

        $withReportJoins($yearlyTrendQuery);

        $yearlyTrendRaw = $yearlyTrendQuery
            ->when($gedungId > 0, fn ($query) => $query->where('g.id', $gedungId))
            ->when($ruanganId > 0, fn ($query) => $query->where('p.ruangan_id', $ruanganId))
            ->when($statusId > 0, fn ($query) => $query->where('p.status_id', $statusId))
            ->when($prodi !== '', fn ($query) => $query->where('u.prodi', $prodi))
            ->when($userKeyword !== '', function ($query) use ($userKeyword) {
                $query->where(function ($query) use ($userKeyword) {
                    $query->where('u.nama', 'like', "%{$userKeyword}%")
                        ->orWhere('u.username', 'like', "%{$userKeyword}%");
                });
            })
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query->where('r.nama_ruangan', 'like', "%{$keyword}%")
                        ->orWhere('p.nama_kegiatan', 'like', "%{$keyword}%")
                        ->orWhere('p.catatan_admin', 'like', "%{$keyword}%");
                });
            })
            ->groupByRaw('MONTH(p.tanggal)')
            ->orderByRaw('MONTH(p.tanggal)')
            ->get();

        $prodiDistribution = $reportQuery()
            ->selectRaw("COALESCE(NULLIF(u.prodi, ''), 'Tanpa Prodi') as nama")
            ->selectRaw('COUNT(p.id) as jumlah')
            ->groupByRaw("COALESCE(NULLIF(u.prodi, ''), 'Tanpa Prodi')")
            ->orderByDesc('jumlah')
            ->limit(6)
            ->get()
            ->map(function ($row) use ($totalRequests) {
                $row->percent = $totalRequests > 0 ? round(((int) $row->jumlah / $totalRequests) * 100, 1) : 0.0;
                return $row;
            });

        $topRuangan = $approvedReportQuery()
            ->select('r.id', 'g.nama_gedung as gedung', 'r.nama_ruangan')
            ->selectRaw('COUNT(p.id) as jumlah_booking')
            ->selectRaw('COALESCE(SUM(TIME_TO_SEC(TIMEDIFF(p.jam_selesai, p.jam_mulai))), 0) as total_detik')
            ->groupBy('r.id', 'g.nama_gedung', 'r.nama_ruangan')
            ->orderByDesc('jumlah_booking')
            ->orderByDesc('total_detik')
            ->limit(10)
            ->get();

        $roomCapacityHours = max(1.0, $start->daysInMonth * 12.0);
        $topRuangan = $topRuangan->map(function ($room) use ($roomCapacityHours) {
            $seconds = (int) ($room->total_detik ?? 0);
            $room->used_hours = round($seconds / 3600, 1);
            $room->total_jam = $this->formatDuration($seconds);
            $room->capacity_hours = $roomCapacityHours;
            $room->utilization_rate = round(min(100, ($room->used_hours / $roomCapacityHours) * 100), 1);
            return $room;
        });

        $userDistributionObj = User::query()
            ->selectRaw("SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as admin_total")
            ->selectRaw("SUM(CASE WHEN role = 'mahasiswa' THEN 1 ELSE 0 END) as mahasiswa_total")
            ->first();

        $roomCount = $ruanganId > 0 ? 1 : Ruangan::count();
        $capacityHours = max(1.0, $roomCount * $start->daysInMonth * 12.0);
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
                'u.username as username_peminjam',
                'u.prodi',
                'g.nama_gedung as gedung',
                'r.nama_ruangan'
            )
            ->orderBy($sortColumn, $sortDir)
            ->orderByDesc('p.tanggal')
            ->orderByDesc('p.jam_mulai')
            ->orderByDesc('p.id')
            ->get();

        if (($validated['export'] ?? null) === 'csv') {
            return response()->streamDownload(function () use ($detail) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['ID', 'Tanggal', 'Jam Mulai', 'Jam Selesai', 'Durasi (menit)', 'Ruangan', 'Peminjam', 'Username', 'Prodi', 'Kegiatan', 'Peserta', 'Status', 'Catatan']);
                foreach ($detail as $d) {
                    $startTime = Carbon::parse($d->tanggal . ' ' . $d->jam_mulai);
                    $endTime = Carbon::parse($d->tanggal . ' ' . $d->jam_selesai);
                    $durMin = max(0, $startTime->diffInMinutes($endTime, false));
                    fputcsv($out, [
                        $d->id,
                        $d->tanggal,
                        Carbon::parse($d->jam_mulai)->format('H:i'),
                        Carbon::parse($d->jam_selesai)->format('H:i'),
                        $durMin,
                        ($d->gedung ?? '-') . ' - ' . $d->nama_ruangan,
                        $d->nama_peminjam,
                        $d->username_peminjam,
                        $d->prodi ?? '-',
                        $d->nama_kegiatan,
                        $d->jumlah_peserta ?? '',
                        $d->nama_status,
                        $d->catatan_admin ?? '',
                    ]);
                }
                fclose($out);
            }, 'laporan_' . $start->format('Ymd') . '_' . $end->format('Ymd') . '.csv', [
                'Content-Type' => 'text/csv; charset=utf-8',
            ]);
        }

        $yearlyTrendMap = [];
        foreach ($yearlyTrendRaw as $row) {
            $yearlyTrendMap[(int) $row->month_num] = $row;
        }

        $monthLabels = [];
        $trendTotalData = [];
        $trendApprovedData = [];
        $trendRejectedData = [];
        $trendPendingData = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthLabels[] = Carbon::create($year, $m, 1)->translatedFormat('M');
            $trendRow = $yearlyTrendMap[$m] ?? null;
            $trendTotalData[] = (int) ($trendRow->total_pengajuan ?? 0);
            $trendApprovedData[] = (int) ($trendRow->disetujui ?? 0);
            $trendRejectedData[] = (int) ($trendRow->ditolak ?? 0);
            $trendPendingData[] = (int) ($trendRow->pending ?? 0);
        }

        $exportParams = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'gedung_id' => $gedungId,
            'ruangan_id' => $ruanganId,
            'status_id' => $statusId,
            'prodi' => $prodi,
            'user_keyword' => $userKeyword,
            'keyword' => $keyword,
            'sort_by' => $sortBy,
            'sort_dir' => $sortDir,
            'export' => 'csv',
        ];

        return view('admin.laporan', compact(
            'year',
            'month',
            'startDate',
            'endDate',
            'gedungId',
            'ruanganId',
            'statusId',
            'prodi',
            'userKeyword',
            'keyword',
            'sortBy',
            'sortDir',
            'gedungList',
            'ruanganList',
            'statusList',
            'prodiList',
            'totalRequests',
            'approvedCount',
            'rejectedCount',
            'pendingCount',
            'approvalRate',
            'rejectionRate',
            'avgDurasi',
            'avgDurationHours',
            'totalJam',
            'usedHours',
            'capacityHours',
            'utilizationRate',
            'totalPeserta',
            'statusCounts',
            'prodiDistribution',
            'topRuangan',
            'detail',
            'monthLabels',
            'trendTotalData',
            'trendApprovedData',
            'trendRejectedData',
            'trendPendingData',
            'userDistributionObj',
            'exportParams'
        ));
    }

    private function formatDuration(int $seconds): string
    {
        $seconds = max(0, $seconds);
        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);

        return sprintf('%02d:%02d', $hours, $minutes);
    }
}
