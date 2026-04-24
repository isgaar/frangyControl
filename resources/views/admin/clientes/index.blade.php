@extends('layouts.dashboard')

@section('title', 'Clientes')

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
        $limit = request('limit', $data->perPage());
        $sortBy = $sortBy ?? request('sort_by', 'id_cliente');
        $sortOrder = $sortOrder ?? request('sort_order', 'asc');
    @endphp

    <div class="resource-page">
        <section class="resource-hero">
            <div class="resource-hero__top">
                <div class="resource-hero__copy">
                    <span class="resource-hero__eyebrow">Directorio y atención</span>
                    <h1 class="resource-hero__title">Clientes listos para operar</h1>
                    <p>Consulta tarjetas de contacto, busca rápido por nombre, teléfono o correo y mantén el padrón del taller más claro.</p>
                </div>

                <div class="resource-hero__actions">
                    <a href="{{ route('clientes.create') }}" class="btn btn-primary">
                        <i class="fas fa-user-plus mr-1"></i> Nuevo cliente
                    </a>
                </div>
            </div>

            <div class="resource-metrics">
                <article class="resource-metric">
                    <span class="resource-metric__label">Clientes</span>
                    <p class="resource-metric__value">{{ $data->total() }}</p>
                    <p class="resource-metric__copy">Registros encontrados en el directorio.</p>
                </article>
                <article class="resource-metric">
                    <span class="resource-metric__label">Página</span>
                    <p class="resource-metric__value">{{ $data->isEmpty() ? 0 : $data->currentPage() }}</p>
                    <p class="resource-metric__copy">Vista actual de {{ $data->lastPage() }} disponibles.</p>
                </article>
                <article class="resource-metric">
                    <span class="resource-metric__label">Orden</span>
                    <p class="resource-metric__value">{{ $sortOrder === 'desc' ? 'DESC' : 'ASC' }}</p>
                    <p class="resource-metric__copy">Acomodado por {{ $sortBy === 'nombreCompleto' ? 'nombre' : 'ID' }}.</p>
                </article>
            </div>
        </section>

        <section class="resource-panel">
            <div class="resource-panel__header">
                <div>
                    <span class="resource-panel__eyebrow">Búsqueda</span>
                    <h2 class="resource-panel__title">Refina el directorio</h2>
                    <p class="resource-panel__copy">Puedes combinar búsqueda, cantidad por página y orden del listado.</p>
                </div>

                @if ($search)
                    <div class="resource-pill">
                        <i class="fas fa-magnifying-glass"></i> "{{ $search }}"
                    </div>
                @endif
            </div>

            <form action="{{ route('clientes.index') }}" method="GET" class="resource-toolbar mt-4">
                <div class="resource-toolbar__field">
                    <label for="search">Buscar cliente</label>
                    <input type="text" id="search" name="search" class="form-control" value="{{ $search }}"
                        placeholder="Nombre, teléfono o correo">
                </div>

                <div class="resource-toolbar__field">
                    <label for="limit">Mostrar</label>
                    <select name="limit" id="limit" class="form-control">
                        <option value="6" {{ (string) $limit === '6' ? 'selected' : '' }}>6 tarjetas</option>
                        <option value="9" {{ (string) $limit === '9' ? 'selected' : '' }}>9 tarjetas</option>
                        <option value="10" {{ (string) $limit === '10' ? 'selected' : '' }}>10 tarjetas</option>
                        <option value="12" {{ (string) $limit === '12' ? 'selected' : '' }}>12 tarjetas</option>
                        <option value="15" {{ (string) $limit === '15' ? 'selected' : '' }}>15 tarjetas</option>
                    </select>
                </div>

                <div class="resource-toolbar__field">
                    <label for="sort_by">Ordenar por</label>
                    <select name="sort_by" id="sort_by" class="form-control">
                        <option value="id_cliente" {{ $sortBy === 'id_cliente' ? 'selected' : '' }}>ID</option>
                        <option value="nombreCompleto" {{ $sortBy === 'nombreCompleto' ? 'selected' : '' }}>Nombre</option>
                    </select>
                </div>

                <div class="resource-toolbar__field">
                    <label for="sort_order">Dirección</label>
                    <select name="sort_order" id="sort_order" class="form-control">
                        <option value="asc" {{ $sortOrder === 'asc' ? 'selected' : '' }}>Ascendente</option>
                        <option value="desc" {{ $sortOrder === 'desc' ? 'selected' : '' }}>Descendente</option>
                    </select>
                </div>

                <div class="resource-toolbar__actions">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search mr-1"></i> Aplicar
                    </button>
                    <a href="{{ route('clientes.index') }}" class="btn btn-outline-dark">
                        <i class="fas fa-rotate-left mr-1"></i> Limpiar
                    </a>
                </div>
            </form>
        </section>

        <section class="resource-panel">
            <div class="resource-panel__header">
                <div>
                    <span class="resource-panel__eyebrow">Tarjetas</span>
                    <h2 class="resource-panel__title">Clientes registrados</h2>
                    <p class="resource-panel__copy">
                        {{ $data->total() }} tarjeta(s) encontradas.
                    </p>
                </div>
            </div>

            @if ($data->isEmpty())
                <div class="resource-empty">
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
                                    <h3 class="resource-person-card__title">{{ ucwords($cliente->nombreCompleto) }}</h3>
                                    <p class="resource-person-card__copy">Contacto activo dentro del panel administrativo.</p>
                                </div>
                            </div>

                            <div class="resource-kv">
                                <div class="resource-kv__item">
                                    <span class="resource-kv__label">Teléfono</span>
                                    <p class="resource-kv__value">{{ $cliente->telefono }}</p>
                                </div>
                                <div class="resource-kv__item">
                                    <span class="resource-kv__label">Correo electrónico</span>
                                    <p class="resource-kv__value">{{ $cliente->correo }}</p>
                                </div>
                            </div>

                            <div class="resource-person-card__footer">
                                <a class="btn btn-outline-dark" href="{{ route('clientes.edit', $cliente->id_cliente) }}" title="Editar cliente">
                                    <i class="fas fa-user-pen mr-1"></i> Editar tarjeta
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="d-flex justify-content-between align-items-center flex-wrap mt-4" style="gap: .75rem;">
                    <p class="mb-0 text-muted">Mostrando {{ $data->count() }} registro(s) en esta página.</p>
                    {{ $data->appends(request()->except('page'))->links() }}
                </div>
            @endif
        </section>
    </div>
@endsection
