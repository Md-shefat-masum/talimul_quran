@props([
    'variant' => 'index',
    'kicker' => '',
    'title' => '',
    'subtitle' => '',
    'icon' => 'mdi mdi-view-dashboard-outline',
])

@if($variant === 'hero')
    <div {{ $attributes->class(['user-management-hero user-management-hero--compact']) }}>
        <div>
            @if($kicker !== '')
                <div class="user-management-hero__eyebrow">{{ $kicker }}</div>
            @endif
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="{{ $icon }}"></i>
                </span>
                {{ $title }}
            </h3>
            @if($subtitle !== '')
                <p class="user-management-hero__subtitle">{{ $subtitle }}</p>
            @endif
        </div>

        @isset($actions)
            {{ $actions }}
        @endisset
    </div>
@else
    <div {{ $attributes->class(['user-index-topbar']) }}>
        <div class="user-index-heading">
            <span class="user-index-heading__icon bg-gradient-primary text-white">
                <i class="{{ $icon }}"></i>
            </span>
            <div>
                @if($kicker !== '')
                    <p class="user-index-heading__kicker">{{ $kicker }}</p>
                @endif
                <h3 class="user-index-heading__title">{{ $title }}</h3>
                @if($subtitle !== '')
                    <p class="user-index-heading__subtitle">{{ $subtitle }}</p>
                @endif
            </div>
        </div>

        @isset($actions)
            {{ $actions }}
        @endisset
    </div>
@endif
