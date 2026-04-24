@extends('layouts.dashboard')

@section('title', 'Inicio')

@php
    $currentHour = (int) now()->timezone(config('app.timezone'))->format('H');
    $timeOfDay = $currentHour >= 5 && $currentHour < 12 ? 'morning' : ($currentHour >= 12 && $currentHour < 18 ? 'afternoon' : 'evening');
    $greetingMessage = $timeOfDay === 'morning' ? 'Buenos días' : ($timeOfDay === 'afternoon' ? 'Buenas tardes' : 'Buenas noches');
    $greetingIcon = $timeOfDay === 'evening' ? asset('moon.svg') : asset('sun.svg');
    $username = auth()->user()->name ?? 'Usuario';
    $pageTitle = 'Inicio';
    $pageSubtitle = 'Resumen operativo del taller con alertas, búsqueda rápida y actividad reciente.';
    $breadcrumbs = [
        ['label' => 'Panel', 'url' => route('home')],
        ['label' => 'Inicio'],
    ];

    $statusTones = [
        'cancelada' => 'danger',
        'finalizada' => 'success',
        'en proceso' => 'info',
    ];
    $canViewVehicles = auth()->user() && method_exists(auth()->user(), 'can')
        ? auth()->user()->can('admin.datosv.vehiculosnom')
        : false;
@endphp

