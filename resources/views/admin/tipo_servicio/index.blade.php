@extends('layouts.dashboard')

@section('title', 'Tipos de servicio')

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
                    <span class="resource-hero__eyebrow">Servicios disponibles</span>
                    <h1 class="resource-hero__title">Catálogo de tipos de servicio</h1>
                    <p>Organiza las opciones que aparecen al capturar órdenes y mantén visible qué servicios están listos para usar.</p>
                </div>

                <div class="resource-hero__actions">
                    <a href="{{ route('tipo_servicio.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle mr-1"></i> Nuevo servicio
                    </a>
                    <a href="{{ route('datosv.index') }}" class="btn btn-outline-light">
                        <i class="fas fa-layer-group mr-1"></i> Panel general
                    </a>
                </div>
            </div>

            <div class="resource-metrics">
                <article class="resource-metric">
                    <span class="resource-metric__label">Servicios</span>
                    <p class="resource-metric__value">{{ $data->total() }}</p>
                    <p class="resource-metric__copy">Registros disponibles en el catálogo.</p>
                </article>
                <article class="resource-metric">
                    <span class="resource-metric__label">Página</span>
                    <p class="resource-metric__value">{{ $data->isEmpty() ? 0 : $data->currentPage() }}</p>
                    <p class="resource-metric__copy">Vista {{ $data->isEmpty() ? 0 : $data->currentPage() }} de {{ $data->lastPage() }}.</p>
                </article>
                <article class="resource-metric">
                    <span class="resource-metric__label">Filtro</span>
                    <p class="resource-metric__value">{{ $search ? 'Sí' : 'No' }}</p>
                    <p class="resource-metric__copy">{{ $search ? 'Búsqueda aplicada por nombre.' : 'Sin búsqueda aplicada.' }}</p>
                </article>
            </div>
        </section>

        <section class="resource-panel">
            <div class="resource-panel__header">
                <div>
                    <span class="resource-panel__eyebrow">Buscar</span>
                    <h2 class="resource-panel__title">Ubica un servicio</h2>
                    <p class="resource-panel__copy">Escribe una palabra clave para filtrar rápido los servicios registrados.</p>
                </div>
            </div>

            <form action="{{ route('tipo_servicio.index') }}" method="get" class="resource-toolbar mt-4"
                style="grid-template-columns: minmax(0, 1fr) auto;">
                <div class="resource-toolbar__field">
                    <label for="search">Buscar servicio</label>
                    <input type="text" id="search" name="search" class="form-control" value="{{ $search }}"
                        placeholder="Ejemplo: Afinación, diagnóstico, frenos">
                </div>

                <div class="resource-toolbar__actions">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search mr-1"></i> Buscar
                    </button>
                    <a href="{{ route('tipo_servicio.index') }}" class="btn btn-outline-dark">
                        <i class="fas fa-rotate-left mr-1"></i> Limpiar
                    </a>
                </div>
            </form>
        </section>

        <section class="resource-panel">
            <div class="resource-panel__header">
                <div>
                    <span class="resource-panel__eyebrow">Listado</span>
                    <h2 class="resource-panel__title">Servicios registrados</h2>
                    <p class="resource-panel__copy">
                        {{ $data->total() }} registro(s) encontrados. Página
                        {{ $data->isEmpty() ? 0 : $data->currentPage() }} de {{ $data->lastPage() }}.
                    </p>
                </div>
            </div>

            @if ($data->isEmpty())
                <div class="resource-empty">
                    No hay servicios que coincidan con "{{ $search }}".
                </div>
            @else
                <div class="resource-table-wrap mt-4">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th class="text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $row)
                                    <tr>
                                        <td>#{{ $row->id_servicio }}</td>
                                        <td>{{ $row->nombreServicio }}</td>
                                        <td class="text-right">
                                            <div class="resource-actions justify-content-end">
                                                <a class="btn btn-outline-dark" href="{{ route('tipo_servicio.edit', $row->id_servicio) }}" title="Editar servicio">
                                                    <i class="fas fa-pen"></i>
                                                </a>
                                                <a class="btn btn-outline-danger" href="{{ route('tipo_servicio.delete', $row->id_servicio) }}" title="Eliminar servicio">
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
                    <p class="mb-0 text-muted">Mostrando {{ $data->count() }} elemento(s) en esta página.</p>
                    {{ $data->setPath(route('tipo_servicio.index'))->appends(Request::except('page'))->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </section>
    </div>
@stop
