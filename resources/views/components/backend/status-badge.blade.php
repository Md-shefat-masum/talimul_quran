@props([
    'status' => null,
    'label' => null,
    'variant' => null,
])

@php
    $resolvedVariant = $variant;
    $resolvedLabel = $label;

    if ($resolvedVariant === null) {
        if (is_bool($status) || $status === 1 || $status === 0 || $status === '1' || $status === '0') {
            $resolvedVariant = filter_var($status, FILTER_VALIDATE_BOOL) ? 'active' : 'inactive';
        } else {
            $resolvedVariant = 'muted';
        }
    }

    if ($resolvedLabel === null) {
        $resolvedLabel = match ($resolvedVariant) {
            'active' => 'Active',
            'inactive' => 'Inactive',
            'system' => 'System',
            default => ucfirst((string) $resolvedVariant),
        };
    }
@endphp

<span {{ $attributes->class([
    'user-status-badge',
    'user-status-badge--'.$resolvedVariant,
]) }}>
    {{ $resolvedLabel }}
</span>
