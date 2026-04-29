<x-layouts.mahasiswa-layout>
    <x-slot:title>Peminjaman - Peminjaman Ruangan</x-slot>

        @push('styles')
            <style>
                .peminjaman-page {
                    padding-top: 30px;
                    padding-bottom: 30px;
                    min-height: calc(100vh - 68px);
                }
            </style>
        @endpush

        <div class="wrap peminjaman-page">
            <div class="container">
                <!--HEAD-->
                @include('components.Head-Peminjaman')

                <!--SUCCES And ERORR ALERT-->
                @include('components.Error-user')
                <!--FORM-->
                @include('components.Form_peminjaman')

                <!-- TABEL RIWAYAT -->
                <x-table-card title="Riwayat Pengajuan" icon="bi bi-clock-history" table-id="tableRiwayat"
                    search-placeholder="Cari ruangan, kegiatan..." :total="$riwayat->count()"
                    total-label="pengajuan terdaftar" :empty="$riwayat->isEmpty()"
                    empty-title="Belum ada pengajuan peminjaman" empty-subtitle="Ajukan peminjaman ruangan pertama Anda"
                    colspan="8">
                    <x-slot name="head">
                        <tr>
                            <th class="text-center" style="width: 50px; padding: 15px 10px;">
                                <i class="bi bi-hash"></i>
                            </th>
                            <th class="text-center" style="width: 20%; padding: 15px;">
                                <i class="bi bi-door-closed me-1"></i>Ruangan
                            </th>
                            <th class="text-center" style="width: 12%; padding: 15px;">
                                <i class="bi bi-calendar-event me-1"></i>Tanggal
                            </th>
                            <th class="text-center" style="width: 10%; padding: 15px;">
                                <i class="bi bi-clock me-1"></i>Jam
                            </th>
                            <th class="text-center" style="width: 12%; padding: 15px;">
                                <i class="bi bi-card-text me-1"></i>Kegiatan
                            </th>
                            <th class="text-center" style="width: 10%; padding: 15px;">
                                <i class="bi bi-patch-check me-1"></i>Status
                            </th>
                            <th class="text-center" style="width: 20%; padding: 15px;">
                                <i class="bi bi-chat-left-text me-1"></i>Catatan
                            </th>
                            <th class="text-center" style="width: 210px; padding: 15px; white-space: nowrap;">
                                <i class="bi bi-gear me-1"></i>Aksi
                            </th>
                        </tr>
                    </x-slot>

                    @foreach ($riwayat as $i => $p)
                        @php
                            $statusId = (int) $p->status_id;

                            if ($statusId === 1) {
                                $statusBg = 'linear-gradient(135deg, #f59e0b, #d97706)';
                            } elseif ($statusId === 2) {
                                $statusBg = 'linear-gradient(135deg, #22c55e, #16a34a)';
                            } elseif ($statusId === 3) {
                                $statusBg = 'linear-gradient(135deg, #ef4444, #dc2626)';
                            } elseif ($statusId === 4) {
                                $statusBg = 'linear-gradient(135deg, #6b7280, #4b5563)';
                            } elseif ($statusId === 5) {
                                $statusBg = 'linear-gradient(135deg, #f97316, #ea580c)';
                            } else {
                                $statusBg = 'linear-gradient(135deg, #94a3b8, #64748b)';
                            }
                        @endphp

                        <tr>
                            <td class="text-center">
                                <span class="badge-number">{{ $i + 1 }}</span>
                            </td>

                            <td>
                                <div class="fw-bold text-dark" style="font-size: 1rem;">
                                    {{ $p->nama_ruangan }}
                                </div>
                                <small class="text-muted" style="font-size: 0.85rem;">
                                    <i class="bi bi-building me-1"></i>{{ $p->gedung ?? '-' }}
                                </small>
                            </td>

                            <td class="text-center">
                                <span class="badge px-3 py-2"
                                    style="background: linear-gradient(135deg, #17a2b8, #138496); color: white; font-weight: 600; border-radius: 8px;">
                                    <i class="bi bi-calendar-fill me-1"></i>{{ $p->tanggal }}
                                </span>
                            </td>

                            <td class="text-center">
                                <span class="badge px-3 py-2"
                                    style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; font-weight: 600; border-radius: 8px;">
                                    <i class="bi bi-clock-fill me-1"></i>
                                    {{ substr($p->jam_mulai, 0, 5) }} - {{ substr($p->jam_selesai, 0, 5) }}
                                </span>
                            </td>

                            <td class="text-center">
                                <span class="badge px-3 py-2"
                                    style="background: linear-gradient(135deg, #22c55e, #16a34a); color: white; font-weight: 600; border-radius: 8px;">
                                    <i class="bi bi-people-fill me-1"></i>{{ $p->nama_kegiatan }}
                                </span>
                            </td>

                            <td class="text-center">
                                <span class="badge px-3 py-2"
                                    style="background: {{ $statusBg }}; color: white; font-weight: 600; border-radius: 8px;">
                                    {{ $p->nama_status }}
                                </span>
                            </td>

                            <td>
                                @if (!empty($p->catatan_admin))
                                    <small class="text-muted" style="font-size: 0.85rem;">
                                        <i class="bi bi-info-circle me-1"></i>
                                        {{ substr($p->catatan_admin, 0, 60) }}{{ strlen($p->catatan_admin) > 60 ? '...' : '' }}
                                    </small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td>
                                <div class="d-flex gap-1 justify-content-center">
                                    @if ($statusId === 1)
                                        <form method="POST" action="{{ route('mahasiswa.peminjaman.cancel') }}"
                                            onsubmit="return confirm('Batalkan pengajuan ini?');" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="peminjaman_id" value="{{ $p->id }}">

                                            <button class="btn btn-danger aksi-btn" style="min-width: 90px; font-size: 0.8rem;">
                                                <i class="bi bi-x-circle-fill me-1"></i>Batalkan
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </x-table-card>
            </div>
        </div>

        @push('scripts')
            <script>
                document.getElementById('searchInput')?.addEventListener('keyup', function () {
                    const searchValue = this.value.toLowerCase();
                    const tableRows = document.querySelectorAll('#tableRiwayat tbody tr');

                    tableRows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        if (text.includes(searchValue)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });

                // Auto-dismiss alerts after 5 seconds
                document.addEventListener('DOMContentLoaded', function () {
                    const alerts = document.querySelectorAll('.alert-group .alert');
                    alerts.forEach(alert => {
                        setTimeout(() => {
                            const bsAlert = new bootstrap.Alert(alert);
                            bsAlert.close();
                        }, 5000);
                    });
                });
            </script>
        @endpush
</x-layouts.mahasiswa-layout>