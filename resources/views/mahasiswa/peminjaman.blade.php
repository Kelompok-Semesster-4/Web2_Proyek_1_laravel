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
                    colspan="7">
                    <x-slot name="head">
                        <tr>
                            <th class="text-center" style="width: 50px; padding: 15px 10px;">
                                <i class="bi bi-hash"></i>
                            </th>
                            <th class="text-start" style="width: 120px; padding: 15px;">
                                <i class="bi bi-door-closed me-1"></i>Ruangan
                            </th>
                            <th class="text-center" style="width: 12%; padding: 15px 10px;">
                                <i class="bi bi-calendar-event me-1"></i>Waktu
                            </th>
                            <th class="text-center" style="width: 220px; padding: 15px;">
                                <i class="bi bi-card-text me-1"></i>Kegiatan
                            </th>
                            <th class="text-center" style="width: 10%; padding: 15px;">
                                <i class="bi bi-patch-check me-1"></i>Status
                            </th>
                            <th class="text-center" style="width: 180px; padding: 15px;">
                                <i class="bi bi-chat-left-text me-1"></i>Catatan
                            </th>
                            <th class="text-center" style="width: 100px; padding: 15px 8px; white-space: nowrap;">
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
                                $statusBg = 'linear-gradient(135deg, #ef4444, #dc2626)';
                            } else {
                                $statusBg = 'linear-gradient(135deg, #94a3b8, #64748b)';
                            }
                        @endphp

                        <tr>
                            <td class="text-center" data-label="#">
                                <span class="badge-number">{{ $i + 1 }}</span>
                            </td>

                            <td data-label="Ruangan">
                                <div class="fw-bold text-dark" style="font-size: 1rem;">
                                    {{ $p->nama_ruangan }}
                                </div>
                                <small class="text-muted" style="font-size: 0.85rem;">
                                    <i class="bi bi-building me-1"></i>{{ $p->gedung ?? '-' }}
                                </small>
                            </td>

                            <td class="text-center" data-label="Waktu">
                                <div class="text-dark" style="font-size: 1rem;">
                                    {{ $p->tanggal }}
                                </div>
                                <small class="text-muted" style="font-size: 0.85rem;">
                                    {{ substr($p->jam_mulai, 0, 5) }} - {{ substr($p->jam_selesai, 0, 5) }}
                                </small>
                            </td>

                            <td class="text-center" data-label="Kegiatan">
                                <span class="badge px-3 py-2"
                                    style="background: linear-gradient(135deg, #22c55e, #16a34a); color: white; font-weight: 600; border-radius: 8px;">
                                    <i class="bi bi-people-fill me-1"></i><span
                                        class="riwayat-kegiatan-text">{{ $p->nama_kegiatan }}</span>
                                </span>
                            </td>

                            <td class="text-center" data-label="Status">
                                <span class="badge px-3 py-2"
                                    style="background: {{ $statusBg }}; color: white; font-weight: 600; border-radius: 8px;">
                                    {{ $p->nama_status }}
                                </span>
                            </td>

                            <td class="{{ empty($p->catatan_admin) ? 'text-center' : '' }}" data-label="Catatan">
                                @if (!empty($p->catatan_admin))
                                    <div class="riwayat-note">
                                        <small class="text-muted riwayat-note-preview">
                                            <span class="riwayat-note-text" data-note="{{ e($p->catatan_admin) }}">{{ $p->catatan_admin }}</span>
                                        </small>
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td data-label="Aksi">
                                <div class="d-flex gap-1 justify-content-center">
                                    @if ($statusId === 1)
                                        <form method="POST" action="{{ route('mahasiswa.peminjaman.cancel') }}"
                                            class="d-inline cancel-pengajuan-form">
                                            @csrf
                                            <input type="hidden" name="peminjaman_id" value="{{ $p->id }}">

                                            <button type="button" class="btn btn-danger aksi-btn cancel-pengajuan-btn"
                                                style="min-width: 90px; font-size: 0.8rem;">
                                                <i class="bi bi-x-circle-fill me-1"></i>Batalkan
                                            </button>
                                        </form>
                                    @elseif ($statusId === 2)
                                        <span class="riwayat-action-note is-approved">
                                            <i class="bi bi-check-circle-fill me-1"></i>Terkunci
                                        </span>
                                    @else
                                        <span class="riwayat-action-note">
                                            <i class="bi bi-dash-circle me-1"></i>Tidak ada aksi
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </x-table-card>
            </div>
        </div>

        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                document.querySelectorAll('.cancel-pengajuan-btn').forEach(function (button) {
                    button.addEventListener('click', function () {
                        const form = this.closest('.cancel-pengajuan-form');

                        Swal.fire({
                            title: 'Batalkan pengajuan ini?',
                            text: 'Pengajuan akan diproses sebagai dibatalkan.',
                            icon: 'warning',
                            width: 390,
                            padding: '0.85rem',
                            showCancelButton: true,
                            confirmButtonColor: '#dc3545',
                            cancelButtonColor: '#6c757d',
                            cancelButtonText: 'Tidak',
                            confirmButtonText: '<i class="bi bi-x-circle me-1"></i>Batalkan',
                            customClass: {
                                popup: 'swal-compact',
                                title: 'swal-compact-title',
                                htmlContainer: 'swal-compact-text',
                                confirmButton: 'swal-compact-btn',
                                cancelButton: 'swal-compact-btn'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    });
                });

                document.querySelectorAll('.riwayat-note-text').forEach(function (text) {
                    text.addEventListener('click', function () {
                        Swal.fire({
                            title: 'Catatan Admin',
                            text: this.dataset.note || '-',
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

                const riwayatSearchInput = document.getElementById('tableRiwayatSearch');
                const riwayatTableBody = document.querySelector('#tableRiwayat tbody');
                const riwayatEmptyRow = document.createElement('tr');
                riwayatEmptyRow.className = 'riwayat-search-empty-row';
                riwayatEmptyRow.style.display = 'none';
                riwayatEmptyRow.innerHTML = `
                    <td colspan="7" class="text-center py-5">
                        <div class="text-muted">
                            <i class="bi bi-search display-5 d-block mb-3"></i>
                            <p class="mb-0 fw-semibold">Belum ada riwayat peminjaman untuk ruangan ini.</p>
                            <small>Coba gunakan kata kunci lain.</small>
                        </div>
                    </td>
                `;

                if (riwayatTableBody) {
                    riwayatTableBody.appendChild(riwayatEmptyRow);
                }

                riwayatSearchInput?.addEventListener('input', function () {
                    const searchValue = this.value.toLowerCase().trim();
                    const tableRows = document.querySelectorAll('#tableRiwayat tbody tr:not(.riwayat-search-empty-row)');
                    let visibleCount = 0;

                    tableRows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        if (text.includes(searchValue)) {
                            row.style.display = '';
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    riwayatEmptyRow.style.display = searchValue && visibleCount === 0 ? '' : 'none';
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
