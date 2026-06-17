@props([
    'ability' => null,
    'as' => 'a',
    'href' => '#',
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'sm',
    'icon' => null,
    'disabled' => false,
])

@php
    $canRender = $ability === null || auth()->user()?->can($ability);
    $variantClass = match ($variant) {
        'primary' => 'btn-gradient-primary',
        'outline-primary' => 'btn-outline-primary',
        'danger' => 'btn-outline-danger',
        'light' => 'btn-light',
        default => $variant,
    };
    $sizeClass = $size !== '' ? 'btn-'.$size : '';
@endphp

@if($canRender)
    @if($as === 'button')
        <button
            type="{{ $type }}"
            {{ $attributes->class(['btn', $sizeClass, $variantClass]) }}
            @disabled($disabled)
        >
            @if($icon)
                <i class="{{ $icon }} me-1"></i>
            @endif
            {{ $slot }}
        </button>
    @else
        <a href="{{ $href }}" {{ $attributes->class(['btn', $sizeClass, $variantClass]) }}>
            @if($icon)
                <i class="{{ $icon }} me-1"></i>
            @endif
            {{ $slot }}
        </a>
    @endif
@endif
