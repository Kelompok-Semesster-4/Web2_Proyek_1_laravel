<x-layouts.admin-layout>

<div class="admin-container" style="max-width: 100%;">
    <!-- Page Header -->
    <x-head-title-admin
    title="Persetujuan Peminjaman"
    icon="bi bi-clipboard-check"
    :showButton="false"
    />
    <!-- Alert Messages -->
    <x-alert-admin entity="Pengajuan" />

    <!-- Card Tabel Persetujuan -->
    <x-table-card
    title="Daftar Persetujuan"
    icon="bi bi-list-ul"
    table-id="tablePersetujuan"
    search-placeholder="Cari mahasiswa, ruangan, kegiatan..."
    :total="count($pending)"
    total-label="pengajuan menunggu"
    :empty="empty($pending)"
    empty-title="Belum ada pengajuan menunggu"
    empty-subtitle="Semua pengajuan telah diproses"
    :colspan="9"
    :show-footer="false"
>
    <x-slot name="head">
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
    </x-slot>

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
                    <a class="btn btn-warning"
                        href="{{ asset('storage/uploads/surat/' . $p->surat) }}"
                        target="_blank"
                        rel="noopener">
                        <i class="bi bi-file-earmark-pdf me-1"></i>Lihat
                    </a>
                @else
                    <span class="text-muted">-</span>
                @endif
            </td>

            <td>
                <form method="POST"
                    action="{{ route('admin.approve.process') }}"
                    class="d-flex flex-column gap-2"
                    style="padding: 0.5rem;">
                    @csrf

                    <input type="hidden" name="peminjaman_id" value="{{ $p->id }}">

                    <input type="text"
                        name="catatan_admin"
                        class="form-control form-control-sm"
                        placeholder="Catatan / alasan (opsional)"
                        style="font-size: 0.875rem;">

                    <div class="d-flex justify-content-center">
                        <button class="btn btn-success aksi-btn me-2"
                            name="action"
                            value="approve"
                            onclick="return confirm('Setujui pengajuan ini?\n\nPengajuan lain yang bentrok jadwal akan otomatis ditolak.');">
                            <i class="bi bi-check-circle me-1"></i>Setujui
                        </button>

                        <button class="btn btn-danger aksi-btn"
                            name="action"
                            value="reject"
                            onclick="return confirm('Tolak pengajuan ini?');">
                            <i class="bi bi-x-circle me-1"></i>Tolak
                        </button>
                        </div>
                    </form>
                </td>
            </tr>
        @endforeach
    </x-table-card>

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
