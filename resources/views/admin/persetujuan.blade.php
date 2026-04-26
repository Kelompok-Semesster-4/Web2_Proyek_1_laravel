<x-layouts.admin-layout>

<div class="admin-container" style="max-width: 100%;">
    <!-- Page Header -->
    <div class="kelola-header mb-4">
        <h1><i class="bi bi-clipboard-check me-2"></i>Persetujuan Peminjaman</h1>
    </div>

    <!-- Alert Messages -->
    <x-alert-admin />

    <!-- Card Tabel Persetujuan -->
    <div class="card shadow border-0" style="border-radius: 15px; overflow: hidden;">
        <div class="card-header bg-white py-3 border-bottom" style="background: linear-gradient(to right, #f8f9fa, #e9ecef) !important;">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0 fw-bold" style="color: #495057;">
                        <i class="bi bi-list-ul me-2" style="color: #22c55e;"></i>Daftar Persetujuan
                    </h5>
                </div>
                <div class="col-md-6">
                    <div class="input-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search" style="color: #22c55e;"></i>
                        </span>
                        <input type="text" class="form-control border-start-0 bg-white" id="searchInput"
                            placeholder="Cari mahasiswa, ruangan, kegiatan..." style="border-left: 0;">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tablePersetujuan">
                    <thead style="background: linear-gradient(to right, #f8f9fa, #e9ecef);">
                        <tr>
                            <th class="text-center" style="width: 50px; padding: 15px 10px;">
                                <i class="bi bi-hash"></i>
                            </th>
                            <th style="width: 20%; padding: 15px;">
                                <i class="bi bi-person me-1"></i>Mahasiswa
                            </th>
                            <th style="width: 15%; padding: 15px;">
                                <i class="bi bi-door-closed me-1"></i>Ruangan
                            </th>
                            <th class="text-center" style="width: 10%; padding: 15px;">
                                <i class="bi bi-calendar3 me-1"></i>Tanggal
                            </th>
                            <th class="text-center" style="width: 10%; padding: 15px;">
                                <i class="bi bi-clock me-1"></i>Jam
                            </th>
                            <th style="width: 15%; padding: 15px;">
                                <i class="bi bi-clipboard-check me-1"></i>Kegiatan
                            </th>
                            <th class="text-center" style="width: 8%; padding: 15px;">
                                <i class="bi bi-people me-1"></i>Peserta
                            </th>
                            <th class="text-center" style="width: 8%; padding: 15px;">
                                <i class="bi bi-file-text me-1"></i>Surat
                            </th>
                            <th class="text-center" style="width: 280px; padding: 15px;">
                                <i class="bi bi-gear me-1"></i>Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (empty($pending))
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-inbox display-4 d-block mb-3"></i>
                                        <p class="mb-0">Belum ada pengajuan menunggu</p>
                                        <small>Semua pengajuan telah diproses</small>
                                    </div>
                                </td>
                            </tr>
                        @else
                            @foreach ($pending as $i => $p)
                                <tr>
                                    <td class="text-center">
                                        <span class="badge-number">{{ $i + 1 }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark" style="font-size: 1rem;">
                                            {{ $p->nama_user ?? '-' }}
                                        </div>
                                        <small class="text-muted" style="font-size: 0.85rem;">
                                            <i class="bi bi-person-badge me-1"></i>{{ $p->username_user ?? '-' }}
                                            {{ !empty($p->prodi_user) ? ' • ' . $p->prodi_user : '' }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark" style="font-size: 1rem;">
                                            {{ $p->nama_ruangan ?? '-' }}
                                        </div>
                                        <small class="text-muted" style="font-size: 0.85rem;">
                                            <i class="bi bi-building me-1"></i>{{ $p->gedung ?? '-' }}
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge" style="background: linear-gradient(135deg, #10b981, #059669);">
                                            {{ date('d M Y', strtotime($p->tanggal)) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                                            {{ substr($p->jam_mulai, 0, 5) . ' - ' . substr($p->jam_selesai, 0, 5) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-dark">{{ $p->nama_kegiatan }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                                            {{ $p->jumlah_peserta ?? '0' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if (!empty($p->surat))
                                            <a class="btn btn-warning" href="{{ asset('storage/uploads/surat/' . $p->surat) }}" target="_blank" rel="noopener">
                                                <i class="bi bi-file-earmark-pdf me-1"></i>Lihat
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <form method="POST" action="{{ route('admin.approve.process') }}" class="d-flex flex-column gap-2" style="padding: 0.5rem;">
                                            @csrf
                                            <input type="hidden" name="peminjaman_id" value="{{ $p->id }}">

                                            <input type="text" name="catatan_admin" class="form-control form-control-sm"
                                                placeholder="Catatan / alasan (opsional)" style="font-size: 0.875rem;">

                                            <div class="d-flex justify-content-center">
                                                <button class="btn btn-success aksi-btn me-2" name="action" value="approve"
                                                    onclick="return confirm('Setujui pengajuan ini?\n\nPengajuan lain yang bentrok jadwal akan otomatis ditolak.');">
                                                    <i class="bi bi-check-circle me-1"></i>Setujui
                                                </button>

                                                <button class="btn btn-danger aksi-btn" name="action" value="reject"
                                                    onclick="return confirm('Tolak pengajuan ini?');">
                                                    <i class="bi bi-x-circle me-1"></i>Tolak
                                                </button>
                                            </div>
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

@push('scripts')
<script>
    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const table = document.getElementById('tablePersetujuan');
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];

            // Skip empty state row
            if (row.cells.length === 1) continue;

            const text = row.textContent.toLowerCase();

            if (text.includes(searchValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });

    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
</script>
@endpush
</x-layouts.admin-layout>
