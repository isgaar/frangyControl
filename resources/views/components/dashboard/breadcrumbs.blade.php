@props([
    'items' => [],
])

<nav class="dashboard-breadcrumbs" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        @foreach ($items as $item)
            @if (!empty($item['url']))
                <li class="breadcrumb-item">
                    <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                </li>
            @else
                <li class="breadcrumb-item active" aria-current="page">{{ $item['label'] }}</li>
            @endif
        @endforeach
    </ol>
</nav>
