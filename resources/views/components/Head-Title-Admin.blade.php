@props([
    'title' => null,
    'icon' => null,
    'buttonText' => 'Tambah Gedung',
    'modalTarget' => '#modalAddGedung',
])

<div class="kelola-header mb-4">
    <div class="kelola-header-title">
        @if ($icon)
            <i class="{{ $icon }}"></i>
        @endif

        <h1>{{ $title }}</h1>
    </div>

    <button class="btn-tambah" data-bs-toggle="modal" data-bs-target="{{ $modalTarget }}">
        <i class="bi bi-plus-circle-fill me-2"></i>{{ $buttonText }}
    </button>
</div>