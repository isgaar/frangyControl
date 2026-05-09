@extends('layouts.dashboard')

@section('title', 'Cotizaciones')

@section('content')
    @php
        $collection = $cotizaciones->getCollection();
        $totals = [
            'total' => $cotizaciones->total(),
            'enviada' => $collection->where('estado', 'enviada')->count(),
            'aceptada' => $collection->where('estado', 'aceptada')->count(),
            'importe' => $collection->sum(fn ($cotizacion) => (float) $cotizacion->total),
        ];
        $estadoLabel = [
            'borrador' => 'Borrador',
            'enviada' => 'Enviada',
            'aceptada' => 'Aceptada',
            'rechazada' => 'Rechazada',
            'vencida' => 'Vencida',
        ];
    @endphp

    <style>
        .quote-list-card {
            display: grid;
            gap: .2rem;
        }

        .quote-list-card strong {
            color: var(--dashboard-text);
        }

        .quote-list-card span {
            color: var(--dashboard-muted);
            font-size: .82rem;
        }

        .quote-filter-field {
            display: grid;
            gap: .25rem;
        }

        .quote-filter-field label {
            margin: 0;
            font-size: .74rem;
            font-weight: 800;
            color: var(--dashboard-muted);
            text-transform: uppercase;
        }

        .quote-empty-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: .5rem;
            margin-top: .75rem;
        }
    </style>

    <div class="resource-page">
        <div class="page-header">
            <div>
                <span class="page-eyebrow">Módulo comercial</span>
                <h1 class="page-title">Cotizaciones de servicios</h1>
            </div>
            <a href="{{ route('cotizaciones.create') }}" class="btn btn-primary" style="font-weight:800;">
                <i class="fas fa-file-invoice-dollar me-1"></i> Nueva cotización
            </a>
        </div>

        <div class="metrics-grid">
            <article class="metric-card">
                <span class="metric-card__label">Resultados</span>
                <p class="metric-card__value">{{ $totals['total'] }}</p>
                <p class="metric-card__copy">Con el filtro actual</p>
            </article>
            <article class="metric-card">
                <span class="metric-card__label">Enviadas</span>
                <p class="metric-card__value">{{ $totals['enviada'] }}</p>
                <p class="metric-card__copy">En esta página</p>
            </article>
            <article class="metric-card">
                <span class="metric-card__label">Aceptadas</span>
                <p class="metric-card__value">{{ $totals['aceptada'] }}</p>
                <p class="metric-card__copy">En esta página</p>
            </article>
            <article class="metric-card">
                <span class="metric-card__label">Importe</span>
                <p class="metric-card__value">${{ number_format($totals['importe'], 2) }}</p>
                <p class="metric-card__copy">Suma de la vista actual</p>
            </article>
        </div>

        <section class="resource-panel">
            <form action="{{ route('cotizaciones.index') }}" method="get">
                <div class="filter-toolbar">
                    <div class="filter-search-box">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por folio, servicio o responsable...">
                        <button type="submit" title="Buscar">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>

                    <div class="quote-filter-field">
                        <label for="estado">Estado</label>
                        <select name="estado" id="estado" class="form-control">
                            <option value="">Todos los estados</option>
                            @foreach ($estadoLabel as $value => $label)
                                <option value="{{ $value }}" {{ $estado === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="quote-filter-field">
                        <label for="order">Orden</label>
                        <select name="order" id="order" class="form-control">
                            <option value="desc" {{ $order === 'desc' ? 'selected' : '' }}>Últimas primero</option>
                            <option value="asc" {{ $order === 'asc' ? 'selected' : '' }}>Primeras primero</option>
                        </select>
                    </div>

                    <div class="quote-filter-field">
                        <label for="limit">Vista</label>
                        <select name="limit" id="limit" class="form-control">
                            @foreach ([5, 10, 15, 25] as $option)
                                <option value="{{ $option }}" {{ (int) $limit === $option ? 'selected' : '' }}>{{ $option }} por página</option>
                            @endforeach
                        </select>
                    </div>

                    <a href="{{ route('cotizaciones.index') }}" class="btn btn-outline-dark filter-btn-clear">
                        <i class="fas fa-redo-alt"></i> Limpiar
                    </a>
                </div>
            </form>

            <div class="table-compact-info">
                <p><strong>{{ $cotizaciones->total() }}</strong> cotización(es) encontradas.</p>
                <span class="badge-active-filter" style="background:var(--dashboard-surface-soft); color:var(--dashboard-text); border-color:var(--dashboard-border);">
                    Página {{ $cotizaciones->isEmpty() ? 0 : $cotizaciones->currentPage() }} de {{ $cotizaciones->lastPage() }}
                </span>
            </div>

            @if ($cotizaciones->isEmpty())
                <div class="resource-empty">
                    <strong>No hay cotizaciones para mostrar.</strong>
                    <div class="quote-empty-actions">
                        <a href="{{ route('cotizaciones.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i> Crear cotización
                        </a>
                        <a href="{{ route('cotizaciones.index') }}" class="btn btn-outline-dark btn-sm">
                            <i class="fas fa-redo-alt me-1"></i> Limpiar filtros
                        </a>
                    </div>
                </div>
            @else
                <div class="resource-table-wrap">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 table-compact">
                            <thead>
                                <tr>
                                    <th>Folio</th>
                                    <th>Responsable</th>
                                    <th>Servicio</th>
                                    <th>Seguimiento</th>
                                    <th>Vigencia</th>
                                    <th>Total</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cotizaciones as $row)
                                    <tr>
                                        <th>
                                            <div class="quote-list-card">
                                                <strong>{{ $row->folio }}</strong>
                                                <span>{{ $row->created_at?->format('d/m/Y H:i') }}</span>
                                            </div>
                                        </th>
                                        <td>
                                            <div class="quote-list-card">
                                                <strong>{{ $row->user?->name ?? 'Sin responsable' }}</strong>
                                                <span>{{ $estadoLabel[$row->estado] ?? ucfirst($row->estado) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="quote-list-card">
                                                <strong>{{ $row->servicio?->nombreServicio ?? 'Sin servicio' }}</strong>
                                                <span>Subtotal ${{ number_format((float) $row->precio_base, 2) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge-active-filter">{{ $estadoLabel[$row->estado] ?? ucfirst($row->estado) }}</span>
                                        </td>
                                        <td>
                                            <div class="quote-list-card">
                                                <strong>{{ $row->vigencia ? $row->vigencia->format('d/m/Y') : 'Sin vigencia' }}</strong>
                                                <span>{{ $row->vigencia && $row->vigencia->isBefore(today()) ? 'Vigencia vencida' : 'Seguimiento abierto' }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="quote-list-card">
                                                <strong>${{ number_format((float) $row->total, 2) }}</strong>
                                                <span>Rebaja ${{ number_format((float) $row->descuento_monto, 2) }}</span>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <div class="resource-actions justify-content-end">
                                                <a class="btn btn-outline-dark btn-sm" href="{{ route('cotizaciones.show', $row->id_cotizacion) }}" title="Ver cotización">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a class="btn btn-outline-dark btn-sm" href="{{ route('cotizaciones.edit', $row->id_cotizacion) }}" title="Editar cotización">
                                                    <i class="fas fa-pen"></i>
                                                </a>
                                                <form action="{{ route('cotizaciones.destroy', $row->id_cotizacion) }}" method="post" class="d-inline" onsubmit="return confirm('¿Eliminar esta cotización?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-outline-danger btn-sm" type="submit" title="Eliminar cotización">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center flex-wrap mt-3" style="gap:.75rem;">
                    <p class="mb-0 text-muted" style="font-size:0.84rem;">Mostrando {{ $cotizaciones->count() }} elemento(s) en esta página.</p>
                    {{ $cotizaciones->appends(Request::except('page'))->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </section>
    </div>
@endsection
