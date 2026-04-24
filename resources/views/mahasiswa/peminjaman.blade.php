<x-mahasiswa-layout>
    <x-slot:title>Peminjaman - Peminjaman Ruangan</x-slot>

    <div class="container py-4">
        <div class="kelola-header mb-4 mt-5">
            <h1 class="text-white">Ajukan Peminjaman</h1>
        </div>

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card mb-4">
            <div class="card-body">
                <form method="POST" action="{{ route('mahasiswa.peminjaman.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="mb-0 fw-bold" style="color: #495057; padding-bottom: 20px;">
                                Form Detail Peminjaman
                            </h5>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Ruangan</label>
                            <select name="ruangan_id" class="form-select" required>
                                <option value="">-- Pilih Ruangan --</option>
                                @foreach ($ruanganList as $r)
                                    <option value="{{ $r->id }}" {{ (old('ruangan_id', $preselectRuanganId) == $r->id) ? 'selected' : '' }}>
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
                            <label class="form-label">Jumlah Peserta (opsional)</label>
                            <input type="number" name="jumlah_peserta" class="form-control" min="1"
                                value="{{ old('jumlah_peserta') }}">
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

        <!-- Card Tabel Riwayat Peminjaman -->
        <div class="card shadow border-0" style="border-radius: 15px; overflow: hidden;">
            <div class="card-header bg-white py-3 border-bottom"
                style="background: linear-gradient(to right, #f8f9fa, #e9ecef) !important;">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0 fw-bold" style="color: #495057;">
                            <i style="color: #22c55e;"></i>Riwayat Pengajuan
                        </h5>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search" style="color: #22c55e;"></i>
                            </span>
                            <input type="text" class="form-control border-start-0 bg-white" id="searchInput"
                                placeholder="Cari ruangan, kegiatan..." style="border-left: 0;">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="tableRiwayat">
                        <thead style="background: linear-gradient(to right, #f8f9fa, #e9ecef);">
                            <tr>
                                <th class="text-center" style="width: 50px; padding: 15px 10px;">
                                    <i class="bi bi-hash"></i>
                                </th>
                                <th class="text-center" style="width: 20%; padding: 15px;"><i class="bi bi-door-closed me-1"></i>Ruangan</th>
                                <th class="text-center" style="width: 12%; padding: 15px;"><i class="bi bi-calendar-event me-1"></i>Tanggal</th>
                                <th class="text-center" style="width: 10%; padding: 15px;"><i class="bi bi-clock me-1"></i>Jam</th>
                                <th class="text-center" style="width: 12%; padding: 15px;"><i class="bi bi-card-text me-1"></i>Kegiatan</th>
                                <th class="text-center" style="width: 10%; padding: 15px;"><i class="bi bi-patch-check me-1"></i>Status</th>
                                <th class="text-center" style="width: 20%; padding: 15px;"><i class="bi bi-chat-left-text me-1"></i>Catatan </th>
                                <th class="text-center" style="width: 210px; padding: 15px; white-space: nowrap;"><i class="bi bi-gear me-1"></i>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($riwayat->isEmpty())
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="bi bi-inbox display-4 d-block mb-3"></i>
                                            <p class="mb-0">Belum ada pengajuan peminjaman</p>
                                            <small>Ajukan peminjaman ruangan pertama Anda</small>
                                        </div>
                                    </td>
                                </tr>
                            @else
                                @foreach ($riwayat as $i => $p)
                                    @php
                                        $statusId = (int) $p->status_id;
                                        if ($statusId === 1) $statusBg = 'linear-gradient(135deg, #f59e0b, #d97706)';
                                        elseif ($statusId === 2) $statusBg = 'linear-gradient(135deg, #22c55e, #16a34a)';
                                        elseif ($statusId === 3) $statusBg = 'linear-gradient(135deg, #ef4444, #dc2626)';
                                        elseif ($statusId === 4) $statusBg = 'linear-gradient(135deg, #6b7280, #4b5563)';
                                        elseif ($statusId === 5) $statusBg = 'linear-gradient(135deg, #f97316, #ea580c)';
                                        else $statusBg = 'linear-gradient(135deg, #94a3b8, #64748b)';
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
                                        <td>
                                            <span class="badge px-3 py-2"
                                                style="background: linear-gradient(135deg, #17a2b8, #138496); color: white; font-weight: 600; border-radius: 8px;">
                                                <i class="bi bi-calendar-fill me-1"></i>{{ $p->tanggal }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge px-3 py-2"
                                                style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; font-weight: 600; border-radius: 8px;">
                                                <i class="bi bi-clock-fill me-1"></i>{{ substr($p->jam_mulai, 0, 5) }} - {{ substr($p->jam_selesai, 0, 5) }}
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
                                                    <i class="bi bi-info-circle me-1"></i>{{ substr($p->catatan_admin, 0, 60) }}{{ strlen($p->catatan_admin) > 60 ? '...' : '' }}
                                                </small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1 justify-content-center">
                                                @if ($statusId === 1)
                                                    <form method="POST" action="{{ route('mahasiswa.peminjaman.cancel') }}" onsubmit="return confirm('Batalkan pengajuan ini?');"
                                                        style="display:inline-block;" class="d-inline">
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
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted fw-semibold">
                        <i class="bi bi-info-circle-fill me-1" style="color: #22c55e;"></i>Total Data:
                        <span class="badge ms-1"
                            style="background: linear-gradient(135deg, #22c55e, #16a34a);">{{ $riwayat->count() }}</span>
                        pengajuan terdaftar
                    </small>
                    <small class="text-muted">
                        <i class="bi bi-calendar-check me-1"></i>{{ date('d F Y') }}
                    </small>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('searchInput')?.addEventListener('keyup', function() {
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
    </script>
    @endpush
</x-mahasiswa-layout>
