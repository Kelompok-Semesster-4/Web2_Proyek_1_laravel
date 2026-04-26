@php
    $success = session('success') ?? session('flash_success');
    $error = session('error') ?? session('flash_error');
@endphp

@if ($success)
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-left: 5px solid #28a745; background: white;">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-3 fs-4 text-success"></i>
            <div>
                <strong class="d-block">Berhasil!</strong>
                <span class="text-muted">
                    @if($success === 'add') Gedung/Data berhasil ditambahkan.
                    @elseif($success === 'edit') Gedung/Data berhasil diperbarui.
                    @elseif($success === 'delete') Gedung/Data berhasil dihapus.
                    @else {{ $success }}
                    @endif
                </span>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($error)
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-left: 5px solid #dc3545; background: white;">
        <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill me-3 fs-4 text-danger"></i>
            <div>
                <strong class="d-block">Terjadi Kesalahan!</strong>
                <span class="text-muted">{{ $error }}</span>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-left: 5px solid #ffc107; background: white;">
        <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-circle-fill me-3 fs-4 text-warning"></i>
            <div>
                <strong class="d-block">Periksa Kembali Input Anda:</strong>
                <ul class="mb-0 ps-3 text-muted">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
