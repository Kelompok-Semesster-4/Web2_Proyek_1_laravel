<x-layouts.admin-layout>

<div class="admin-container" style="max-width: 100%;">
    <!-- Page Header -->
    <x-head-title-admin
        title="Kelola Ruangan"
        icon="bi bi-house-up"
        buttonText="Tambah Ruangan"
        modalTarget="#modalAddRuangan"
    />
    <!-- Alert Messages -->
    <x-alert-admin />

    <!-- Card Tabel Ruangan -->
    <x-table-card
    title="Daftar Ruangan"
    icon="bi bi-door-open"
    table-id="tableRuangan"
    search-placeholder="Cari ruangan, gedung..."
    :total="count($ruangans)"
    total-label="ruangan terdaftar"
    :empty="$ruangans->isEmpty()"
    empty-title="Belum ada data ruangan"
    empty-subtitle="Tambahkan ruangan pertama Anda"
    :colspan="7"
>
    <x-slot name="head">
        <tr>
            <th class="text-center" style="width: 50px; padding: 15px 10px;">
                <i class="bi bi-hash"></i>
            </th>
            <th class="text-center" style="width: 20%; padding: 15px;">
                <i class="bi bi-door-closed me-1"></i>Nama Ruangan
            </th>
            <th class="text-center" style="width: 12%; padding: 15px;">
                <i class="bi bi-building me-1"></i>Gedung
            </th>
            <th class="text-center" style="width: 10%; padding: 15px;">
                <i class="bi bi-layers me-1"></i>Lantai
            </th>
            <th class="text-center" style="width: 12%; padding: 15px;">
                <i class="bi bi-people me-1"></i>Kapasitas
            </th>
            <th class="text-center" style="width: 10%; padding: 15px;">
                <i class="bi bi-image me-1"></i>Foto
            </th>
            <th class="text-center" style="width: 280px; padding: 15px;">
                <i class="bi bi-gear me-1"></i>Aksi
            </th>
        </tr>
    </x-slot>

    @foreach ($ruangans as $i => $ruangan)
        <tr>
            <td class="text-center">
                <span class="badge-number">{{ $i + 1 }}</span>
            </td>

            <td>
                <div class="fw-bold text-dark" style="font-size: 1rem;">
                    {{ $ruangan->nama_ruangan }}
                </div>

                @if (!empty($ruangan->deskripsi))
                    <small class="text-muted" style="font-size: 0.85rem;">
                        <i class="bi bi-info-circle me-1"></i>
                        {{ Str::limit($ruangan->deskripsi, 50) }}
                    </small>
                @endif
            </td>

            <td>
                <span class="badge px-3 py-2"
                    style="background: linear-gradient(135deg, #17a2b8, #138496); color: white; font-weight: 600; border-radius: 8px;">
                    <i class="bi bi-building-fill me-1"></i>{{ $ruangan->gedung ?? '-' }}
                </span>
            </td>

            <td class="text-center">
                <span class="badge px-3 py-2"
                    style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; font-weight: 600; border-radius: 8px;">
                    <i class="bi bi-layers-fill me-1"></i>{{ $ruangan->Lantai ?? '-' }}
                </span>
            </td>

            <td class="text-center">
                <span class="badge px-3 py-2"
                    style="background: linear-gradient(135deg, #22c55e, #16a34a); color: white; font-weight: 600; border-radius: 8px;">
                    <i class="bi bi-people-fill me-1"></i>{{ $ruangan->kapasitas ?? '0' }} orang
                </span>
            </td>

            <td class="text-center">
                @if ($ruangan->cover_foto)
                    <img src="{{ asset('storage/uploads/ruangan/' . $ruangan->cover_foto) }}"
                        alt="{{ $ruangan->nama_ruangan }}"
                        class="rounded shadow-sm img-thumbnail"
                        style="width: 80px; height: 55px; object-fit: cover; cursor: pointer;"
                        data-bs-toggle="modal"
                        data-bs-target="#modalViewImage"
                        onclick="viewImage('{{ asset('storage/uploads/ruangan/' . $ruangan->cover_foto) }}', '{{ addslashes($ruangan->nama_ruangan) }}')">
                @else
                    <span class="badge bg-secondary bg-opacity-10 text-secondary">
                        <i class="bi bi-image"></i> No Image
                    </span>
                @endif

                <div class="small text-muted mt-1">
                    Detail: {{ $ruangan->detail_count ?? 0 }} foto
                </div>
            </td>

            <td>
                <div class="d-flex gap-1 justify-content-center">
                    <button class="btn btn-info aksi-btn"
                        style="min-width: 65px; font-size: 0.8rem;"
                        data-bs-toggle="modal"
                        data-bs-target="#modalViewDetail"
                        onclick="viewDetail(
                            {{ $ruangan->id }},
                            '{{ addslashes($ruangan->nama_ruangan) }}',
                            '{{ addslashes($ruangan->gedung ?? '') }}',
                            '{{ addslashes($ruangan->Lantai ?? '') }}',
                            {{ $ruangan->kapasitas ?? 0 }},
                            '{{ addslashes($ruangan->deskripsi ?? '') }}',
                            '{{ $ruangan->cover_foto ?? '' }}'
                        )">
                        <i class="bi bi-eye-fill me-1"></i>Detail
                    </button>

                    <button class="btn btn-warning aksi-btn"
                        style="min-width: 60px; font-size: 0.8rem;"
                        data-bs-toggle="modal"
                        data-bs-target="#modalEditRuangan"
                        onclick="editRuangan(
                            {{ $ruangan->id }},
                            '{{ addslashes($ruangan->nama_ruangan) }}',
                            {{ $ruangan->gedung_id ?? 0 }},
                            {{ $ruangan->lantai_id ?? 0 }},
                            {{ $ruangan->kapasitas ?? 0 }},
                            '{{ addslashes($ruangan->deskripsi ?? '') }}'
                        )">
                        <i class="bi bi-pencil-fill me-1"></i>Edit
                    </button>

                    <form action="{{ route('admin.ruangan.destroy', $ruangan->id) }}"
                        method="POST"
                        class="d-inline"
                        id="formDelete{{ $ruangan->id }}">
                        @csrf
                        @method('DELETE')

                        <button type="button"
                            class="btn btn-danger aksi-btn"
                            style="min-width: 65px; font-size: 0.8rem;"
                            onclick="deleteRuangan({{ $ruangan->id }}, '{{ addslashes($ruangan->nama_ruangan) }}')">
                            <i class="bi bi-trash-fill me-1"></i>Hapus
                        </button>
                    </form>
                </div>
            </td>
        </tr>
    @endforeach
    </x-table-card>

