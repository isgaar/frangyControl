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
        <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:1rem;">
            <div>
                <span class="resource-hero__eyebrow" style="font-size:0.78rem;">Directorio y atención</span>
                <h1 class="resource-hero__title" style="font-size:1.5rem; font-weight:800; margin:2px 0 0;">Clientes listos para operar</h1>
            </div>
            <a href="{{ route('clientes.create') }}" class="btn btn-primary" style="font-size:0.9rem; font-weight:800;">
                <i class="fas fa-user-plus me-1"></i> Nuevo cliente
            </a>
        </div>

        {{-- Métricas compactas --}}
        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:8px; margin-bottom:1rem;">
            <article class="resource-metric" style="padding:10px 14px;">
                <span class="resource-metric__label" style="font-size:0.75rem; font-weight:700;">Clientes</span>
                <p class="resource-metric__value" style="font-size:1.4rem; font-weight:800; margin:3px 0;">{{ $data->total() }}</p>
                <p class="resource-metric__copy" style="font-size:0.75rem; margin:0;">Registros en el directorio</p>
            </article>
            <article class="resource-metric" style="padding:10px 14px;">
                <span class="resource-metric__label" style="font-size:0.75rem; font-weight:700;">Página</span>
                <p class="resource-metric__value" style="font-size:1.4rem; font-weight:800; margin:3px 0;">{{ $data->isEmpty() ? 0 : $data->currentPage() }}</p>
                <p class="resource-metric__copy" style="font-size:0.75rem; margin:0;">De {{ $data->lastPage() }} disponibles</p>
            </article>
            <article class="resource-metric" style="padding:10px 14px;">
                <span class="resource-metric__label" style="font-size:0.75rem; font-weight:700;">Orden</span>
                <p class="resource-metric__value" style="font-size:1.4rem; font-weight:800; margin:3px 0;">{{ $sortOrder === 'desc' ? 'DESC' : 'ASC' }}</p>
                <p class="resource-metric__copy" style="font-size:0.75rem; margin:0;">Por {{ $sortBy === 'nombreCompleto' ? 'nombre' : 'ID' }}</p>
            </article>
        </div>

        {{-- Panel unificado: filtros + listado --}}
        <section class="resource-panel">

            {{-- Barra de filtros --}}
            <form action="{{ route('clientes.index') }}" method="GET">
                <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-bottom:1rem;">

                    {{-- Búsqueda con lupa integrada --}}
                    <div style="display:flex; align-items:center; flex:1; min-width:220px; border:1px solid #ced4da; border-radius:6px; overflow:hidden; height:40px;">
                        <input
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Nombre, teléfono o correo…"
                            style="flex:1; border:none; outline:none; padding:0 12px; font-size:0.9rem; height:100%; background:transparent; color:inherit;">
                        <button type="submit" title="Buscar" style="height:40px; width:44px; background:#0d6efd; border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                        </button>
                    </div>

                    <select name="sort_by" class="form-select" style="height:40px; width:auto; font-size:0.9rem;">
                        <option value="id_cliente"    {{ $sortBy === 'id_cliente'    ? 'selected' : '' }}>Ordenar por ID</option>
                        <option value="nombreCompleto" {{ $sortBy === 'nombreCompleto' ? 'selected' : '' }}>Ordenar por nombre</option>
                    </select>

                    <select name="sort_order" class="form-select" style="height:40px; width:auto; font-size:0.9rem;">
                        <option value="asc"  {{ $sortOrder === 'asc'  ? 'selected' : '' }}>Ascendente</option>
                        <option value="desc" {{ $sortOrder === 'desc' ? 'selected' : '' }}>Descendente</option>
                    </select>

                    <select name="limit" class="form-select" style="height:40px; width:auto; font-size:0.9rem;">
                        <option value="6"  {{ (string) $limit === '6'  ? 'selected' : '' }}>6 por página</option>
                        <option value="9"  {{ (string) $limit === '9'  ? 'selected' : '' }}>9 por página</option>
                        <option value="10" {{ (string) $limit === '10' ? 'selected' : '' }}>10 por página</option>
                        <option value="12" {{ (string) $limit === '12' ? 'selected' : '' }}>12 por página</option>
                        <option value="15" {{ (string) $limit === '15' ? 'selected' : '' }}>15 por página</option>
                    </select>

                    <a href="{{ route('clientes.index') }}" class="btn btn-outline-dark" style="height:40px; display:inline-flex; align-items:center; font-size:0.9rem; font-weight:600; gap:6px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.5"/>
                        </svg>
                        Limpiar
                    </a>

                    @if ($search || (string) $limit !== '10' || $sortBy !== 'id_cliente' || $sortOrder !== 'asc')
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
                    <strong>{{ $data->total() }}</strong> registro(s) &middot;
                    Página {{ $data->isEmpty() ? 0 : $data->currentPage() }} de {{ $data->lastPage() }}
                </p>
                <span style="display:inline-flex; align-items:center; gap:6px; font-size:0.84rem; font-weight:700; padding:4px 12px; border-radius:20px; background:#e2e3e5; color:#41464b; border:1px solid #ced4da;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M3 15h18M9 3v18"/>
                    </svg>
                    Límite {{ $data->isEmpty() ? 0 : $data->perPage() }}
                </span>
            </div>

            {{-- Listado --}}
            @if ($data->isEmpty())
                <div class="resource-empty" style="font-size:0.9rem;">
                    No existe el cliente "{{ $search }}".
                </div>
            @else
                <div class="resource-card-grid mt-4">
                    @foreach ($data as $cliente)
                        @php
                            $initials = collect(preg_split('/\s+/', trim($cliente->nombreCompleto)))
                                ->filter()
                                ->take(2)
                                ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
                                ->implode('');
                        @endphp
                        <article class="resource-person-card">
                            <div class="resource-person-card__top">
                                <div class="resource-avatar">{{ $initials ?: 'C' }}</div>
                                <div>
                                    <h3 class="resource-person-card__title" style="font-size:1rem; font-weight:800;">{{ ucwords($cliente->nombreCompleto) }}</h3>
                                    <p class="resource-person-card__copy" style="font-size:0.84rem;">Contacto activo dentro del panel administrativo.</p>
                                </div>
                            </div>

                            <div class="resource-kv">
                                <div class="resource-kv__item">
                                    <span class="resource-kv__label" style="font-size:0.78rem; font-weight:700;">Teléfono</span>
                                    <p class="resource-kv__value" style="font-size:0.9rem;">{{ $cliente->telefono }}</p>
                                </div>
                                <div class="resource-kv__item">
                                    <span class="resource-kv__label" style="font-size:0.78rem; font-weight:700;">Correo electrónico</span>
                                    <p class="resource-kv__value" style="font-size:0.9rem;">{{ $cliente->correo }}</p>
                                </div>
                            </div>

                            <div class="resource-person-card__footer">
                                <a class="btn btn-outline-dark btn-sm" href="{{ route('clientes.show', $cliente->id_cliente) }}" title="Ver cliente" style="font-size:0.84rem; font-weight:600;">
                                    <i class="fas fa-eye me-1"></i> Ver
                                </a>
                                <a class="btn btn-outline-dark btn-sm" href="{{ route('clientes.edit', $cliente->id_cliente) }}" title="Editar cliente" style="font-size:0.84rem; font-weight:600;">
                                    <i class="fas fa-user-edit me-1"></i> Editar tarjeta
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="d-flex justify-content-between align-items-center flex-wrap mt-4" style="gap:.75rem;">
                    <p class="mb-0 text-muted" style="font-size:0.84rem;">Mostrando {{ $data->count() }} registro(s) en esta página.</p>
                    {{ $data->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
                </div>
            @endif

        </section>
    </div>
@endsection