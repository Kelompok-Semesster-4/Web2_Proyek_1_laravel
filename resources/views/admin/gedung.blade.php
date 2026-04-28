<x-layouts.admin-layout>

<div class="admin-container" style="max-width: 100%;">
    <!-- Page Header -->
    <x-head-title-admin
        title="Kelola Gedung"
        icon="bi bi-building"
        buttonText="Tambah Gedung"
        modalTarget="#modalAddGedung"
    />
    <!-- Alert Messages -->
    <x-alert-admin entity="Gedung" />
    
    <!-- Card Tabel Gedung -->
    <x-table-card
    title="Daftar Gedung"
    icon="bi bi-building"
    table-id="tableGedung"
    search-placeholder="Cari gedung..."
    :total="count($gedungs)"
    total-label="gedung terdaftar"
    :empty="empty($gedungs)"
    empty-title="Belum ada data gedung"
    empty-subtitle="Tambahkan gedung pertama Anda"
    :colspan="5"
    body-max-height="420px"
    :sticky-head="true"
>
    <x-slot name="head">
        <tr>
            <th class="text-center" style="width: 50px; padding: 15px 10px;">
                <i class="bi bi-hash"></i>
            </th>
            <th class="text-center" style="width: 35%; padding: 15px;">
                <i class="bi bi-building me-1"></i>Nama Gedung
            </th>
            <th class="text-center" style="width: 15%; padding: 15px;">
                <i class="bi bi-layers me-1"></i>Lantai
            </th>
            <th class="text-center" style="width: 20%; padding: 15px;">
                <i class="bi bi-door-closed me-1"></i>Ruangan
            </th>
            <th class="text-center" style="width: 280px; padding: 15px;">
                <i class="bi bi-gear me-1"></i>Aksi
            </th>
        </tr>
    </x-slot>

    @foreach ($gedungs as $i => $gedung)
        <tr>
            <td class="text-center">
                <span class="badge-number">{{ $i + 1 }}</span>
            </td>

            <td class="text-center">
                <span class="badge px-3 py-2"
                    style="background: linear-gradient(135deg, #17a2b8, #138496); color: white; font-weight: 600; border-radius: 8px;">
                    <i class="bi bi-building-fill me-1"></i>
                    <span class="gedung-name">{{ $gedung->nama_gedung }}</span>
                </span>
            </td>

            <td class="text-center">
                <span class="badge px-3 py-2"
                    style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; font-weight: 600; border-radius: 8px;">
                    <i class="bi bi-layers-fill me-1"></i>{{ $gedung->jumlah_lantai }}
                </span>
            </td>

            <td class="text-center">
                <span class="badge px-3 py-2"
                    style="background: linear-gradient(135deg, #22c55e, #16a34a); color: white; font-weight: 600; border-radius: 8px;">
                    <i class="bi bi-door-closed-fill me-1"></i>{{ $gedung->jumlah_ruangan }} ruangan
                </span>
            </td>

            <td>
                <div class="d-flex gap-1 justify-content-center">
                    <button type="button"
                        class="btn btn-info aksi-btn"
                        style="min-width: 65px; font-size: 0.8rem;"
                        onclick="viewDetailGedung('{{ addslashes($gedung->nama_gedung) }}', '{{ $gedung->jumlah_lantai }}', '{{ $gedung->jumlah_ruangan }}')">
                        <i class="bi bi-eye-fill me-1"></i>Detail
                    </button>

                    <button type="button"
                        class="btn btn-warning aksi-btn"
                        style="min-width: 60px; font-size: 0.8rem;"
                        data-bs-toggle="modal"
                        data-bs-target="#modalEditGedung"
                        onclick="editGedung({{ $gedung->id }}, '{{ addslashes($gedung->nama_gedung) }}', {{ $gedung->jumlah_lantai }})">
                        <i class="bi bi-pencil-fill me-1"></i>Edit
                    </button>

                    <form action="{{ route('admin.gedung.destroy', $gedung->id) }}"
                        method="POST"
                        class="d-inline"
                        id="formDelete{{ $gedung->id }}">
                        @csrf
                        @method('DELETE')

                        <button type="button"
                            class="btn btn-danger aksi-btn"
                            style="min-width: 65px; font-size: 0.8rem;"
                            onclick="deleteGedung({{ $gedung->id }}, '{{ addslashes($gedung->nama_gedung) }}')">
                            <i class="bi bi-trash-fill me-1"></i>Hapus
                        </button>
                    </form>
                    </div>
                </td>
            </tr>
        @endforeach
    </x-table-card>

<!-- Modal Add Gedung -->
<x-modal-admin
    id="modalAddGedung"
    title="Tambah Gedung Baru"
    icon="bi bi-plus-circle-fill"
