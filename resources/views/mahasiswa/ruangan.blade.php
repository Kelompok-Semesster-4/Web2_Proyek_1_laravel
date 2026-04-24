<x-mahasiswa-layout>
    <x-slot:title>Ruangan - Peminjaman Ruangan</x-slot>

    <!-- HERO -->
    <section class="hero-page">
        <img src="{{ !empty($heroImg) ? asset('storage/uploads/ruangan/' . $heroImg) : asset('assets/icons/logo_big.svg') }}"
            class="hero-bg" alt="Hero Ruangan">

        <div class="hero-overlay"></div>

        <div class="hero-page-content">
            <h1>Ruangan Kami</h1>
            <div class="breadcrumb">
                <a href="{{ route('mahasiswa.dashboard') }}">Home</a>
                <span class="sep">›</span>
                <span class="current">Ruangan</span>
            </div>
        </div>
    </section>

    <div class="wrap">
        <section class="room-section">
            <div class="container">

                @if ($ruangans->isEmpty())
                    <div class="text-center text-white mt-5">
                        <h5>Belum ada data ruangan</h5>
                    </div>
                @else
                    <div class="room-grid">
                        @foreach ($ruangans as $r)
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
                                            Lokasi : {{ $r->gedung ?: '-' }}<br>
                                            Lantai : {{ $r->Lantai ?? ($r->lantai ?? '-') }}<br>
                                            Kapasitas : {{ $r->kapasitas ?? 0 }} orang
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
</x-mahasiswa-layout>
