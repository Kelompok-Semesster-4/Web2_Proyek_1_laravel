<x-layouts.admin-layout>
    <x-slot:title>Dashboard - Admin</x-slot>

        <div class="admin-container" style="max-width:100%;">
            <x-head-title-admin title="Dashboard Real-Time" icon="bi bi-calendar4-week" :showButton="false" />

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
                        <div
                            class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap pending-card-header">
                            <h6 class="pending-card-title">Pending Booking Requests</h6>
                            <div class="d-flex align-items-center pending-header-actions">
                                <span
                                    class="badge bg-danger-subtle text-danger border border-danger-subtle pending-total-badge">{{ $pendingTotal }}</span>
                                <a href="{{ route('admin.persetujuan') }}"
                                    class="btn btn-outline-danger pending-view-all-btn">Lihat semua</a>
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
                                            <td colspan="6" class="text-center py-4 text-muted">Tidak ada antrean pending.
                                            </td>
                                        </tr>
                                    @else
                                        @foreach ($pendingList as $i => $item)
                                            <tr>
                                                <td>{{ $i + 1 }}</td>
                                                <td>{{ $item->nama_user }}<br><small
                                                        class="text-muted">{{ $item->prodi ?? '-' }}</small></td>
                                                <td>{{ $item->nama_ruangan }}<br><small
                                                        class="text-muted">{{ $item->gedung ?? '-' }}</small></td>
                                                <td>{{ date('d M Y', strtotime($item->tanggal)) }}<br><small
                                                        class="text-muted">{{ substr($item->jam_mulai, 0, 5) }} -
                                                        {{ substr($item->jam_selesai, 0, 5) }}</small></td>
                                                <td>{{ $item->nama_kegiatan }}</td>
                                                <td>
                                                    <form method="POST" action="{{ route('admin.approve.process') }}"
                                                        class="pending-action-form" style="padding:0;">
                                                        @csrf
                                                        <input type="hidden" name="peminjaman_id" value="{{ $item->id }}">
                                                        <input type="text" name="catatan_admin"
                                                            class="form-control form-control-sm" placeholder="Catatan">
                                                        <button type="button"
                                                            class="btn btn-success pending-icon-btn approve-action-btn"
                                                            title="Setujui" aria-label="Setujui"><i
                                                                class="bi bi-check-lg"></i></button>
                                                        <button type="button"
                                                            class="btn btn-danger pending-icon-btn reject-action-btn"
                                                            title="Tolak" aria-label="Tolak"><i class="bi bi-x-lg"></i></button>
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
                                            <td colspan="5" class="text-center py-4 text-muted">Belum ada jadwal hari ini.
                                            </td>
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
                                                <td>{{ substr($jadwal->jam_mulai, 0, 5) }} -
                                                    {{ substr($jadwal->jam_selesai, 0, 5) }}</td>
                                                <td>{{ $jadwal->nama_ruangan }}<br><small
                                                        class="text-muted">{{ $jadwal->gedung ?? '-' }}</small></td>
                                                <td>{{ $jadwal->nama_peminjam }}</td>
                                                <td>{{ $jadwal->nama_kegiatan }}</td>
                                                <td><span
                                                        class="badge bg-{{ $todayStatusBadge }}">{{ $jadwal->nama_status }}</span>
                                                </td>
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
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

                document.querySelectorAll('.approve-action-btn').forEach(function (button) {
                    button.addEventListener('click', function () {
                        const form = this.closest('form');

                        Swal.fire({
                            title: 'Setujui pengajuan ini?',
                            html: 'Pengajuan lain yang bentrok jadwal akan otomatis ditolak.',
                            icon: 'question',
                            width: 400,
                            padding: '0.85rem',
                            showCancelButton: true,
                            confirmButtonColor: '#198754',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: '<i class="bi bi-check-circle me-1"></i>Setujui',
                            cancelButtonText: 'Batal',
                            customClass: {
                                popup: 'swal-compact',
                                title: 'swal-compact-title',
                                htmlContainer: 'swal-compact-text',
                                confirmButton: 'swal-compact-btn',
                                cancelButton: 'swal-compact-btn'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.querySelector('input[name="action"]')?.remove();
                                const actionInput = document.createElement('input');
                                actionInput.type = 'hidden';
                                actionInput.name = 'action';
                                actionInput.value = 'approve';
                                form.appendChild(actionInput);
                                form.submit();
                            }
                        });
                    });
                });

                document.querySelectorAll('.reject-action-btn').forEach(function (button) {
                    button.addEventListener('click', function () {
                        const form = this.closest('form');

                        Swal.fire({
                            title: 'Tolak pengajuan ini?',
                            text: 'Pengajuan akan diproses sebagai ditolak.',
                            icon: 'warning',
                            width: 390,
                            padding: '0.85rem',
                            showCancelButton: true,
                            confirmButtonColor: '#dc3545',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: '<i class="bi bi-x-circle me-1"></i>Tolak',
                            cancelButtonText: 'Batal',
                            customClass: {
                                popup: 'swal-compact',
                                title: 'swal-compact-title',
                                htmlContainer: 'swal-compact-text',
                                confirmButton: 'swal-compact-btn',
                                cancelButton: 'swal-compact-btn'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.querySelector('input[name="action"]')?.remove();
                                const actionInput = document.createElement('input');
                                actionInput.type = 'hidden';
                                actionInput.name = 'action';
                                actionInput.value = 'reject';
                                form.appendChild(actionInput);
                                form.submit();
                            }
                        });
                    });
                });
            </script>
        @endpush

</x-layouts.admin-layout>