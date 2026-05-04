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
        <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:1rem;">
            <div>
                <span class="resource-hero__eyebrow" style="font-size:0.78rem;">Seguimiento del taller</span>
                <h1 class="resource-hero__title" style="font-size:1.5rem; font-weight:800; margin:2px 0 0;">Órdenes activas y registradas</h1>
            </div>
            <a href="{{ route('ordenes.registro') }}" class="btn btn-primary" style="font-size:0.9rem; font-weight:800;">
                <i class="fas fa-clipboard-list me-1"></i> Nueva orden
            </a>
        </div>

        {{-- Métricas compactas --}}
        <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:8px; margin-bottom:1rem;">
            <article class="resource-metric" style="padding:10px 14px;">
                <span class="resource-metric__label" style="font-size:0.75rem; font-weight:700;">Resultados</span>
                <p class="resource-metric__value" style="font-size:1.4rem; font-weight:800; margin:3px 0;">{{ $ordenes->total() }}</p>
                <p class="resource-metric__copy" style="font-size:0.75rem; margin:0;">Con el filtro actual</p>
            </article>
            <article class="resource-metric" style="padding:10px 14px;">
                <span class="resource-metric__label" style="font-size:0.75rem; font-weight:700;">En proceso</span>
                <p class="resource-metric__value" style="font-size:1.4rem; font-weight:800; margin:3px 0;">{{ $statusCounts['en proceso'] }}</p>
                <p class="resource-metric__copy" style="font-size:0.75rem; margin:0;">Abiertas en esta página</p>
            </article>
            <article class="resource-metric" style="padding:10px 14px;">
                <span class="resource-metric__label" style="font-size:0.75rem; font-weight:700;">Finalizadas</span>
                <p class="resource-metric__value" style="font-size:1.4rem; font-weight:800; margin:3px 0;">{{ $statusCounts['finalizada'] }}</p>
                <p class="resource-metric__copy" style="font-size:0.75rem; margin:0;">Listas para consulta</p>
            </article>
            <article class="resource-metric" style="padding:10px 14px;">
                <span class="resource-metric__label" style="font-size:0.75rem; font-weight:700;">Vista actual</span>
                <p class="resource-metric__value" style="font-size:1.4rem; font-weight:800; margin:3px 0;">{{ $ordenes->isEmpty() ? 0 : $ordenes->currentPage() }}</p>
                <p class="resource-metric__copy" style="font-size:0.75rem; margin:0;">Página {{ $ordenes->isEmpty() ? 0 : $ordenes->currentPage() }} de {{ $ordenes->lastPage() }}</p>
            </article>
        </div>

        {{-- Panel unificado: filtros + listado --}}
        <section class="resource-panel">

            {{-- Barra de filtros --}}
            <form action="{{ route('ordenes.index') }}" method="get">
                <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-bottom:1rem;">

                    {{-- Búsqueda con lupa integrada --}}
                    <div style="display:flex; align-items:center; flex:1; min-width:220px; border:1px solid #ced4da; border-radius:6px; overflow:hidden; height:40px;">
                        <input
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Cliente, placas, servicio o encargado…"
                            style="flex:1; border:none; outline:none; padding:0 12px; font-size:0.9rem; height:100%; background:transparent; color:inherit;">
                        <button type="submit" title="Buscar" style="height:40px; width:44px; background:#0d6efd; border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                        </button>
                    </div>

                    <select name="status" class="form-control" style="height:40px; width:auto; font-size:0.9rem;">
                        <option value="">Todos los estados</option>
                        <option value="en proceso" {{ $status === 'en proceso' ? 'selected' : '' }}>En proceso</option>
                        <option value="cancelada"  {{ $status === 'cancelada'  ? 'selected' : '' }}>Cancelada</option>
                        <option value="finalizada" {{ $status === 'finalizada' ? 'selected' : '' }}>Finalizada</option>
                    </select>

                    <select name="order" class="form-control" style="height:40px; width:auto; font-size:0.9rem;">
                        <option value="desc" {{ $order === 'desc' ? 'selected' : '' }}>Últimos agregados</option>
                        <option value="asc"  {{ $order === 'asc'  ? 'selected' : '' }}>Primeros registros</option>
                    </select>

                    <select name="limit" class="form-control" style="height:40px; width:auto; font-size:0.9rem;">
                        <option value="5"  {{ (string) $limit === '5'  ? 'selected' : '' }}>5 por página</option>
                        <option value="10" {{ (string) $limit === '10' ? 'selected' : '' }}>10 por página</option>
                        <option value="15" {{ (string) $limit === '15' ? 'selected' : '' }}>15 por página</option>
                    </select>

                    <a href="{{ route('ordenes.index') }}" class="btn btn-outline-dark" style="height:40px; display:inline-flex; align-items:center; font-size:0.9rem; font-weight:600; gap:6px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.5"/>
                        </svg>
                        Limpiar
                    </a>

                    @if ($search || $status || $limit || $order)
                        <span style="display:inline-flex; align-items:center; gap:6px; font-size:0.84rem; font-weight:700; padding:4px 12px; border-radius:20px; background:#fff3cd; color:#856404; border:1px solid #ffc107;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                            </svg>
                            Filtro activo
                        </span>
                    @endif
                </div>
            </form>

            {{-- Info del listado --}}
            <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px; margin-bottom:.75rem;">
                <p style="font-size:0.9rem; margin:0;">
                    <strong>{{ $ordenes->total() }}</strong> registro(s) &middot;
                    Página {{ $ordenes->isEmpty() ? 0 : $ordenes->currentPage() }} de {{ $ordenes->lastPage() }}
                </p>
                <span style="display:inline-flex; align-items:center; gap:6px; font-size:0.84rem; font-weight:700; padding:4px 12px; border-radius:20px; background:#e2e3e5; color:#41464b; border:1px solid #ced4da;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M3 15h18M9 3v18"/>
                    </svg>
                    Límite {{ $ordenes->isEmpty() ? 0 : $ordenes->perPage() }}
                </span>
            </div>

            {{-- Listado --}}
            @if ($ordenes->isEmpty())
                <div class="resource-empty" style="font-size:0.9rem;">
                    No hay coincidencias para "{{ $search ?: 'tu búsqueda actual' }}".
                </div>
            @else
                <div class="resource-table-wrap">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="font-size:0.9rem;">
                            <thead style="font-size:0.82rem;">
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
                                        <th style="font-size:0.9rem;">#{{ $row->id_ordenes }}</th>
                                        <td>
                                            <span class="resource-status is-{{ $statusClass }}" style="font-size:0.84rem; font-weight:700;">
                                                <i class="fas fa-circle"></i> {{ ucwords($row->status) }}
                                            </span>
                                        </td>
                                        <td style="font-size:0.9rem;">{{ ucwords($row->cliente->nombreCompleto) }}</td>
                                        <td style="font-size:0.9rem;">{{ $row->servicio->nombreServicio }}</td>
                                        <td style="font-size:0.9rem;">{{ $row->vehiculo->marca }}</td>
                                        <td style="font-size:0.9rem;">{{ $row->placas }}</td>
                                        <td style="font-size:0.9rem;">{{ $row->user->name }}</td>
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
                                                        <h5 class="modal-title" style="font-size:1rem; font-weight:800;">Eliminar orden</h5>
                                                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Cerrar">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body" style="font-size:0.9rem;">
                                                        ¿Seguro que deseas eliminar la orden <strong>#{{ $row->id_ordenes }}</strong>?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal" style="font-size:0.9rem; font-weight:600;">Cancelar</button>
                                                        <form method="POST" action="{{ route('ordenes.destroy', $row->id_ordenes) }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger" style="font-size:0.9rem; font-weight:800;">Eliminar</button>
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