>
    <form action="{{ route('admin.gedung.store') }}" method="POST">
        @csrf
        <div class="modal-body p-4">
            <div class="mb-3">
                <label for="addNamaGedung" class="form-label fw-semibold">
                    <i class="bi bi-building me-1" style="color: #22c55e;"></i>Nama Gedung
                    <span class="text-danger">*</span>
                </label>
                <input type="text" class="form-control" id="addNamaGedung" name="nama_gedung" maxlength="100" required placeholder="Contoh: Gedung A">
                <small class="text-muted">Nama gedung harus unik</small>
            </div>
            <div class="mb-3">
                <label for="addJumlahLantai" class="form-label fw-semibold">
                    <i class="bi bi-layers me-1" style="color: #22c55e;"></i>Jumlah Lantai
                    <span class="text-danger">*</span>
                </label>
                <input type="number" class="form-control" id="addJumlahLantai" name="jumlah_lantai" min="1" required placeholder="Contoh: 3">
                <small class="text-muted">Lantai akan dibuat otomatis (contoh: isi 3 = dibuatkan lantai 1, 2, 3)</small>
            </div>
        </div>
        <div class="modal-footer border-0 pt-0">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-success">
                <i class="bi bi-check-circle me-1"></i>Simpan
            </button>
        </div>
    </form>
</x-modal-admin>

<!-- Modal Edit Gedung -->
<x-modal-admin
    id="modalEditGedung"
    title="Edit Gedung"
    icon="bi bi-pencil-square"
    header-gradient="linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%)"
>
    <form action="{{ route('admin.gedung.update') }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body p-4">
            <input type="hidden" id="editGedungId" name="id">
            <div class="mb-3">
                <label for="editNamaGedung" class="form-label fw-semibold">
                    <i class="bi bi-building me-1" style="color: #22c55e;"></i>Nama Gedung
                    <span class="text-danger">*</span>
                </label>
                <input type="text" class="form-control" id="editNamaGedung" name="nama_gedung" maxlength="100" required placeholder="Contoh: Gedung A">
                <small class="text-muted">Nama gedung harus unik</small>
            </div>
            <div class="mb-3">
                <label for="editJumlahLantai" class="form-label fw-semibold">
                    <i class="bi bi-layers me-1" style="color: #22c55e;"></i>Jumlah Lantai
                    <span class="text-danger">*</span>
                </label>
                <input type="number" class="form-control" id="editJumlahLantai" name="jumlah_lantai" min="1" required placeholder="Contoh: 3">
                <small class="text-muted text-warning"><i class="bi bi-exclamation-triangle-fill"></i> Mengurangi jumlah lantai akan menghapus lantai atas jika belum ada ruangan.</small>
            </div>
        </div>
        <div class="modal-footer border-0 pt-0">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-warning">
                <i class="bi bi-check-circle me-1"></i>Perbarui
            </button>
        </div>
    </form>
</x-modal-admin>

<!-- Modal Detail Gedung -->
<x-modal-admin
    id="modalViewDetailGedung"
    title="Detail Gedung"
    icon="bi bi-eye-fill"
    header-gradient="linear-gradient(135deg, #22b8cf 0%, #0ea5b7 100%)"
>
    <div class="modal-body p-4">
        <div class="mb-3">
            <label class="form-label fw-semibold text-muted mb-1">Nama Gedung</label>
            <div id="detailGedungNama" class="fw-bold fs-5"></div>
        </div>
        <div class="row">
            <div class="col-6">
                <label class="form-label fw-semibold text-muted mb-1">Jumlah Lantai</label>
                <div id="detailGedungLantai" class="fw-bold"></div>
            </div>
            <div class="col-6">
                <label class="form-label fw-semibold text-muted mb-1">Jumlah Ruangan</label>
                <div id="detailGedungRuangan" class="fw-bold"></div>
            </div>
        </div>
    </div>
    <div class="modal-footer bg-light border-0">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
    </div>
</x-modal-admin>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Search functionality
    const gedungSearchInput = document.getElementById('tableGedungSearch') || document.getElementById('searchInput');
    if (gedungSearchInput) {
        gedungSearchInput.addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('#tableGedung tbody tr');

            tableRows.forEach(row => {
                const gedungName = row.querySelector('.gedung-name')?.textContent.toLowerCase() || '';
                row.style.display = gedungName.includes(searchValue) ? '' : 'none';
            });
        });
    }

    // View detail gedung
    function viewDetailGedung(namaGedung, jumlahLantai, jumlahRuangan) {
        document.getElementById('detailGedungNama').textContent = namaGedung || '-';
        document.getElementById('detailGedungLantai').textContent = `${jumlahLantai || 0} lantai`;
        document.getElementById('detailGedungRuangan').textContent = `${jumlahRuangan || 0} ruangan`;

        const modal = new bootstrap.Modal(document.getElementById('modalViewDetailGedung'));
        modal.show();
    }

    // Edit gedung function
    function editGedung(id, namaGedung, jumlahLantai) {
        document.getElementById('editGedungId').value = id;
        document.getElementById('editNamaGedung').value = namaGedung;
        document.getElementById('editJumlahLantai').value = jumlahLantai;
    }

    // Delete gedung with confirmation
    function deleteGedung(id, namaGedung) {
        Swal.fire({
            title: 'Hapus Gedung?',
            html: `<strong>${namaGedung}</strong><br><small class="text-muted">Data ini akan dihapus secara permanen</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-trash me-2"></i>Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('formDelete' + id).submit();
            }
        });
    }

    // Reset add form when modal is closed
    document.getElementById('modalAddGedung').addEventListener('hidden.bs.modal', function() {
        document.getElementById('addNamaGedung').value = '';
        document.getElementById('addJumlahLantai').value = '';
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
</x-layouts.admin-layout>
