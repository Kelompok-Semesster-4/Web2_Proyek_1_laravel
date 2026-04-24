<x-admin-layout>

<div class="admin-container" style="max-width:100%;">
    <div class="kelola-header mb-4">
        <h1>Laporan Historis & Analisis</h1>
        <p class="text-muted mb-0">Seluruh chart bulanan, statistik penggunaan, dan tabel transaksi detail ada di halaman ini.</p>
    </div>

    <div class="card shadow border-0 mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0">Filter Laporan</h6>
        </div>
        <div class="card-body">
            <form class="row g-3 align-items-end" method="GET" action="{{ route('admin.laporan') }}">
                <div class="col-md-2">
                    <label class="form-label">Bulan</label>
                    <select name="month" class="form-select">
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $m === $month ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tahun</label>
                    <select name="year" class="form-select">
                        @php $yNow = (int) date('Y'); @endphp
                        @for ($y = $yNow - 3; $y <= $yNow + 1; $y++)
                            <option value="{{ $y }}" {{ $y === $year ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Ruangan</label>
                    <select name="ruangan_id" class="form-select">
                        <option value="0">Semua Ruangan</option>
                        @foreach ($ruanganList as $r)
                            <option value="{{ $r->id }}" {{ $r->id === $ruanganId ? 'selected' : '' }}>
                                {{ ($r->gedung ?? '-') . ' - ' . $r->nama_ruangan }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status_id" class="form-select">
                        <option value="0">Semua Status</option>
                        @foreach ($statusList as $s)
                            <option value="{{ $s->id }}" {{ $s->id === $statusId ? 'selected' : '' }}>
                                {{ $s->nama_status }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success w-100">Terapkan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card-slim bg-primary text-white p-3 rounded shadow-sm text-center">
                <div class="stat-card-header"><span class="stat-value fw-bold fs-3">{{ $totalRequests }}</span></div>
                <div class="stat-card-label small">Total Request</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-slim bg-success text-white p-3 rounded shadow-sm text-center">
                <div class="stat-card-header"><span class="stat-value fw-bold fs-3">{{ number_format($approvalRate, 1) }}%</span></div>
                <div class="stat-card-label small">Approval Rate</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-slim bg-danger text-white p-3 rounded shadow-sm text-center">
                <div class="stat-card-header"><span class="stat-value fw-bold fs-3">{{ number_format($rejectionRate, 1) }}%</span></div>
                <div class="stat-card-label small">Rejection Rate</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-slim bg-info text-white p-3 rounded shadow-sm text-center">
                <div class="stat-card-header"><span class="stat-value fw-bold fs-4">{{ $avgDurasi }}</span></div>
                <div class="stat-card-label small">Rata-rata Durasi</div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Booking Trends Over Time ({{ $year }})</h6>
                </div>
                <div class="card-body" style="height:280px;"><canvas id="bookingTrendChart"></canvas></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Daily Activity ({{ date('F Y', strtotime($startDate)) }})</h6>
                </div>
                <div class="card-body" style="height:280px;"><canvas id="dailyActivityChart"></canvas></div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Kunjungan Bulanan</h6>
                </div>
                <div class="card-body" style="height:240px;"><canvas id="monthlyVisitChart"></canvas></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Distribusi Pengguna</h6>
                </div>
                <div class="card-body" style="height:240px;"><canvas id="userDistributionChart"></canvas></div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Room Usage Analysis</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Jam Digunakan</span>
                        <strong>{{ $totalJam }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Jam (desimal)</span>
                        <strong>{{ number_format($usedHours, 2) }} jam</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Kapasitas Jam</span>
                        <strong>{{ number_format($capacityHours, 0) }} jam</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Utilization Rate</span>
                        <strong>{{ number_format($utilizationRate, 1) }}%</strong>
                    </div>
                    <div class="progress" style="height:10px;">
                        <div class="progress-bar bg-success" style="width:{{ $utilizationRate }}%;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Most Used Rooms</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Ruangan</th>
                                <th class="text-center">Jumlah Booking</th>
                                <th class="text-center">Total Jam</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (empty($topRuangan))
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Tidak ada data.</td>
                                </tr>
                            @else
                                @foreach ($topRuangan as $i => $room)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>
                                            {{ $room->nama_ruangan }}<br>
                                            <small class="text-muted">{{ $room->gedung ?? '-' }}</small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info">{{ $room->jumlah_booking }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success">{{ $room->total_jam }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Detail Transaction Table</h6>
            <a class="btn btn-sm btn-success"
                href="{{ route('admin.laporan', ['year' => $year, 'month' => $month, 'ruangan_id' => $ruanganId, 'status_id' => $statusId, 'export' => 'csv']) }}">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i>Export CSV
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Ruangan</th>
                        <th>Peminjam</th>
                        <th>Prodi</th>
                        <th>Kegiatan</th>
                        <th class="text-center">Peserta</th>
                        <th class="text-center">Status</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @if (empty($detail))
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">Tidak ada data transaksi.</td>
                        </tr>
                    @else
                        @php
                            $statusBadgeMap = [
                                'Menunggu' => 'warning', 
                                'Disetujui' => 'success', 
                                'Ditolak' => 'danger', 
                                'Selesai' => 'info', 
                                'Dibatalkan' => 'secondary'
                            ];
                        @endphp
                        @foreach ($detail as $idx => $d)
                            @php $badgeClass = $statusBadgeMap[$d->nama_status] ?? 'secondary'; @endphp
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td>{{ date('d M Y', strtotime($d->tanggal)) }}</td>
                                <td>{{ substr($d->jam_mulai, 0, 5) }} - {{ substr($d->jam_selesai, 0, 5) }}</td>
                                <td>
                                    {{ $d->nama_ruangan }}<br>
                                    <small class="text-muted">{{ $d->gedung ?? '-' }}</small>
                                </td>
                                <td>{{ $d->nama_peminjam }}</td>
                                <td>{{ !empty($d->prodi) ? $d->prodi : '-' }}</td>
                                <td>{{ $d->nama_kegiatan }}</td>
                                <td class="text-center">
                                    <span class="badge bg-info">{{ $d->jumlah_peserta ?? '-' }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $badgeClass }}">{{ $d->nama_status }}</span>
                                </td>
                                <td>{{ !empty($d->catatan_admin) ? $d->catatan_admin : '-' }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const monthLabels = @json($monthLabels);
    
    new Chart(document.getElementById('bookingTrendChart'), {
        type: 'line',
        data: {
            labels: monthLabels,
            datasets: [
                { label: 'Total Request', data: @json($trendTotalData), borderColor: '#2563eb', backgroundColor: 'rgba(37,99,235,.12)', tension: .35, fill: true },
                { label: 'Disetujui', data: @json($trendApprovedData), borderColor: '#10b981', tension: .35 },
                { label: 'Ditolak', data: @json($trendRejectedData), borderColor: '#ef4444', tension: .35 }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { usePointStyle: true } } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } }, x: { grid: { display: false } } }
        }
    });

    new Chart(document.getElementById('dailyActivityChart'), {
        type: 'bar',
        data: {
            labels: @json($dailyLabels),
            datasets: [{ data: @json($dailyTotals), backgroundColor: '#3b82f6', borderRadius: 6, borderSkipped: false }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } }, x: { grid: { display: false } } }
        }
    });

    new Chart(document.getElementById('monthlyVisitChart'), {
        type: 'bar',
        data: {
            labels: monthLabels,
            datasets: [{ data: @json($visitData), backgroundColor: '#0ea5e9', borderRadius: 8, borderSkipped: false }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } }, x: { grid: { display: false } } }
        }
    });

    new Chart(document.getElementById('userDistributionChart'), {
        type: 'doughnut',
        data: {
            labels: ['Admin', 'Mahasiswa'],
            datasets: [{
                data: [{{ (int) ($userDistributionObj->admin_total ?? 0) }}, {{ (int) ($userDistributionObj->mahasiswa_total ?? 0) }}],
                backgroundColor: ['#2563eb', '#06b6d4'], borderWidth: 0
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { usePointStyle: true } } }
        }
    });
</script>
@endpush
</x-admin-layout>
