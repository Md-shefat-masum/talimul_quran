@props([
    'title' => '',
    'subtitle' => '',
    'badge' => '',
    'extraClass' => '',
    'bodyClass' => '',
])

<div {{ $attributes->class(['card user-management-card user-form-card', $extraClass]) }}>
    <div @class(['card-body', $bodyClass])>
        @if($title !== '' || $subtitle !== '' || $badge !== '' || isset($headerActions))
            <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                <div>
                    @if($title !== '')
                        <h4 class="user-form-card__heading mb-1">{{ $title }}</h4>
                    @endif
                    @if($subtitle !== '')
                        <p class="text-muted small mb-0">{{ $subtitle }}</p>
                    @endif
                </div>

                @isset($headerActions)
                    {{ $headerActions }}
                @elseif($badge !== '')
                    <span class="badge user-form-page-badge">{{ $badge }}</span>
                @endisset
            </div>
        @endif

        {{ $slot }}
    </div>
</div>
