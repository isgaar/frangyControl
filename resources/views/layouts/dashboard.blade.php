@php
    $resolvedPageTitle    = $pageTitle    ?? trim($__env->yieldContent('title', config('app.name', 'Frangy Control')));
    $resolvedPageSubtitle = $pageSubtitle ?? null;
    $resolvedBreadcrumbs  = $breadcrumbs  ?? [];
    $brand                = config('dashboard.brand', []);
    $menu                 = app(\App\Support\DashboardMenu::class)->for(auth()->user(), request());
    $hasLegacyHeader      = trim($__env->yieldContent('content_header')) !== '';
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
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    @vite(['resources/css/dashboard.css', 'resources/js/dashboard.js'])
    @yield('css')

    <style>
        /* ── Design Tokens ── */
        :root {
            --db-bg:            #070e1b;
            --db-sidebar-w:     260px;
            --db-sidebar-bg:    #0c1627;
            --db-sidebar-border: rgba(255,255,255,0.07);
            --db-navbar-h:      58px;
            --db-surface:       #111c30;
            --db-surface-2:     #162035;
            --db-border:        rgba(255,255,255,0.08);
            --db-border-h:      rgba(255,255,255,0.18);
            --db-text:          #dde5f3;
            --db-text-muted:    rgba(221,229,243,0.5);
            --db-accent:        #3d8bff;
            --db-accent-h:      #5fa0ff;
            --db-accent-dim:    rgba(61,139,255,0.12);
            --db-success:       #34c759;
            --db-danger:        #e25f5f;
            --db-warning:       #ffb800;
            --db-radius:        10px;
            --db-radius-lg:     14px;
            --db-transition:    0.2s cubic-bezier(0.4,0,0.2,1);
            --db-shadow:        0 2px 16px rgba(0,0,0,0.35);
        }

        *, *::before, *::after { box-sizing: border-box; }

        body.dashboard-body {
            margin: 0;
            min-height: 100vh;
            background: var(--db-bg);
            color: var(--db-text);
            font-family: 'Nunito', sans-serif;
            font-size: 15px;
            line-height: 1.65;
        }

        /* ── Layout shell ── */
        .dashboard-app {
            display: flex;
            min-height: 100vh;
        }

        /* ── Backdrop (mobile overlay) ── */
        .dashboard-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 199;
            background: rgba(0,0,0,0.55);
            backdrop-filter: blur(2px);
            -webkit-backdrop-filter: blur(2px);
            border: none;
            cursor: pointer;
            opacity: 0;
            transition: opacity var(--db-transition);
        }

        .sidebar-open .dashboard-backdrop {
            display: block;
            opacity: 1;
        }

        /* ── Sidebar ── */
        .dashboard-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: var(--db-sidebar-w);
            z-index: 200;
            display: flex;
            flex-direction: column;
            background: var(--db-sidebar-bg);
            border-right: 1px solid var(--db-sidebar-border);
            transition: transform var(--db-transition);
            overflow: hidden;
        }

        /* ── Sidebar brand ── */
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0 1.25rem;
            height: var(--db-navbar-h);
            border-bottom: 1px solid var(--db-sidebar-border);
            flex-shrink: 0;
            text-decoration: none;
        }

        .sidebar-brand img {
            height: 30px;
            width: auto;
            object-fit: contain;
        }

        .sidebar-brand-name {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            color: var(--db-text);
            letter-spacing: -0.01em;
        }

        /* ── Sidebar nav ── */
        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 1rem 0.75rem;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.08) transparent;
        }

        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }

        .sidebar-section-label {
            font-size: 0.65rem;
            font-weight: 800;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--db-text-muted);
            padding: 0 0.75rem;
            margin: 1.1rem 0 0.35rem;
        }

        .sidebar-section-label:first-child { margin-top: 0; }

        .sidebar-item {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            padding: 0.55rem 0.75rem;
            border-radius: var(--db-radius);
            color: var(--db-text-muted);
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            transition: color var(--db-transition), background var(--db-transition);
            position: relative;
            cursor: pointer;
        }

        .sidebar-item:hover {
            color: var(--db-text);
            background: rgba(255,255,255,0.05);
            text-decoration: none;
        }

        .sidebar-item.active {
            color: var(--db-accent);
            background: var(--db-accent-dim);
            font-weight: 700;
        }

        .sidebar-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 20%;
            bottom: 20%;
            width: 3px;
            background: var(--db-accent);
            border-radius: 0 3px 3px 0;
        }

        .sidebar-item-icon {
            width: 18px;
            text-align: center;
            flex-shrink: 0;
            font-size: 0.875rem;
        }

        .sidebar-item-badge {
            margin-left: auto;
            font-size: 0.65rem;
            font-weight: 800;
            padding: 0.15em 0.55em;
            border-radius: 999px;
            background: var(--db-accent-dim);
            color: var(--db-accent);
        }

        /* ── Sidebar user footer ── */
        .sidebar-user {
            padding: 0.85rem 1rem;
            border-top: 1px solid var(--db-sidebar-border);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-shrink: 0;
        }

        .sidebar-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--db-accent-dim);
            border: 1px solid rgba(61,139,255,0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            font-size: 0.75rem;
            color: var(--db-accent);
            flex-shrink: 0;
        }

        .sidebar-user-info { overflow: hidden; }

        .sidebar-user-name {
            font-size: 0.8125rem;
            font-weight: 700;
            color: var(--db-text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-user-role {
            font-size: 0.7rem;
            color: var(--db-text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-logout {
            margin-left: auto;
            flex-shrink: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--db-radius);
            background: transparent;
            border: 1px solid var(--db-border);
            color: var(--db-text-muted);
            cursor: pointer;
            transition: all var(--db-transition);
            font-size: 0.8rem;
        }

        .sidebar-logout:hover {
            background: rgba(226,95,95,0.12);
            border-color: rgba(226,95,95,0.4);
            color: var(--db-danger);
        }

        /* ── Main area ── */
        .dashboard-main {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            margin-left: var(--db-sidebar-w);
            transition: margin-left var(--db-transition);
        }

        /* ── Top navbar ── */
        .dashboard-navbar {
            position: sticky;
            top: 0;
            z-index: 100;
            height: var(--db-navbar-h);
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0 1.5rem;
            background: rgba(7,14,27,0.88);
            backdrop-filter: blur(14px) saturate(1.4);
            -webkit-backdrop-filter: blur(14px) saturate(1.4);
            border-bottom: 1px solid var(--db-border);
        }

        /* Mobile sidebar toggle in navbar */
        .dashboard-sidebar-toggle {
            display: none;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: var(--db-radius);
            background: var(--db-surface);
            border: 1px solid var(--db-border);
            color: var(--db-text-muted);
            cursor: pointer;
            flex-shrink: 0;
            font-size: 0.875rem;
            transition: all var(--db-transition);
        }

        .dashboard-sidebar-toggle:hover {
            background: rgba(255,255,255,0.08);
            color: var(--db-text);
            border-color: var(--db-border-h);
        }

        .navbar-page-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 0.9375rem;
            font-weight: 700;
            color: var(--db-text);
            letter-spacing: -0.01em;
            margin: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .navbar-end {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .navbar-icon-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: var(--db-radius);
            background: transparent;
            border: 1px solid var(--db-border);
            color: var(--db-text-muted);
            cursor: pointer;
            transition: all var(--db-transition);
            position: relative;
            font-size: 0.875rem;
        }

        .navbar-icon-btn:hover {
            background: var(--db-surface);
            color: var(--db-text);
            border-color: var(--db-border-h);
        }

        .navbar-badge {
            position: absolute;
            top: 5px;
            right: 5px;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--db-danger);
            border: 1.5px solid var(--db-bg);
        }

        .navbar-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--db-accent-dim);
            border: 1px solid rgba(61,139,255,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            font-size: 0.75rem;
            color: var(--db-accent);
            cursor: pointer;
            transition: border-color var(--db-transition);
        }

        .navbar-avatar:hover { border-color: var(--db-accent); }

        /* ── Content area ── */
        .dashboard-content {
            flex: 1;
            padding: 0;
        }

        /* ── Page header ── */
        .dashboard-page-header {
            padding: 1.5rem 1.75rem 0;
        }

        .page-header-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .page-title-block {}

        .page-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.375rem;
            font-weight: 700;
            color: var(--db-text);
            margin: 0 0 0.2rem;
            letter-spacing: -0.02em;
            line-height: 1.2;
        }

        .page-subtitle {
            font-size: 0.875rem;
            color: var(--db-text-muted);
            margin: 0;
        }

        /* ── Breadcrumbs ── */
        .db-breadcrumb {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.35rem;
            list-style: none;
            padding: 0;
            margin: 0 0 0.75rem;
            font-size: 0.78rem;
        }

        .db-breadcrumb-item { display: flex; align-items: center; gap: 0.35rem; color: var(--db-text-muted); }
        .db-breadcrumb-item + .db-breadcrumb-item::before { content: '/'; color: var(--db-border-h); }
        .db-breadcrumb-item a { color: var(--db-text-muted); text-decoration: none; }
        .db-breadcrumb-item a:hover { color: var(--db-accent); }
        .db-breadcrumb-item.active { color: var(--db-text); font-weight: 600; }

        /* ── Flash messages ── */
        .dashboard-flash {
            padding: 0 1.75rem;
            margin-top: 1.25rem;
        }

        /* ── Panel / main content wrapper ── */
        .dashboard-panel {
            padding: 1.5rem 1.75rem 2.5rem;
        }

        /* ── Legacy header ── */
        .dashboard-legacy-header {
            padding: 1rem 1.75rem;
            border-bottom: 1px solid var(--db-border);
        }

        /* ── Cards ── */
        .card, .db-card {
            background: var(--db-surface) !important;
            border: 1px solid var(--db-border) !important;
            border-radius: var(--db-radius-lg) !important;
            color: var(--db-text) !important;
            transition: border-color var(--db-transition), box-shadow var(--db-transition);
        }

        .card:hover, .db-card:hover {
            border-color: var(--db-border-h) !important;
            box-shadow: var(--db-shadow) !important;
        }

        .card-header {
            background: var(--db-surface-2) !important;
            border-bottom: 1px solid var(--db-border) !important;
            border-radius: calc(var(--db-radius-lg) - 1px) calc(var(--db-radius-lg) - 1px) 0 0 !important;
            padding: 0.9rem 1.25rem !important;
            font-weight: 700;
            font-size: 0.875rem;
            color: var(--db-text);
        }

        .card-body { padding: 1.25rem !important; }
        .card-footer {
            background: var(--db-surface-2) !important;
            border-top: 1px solid var(--db-border) !important;
        }

        /* ── Metric cards ── */
        .metric-card {
            background: var(--db-surface);
            border: 1px solid var(--db-border);
            border-radius: var(--db-radius-lg);
            padding: 1.25rem 1.4rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            transition: border-color var(--db-transition), transform var(--db-transition);
        }

        .metric-card:hover {
            border-color: var(--db-border-h);
            transform: translateY(-2px);
            box-shadow: var(--db-shadow);
        }

        .metric-label {
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: var(--db-text-muted);
        }

        .metric-value {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--db-text);
            line-height: 1;
        }

        .metric-delta {
            font-size: 0.78rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .metric-delta.up   { color: var(--db-success); }
        .metric-delta.down { color: var(--db-danger); }

        /* ── Forms ── */
        .form-label {
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: var(--db-text-muted);
            margin-bottom: 0.4rem;
            display: block;
        }

        .form-control,
        .form-select {
            background: rgba(255,255,255,0.04) !important;
            border: 1px solid var(--db-border) !important;
            border-radius: var(--db-radius) !important;
            color: var(--db-text) !important;
            font-family: 'Nunito', sans-serif;
            font-size: 0.9375rem;
            padding: 0.6rem 0.95rem !important;
            transition: border-color var(--db-transition), box-shadow var(--db-transition), background var(--db-transition);
        }

        .form-control:focus,
        .form-select:focus {
            background: rgba(255,255,255,0.06) !important;
            border-color: var(--db-accent) !important;
            box-shadow: 0 0 0 3px rgba(61,139,255,0.16) !important;
            outline: none !important;
            color: var(--db-text) !important;
        }

        .form-control::placeholder { color: rgba(221,229,243,0.28); }
        .form-select option { background: #111c30; color: var(--db-text); }

        .form-control.is-invalid,
        .form-select.is-invalid {
            border-color: var(--db-danger) !important;
            box-shadow: 0 0 0 3px rgba(226,95,95,0.14) !important;
        }

        .invalid-feedback { color: var(--db-danger); font-size: 0.78rem; margin-top: 0.25rem; }
        .valid-feedback   { color: var(--db-success); font-size: 0.78rem; margin-top: 0.25rem; }

        .form-check-input {
            background-color: rgba(255,255,255,0.05) !important;
            border-color: var(--db-border-h) !important;
        }

        .form-check-input:checked {
            background-color: var(--db-accent) !important;
            border-color: var(--db-accent) !important;
        }

        .form-check-label { color: var(--db-text); font-size: 0.9rem; }

        .input-group-text {
            background: var(--db-surface-2) !important;
            border-color: var(--db-border) !important;
            color: var(--db-text-muted) !important;
        }

        /* ── Buttons ── */
        .btn {
            font-family: 'Nunito', sans-serif;
            font-weight: 700;
            border-radius: var(--db-radius);
            letter-spacing: 0.01em;
            transition: all var(--db-transition);
        }

        .btn-primary {
            background: var(--db-accent) !important;
            border-color: var(--db-accent) !important;
            color: #fff !important;
        }

        .btn-primary:hover {
            background: var(--db-accent-h) !important;
            border-color: var(--db-accent-h) !important;
            box-shadow: 0 4px 16px rgba(61,139,255,0.3) !important;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: var(--db-surface-2) !important;
            border-color: var(--db-border-h) !important;
            color: var(--db-text-muted) !important;
        }

        .btn-secondary:hover {
            background: rgba(255,255,255,0.08) !important;
            color: var(--db-text) !important;
        }

        .btn-success { background: rgba(52,199,89,0.15) !important; border-color: rgba(52,199,89,0.4) !important; color: var(--db-success) !important; }
        .btn-danger  { background: rgba(226,95,95,0.12) !important; border-color: rgba(226,95,95,0.4) !important; color: var(--db-danger) !important; }

        .btn-outline-primary {
            background: transparent !important;
            border-color: var(--db-accent) !important;
            color: var(--db-accent) !important;
        }

        .btn-outline-primary:hover {
            background: var(--db-accent-dim) !important;
        }

        /* ── Tables ── */
        .table {
            color: var(--db-text);
            border-color: var(--db-border);
            margin: 0;
        }

        .table > thead > tr > th {
            background: var(--db-surface-2);
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: var(--db-text-muted);
            border-bottom: 1px solid var(--db-border-h);
            padding: 0.75rem 1rem;
            white-space: nowrap;
        }

        .table > tbody > tr > td {
            padding: 0.8rem 1rem;
            border-bottom: 1px solid var(--db-border);
            vertical-align: middle;
            font-size: 0.9rem;
        }

        .table > tbody > tr:last-child > td { border-bottom: none; }

        .table-hover > tbody > tr:hover > td {
            background: rgba(255,255,255,0.025);
        }

        .table-responsive { border-radius: var(--db-radius-lg); border: 1px solid var(--db-border); }

        /* ── Badges ── */
        .badge {
            font-weight: 700;
            font-size: 0.68rem;
            letter-spacing: 0.04em;
            padding: 0.3em 0.7em;
            border-radius: 999px;
        }

        .badge.bg-primary   { background: var(--db-accent-dim) !important; color: var(--db-accent); }
        .badge.bg-success   { background: rgba(52,199,89,0.15) !important; color: var(--db-success); }
        .badge.bg-danger    { background: rgba(226,95,95,0.12) !important; color: var(--db-danger); }
        .badge.bg-warning   { background: rgba(255,184,0,0.12) !important;  color: var(--db-warning); }
        .badge.bg-secondary { background: rgba(255,255,255,0.08) !important; color: var(--db-text-muted); }

        /* ── Alerts ── */
        .alert {
            border-radius: var(--db-radius);
            border-width: 1px;
            font-size: 0.9rem;
        }

        .alert-success { background: rgba(52,199,89,0.1);  border-color: rgba(52,199,89,0.3);  color: #7de3a0; }
        .alert-danger  { background: rgba(226,95,95,0.1);  border-color: rgba(226,95,95,0.3);  color: #f4a3a3; }
        .alert-warning { background: rgba(255,184,0,0.1);  border-color: rgba(255,184,0,0.3);  color: #ffd966; }
        .alert-info    { background: rgba(61,139,255,0.1); border-color: rgba(61,139,255,0.3); color: #85b8ff; }

        /* ── Modals ── */
        .modal-content {
            background: var(--db-surface) !important;
            border: 1px solid var(--db-border-h) !important;
            border-radius: var(--db-radius-lg) !important;
            color: var(--db-text) !important;
        }

        .modal-header {
            border-bottom-color: var(--db-border) !important;
            background: var(--db-surface-2) !important;
            border-radius: calc(var(--db-radius-lg) - 1px) calc(var(--db-radius-lg) - 1px) 0 0 !important;
            padding: 1rem 1.25rem !important;
        }

        .modal-title { font-family: 'Space Grotesk', sans-serif; font-weight: 700; }
        .modal-footer { border-top-color: var(--db-border) !important; background: var(--db-surface-2) !important; }
        .btn-close { filter: invert(1) opacity(0.6); }
        .modal-backdrop { --bs-backdrop-bg: #000; --bs-backdrop-opacity: 0.6; }

        /* ── Dropdowns ── */
        .dropdown-menu {
            background: var(--db-surface) !important;
            border: 1px solid var(--db-border-h) !important;
            border-radius: var(--db-radius-lg) !important;
            box-shadow: 0 8px 32px rgba(0,0,0,0.4) !important;
            padding: 0.5rem !important;
        }

        .dropdown-item {
            color: var(--db-text-muted) !important;
            font-size: 0.875rem;
            font-weight: 600;
            border-radius: var(--db-radius) !important;
            padding: 0.5rem 0.85rem !important;
            transition: background var(--db-transition), color var(--db-transition);
        }

        .dropdown-item:hover {
            background: rgba(255,255,255,0.06) !important;
            color: var(--db-text) !important;
        }

        .dropdown-divider { border-color: var(--db-border) !important; margin: 0.35rem 0 !important; }
        .dropdown-header { font-size: 0.68rem; font-weight: 800; letter-spacing: 0.07em; text-transform: uppercase; color: var(--db-text-muted) !important; padding: 0.5rem 0.85rem 0.25rem !important; }

        /* ── Tabs ── */
        .nav-tabs {
            border-bottom: 1px solid var(--db-border);
        }

        .nav-tabs .nav-link {
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--db-text-muted);
            border: none;
            border-bottom: 2px solid transparent;
            border-radius: 0;
            padding: 0.65rem 1rem;
            transition: color var(--db-transition), border-color var(--db-transition);
        }

        .nav-tabs .nav-link:hover { color: var(--db-text); border-bottom-color: var(--db-border-h); }

        .nav-tabs .nav-link.active {
            color: var(--db-accent) !important;
            background: transparent !important;
            border-bottom-color: var(--db-accent) !important;
        }

        /* ── Pagination ── */
        .page-link {
            background: var(--db-surface) !important;
            border-color: var(--db-border) !important;
            color: var(--db-text-muted) !important;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all var(--db-transition);
        }

        .page-link:hover {
            background: var(--db-surface-2) !important;
            color: var(--db-text) !important;
            border-color: var(--db-border-h) !important;
        }

        .page-item.active .page-link {
            background: var(--db-accent) !important;
            border-color: var(--db-accent) !important;
            color: #fff !important;
        }

        .page-item.disabled .page-link { opacity: 0.4; }

        /* ── Utilities ── */
        a { color: var(--db-accent); }
        a:hover { color: var(--db-accent-h); }
        hr { border-color: var(--db-border); opacity: 1; }
        .text-muted { color: var(--db-text-muted) !important; }
        .text-success { color: var(--db-success) !important; }
        .text-danger  { color: var(--db-danger) !important; }
        .text-warning { color: var(--db-warning) !important; }
        .text-primary { color: var(--db-accent) !important; }

        /* ── Responsive breakpoints ── */
        @media (max-width: 991.98px) {
            :root { --db-sidebar-w: 260px; }

            .dashboard-sidebar {
                transform: translateX(-100%);
                box-shadow: none;
            }

            .sidebar-open .dashboard-sidebar {
                transform: translateX(0);
                box-shadow: 8px 0 32px rgba(0,0,0,0.5);
            }

            .dashboard-main { margin-left: 0; }

            .dashboard-sidebar-toggle { display: flex; }
        }

        @media (max-width: 575.98px) {
            .dashboard-panel { padding: 1rem 1rem 2rem; }
            .dashboard-page-header { padding: 1.1rem 1rem 0; }
            .dashboard-flash { padding: 0 1rem; }
            .dashboard-navbar { padding: 0 1rem; }
            .navbar-page-title { font-size: 0.875rem; }
            .page-title { font-size: 1.125rem; }
        }
    </style>
</head>
<body class="dashboard-body">
    <div class="dashboard-app" id="dashboardApp" data-dashboard-app>
        <button class="dashboard-backdrop"
                type="button"
                id="dashboardBackdrop"
                data-dashboard-backdrop
                aria-label="Cerrar navegación lateral">
        </button>

        {{-- Sidebar --}}
        <aside class="dashboard-sidebar" id="dashboardSidebar" role="navigation" aria-label="Navegación principal">
            {{-- Brand --}}
            <a class="sidebar-brand" href="{{ Route::has('home') ? route('home') : url('/') }}">
                @if(!empty($brand['logo']))
                    <img src="{{ $brand['logo'] }}" alt="{{ $brand['name'] ?? config('app.name') }}">
                @else
                    <img src="{{ asset('pestaña.png') }}" alt="{{ config('app.name') }}">
                @endif
                <span class="sidebar-brand-name">{{ $brand['name'] ?? config('app.name', 'Frangy Control') }}</span>
            </a>

            {{-- Nav items --}}
            <nav class="sidebar-nav" aria-label="Menú principal">
                <x-dashboard.sidebar :menu="$menu" :brand="$brand" />
            </nav>

            {{-- User footer --}}
            @auth
            <div class="sidebar-user">
                <div class="sidebar-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="sidebar-user-info">
                    <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
                    <div class="sidebar-user-role">{{ auth()->user()->email }}</div>
                </div>
                <form action="{{ route('logout') }}" method="POST" style="display:contents">
                    @csrf
                    <button type="submit" class="sidebar-logout" title="Cerrar sesión">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
            @endauth
        </aside>

        {{-- Main --}}
        <div class="dashboard-main">
            {{-- Top navbar --}}
            <header class="dashboard-navbar" role="banner">
                <button class="dashboard-sidebar-toggle"
                        id="sidebarToggle"
                        type="button"
                        aria-label="Abrir menú"
                        aria-expanded="false"
                        aria-controls="dashboardSidebar">
                    <i class="fas fa-bars"></i>
                </button>

                @if ($resolvedPageTitle)
                    <h1 class="navbar-page-title">{{ $resolvedPageTitle }}</h1>
                @endif

                <div class="navbar-end">
                    {{-- Notifications button --}}
                    <button class="navbar-icon-btn" type="button" aria-label="Notificaciones">
                        <i class="fas fa-bell"></i>
                        <span class="navbar-badge"></span>
                    </button>

                    {{-- Navbar slot from component --}}
                    <x-dashboard.navbar :title="$resolvedPageTitle" :brand="$brand" />

                    {{-- User avatar / dropdown --}}
                    @auth
                    <div class="navbar-avatar" title="{{ auth()->user()->name }}">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    @endauth
                </div>
            </header>

            {{-- Content --}}
            <main class="dashboard-content" id="dashboardContent">
                @unless ($hasLegacyHeader)
                    <div class="dashboard-flash">
                        <x-dashboard.flash />
                    </div>
                @endunless

                @if ($hasLegacyHeader)
                    <section class="dashboard-legacy-header">
                        @yield('content_header')
                    </section>
                @elseif ($resolvedPageTitle || !empty($resolvedBreadcrumbs))
                    <div class="dashboard-page-header">
                        @if (!empty($resolvedBreadcrumbs))
                            <ol class="db-breadcrumb" aria-label="Ruta de navegación">
                                @foreach ($resolvedBreadcrumbs as $crumb)
                                    <li class="db-breadcrumb-item {{ $loop->last ? 'active' : '' }}"
                                        @if($loop->last) aria-current="page" @endif>
                                        @if (!$loop->last && isset($crumb['url']))
                                            <a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a>
                                        @else
                                            {{ $crumb['label'] ?? $crumb }}
                                        @endif
                                    </li>
                                @endforeach
                            </ol>
                        @endif

                        <div class="page-header-row">
                            <div class="page-title-block">
                                <h1 class="page-title">{{ $resolvedPageTitle }}</h1>
                                @if ($resolvedPageSubtitle)
                                    <p class="page-subtitle">{{ $resolvedPageSubtitle }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <section class="dashboard-panel">
                    @yield('content')
                </section>
            </main>
        </div>
    </div>

    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <script>
        (function () {
            var app      = document.getElementById('dashboardApp');
            var toggle   = document.getElementById('sidebarToggle');
            var backdrop = document.getElementById('dashboardBackdrop');

            function openSidebar() {
                app.classList.add('sidebar-open');
                if (toggle) toggle.setAttribute('aria-expanded', 'true');
            }

            function closeSidebar() {
                app.classList.remove('sidebar-open');
                if (toggle) toggle.setAttribute('aria-expanded', 'false');
            }

            if (toggle) {
                toggle.addEventListener('click', function () {
                    app.classList.contains('sidebar-open') ? closeSidebar() : openSidebar();
                });
            }

            if (backdrop) {
                backdrop.addEventListener('click', closeSidebar);
            }

            /* Close on Escape */
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') closeSidebar();
            });

            /* Close sidebar when a nav link is tapped on mobile */
            document.querySelectorAll('#dashboardSidebar .sidebar-item').forEach(function (link) {
                link.addEventListener('click', function () {
                    if (window.innerWidth < 992) closeSidebar();
                });
            });
        })();
    </script>

    @yield('js')
</body>
</html>
