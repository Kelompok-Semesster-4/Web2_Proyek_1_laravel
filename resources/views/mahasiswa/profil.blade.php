<x-layouts.mahasiswa-layout>
    <x-slot:title>Profil - Profil Pengguna</x-slot>

        @push('styles')
            <style>
                .profil-page {
                    padding-top: 0;
                    padding-bottom: 0;
                    min-height: calc(100vh - 68px);
                }

                .profil-container {
                    display: grid;
                    grid-template-columns: 300px 1fr;
                    gap: 30px;
                    padding: 30px 0;
                    width: 100%;
                }

                .profil-sidebar {
                    background: white;
                    border-radius: 8px;
                    padding: 30px;
                    text-align: center;
                    height: fit-content;
                    align-self: start;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
                }

                .avatar-container {
                    margin-bottom: 20px;
                }

                .avatar {
                    width: 100px;
                    height: 100px;
                    border-radius: 50%;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin: 0 auto;
                    font-size: 40px;
                    color: white;
                    font-weight: bold;
                    overflow: hidden;
                    position: relative;
                }

                .avatar img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    display: block;
                }

                .avatar-initial {
                    line-height: 1;
                }

                .photo-upload-wrap {
                    margin-top: 14px;
                }

                .photo-upload-btn {
                    display: inline-flex;
                    align-items: center;
                    gap: 8px;
                    padding: 8px 14px;
                    border-radius: 999px;
                    background: #0c63e4;
                    color: #fff;
                    font-size: 13px;
                    font-weight: 600;
                    cursor: pointer;
                    transition: background 0.2s ease;
                }

                .photo-upload-btn:hover {
                    background: #0a58ca;
                }

                .photo-upload-help {
                    margin-top: 8px;
                    font-size: 12px;
                    color: #6c757d;
                }

                .user-name {
                    font-size: 20px;
                    font-weight: 600;
                    color: #212529;
                    margin: 15px 0 5px 0;
                }

                .user-handle {
                    font-size: 14px;
                    color: #6c757d;
                    margin-bottom: 20px;
                }

                .user-role {
                    display: inline-block;
                    background: #20c997;
                    color: white;
                    padding: 4px 12px;
                    border-radius: 20px;
                    font-size: 12px;
                    font-weight: 600;
                    margin-bottom: 20px;
                }

                .user-info {
                    border-top: 1px solid #e9ecef;
                    padding-top: 20px;
                }

                .info-item {
                    margin-bottom: 15px;
                    text-align: left;
                }

                .info-label {
                    font-size: 12px;
                    color: #6c757d;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    margin-bottom: 3px;
                }

                .info-value {
                    font-size: 14px;
                    color: #212529;
                    word-break: break-all;
                }

                .profil-forms {
                    display: flex;
                    flex-direction: column;
                    gap: 20px;
                }

                .profil-card {
                    padding: 30px;
                    background: white;
                    border-radius: 8px;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
                }

                .profil-card:last-child {
                    border-bottom: none;
                }

                .form-section {
                    margin-bottom: 0;
                }

                .section-header {
                    display: flex;
                    align-items: center;
                    margin-bottom: 20px;
                    padding-bottom: 15px;
                    border-bottom: 2px solid #e9ecef;
                }

                .section-icon {
                    width: 32px;
                    height: 32px;
                    background: #e7f5ff;
                    border-radius: 6px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: #0c63e4;
                    font-size: 16px;
                    margin-right: 12px;
                }

                .section-title {
                    font-size: 16px;
                    font-weight: 600;
                    color: #212529;
                    margin: 0;
                }

                .section-subtitle {
                    font-size: 13px;
                    color: #6c757d;
                    margin-top: 5px;
                }

                .form-group {
                    margin-bottom: 20px;
                }

                .form-label {
                    font-size: 13px;
                    font-weight: 600;
                    color: #212529;
                    margin-bottom: 8px;
                    display: block;
                }

                .form-label .required {
                    color: #dc3545;
                }

                .form-control {
                    border: 1px solid #dee2e6;
                    border-radius: 6px;
                    padding: 10px 12px;
                    font-size: 14px;
                    transition: all 0.3s ease;
                }

                .form-control:focus {
                    border-color: #0c63e4;
                    box-shadow: 0 0 0 0.2rem rgba(12, 99, 228, 0.15);
                }

                .form-row {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 20px;
                }

                .form-row.full {
                    grid-template-columns: 1fr;
                }

                .btn-group {
                    display: flex;
                    gap: 12px;
                    justify-content: flex-end;
                    margin-top: -10px;
                }

                .btn {
                    padding: 10px 24px;
                    font-weight: 600;
                    border-radius: 6px;
                    border: none;
                    cursor: pointer;
                    font-size: 14px;
                    transition: all 0.3s ease;
                }

                .btn-reset {
                    background: white;
                    color: #6c757d;
                    border: 1px solid #dee2e6;
                }

                .btn-reset:hover {
                    background: #f8f9fa;
                    border-color: #adb5bd;
                }

                .btn-submit {
                    background: #20c997;
                    color: white;
                }

                .btn-submit:hover {
                    background: #1aa179;
                }

                .alert {
                    border-radius: 6px;
                    margin-top: 20px;
                }

                .invalid-feedback {
                    display: block;
                    font-size: 12px;
                    color: #dc3545;
                    margin-top: 5px;
                }

                #profilForm {
                    padding: 0;
                }

                @media (max-width: 768px) {
                    .profil-container {
                        grid-template-columns: 1fr;
                    }

                    .profil-sidebar {
                        order: -1;
                    }

                    .form-row {
                        grid-template-columns: 1fr;
                    }

                    .btn-group {
                        flex-direction: column;
                    }

                    .btn {
                        width: 100%;
                    }
                }
            </style>
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
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert"
                        style="border-left: 5px solid #28a745; background: white; margin-top: 20px;">
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
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert"
                        style="border-left: 5px solid #dc3545; background: white; margin-top: 20px;">
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

                <form action="{{ route('mahasiswa.ubahProfil') }}" method="post" enctype="multipart/form-data" id="profilForm">
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
                                    <div class="info-value">{{ $user->created_at->locale('id')->format('F Y') ?? 'N/A' }}
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
                                                placeholder="Min. 6 karakter">
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
                                <button type="reset" class="btn btn-reset">Reset</button>
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