<x-layouts.admin-layout>

@push('styles')
<style>
    .top-equal-card {
        height: 312px;
        display: flex;
        flex-direction: column;
    }

    .room-status-body {
        flex: 1 1 auto;
        min-height: 0;
    }

    .pending-equal-card {
        overflow: hidden;
    }

    .pending-equal-card .table-responsive {
        flex: 1 1 auto;
        min-height: 0;
        max-height: none;
    }

    .dashboard-scroll-260 {
        max-height: 260px;
        overflow-y: auto;
    }

    .dashboard-scroll-220 {
        max-height: 220px;
        overflow-y: auto;
    }

    .dashboard-scroll-thin {
        scrollbar-width: thin;
        scrollbar-color: rgba(15, 23, 42, 0.35) transparent;
    }

    .dashboard-scroll-thin::-webkit-scrollbar {
        width: 6px;
    }

    .dashboard-scroll-thin::-webkit-scrollbar-track {
        background: transparent;
    }

    .dashboard-scroll-thin::-webkit-scrollbar-thumb {
        background: rgba(15, 23, 42, 0.35);
        border-radius: 999px;
    }

    .dashboard-scroll-thin::-webkit-scrollbar-thumb:hover {
        background: rgba(15, 23, 42, 0.5);
    }

    #pendingCompactTable thead th,
    #todayScheduleCompactTable thead th {
        position: sticky;
        top: 0;
        background: #fff;
        z-index: 1;
    }

    #pendingCompactTable th,
    #pendingCompactTable td,
    #todayScheduleCompactTable th,
    #todayScheduleCompactTable td {
        padding: 0.45rem 0.5rem;
        vertical-align: middle;
    }

    #pendingCompactTable thead th,
    #todayScheduleCompactTable thead th {
        font-size: 0.82rem;
        font-weight: 600;
    }

    #pendingCompactTable td,
    #todayScheduleCompactTable td {
        font-size: 0.88rem;
        line-height: 1.3;
    }

    #pendingCompactTable td small,
    #todayScheduleCompactTable td small {
        font-size: 0.76rem;
    }

    .pending-action-form {
        display: flex;
        flex-wrap: nowrap;
        gap: 0.4rem;
        align-items: center;
        justify-content: flex-start;
    }

    .pending-action-form .form-control,
    .pending-action-form .form-control.form-control-sm {
        min-width: 108px;
        height: 34px !important;
        font-size: 0.82rem;
        padding: 0.25rem 0.5rem;
        margin: 0 !important;
        line-height: 1.2;
    }

    .pending-icon-btn {
        width: 34px;
        height: 34px;
        min-height: 34px;
        padding: 0;
        margin: 0 !important;
        flex: 0 0 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
    }

    .pending-icon-btn i {
        line-height: 1;
    }

    .pending-card-header {
        gap: 0.35rem;
        padding-top: 0.45rem;
        padding-bottom: 0.45rem;
        padding-left: 0.75rem;
        padding-right: 0.75rem;
        border-bottom: 1px solid #dee2e6;
    }

    .pending-card-title {
        font-size: 0.92rem;
        font-weight: 600;
        color: #212529;
        margin: 0;
    }

    .pending-header-actions {
        display: flex;
        align-items: center;
        gap: 0.35rem !important;
    }

    .pending-total-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 28px;
        min-width: 28px;
        font-size: 0.72rem;
        line-height: 1;
        padding: 0 0.42rem;
    }

    .pending-view-all-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 28px;
        font-size: 0.78rem;
        font-weight: 600;
        padding: 0 0.56rem;
        line-height: 1;
        border-radius: 0.6rem;
        margin: 0;
    }
</style>
@endpush

