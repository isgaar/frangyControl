@extends('layouts.dashboard')

@section('title', 'Panel de control')

@php
    $pageTitle = 'Panel de control';
    $pageSubtitle = 'Resumen operativo del taller, accesos rápidos y órdenes recientes.';
    $breadcrumbs = [
        ['label' => 'Panel'],
    ];
@endphp

@section('content')
    <div class="resource-page">
        <section class="resource-hero">
            <div class="resource-hero__top">
                <div class="resource-hero__copy">
                    <span class="resource-hero__eyebrow">Vista general</span>
                    <h1 class="resource-hero__title">Operación del taller</h1>
                    <p>Consulta métricas clave, revisa órdenes recientes y entra rápido a los módulos más usados.</p>
                </div>

                <div class="resource-hero__actions">
                    @foreach ($quickActions as $action)
                        <a href="{{ route($action['route']) }}" class="btn {{ $loop->first ? 'btn-primary' : 'btn-outline-light' }}">
                            <i class="{{ $action['icon'] }} me-1"></i> {{ $action['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="resource-metrics">
                @foreach ($stats as $stat)
                    <article class="resource-metric">
                        <span class="resource-metric__label">
                            <i class="{{ $stat['icon'] }} me-1 text-{{ $stat['accent'] }}"></i>
                            {{ $stat['label'] }}
                        </span>
                        <p class="resource-metric__value">{{ $stat['value'] }}</p>
                        <p class="resource-metric__copy">{{ $stat['caption'] }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        @if ($operationalAlerts->isNotEmpty())
            <section class="resource-panel">
                <div class="resource-panel__header">
                    <div>
                        <span class="resource-panel__eyebrow">Atención</span>
                        <h2 class="resource-panel__title">Alertas operativas</h2>
                        <p class="resource-panel__copy">Puntos que conviene revisar antes de continuar con el trabajo diario.</p>
                    </div>
                </div>

                <div class="resource-card-grid mt-4">
                    @foreach ($operationalAlerts as $alert)
                        <article class="alert alert-{{ $alert['tone'] }} mb-0">
                            <strong>{{ $alert['title'] }}: {{ $alert['count'] }}</strong>
                            <p class="mb-0 mt-1">{{ $alert['message'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        <section class="resource-panel">
            <div class="resource-panel__header">
                <div>
                    <span class="resource-panel__eyebrow">Órdenes</span>
                    <h2 class="resource-panel__title">Listado operativo</h2>
                    <p class="resource-panel__copy">Filtra por cliente, vehículo, placas, servicio, responsable o folio.</p>
                </div>
            </div>

            <form action="{{ route('panel.index') }}" method="get" class="resource-toolbar mt-4">
                <div class="resource-toolbar__field">
                    <label for="search" class="form-label">Buscar</label>
                    <input id="search" type="text" name="search" class="form-control" value="{{ $search }}"
                        placeholder="Cliente, placas, servicio o folio">
                </div>

                <div class="resource-toolbar__field">
                    <label for="limit" class="form-label">Mostrar</label>
                    <select id="limit" name="limit" class="form-select">
                        @foreach ([5, 10, 15, 25] as $option)
                            <option value="{{ $option }}" @selected((int) $limit === $option)>{{ $option }} registros</option>
                        @endforeach
                    </select>
                </div>

                <div class="resource-toolbar__field">
                    <label for="order" class="form-label">Orden</label>
                    <select id="order" name="order" class="form-select">
                        <option value="desc" @selected($order === 'desc')>Más recientes</option>
                        <option value="asc" @selected($order === 'asc')>Más antiguas</option>
                    </select>
                </div>

                <div class="resource-toolbar__actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i> Aplicar
                    </button>
                    <a href="{{ route('panel.index') }}" class="btn btn-outline-dark">
                        <i class="fas fa-undo-alt me-1"></i> Limpiar
                    </a>
                </div>
            </form>

            <div class="resource-table-wrap mt-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Folio</th>
                                <th>Cliente</th>
                                <th>Vehículo</th>
                                <th>Servicio</th>
                                <th>Estado</th>
                                <th class="text-end">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($ordenes as $orden)
                                <tr>
                                    <td>#{{ $orden->id_ordenes }}</td>
                                    <td>{{ optional($orden->cliente)->nombreCompleto ?? 'Sin cliente' }}</td>
                                    <td>
                                        {{ optional($orden->vehiculo)->marca ?? 'Sin vehículo' }}
                                        @if (!empty($orden->placas))
                                            <span class="d-block text-muted">{{ $orden->placas }}</span>
                                        @endif
                                    </td>
                                    <td>{{ optional($orden->servicio)->nombreServicio ?? 'Sin servicio' }}</td>
                                    <td>
                                        <span class="badge text-bg-secondary">{{ $orden->status ?? 'Sin estado' }}</span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('ordenes.show', $orden->id_ordenes) }}" class="btn btn-outline-dark btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="resource-empty mb-0">No hay órdenes para mostrar con los filtros actuales.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center flex-wrap mt-4" style="gap: .75rem;">
                <p class="mb-0 text-muted">Mostrando {{ $ordenes->count() }} orden(es) en esta página.</p>
                {{ $ordenes->links('pagination::bootstrap-5') }}
            </div>
        </section>

        <div class="resource-catalog-grid">
            <section class="resource-overview-card">
                <span class="resource-panel__eyebrow">Actividad</span>
                <h2 class="resource-overview-card__title">Órdenes recientes</h2>
                <div class="home-list mt-3">
                    @forelse ($recentOrders as $order)
                        <a class="home-list__item" href="{{ $order['url'] }}">
                            <div class="home-list__row">
                                <p class="home-list__title">#{{ $order['id'] }} · {{ $order['cliente'] }}</p>
                                <span class="badge text-bg-info">{{ $order['status'] }}</span>
                            </div>
                            <p class="home-list__subtitle">{{ $order['servicio'] }} · {{ $order['created_at'] }}</p>
                        </a>
                    @empty
                        <div class="resource-empty">Sin órdenes recientes.</div>
                    @endforelse
                </div>
            </section>

            <section class="resource-overview-card">
                <span class="resource-panel__eyebrow">Clientes</span>
                <h2 class="resource-overview-card__title">Últimos registros</h2>
                <div class="home-list mt-3">
                    @forelse ($recentClients as $client)
                        <div class="home-list__item">
                            <p class="home-list__title">{{ $client->nombreCompleto }}</p>
                            <p class="home-list__subtitle">{{ $client->telefono }} · {{ $client->correo }}</p>
                        </div>
                    @empty
                        <div class="resource-empty">Sin clientes recientes.</div>
                    @endforelse
                </div>
            </section>

            <section class="resource-overview-card">
                <span class="resource-panel__eyebrow">Mensajes</span>
                <h2 class="resource-overview-card__title">Lectura rápida</h2>
                <div class="home-list mt-3">
                    @foreach ($operationalMessages as $message)
                        <article class="home-list__item">
                            <div class="home-list__row">
                                <p class="home-list__title">{{ $message['title'] }}</p>
                                <span class="badge text-bg-{{ $message['tone'] }}">{{ $message['eyebrow'] }}</span>
                            </div>
                            <p class="home-list__subtitle">{{ $message['message'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>
        </div>
    </div>
@endsection
