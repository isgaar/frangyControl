@extends('layouts.dashboard')

@section('title', 'Órdenes')

@section('content_header')
    @if (Session::has('status'))
        <div class="col-md-12 alert-section">
            <div class="alert alert-{{ Session::get('status_type') }} dashboard-legacy-alert">
                <span class="dashboard-legacy-alert__text">
                    {{ Session::get('status') }}
                    @php Session::forget('status'); @endphp
                </span>
            </div>
        </div>
    @endif
@stop

@section('content')
    @php
        $collection = $ordenes->getCollection();
        $statusCounts = [
            'en proceso' => $collection->where('status', 'en proceso')->count(),
            'finalizada'  => $collection->where('status', 'finalizada')->count(),
            'cancelada'   => $collection->where('status', 'cancelada')->count(),
        ];
    @endphp

    <div class="resource-page">

        {{-- Encabezado compacto --}}
        <div class="page-header">
            <div>
                <span class="page-eyebrow">Seguimiento del taller</span>
                <h1 class="page-title">Órdenes activas y registradas</h1>
            </div>
            <a href="{{ route('ordenes.registro') }}" class="btn btn-primary" style="font-weight:800;">
                <i class="fas fa-clipboard-list me-1"></i> Nueva orden
            </a>
        </div>

        {{-- Métricas compactas --}}
        <div class="metrics-grid">
            <article class="metric-card">
                <span class="metric-card__label">Resultados</span>
                <p class="metric-card__value">{{ $ordenes->total() }}</p>
                <p class="metric-card__copy">Con el filtro actual</p>
            </article>
            <article class="metric-card">
                <span class="metric-card__label">En proceso</span>
                <p class="metric-card__value">{{ $statusCounts['en proceso'] }}</p>
                <p class="metric-card__copy">Abiertas en esta página</p>
            </article>
            <article class="metric-card">
                <span class="metric-card__label">Finalizadas</span>
                <p class="metric-card__value">{{ $statusCounts['finalizada'] }}</p>
                <p class="metric-card__copy">Listas para consulta</p>
            </article>
            <article class="metric-card">
                <span class="metric-card__label">Vista actual</span>
                <p class="metric-card__value">{{ $ordenes->isEmpty() ? 0 : $ordenes->currentPage() }}</p>
                <p class="metric-card__copy">Página {{ $ordenes->isEmpty() ? 0 : $ordenes->currentPage() }} de {{ $ordenes->lastPage() }}</p>
            </article>
        </div>

        {{-- Panel unificado: filtros + listado --}}
        <section class="resource-panel">

            {{-- Barra de filtros --}}
            <form action="{{ route('ordenes.index') }}" method="get">
                <div class="filter-toolbar">

                    {{-- Búsqueda con lupa integrada --}}
                    <div class="filter-search-box">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por orden, cliente, placas, servicio, encargado o modelo...">
                        <button type="submit" title="Buscar">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                        </button>
                    </div>

                    <select name="status" class="form-control">
                        <option value="">Todos los estados</option>
                        <option value="en proceso" {{ $status === 'en proceso' ? 'selected' : '' }}>En proceso</option>
                        <option value="cancelada"  {{ $status === 'cancelada'  ? 'selected' : '' }}>Cancelada</option>
                        <option value="finalizada" {{ $status === 'finalizada' ? 'selected' : '' }}>Finalizada</option>
                    </select>

                    <select name="order" class="form-control">
                        <option value="desc" {{ $order === 'desc' ? 'selected' : '' }}>Últimos agregados</option>
                        <option value="asc"  {{ $order === 'asc'  ? 'selected' : '' }}>Primeros registros</option>
                    </select>

                    <select name="limit" class="form-control">
                        <option value="5"  {{ (string) $limit === '5'  ? 'selected' : '' }}>5 por página</option>
                        <option value="10" {{ (string) $limit === '10' ? 'selected' : '' }}>10 por página</option>
                        <option value="15" {{ (string) $limit === '15' ? 'selected' : '' }}>15 por página</option>
                    </select>

                    <a href="{{ route('ordenes.index') }}" class="btn btn-outline-dark filter-btn-clear">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.5"/>
                        </svg>
                        Limpiar
                    </a>

                    @if ($search || $status || $limit || $order)
                        <span class="badge-active-filter">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                            </svg>
                            Filtro activo
                        </span>
                    @endif
                </div>
            </form>

            {{-- Info del listado --}}
            <div class="table-compact-info">
                <p>
                    <strong>{{ $ordenes->total() }}</strong> registro(s) &middot;
                    Página {{ $ordenes->isEmpty() ? 0 : $ordenes->currentPage() }} de {{ $ordenes->lastPage() }}
                </p>
                <span class="badge-active-filter" style="background:var(--dashboard-surface-soft); color:var(--dashboard-text); border-color:var(--dashboard-border);">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M3 15h18M9 3v18"/>
                    </svg>
                    Límite {{ $ordenes->isEmpty() ? 0 : $ordenes->perPage() }}
                </span>
            </div>

            {{-- Listado --}}
            @if ($ordenes->isEmpty())
                <div class="resource-empty">
                    No hay coincidencias para "{{ $search ?: 'tu búsqueda actual' }}".
                </div>
            @else
                <div class="resource-table-wrap">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 table-compact">
                            <thead>
                                <tr>
                                    <th># en sistema</th>
                                    <th>Estado</th>
                                    <th>Cliente</th>
                                    <th>Servicio</th>
                                    <th>Vehículo</th>
                                    <th>Placas</th>
                                    <th>Encargado</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ordenes as $row)
                                    @php
                                        $statusClass = match ($row->status) {
                                            'finalizada' => 'success',
                                            'cancelada'  => 'danger',
                                            default      => 'info',
                                        };
                                    @endphp
                                    <tr>
                                        <th>#{{ $row->id_ordenes }}</th>
                                        <td>
                                            <span class="resource-status is-{{ $statusClass }}">
                                                <i class="fas fa-circle"></i> {{ ucwords($row->status) }}
                                            </span>
                                        </td>
                                        <td>{{ ucwords($row->cliente->nombreCompleto) }}</td>
                                        <td>{{ $row->servicio->nombreServicio }}</td>
                                        <td>{{ $row->vehiculo->marca }}</td>
                                        <td>{{ $row->placas }}</td>
                                        <td>{{ $row->user->name }}</td>
                                        <td class="text-end">
                                            <div class="resource-actions justify-content-end">
                                                <a class="btn btn-outline-dark btn-sm" href="{{ route('ordenes.show', $row->id_ordenes) }}" title="Visualizar a detalle">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a class="btn btn-outline-dark btn-sm" href="{{ route('ordenes.edit', $row->id_ordenes) }}" title="Editar orden">
                                                    <i class="fas fa-pen"></i>
                                                </a>
                                                <a class="btn btn-outline-dark btn-sm" href="{{ route('ordenes.export', $row->id_ordenes) }}" title="Exportar a PDF">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                                @if (auth()->user()->can('admin.orden.destroy'))
                                                    <button class="btn btn-outline-danger btn-sm" title="Eliminar orden"
                                                        data-toggle="modal" data-target="#deleteModal{{ $row->id_ordenes }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>

                                    @if (auth()->user()->can('admin.orden.destroy'))
                                        <div class="modal fade" id="deleteModal{{ $row->id_ordenes }}" tabindex="-1"
                                            aria-labelledby="deleteModalLabel{{ $row->id_ordenes }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title" style="font-weight:800;">Eliminar orden</h5>
                                                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Cerrar">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        ¿Seguro que deseas eliminar la orden <strong>#{{ $row->id_ordenes }}</strong>?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal" style="font-weight:600;">Cancelar</button>
                                                        <form method="POST" action="{{ route('ordenes.destroy', $row->id_ordenes) }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger" style="font-weight:800;">Eliminar</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center flex-wrap mt-3" style="gap:.75rem;">
                    <p class="mb-0 text-muted" style="font-size:0.84rem;">Mostrando {{ $ordenes->count() }} elemento(s) en esta página.</p>
                    {{ $ordenes->appends(Request::except('page'))->links('pagination::bootstrap-5') }}
                </div>
            @endif

        </section>
    </div>
@stop