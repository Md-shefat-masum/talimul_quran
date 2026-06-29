<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-url" content="{{ url('/') }}">
    <title>@yield('title', 'Dashboard') | {{ config('app.name', 'Laravel') }}</title>

    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/materialdesignicons/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/backend/styles/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/backend/styles/custom.css') }}?v={{ env('APP_VERSION', '1.0.0') }}">
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/datatables/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/datatables/css/responsive.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/sweetalert2/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/backend/styles/theme-teal.css') }}?v={{ env('APP_VERSION', '1.0.0') }}">
    <link rel="stylesheet" href="{{ asset('assets/backend/styles/modules/sidebar.css') }}?v={{ env('APP_VERSION', '1.0.0') }}">
    <link rel="stylesheet" href="{{ asset('assets/backend/styles/modules/users.css') }}?v={{ env('APP_VERSION', '1.0.0') }}">
    <link rel="stylesheet" href="{{ asset('assets/backend/styles/layout.css') }}?v={{ env('APP_VERSION', '1.0.0') }}">
    <link rel="stylesheet" href="{{ asset('assets/backend/js/file_manager/vendor/filerobot-image-editor.bundle.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/backend/js/file_manager/file-manager.css') }}?v={{ env('APP_VERSION', '1.0.0') }}">
    @stack('styles')

    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
</head>
<body>
<div class="container-scroller">
    @include('backend.layout.includes.navbar')

    <div class="container-fluid page-body-wrapper">
        @include('backend.layout.includes.sidebar')

        <div class="main-panel">
            <div class="content-wrapper">
                @yield('content')
            </div>

            @include('backend.layout.includes.footer')
        </div>
    </div>
</div>

@unless(request()->routeIs('backend.file-manager.index'))
    <file-manager></file-manager>
@endunless

@include('backend.components.flash-alert')

@include('backend.layout.includes.scripts')
@stack('scripts')
</body>
</html>
