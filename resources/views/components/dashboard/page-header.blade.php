@props([
    'title' => null,
    'subtitle' => null,
    'breadcrumbs' => [],
])

<section class="dashboard-page-header">
    <div class="dashboard-page-header__content">
        @if (!empty($breadcrumbs))
            <x-dashboard.breadcrumbs :items="$breadcrumbs" />
        @endif

        <span class="dashboard-page-header__eyebrow">Vista activa</span>
        <h1>{{ $title }}</h1>

        @if ($subtitle)
            <p>{{ $subtitle }}</p>
        @endif
    </div>
</section>
