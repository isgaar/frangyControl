@php
    $resolvedPageTitle = $pageTitle ?? trim($__env->yieldContent('title', config('app.name', 'Frangy Control')));
    $brand = config('dashboard.brand', []);
    $menu = app(\App\Support\DashboardMenu::class)->for(auth()->user(), request());
    $legacyHeaderContent = trim($__env->yieldContent('content_header'));
    $hasLegacyHeader = $legacyHeaderContent !== '';
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('icon.svg') }}">

    <title>{{ $resolvedPageTitle ?: config('app.name', 'Frangy Control') }}</title>

    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    @include('layouts.partials.theme-head')
    <script>
        (function () {
            try {
                if (localStorage.getItem('frangy-control-dashboard-sidebar-collapsed') === 'true') {
                    document.documentElement.classList.add('dashboard-sidebar-collapsed');
                }
            } catch (error) {
                return;
            }
        }());
    </script>
    @vite(['resources/css/dashboard.css', 'resources/js/dashboard.js'])
    @yield('css')
    <style>
        /* Tipografía Global Inter */
        body.dashboard-body,
        body.dashboard-body input,
        body.dashboard-body select,
        body.dashboard-body textarea,
        body.dashboard-body button,
        body.dashboard-body .table,
        body.dashboard-body .card {
            font-family: 'inter', sans-serif !important;
        }

        /* Clases Utilitarias Estandarizadas */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }
        
        .page-eyebrow {
            font-size: 0.78rem;
            color: var(--dashboard-muted);
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .02em;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 800;
            margin: 2px 0 0;
            color: var(--dashboard-text);
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 8px;
            margin-bottom: 1rem;
        }

        .metric-card {
            padding: 10px 14px;
            border: 1px solid var(--dashboard-border);
            border-radius: 12px;
            background: var(--dashboard-surface);
        }

        .metric-card__label {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--dashboard-muted);
            text-transform: uppercase;
        }

        .metric-card__value {
            font-size: 1.4rem;
            font-weight: 800;
            margin: 3px 0;
            color: var(--dashboard-text);
        }

        .metric-card__copy {
            font-size: 0.75rem;
            margin: 0;
            color: var(--dashboard-muted);
        }

        .filter-toolbar {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }

        .filter-search-box {
            display: flex;
            align-items: center;
            flex: 1;
            min-width: 220px;
            border: 1px solid var(--dashboard-border);
            border-radius: 6px;
            overflow: hidden;
            height: 40px;
            background: var(--dashboard-surface);
        }

        .filter-search-box input {
            flex: 1;
            border: none;
            outline: none;
            padding: 0 12px;
            font-size: 0.9rem;
            height: 100%;
            background: transparent;
            color: inherit;
        }

        .filter-search-box button {
            height: 40px;
            width: 44px;
            background: var(--dashboard-primary);
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: #fff;
        }

        .filter-toolbar .form-control {
            height: 40px;
            width: auto;
            font-size: 0.9rem;
            border-color: var(--dashboard-border);
            background-color: var(--dashboard-surface);
            color: var(--dashboard-text);
        }

        .filter-btn-clear {
            height: 40px;
            display: inline-flex;
            align-items: center;
            font-size: 0.9rem;
            font-weight: 600;
            gap: 6px;
        }

        .table-compact {
            font-size: 0.9rem;
            margin-bottom: 0;
        }

        .table-compact thead {
            font-size: 0.82rem;
        }

        .table-compact th, 
        .table-compact td {
            font-size: 0.9rem;
            vertical-align: middle;
        }

        .table-compact-info {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: .75rem;
        }

        .table-compact-info p {
            font-size: 0.9rem;
            margin: 0;
        }

        .badge-active-filter {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.84rem;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 20px;
            background: rgba(255, 193, 7, 0.15);
            color: #856404;
            border: 1px solid #ffc107;
        }
        
        html[data-theme="dark"] .badge-active-filter {
            color: #ffc107;
        }
    </style>
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
                        {!! $legacyHeaderContent !!}
                    </section>
                @endif

                <section class="dashboard-panel">
                    @yield('content')
                </section>
            </main>
        </div>
    </div>

    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @include('layouts.partials.theme-script')
    @yield('js')
    <script>
        $(document).ready(function() {
            @auth
            function fetchUnreadCount() {
                $.ajax({
                    url: '{{ route("chat.unread_count") }}',
                    type: 'GET',
                    success: function(res) {
                        const count = res.count;
                        const badge = $('#nav-unread-count');
                        if (count > 0) {
                            badge.text(count).show();
                        } else {
                            badge.hide();
                        }
                    }
                });
            }
            
            fetchUnreadCount();
            setInterval(fetchUnreadCount, 10000); // Revisar cada 10 segundos globalmente
            @endauth
        });
    </script>
</body>
</html>
