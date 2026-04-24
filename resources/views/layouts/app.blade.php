<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('icon.svg') }}">

    <title>@yield('title', config('app.name', 'Frangy Control'))</title>

    <link href="https://fonts.bunny.net/css?family=space-grotesk:500,700|nunito:400,600,700,800" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    @include('layouts.partials.theme-head')
    @yield('css')

    <style>
        :root {
            --fc-bg: #eef4ff;
            --fc-bg-gradient:
                radial-gradient(ellipse 80% 50% at 50% -10%, rgba(37, 99, 235, 0.16) 0%, transparent 60%),
                radial-gradient(ellipse 60% 40% at 90% 80%, rgba(14, 165, 233, 0.12) 0%, transparent 55%),
                linear-gradient(180deg, #eef4ff 0%, #f8fafc 100%);
            --fc-surface: rgba(255, 255, 255, 0.84);
            --fc-surface-strong: rgba(255, 255, 255, 0.96);
            --fc-border: rgba(148, 163, 184, 0.24);
            --fc-border-h: rgba(71, 85, 105, 0.28);
            --fc-text: #0f172a;
            --fc-text-muted: #64748b;
            --fc-accent: #2563eb;
            --fc-accent-h: #1d4ed8;
            --fc-danger: #dc2626;
            --fc-nav-bg: rgba(255, 255, 255, 0.76);
            --fc-nav-bg-solid: rgba(255, 255, 255, 0.94);
            --fc-theme-btn-bg: rgba(255, 255, 255, 0.92);
            --fc-shadow: 0 24px 60px rgba(15, 23, 42, 0.1);
            --fc-nav-h: 64px;
            --fc-radius: 14px;
            --fc-radius-lg: 22px;
            --fc-transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        html[data-theme='dark'] {
            --fc-bg: #050b16;
            --fc-bg-gradient:
                radial-gradient(ellipse 80% 50% at 50% -10%, rgba(61, 139, 255, 0.18) 0%, transparent 60%),
                radial-gradient(ellipse 60% 40% at 90% 80%, rgba(61, 139, 255, 0.08) 0%, transparent 55%),
                linear-gradient(180deg, #050b16 0%, #09111d 100%);
            --fc-surface: rgba(255, 255, 255, 0.05);
            --fc-surface-strong: rgba(8, 17, 31, 0.88);
            --fc-border: rgba(255, 255, 255, 0.1);
            --fc-border-h: rgba(255, 255, 255, 0.22);
            --fc-text: #e8edf5;
            --fc-text-muted: rgba(232, 237, 245, 0.62);
            --fc-accent: #60a5fa;
            --fc-accent-h: #93c5fd;
            --fc-danger: #f87171;
            --fc-nav-bg: rgba(5, 11, 22, 0.82);
            --fc-nav-bg-solid: rgba(5, 11, 22, 0.96);
            --fc-theme-btn-bg: rgba(255, 255, 255, 0.08);
            --fc-shadow: 0 24px 60px rgba(0, 0, 0, 0.32);
        }

        *, *::before, *::after {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body.public-shell-body {
            min-height: 100vh;
            margin: 0;
            color: var(--fc-text);
            background-color: var(--fc-bg);
            background-image: var(--fc-bg-gradient);
            font-family: 'Nunito', sans-serif;
            font-size: 15px;
            line-height: 1.65;
        }

        body.public-shell-body a,
        body.public-shell-body button {
            transition: color var(--fc-transition), background-color var(--fc-transition), border-color var(--fc-transition), transform var(--fc-transition), box-shadow var(--fc-transition);
        }

        #app {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .public-navbar {
            position: sticky;
            top: 0;
            z-index: 100;
            min-height: var(--fc-nav-h);
            background: var(--fc-nav-bg);
            backdrop-filter: blur(16px) saturate(1.3);
            -webkit-backdrop-filter: blur(16px) saturate(1.3);
            border-bottom: 1px solid var(--fc-border);
        }

        .public-navbar.scrolled {
            background: var(--fc-nav-bg-solid);
            border-bottom-color: var(--fc-border-h);
            box-shadow: 0 12px 40px rgba(15, 23, 42, 0.08);
        }

        .public-navbar .container {
            min-height: var(--fc-nav-h);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .nav-brand {
            display: inline-flex;
            align-items: center;
            gap: 0.7rem;
            color: var(--fc-text);
            text-decoration: none;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.05rem;
            font-weight: 700;
            letter-spacing: -0.01em;
        }

        .nav-brand:hover,
        .nav-brand:focus {
            color: var(--fc-text);
            text-decoration: none;
            opacity: 0.88;
        }

        .nav-brand img {
            width: 34px;
            height: 34px;
            object-fit: contain;
        }

        .nav-collapse {
            display: flex;
            align-items: center;
            margin-left: auto;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-left: auto;
        }

        .nav-link-item {
            color: var(--fc-text-muted);
            font-size: 0.9rem;
            font-weight: 700;
            padding: 0.5rem 0.9rem;
            border-radius: var(--fc-radius);
            text-decoration: none;
        }

        .nav-link-item:hover,
        .nav-link-item:focus,
        .nav-link-item.active {
            color: var(--fc-text);
            background: var(--fc-surface);
            text-decoration: none;
        }

        .nav-user-name {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.5rem 0.9rem;
            border-radius: 999px;
            border: 1px solid var(--fc-border);
            background: var(--fc-surface);
            color: var(--fc-text-muted);
            font-size: 0.82rem;
            font-weight: 800;
        }

        .nav-btn,
        .theme-toggle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.55rem;
            min-height: 42px;
            padding: 0.58rem 1rem;
            border: 1px solid transparent;
            border-radius: var(--fc-radius);
            font-size: 0.9rem;
            font-weight: 800;
            text-decoration: none;
            cursor: pointer;
        }

        .nav-btn-primary {
            background: var(--fc-accent);
            border-color: var(--fc-accent);
            color: #fff;
        }

        .nav-btn-primary:hover,
        .nav-btn-primary:focus {
            background: var(--fc-accent-h);
            border-color: var(--fc-accent-h);
            color: #fff;
            text-decoration: none;
            transform: translateY(-1px);
        }

        .nav-btn-ghost {
            background: transparent;
            border-color: rgba(248, 113, 113, 0.28);
            color: var(--fc-danger);
        }

        .nav-btn-ghost:hover,
        .nav-btn-ghost:focus {
            background: rgba(248, 113, 113, 0.08);
            border-color: rgba(248, 113, 113, 0.4);
            color: var(--fc-danger);
            text-decoration: none;
        }

        .theme-toggle {
            background: var(--fc-theme-btn-bg);
            border-color: var(--fc-border);
            color: var(--fc-text);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.04);
        }

        .theme-toggle:hover,
        .theme-toggle:focus {
            background: var(--fc-surface-strong);
            border-color: var(--fc-border-h);
            color: var(--fc-text);
            transform: translateY(-1px);
        }

        .theme-toggle__label {
            white-space: nowrap;
        }

        .nav-toggler {
            display: none;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            margin-left: auto;
            border: 1px solid var(--fc-border);
            border-radius: var(--fc-radius);
            background: var(--fc-surface);
            color: var(--fc-text);
            cursor: pointer;
        }

        .hamburger {
            display: flex;
            flex-direction: column;
            gap: 4px;
            width: 18px;
        }

        .hamburger span {
            display: block;
            height: 2px;
            border-radius: 999px;
            background: currentColor;
        }

        .app-main {
            flex: 1;
            padding: 2rem 0 4rem;
        }

        body.public-shell-body .card,
        body.public-shell-body .modal-content {
            border: 1px solid var(--fc-border);
            border-radius: var(--fc-radius-lg);
            background: var(--fc-surface-strong);
            color: var(--fc-text);
            box-shadow: var(--fc-shadow);
        }

        body.public-shell-body .card-header,
        body.public-shell-body .card-footer,
        body.public-shell-body .modal-header,
        body.public-shell-body .modal-footer {
            border-color: var(--fc-border);
            background: rgba(255, 255, 255, 0.04);
            color: inherit;
        }

        html[data-theme='light'] body.public-shell-body .card-header,
        html[data-theme='light'] body.public-shell-body .card-footer,
        html[data-theme='light'] body.public-shell-body .modal-header,
        html[data-theme='light'] body.public-shell-body .modal-footer {
            background: rgba(37, 99, 235, 0.04);
        }

        body.public-shell-body .form-control,
        body.public-shell-body .form-select,
        body.public-shell-body .input-group-text {
            border-radius: var(--fc-radius);
            border-color: var(--fc-border);
            background: rgba(255, 255, 255, 0.04);
            color: var(--fc-text);
        }

        html[data-theme='light'] body.public-shell-body .form-control,
        html[data-theme='light'] body.public-shell-body .form-select,
        html[data-theme='light'] body.public-shell-body .input-group-text {
            background: rgba(255, 255, 255, 0.92);
        }

        body.public-shell-body .form-control:focus,
        body.public-shell-body .form-select:focus {
            border-color: var(--fc-accent);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.16);
            color: var(--fc-text);
        }

        body.public-shell-body .form-control::placeholder {
            color: var(--fc-text-muted);
        }

        body.public-shell-body .btn-outline-secondary {
            color: var(--fc-text-muted);
            border-color: var(--fc-border-h);
        }

        body.public-shell-body .btn-outline-secondary:hover,
        body.public-shell-body .btn-outline-secondary:focus {
            color: var(--fc-text);
            background: var(--fc-surface);
            border-color: var(--fc-border-h);
        }

        body.public-shell-body .alert {
            border-radius: var(--fc-radius);
        }

        body.public-shell-body .table {
            color: var(--fc-text);
        }

        body.public-shell-body .text-muted {
            color: var(--fc-text-muted) !important;
        }

        @media (max-width: 767.98px) {
            .nav-toggler {
                display: inline-flex;
            }

            .nav-collapse {
                display: none;
                position: absolute;
                top: calc(100% + 0.25rem);
                left: 0.75rem;
                right: 0.75rem;
                z-index: 20;
                padding: 1rem;
                border: 1px solid var(--fc-border);
                border-radius: 20px;
                background: var(--fc-nav-bg-solid);
                box-shadow: var(--fc-shadow);
            }

            .nav-collapse.open {
                display: block;
            }

            .nav-actions {
                flex-direction: column;
                align-items: stretch;
                margin-left: 0;
            }

            .nav-btn,
            .theme-toggle {
                width: 100%;
            }

            .nav-user-name {
                justify-content: center;
            }

            .app-main {
                padding-top: 1rem;
            }
        }
    </style>
</head>

<body class="public-shell-body">
    <div id="app">
        <nav class="public-navbar navbar navbar-light bg-white" id="publicNav">
            <div class="container position-relative">
                <a class="nav-brand navbar-brand"
                   href="{{ auth()->check() && Route::has('home') ? route('home') : url('/') }}">
                    <img src="{{ asset('pestaña.png') }}" alt="{{ config('app.name') }}">
                    <span>{{ config('app.name', 'Frangy Control') }}</span>
                </a>

                <button class="nav-toggler navbar-toggler"
                        id="navToggler"
                        type="button"
                        aria-expanded="false"
                        aria-label="{{ __('Toggle navigation') }}">
                    <span class="hamburger">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>

                <div class="nav-collapse" id="navCollapse">
                    <div class="nav-actions">
                        <button class="theme-toggle" type="button" data-theme-toggle aria-label="Activar modo oscuro">
                            <i class="fas fa-moon" data-theme-icon></i>
                            <span class="theme-toggle__label" data-theme-text>Modo oscuro</span>
                        </button>

                        @guest
                            <a href="{{ route('landing.pages.welcome') }}" class="nav-link-item nav-link">Inicio</a>

                            @if (Route::has('login'))
                                <a href="{{ route('login') }}" class="nav-btn nav-btn-primary">
                                    <i class="fas fa-sign-in-alt fa-sm"></i>
                                    Acceder
                                </a>
                            @endif
                        @else
                            <span class="nav-user-name">
                                <i class="fas fa-user-circle fa-sm"></i>
                                {{ Auth::user()->name }}
                            </span>

                            @if (Route::has('home'))
                                <a href="{{ route('home') }}" class="nav-link-item nav-link">
                                    <i class="fas fa-th-large fa-sm mr-1"></i>
                                    Control
                                </a>
                            @endif

                            <form action="{{ route('logout') }}" method="POST" class="d-inline-flex">
                                @csrf
                                <button type="submit" class="nav-btn nav-btn-ghost">
                                    <i class="fas fa-sign-out-alt fa-sm"></i>
                                    {{ __('Logout') }}
                                </button>
                            </form>
                        @endguest
                    </div>
                </div>
            </div>
        </nav>

        <main class="app-main py-4">
            @yield('content')
        </main>
    </div>

    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <script>
        (function () {
            var nav = document.getElementById('publicNav');
            var toggler = document.getElementById('navToggler');
            var collapse = document.getElementById('navCollapse');

            function syncNavbar() {
                if (!nav) {
                    return;
                }

                nav.classList.toggle('scrolled', window.scrollY > 8);
            }

            syncNavbar();
            window.addEventListener('scroll', syncNavbar, { passive: true });

            if (toggler && collapse) {
                toggler.addEventListener('click', function () {
                    var open = collapse.classList.toggle('open');
                    toggler.setAttribute('aria-expanded', open ? 'true' : 'false');
                });

                document.addEventListener('click', function (event) {
                    if (!nav.contains(event.target)) {
                        collapse.classList.remove('open');
                        toggler.setAttribute('aria-expanded', 'false');
                    }
                });
            }
        }());
    </script>

    @include('layouts.partials.theme-script')
    @yield('js')
</body>
</html>