@section('content')
<div class="home-grid">
    <section class="home-hero">
        <div class="home-hero__top">
            <div class="home-hero__copy">
                <span class="home-card__eyebrow">Operación diaria</span>
                <h2 class="home-hero__title">{{ $greetingMessage }}, {{ $username }}</h2>
                <p class="mb-0">
                    Aquí tienes una vista clara del estado del taller, accesos rápidos y los registros que más
                    probablemente necesitarás revisar hoy.
                </p>
            </div>

            <div>
                <img src="{{ $greetingIcon }}" alt="Estado del día" class="home-hero__icon">
                <div class="home-hero__meta mt-3">
                    <i class="far fa-clock"></i>
                    <span>{{ now()->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>

        <form action="{{ route('home') }}" method="GET" class="home-search">
            <div class="home-search__field">
                <label for="search">Búsqueda global</label>
                <input
                    id="search"
                    name="search"
                    type="text"
                    value="{{ $search }}"
                    placeholder="Orden, cliente, placas, vehículo o encargado">
            </div>

            <div class="home-search__field">
                <label for="limit">Resultados</label>
                <select id="limit" name="limit">
                    @foreach ([5, 10, 15, 20] as $option)
                        <option value="{{ $option }}" {{ (int) $limit === $option ? 'selected' : '' }}>{{ $option }}</option>
                    @endforeach
                </select>
            </div>

            <div class="home-search__field">
                <label for="order">Orden</label>
                <select id="order" name="order">
                    <option value="desc" {{ $order === 'desc' ? 'selected' : '' }}>Más recientes</option>
                    <option value="asc" {{ $order === 'asc' ? 'selected' : '' }}>Más antiguas</option>
                </select>
            </div>

            <div class="home-search__actions">
                <button type="submit" class="home-search__btn">Buscar</button>

                @if ($search !== '' || (int) $limit !== 5 || $order !== 'desc')
                    <a href="{{ route('home') }}" class="home-search__btn home-search__btn--ghost">Limpiar</a>
                @endif
            </div>
        </form>

        @if ($quickActions->isNotEmpty())
            <div class="home-quick-actions">
                @foreach ($quickActions as $action)
                    <a href="{{ route($action['route']) }}">
                        <i class="{{ $action['icon'] }} mr-2"></i>
                        {{ $action['label'] }}
                    </a>
                @endforeach
            </div>
        @endif
    </section>

    <section class="home-stats">
        @foreach ($stats as $stat)
            <a href="{{ $stat['url'] }}" class="home-stat">
                <div class="home-stat__top">
                    <span class="home-card__eyebrow">{{ $stat['label'] }}</span>
                    <span class="home-stat__icon is-{{ $stat['accent'] }}">
                        <i class="{{ $stat['icon'] }}"></i>
                    </span>
                </div>
                <div class="home-stat__value">{{ number_format($stat['value']) }}</div>
                <p class="home-stat__caption">{{ $stat['caption'] }}</p>
            </a>
        @endforeach
    </section>

    <section class="home-insights">
        <article class="home-card">
            <div class="home-card__header">
                <div>
                    <span class="home-card__eyebrow">Alertas</span>
                    <h3 class="home-card__title">Atención inmediata</h3>
                </div>
                <span class="dashboard-badge is-warning">{{ $operationalAlerts->count() }} activas</span>
            </div>

            <div class="home-list">
                @forelse ($operationalAlerts as $alert)
                    <div class="home-list__item">
                        <div class="home-list__row">
                            <p class="home-list__title">{{ $alert['title'] }}</p>
                            <span class="dashboard-badge is-{{ $alert['tone'] }}">{{ $alert['count'] }}</span>
                        </div>
                        <p class="home-list__subtitle">{{ $alert['message'] }}</p>
                    </div>
                @empty
                    <p class="dashboard-empty">No hay alertas operativas urgentes en este momento.</p>
                @endforelse
            </div>
        </article>

        <article class="home-card">
            <div class="home-card__header">
                <div>
                    <span class="home-card__eyebrow">Contexto</span>
                    <h3 class="home-card__title">Recomendaciones del día</h3>
                </div>
                <span class="dashboard-badge is-info">{{ $dashboardProfile['primary_role'] }}</span>
            </div>

            <div class="home-list">
                @foreach ($operationalMessages as $message)
                    <div class="home-list__item">
                        <div class="home-list__row">
                            <p class="home-list__title">{{ $message['title'] }}</p>
                            <span class="dashboard-badge is-{{ $message['tone'] }}">{{ $message['eyebrow'] }}</span>
                        </div>
                        <p class="home-list__subtitle">{{ $message['message'] }}</p>
                    </div>
                @endforeach
            </div>
        </article>
    </section>

    <section class="dashboard-grid dashboard-grid--3 home-recent">
        <article class="home-card">
            <div class="home-card__header">
                <div>
                    <span class="home-card__eyebrow">Actividad reciente</span>
                    <h3 class="home-card__title">Órdenes guardadas</h3>
                </div>
                <a href="{{ route('ordenes.index') }}" class="home-card__link">Ver todas</a>
            </div>

            <div class="home-list">
                @forelse ($recentOrders as $recentOrder)
                    <a href="{{ $recentOrder['url'] }}" class="home-list__item">
                        <div class="home-list__row">
                            <p class="home-list__title">Orden #{{ $recentOrder['id'] }}</p>
                            <span class="dashboard-badge is-{{ $statusTones[$recentOrder['status']] ?? 'warning' }}">
                                {{ ucwords($recentOrder['status']) }}
                            </span>
                        </div>
                        <p class="home-list__meta">{{ $recentOrder['created_at'] }}</p>
                        <p class="home-list__subtitle">{{ $recentOrder['cliente'] }} · {{ $recentOrder['vehiculo'] }}</p>
                        <p class="home-list__meta">{{ $recentOrder['servicio'] }}</p>
                    </a>
                @empty
                    <p class="dashboard-empty">Todavía no hay órdenes registradas.</p>
                @endforelse
            </div>
        </article>

        <article class="home-card">
            <div class="home-card__header">
                <div>
                    <span class="home-card__eyebrow">Actividad reciente</span>
                    <h3 class="home-card__title">Clientes guardados</h3>
                </div>
                <a href="{{ route('clientes.index') }}" class="home-card__link">Ver clientes</a>
            </div>

            <div class="home-list">
                @forelse ($recentClients as $client)
                    <a href="{{ route('clientes.show', $client->id_cliente) }}" class="home-list__item">
                        <div class="home-list__row">
                            <p class="home-list__title">{{ $client->nombreCompleto }}</p>
                            <span class="dashboard-badge is-info">{{ optional($client->created_at)->format('d/m/Y') }}</span>
                        </div>
                        <p class="home-list__meta">{{ optional($client->created_at)->format('H:i') ?: 'Sin hora' }}</p>
                        <p class="home-list__subtitle">{{ $client->telefono ?: 'Sin teléfono' }}</p>
                        <p class="home-list__meta">{{ $client->correo ?: 'Sin correo' }}</p>
                    </a>
                @empty
                    <p class="dashboard-empty">Todavía no hay clientes registrados.</p>
                @endforelse
            </div>
        </article>

        @if ($canViewVehicles)
            <article class="home-card">
                <div class="home-card__header">
                    <div>
                        <span class="home-card__eyebrow">Actividad reciente</span>
                        <h3 class="home-card__title">Vehículos guardados</h3>
                    </div>
                    <a href="{{ route('datosv.index') }}" class="home-card__link">Ver catálogo</a>
                </div>

                <div class="home-list">
                    @forelse ($recentVehicles as $vehicle)
                        <a href="{{ route('datosv.index') }}" class="home-list__item">
                            <div class="home-list__row">
                                <p class="home-list__title">{{ $vehicle->marca }}</p>
                                <span class="dashboard-badge is-success">{{ optional($vehicle->created_at)->format('d/m/Y') }}</span>
                            </div>
                            <p class="home-list__meta">{{ optional($vehicle->created_at)->format('H:i') ?: 'Sin hora' }}</p>
                            <p class="home-list__subtitle">Registro #{{ $vehicle->id_vehiculo }}</p>
                            <p class="home-list__meta">Marca disponible en el catálogo</p>
                        </a>
                    @empty
                        <p class="dashboard-empty">Todavía no hay vehículos registrados.</p>
                    @endforelse
                </div>
            </article>
        @endif
    </section>

    <section class="home-table">
        <div class="home-table__header">
            <div>
                <span class="home-card__eyebrow">Resultados</span>
                <h3 class="home-table__title">Últimas órdenes</h3>
            </div>
            <a href="{{ route('ordenes.index') }}" class="home-card__link">Ir al módulo completo</a>
        </div>

        @if ($search !== '')
            <p class="home-table__meta">
                Mostrando {{ $ordenes->total() }} coincidencias para <strong>"{{ $search }}"</strong>.
            </p>
        @endif

        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Estado</th>
                        <th>Cliente</th>
                        <th>Servicio</th>
                        <th>Vehículo</th>
                        <th>Encargado/a</th>
                        <th>Entrega</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ordenes as $row)
                        <tr>
                            <td>{{ $row->id_ordenes }}</td>
                            <td>
                                <span class="dashboard-badge is-{{ $statusTones[$row->status] ?? 'warning' }}">
                                    {{ ucwords($row->status) }}
                                </span>
                            </td>
                            <td>{{ ucwords(optional($row->cliente)->nombreCompleto ?? 'Sin cliente') }}</td>
                            <td>{{ optional($row->servicio)->nombreServicio ?? 'Sin servicio' }}</td>
                            <td>{{ optional($row->vehiculo)->marca ?? 'Sin vehículo' }}</td>
                            <td>{{ optional($row->user)->name ?? 'Sin asignar' }}</td>
                            <td>{{ $row->fechaEntrega ? \Illuminate\Support\Carbon::parse($row->fechaEntrega)->format('d/m/Y') : 'Sin fecha' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No hay órdenes para mostrar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <p class="home-table__meta">Mostrando {{ $ordenes->count() }} resultados en la página actual.</p>

        @if ($ordenes->hasPages())
            <div class="mt-3">
                {{ $ordenes->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </section>
</div>
@endsection
