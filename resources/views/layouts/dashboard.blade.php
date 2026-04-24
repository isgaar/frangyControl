@php
    $resolvedPageTitle = $pageTitle ?? trim($__env->yieldContent('title', config('app.name', 'Frangy Control')));
    $resolvedPageSubtitle = $pageSubtitle ?? null;
    $resolvedBreadcrumbs = $breadcrumbs ?? [];
    $brand = config('dashboard.brand', []);
    $menu = app(\App\Support\DashboardMenu::class)->for(auth()->user(), request());
    $hasLegacyHeader = trim($__env->yieldContent('content_header')) !== '';
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('icon.svg') }}">

    <title>{{ $resolvedPageTitle ?: config('app.name', 'Frangy Control') }}</title>

    <link href="https://fonts.bunny.net/css?family=space-grotesk:500,700|nunito:400,600,700,800" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    @include('layouts.partials.theme-head')
    @vite(['resources/css/dashboard.css', 'resources/js/dashboard.js'])
    @yield('css')
</head>
<body class="dashboard-body">
    <div class="dashboard-app">
        <button class="dashboard-backdrop"
                type="button"
                data-dashboard-backdrop
                aria-label="Cerrar navegación lateral">
        </button>

        <x-dashboard.sidebar :menu="$menu" :brand="$brand" />

        <div class="dashboard-main">
            <x-dashboard.navbar :title="$resolvedPageTitle" :brand="$brand" />

            <main class="dashboard-content">
                @unless ($hasLegacyHeader)
                    <x-dashboard.flash />
                @endunless

                @if ($hasLegacyHeader)
                    <section class="dashboard-legacy-header">
                        @yield('content_header')
                    </section>
                @elseif ($resolvedPageTitle || !empty($resolvedBreadcrumbs))
                    <x-dashboard.page-header
                        :title="$resolvedPageTitle"
                        :subtitle="$resolvedPageSubtitle"
                        :breadcrumbs="$resolvedBreadcrumbs" />
                @endif

                <section class="dashboard-panel">
                    @yield('content')
                </section>
            </main>
        </div>
    </div>

    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    @include('layouts.partials.theme-script')
    @yield('js')
</body>
</html>
