<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('icon.svg') }}">

    <title>@yield('title', config('app.name', 'Frangy Control'))</title>

    <link href="https://fonts.bunny.net/css?family=space-grotesk:500,700|nunito:400,600,700,800" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">

    <style>
        /* ── Design Tokens ── */
        :root {
            --fc-bg:          #050b16;
            --fc-surface:     rgba(255,255,255,0.04);
            --fc-border:      rgba(255,255,255,0.10);
            --fc-border-h:    rgba(255,255,255,0.22);
            --fc-text:        #e8edf5;
            --fc-text-muted:  rgba(232,237,245,0.55);
            --fc-accent:      #3d8bff;
            --fc-accent-h:    #5fa0ff;
            --fc-danger:      #e25f5f;
            --fc-nav-h:       56px;
            --fc-radius:      10px;
            --fc-radius-lg:   16px;
            --fc-transition:  0.2s cubic-bezier(0.4,0,0.2,1);
        }

        *, *::before, *::after { box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            min-height: 100vh;
            margin: 0;
            color: var(--fc-text);
            background-color: var(--fc-bg);
            background-image:
                radial-gradient(ellipse 80% 50% at 50% -10%, rgba(61,139,255,0.18) 0%, transparent 60%),
                radial-gradient(ellipse 60% 40% at 90% 80%,  rgba(61,139,255,0.08) 0%, transparent 55%);
            font-family: 'Nunito', sans-serif;
            font-size: 15px;
            line-height: 1.65;
        }

        #app { min-height: 100vh; display: flex; flex-direction: column; }

        /* ── Navbar ── */
        .public-navbar {
            position: sticky;
            top: 0;
            z-index: 100;
            height: var(--fc-nav-h);
            background: rgba(5,11,22,0.82);
            backdrop-filter: blur(14px) saturate(1.4);
            -webkit-backdrop-filter: blur(14px) saturate(1.4);
            border-bottom: 1px solid var(--fc-border);
            transition: border-color var(--fc-transition), background var(--fc-transition);
        }

        .public-navbar.scrolled {
            background: rgba(5,11,22,0.96);
            border-bottom-color: var(--fc-border-h);
        }

        .public-navbar .container {
            height: 100%;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        /* Brand */
        .nav-brand {
            display: inline-flex;
            align-items: center;
            gap: 0.65rem;
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            font-size: 1.05rem;
            color: var(--fc-text);
            text-decoration: none;
            flex-shrink: 0;
            letter-spacing: -0.01em;
            transition: opacity var(--fc-transition);
        }

        .nav-brand:hover { opacity: 0.85; color: var(--fc-text); text-decoration: none; }

        .nav-brand img {
            height: 32px;
            width: auto;
            object-fit: contain;
        }

        /* Desktop nav */
        .nav-actions {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            margin-left: auto;
        }

        .nav-link-item {
            color: var(--fc-text-muted);
            font-weight: 600;
            font-size: 0.875rem;
            text-decoration: none;
            padding: 0.4rem 0.85rem;
            border-radius: var(--fc-radius);
            transition: color var(--fc-transition), background var(--fc-transition);
            white-space: nowrap;
        }

        .nav-link-item:hover {
            color: var(--fc-text);
            background: rgba(255,255,255,0.07);
            text-decoration: none;
        }

        .nav-link-item.active {
            color: var(--fc-text);
        }

        .nav-user-name {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--fc-text-muted);
            padding: 0.4rem 0.75rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--fc-border);
            border-radius: 999px;
            letter-spacing: 0.01em;
        }

        .nav-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-family: inherit;
            font-size: 0.875rem;
            font-weight: 700;
            padding: 0.42rem 1.1rem;
            border-radius: var(--fc-radius);
            border: 1px solid transparent;
            cursor: pointer;
            text-decoration: none;
            transition: all var(--fc-transition);
            white-space: nowrap;
        }

        .nav-btn-primary {
            background: var(--fc-accent);
            color: #fff;
            border-color: var(--fc-accent);
        }

        .nav-btn-primary:hover {
            background: var(--fc-accent-h);
            border-color: var(--fc-accent-h);
            color: #fff;
            text-decoration: none;
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(61,139,255,0.35);
        }

        .nav-btn-ghost {
            background: transparent;
            color: var(--fc-danger);
            border-color: rgba(226,95,95,0.3);
        }

        .nav-btn-ghost:hover {
            background: rgba(226,95,95,0.1);
            border-color: rgba(226,95,95,0.6);
            color: var(--fc-danger);
            text-decoration: none;
        }

        /* Hamburger */
        .nav-toggler {
            display: none;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            background: var(--fc-surface);
            border: 1px solid var(--fc-border);
            border-radius: var(--fc-radius);
            cursor: pointer;
            flex-shrink: 0;
            margin-left: auto;
            transition: background var(--fc-transition), border-color var(--fc-transition);
        }

        .nav-toggler:hover {
            background: rgba(255,255,255,0.08);
            border-color: var(--fc-border-h);
        }

        .hamburger {
            display: flex;
            flex-direction: column;
            gap: 5px;
            width: 18px;
        }

        .hamburger span {
            display: block;
            height: 2px;
            background: var(--fc-text);
            border-radius: 2px;
            transition: transform var(--fc-transition), opacity var(--fc-transition);
        }

        .nav-toggler[aria-expanded="true"] .hamburger span:nth-child(1) {
            transform: translateY(7px) rotate(45deg);
        }
        .nav-toggler[aria-expanded="true"] .hamburger span:nth-child(2) {
            opacity: 0;
        }
        .nav-toggler[aria-expanded="true"] .hamburger span:nth-child(3) {
            transform: translateY(-7px) rotate(-45deg);
        }

        /* Mobile drawer */
        .nav-collapse {
            display: flex;
        }

        @media (max-width: 767.98px) {
            .nav-toggler { display: flex; }

            .nav-brand { font-size: 0.95rem; }

            .nav-collapse {
                display: none;
                position: absolute;
                top: var(--fc-nav-h);
                left: 0;
                right: 0;
                background: rgba(5,11,22,0.97);
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                border-bottom: 1px solid var(--fc-border);
                padding: 1rem 1.25rem 1.5rem;
                flex-direction: column;
                gap: 0.35rem;
                animation: slideDown 0.22s ease forwards;
            }

            @keyframes slideDown {
                from { opacity: 0; transform: translateY(-8px); }
                to   { opacity: 1; transform: translateY(0); }
            }

            .nav-collapse.open { display: flex; }

            .nav-link-item {
                padding: 0.65rem 0.85rem;
                font-size: 0.95rem;
            }

            .nav-user-name {
                align-self: flex-start;
                margin-bottom: 0.25rem;
            }

            .nav-btn { padding: 0.6rem 1rem; font-size: 0.9rem; justify-content: center; }

            .nav-actions { flex-direction: column; align-items: stretch; gap: 0.35rem; margin: 0; width: 100%; }

            .nav-btn-ghost { border-color: rgba(226,95,95,0.4); }
        }

        /* ── Main content ── */
        .app-main {
            flex: 1;
            padding: 2rem 0 4rem;
        }

        /* ── Cards & surfaces ── */
        .card-fc {
            background: var(--fc-surface);
            border: 1px solid var(--fc-border);
            border-radius: var(--fc-radius-lg);
            padding: 1.75rem 2rem;
            transition: border-color var(--fc-transition);
        }

        .card-fc:hover { border-color: var(--fc-border-h); }

        /* ── Forms ── */
        .form-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--fc-text-muted);
            margin-bottom: 0.45rem;
        }

        .form-control,
        .form-select {
            width: 100%;
            background: rgba(255,255,255,0.04) !important;
            border: 1px solid var(--fc-border) !important;
            border-radius: var(--fc-radius) !important;
            color: var(--fc-text) !important;
            font-family: 'Nunito', sans-serif;
            font-size: 0.9375rem;
            padding: 0.65rem 1rem !important;
            transition: border-color var(--fc-transition), box-shadow var(--fc-transition), background var(--fc-transition);
            outline: none;
        }

        .form-control:focus,
        .form-select:focus {
            background: rgba(255,255,255,0.07) !important;
            border-color: var(--fc-accent) !important;
            box-shadow: 0 0 0 3px rgba(61,139,255,0.18) !important;
            color: var(--fc-text) !important;
        }

        .form-control::placeholder { color: rgba(232,237,245,0.3); }
        .form-select option { background: #0e1a2e; color: var(--fc-text); }

        .form-control.is-invalid,
        .form-select.is-invalid {
            border-color: var(--fc-danger) !important;
        }

        .invalid-feedback { color: var(--fc-danger); font-size: 0.8rem; margin-top: 0.3rem; }

        /* Checkbox / Radio */
        .form-check-input {
            background-color: rgba(255,255,255,0.06) !important;
            border-color: var(--fc-border-h) !important;
        }

        .form-check-input:checked {
            background-color: var(--fc-accent) !important;
            border-color: var(--fc-accent) !important;
        }

        /* ── Buttons ── */
        .btn {
            font-family: 'Nunito', sans-serif;
            font-weight: 700;
            border-radius: var(--fc-radius);
            transition: all var(--fc-transition);
            letter-spacing: 0.01em;
        }

        .btn-primary {
            background: var(--fc-accent);
            border-color: var(--fc-accent);
            color: #fff;
        }

        .btn-primary:hover {
            background: var(--fc-accent-h);
            border-color: var(--fc-accent-h);
            box-shadow: 0 4px 18px rgba(61,139,255,0.35);
            transform: translateY(-1px);
        }

        .btn-outline-secondary {
            color: var(--fc-text-muted);
            border-color: var(--fc-border-h);
            background: transparent;
        }

        .btn-outline-secondary:hover {
            background: rgba(255,255,255,0.07);
            color: var(--fc-text);
            border-color: var(--fc-border-h);
        }

        /* ── Alerts ── */
        .alert {
            border-radius: var(--fc-radius);
            border-width: 1px;
            font-size: 0.9rem;
        }

        .alert-danger  { background: rgba(226,95,95,0.1);  border-color: rgba(226,95,95,0.3);  color: #f4a3a3; }
        .alert-success { background: rgba(52,199,89,0.1);  border-color: rgba(52,199,89,0.3);  color: #7de3a0; }
        .alert-warning { background: rgba(255,184,0,0.1);  border-color: rgba(255,184,0,0.3);  color: #ffd966; }
        .alert-info    { background: rgba(61,139,255,0.1); border-color: rgba(61,139,255,0.3); color: #85b8ff; }

        /* ── Tables ── */
        .table {
            color: var(--fc-text);
            border-color: var(--fc-border);
        }

        .table th {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--fc-text-muted);
            border-bottom-color: var(--fc-border-h);
            padding: 0.85rem 1rem;
        }

        .table td {
            padding: 0.85rem 1rem;
            border-bottom-color: var(--fc-border);
            vertical-align: middle;
        }

        .table-hover tbody tr:hover td {
            background: rgba(255,255,255,0.03);
        }

        /* ── Badges ── */
        .badge {
            font-weight: 700;
            font-size: 0.7rem;
            letter-spacing: 0.04em;
            padding: 0.3em 0.65em;
            border-radius: 999px;
        }

        /* ── Utilities ── */
        a { color: var(--fc-accent); }
        a:hover { color: var(--fc-accent-h); }

        hr { border-color: var(--fc-border); opacity: 1; }

        .text-muted { color: var(--fc-text-muted) !important; }

        @yield('css')
    </style>
</head>

<body>
    <div id="app">
        <nav class="public-navbar" id="publicNav">
            <div class="container position-relative">
                <a class="nav-brand"
                   href="{{ auth()->check() && Route::has('home') ? route('home') : url('/') }}">
                    <img src="{{ asset('pestaña.png') }}" alt="{{ config('app.name') }}">
                    <span>{{ config('app.name', 'Frangy Control') }}</span>
                </a>

                <button class="nav-toggler"
                        id="navToggler"
                        type="button"
                        aria-expanded="false"
                        aria-label="{{ __('Toggle navigation') }}">
                    <span class="hamburger">
                        <span></span><span></span><span></span>
                    </span>
                </button>

                <div class="nav-collapse" id="navCollapse">
                    <div class="nav-actions">
                        @guest
                            <a href="{{ route('landing.pages.welcome') }}" class="nav-link-item">Inicio</a>

                            @if (Route::has('login'))
                                <a href="{{ route('login') }}" class="nav-btn nav-btn-primary">
                                    <i class="fas fa-sign-in-alt fa-sm"></i>
                                    Acceder
                                </a>
                            @endif
                        @else
                            <span class="nav-user-name">
                                <i class="fas fa-user-circle fa-sm me-1"></i>
                                {{ Auth::user()->name }}
                            </span>

                            @if (Route::has('home'))
                                <a href="{{ route('home') }}" class="nav-link-item">
                                    <i class="fas fa-th-large fa-sm me-1"></i>
                                    Control
                                </a>
                            @endif

                            <form action="{{ route('logout') }}" method="POST" style="display:contents">
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

        <main class="app-main">
            @yield('content')
        </main>
    </div>

    <script>
        (function () {
            var nav      = document.getElementById('publicNav');
            var toggler  = document.getElementById('navToggler');
            var collapse = document.getElementById('navCollapse');

            /* scroll shadow */
            window.addEventListener('scroll', function () {
                nav.classList.toggle('scrolled', window.scrollY > 8);
            }, { passive: true });

            /* hamburger toggle */
            if (toggler && collapse) {
                toggler.addEventListener('click', function () {
                    var open = collapse.classList.toggle('open');
                    toggler.setAttribute('aria-expanded', open ? 'true' : 'false');
                });

                /* close on outside click */
                document.addEventListener('click', function (e) {
                    if (!nav.contains(e.target)) {
                        collapse.classList.remove('open');
                        toggler.setAttribute('aria-expanded', 'false');
                    }
                });
            }
        })();
    </script>
    @yield('js')
</body>
</html>
