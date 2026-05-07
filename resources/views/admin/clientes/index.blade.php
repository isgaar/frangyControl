@extends('layouts.dashboard')

@section('title', 'Clientes')

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
        $limit     = request('limit', $data->perPage());
        $sortBy    = $sortBy    ?? request('sort_by',    'id_cliente');
        $sortOrder = $sortOrder ?? request('sort_order', 'asc');
    @endphp

    <div class="resource-page">

        {{-- Encabezado compacto --}}
        <div class="page-header">
            <div>
                <span class="page-eyebrow">Directorio y atención</span>
                <h1 class="page-title">Clientes listos para operar</h1>
            </div>
            <a href="{{ route('clientes.create') }}" class="btn btn-primary" style="font-weight:800;">
                <i class="fas fa-user-plus me-1"></i> Nuevo cliente
            </a>
        </div>

        {{-- Métricas compactas --}}
        <div class="metrics-grid">
            <article class="metric-card">
                <span class="metric-card__label">Clientes</span>
                <p class="metric-card__value">{{ $data->total() }}</p>
                <p class="metric-card__copy">Registros en el directorio</p>
            </article>
            <article class="metric-card">
                <span class="metric-card__label">Página</span>
                <p class="metric-card__value">{{ $data->isEmpty() ? 0 : $data->currentPage() }}</p>
                <p class="metric-card__copy">De {{ $data->lastPage() }} disponibles</p>
            </article>
            <article class="metric-card">
                <span class="metric-card__label">Orden</span>
                <p class="metric-card__value">{{ $sortOrder === 'desc' ? 'DESC' : 'ASC' }}</p>
                <p class="metric-card__copy">Por {{ $sortBy === 'nombreCompleto' ? 'nombre' : 'ID' }}</p>
            </article>
        </div>

        {{-- Panel unificado: filtros + listado --}}
        <section class="resource-panel">

            {{-- Barra de filtros --}}
            <form action="{{ route('clientes.index') }}" method="GET">
                <div class="filter-toolbar">

                    {{-- Búsqueda con lupa integrada --}}
                    <div class="filter-search-box">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por nombre, teléfono, correo o RFC...">
                        <button type="submit" title="Buscar">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                        </button>
                    </div>

                    <select name="sort_by" class="form-control">
                        <option value="id_cliente"    {{ $sortBy === 'id_cliente'    ? 'selected' : '' }}>Ordenar por ID</option>
                        <option value="nombreCompleto" {{ $sortBy === 'nombreCompleto' ? 'selected' : '' }}>Ordenar por nombre</option>
                    </select>

                    <select name="sort_order" class="form-control">
                        <option value="asc"  {{ $sortOrder === 'asc'  ? 'selected' : '' }}>Ascendente</option>
                        <option value="desc" {{ $sortOrder === 'desc' ? 'selected' : '' }}>Descendente</option>
                    </select>

                    <select name="limit" class="form-control">
                        <option value="6"  {{ (string) $limit === '6'  ? 'selected' : '' }}>6 por página</option>
                        <option value="9"  {{ (string) $limit === '9'  ? 'selected' : '' }}>9 por página</option>
                        <option value="10" {{ (string) $limit === '10' ? 'selected' : '' }}>10 por página</option>
                        <option value="12" {{ (string) $limit === '12' ? 'selected' : '' }}>12 por página</option>
                        <option value="15" {{ (string) $limit === '15' ? 'selected' : '' }}>15 por página</option>
                    </select>

                    <a href="{{ route('clientes.index') }}" class="btn btn-outline-dark filter-btn-clear">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.5"/>
                        </svg>
                        Limpiar
                    </a>

                    @if ($search || (string) $limit !== '10' || $sortBy !== 'id_cliente' || $sortOrder !== 'asc')
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
                    <strong>{{ $data->total() }}</strong> registro(s) &middot;
                    Página {{ $data->isEmpty() ? 0 : $data->currentPage() }} de {{ $data->lastPage() }}
                </p>
                <span class="badge-active-filter" style="background:var(--dashboard-surface-soft); color:var(--dashboard-text); border-color:var(--dashboard-border);">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M3 15h18M9 3v18"/>
                    </svg>
                    Límite {{ $data->isEmpty() ? 0 : $data->perPage() }}
                </span>
            </div>

            {{-- Listado --}}
            @if ($data->isEmpty())
                <div class="resource-empty">
                    No existe el cliente "{{ $search }}".
                </div>
            @else
                <div class="resource-table-wrap mt-4">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 table-compact">
                            <thead>
                                <tr>
                                    <th># ID</th>
                                    <th>Cliente</th>
                                    <th>Teléfono</th>
                                    <th>Correo electrónico</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $cliente)
                                    @php
                                        $initials = collect(preg_split('/\s+/', trim($cliente->nombreCompleto)))
                                            ->filter()
                                            ->take(2)
                                            ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
                                            ->implode('');
                                    @endphp
                                    <tr>
                                        <th>#{{ $cliente->id_cliente }}</th>
                                        <td>
                                            <div class="d-flex align-items-center" style="gap:.85rem;">
                                                <div class="resource-avatar" style="width:40px; height:40px; border-radius:12px; font-size:0.9rem;">
                                                    {{ $initials ?: 'C' }}
                                                </div>
                                                <div class="fw-bold">{{ ucwords($cliente->nombreCompleto) }}</div>
                                            </div>
                                        </td>
                                        <td>{{ $cliente->telefono }}</td>
                                        <td>{{ $cliente->correo }}</td>
                                        <td class="text-end">
                                            <div class="resource-actions justify-content-end">
                                                <a class="btn btn-outline-dark btn-sm" href="{{ route('clientes.show', $cliente->id_cliente) }}" title="Ver cliente">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a class="btn btn-outline-dark btn-sm" href="{{ route('clientes.edit', $cliente->id_cliente) }}" title="Editar cliente">
                                                    <i class="fas fa-pen"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center flex-wrap mt-4" style="gap:.75rem;">
                    <p class="mb-0 text-muted" style="font-size:0.84rem;">Mostrando {{ $data->count() }} registro(s) en esta página.</p>
                    {{ $data->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
                </div>
            @endif

        </section>
    </div>
@endsection