@php
    $alerts = collect([
        ['type' => 'success', 'message' => session('success')],
        ['type' => 'error', 'message' => session('error')],
        ['type' => 'warning', 'message' => session('warning')],
        ['type' => 'info', 'message' => session('info')],
        ['type' => 'success', 'message' => session('status')],
    ])->filter(fn (array $alert): bool => filled($alert['message']))->values();
@endphp

@if($alerts->isNotEmpty())
    <script type="application/json" data-app-flash-alerts>@json($alerts)</script>
@endif
