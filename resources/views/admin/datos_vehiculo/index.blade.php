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
        {{-- Encabezado compacto (Estilo Órdenes) --}}
        <div class="page-header">
            <div>
                <span class="page-eyebrow">Catálogos base</span>
                <h1 class="page-title">Datos generales del taller</h1>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary" style="font-weight:800;" data-bs-toggle="modal" data-bs-target="#brandModal">
                    <i class="fas fa-car-side me-1"></i> Nueva marca
                </button>
                <button type="button" class="btn btn-outline-dark" style="font-weight:800;" data-bs-toggle="modal" data-bs-target="#typeModal">
                    <i class="fas fa-truck me-1"></i> Nuevo tipo
                </button>
                <button type="button" class="btn btn-outline-dark" style="font-weight:800;" data-bs-toggle="modal" data-bs-target="#serviceModal">
                    <i class="fas fa-tools me-1"></i> Nuevo servicio
                </button>
            </div>
        </div>

        {{-- Métricas compactas (Estilo Órdenes) --}}
        <div class="metrics-grid">
            <article class="metric-card">
                <span class="metric-card__label">Marcas</span>
                <p class="metric-card__value">{{ $catalogTotals['brands'] }}</p>
                <p class="metric-card__copy">Disponibles para capturar unidades</p>
            </article>
            <article class="metric-card">
                <span class="metric-card__label">Tipos</span>
                <p class="metric-card__value">{{ $catalogTotals['types'] }}</p>
                <p class="metric-card__copy">Clasificaciones operativas</p>
            </article>
            <article class="metric-card">
                <span class="metric-card__label">Servicios</span>
                <p class="metric-card__value">{{ $catalogTotals['services'] }}</p>
                <p class="metric-card__copy">Opciones base de servicio</p>
            </article>
        </div>

        {{-- ✅ SECCIÓN UNIFICADA: Filtros + Listado en un solo panel --}}
        <section class="resource-panel">

            {{-- Barra de filtros (Estilo Órdenes) --}}
            <form action="{{ route('catalogos.index') }}" method="get">
                <div class="filter-toolbar">
                    
                    {{-- Búsqueda Marca --}}
                    <div class="filter-search-box" style="min-width:180px;">
                        <input type="text" name="brand_search" value="{{ $brandSearch }}" placeholder="Buscar marca...">
                    </div>

                    {{-- Búsqueda Tipo --}}
                    <div class="filter-search-box" style="min-width:180px;">
                        <input type="text" name="type_search" value="{{ $typeSearch }}" placeholder="Buscar tipo...">
                    </div>

                    {{-- Búsqueda Servicio --}}
                    <div class="filter-search-box" style="min-width:180px;">
                        <input type="text" name="service_search" value="{{ $serviceSearch }}" placeholder="Buscar servicio...">
                        <button type="submit" title="Buscar">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                        </button>
                    </div>

                    <select name="order" class="form-control">
                        <option value="asc" {{ $order === 'asc' ? 'selected' : '' }}>A-Z</option>
                        <option value="desc" {{ $order === 'desc' ? 'selected' : '' }}>Z-A</option>
                    </select>

                    <a href="{{ route('catalogos.index') }}" class="btn btn-outline-dark filter-btn-clear">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.5"/>
                        </svg>
                        Limpiar
                    </a>

                    @if ($brandSearch || $typeSearch || $serviceSearch || $order !== 'asc')
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
                    <strong>{{ $dataVehiculos->count() + $dataTiposVehiculos->count() + $dataServicios->count() }}</strong> registro(s) encontrados en esta vista.
                </p>
                <span class="badge-active-filter" style="background:var(--dashboard-surface-soft); color:var(--dashboard-text); border-color:var(--dashboard-border);">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M3 15h18M9 3v18"/>
                    </svg>
                    3 catálogos
                </span>
            </div>

            {{-- Grid de catálogos --}}
            <div class="resource-catalog-grid mt-4">

                {{-- Marcas --}}
                <section class="resource-catalog-block" id="marcas">
                    <div class="resource-panel__header">
                        <div>
                            <span class="resource-panel__eyebrow">Marcas</span>
                            <h3 class="resource-catalog-block__title">Marcas de vehículos</h3>
                            <p class="resource-panel__copy">{{ $dataVehiculos->count() }} resultado(s) en esta vista.</p>
                        </div>

                        <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#brandModal">
                            <i class="fas fa-plus me-1"></i> Agregar
                        </button>
                    </div>

                    <div class="resource-table-wrap mt-4">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 table-compact">
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

                {{-- Tipos de vehículo --}}
                <section class="resource-catalog-block" id="tipos">
                    <div class="resource-panel__header">
                        <div>
                            <span class="resource-panel__eyebrow">Clasificación</span>
                            <h3 class="resource-catalog-block__title">Tipos de vehículo</h3>
                            <p class="resource-panel__copy">{{ $dataTiposVehiculos->count() }} resultado(s) en esta vista.</p>
                        </div>

                        <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#typeModal">
                            <i class="fas fa-plus me-1"></i> Agregar
                        </button>
                    </div>

                    <div class="resource-table-wrap mt-4">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 table-compact">
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

                {{-- Servicios --}}
                <section class="resource-catalog-block" id="servicios">
                    <div class="resource-panel__header">
                        <div>
                            <span class="resource-panel__eyebrow">Servicios</span>
                            <h3 class="resource-catalog-block__title">Nombres de servicios</h3>
                            <p class="resource-panel__copy">{{ $dataServicios->count() }} resultado(s) en esta vista.</p>
                        </div>

                        <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#serviceModal">
                            <i class="fas fa-plus me-1"></i> Agregar
                        </button>
                    </div>

                    <div class="resource-table-wrap mt-4">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 table-compact">
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
        </section>
    </div>

    {{-- Modal: Marcas --}}
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

    {{-- Modal: Tipos --}}
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

    {{-- Modal: Servicios --}}
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