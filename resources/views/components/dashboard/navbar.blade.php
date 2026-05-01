@props([
    'title' => null,
    'brand' => [],
])

@php
    $user = auth()->user();
    $appName = $brand['name'] ?? config('app.name', 'Frangy Control');
    $initials = collect(explode(' ', trim($user->name ?? 'US')))
        ->filter()
        ->take(2)
        ->map(fn ($chunk) => mb_strtoupper(mb_substr($chunk, 0, 1)))
        ->implode('');
    $roleLabel = method_exists($user, 'getRoleNames')
        ? $user->getRoleNames()->implode(', ')
        : null;
    $pageLabel = $title ?: $appName;
    $updatedAt = now()->timezone(config('app.timezone'))->format('d/m/Y H:i');
@endphp

<header class="dashboard-navbar">
    <div class="dashboard-navbar__shell">
        <div class="dashboard-navbar__left">
            <button class="dashboard-navbar__toggle" type="button" data-dashboard-toggle aria-label="Abrir navegación lateral">
                <i class="fas fa-bars"></i>
            </button>

            <div class="dashboard-navbar__heading">
                <span class="dashboard-navbar__eyebrow">Panel administrativo</span>
                <div class="dashboard-navbar__title-row">
                    <strong class="dashboard-navbar__title">{{ $pageLabel }}</strong>
                    <span class="dashboard-navbar__divider" aria-hidden="true"></span>
                    <span class="dashboard-navbar__brand">{{ $appName }}</span>
                </div>
            </div>
        </div>

        <div class="dashboard-navbar__right">
            <div class="dashboard-navbar__meta">
                <span class="dashboard-navbar__meta-label">Última actualización</span>
                <strong>{{ $updatedAt }}</strong>
            </div>

            <button class="dashboard-theme-toggle" type="button" data-theme-toggle aria-label="Activar modo oscuro">
                <i class="fas fa-moon" data-theme-icon></i>
                <span class="dashboard-theme-toggle__label" data-theme-text>Modo oscuro</span>
            </button>

            <details class="dashboard-user-menu">
                <summary class="dashboard-user-menu__summary">
                    <span class="dashboard-user-menu__avatar">{{ $initials ?: 'US' }}</span>
                    <span class="dashboard-user-menu__copy">
                        <strong>{{ $user->name ?? 'Usuario' }}</strong>
                        <small>{{ $roleLabel ?: 'Acceso autenticado' }}</small>
                    </span>
                    <i class="fas fa-chevron-down"></i>
                </summary>

                <div class="dashboard-user-menu__dropdown">
                    @if (Route::has('home'))
                        <a href="{{ route('home') }}">Inicio</a>
                    @endif

                    @if (Route::has('acerca'))
                        <a href="{{ route('acerca') }}">Acerca del proyecto</a>
                    @endif

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit">Cerrar sesión</button>
                    </form>
                </div>
            </details>
        </div>
    </div>
</header>
