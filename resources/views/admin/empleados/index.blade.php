@extends('layouts.dashboard')

@section('title', 'Usuarios')

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
    <div class="resource-page">
        <section class="resource-hero">
            <div class="resource-hero__top">
                <div class="resource-hero__copy">
                    <span class="resource-hero__eyebrow">Accesos y permisos</span>
                    <h1 class="resource-hero__title">Usuarios del sistema</h1>
                    <p>Administra accesos, revisa correos, ubica perfiles rápido y entra directo a editar o visualizar permisos del equipo.</p>
                </div>

                <div class="resource-hero__actions">
                    <a href="{{ route('users.create') }}" class="btn btn-primary">
                        <i class="fas fa-user-plus mr-1"></i> Nuevo usuario
                    </a>
                </div>
            </div>

            <div class="resource-metrics">
                <article class="resource-metric">
                    <span class="resource-metric__label">Usuarios</span>
                    <p class="resource-metric__value">{{ $data->total() }}</p>
                    <p class="resource-metric__copy">Registros encontrados con la búsqueda actual.</p>
                </article>
                <article class="resource-metric">
                    <span class="resource-metric__label">Página</span>
                    <p class="resource-metric__value">{{ $data->isEmpty() ? 0 : $data->currentPage() }}</p>
                    <p class="resource-metric__copy">Vista {{ $data->isEmpty() ? 0 : $data->currentPage() }} de {{ $data->lastPage() }}.</p>
                </article>
                <article class="resource-metric">
                    <span class="resource-metric__label">Búsqueda</span>
                    <p class="resource-metric__value">{{ $search ? 'Sí' : 'No' }}</p>
                    <p class="resource-metric__copy">{{ $search ? 'Filtro por texto activo.' : 'Mostrando el directorio completo.' }}</p>
                </article>
            </div>
        </section>

        <section class="resource-panel">
            <div class="resource-panel__header">
                <div>
                    <span class="resource-panel__eyebrow">Consulta</span>
                    <h2 class="resource-panel__title">Buscar personal</h2>
                    <p class="resource-panel__copy">Busca por nombre o correo electrónico para ubicar un perfil en segundos.</p>
                </div>
            </div>

            <form action="{{ route('users.index') }}" method="get" class="resource-toolbar mt-4" style="grid-template-columns: minmax(0, 1fr) auto;">
                <div class="resource-toolbar__field">
                    <label for="search">Buscar usuario</label>
                    <input type="text" id="search" name="search" class="form-control" value="{{ $search }}"
                        placeholder="Nombre o correo electrónico">
                </div>

                <div class="resource-toolbar__actions">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search mr-1"></i> Aplicar
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-dark">
                        <i class="fas fa-rotate-left mr-1"></i> Limpiar
                    </a>
                </div>
            </form>
        </section>

        <section class="resource-panel">
            <div class="resource-panel__header">
                <div>
                    <span class="resource-panel__eyebrow">Listado</span>
                    <h2 class="resource-panel__title">Perfiles del panel</h2>
                    <p class="resource-panel__copy">
                        {{ $data->total() }} registro(s) encontrado(s). Página
                        {{ $data->isEmpty() ? 0 : $data->currentPage() }} de {{ $data->lastPage() }}.
                    </p>
                </div>
            </div>

            @if ($data->isEmpty())
                <div class="resource-empty">
                    {{ $message ?? 'No hay usuarios que coincidan con tu búsqueda.' }}
                </div>
            @else
                <div class="resource-table-wrap mt-4">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Correo electrónico</th>
                                    <th>Rol</th>
                                    <th class="text-right">Acciones</th>
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
                                            <div class="d-flex align-items-center" style="gap: .85rem;">
                                                <div class="resource-avatar" style="width: 48px; height: 48px; border-radius: 16px;">
                                                    {{ $initials ?: 'U' }}
                                                </div>
                                                <div>
                                                    <div class="font-weight-bold">{{ $row->name }}</div>
                                                    <small class="text-muted">ID {{ $row->id }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $row->email }}</td>
                                        <td>
                                            <span class="resource-pill">{{ $roleName }}</span>
                                        </td>
                                        <td class="text-right">
                                            <div class="resource-actions justify-content-end">
                                                <a class="btn btn-outline-dark" href="{{ route('users.show', $row->id) }}" title="Visualizar usuario">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a class="btn btn-outline-dark {{ $row->id === 1 ? 'disabled' : '' }}"
                                                    href="{{ route('users.edit', $row->id) }}" title="Editar usuario">
                                                    <i class="fas fa-pen"></i>
                                                </a>
                                                <a class="btn btn-outline-danger {{ $row->id === 1 ? 'disabled' : '' }}"
                                                    href="{{ route('users.delete', $row->id) }}" title="Eliminar usuario">
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

                <div class="d-flex justify-content-between align-items-center flex-wrap mt-4" style="gap: .75rem;">
                    <p class="mb-0 text-muted">Mostrando {{ $data->count() }} usuario(s) en esta página.</p>
                    {{ $data->setPath(route('users.index'))->appends(Request::except('page'))->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </section>
    </div>
@stop
