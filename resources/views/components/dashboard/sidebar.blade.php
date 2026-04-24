@props([
    'menu' => [],
    'brand' => [],
])

@php
    $homeRoute = $brand['home_route'] ?? null;
    $homeUrl = $homeRoute && Route::has($homeRoute) ? route($homeRoute) : url('/');
    $logo = $brand['logo'] ?? null;
@endphp

<aside class="dashboard-sidebar" data-dashboard-sidebar>
    <div class="dashboard-sidebar__brand">
        <a href="{{ $homeUrl }}" class="dashboard-sidebar__brand-link">
            @if ($logo)
                <img src="{{ asset($logo) }}" alt="{{ $brand['name'] ?? config('app.name', 'Frangy Control') }}">
            @endif

            <span>
                <strong>{{ $brand['name'] ?? config('app.name', 'Frangy Control') }}</strong>
                <small>{{ $brand['tagline'] ?? 'Control operativo del taller' }}</small>
            </span>
        </a>

        <button class="dashboard-sidebar__close" type="button" data-dashboard-close aria-label="Cerrar navegación lateral">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="dashboard-sidebar__body">
        @foreach ($menu as $section)
            <section class="dashboard-sidebar__section">
                <p class="dashboard-sidebar__section-label">{{ $section['label'] }}</p>

                <nav class="dashboard-sidebar__nav" aria-label="{{ $section['label'] }}">
                    @foreach ($section['items'] as $item)
                        <a href="{{ $item['url'] }}" class="dashboard-sidebar__link {{ $item['active'] ? 'is-active' : '' }}">
                            <span class="dashboard-sidebar__icon">
                                <i class="{{ $item['icon'] }}"></i>
                            </span>

                            <span class="dashboard-sidebar__copy">
                                <strong>{{ $item['label'] }}</strong>
                                @if (!empty($item['description']))
                                    <small>{{ $item['description'] }}</small>
                                @endif
                            </span>
                        </a>
                    @endforeach
                </nav>
            </section>
        @endforeach
    </div>
</aside>
