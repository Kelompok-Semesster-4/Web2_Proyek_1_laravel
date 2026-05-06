<x-layouts.mahasiswa-layout>
    <x-slot:title>Profil - Profil Pengguna</x-slot>

        @push('styles')
            <link rel="stylesheet" href="{{ asset('assets/css/user.css') }}?v=1">
        @endpush

        <div class="wrap profil-page">
            <div class="container">
                @php
                    $profilePhotoUrl = null;
                    if (!empty($user->foto_profil)) {
                        if (filter_var($user->foto_profil, FILTER_VALIDATE_URL)) {
                            $profilePhotoUrl = $user->foto_profil;
                        } else {
                            $profilePhotoUrl = asset('storage/uploads/profil/' . $user->foto_profil);
                        }
                    }
                    $profileInitial = strtoupper(substr($user->nama, 0, 1));
                @endphp

                <!-- Alert Messages -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-0" role="alert"
                        style="border-left: 5px solid #28a745; background: white;">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle-fill me-3 fs-4 text-success"></i>
                            <div>
                                <strong class="d-block">Berhasil!</strong>
                                <span class="text-muted">{{ session('success') }}</span>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-0" role="alert"
                        style="border-left: 5px solid #dc3545; background: white;">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle-fill me-3 fs-4 text-danger"></i>
                            <div>
                                <strong class="d-block">Terjadi Kesalahan!</strong>
                                <span class="text-muted">Periksa kembali input Anda</span>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('mahasiswa.ubahProfil') }}" method="post" enctype="multipart/form-data"
                    id="profilForm">
                    @csrf
                    @method('PATCH')

                    <div class="profil-container">
                        <!-- Sidebar Profil -->
                        <div class="profil-sidebar">
                            <div class="avatar-container">
                                <div class="avatar">
                                    <img id="avatarPreview" src="{{ $profilePhotoUrl ?? '' }}" alt="Foto Profil"
                                        class="{{ $profilePhotoUrl ? '' : 'd-none' }}">
                                    <span id="avatarInitial"
                                        class="avatar-initial {{ $profilePhotoUrl ? 'd-none' : '' }}">{{ $profileInitial }}</span>
                                </div>
                            </div>
                            <div class="photo-upload-wrap">
                                <label for="foto_profil" class="photo-upload-btn">
                                    <i class="bi bi-camera"></i>
                                    Ubah Foto
                                </label>
                                <input type="file" name="foto_profil" id="foto_profil" class="d-none" accept="image/*">
                            </div>
                            <h2 class="user-name">{{ $user->nama }}</h2>
                            <p class="user-handle">{{ $user->username }}</p>
                            <span class="user-role">
                                {{ ucfirst($user->role === 'mahasiswa' ? 'Mahasiswa' : ($user->prodi ?? 'User')) }}
                            </span>

                            <div class="user-info">
                                <div class="info-item">
                                    <div class="info-label">Email</div>
                                    <div class="info-value">{{ $user->email ?? '-' }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Bergabung</div>
                                    <div class="info-value">
                                        {{ $user->created_at ? $user->created_at->format('d-m-Y') : 'N/A' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Konten Profil -->
                        <div class="profil-forms">
                            <div class="profil-card">
                                <!-- Informasi Dasar -->
                                <div class="form-section">
                                    <div class="section-header">
                                        <div class="section-icon">
                                            <i class="bi bi-person"></i>
                                        </div>
                                        <div>
                                            <p class="section-title">Informasi Dasar</p>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="nama" class="form-label">
                                            Nama Lengkap <span class="required">*</span>
                                        </label>
                                        <input type="text" name="nama" id="nama"
                                            class="form-control @error('nama') is-invalid @enderror"
                                            value="{{ old('nama', $user->nama) }}" required>
                                        @error('nama')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="username" class="form-label">
                                                Username <span class="required">*</span>
                                            </label>
                                            <input type="text" name="username" id="username"
                                                class="form-control @error('username') is-invalid @enderror"
                                                value="{{ old('username', $user->username) }}" required>
                                            @error('username')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="email" class="form-label">
                                                Email <span class="required">*</span>
                                            </label>
                                            <input type="email" name="email" id="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                value="{{ old('email', $user->email) }}" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Card: Ubah Password -->
                            <div class="profil-card">

                                <!-- Ubah Password -->
                                <div class="form-section">
                                    <div class="section-header">
                                        <div class="section-icon">
                                            <i class="bi bi-lock"></i>
                                        </div>
                                        <div>
                                            <p class="section-title">Ubah Password</p>
                                            <p class="section-subtitle">Kosongkan jika tidak ingin mengubah password</p>
                                        </div>
                                    </div>

                                    <div class="form-group" id="currentPasswordGroup" @if (!$user->password)
                                    style="display: none;" @endif>
                                        <label for="current_password" class="form-label">
                                            Password Lama <span class="required">*</span>
                                        </label>
                                        <input type="password" name="current_password" id="current_password"
                                            class="form-control @error('current_password') is-invalid @enderror"
                                            placeholder="Masukkan password lama Anda">
                                        @error('current_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="password" class="form-label">
                                                Password Baru
                                            </label>
                                            <input type="password" name="password" id="password"
                                                class="form-control @error('password') is-invalid @enderror"
                                                placeholder="Min. 8 karakter">
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="password_confirmation" class="form-label">
                                                Konfirmasi Password Baru
                                            </label>
                                            <input type="password" name="password_confirmation"
                                                id="password_confirmation"
                                                class="form-control @error('password_confirmation') is-invalid @enderror"
                                                placeholder="Ulangi password baru">
                                            @error('password_confirmation')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tombol Aksi di Luar Card -->
                            <div class="btn-group">
                                <button type="reset" class="btn btn-reset">Batalkan</button>
                                <button type="submit" class="btn btn-submit">
                                    <i class="bi bi-check-lg"></i> Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                @push('scripts')
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const profilForm = document.getElementById('profilForm');
                            const passwordInput = document.getElementById('password');
                            const currentPasswordGroup = document.getElementById('currentPasswordGroup');
                            const currentPasswordInput = document.getElementById('current_password');
                            const fotoInput = document.getElementById('foto_profil');
                            const avatarPreview = document.getElementById('avatarPreview');
                            const avatarInitial = document.getElementById('avatarInitial');
                            const hasPassword = {{ $user->password ? 'true' : 'false' }};
                            const originalAvatarUrl = @json($profilePhotoUrl);

                            function toggleCurrentPasswordField() {
                                const hasNewPassword = passwordInput.value.trim() !== '';

                                if (hasPassword) {
                                    // Jika user sudah punya password, selalu tampilkan field password lama
                                    currentPasswordGroup.style.display = 'block';
                                    currentPasswordInput.required = hasNewPassword;
                                } else {
                                    // Jika user tidak punya password (Google login), tampilkan hanya saat input password baru
                                    currentPasswordGroup.style.display = hasNewPassword ? 'block' : 'none';
                                    currentPasswordInput.required = hasNewPassword;
                                }
                            }

                            // Check on input
                            passwordInput.addEventListener('input', toggleCurrentPasswordField);

                            // Initial check
                            toggleCurrentPasswordField();

                            if (profilForm) {
                                profilForm.addEventListener('reset', function () {
                                    window.setTimeout(function () {
                                        if (fotoInput) {
                                            fotoInput.value = '';
                                        }

                                        if (originalAvatarUrl && avatarPreview) {
                                            avatarPreview.src = originalAvatarUrl;
                                            avatarPreview.classList.remove('d-none');
                                            avatarInitial.classList.add('d-none');
                                        } else {
                                            avatarPreview?.classList.add('d-none');
                                            avatarInitial?.classList.remove('d-none');
                                        }

                                        if (passwordInput) {
                                            passwordInput.value = '';
                                        }

                                        toggleCurrentPasswordField();
                                    }, 0);
                                });
                            }

                            if (fotoInput) {
                                fotoInput.addEventListener('change', function () {
                                    const file = this.files && this.files[0];

                                    if (!file) {
                                        if (originalAvatarUrl && avatarPreview) {
                                            avatarPreview.src = originalAvatarUrl;
                                            avatarPreview.classList.remove('d-none');
                                            avatarInitial.classList.add('d-none');
                                        } else {
                                            avatarPreview?.classList.add('d-none');
                                            avatarInitial?.classList.remove('d-none');
                                        }
                                        return;
                                    }

                                    const reader = new FileReader();
                                    reader.onload = function (event) {
                                        if (avatarPreview) {
                                            avatarPreview.src = event.target.result;
                                            avatarPreview.classList.remove('d-none');
                                        }
                                        avatarInitial?.classList.add('d-none');
                                    };
                                    reader.readAsDataURL(file);
                                });
                            }

                            // Auto-dismiss alerts after 5 seconds
                            const alerts = document.querySelectorAll('.alert');
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