<!-- Modal Tambah Ruangan -->
<div class="modal fade" id="modalAddRuangan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); border: none;">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-plus-circle-fill me-2"></i>Tambah Ruangan Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.ruangan.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-door-closed me-1" style="color: #22c55e;"></i>Nama Ruangan
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" name="nama_ruangan" required placeholder="Contoh: Ruang 301">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-building me-1" style="color: #22c55e;"></i>Gedung
                                <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="addGedungSelect" required>
                                <option value="">-- Pilih Gedung --</option>
                                @foreach ($gedungList as $g)
                                    <option value="{{ $g->id }}">{{ $g->nama_gedung }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-layers me-1" style="color: #22c55e;"></i>Lantai
                                <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" name="lantai_id" id="addLantaiSelect" required disabled>
                                <option value="">-- Pilih Gedung Terlebih Dahulu --</option>
                            </select>
                            <small class="text-muted">Pilih gedung terlebih dahulu</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-people me-1" style="color: #22c55e;"></i>Kapasitas
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="number" class="form-control" name="kapasitas" placeholder="Jumlah orang" min="1" required>
                            <span class="input-group-text">
                                <i class="bi bi-person-fill"></i> orang
                            </span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-card-text me-1" style="color: #22c55e;"></i>Deskripsi
                        </label>
                        <textarea class="form-control" name="deskripsi" rows="3" placeholder="Keterangan ruangan (opsional)"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-check2-square me-1" style="color: #22c55e;"></i>Fasilitas
                        </label>
                        @if ($fasilitasList->isEmpty())
                            <div class="text-muted small">Data fasilitas belum tersedia.</div>
                        @else
                            <div class="row g-2 fasilitas-grid">
                                @foreach ($fasilitasList as $f)
                                    <div class="col-md-6">
                                        <div class="fasilitas-item">
                                            <input class="form-check-input" type="checkbox" name="fasilitas_ids[]"
                                                value="{{ $f->id }}" id="addFasilitas{{ $f->id }}">
                                            <label class="form-check-label" for="addFasilitas{{ $f->id }}">
                                                {{ $f->nama_fasilitas }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-image me-1" style="color: #22c55e;"></i>Foto Sampul
                        </label>
                        <input type="file" class="form-control" name="foto_cover" accept="image/*" id="addCoverInput" onchange="previewAddCover(event)">
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>Format: JPG, PNG, GIF (Max 2MB)
                        </small>
                        <div class="mt-3" id="addCoverPreviewContainer" style="display: none;">
                            <img id="addCoverPreview" src="" alt="Preview" class="img-thumbnail rounded" style="max-height: 200px;">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-images me-1" style="color: #22c55e;"></i>Foto Detail (Bisa lebih dari satu)
                        </label>
                        <input type="file" class="form-control" name="foto_detail[]" accept="image/*" multiple>
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>Pilih beberapa foto untuk detail
                        </small>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn text-white" data-bs-dismiss="modal"
                        style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border: none; border-radius: 8px; padding: 10px 24px; font-weight: 600;">
                        <i class="bi bi-x-circle me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn text-white"
                        style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); border: none; border-radius: 8px; padding: 10px 24px; font-weight: 600;">
                        <i class="bi bi-save me-1"></i>Simpan Ruangan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Ruangan -->
<div class="modal fade" id="modalEditRuangan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); border: none;">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pencil-square me-2"></i>Edit Ruangan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.ruangan.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="editRuanganId">

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-door-closed me-1" style="color: #f59e0b;"></i>Nama Ruangan
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" name="nama_ruangan" id="editNamaRuangan" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-building me-1" style="color: #f59e0b;"></i>Gedung
                                <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="editGedungSelect" required>
                                <option value="">-- Pilih Gedung --</option>
                                @foreach ($gedungList as $g)
                                    <option value="{{ $g->id }}">{{ $g->nama_gedung }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-layers me-1" style="color: #f59e0b;"></i>Lantai
                                <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" name="lantai_id" id="editLantaiSelect" required disabled>
                                <option value="">-- Pilih Gedung Terlebih Dahulu --</option>
                            </select>
                            <small class="text-muted">Pilih gedung terlebih dahulu</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-people me-1" style="color: #f59e0b;"></i>Kapasitas
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="number" class="form-control" name="kapasitas" id="editKapasitas" min="1" required>
                            <span class="input-group-text">
                                <i class="bi bi-person-fill"></i> orang
                            </span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-card-text me-1" style="color: #f59e0b;"></i>Deskripsi
                        </label>
                        <textarea class="form-control" name="deskripsi" id="editDeskripsi" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-check2-square me-1" style="color: #f59e0b;"></i>Fasilitas
                        </label>
                        @if ($fasilitasList->isEmpty())
                            <div class="text-muted small">Data fasilitas belum tersedia.</div>
                        @else
                            <div class="row g-2 fasilitas-grid">
                                @foreach ($fasilitasList as $f)
                                    <div class="col-md-6">
                                        <div class="fasilitas-item">
                                            <input class="form-check-input edit-fasilitas-checkbox" type="checkbox"
                                                name="fasilitas_ids[]" value="{{ $f->id }}"
                                                id="editFasilitas{{ $f->id }}">
                                            <label class="form-check-label" for="editFasilitas{{ $f->id }}">
                                                {{ $f->nama_fasilitas }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-image me-1" style="color: #f59e0b;"></i>Foto Sampul Saat Ini
                        </label>
                        <div id="editExistingCover" class="d-flex flex-wrap gap-2"></div>
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>Centang jika ingin menghapus foto
                        </small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-images me-1" style="color: #f59e0b;"></i>Foto Detail Saat Ini
                        </label>
                        <div id="editExistingDetail" class="d-flex flex-wrap gap-2"></div>
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>Centang foto detail untuk dihapus
                        </small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-image me-1" style="color: #f59e0b;"></i>Ganti Foto Sampul
                        </label>
                        <input type="file" class="form-control" name="foto_cover" accept="image/*" id="editCoverFile" onchange="previewEditCover(event)">
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>Kosongkan jika tidak ingin mengubah foto sampul
                        </small>
                        <div class="mt-3" id="editCoverPreviewContainer" style="display: none;">
                            <img id="editCoverPreview" src="" alt="Foto Preview" class="img-thumbnail rounded" style="max-height: 200px;">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-images me-1" style="color: #f59e0b;"></i>Tambah Foto Detail
                        </label>
                        <input type="file" class="form-control" name="foto_detail[]" accept="image/*" multiple>
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>Bisa pilih lebih dari satu foto
                        </small>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn text-white" data-bs-dismiss="modal"
                        style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border: none; border-radius: 8px; padding: 10px 24px; font-weight: 600;">
                        <i class="bi bi-x-circle me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn text-white"
                        style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); border: none; border-radius: 8px; padding: 10px 24px; font-weight: 600;">
                        <i class="bi bi-check-circle me-1"></i>Update Ruangan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal View Detail -->
<div class="modal fade" id="modalViewDetail" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #0dcaf0 0%, #0aa2c0 100%); border: none;">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-eye-fill me-2"></i>Detail Ruangan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="text-muted small mb-1">
                                <i class="bi bi-door-closed me-1"></i>Nama Ruangan
                            </label>
                            <h5 class="fw-bold" id="detailNamaRuangan">-</h5>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small mb-1">
                                <i class="bi bi-building me-1"></i>Gedung
                            </label>
                            <h6 id="detailGedung">-</h6>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small mb-1">
                                <i class="bi bi-people me-1"></i>Kapasitas
                            </label>
                            <h6 id="detailKapasitas">-</h6>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small mb-1">
                                <i class="bi bi-layers me-1"></i>Lantai
                            </label>
                            <h6 id="detailLantai">-</h6>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small mb-1">
                                <i class="bi bi-check2-square me-1"></i>Fasilitas
                            </label>
                            <div id="detailFasilitasList" class="d-flex flex-wrap gap-2"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small mb-2">
                            <i class="bi bi-images me-1"></i>Galeri Foto (Sampul + Detail)
                        </label>
                        <div id="detailGalleryContainer" class="detail-gallery-wrap">
                            <div class="p-3 text-muted small">Belum ada foto.</div>
                        </div>
                        <div class="mt-3">
                            <div class="small text-muted mt-2" id="detailFotoSummary">-</div>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <label class="text-muted small mb-1">
                        <i class="bi bi-card-text me-1"></i>Deskripsi
                    </label>
                    <p class="border p-3 rounded bg-light" id="detailDeskripsi">-</p>
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                    style="border-radius: 8px; padding: 10px 24px;">
                    <i class="bi bi-x-circle me-1"></i>Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal View Image -->
<div class="modal fade" id="modalViewImage" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg bg-dark" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header bg-dark text-white border-0">
                <h5 class="modal-title fw-bold" id="viewImageTitle">
                    <i class="bi bi-images me-2"></i>Foto Ruangan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 bg-dark text-center">
                <img id="viewImageSrc" src="" alt="Foto Ruangan" class="img-fluid" style="max-height: 70vh;">
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const ruanganPhotos = @json($ruanganPhotos);
    const ruanganFasilitasMap = @json($ruanganFasilitasMap);
    const fasilitasNameMap = @json($fasilitasNameMap);
    const lantaiMapByGedung = @json($lantaiMapByGedung);
    const baseUrl = "{{ url('/') }}";
    const assetUrl = "{{ asset('storage/uploads/ruangan/') }}";

    function renderLantaiOptions(selectId, gedungId, selectedLantaiId = '') {
        const selectEl = document.getElementById(selectId);
        if (!selectEl) return;

        const gid = String(gedungId || '');
        const lantaiList = gid && lantaiMapByGedung[gid] ? lantaiMapByGedung[gid] : [];

        if (!gid) {
            selectEl.innerHTML = '<option value="">-- Pilih Gedung Terlebih Dahulu --</option>';
            selectEl.disabled = true;
        } else if (lantaiList.length === 0) {
            selectEl.innerHTML = '<option value="">-- Tidak Ada Lantai Tersedia --</option>';
            selectEl.disabled = true;
        } else {
            selectEl.innerHTML = '<option value="">-- Pilih Lantai --</option>';
            lantaiList.forEach((item) => {
                const option = document.createElement('option');
                option.value = String(item.id);
                option.textContent = 'Lantai ' + item.nomor;
                if (String(selectedLantaiId) === String(item.id)) {
                    option.selected = true;
                }
                selectEl.appendChild(option);
            });
            selectEl.disabled = false;
        }
    }

    // Initialize Bootstrap tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        const addGedungSelect = document.getElementById('addGedungSelect');
        const editGedungSelect = document.getElementById('editGedungSelect');

        if (addGedungSelect) {
            addGedungSelect.addEventListener('change', function() {
                renderLantaiOptions('addLantaiSelect', this.value, '');
            });
            renderLantaiOptions('addLantaiSelect', addGedungSelect.value, '');
        }

        if (editGedungSelect) {
            editGedungSelect.addEventListener('change', function() {
                renderLantaiOptions('editLantaiSelect', this.value, '');
            });
            renderLantaiOptions('editLantaiSelect', editGedungSelect.value, '');
        }
    });

    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('#tableRuangan tbody tr');

        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Preview image on add modal
    function previewAddCover(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('addCoverPreview').src = e.target.result;
                document.getElementById('addCoverPreviewContainer').style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }

    // Preview image on edit modal
    function previewEditCover(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('editCoverPreview').src = e.target.result;
                document.getElementById('editCoverPreviewContainer').style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }

    function renderExistingFotos(id) {
        const data = ruanganPhotos[id] || { cover: [], detail: [] };
        const coverWrap = document.getElementById('editExistingCover');
        const detailWrap = document.getElementById('editExistingDetail');

        coverWrap.innerHTML = '';
        detailWrap.innerHTML = '';

        if (!data.cover.length) {
            coverWrap.innerHTML = '<div class="text-muted small">Belum ada foto sampul.</div>';
        } else {
            data.cover.forEach(item => {
                coverWrap.innerHTML += `
                    <label class="border rounded p-2 text-center" style="width:120px;">
                        <img src="${assetUrl}/${item.nama_file}" alt="Cover" class="img-fluid rounded" style="height:70px;object-fit:cover;width:100%;">
                        <div class="form-check mt-1">
                            <input class="form-check-input" type="checkbox" name="delete_foto[]" value="${item.id}">
                            <span class="small">hapus</span>
                        </div>
                    </label>
                `;
            });
        }

        if (!data.detail.length) {
            detailWrap.innerHTML = '<div class="text-muted small">Belum ada foto detail.</div>';
        } else {
            data.detail.forEach(item => {
                detailWrap.innerHTML += `
                    <label class="border rounded p-2 text-center" style="width:120px;">
                        <img src="${assetUrl}/${item.nama_file}" alt="Detail" class="img-fluid rounded" style="height:70px;object-fit:cover;width:100%;">
                        <div class="form-check mt-1">
                            <input class="form-check-input" type="checkbox" name="delete_foto[]" value="${item.id}">
                            <span class="small">hapus</span>
                        </div>
                    </label>
                `;
            });
        }
    }

    // Edit ruangan function
    function editRuangan(id, nama, gedungId, lantaiId, kapasitas, deskripsi) {
        document.getElementById('editRuanganId').value = id;
        document.getElementById('editNamaRuangan').value = nama;
        document.getElementById('editKapasitas').value = kapasitas;
        document.getElementById('editDeskripsi').value = deskripsi || '';

        const gedungSelect = document.getElementById('editGedungSelect');
        gedungSelect.value = gedungId;
        renderLantaiOptions('editLantaiSelect', gedungId, lantaiId);

        // Reset check fasilitas
        document.querySelectorAll('.edit-fasilitas-checkbox').forEach(cb => {
            cb.checked = false;
        });

        // Check fasilitas per ruangan
        const selectedFasilitas = ruanganFasilitasMap[id] || [];
        selectedFasilitas.forEach(fId => {
            const cb = document.getElementById('editFasilitas' + fId);
            if (cb) cb.checked = true;
        });

        // Reset image previews
        document.getElementById('editCoverFile').value = '';
        document.getElementById('editCoverPreviewContainer').style.display = 'none';

        // Render existing photos
        renderExistingFotos(id);
    }

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function viewDetail(id, nama, gedung, lantai, kapasitas, deskripsi, coverFoto) {
        document.getElementById('detailNamaRuangan').textContent = nama || '-';
        document.getElementById('detailGedung').textContent = gedung || '-';
        document.getElementById('detailKapasitas').textContent = kapasitas ? `${kapasitas} orang` : '-';
        document.getElementById('detailLantai').textContent = lantai || '-';
        document.getElementById('detailDeskripsi').textContent = deskripsi || '-';

        // Render Fasilitas
        const fList = document.getElementById('detailFasilitasList');
        fList.innerHTML = '';
        const fIds = ruanganFasilitasMap[id] || [];
        if (!fIds.length) {
            fList.innerHTML = '<span class="text-muted small">Tidak ada fasilitas</span>';
        } else {
            fIds.forEach(fId => {
                const fname = fasilitasNameMap[fId] || ('Fasilitas ' + fId);
                fList.innerHTML += `<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1"><i class="bi bi-check2 me-1"></i>${escapeHtml(fname)}</span>`;
            });
        }

        // Render Gallery
        const gallery = document.getElementById('detailGalleryContainer');
        const data = ruanganPhotos[id] || { cover: [], detail: [] };
        let countCover = data.cover.length;
        let countDetail = data.detail.length;

        document.getElementById('detailFotoSummary').innerHTML = `
            <i class="bi bi-info-circle me-1"></i>Total Foto: ${countCover} Sampul, ${countDetail} Detail
        `;

        let html = '';
        if (countCover === 0 && countDetail === 0) {
            html = '<div class="p-3 text-muted small border rounded">Belum ada foto yang diunggah.</div>';
        } else {
            if (countCover > 0) {
                html += '<div class="mb-2 fw-semibold small text-muted">Foto Sampul</div><div class="d-flex flex-wrap gap-2 mb-3">';
                data.cover.forEach(f => {
                    html += `
                        <img src="${assetUrl}/${f.nama_file}" 
                             alt="Cover" class="img-thumbnail rounded" 
                             style="width: 100px; height: 75px; object-fit: cover; cursor: pointer; border: 2px solid #22c55e;"
                             data-bs-toggle="modal" data-bs-target="#modalViewImage"
                             onclick="viewImage('${assetUrl}/${f.nama_file}', 'Foto Sampul - ${escapeHtml(nama)}')">
                    `;
                });
                html += '</div>';
            }
            if (countDetail > 0) {
                html += '<div class="mb-2 fw-semibold small text-muted">Foto Detail</div><div class="d-flex flex-wrap gap-2">';
                data.detail.forEach(f => {
                    html += `
                        <img src="${assetUrl}/${f.nama_file}" 
                             alt="Detail" class="img-thumbnail rounded" 
                             style="width: 80px; height: 60px; object-fit: cover; cursor: pointer;"
                             data-bs-toggle="modal" data-bs-target="#modalViewImage"
                             onclick="viewImage('${assetUrl}/${f.nama_file}', 'Foto Detail - ${escapeHtml(nama)}')">
                    `;
                });
                html += '</div>';
            }
        }
        gallery.innerHTML = html;

        const modalEl = document.getElementById('modalViewDetail')
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    }

    function viewImage(src, title) {
        document.getElementById('viewImageSrc').src = src;
        document.getElementById('viewImageTitle').innerHTML = `<i class="bi bi-images me-2"></i>${title}`;
    }

    function deleteRuangan(id, namaRuangan) {
        Swal.fire({
            title: 'Hapus Ruangan?',
            html: `<strong>${namaRuangan}</strong><br><small class="text-muted">Data beserta semua fotonya akan dihapus permanen.</small>`,
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

    // Auto-dismiss alerts
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
