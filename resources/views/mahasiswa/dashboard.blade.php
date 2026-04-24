<x-mahasiswa-layout>
    <x-slot:title>Home - Peminjaman Ruangan</x-slot>

    <!-- HERO -->
    <section class="hero-full">
        <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
            <div class="carousel-inner">
                @if ($heroImages->isEmpty())
                    <div class="carousel-item active">
                        <img src="{{ asset('assets/icons/logo_big.svg') }}" class="d-block w-100" alt="SIPERU">
                    </div>
                @else
                    @foreach ($heroImages as $i => $img)
                        <div class="carousel-item {{ $i == 0 ? 'active' : '' }}">
                            <img src="{{ asset('storage/uploads/ruangan/' . $img->nama_file) }}" class="d-block w-100" alt="Hero Ruangan {{ $i + 1 }}">
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="hero-content">
            <h5>WELCOME TO SIPERU</h5>
            <h1>Room Booking System</h1>
            <h2>Fasilkom Unsri</h2>
        </div>
    </section>

    <!-- FILTER -->
    <section class="filter-floating" id="filterSection">
        <div class="container">
            <form class="filter-box" method="get" action="{{ route('mahasiswa.dashboard') }}" id="filterForm">
                <div class="row g-3 align-items-end">

                    <div class="col-md-3">
                        <label>Tanggal Awal</label>
                        <input type="date" name="tgl_awal" value="{{ $tgl_awal ?? '' }}" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label>Tanggal Akhir</label>
                        <input type="date" name="tgl_akhir" value="{{ $tgl_akhir ?? '' }}" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label>Gedung</label>
                        <div class="select-wrap">
                            <select name="gedung" class="form-control">
                                <option value="">Semua Gedung</option>
                                @foreach ($gedungList as $g)
                                    <option value="{{ $g->nama_gedung }}" {{ ($gedung ?? '') == $g->nama_gedung ? 'selected' : '' }}>
                                        {{ $g->nama_gedung }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <button class="btn w-100">Check</button>
                    </div>

                </div>
            </form>
        </div>
    </section>

    <div class="wrap">
        <section class="room-section">
            <div class="container">

                @if ($ruangan->isEmpty())
                    <div class="text-center text-muted fs-5 mt-5">
                        <p class="text-white">Saat ini ruangan di gedung yang dipilih tidak tersedia.</p><br>
                        <p class="text-white">Silakan cek gedung lain.</p>
                    </div>
                @else
                    <div class="room-grid">
                        @foreach ($ruangan as $r)
                            @php $fotoRuangan = $r->foto_utama ?? ''; @endphp

                            <div class="room-item">
                                <div class="room-card">

                                    <div class="room-img">
                                        <img src="{{ !empty($fotoRuangan) ? asset('storage/uploads/ruangan/' . $fotoRuangan) : asset('assets/icons/logo_big.svg') }}"
                                            alt="{{ $r->nama_ruangan }}">
                                    </div>

                                    <div class="room-body">
                                        <div class="room-title">{{ $r->nama_ruangan }}</div>

                                        <div class="room-meta">
                                            Lokasi: {{ $r->gedung }}<br>
                                            Lantai: {{ $r->Lantai ?? ($r->lantai ?? '-') }}<br>
                                            Kapasitas: {{ $r->kapasitas }} orang
                                        </div>

                                        <a href="{{ route('mahasiswa.ruangan.detail', $r->id) }}" class="room-btn">
                                            View Details
                                        </a>
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>
        </section>
    </div>

    @push('scripts')
    <script>
        // Scroll ke filter section setelah page load jika ada parameter filter
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const hasFilter = urlParams.has('tgl_awal') || urlParams.has('tgl_akhir') || urlParams.has('gedung');

            if (hasFilter) {
                const filterSection = document.getElementById('filterSection');
                if (filterSection) {
                    setTimeout(() => {
                        const offsetTop = filterSection.offsetTop - 100;
                        window.scrollTo({
                            top: offsetTop,
                            behavior: 'smooth'
                        });
                    }, 100);
                }
            }
        });
    </script>
    @endpush
</x-mahasiswa-layout>