<div class="admin-container" style="max-width:100%;">
    <x-head-title-admin
        title="Dashboard Real-Time"
        icon="bi bi-calendar4-week"
        :showButton="false"
    />

    @if (session('flash_error'))
        <div class="alert alert-danger">{{ session('flash_error') }}</div>
    @endif
    @if (session('flash_success'))
        <div class="alert alert-success">{{ session('flash_success') }}</div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card-slim bg-primary">
                <div class="stat-card-header"><span class="stat-value">{{ $bookingHariIni }}</span></div>
                <div class="stat-card-label">Booking Hari Ini</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card-slim bg-success">
                <div class="stat-card-header"><span class="stat-value">{{ $ruanganTerpakai }}</span></div>
                <div class="stat-card-label">Ruangan Sedang Dipakai</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card-slim bg-danger">
                <div class="stat-card-header"><span class="stat-value">{{ $pendingTotal }}</span></div>
                <div class="stat-card-label">Pending Approval (Semua)</div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-4">
            <div class="card shadow-sm top-equal-card">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Status Ruangan Saat Ini</h6>
                </div>
                <div class="card-body room-status-body"><canvas id="roomStatusChart"></canvas></div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card shadow-sm top-equal-card pending-equal-card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap pending-card-header">
                    <h6 class="pending-card-title">Pending Booking Requests</h6>
                    <div class="d-flex align-items-center pending-header-actions">
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle pending-total-badge">{{ $pendingTotal }}</span>
                        <a href="{{ url('/admin/approve') }}" class="btn btn-outline-danger pending-view-all-btn">Lihat semua</a>
                    </div>
                </div>
                <div class="table-responsive dashboard-scroll-thin">
                    <table class="table table-hover mb-0 align-middle" id="pendingCompactTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Mahasiswa</th>
                                <th>Ruangan</th>
                                <th>Waktu</th>
                                <th>Kegiatan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (empty($pendingList))
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Tidak ada antrean pending.</td>
                                </tr>
                            @else
                                @foreach ($pendingList as $i => $item)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $item->nama_user }}<br><small class="text-muted">{{ $item->prodi ?? '-' }}</small></td>
                                        <td>{{ $item->nama_ruangan }}<br><small class="text-muted">{{ $item->gedung ?? '-' }}</small></td>
                                        <td>{{ date('d M Y', strtotime($item->tanggal)) }}<br><small class="text-muted">{{ substr($item->jam_mulai, 0, 5) }} - {{ substr($item->jam_selesai, 0, 5) }}</small></td>
                                        <td>{{ $item->nama_kegiatan }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('admin.approve.process') }}" class="pending-action-form" style="padding:0;">
                                                @csrf
                                                <input type="hidden" name="peminjaman_id" value="{{ $item->id }}">
                                                <input type="text" name="catatan_admin" class="form-control form-control-sm" placeholder="Catatan">
                                                <button class="btn btn-success pending-icon-btn" name="action" value="approve" title="Setujui" aria-label="Setujui" onclick="return confirm('Setujui pengajuan ini?')"><i class="bi bi-check-lg"></i></button>
                                                <button class="btn btn-danger pending-icon-btn" name="action" value="reject" title="Tolak" aria-label="Tolak" onclick="return confirm('Tolak pengajuan ini?')"><i class="bi bi-x-lg"></i></button>
                                            </form>
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

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Today's Schedule</h6>
                </div>
                <div class="table-responsive dashboard-scroll-220 dashboard-scroll-thin">
                    <table class="table table-hover mb-0 align-middle" id="todayScheduleCompactTable">
                        <thead>
                            <tr>
                                <th>Jam</th>
                                <th>Ruangan</th>
                                <th>Peminjam</th>
                                <th>Kegiatan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (empty($jadwalHariIni))
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada jadwal hari ini.</td>
                                </tr>
                            @else
                                @php
                                $todayStatusBadgeMap = [
                                    'Disetujui' => 'success',
                                    'Selesai' => 'success',
                                    'Ditolak' => 'danger',
                                    'Menunggu' => 'warning',
                                    'Dibatalkan' => 'secondary',
                                ];
                                @endphp
                                @foreach ($jadwalHariIni as $jadwal)
                                    @php $todayStatusBadge = $todayStatusBadgeMap[$jadwal->nama_status] ?? 'secondary'; @endphp
                                    <tr>
                                        <td>{{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}</td>
                                        <td>{{ $jadwal->nama_ruangan }}<br><small class="text-muted">{{ $jadwal->gedung ?? '-' }}</small></td>
                                        <td>{{ $jadwal->nama_peminjam }}</td>
                                        <td>{{ $jadwal->nama_kegiatan }}</td>
                                        <td><span class="badge bg-{{ $todayStatusBadge }}">{{ $jadwal->nama_status }}</span></td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Status Hari Ini</h6>
                </div>
                <div class="card-body" style="height:220px;"><canvas id="todayStatusChart"></canvas></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    new Chart(document.getElementById('roomStatusChart'), {
        type: 'doughnut',
        data: { labels: ['Terpakai', 'Tersedia'], datasets: [{ data: [{{ $ruanganTerpakai }}, {{ $ruanganTersedia }}], backgroundColor: ['#ef4444', '#10b981'], borderWidth: 0 }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { usePointStyle: true } } } }
    });

    new Chart(document.getElementById('todayStatusChart'), {
        type: 'bar',
        data: { labels: ['Disetujui', 'Pending', 'Ditolak'], datasets: [{ data: [{{ $disetujuiHariIni }}, {{ $pendingHariIni }}, {{ $ditolakHariIni }}], backgroundColor: ['#10b981', '#f59e0b', '#ef4444'], borderRadius: 8, borderSkipped: false }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { grid: { display: false } }, y: { beginAtZero: true, ticks: { precision: 0 } } } }
    });
</script>
@endpush

</x-layouts.admin-layout>
