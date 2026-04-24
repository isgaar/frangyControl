@extends('layouts.dashboard')

@section('title', 'Órdenes')

@section('content_header')
    @if (Session::has('status'))
        <div class="col-md-12 alert-section">
            <div class="alert alert-{{ Session::get('status_type') }} dashboard-legacy-alert">
                <span class="dashboard-legacy-alert__text">
                    {{ Session::get('status') }}
                    @php
                        Session::forget('status');
                    @endphp
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
            'finalizada' => $collection->where('status', 'finalizada')->count(),
            'cancelada' => $collection->where('status', 'cancelada')->count(),
        ];
        $isFirefox = str_contains($_SERVER['HTTP_USER_AGENT'] ?? '', 'Firefox');
    @endphp

    <div class="resource-page">
        @unless ($isFirefox)
            <div class="resource-note is-info">
                <strong>Mejor experiencia visual</strong>
                Este panel luce mejor en Firefox, aunque puedes trabajar sin problema desde tu navegador actual.
            </div>
        @endunless

        <section class="resource-hero">
            <div class="resource-hero__top">
                <div class="resource-hero__copy">
                    <span class="resource-hero__eyebrow">Seguimiento del taller</span>
                    <h1 class="resource-hero__title">Órdenes activas y registradas</h1>
                    <p>Consulta el estado del trabajo, ubica al cliente correcto y entra directo a editar, visualizar o exportar cada orden.</p>
                </div>

                <div class="resource-hero__actions">
                    <a href="{{ route('ordenes.registro') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle mr-1"></i> Cliente nuevo
                    </a>
                    <a href="{{ route('ordenes.asigne') }}" class="btn btn-outline-light">
                        <i class="fas fa-user-check mr-1"></i> Cliente existente
                    </a>
                </div>
            </div>

            <div class="resource-metrics">
                <article class="resource-metric">
                    <span class="resource-metric__label">Resultados</span>
                    <p class="resource-metric__value">{{ $ordenes->total() }}</p>
                    <p class="resource-metric__copy">Órdenes encontradas con el filtro actual.</p>
                </article>
                <article class="resource-metric">
                    <span class="resource-metric__label">En proceso</span>
                    <p class="resource-metric__value">{{ $statusCounts['en proceso'] }}</p>
                    <p class="resource-metric__copy">Órdenes que siguen abiertas en esta página.</p>
                </article>
                <article class="resource-metric">
                    <span class="resource-metric__label">Finalizadas</span>
                    <p class="resource-metric__value">{{ $statusCounts['finalizada'] }}</p>
                    <p class="resource-metric__copy">Trabajos cerrados listos para consulta.</p>
                </article>
                <article class="resource-metric">
                    <span class="resource-metric__label">Vista actual</span>
                    <p class="resource-metric__value">{{ $ordenes->isEmpty() ? 0 : $ordenes->currentPage() }}</p>
                    <p class="resource-metric__copy">Página {{ $ordenes->isEmpty() ? 0 : $ordenes->currentPage() }} de {{ $ordenes->lastPage() }}.</p>
                </article>
            </div>
        </section>

        <section class="resource-panel">
            <div class="resource-panel__header">
                <div>
                    <span class="resource-panel__eyebrow">Filtros</span>
                    <h2 class="resource-panel__title">Buscar y ordenar</h2>
                    <p class="resource-panel__copy">Encuentra por número de orden, cliente, placas, servicio, vehículo o responsable.</p>
                </div>

                @if ($search || $status || $limit || $order)
                    <div class="resource-pill">
                        <i class="fas fa-filter"></i> Filtro activo
                    </div>
                @endif
            </div>

            <form action="{{ route('ordenes.index') }}" method="get" class="resource-toolbar mt-4">
                <div class="resource-toolbar__field">
                    <label for="search">Buscar orden</label>
                    <input type="text" id="search" name="search" class="form-control" value="{{ $search }}"
                        placeholder="Cliente, placas, servicio o encargado">
                </div>

                <div class="resource-toolbar__field">
                    <label for="limit">Registros</label>
                    <select name="limit" id="limit" class="form-control">
                        <option value="5" {{ (string) $limit === '5' ? 'selected' : '' }}>5 por página</option>
                        <option value="10" {{ (string) $limit === '10' ? 'selected' : '' }}>10 por página</option>
                        <option value="15" {{ (string) $limit === '15' ? 'selected' : '' }}>15 por página</option>
                    </select>
                </div>

                <div class="resource-toolbar__field">
                    <label for="order">Orden</label>
                    <select name="order" id="order" class="form-control">
                        <option value="desc" {{ $order === 'desc' ? 'selected' : '' }}>Últimos agregados</option>
                        <option value="asc" {{ $order === 'asc' ? 'selected' : '' }}>Primeros registros</option>
                    </select>
                </div>

                <div class="resource-toolbar__field">
                    <label for="status">Estado</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">Todos los estados</option>
                        <option value="en proceso" {{ $status === 'en proceso' ? 'selected' : '' }}>En proceso</option>
                        <option value="cancelada" {{ $status === 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                        <option value="finalizada" {{ $status === 'finalizada' ? 'selected' : '' }}>Finalizada</option>
                    </select>
                </div>

                <div class="resource-toolbar__actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search mr-1"></i> Aplicar
                    </button>
                    <a href="{{ route('ordenes.index') }}" class="btn btn-outline-dark">
                        <i class="fas fa-rotate-left mr-1"></i> Limpiar
                    </a>
                </div>
            </form>
        </section>

        <section class="resource-panel">
            <div class="resource-panel__header">
                <div>
                    <span class="resource-panel__eyebrow">Listado</span>
                    <h2 class="resource-panel__title">Órdenes registradas</h2>
                    <p class="resource-panel__copy">
                        {{ $ordenes->total() }} registro(s) encontrados. Página
                        {{ $ordenes->isEmpty() ? 0 : $ordenes->currentPage() }} de {{ $ordenes->lastPage() }}.
                    </p>
                </div>

                <div class="resource-pill">
                    <i class="fas fa-table"></i> Límite {{ $ordenes->isEmpty() ? 0 : $ordenes->perPage() }}
                </div>
            </div>

            @if ($ordenes->isEmpty())
                <div class="resource-empty">
                    No hay coincidencias para "{{ $search ?: 'tu búsqueda actual' }}".
                </div>
            @else
                <div class="resource-table-wrap mt-4">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th># en sistema</th>
                                    <th>Estado</th>
                                    <th>Cliente</th>
                                    <th>Servicio</th>
                                    <th>Vehículo</th>
                                    <th>Placas</th>
                                    <th>Encargado</th>
                                    <th class="text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ordenes as $row)
                                    @php
                                        $statusClass = match ($row->status) {
                                            'finalizada' => 'success',
                                            'cancelada' => 'danger',
                                            default => 'info',
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
                                        <td class="text-right">
                                            <div class="resource-actions justify-content-end">
                                                <a class="btn btn-outline-dark" href="{{ route('ordenes.show', $row->id_ordenes) }}"
                                                    title="Visualizar a detalle">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a class="btn btn-outline-dark" href="{{ route('ordenes.edit', $row->id_ordenes) }}"
                                                    title="Editar orden">
                                                    <i class="fas fa-pen"></i>
                                                </a>
                                                <a class="btn btn-outline-dark" href="{{ route('ordenes.export', $row->id_ordenes) }}"
                                                    title="Exportar a PDF">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                                @if (auth()->user()->can('admin.orden.destroy'))
                                                    <button class="btn btn-outline-danger" title="Eliminar orden" data-toggle="modal"
                                                        data-target="#deleteConfirmationModal{{ $row->id_ordenes }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>

                                    @if (auth()->user()->can('admin.orden.destroy'))
                                        <div class="modal fade" id="deleteConfirmationModal{{ $row->id_ordenes }}" tabindex="-1"
                                            aria-labelledby="deleteConfirmationModalLabel{{ $row->id_ordenes }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title" id="deleteConfirmationModalLabel{{ $row->id_ordenes }}">Eliminar orden</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        ¿Seguro que deseas eliminar la orden <strong>#{{ $row->id_ordenes }}</strong>?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-outline-dark" data-dismiss="modal">Cancelar</button>
                                                        <form method="POST" action="{{ route('ordenes.destroy', $row->id_ordenes) }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Eliminar</button>
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

                <div class="d-flex justify-content-between align-items-center flex-wrap mt-4" style="gap: .75rem;">
                    <p class="mb-0 text-muted">Mostrando {{ $ordenes->count() }} elemento(s) en esta página.</p>
                    {{ $ordenes->appends(Request::except('page'))->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </section>
    </div>
@stop
