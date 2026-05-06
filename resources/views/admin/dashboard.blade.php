<x-layouts.admin-layout>
    <x-slot:title>Dashboard - Admin</x-slot>

    <div class="admin-container admin-dashboard" style="max-width:100%;">
        <x-head-title-admin title="Dashboard" icon="bi bi-calendar4-week" :showButton="false" />

        @if (session('flash_error'))
            <div class="alert alert-danger">{{ session('flash_error') }}</div>
        @endif
        @if (session('flash_success'))
            <div class="alert alert-success">{{ session('flash_success') }}</div>
        @endif

        <div class="dashboard-toolbar mb-4">
            <small class="dashboard-updated-text">
                <i class="bi bi-arrow-clockwise me-1"></i>
                Data terakhir diperbarui: {{ $now->format('H:i:s') }}
            </small>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-success btn-sm dashboard-refresh-btn">
                <i class="bi bi-arrow-clockwise me-1"></i>Refresh
            </a>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="dashboard-stat-card card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="dashboard-stat-icon bg-success-subtle text-success">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <div class="dashboard-stat-value">{{ $bookingHariIni }}</div>
                        <div class="dashboard-stat-label">Booking Hari Ini</div>
                        <small class="text-muted">{{ $disetujuiHariIni }} disetujui hari ini</small>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="dashboard-stat-card card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="dashboard-stat-icon bg-primary-subtle text-primary">
                            <i class="bi bi-door-open"></i>
                        </div>
                        <div class="dashboard-stat-value">{{ $ruanganTerpakai }}</div>
                        <div class="dashboard-stat-label">Ruangan Dipakai</div>
                        <small class="text-muted">{{ $ruanganTerpakai }} dari {{ $totalRuangan }} ruangan</small>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="dashboard-stat-card card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="dashboard-stat-icon bg-warning-subtle text-warning">
                            <i class="bi bi-clock"></i>
                        </div>
                        <div class="dashboard-stat-value">{{ $pendingTotal }}</div>
                        <div class="dashboard-stat-label">Pending Approval</div>
                        <small class="text-muted">Perlu ditinjau</small>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="dashboard-stat-card card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="dashboard-stat-icon bg-info-subtle text-info">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="dashboard-stat-value">{{ $totalPengguna }}</div>
                        <div class="dashboard-stat-label">Total Pengguna</div>
                        <small class="text-muted">Akun terdaftar</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-lg-4">
                <section class="card shadow-sm border-0 dashboard-panel h-100">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 class="dashboard-panel-title mb-0">
                            <span class="dashboard-panel-icon bg-success-subtle text-success"><i class="bi bi-door-open"></i></span>
                            Status Ruangan
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="dashboard-progress-item">
                            <div class="d-flex justify-content-between">
                                <span>Tersedia</span>
                                <strong>{{ $ruanganTersedia }}</strong>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-success" style="width: {{ $availablePercent }}%"></div>
                            </div>
                        </div>

                        <div class="dashboard-progress-item">
                            <div class="d-flex justify-content-between">
                                <span>Dipakai</span>
                                <strong>{{ $ruanganTerpakai }}</strong>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-primary" style="width: {{ $usedPercent }}%"></div>
                            </div>
                        </div>

                        <small class="text-muted">Total {{ $totalRuangan }} ruangan aktif</small>
                    </div>
                </section>
            </div>

            <div class="col-lg-8">
                <section class="card shadow-sm border-0 dashboard-panel h-100">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 class="dashboard-panel-title mb-0">
                            <span class="dashboard-panel-icon bg-primary-subtle text-primary"><i class="bi bi-bar-chart"></i></span>
                            Ringkasan Booking Bulan Ini
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 mb-3">
                            <div class="col-sm-6 col-xl-3">
                                <div class="dashboard-summary-box">
                                    <strong>{{ $pengajuanBulanIni }}</strong>
                                    <span>Pengajuan</span>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xl-3">
                                <div class="dashboard-summary-box success">
                                    <strong>{{ $disetujuiBulanIni }}</strong>
                                    <span>Disetujui</span>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xl-3">
                                <div class="dashboard-summary-box danger">
                                    <strong>{{ $ditolakBulanIni }}</strong>
                                    <span>Ditolak</span>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xl-3">
                                <div class="dashboard-summary-box warning">
                                    <strong>{{ $pendingBulanIni }}</strong>
                                    <span>Menunggu</span>
                                </div>
                            </div>
                        </div>

                        <div class="dashboard-booking-stack">
                            <span class="bg-success" style="width: {{ $approvedPercent }}%"></span>
                            <span class="bg-danger" style="width: {{ $rejectedPercent }}%"></span>
                            <span class="bg-warning" style="width: {{ $waitingPercent }}%"></span>
                        </div>
                        <div class="dashboard-legend">
                            <span><i class="bg-success"></i>Disetujui</span>
                            <span><i class="bg-danger"></i>Ditolak</span>
                            <span><i class="bg-warning"></i>Pending</span>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <section class="card shadow-sm border-0 dashboard-panel mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="dashboard-panel-title mb-0">
                    <span class="dashboard-panel-icon bg-warning-subtle text-warning"><i class="bi bi-clock-history"></i></span>
                    Pending Booking Requests
                </h5>
                <a href="{{ route('admin.persetujuan') }}"
                    class="btn btn-outline-warning btn-sm dashboard-view-all-link">
                    Lihat semua
                </a>
            </div>

            <div class="list-group list-group-flush dashboard-pending-list">
                @forelse ($pendingList as $item)
                    <div class="list-group-item dashboard-pending-item">
                        <div class="dashboard-pending-info">
                            <div class="dashboard-pending-name">
                                {{ $item->nama_user }}
                                <small>{{ '@' . ($item->username_user ?? $item->id) }}</small>
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Menunggu</span>
                            </div>
                            <div class="dashboard-pending-meta">
                                <span><i class="bi bi-door-open me-1"></i>{{ $item->nama_ruangan }} - {{ $item->gedung ?? '-' }}</span>
                                <span><i class="bi bi-calendar3 me-1"></i>{{ \Illuminate\Support\Carbon::parse($item->tanggal)->format('d-m-Y') }}, {{ \Illuminate\Support\Carbon::parse($item->jam_mulai)->format('H:i') }}-{{ \Illuminate\Support\Carbon::parse($item->jam_selesai)->format('H:i') }}</span>
                                <span><i class="bi bi-file-earmark-text me-1"></i>{{ $item->nama_kegiatan }}</span>
                                <span><i class="bi bi-people me-1"></i>{{ $item->jumlah_peserta ?? 0 }} peserta</span>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('admin.peminjaman.approve', $item->id) }}"
                            class="dashboard-pending-actions"
                            data-approve-url="{{ route('admin.peminjaman.approve', $item->id) }}"
                            data-reject-url="{{ route('admin.peminjaman.reject', $item->id) }}">
                            @csrf
                            <input type="text" name="catatan_admin" class="form-control form-control-sm"
                                placeholder="Catatan (opsional)...">
                            <div class="dashboard-action-buttons">
                                <button type="button" class="btn btn-success approve-action-btn">
                                    <i class="bi bi-check-circle me-1"></i>Setujui
                                </button>
                                <button type="button" class="btn btn-danger reject-action-btn">
                                    <i class="bi bi-x-circle me-1"></i>Tolak
                                </button>
                            </div>
                        </form>
                    </div>
                @empty
                    <div class="list-group-item text-center py-5 text-muted">
                        <i class="bi bi-inbox display-5 d-block mb-2"></i>
                        Tidak ada antrean pending.
                    </div>
                @endforelse
            </div>
        </section>

        <section class="card shadow-sm border-0 dashboard-panel">
            <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="dashboard-panel-title mb-0">
                    <span class="dashboard-panel-icon bg-primary-subtle text-primary"><i class="bi bi-calendar2-week"></i></span>
                    Jadwal Hari Ini
                </h5>
                <div class="text-end dashboard-schedule-current">
                    <strong>{{ $now->format('H:i') }}</strong>
                    <small class="text-muted d-block">{{ $now->format('d-m-Y') }}</small>
                </div>
            </div>

            <div class="list-group list-group-flush dashboard-schedule-list">
                @forelse ($jadwalHariIni as $jadwal)
                    <div class="list-group-item dashboard-schedule-item">
                        <div class="dashboard-schedule-time">
                            <strong>{{ \Illuminate\Support\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}</strong>
                            <span>{{ \Illuminate\Support\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}</span>
                        </div>
                        <div class="dashboard-schedule-line bg-{{ $statusBadgeMap[$jadwal->nama_status] ?? 'secondary' }}"></div>
                        <div class="dashboard-schedule-body">
                            <div class="fw-bold text-dark">
                                {{ $jadwal->nama_kegiatan }}
                                <span class="badge bg-{{ $statusBadgeMap[$jadwal->nama_status] ?? 'secondary' }}-subtle text-{{ $statusBadgeMap[$jadwal->nama_status] ?? 'secondary' }} ms-2">{{ $jadwal->nama_status }}</span>
                            </div>
                            <div class="text-muted">{{ $jadwal->nama_ruangan }} - {{ $jadwal->gedung ?? '-' }}</div>
                            <small class="text-muted">{{ $jadwal->nama_peminjam }}</small>
                        </div>
                    </div>
                @empty
                    <div class="list-group-item text-center py-5 text-muted">
                        <i class="bi bi-calendar-x display-5 d-block mb-2"></i>
                        Belum ada jadwal hari ini.
                    </div>
                @endforelse
            </div>
        </section>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
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
                            form.action = form.dataset.approveUrl;
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
                            form.action = form.dataset.rejectUrl;
                            form.submit();
                        }
                    });
                });
            });
        </script>
    @endpush
</x-layouts.admin-layout>
