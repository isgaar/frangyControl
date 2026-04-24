@props([
    'title' => null,
    'brand' => [],
])

@php
    $user = auth()->user();
    $initials = collect(explode(' ', trim($user->name ?? 'US')))
        ->filter()
        ->take(2)
        ->map(fn ($chunk) => mb_strtoupper(mb_substr($chunk, 0, 1)))
        ->implode('');
    $roleLabel = method_exists($user, 'getRoleNames')
        ? $user->getRoleNames()->implode(', ')
        : null;
@endphp

<header class="dashboard-navbar">
    <div class="dashboard-navbar__left">
        <button class="dashboard-navbar__toggle" type="button" data-dashboard-toggle aria-label="Abrir navegación lateral">
            <i class="fas fa-bars"></i>
        </button>

        <div class="dashboard-navbar__heading">
            <span class="dashboard-navbar__eyebrow">Panel administrativo</span>
            <strong class="dashboard-navbar__title">{{ $title ?: ($brand['name'] ?? config('app.name', 'Frangy Control')) }}</strong>
        </div>
    </div>

    <div class="dashboard-navbar__right">
        <div class="dashboard-navbar__meta">
            <span class="dashboard-navbar__meta-label">Actualizado</span>
            <strong>{{ now()->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</strong>
        </div>

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
</header>
