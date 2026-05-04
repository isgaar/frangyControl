@extends('layouts.dashboard')

@section('title', 'Usuarios')

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
        $limit = $limit ?? request('limit', $data->perPage());
        $order = $order ?? request('order', 'asc');
    @endphp

    <div class="resource-page">

        {{-- Encabezado compacto --}}
        <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:1rem;">
            <div>
                <span class="resource-hero__eyebrow" style="font-size:0.78rem;">Accesos y permisos</span>
                <h1 class="resource-hero__title" style="font-size:1.5rem; font-weight:800; margin:2px 0 0;">Usuarios del sistema</h1>
            </div>
            <a href="{{ route('usuarios.create') }}" class="btn btn-primary" style="font-size:0.9rem; font-weight:800;">
                <i class="fas fa-user-plus me-1"></i> Nuevo usuario
            </a>
        </div>

        {{-- Métricas compactas --}}
        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:8px; margin-bottom:1rem;">
            <article class="resource-metric" style="padding:10px 14px;">
                <span class="resource-metric__label" style="font-size:0.75rem; font-weight:700;">Usuarios</span>
                <p class="resource-metric__value" style="font-size:1.4rem; font-weight:800; margin:3px 0;">{{ $data->total() }}</p>
                <p class="resource-metric__copy" style="font-size:0.75rem; margin:0;">Con la búsqueda actual</p>
            </article>
            <article class="resource-metric" style="padding:10px 14px;">
                <span class="resource-metric__label" style="font-size:0.75rem; font-weight:700;">Página</span>
                <p class="resource-metric__value" style="font-size:1.4rem; font-weight:800; margin:3px 0;">{{ $data->isEmpty() ? 0 : $data->currentPage() }}</p>
                <p class="resource-metric__copy" style="font-size:0.75rem; margin:0;">De {{ $data->lastPage() }} disponibles</p>
            </article>
        </div>

        {{-- Panel unificado: filtros + listado --}}
        <section class="resource-panel">

            {{-- Barra de filtros --}}
            <form action="{{ route('usuarios.index') }}" method="get">
                <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-bottom:1rem;">

                    {{-- Búsqueda con lupa integrada --}}
                    <div style="display:flex; align-items:center; flex:1; min-width:220px; border:1px solid #ced4da; border-radius:6px; overflow:hidden; height:40px;">
                        <input
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Nombre o correo electrónico…"
                            style="flex:1; border:none; outline:none; padding:0 12px; font-size:0.9rem; height:100%; background:transparent; color:inherit;">
                        <button type="submit" title="Buscar" style="height:40px; width:44px; background:#0d6efd; border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                        </button>
                    </div>

                    <select name="order" class="form-control" style="height:40px; width:auto; font-size:0.9rem;">
                        <option value="asc"  {{ $order === 'asc'  ? 'selected' : '' }}>A–Z</option>
                        <option value="desc" {{ $order === 'desc' ? 'selected' : '' }}>Z–A</option>
                    </select>

                    <select name="limit" class="form-control" style="height:40px; width:auto; font-size:0.9rem;">
                        <option value="5"  {{ (string) $limit === '5'  ? 'selected' : '' }}>5 por página</option>
                        <option value="10" {{ (string) $limit === '10' ? 'selected' : '' }}>10 por página</option>
                        <option value="15" {{ (string) $limit === '15' ? 'selected' : '' }}>15 por página</option>
                    </select>

                    <a href="{{ route('usuarios.index') }}" class="btn btn-outline-dark" style="height:40px; display:inline-flex; align-items:center; font-size:0.9rem; font-weight:600; gap:6px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.5"/>
                        </svg>
                        Limpiar
                    </a>

                    @if ($search || (string) $limit !== '5' || $order !== 'asc')
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
                    {{ $message ?? 'No hay usuarios que coincidan con tu búsqueda.' }}
                </div>
            @else
                <div class="resource-table-wrap">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="font-size:0.9rem;">
                            <thead style="font-size:0.82rem;">
                                <tr>
                                    <th>Usuario</th>
                                    <th>Correo electrónico</th>
                                    <th>Rol</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $row)
                                    @php
                                        $roleName = optional($row->roles->first())->name ?? 'Sin rol';
                                        $initials = collect(preg_split('/\s+/', trim($row->name)))
                                            ->filter()
                                            ->take(2)
                                            ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
                                            ->implode('');
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center" style="gap:.85rem;">
                                                <div class="resource-avatar" style="width:48px; height:48px; border-radius:16px;">
                                                    {{ $initials ?: 'U' }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold" style="font-size:0.9rem;">{{ $row->name }}</div>
                                                    <small class="text-muted" style="font-size:0.78rem;">ID {{ $row->id }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="font-size:0.9rem;">{{ $row->email }}</td>
                                        <td>
                                            <span class="resource-pill" style="font-size:0.78rem; font-weight:700;">{{ $roleName }}</span>
                                        </td>
                                        <td class="text-end">
                                            <div class="resource-actions justify-content-end">
                                                <a class="btn btn-outline-dark btn-sm" href="{{ route('usuarios.show', $row->id) }}" title="Visualizar usuario">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a class="btn btn-outline-dark btn-sm {{ $row->id === 1 ? 'disabled' : '' }}"
                                                    href="{{ route('usuarios.edit', $row->id) }}" title="Editar usuario">
                                                    <i class="fas fa-pen"></i>
                                                </a>
                                                <a class="btn btn-outline-danger btn-sm {{ $row->id === 1 ? 'disabled' : '' }}"
                                                    href="{{ route('usuarios.delete', $row->id) }}" title="Eliminar usuario">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center flex-wrap mt-3" style="gap:.75rem;">
                    <p class="mb-0 text-muted" style="font-size:0.84rem;">Mostrando {{ $data->count() }} usuario(s) en esta página.</p>
                    {{ $data->setPath(route('usuarios.index'))->appends(Request::except('page'))->links('pagination::bootstrap-5') }}
                </div>
            @endif

        </section>
    </div>
@stop