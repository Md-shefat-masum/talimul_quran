<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Authentication') | {{ config('app.name', 'Laravel') }}</title>

    <link rel="stylesheet" href="{{ asset('assets/backend/styles/auth.css') }}?v={{ env('APP_VERSION', '1.0.0') }}">
</head>
<body>
    <main class="auth-shell">
        <div class="auth-shell__glow auth-shell__glow--left" aria-hidden="true"></div>
        <div class="auth-shell__glow auth-shell__glow--right" aria-hidden="true"></div>
        <div class="auth-shell__grid" aria-hidden="true"></div>

        <section class="auth-panel" aria-label="@yield('title', 'Authentication')">
            <div class="auth-panel__mark" aria-hidden="true">
                <span>{{ strtoupper(substr(config('app.name', 'A'), 0, 1)) }}</span>
            </div>

            @yield('content')
        </section>
    </main>
</body>
</html>
