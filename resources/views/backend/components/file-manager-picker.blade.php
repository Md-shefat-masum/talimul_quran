@php
    $field = $field ?? 'file_url';
    $pathField = $pathField ?? str_replace('_url', '_path', $field);
    $id = $id ?? (($formId ?? 'form').'-'.str_replace('_', '-', $field));
    $label = $label ?? 'File';
    $value = $value ?? '';
    $pathValue = $pathValue ?? '';
    $placeholder = $placeholder ?? 'No file selected';
    $buttonText = $buttonText ?? 'Select';
    $clearText = $clearText ?? 'Clear';
    $preview = $preview ?? 'image';
    $size = $size ?? null;
    $folder = $folder ?? '';
    $accept = $accept ?? 'image/*';
    $multiple = $multiple ?? false;
    $usageModule = $usageModule ?? null;
    $usageField = $usageField ?? $field;
    $ownerType = $ownerType ?? null;
    $ownerId = $ownerId ?? null;
    $usageLabel = $usageLabel ?? $label;
    $valueFormat = $valueFormat ?? ($multiple ? 'json' : 'string');
    $normalizeValues = static function ($raw): array {
        if (is_array($raw)) {
            return array_values(array_filter($raw));
        }

        if (! is_string($raw) || trim($raw) === '') {
            return [];
        }

        $decoded = json_decode($raw, true);

        if (is_array($decoded)) {
            return array_values(array_filter($decoded));
        }

        return array_values(array_filter(array_map('trim', explode(',', $raw))));
    };
    $urlValues = $normalizeValues($value);
    $pathValues = $normalizeValues($pathValue);
    $storedValue = $multiple && is_array($value) ? json_encode($urlValues) : $value;
    $storedPathValue = $multiple && is_array($pathValue) ? json_encode($pathValues) : $pathValue;
    $displayValue = $multiple ? (count($urlValues) ? count($urlValues).' file(s) selected' : '') : $value;
@endphp

<label class="form-label" for="{{ $id }}">{{ $label }}</label>
<div
    class="file-manager-picker"
    data-file-manager-picker
    data-picker-field="{{ $field }}"
    @if($multiple) data-file-manager-picker-multiple @endif
    data-file-manager-value-format="{{ $valueFormat }}"
>
    <div class="file-manager-picker__preview" data-file-manager-preview>
        @if(! $multiple && $preview === 'image' && $value)
            <img src="{{ $value }}" alt="{{ $label }}">
        @else
            <i class="mdi {{ $multiple ? 'mdi-image-multiple-outline' : ($preview === 'image' ? 'mdi-image-outline' : 'mdi-file-outline') }}"></i>
        @endif
    </div>
    <div class="file-manager-picker__controls">
        <input
            type="hidden"
            id="{{ $id }}"
            name="{{ $field }}"
            value="{{ $storedValue }}"
            data-file-manager-value-field
        >
        <input
            type="hidden"
            name="{{ $pathField }}"
            value="{{ $storedPathValue }}"
            data-file-manager-path-field
        >
        <div class="input-group">
            <input
                type="text"
                class="form-control js-file-manager-display"
                value="{{ $displayValue }}"
                placeholder="{{ $placeholder }}"
                readonly
            >
            <button
                type="button"
                class="btn btn-outline-primary"
                data-file-manager
                data-file-manager-target="#{{ $id }}"
                data-file-manager-accept="{{ $accept }}"
                data-file-manager-value-format="{{ $valueFormat }}"
                @if($size) data-file-manager-size="{{ $size }}" @endif
                @if($folder !== '') data-file-manager-path="{{ $folder }}" @endif
                @if($multiple) data-file-manager-multiple @endif
                @if($usageModule && $ownerId) data-file-manager-usage-module="{{ $usageModule }}" @endif
                @if($usageModule && $ownerId && $usageField) data-file-manager-usage-field="{{ $usageField }}" @endif
                @if($usageModule && $ownerId && $ownerType) data-file-manager-owner-type="{{ $ownerType }}" @endif
                @if($usageModule && $ownerId) data-file-manager-owner-id="{{ $ownerId }}" @endif
                @if($usageModule && $ownerId && $usageLabel) data-file-manager-usage-label="{{ $usageLabel }}" @endif
            >
                <i class="mdi mdi-folder-image me-1"></i>
                {{ $buttonText }}
            </button>
            <button type="button" class="btn btn-light js-file-manager-clear">
                <i class="mdi mdi-close-circle-outline me-1"></i>
                {{ $clearText }}
            </button>
        </div>
        @if($multiple)
            <div class="file-manager-picker__gallery" data-file-manager-gallery>
                @foreach($urlValues as $index => $url)
                    <button type="button" class="file-manager-picker__chip" data-file-manager-remove-index="{{ $index }}">
                        @if($preview === 'image')
                            <img src="{{ $url }}" alt="{{ $label }} {{ $index + 1 }}">
                        @else
                            <i class="mdi mdi-file-outline"></i>
                        @endif
                        <span>{{ basename(parse_url($url, PHP_URL_PATH) ?: $url) }}</span>
                        <i class="mdi mdi-close"></i>
                    </button>
                @endforeach
            </div>
        @endif
        <div class="invalid-feedback d-block" data-error-for="{{ $field }}"></div>
        <div class="invalid-feedback d-block" data-error-for="{{ $pathField }}"></div>
    </div>
</div>
