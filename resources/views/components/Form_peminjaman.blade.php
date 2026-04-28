 <div class="card mb-4">
            <div class="card-body">
                <form method="POST" action="{{ route('mahasiswa.peminjaman.store') }}" enctype="multipart/form-data" id="form_peminjaman">
                    @csrf
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="mb-0 fw-bold bi bi-highlighter" style="color: #495057; padding-bottom: 20px;">
                                 Form Detail Peminjaman
                            </h5>
                        </div>
                    </div>
                    <div class="row g-3" >
                        <div class="col-md-6">
                            <label class="form-label">Ruangan</label>
                            <select name="ruangan_id" id="ruangan_id" class="form-select" required>
                                <option value="" style="color: #a0a0a0">-- Pilih Ruangan --</option>
                                @foreach ($ruanganList as $r)
                                    <option value="{{ $r->id }}" data-kapasitas="{{ $r->kapasitas ?? 0 }}" {{ (old('ruangan_id', $preselectRuanganId) == $r->id) ? 'selected' : '' }}>
                                        {{ $r->gedung }} - {{ $r->nama_ruangan }} (Kapasitas: {{ $r->kapasitas ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nama Kegiatan</label>
                            <input type="text" name="nama_kegiatan" class="form-control"
                                value="{{ old('nama_kegiatan') }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal') }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Jam Mulai</label>
                            <input type="time" name="jam_mulai" class="form-control" value="{{ old('jam_mulai') }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="form-control" value="{{ old('jam_selesai') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Jumlah Peserta</label>
                            <input type="number" name="jumlah_peserta" id="jumlah_peserta" class="form-control" min="1" required
                                value="{{ old('jumlah_peserta') }}">
                            <small id="kapasitas_error" class="text-danger d-none mt-1"></small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Surat (opsional: PDF/JPG/PNG)</label>
                            <input type="file" name="surat" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3 px-4 fw-bold">Ajukan</button>
                </form>
            </div>
</div>

<script>
    (() => {
        const form = document.getElementById('form_peminjaman');
        const ruanganSelect = document.getElementById('ruangan_id');
        const pesertaInput = document.getElementById('jumlah_peserta');
        const kapasitasError = document.getElementById('kapasitas_error');

        if (!form || !ruanganSelect || !pesertaInput || !kapasitasError) return;

        const validatePeserta = () => {
            const selectedOption = ruanganSelect.options[ruanganSelect.selectedIndex];
            const kapasitas = Number(selectedOption?.dataset?.kapasitas || 0);
            const jumlahPeserta = Number(pesertaInput.value || 0);

            if (kapasitas > 0 && jumlahPeserta > kapasitas) {
                pesertaInput.classList.add('is-invalid');
                kapasitasError.textContent = `Jumlah peserta melebihi kapasitas ruangan. Maksimal ${kapasitas} peserta.`;
                kapasitasError.classList.remove('d-none');
                return false;
            }

            pesertaInput.classList.remove('is-invalid');
            kapasitasError.textContent = '';
            kapasitasError.classList.add('d-none');
            return true;
        };

        const syncKapasitas = () => {
            const selectedOption = ruanganSelect.options[ruanganSelect.selectedIndex];
            const kapasitas = Number(selectedOption?.dataset?.kapasitas || 0);

            if (kapasitas > 0) {
                pesertaInput.max = kapasitas;
            } else {
                pesertaInput.removeAttribute('max');
            }

            validatePeserta();
        };

        ruanganSelect.addEventListener('change', syncKapasitas);
        pesertaInput.addEventListener('input', validatePeserta);
        form.addEventListener('submit', (event) => {
            if (!validatePeserta()) {
                event.preventDefault();
                pesertaInput.focus();
            }
        });
        syncKapasitas();
    })();
</script>
