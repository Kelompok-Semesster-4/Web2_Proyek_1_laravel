<x-admin-layout>

<div class="admin-container" style="max-width: 100%;">
    <!-- Page Header -->
    <div class="kelola-header mb-4">
        <h1>Kelola Lantai</h1>
        <button class="btn-tambah" data-bs-toggle="modal" data-bs-target="#modalAddLantai">
            <i class="bi bi-plus-circle-fill me-2"></i>Tambah Lantai
        </button>
    </div>

    <!-- Alert Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            @switch(session('success'))
                @case('add')
                    <strong>Berhasil!</strong> Lantai berhasil ditambahkan.
                    @break
                @case('edit')
                    <strong>Berhasil!</strong> Lantai berhasil diperbarui.
                    @break
                @case('delete')
                    <strong>Berhasil!</strong> Lantai berhasil dihapus.
                    @break
            @endswitch
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Error!</strong>
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Card Tabel Lantai -->
    <div class="card shadow border-0" style="border-radius: 15px; overflow: hidden;">
        <div class="card-header bg-white py-3 border-bottom" style="background: linear-gradient(to right, #f8f9fa, #e9ecef) !important;">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0 fw-bold" style="color: #495057;">
                        <i class="bi bi-layers me-2" style="color: #22c55e;"></i>Daftar Lantai
                    </h5>
                </div>
                <div class="col-md-6">
                    <div class="input-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search" style="color: #22c55e;"></i>
                        </span>
                        <input type="text" class="form-control border-start-0 bg-white" id="searchInput" placeholder="Cari nomor lantai..." style="border-left: 0;">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tableLantai">
                    <thead style="background: linear-gradient(to right, #f8f9fa, #e9ecef);">
                        <tr>
                            <th class="text-center" style="width: 50px; padding: 15px 10px;">
                                <i class="bi bi-hash"></i>
                            </th>
                            <th class="text-center" style="width: 25%; padding: 15px;">
                                <i class="bi bi-layers me-1"></i>Nomor Lantai
                            </th>
                            <th class="text-center" style="width: 25%; padding: 15px;">
                                <i class="bi bi-door-closed me-1"></i>Ruangan
                            </th>
                            <th class="text-center" style="width: 280px; padding: 15px;">
                                <i class="bi bi-gear me-1"></i>Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (empty($lantais))
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-inbox display-4 d-block mb-3"></i>
                                        <p class="mb-0">Belum ada data lantai</p>
                                        <small>Tambahkan lantai pertama Anda</small>
                                    </div>
                                </td>
                            </tr>
                        @else
                            @foreach ($lantais as $i => $lantai)
                                <tr class="lantai-row">
                                    <td class="text-center">
                                        <span class="badge-number">{{ $i + 1 }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge px-3 py-2" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; font-weight: 600; border-radius: 8px;">
                                            <i class="bi bi-layers-fill me-1"></i>{{ $lantai->nomor }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge px-3 py-2" style="background: linear-gradient(135deg, #22c55e, #16a34a); color: white; font-weight: 600; border-radius: 8px;">
                                            <i class="bi bi-door-closed-fill me-1"></i>{{ $lantai->jumlah_ruangan_total }} ruangan
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 justify-content-center">
                                            <button type="button" class="btn btn-info aksi-btn" style="min-width: 65px; font-size: 0.8rem;"
                                                onclick="viewDetailLantai({{ $lantai->nomor }}, {{ $lantai->jumlah_ruangan_total }})">
                                                <i class="bi bi-eye-fill me-1"></i>Detail
                                            </button>
                                            <button type="button" class="btn btn-warning aksi-btn" style="min-width: 60px; font-size: 0.8rem;"
                                                onclick="handleEditByNomor({{ $lantai->nomor }})">
                                                <i class="bi bi-pencil-fill me-1"></i>Edit
                                            </button>
                                            <button type="button" class="btn btn-danger aksi-btn" style="min-width: 65px; font-size: 0.8rem;"
                                                onclick="handleDeleteByNomor({{ $lantai->nomor }})">
                                                <i class="bi bi-trash-fill me-1"></i>Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted fw-semibold">
                    <i class="bi bi-info-circle-fill me-1" style="color: #22c55e;"></i>Total Data:
                    <span class="badge ms-1" style="background: linear-gradient(135deg, #22c55e, #16a34a);">{{ count($lantais) }}</span>
                    lantai terdaftar
                </small>
                <small class="text-muted">
                    <i class="bi bi-calendar-check me-1"></i>{{ date('d F Y') }}
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add Lantai -->
<div class="modal fade" id="modalAddLantai" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); border: none;">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-plus-circle-fill me-2"></i>Tambah Lantai Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.lantai.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="addGedung" class="form-label fw-semibold">
                            <i class="bi bi-building me-1" style="color: #22c55e;"></i>Pilih Gedung
                            <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="addGedung" name="gedung_id" required>
                            <option value="">-- Pilih Gedung --</option>
                            @foreach ($gedungs as $gedung)
                                <option value="{{ $gedung->id }}">
                                    {{ $gedung->nama_gedung }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="addNomor" class="form-label fw-semibold">
                            <i class="bi bi-layers me-1" style="color: #22c55e;"></i>Nomor Lantai
                            <span class="text-danger">*</span>
                        </label>
                        <input type="number" class="form-control" id="addNomor" name="nomor" min="1" required placeholder="Contoh: 1, 2, 3...">
                        <small class="text-muted">Nomor lantai harus unik per gedung</small>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Lantai -->
<div class="modal fade" id="modalEditLantai" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); border: none;">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pencil-square me-2"></i>Edit Lantai
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.lantai.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <input type="hidden" id="editLantaiId" name="id">
                    <div class="mb-3">
                        <label for="editGedung" class="form-label fw-semibold">
                            <i class="bi bi-building me-1" style="color: #22c55e;"></i>Pilih Gedung
                            <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="editGedung" name="gedung_id" required>
                            <option value="">-- Pilih Gedung --</option>
                            @foreach ($gedungs as $gedung)
                                <option value="{{ $gedung->id }}">
                                    {{ $gedung->nama_gedung }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editNomor" class="form-label fw-semibold">
                            <i class="bi bi-layers me-1" style="color: #22c55e;"></i>Nomor Lantai
                            <span class="text-danger">*</span>
                        </label>
                        <input type="number" class="form-control" id="editNomor" name="nomor" min="1" required placeholder="Contoh: 1, 2, 3...">
                        <small class="text-muted">Nomor lantai harus unik per gedung</small>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-check-circle me-1"></i>Perbarui
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail Lantai -->
<div class="modal fade" id="modalViewDetailLantai" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #22b8cf 0%, #0ea5b7 100%); border: none;">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-eye-fill me-2"></i>Detail Lantai
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-semibold text-muted mb-1">Nomor Lantai</label>
                    <div id="detailLantaiNomor" class="fw-bold fs-5"></div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <label class="form-label fw-semibold text-muted mb-1">Jumlah Gedung</label>
                        <div id="detailLantaiJumlahGedung" class="fw-bold"></div>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold text-muted mb-1">Total Ruangan (Semua Gedung)</label>
                        <div id="detailLantaiJumlahRuangan" class="fw-bold"></div>
                    </div>
                </div>
                <hr>
                <label class="form-label fw-semibold text-muted mb-2">Rincian per Gedung</label>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Gedung</th>
                                <th class="text-center">Ruangan</th>
                                <th class="text-center" style="width: 170px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="detailLantaiListPerGedung"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Form untuk hapus lantai tersembunyi -->
<form id="formDeleteLantai" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const lantaiDetailMap = @json($lantaiDetailMap);

    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('#tableLantai tbody tr.lantai-row');

        tableRows.forEach(row => {
            const rowText = row.textContent.toLowerCase();

            if (rowText.includes(searchValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // View detail lantai
    function viewDetailLantai(nomor, jumlahRuangan) {
        document.getElementById('detailLantaiNomor').textContent = nomor || '-';
        document.getElementById('detailLantaiJumlahRuangan').textContent = `${jumlahRuangan || 0} ruangan`;

        const detailRows = lantaiDetailMap[String(nomor)] || [];
        document.getElementById('detailLantaiJumlahGedung').textContent = `${detailRows.length} gedung`;

        const tbody = document.getElementById('detailLantaiListPerGedung');
        if (!detailRows.length) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center text-muted">Tidak ada data rincian gedung</td>
                </tr>
            `;
        } else {
            tbody.innerHTML = detailRows.map((item) => `
                <tr>
                    <td>${escapeHtml(item.nama_gedung || '-')}</td>
                    <td class="text-center">${item.jumlah_ruangan || 0}</td>
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditLantai"
                                onclick="editLantai(${item.id}, ${item.gedung_id}, ${nomor});">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm"
                                onclick="deleteLantai(${item.id}, 'Lantai ${escapeHtml(nomor)}', '${escapeHtml(item.nama_gedung || '-')}')">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        const modal = new bootstrap.Modal(document.getElementById('modalViewDetailLantai'));
        modal.show();
    }

    // Main-table action handlers for grouped lantai rows
    function handleEditByNomor(nomor) {
        const detailRows = lantaiDetailMap[String(nomor)] || [];

        if (!detailRows.length) {
            Swal.fire({
                icon: 'info',
                title: 'Data tidak ditemukan',
                text: `Tidak ada data lantai ${nomor}.`
            });
            return;
        }

        if (detailRows.length === 1) {
            const item = detailRows[0];
            editLantai(item.id, item.gedung_id, nomor);
            const modal = new bootstrap.Modal(document.getElementById('modalEditLantai'));
            modal.show();
            return;
        }

        const totalRuangan = detailRows.reduce((sum, item) => sum + Number(item.jumlah_ruangan || 0), 0);
        Swal.fire({
            icon: 'info',
            title: 'Pilih Gedung Terlebih Dahulu',
            text: `Lantai ${nomor} ada di ${detailRows.length} gedung. Pilih gedung pada modal detail.`,
            confirmButtonColor: '#0ea5b7'
        }).then(() => viewDetailLantai(nomor, totalRuangan));
    }

    function handleDeleteByNomor(nomor) {
        const detailRows = lantaiDetailMap[String(nomor)] || [];

        if (!detailRows.length) {
            Swal.fire({
                icon: 'info',
                title: 'Data tidak ditemukan',
                text: `Tidak ada data lantai ${nomor}.`
            });
            return;
        }

        if (detailRows.length === 1) {
            const item = detailRows[0];
            deleteLantai(item.id, `Lantai ${nomor}`, item.nama_gedung || '-');
            return;
        }

        const totalRuangan = detailRows.reduce((sum, item) => sum + Number(item.jumlah_ruangan || 0), 0);
        Swal.fire({
            icon: 'info',
            title: 'Pilih Gedung Terlebih Dahulu',
            text: `Lantai ${nomor} ada di ${detailRows.length} gedung. Pilih gedung pada modal detail.`,
            confirmButtonColor: '#0ea5b7'
        }).then(() => viewDetailLantai(nomor, totalRuangan));
    }

    // Edit lantai function
    function editLantai(id, gedungId, nomor) {
        document.getElementById('editLantaiId').value = id;
        document.getElementById('editGedung').value = gedungId;
        document.getElementById('editNomor').value = nomor;
    }

    // Delete lantai with confirmation
    function deleteLantai(id, lantaiInfo, gedungName) {
        Swal.fire({
            title: 'Hapus Lantai?',
            html: `<strong>${gedungName} - ${lantaiInfo}</strong><br><small class="text-muted">Data ini akan dihapus secara permanen</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-trash me-2"></i>Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('formDeleteLantai');
                form.action = "{{ url('admin/lantai') }}/" + id;
                form.submit();
            }
        });
    }

    // Reset add form when modal is closed
    document.getElementById('modalAddLantai').addEventListener('hidden.bs.modal', function() {
        document.getElementById('addGedung').value = '';
        document.getElementById('addNomor').value = '';
    });

    // Auto-dismiss alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const alertsToClose = document.querySelectorAll('.alert');
        alertsToClose.forEach(alert => {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });
    });
</script>
@endpush
</x-admin-layout>
