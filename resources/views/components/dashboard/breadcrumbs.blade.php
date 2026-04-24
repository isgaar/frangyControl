@props([
    'items' => [],
])

<nav class="dashboard-breadcrumbs" aria-label="Breadcrumb">
    @foreach ($items as $item)
        @if (!empty($item['url']))
            <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
        @else
            <span class="is-current">{{ $item['label'] }}</span>
        @endif
    @endforeach
</nav>
