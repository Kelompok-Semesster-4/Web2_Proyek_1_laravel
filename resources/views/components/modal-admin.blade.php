@props([
    'id',
    'title' => '',
    'subtitle' => null,
    'icon' => null,
    'modalClass' => 'modal fade',
    'headerGradient' => 'linear-gradient(135deg, #22c55e 0%, #16a34a 100%)',
    'headerTextClass' => 'text-white',
    'dialogClass' => 'modal-dialog modal-dialog-centered',
    'contentClass' => 'modal-content border-0 shadow-lg',
    'closeButtonWhite' => true,
    'titleId' => null,
    'subtitleId' => null,
])

<div class="{{ $modalClass }}" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="{{ $dialogClass }}">
        <div class="{{ $contentClass }}" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header {{ $headerTextClass }}" style="background: {{ $headerGradient }}; border: none;">
                <div>
                    <h5 class="modal-title fw-bold" @if ($titleId) id="{{ $titleId }}" @endif>
                        @if ($icon)
                            <i class="{{ $icon }} me-2"></i>
                        @endif
                        {{ $title }}
                    </h5>
                    @if ($subtitle)
                        <div class="modal-subtitle" @if ($subtitleId) id="{{ $subtitleId }}" @endif>
                            {{ $subtitle }}
                        </div>
                    @endif
                </div>
                <button
                    type="button"
                    class="btn-close {{ $closeButtonWhite ? 'btn-close-white' : '' }}"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>
            </div>

            {{ $slot }}
        </div>
    </div>
</div>
