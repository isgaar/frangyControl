@extends('layouts.dashboard')

@section('title', 'Datos generales')

@php
    $flashType = session('status_type');
    $flashClass = match ($flashType) {
        'success' => 'success',
        'warning' => 'warning',
        default => 'danger',
    };
@endphp

@section('content')
    @if (session('status'))
        <div class="alert alert-{{ $flashClass }} alert-dismissible fade show shadow-sm" role="alert">
            <strong>{{ session('status') }}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="resource-page">
        <section class="resource-hero">
            <div class="resource-hero__top">
                <div class="resource-hero__copy">
                    <span class="resource-hero__eyebrow">Catálogos base</span>
                    <h1 class="resource-hero__title">Datos generales del taller</h1>
                    <p>Administra marcas, tipos de vehículo y servicios desde un panel más claro, con accesos directos a cada catálogo.</p>
                </div>

                <div class="resource-hero__actions">
                    <a href="{{ route('datosv.create') }}" class="btn btn-primary">
                        <i class="fas fa-layer-group mr-1"></i> Carga general
                    </a>
                    <a href="{{ route('datosv.createunique') }}" class="btn btn-outline-light">
                        <i class="fas fa-car-side mr-1"></i> Nueva marca
                    </a>
                </div>
            </div>

            <div class="resource-metrics">
                <article class="resource-metric">
                    <span class="resource-metric__label">Marcas</span>
                    <p class="resource-metric__value">{{ $dataVehiculos->count() }}</p>
                    <p class="resource-metric__copy">Catálogo disponible para capturar unidades.</p>
                </article>
                <article class="resource-metric">
                    <span class="resource-metric__label">Tipos</span>
                    <p class="resource-metric__value">{{ $dataTiposVehiculos->count() }}</p>
                    <p class="resource-metric__copy">Clasificaciones operativas listas para usar.</p>
                </article>
                <article class="resource-metric">
                    <span class="resource-metric__label">Servicios</span>
                    <p class="resource-metric__value">{{ $dataServicios->count() }}</p>
                    <p class="resource-metric__copy">Opciones base para nuevas órdenes de servicio.</p>
                </article>
            </div>
        </section>

        <div class="resource-catalog-grid">
            <section class="resource-overview-card">
                <div class="resource-panel__header">
                    <div>
                        <span class="resource-panel__eyebrow">Marcas</span>
                        <h2 class="resource-overview-card__title">Marcas de vehículos</h2>
                        <p class="resource-panel__copy">Gestiona el catálogo de marcas que aparece al registrar una orden.</p>
                    </div>

                    <div class="resource-cta-group">
                        <a href="{{ route('datosv.createunique') }}" class="btn btn-outline-dark btn-sm">
                            <i class="fas fa-plus mr-1"></i> Agregar
                        </a>
                    </div>
                </div>

                <div class="resource-table-wrap mt-4">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Marca</th>
                                    <th class="text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($dataVehiculos as $row)
                                    <tr>
                                        <td>{{ $row->marca }}</td>
                                        <td class="text-right">
                                            <div class="resource-actions justify-content-end">
                                                <a class="btn btn-outline-dark btn-sm" href="{{ route('datosv.edit', $row->id_vehiculo) }}" title="Editar marca">
                                                    <i class="fas fa-pen"></i>
                                                </a>
                                                <a class="btn btn-outline-danger btn-sm" href="{{ route('datosv.delete', $row->id_vehiculo) }}" title="Eliminar marca">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="resource-empty">No hay marcas registradas todavía.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <section class="resource-overview-card">
                <div class="resource-panel__header">
                    <div>
                        <span class="resource-panel__eyebrow">Clasificación</span>
                        <h2 class="resource-overview-card__title">Tipos de vehículo</h2>
                        <p class="resource-panel__copy">Define cómo se clasifican las unidades dentro del sistema.</p>
                    </div>

                    <div class="resource-cta-group">
                        <a href="{{ route('tipo_vehiculo.index') }}" class="btn btn-outline-dark btn-sm">
                            <i class="fas fa-arrow-right mr-1"></i> Ver módulo
                        </a>
                        <a href="{{ route('tipo_vehiculo.create') }}" class="btn btn-outline-dark btn-sm">
                            <i class="fas fa-plus mr-1"></i> Nuevo
                        </a>
                    </div>
                </div>

                <div class="resource-table-wrap mt-4">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th class="text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($dataTiposVehiculos as $row)
                                    <tr>
                                        <td>{{ $row->tipo }}</td>
                                        <td class="text-right">
                                            <div class="resource-actions justify-content-end">
                                                <a class="btn btn-outline-dark btn-sm" href="{{ route('tipo_vehiculo.edit', $row->id_tvehiculo) }}" title="Editar tipo">
                                                    <i class="fas fa-pen"></i>
                                                </a>
                                                <a class="btn btn-outline-danger btn-sm" href="{{ route('tipo_vehiculo.delete', $row->id_tvehiculo) }}" title="Eliminar tipo">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="resource-empty">No hay tipos de vehículo registrados todavía.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <section class="resource-overview-card">
                <div class="resource-panel__header">
                    <div>
                        <span class="resource-panel__eyebrow">Servicios</span>
                        <h2 class="resource-overview-card__title">Nombres de servicios</h2>
                        <p class="resource-panel__copy">Mantén ordenado el catálogo de trabajos disponibles para el taller.</p>
                    </div>

                    <div class="resource-cta-group">
                        <a href="{{ route('tipo_servicio.index') }}" class="btn btn-outline-dark btn-sm">
                            <i class="fas fa-arrow-right mr-1"></i> Ver módulo
                        </a>
                        <a href="{{ route('tipo_servicio.create') }}" class="btn btn-outline-dark btn-sm">
                            <i class="fas fa-plus mr-1"></i> Nuevo
                        </a>
                    </div>
                </div>

                <div class="resource-table-wrap mt-4">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th class="text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($dataServicios as $row)
                                    <tr>
                                        <td>{{ $row->nombreServicio }}</td>
                                        <td class="text-right">
                                            <div class="resource-actions justify-content-end">
                                                <a class="btn btn-outline-dark btn-sm" href="{{ route('tipo_servicio.edit', $row->id_servicio) }}" title="Editar servicio">
                                                    <i class="fas fa-pen"></i>
                                                </a>
                                                <a class="btn btn-outline-danger btn-sm" href="{{ route('tipo_servicio.delete', $row->id_servicio) }}" title="Eliminar servicio">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="resource-empty">No hay servicios registrados todavía.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>
@stop
