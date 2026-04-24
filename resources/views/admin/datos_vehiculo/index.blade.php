@extends('adminlte::page')

@section('title', 'Datos generales')

@php
    $flashType = session('status_type');
    $flashClass = match ($flashType) {
        'success' => 'success',
        'warning' => 'warning',
        default => 'danger',
    };
@endphp

@section('content_header')
    <div class="general-header d-flex flex-column flex-lg-row align-items-lg-center justify-content-between">
        <div>
            <h1 class="mb-1">Datos generales</h1>
            <p class="text-muted mb-0">Administra marcas, tipos de vehiculo y servicios desde un solo panel.</p>
        </div>

        <div class="general-header-actions d-flex">
            <a href="{{ route('datosv.create') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-save mr-1"></i> Guardado general
            </a>
        </div>
    </div>
@stop

@section('content')
    @if (session('status'))
        <div class="alert alert-{{ $flashClass }} alert-dismissible fade show shadow-sm" role="alert">
            <strong>{{ session('status') }}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="general-summary-card">
                <span class="general-summary-label">Marcas</span>
                <strong>{{ $dataVehiculos->count() }}</strong>
                <small>Catalogo disponible para capturar vehiculos.</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="general-summary-card">
                <span class="general-summary-label">Tipos</span>
                <strong>{{ $dataTiposVehiculos->count() }}</strong>
                <small>Clasificaciones como sedan, SUV o pickup.</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="general-summary-card">
                <span class="general-summary-label">Servicios</span>
                <strong>{{ $dataServicios->count() }}</strong>
                <small>Opciones base para ordenes de servicio.</small>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-4 col-lg-6 d-flex">
            <div class="card general-data-card w-100">
                <div class="card-header border-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="card-title mb-1">Marcas de vehiculos</h3>
                            <small class="text-muted">Catalogo para seleccionar la marca de la unidad.</small>
                        </div>
                        <a href="{{ route('datosv.createunique') }}" class="btn btn-outline-primary btn-sm" title="Agregar marca">
                            <i class="fas fa-plus mr-1"></i> Nueva
                        </a>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Marca</th>
                                    <th class="text-right general-actions-col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($dataVehiculos as $row)
                                    <tr>
                                        <td>{{ $row->marca }}</td>
                                        <td class="text-right">
                                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('datosv.edit', $row->id_vehiculo) }}" title="Editar marca">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <a class="btn btn-sm btn-outline-danger" href="{{ route('datosv.delete', $row->id_vehiculo) }}" title="Eliminar marca">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2">
                                            <div class="general-empty-state">
                                                No hay marcas registradas todavia.
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-6 d-flex">
            <div class="card general-data-card w-100">
                <div class="card-header border-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="card-title mb-1">Tipos de vehiculos</h3>
                            <small class="text-muted">Clasifica mejor cada unidad dentro del sistema.</small>
                        </div>
                        <a href="{{ route('tipo_vehiculo.create') }}" class="btn btn-outline-primary btn-sm" title="Agregar tipo">
                            <i class="fas fa-plus mr-1"></i> Nuevo
                        </a>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th class="text-right general-actions-col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($dataTiposVehiculos as $row)
                                    <tr>
                                        <td>{{ $row->tipo }}</td>
                                        <td class="text-right">
                                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('tipo_vehiculo.edit', $row->id_tvehiculo) }}" title="Editar tipo">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <a class="btn btn-sm btn-outline-danger" href="{{ route('tipo_vehiculo.delete', $row->id_tvehiculo) }}" title="Eliminar tipo">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2">
                                            <div class="general-empty-state">
                                                No hay tipos de vehiculo registrados todavia.
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-12 d-flex">
            <div class="card general-data-card w-100">
                <div class="card-header border-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="card-title mb-1">Nombres de servicios</h3>
                            <small class="text-muted">Servicios disponibles para usar dentro de las ordenes.</small>
                        </div>
                        <a href="{{ route('tipo_servicio.create') }}" class="btn btn-outline-primary btn-sm" title="Agregar servicio">
                            <i class="fas fa-plus mr-1"></i> Nuevo
                        </a>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th class="text-right general-actions-col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($dataServicios as $row)
                                    <tr>
                                        <td>{{ $row->nombreServicio }}</td>
                                        <td class="text-right">
                                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('tipo_servicio.edit', $row->id_servicio) }}" title="Editar servicio">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <a class="btn btn-sm btn-outline-danger" href="{{ route('tipo_servicio.delete', $row->id_servicio) }}" title="Eliminar servicio">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2">
                                            <div class="general-empty-state">
                                                No hay servicios registrados todavia.
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .general-header {
            gap: 1rem;
        }

        .general-header-actions {
            gap: 0.75rem;
        }

        .general-summary-card {
            height: 100%;
            padding: 1.1rem 1.2rem;
            border: 1px solid rgba(30, 41, 59, 0.08);
            border-radius: 1rem;
            background: linear-gradient(135deg, #ffffff, #f8fbff);
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
        }

        .general-summary-card strong {
            display: block;
            margin: 0.35rem 0;
            color: #1e3a8a;
            font-size: 2rem;
            line-height: 1;
        }

        .general-summary-card small {
            display: block;
            color: #64748b;
            font-size: 0.92rem;
            line-height: 1.5;
        }

        .general-summary-label {
            display: inline-block;
            color: #334155;
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .general-data-card {
            border: 0;
            border-radius: 1.15rem;
            box-shadow: 0 16px 36px rgba(15, 23, 42, 0.08);
        }

        .general-data-card .card-header {
            padding: 1.15rem 1.2rem 0.75rem;
            background: transparent;
        }

        .general-data-card .card-body {
            padding: 0 1.2rem 1.2rem;
        }

        .general-data-card .card-title {
            font-size: 1.2rem;
            font-weight: 700;
        }

        .general-data-card table thead th {
            border-top: 0;
            color: #475569;
            font-size: 0.9rem;
            font-weight: 700;
        }

        .general-data-card table tbody td {
            vertical-align: middle;
        }

        .general-actions-col {
            width: 112px;
        }

        .general-empty-state {
            padding: 1rem 0.25rem;
            color: #64748b;
            text-align: center;
        }

        @media (max-width: 991.98px) {
            .general-header,
            .general-header-actions {
                gap: 0.75rem;
            }
        }
    </style>
@stop
