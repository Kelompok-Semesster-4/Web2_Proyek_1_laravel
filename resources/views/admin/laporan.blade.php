<x-layouts.admin-layout>
    <x-slot:title>Laporan - Admin</x-slot>

        @php
            $sortParams = [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'gedung_id' => $gedungId,
                'ruangan_id' => $ruanganId,
                'status_id' => $statusId,
                'prodi' => $prodi,
                'user_keyword' => $userKeyword,
                'keyword' => $keyword,
            ];

            $sortLink = function (string $field) use ($sortBy, $sortDir, $sortParams) {
                $nextDir = ($sortBy === $field && $sortDir === 'asc') ? 'desc' : 'asc';

                return route('admin.laporan', array_merge($sortParams, [
                    'sort_by' => $field,
                    'sort_dir' => $nextDir,
                ])) . '#tableLaporan';
            };

            $statusBadgeMap = [
                'Menunggu' => 'warning',
                'Disetujui' => 'success',
                'Ditolak' => 'danger',
                'Selesai' => 'info',
                'Dibatalkan' => 'secondary',
            ];

            $statusTextMap = [
                'Menunggu' => 'warning',
                'Disetujui' => 'success',
                'Ditolak' => 'danger',
                'Selesai' => 'info',
                'Dibatalkan' => 'secondary',
            ];

            $statusIconMap = [
                'Menunggu' => 'bi-clock',
                'Disetujui' => 'bi-check-circle',
                'Ditolak' => 'bi-x-circle',
                'Selesai' => 'bi-check2-all',
                'Dibatalkan' => 'bi-dash-circle',
            ];

            $statusItems = collect($statusCounts)->values();
        @endphp

        <div class="admin-container laporan-page" style="max-width:100%;">
            <x-head-title-admin title="Laporan" icon="bi bi-file-earmark-bar-graph" :showButton="false" />

            <section class="card shadow-sm border-0 dashboard-panel laporan-filter-card mb-4">
                <div class="card-header bg-white">
                    <h5 class="dashboard-panel-title mb-0">
                        <span class="dashboard-panel-icon bg-success-subtle text-success">
                            <i class="bi bi-funnel-fill"></i>
                        </span>
                        Filter Laporan
                    </h5>
                </div>

                <div class="card-body">
                    <form class="laporan-filter-form" method="GET" action="{{ route('admin.laporan') }}">
                        <input type="hidden" name="year"
                            value="{{ \Illuminate\Support\Carbon::parse($startDate)->year }}">
                        <input type="hidden" name="month"
                            value="{{ \Illuminate\Support\Carbon::parse($startDate)->month }}">

                        <div class="row g-3">
                            <div class="col-md-6 col-xl-3">
                                <label class="form-label">Dari Tanggal</label>
                                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <label class="form-label">Sampai Tanggal</label>
                                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <label class="form-label">Gedung</label>
                                <select name="gedung_id" class="form-select">
                                    <option value="0">Semua Gedung</option>
                                    @foreach ($gedungList as $gedung)
                                        <option value="{{ $gedung->id }}" @selected($gedung->id === $gedungId)>
                                            {{ $gedung->nama_gedung }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <label class="form-label">Ruangan</label>
                                <select name="ruangan_id" class="form-select">
                                    <option value="0">Semua Ruangan</option>
                                    @foreach ($ruanganList as $ruangan)
                                        <option value="{{ $ruangan->id }}" @selected($ruangan->id === $ruanganId)>
                                            {{ ($ruangan->gedung ?? '-') . ' - ' . $ruangan->nama_ruangan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <label class="form-label">Status Peminjaman</label>
                                <select name="status_id" class="form-select">
                                    <option value="0">Semua Status</option>
                                    @foreach ($statusList as $status)
                                        <option value="{{ $status->id }}" @selected($status->id === $statusId)>
                                            {{ $status->nama_status }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <label class="form-label">Program Studi</label>
                                <select name="prodi" class="form-select">
                                    <option value="">Semua Prodi</option>
                                    @foreach ($prodiList as $item)
                                        <option value="{{ $item }}" @selected($item === $prodi)>{{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <label class="form-label">Pengguna</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                                    <input type="text" name="user_keyword" class="form-control"
                                        value="{{ $userKeyword }}" placeholder="Nama atau username...">
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <label class="form-label">Kata Kunci</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                                    <input type="text" name="keyword" class="form-control" value="{{ $keyword }}"
                                        placeholder="Ruangan, kegiatan...">
                                </div>
                            </div>
                        </div>

                        <div class="laporan-filter-actions">
                            <span class="laporan-filter-total text-muted fw-semibold">
                                Menampilkan <strong>{{ count($detail) }}</strong> dari
                                <strong>{{ $totalRequests }}</strong> total data
                            </span>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('admin.laporan') }}"
                                    class="btn btn-outline-secondary laporan-filter-btn">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i>Reset Filter
                                </a>
                                <button type="submit" class="btn btn-success laporan-filter-btn">
                                    <i class="bi bi-check2-circle me-1"></i>Terapkan Filter
                                </button>
                                <a class="btn btn-success laporan-filter-btn"
                                    href="{{ route('admin.laporan', $exportParams) }}">
                                    <i class="bi bi-download me-1"></i>Export CSV
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </section>

            <div class="row g-3 mb-4 laporan-stat-grid laporan-stat-grid-top">
                <div class="col">
                    <div class="dashboard-stat-card card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <div class="dashboard-stat-icon bg-secondary-subtle text-secondary">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                            <div class="dashboard-stat-value">{{ $totalRequests }}</div>
                            <div class="dashboard-stat-label">Total Request</div>
                            <small class="text-muted">Semua pengajuan</small>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="dashboard-stat-card card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <div class="dashboard-stat-icon bg-success-subtle text-success">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <div class="dashboard-stat-value">{{ $approvedCount }}</div>
                            <div class="dashboard-stat-label">Disetujui</div>
                            <small class="text-muted">{{ number_format($approvalRate, 1) }}% approval rate</small>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="dashboard-stat-card card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <div class="dashboard-stat-icon bg-danger-subtle text-danger">
                                <i class="bi bi-x-circle"></i>
                            </div>
                            <div class="dashboard-stat-value">{{ $rejectedCount }}</div>
                            <div class="dashboard-stat-label">Ditolak</div>
                            <small class="text-muted">{{ number_format($rejectionRate, 1) }}% rejection rate</small>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="dashboard-stat-card card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <div class="dashboard-stat-icon bg-warning-subtle text-warning">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <div class="dashboard-stat-value">{{ $pendingCount }}</div>
                            <div class="dashboard-stat-label">Pending</div>
                            <small class="text-muted">Menunggu review</small>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="dashboard-stat-card card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <div class="dashboard-stat-icon bg-info-subtle text-info">
                                <i class="bi bi-people"></i>
                            </div>
                            <div class="dashboard-stat-value">{{ $totalPeserta }}</div>
                            <div class="dashboard-stat-label">Total Peserta</div>
                            <small class="text-muted">Dari semua booking</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4 laporan-stat-grid laporan-stat-grid-bottom">
                <div class="col">
                    <div class="dashboard-stat-card card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <div class="dashboard-stat-icon bg-success-subtle text-success">
                                <i class="bi bi-percent"></i>
                            </div>
                            <div class="dashboard-stat-value">{{ number_format($approvalRate, 1) }}%</div>
                            <div class="dashboard-stat-label">Approval Rate</div>
                            <small class="text-muted">Tingkat persetujuan</small>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="dashboard-stat-card card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <div class="dashboard-stat-icon bg-danger-subtle text-danger">
                                <i class="bi bi-x"></i>
                            </div>
                            <div class="dashboard-stat-value">{{ number_format($rejectionRate, 1) }}%</div>
                            <div class="dashboard-stat-label">Rejection Rate</div>
                            <small class="text-muted">Tingkat penolakan</small>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="dashboard-stat-card card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <div class="dashboard-stat-icon bg-primary-subtle text-primary">
                                <i class="bi bi-stopwatch"></i>
                            </div>
                            <div class="dashboard-stat-value">{{ number_format($avgDurationHours, 1) }} jam</div>
                            <div class="dashboard-stat-label">Rata-rata Durasi</div>
                            <small class="text-muted">Per sesi disetujui</small>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="dashboard-stat-card card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <div class="dashboard-stat-icon bg-warning-subtle text-warning">
                                <i class="bi bi-calendar-check"></i>
                            </div>
                            <div class="dashboard-stat-value">{{ number_format($usedHours, 1) }} jam</div>
                            <div class="dashboard-stat-label">Total Jam Digunakan</div>
                            <small class="text-muted">Booking disetujui</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-xl-7">
                    <section class="card shadow-sm border-0 dashboard-panel h-100">
                        <div
                            class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h5 class="dashboard-panel-title mb-0">
                                <span class="dashboard-panel-icon bg-primary-subtle text-primary">
                                    <i class="bi bi-graph-up-arrow"></i>
                                </span>
                                Tren Peminjaman Bulanan
                            </h5>
                            <div class="dashboard-legend mt-0">
                                <span><i class="bg-success"></i>Disetujui</span>
                                <span><i class="bg-danger"></i>Ditolak</span>
                                <span><i class="bg-warning"></i>Pending</span>
                            </div>
                        </div>
                        <div class="card-body laporan-chart-body">
                            <canvas id="bookingTrendChart"></canvas>
                        </div>
                    </section>
                </div>

                <div class="col-xl-5">
                    <section class="card shadow-sm border-0 dashboard-panel distribution-users-panel h-100">
                        <div class="card-header bg-white">
                            <h5 class="dashboard-panel-title mb-0">
                                <span class="dashboard-panel-icon bg-info-subtle text-info">
                                    <i class="bi bi-pie-chart"></i>
                                </span>
                                Distribusi Pengguna
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-12">
                                    <h6 class="laporan-section-label">Distribusi Prodi</h6>
                                    <div class="laporan-dot-list">
                                        @forelse ($prodiDistribution as $item)
                                            <div class="laporan-dot-item">
                                                <span class="laporan-dot bg-success"></span>
                                                <span>{{ $item->nama }}</span>
                                                <strong>{{ number_format($item->percent, 1) }}%</strong>
                                            </div>
                                        @empty
                                            <p class="text-muted mb-0">Belum ada data prodi.</p>
                                        @endforelse
                                    </div>
                                    <div class="laporan-user-box mt-3">
                                        <div>
                                            <strong>{{ (int) ($userDistributionObj->mahasiswa_total ?? 0) }}</strong>
                                            <span>Mahasiswa</span>
                                        </div>
                                        <div>
                                            <strong>{{ (int) ($userDistributionObj->admin_total ?? 0) }}</strong>
                                            <span>Admin</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <section class="card shadow-sm border-0 dashboard-panel mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="dashboard-panel-title mb-0">
                        <span class="dashboard-panel-icon bg-warning-subtle text-warning">
                            <i class="bi bi-building-check"></i>
                        </span>
                        Room Usage Analysis
                    </h5>
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>Kapasitas: {{ number_format($capacityHours, 0) }} jam per
                        periode
                    </small>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @forelse ($topRuangan->take(6) as $room)
                            <div class="col-lg-4">
                                <article class="laporan-room-card h-100">
                                    <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                                        <div>
                                            <h6>{{ $room->nama_ruangan }}</h6>
                                            <small class="text-muted">{{ $room->gedung ?? '-' }}</small>
                                        </div>
                                        <span class="badge bg-light text-dark">#{{ $loop->iteration }}</span>
                                    </div>
                                    <div class="laporan-room-metrics">
                                        <div><strong>{{ $room->jumlah_booking }}</strong><span>Booking</span></div>
                                        <div><strong>{{ number_format($room->used_hours, 1) }}</strong><span>Jam</span>
                                        </div>
                                        <div><strong
                                                class="text-success">{{ number_format($room->utilization_rate, 1) }}%</strong><span>Utilitas</span>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between text-muted small mt-3">
                                        <span>Utilization Rate</span>
                                        <strong>{{ number_format($room->used_hours, 1) }}/{{ number_format($room->capacity_hours, 0) }}
                                            jam</strong>
                                    </div>
                                    <div class="progress mt-2">
                                        <div class="progress-bar bg-success" style="width: {{ $room->utilization_rate }}%">
                                        </div>
                                    </div>
                                </article>
                            </div>
                        @empty
                            <div class="col-lg-8">
                                <div class="text-center text-muted py-5">
                                    <i class="bi bi-inbox display-5 d-block mb-2"></i>
                                    Belum ada data penggunaan ruangan.
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>

            <x-table-card title="Detail Transaksi" icon="bi bi-table" table-id="tableLaporan"
                search-placeholder="Cari transaksi..." :total="count($detail)" total-label="transaksi ditemukan"
                :empty="$detail->isEmpty()" empty-title="Tidak ada data transaksi"
                empty-subtitle="Coba sesuaikan filter pencarian Anda" :colspan="9" body-max-height="520px"
                :sticky-head="true">
                <x-slot name="head">
                    <tr>
                        <th class="text-center" style="width: 60px; padding: 15px 10px;">No</th>
                        <th style="padding: 15px 10px;">
                            <a href="{{ $sortLink('tanggal') }}"
                                class="text-decoration-none text-dark d-flex align-items-center justify-content-between">
                                Tanggal & Waktu @if (in_array($sortBy, ['tanggal', 'jam'], true)) <i
                                class="bi bi-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} text-success"></i> @endif
                            </a>
                        </th>
                        <th style="padding: 15px 10px;">Ruangan</th>
                        <th style="padding: 15px 10px;">
                            <a href="{{ $sortLink('peminjam') }}"
                                class="text-decoration-none text-dark d-flex align-items-center justify-content-between">
                                Peminjam @if ($sortBy === 'peminjam') <i
                                class="bi bi-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} text-success"></i> @endif
                            </a>
                        </th>
                        <th style="padding: 15px 10px;">
                            <a href="{{ $sortLink('prodi') }}"
                                class="text-decoration-none text-dark d-flex align-items-center justify-content-between">
                                Prodi @if ($sortBy === 'prodi') <i
                                class="bi bi-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} text-success"></i> @endif
                            </a>
                        </th>
                        <th style="padding: 15px 10px;">
                            <a href="{{ $sortLink('kegiatan') }}"
                                class="text-decoration-none text-dark d-flex align-items-center justify-content-between">
                                Kegiatan @if ($sortBy === 'kegiatan') <i
                                class="bi bi-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} text-success"></i> @endif
                            </a>
                        </th>
                        <th class="text-center" style="padding: 15px 10px;">
                            <a href="{{ $sortLink('peserta') }}"
                                class="text-decoration-none text-dark d-flex align-items-center justify-content-between">
                                Peserta @if ($sortBy === 'peserta') <i
                                class="bi bi-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} text-success"></i> @endif
                            </a>
                        </th>
                        <th class="text-center" style="padding: 15px 10px;">
                            <a href="{{ $sortLink('status') }}"
                                class="text-decoration-none text-dark d-flex align-items-center justify-content-between">
                                Status @if ($sortBy === 'status') <i
                                class="bi bi-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} text-success"></i> @endif
                            </a>
                        </th>
                        <th style="padding: 15px 10px;">Catatan</th>
                    </tr>
                </x-slot>

                @foreach ($detail as $d)
                    @php
                        $badgeClass = $statusBadgeMap[$d->nama_status] ?? 'secondary';
                    @endphp
                    <tr>
                        <td class="text-center">
                            <span class="badge-number">{{ $loop->iteration }}</span>
                        </td>
                        <td>
                            <div class="fw-semibold">
                                {{ \Illuminate\Support\Carbon::parse($d->tanggal)->format('d-m-Y') }}</div>
                            <div class="text-muted">
                                {{ \Illuminate\Support\Carbon::parse($d->jam_mulai)->format('H:i') }}-{{ \Illuminate\Support\Carbon::parse($d->jam_selesai)->format('H:i') }}
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="fw-bold text-dark">{{ $d->nama_ruangan }}</div>
                            <div class="text-muted">{{ $d->gedung ?? '-' }}</div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $d->nama_peminjam }}</div>
                        </td>
                        <td class="text-center">{{ filled($d->prodi) ? $d->prodi : '-' }}</td>
                        <td>
                            <span class="text-dark laporan-kegiatan-text"
                                data-kegiatan="{{ e($d->nama_kegiatan) }}">{{ $d->nama_kegiatan }}</span>
                        </td>
                        <td class="text-center">{{ $d->jumlah_peserta ?? '-' }}</td>
                        <td class="text-center">
                            <span class="badge bg-{{ $badgeClass }}">{{ $d->nama_status }}</span>
                        </td>
                        <td class="text-center">
                            @if (filled($d->catatan_admin))
                                <span class="text-dark laporan-catatan-text"
                                    data-catatan="{{ e($d->catatan_admin) }}">{{ $d->catatan_admin }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </x-table-card>
        </div>

        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
            <script>
                const monthLabels = @json($monthLabels);
                const trendChart = document.getElementById('bookingTrendChart');

                if (trendChart) {
                    new Chart(trendChart, {
                        type: 'bar',
                        data: {
                            labels: monthLabels,
                            datasets: [
                                { label: 'Disetujui', data: @json($trendApprovedData), backgroundColor: '#10b981', borderRadius: 0, borderSkipped: false },
                                { label: 'Ditolak', data: @json($trendRejectedData), backgroundColor: '#ef4444', borderRadius: 0, borderSkipped: false },
                                { label: 'Pending', data: @json($trendPendingData), backgroundColor: '#f59e0b', borderRadius: 0, borderSkipped: false }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: {
                                x: { stacked: true, grid: { display: false } },
                                y: { stacked: true, beginAtZero: true, ticks: { precision: 0 } }
                            }
                        }
                    });
                }

                document.querySelectorAll('.table-search-input').forEach(function (input) {
                    input.addEventListener('keyup', function () {
                        const table = document.getElementById(this.dataset.targetTable);
                        if (!table) return;

                        const searchValue = this.value.toLowerCase();
                        const rows = table.querySelectorAll('tbody tr');

                        rows.forEach(function (row) {
                            if (row.cells.length === 1) return;
                            row.style.display = row.textContent.toLowerCase().includes(searchValue) ? '' : 'none';
                        });
                    });
                });

                document.querySelectorAll('.laporan-kegiatan-text').forEach(function (text) {
                    text.addEventListener('click', function () {
                        Swal.fire({
                            title: 'Kegiatan',
                            text: this.dataset.kegiatan || '-',
                            icon: 'info',
                            width: 420,
                            padding: '0.95rem',
                            confirmButtonColor: '#198754',
                            confirmButtonText: 'Tutup',
                            customClass: {
                                popup: 'swal-compact',
                                title: 'swal-compact-title',
                                htmlContainer: 'swal-compact-text',
                                confirmButton: 'swal-compact-btn'
                            }
                        });
                    });
                });

                document.querySelectorAll('.laporan-catatan-text').forEach(function (text) {
                    text.addEventListener('click', function () {
                        Swal.fire({
                            title: 'Catatan',
                            text: this.dataset.catatan || '-',
                            icon: 'info',
                            width: 420,
                            padding: '0.95rem',
                            confirmButtonColor: '#198754',
                            confirmButtonText: 'Tutup',
                            customClass: {
                                popup: 'swal-compact',
                                title: 'swal-compact-title',
                                htmlContainer: 'swal-compact-text',
                                confirmButton: 'swal-compact-btn'
                            }
                        });
                    });
                });
            </script>
        @endpush
</x-layouts.admin-layout>
