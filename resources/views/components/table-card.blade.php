@props([
    'title' => 'Data Table',
    'icon' => 'bi bi-table',
    'searchPlaceholder' => 'Cari data...',
    'tableId' => 'dataTable',
    'searchId' => null,
    'total' => 0,
    'totalLabel' => 'data terdaftar',
    'empty' => false,
    'emptyTitle' => 'Belum ada data',
    'emptySubtitle' => 'Data akan muncul di sini',
    'colspan' => 1,
    'showSearch' => true,
    'showFooter' => true,
    'bodyMaxHeight' => null,
    'stickyHead' => false,
])

@php
    $finalSearchId = $searchId ?? $tableId . 'Search';
@endphp

<div class="card shadow border-0" style="border-radius: 15px; overflow: hidden;">
    <div class="card-header bg-white py-3 border-bottom"
        style="background: linear-gradient(to right, #f8f9fa, #e9ecef) !important;">
        <div class="row align-items-center g-3">
            <div class="col-md-4">
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="{{ $icon }} me-1" style="color: #22c55e;"></i>{{ ' '. $title }}
                </h5>
            </div>

            <div class="col-md-8">
                <div class="d-flex flex-wrap justify-content-md-end align-items-center gap-2">
                    @if (isset($headerActions))
                        <div class="header-actions">
                            {{ $headerActions }}
                        </div>
                    @endif

                    @if ($showSearch)
                        <div class="input-group shadow-sm" style="border-radius: 8px; overflow: hidden; max-width: 300px;">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search" style="color: #22c55e;"></i>
                            </span>
                            <input type="text"
                                class="form-control border-start-0 bg-white table-search-input"
                                id="{{ $finalSearchId }}"
                                data-target-table="{{ $tableId }}"
                                placeholder="{{ $searchPlaceholder }}"
                                style="border-left: 0;">
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive table-scroll-area"
            @if ($bodyMaxHeight)
                style="max-height: {{ $bodyMaxHeight }}; overflow-y: auto;"
            @endif>
            <table class="table table-hover align-middle mb-0 custom-data-table" id="{{ $tableId }}">
                <thead
                    style="background: linear-gradient(to right, #f8f9fa, #e9ecef); @if ($stickyHead) position: sticky; top: 0; z-index: 2; @endif">
                    {{ $head }}
                </thead>

                <tbody>
                    @if ($empty)
                        <tr>
                            <td colspan="{{ $colspan }}" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox display-4 d-block mb-3"></i>
                                    <p class="mb-0">{{ $emptyTitle }}</p>
                                    <small>{{ $emptySubtitle }}</small>
                                </div>
                            </td>
                        </tr>
                    @else
                        {{ $slot }}
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <style>
        .table-scroll-area {
            scrollbar-width: thin;
            scrollbar-color: #ffffff #edf2f7;
        }

        .table-scroll-area::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .table-scroll-area::-webkit-scrollbar-track {
            background: #edf2f7;
        }

        .table-scroll-area::-webkit-scrollbar-thumb {
            background: #ffffff;
            border-radius: 999px;
            border: 1px solid #d1d5db;
        }

        .table-scroll-area::-webkit-scrollbar-thumb:hover {
            background: #f8fafc;
        }
    </style>

    @if ($showFooter)
        <div class="card-footer bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted fw-semibold">
                    <i class="bi bi-info-circle-fill me-1" style="color: #22c55e;"></i>Total Data:
                    <span class="badge ms-1"
                        style="background: linear-gradient(135deg, #22c55e, #16a34a);">
                        {{ $total }}
                    </span>
                    {{ $totalLabel }}
                </small>

                <small class="text-muted">
                    <i class="bi bi-calendar-check me-1"></i>{{ date('d F Y') }}
                </small>
            </div>
        </div>
    @endif
</div>