@extends('layouts.dashboard')

@section('title', 'Catálogos')

@php
    $flashType = session('status_type');
    $flashClass = match ($flashType) {
        'success' => 'success',
        'warning' => 'warning',
        default => 'danger',
    };

    $modalToOpen = old('_catalog_modal', request('open'));
@endphp

@section('content')
    @if (session('status'))
        <div class="alert alert-{{ $flashClass }} alert-dismissible fade show shadow-sm" role="alert">
            <strong>{{ session('status') }}</strong>
            <button type="button" class="close" data-bs-dismiss="alert" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <strong>Revisa los campos del modal antes de guardar.</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-bs-dismiss="alert" aria-label="Cerrar">
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
                    <p>Administra marcas, tipos de vehículo y servicios desde una sola pantalla, sin saltar entre módulos.</p>
                </div>

                <div class="resource-hero__actions">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#brandModal">
                        <i class="fas fa-car-side me-1"></i> Nueva marca
                    </button>
                    <button type="button" class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#typeModal">
                        <i class="fas fa-truck me-1"></i> Nuevo tipo
                    </button>
                    <button type="button" class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#serviceModal">
                        <i class="fas fa-tools me-1"></i> Nuevo servicio
                    </button>
                </div>
            </div>

            <div class="resource-metrics">
                <article class="resource-metric">
                    <span class="resource-metric__label">Marcas</span>
                    <p class="resource-metric__value">{{ $catalogTotals['brands'] }}</p>
                    <p class="resource-metric__copy">Catálogo disponible para capturar unidades.</p>
                </article>
                <article class="resource-metric">
                    <span class="resource-metric__label">Tipos</span>
                    <p class="resource-metric__value">{{ $catalogTotals['types'] }}</p>
                    <p class="resource-metric__copy">Clasificaciones operativas listas para usar.</p>
                </article>
                <article class="resource-metric">
                    <span class="resource-metric__label">Servicios</span>
                    <p class="resource-metric__value">{{ $catalogTotals['services'] }}</p>
                    <p class="resource-metric__copy">Opciones base para nuevas órdenes de servicio.</p>
                </article>
            </div>
        </section>

        <div class="resource-catalog-grid">
            <section class="resource-overview-card" id="marcas">
                <div class="resource-panel__header">
                    <div>
                        <span class="resource-panel__eyebrow">Marcas</span>
                        <h2 class="resource-overview-card__title">Marcas de vehículos</h2>
                        <p class="resource-panel__copy">{{ $dataVehiculos->count() }} resultado(s) en esta vista.</p>
                    </div>

                    <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#brandModal">
                        <i class="fas fa-plus me-1"></i> Agregar
                    </button>
                </div>

                <form action="{{ route('catalogos.index') }}#marcas" method="get" class="resource-toolbar mt-4">
                    <div class="resource-toolbar__field">
                        <label for="brand_search">Buscar marca</label>
                        <input id="brand_search" name="brand_search" type="text" class="form-control" value="{{ $brandSearch }}" placeholder="Ejemplo: Toyota">
                    </div>
                    <div class="resource-toolbar__actions">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search me-1"></i> Buscar</button>
                        <a href="{{ route('catalogos.index') }}#marcas" class="btn btn-outline-dark">Limpiar</a>
                    </div>
                </form>

                <div class="resource-table-wrap mt-4">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Marca</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($dataVehiculos as $row)
                                    <tr>
                                        <td>{{ $row->marca }}</td>
                                        <td class="text-end">
                                            <div class="resource-actions justify-content-end">
                                                <a class="btn btn-outline-dark btn-sm" href="{{ route('catalogos.marcas.edit', $row->id_vehiculo) }}" title="Editar marca">
                                                    <i class="fas fa-pen"></i>
                                                </a>
                                                <a class="btn btn-outline-danger btn-sm" href="{{ route('catalogos.marcas.delete', $row->id_vehiculo) }}" title="Eliminar marca">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="resource-empty">No hay marcas para mostrar.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <section class="resource-overview-card" id="tipos">
                <div class="resource-panel__header">
                    <div>
                        <span class="resource-panel__eyebrow">Clasificación</span>
                        <h2 class="resource-overview-card__title">Tipos de vehículo</h2>
                        <p class="resource-panel__copy">{{ $dataTiposVehiculos->count() }} resultado(s) en esta vista.</p>
                    </div>

                    <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#typeModal">
                        <i class="fas fa-plus me-1"></i> Agregar
                    </button>
                </div>

                <form action="{{ route('catalogos.index') }}#tipos" method="get" class="resource-toolbar mt-4">
                    <div class="resource-toolbar__field">
                        <label for="type_search">Buscar tipo</label>
                        <input id="type_search" name="type_search" type="text" class="form-control" value="{{ $typeSearch }}" placeholder="Ejemplo: SUV">
                    </div>
                    <div class="resource-toolbar__actions">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search me-1"></i> Buscar</button>
                        <a href="{{ route('catalogos.index') }}#tipos" class="btn btn-outline-dark">Limpiar</a>
                    </div>
                </form>

                <div class="resource-table-wrap mt-4">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($dataTiposVehiculos as $row)
                                    <tr>
                                        <td>{{ $row->tipo }}</td>
                                        <td class="text-end">
                                            <div class="resource-actions justify-content-end">
                                                <a class="btn btn-outline-dark btn-sm" href="{{ route('catalogos.tipos_vehiculo.edit', $row->id_tvehiculo) }}" title="Editar tipo">
                                                    <i class="fas fa-pen"></i>
                                                </a>
                                                <a class="btn btn-outline-danger btn-sm" href="{{ route('catalogos.tipos_vehiculo.delete', $row->id_tvehiculo) }}" title="Eliminar tipo">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="resource-empty">No hay tipos de vehículo para mostrar.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <section class="resource-overview-card" id="servicios">
                <div class="resource-panel__header">
                    <div>
                        <span class="resource-panel__eyebrow">Servicios</span>
                        <h2 class="resource-overview-card__title">Nombres de servicios</h2>
                        <p class="resource-panel__copy">{{ $dataServicios->count() }} resultado(s) en esta vista.</p>
                    </div>

                    <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#serviceModal">
                        <i class="fas fa-plus me-1"></i> Agregar
                    </button>
                </div>

                <form action="{{ route('catalogos.index') }}#servicios" method="get" class="resource-toolbar mt-4">
                    <div class="resource-toolbar__field">
                        <label for="service_search">Buscar servicio</label>
                        <input id="service_search" name="service_search" type="text" class="form-control" value="{{ $serviceSearch }}" placeholder="Ejemplo: Afinación">
                    </div>
                    <div class="resource-toolbar__actions">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search me-1"></i> Buscar</button>
                        <a href="{{ route('catalogos.index') }}#servicios" class="btn btn-outline-dark">Limpiar</a>
                    </div>
                </form>

                <div class="resource-table-wrap mt-4">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($dataServicios as $row)
                                    <tr>
                                        <td>{{ $row->nombreServicio }}</td>
                                        <td class="text-end">
                                            <div class="resource-actions justify-content-end">
                                                <a class="btn btn-outline-dark btn-sm" href="{{ route('catalogos.servicios.edit', $row->id_servicio) }}" title="Editar servicio">
                                                    <i class="fas fa-pen"></i>
                                                </a>
                                                <a class="btn btn-outline-danger btn-sm" href="{{ route('catalogos.servicios.delete', $row->id_servicio) }}" title="Eliminar servicio">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="resource-empty">No hay servicios para mostrar.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <div class="modal fade" id="brandModal" tabindex="-1" aria-labelledby="brandModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('catalogos.marcas.store') }}" method="post">
                    @csrf
                    <input type="hidden" name="_catalog_modal" value="brand">
                    <div class="modal-header">
                        <div>
                            <span class="resource-panel__eyebrow">Marcas</span>
                            <h2 class="modal-title fs-5" id="brandModalLabel">Agregar marcas de vehículo</h2>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div id="brandFields" class="resource-kv">
                            <div class="resource-kv__item">
                                <label for="brand-0" class="form-label">Marca</label>
                                <input id="brand-0" class="form-control" name="marcas[]" type="text" required oninput="formatCatalogInput(this, false)">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-dark" data-catalog-add="brand">
                            <i class="fas fa-plus me-1"></i> Agregar otra
                        </button>
                        <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar marcas</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="typeModal" tabindex="-1" aria-labelledby="typeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('catalogos.tipos_vehiculo.store') }}" method="post">
                    @csrf
                    <input type="hidden" name="_catalog_modal" value="type">
                    <div class="modal-header">
                        <div>
                            <span class="resource-panel__eyebrow">Tipos de vehículo</span>
                            <h2 class="modal-title fs-5" id="typeModalLabel">Agregar tipos de vehículo</h2>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div id="typeFields" class="resource-kv">
                            <div class="resource-kv__item">
                                <label for="type-0" class="form-label">Tipo</label>
                                <input id="type-0" class="form-control" name="tipos[]" type="text" required oninput="formatCatalogInput(this, true)">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-dark" data-catalog-add="type">
                            <i class="fas fa-plus me-1"></i> Agregar otro
                        </button>
                        <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar tipos</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="serviceModal" tabindex="-1" aria-labelledby="serviceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('catalogos.servicios.store') }}" method="post">
                    @csrf
                    <input type="hidden" name="_catalog_modal" value="service">
                    <div class="modal-header">
                        <div>
                            <span class="resource-panel__eyebrow">Servicios</span>
                            <h2 class="modal-title fs-5" id="serviceModalLabel">Agregar servicios</h2>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div id="serviceFields" class="resource-kv">
                            <div class="resource-kv__item">
                                <label for="service-0" class="form-label">Servicio</label>
                                <input id="service-0" class="form-control" name="tipos[]" type="text" required oninput="formatCatalogInput(this, true)">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-dark" data-catalog-add="service">
                            <i class="fas fa-plus me-1"></i> Agregar otro
                        </button>
                        <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar servicios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        function formatCatalogInput(input, allowNumbers) {
            var pattern = allowNumbers ? /[^A-Za-z0-9ÁÉÍÓÚÜÑáéíóúüñ\s]/g : /[^A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]/g;

            input.value = input.value
                .replace(pattern, '')
                .replace(/\s+/g, ' ')
                .trimStart()
                .replace(/\b\w/g, function(letter) {
                    return letter.toUpperCase();
                });
        }

        document.addEventListener('DOMContentLoaded', function () {
            var modalMap = {
                brand: { id: 'brandModal', fields: 'brandFields', label: 'Marca', name: 'marcas[]', allowNumbers: false },
                type: { id: 'typeModal', fields: 'typeFields', label: 'Tipo', name: 'tipos[]', allowNumbers: true },
                service: { id: 'serviceModal', fields: 'serviceFields', label: 'Servicio', name: 'tipos[]', allowNumbers: true }
            };

            document.querySelectorAll('[data-catalog-add]').forEach(function (button) {
                button.addEventListener('click', function () {
                    var key = button.getAttribute('data-catalog-add');
                    var config = modalMap[key];
                    var container = config ? document.getElementById(config.fields) : null;

                    if (!container) {
                        return;
                    }

                    var index = container.children.length;
                    var item = document.createElement('div');
                    var inputId = key + '-' + index;
                    item.className = 'resource-kv__item';
                    item.innerHTML = [
                        '<div class="d-flex justify-content-between align-items-center mb-2" style="gap:.75rem;">',
                        '<label class="form-label mb-0" for="' + inputId + '">' + config.label + '</label>',
                        '<button class="btn btn-outline-danger btn-sm" type="button" data-catalog-remove><i class="fas fa-trash"></i></button>',
                        '</div>',
                        '<input id="' + inputId + '" class="form-control" name="' + config.name + '" type="text" required>'
                    ].join('');

                    var input = item.querySelector('input');
                    input.addEventListener('input', function () {
                        formatCatalogInput(input, config.allowNumbers);
                    });
                    container.appendChild(item);
                    input.focus();
                });
            });

            document.addEventListener('click', function (event) {
                var removeButton = event.target.closest('[data-catalog-remove]');

                if (removeButton) {
                    removeButton.closest('.resource-kv__item').remove();
                }
            });

            var modalToOpen = @json($modalToOpen);
            var modalId = modalMap[modalToOpen] ? modalMap[modalToOpen].id : null;

            if (modalId && window.bootstrap) {
                bootstrap.Modal.getOrCreateInstance(document.getElementById(modalId)).show();
            }
        });
    </script>
@endsection
