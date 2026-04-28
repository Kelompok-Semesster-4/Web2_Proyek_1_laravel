<x-layouts.mahasiswa-layout>
    <x-slot:title>Profil - Profil Pengguna</x-slot>

    @push('styles')
    <style>
        .profil-page {
            padding-top: 30px;
            padding-bottom: 30px;
            min-height: calc(100vh - 68px);
        }
    </style>
    @endpush

    <div class="wrap profil-page">
        <div class="container">
            <div class="kelola-header mb-4">
                <h1 class="text-white">Profil Pengguna</h1>
            </div>

            @include('components.Error-user');

            <div class="card mb-4">
                <div class="card-body">
                    <form action="{{ route('mahasiswa.ubahProfil') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="row mb-2">
                                    <div class="col-md-12">
                                        <label for="nama" class="form-label">Nama</label>
                                        <input type="text" name="nama" id="nama" class="form-control" value="{{ old('nama', $user->nama) }}" required=true>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-12">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" name="username" id="username" class="form-control" value="{{ old('username', $user->username) }}" required=true>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-12">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="text" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required=true>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row mb-2">
                                    <div class="col-md-12">
                                        <label for="current_password" class="form-label">Password Lama</label>
                                        <input type="password" name="current_password" id="current_password" class="form-control" placeholder="Masukkan password lama...">
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-12">
                                        <label for="password" class="form-label">Password Baru</label>
                                        <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password baru...">
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-12">
                                        <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Konfirmasi password baru...">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary mt-3 px-4 fw-bold">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.mahasiswa-layout>
