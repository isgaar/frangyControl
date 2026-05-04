@extends('layouts.dashboard')

@section('title', 'Panel de control')

@php
    $pageTitle = 'Panel de control';
    $pageSubtitle = 'Resumen operativo del taller, accesos rápidos y órdenes recientes.';
    $breadcrumbs = [
        ['label' => 'Panel'],
    ];
@endphp

@section('content')
<div class="resource-page">

    {{-- ── Hero / cabecera ─────────────────────────────────────────────── --}}
    <section class="resource-hero">
        <div class="resource-hero__top">
            <div class="resource-hero__copy">
                <span class="resource-hero__eyebrow" style="font-size:0.78rem;">Vista general</span>
                <h1 class="resource-hero__title" style="font-size:1.5rem; font-weight:800;">Operación del taller</h1>
                <p style="font-size:0.9rem;">Consulta métricas clave, revisa órdenes recientes y entra rápido a los módulos más usados.</p>
            </div>
            <div class="resource-hero__actions">
                @foreach ($quickActions as $action)
                    <a href="{{ route($action['route']) }}"
                       class="btn {{ $loop->first ? 'btn-primary' : 'btn-outline-light' }}"
                       style="font-size:0.9rem; font-weight:{{ $loop->first ? '800' : '600' }};">
                        <i class="{{ $action['icon'] }} me-1"></i> {{ $action['label'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- KPIs                                                               --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div class="row g-3 mt-1 mb-3">

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 rounded-3 h-100 overflow-hidden">
                <div class="card-body pb-2">
                    <p class="text-uppercase text-muted mb-1" style="font-size:0.65rem; letter-spacing:.06em; font-weight:700;">Ventas del mes</p>
                    <p class="fw-medium mb-1" style="font-size:1.4rem; font-weight:800; line-height:1;">$148,320</p>
                    <p class="mb-0 d-flex align-items-center gap-1" style="font-size:0.75rem; color:#6c757d;">
                        <span class="badge rounded-2" style="font-size:0.65rem; font-weight:700; background:#d1e7dd; color:#0a3622;">+12.4 %</span>
                        vs mes anterior
                    </p>
                </div>
                <div style="height:3px; background:#0d6efd; width:72%;"></div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 rounded-3 h-100 overflow-hidden">
                <div class="card-body pb-2">
                    <p class="text-uppercase text-muted mb-1" style="font-size:0.65rem; letter-spacing:.06em; font-weight:700;">Órdenes abiertas</p>
                    <p class="fw-medium mb-1" style="font-size:1.4rem; font-weight:800; line-height:1;">37</p>
                    <p class="mb-0 d-flex align-items-center gap-1" style="font-size:0.75rem; color:#6c757d;">
                        <span class="badge rounded-2" style="font-size:0.65rem; font-weight:700; background:#fff3cd; color:#664d03;">+3</span>
                        desde ayer
                    </p>
                </div>
                <div style="height:3px; background:#ffc107; width:45%;"></div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 rounded-3 h-100 overflow-hidden">
                <div class="card-body pb-2">
                    <p class="text-uppercase text-muted mb-1" style="font-size:0.65rem; letter-spacing:.06em; font-weight:700;">Clientes activos</p>
                    <p class="fw-medium mb-1" style="font-size:1.4rem; font-weight:800; line-height:1;">284</p>
                    <p class="mb-0 d-flex align-items-center gap-1" style="font-size:0.75rem; color:#6c757d;">
                        <span class="badge rounded-2" style="font-size:0.65rem; font-weight:700; background:#d1e7dd; color:#0a3622;">+8</span>
                        este mes
                    </p>
                </div>
                <div style="height:3px; background:#198754; width:84%;"></div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 rounded-3 h-100 overflow-hidden">
                <div class="card-body pb-2">
                    <p class="text-uppercase text-muted mb-1" style="font-size:0.65rem; letter-spacing:.06em; font-weight:700;">Ticket promedio</p>
                    <p class="fw-medium mb-1" style="font-size:1.4rem; font-weight:800; line-height:1;">$4,010</p>
                    <p class="mb-0 d-flex align-items-center gap-1" style="font-size:0.75rem; color:#6c757d;">
                        <span class="badge rounded-2" style="font-size:0.65rem; font-weight:700; background:#f8d7da; color:#58151c;">−2.1 %</span>
                        vs mes anterior
                    </p>
                </div>
                <div style="height:3px; background:#dc3545; width:58%;"></div>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- Alertas operativas                                                  --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    @if ($operationalAlerts->isNotEmpty())
    <div class="row g-3 mb-3">
        <div class="col-12">
            <div class="card border-0 rounded-3">
                <div class="card-body">
                    <p class="text-uppercase text-muted mb-1" style="font-size:0.65rem; letter-spacing:.06em; font-weight:700;">Atención</p>
                    <h6 class="fw-medium mb-3" style="font-size:0.84rem; font-weight:800;">Alertas operativas</h6>
                    <div class="row g-2">
                        @foreach ($operationalAlerts as $alert)
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="alert alert-{{ $alert['tone'] }} mb-0 py-2 px-3" style="font-size:0.84rem;">
                                <strong style="font-size:0.84rem;">{{ $alert['title'] }}: {{ $alert['count'] }}</strong>
                                <p class="mb-0 mt-1" style="font-size:0.78rem;">{{ $alert['message'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- Fila 1: Barras semanales + Donut                                    --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div class="row g-3 mb-3">

        <div class="col-12 col-lg-7">
            <div class="card border-0 rounded-3 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <p class="text-uppercase text-muted mb-0" style="font-size:0.65rem; letter-spacing:.06em; font-weight:700;">Gráfica</p>
                            <p class="fw-medium mb-0" style="font-size:0.84rem; font-weight:800;">Ingresos semanales</p>
                            <p class="text-muted mb-0" style="font-size:0.75rem;">Semana actual vs semana anterior</p>
                        </div>
                        <a href="#" class="text-primary" style="font-size:0.75rem; text-decoration:none;">Ver reporte →</a>
                    </div>

                    @php
                        $bars = [
                            ['lbl'=>'Lun','cur'=>52,'pre'=>40],
                            ['lbl'=>'Mar','cur'=>68,'pre'=>55],
                            ['lbl'=>'Mié','cur'=>45,'pre'=>50],
                            ['lbl'=>'Jue','cur'=>80,'pre'=>62],
                            ['lbl'=>'Vie','cur'=>93,'pre'=>75],
                            ['lbl'=>'Sáb','cur'=>70,'pre'=>55],
                            ['lbl'=>'Dom','cur'=>30,'pre'=>20],
                        ];
                    @endphp

                    <div class="d-flex gap-2" style="height:120px; align-items:flex-end;">
                        @foreach ($bars as $b)
                        <div class="d-flex flex-column align-items-center flex-fill" style="height:100%; justify-content:flex-end; gap:2px;">
                            <div class="w-100" style="height:{{ $b['cur'] }}%; background:#0d6efd; border-radius:3px 3px 0 0; min-height:4px;"></div>
                            <div class="w-100" style="height:{{ $b['pre'] }}%; background:#e9ecef; border-radius:3px 3px 0 0; min-height:4px;"></div>
                            <span class="text-muted text-center" style="font-size:0.65rem; margin-top:4px;">{{ $b['lbl'] }}</span>
                        </div>
                        @endforeach
                    </div>

                    <div class="d-flex gap-3 mt-2">
                        <span class="d-flex align-items-center gap-1 text-muted" style="font-size:0.75rem;">
                            <span style="width:8px; height:8px; border-radius:50%; background:#0d6efd; display:inline-block; flex-shrink:0;"></span>
                            Esta semana
                        </span>
                        <span class="d-flex align-items-center gap-1 text-muted" style="font-size:0.75rem;">
                            <span style="width:8px; height:8px; border-radius:50%; background:#e9ecef; border:.5px solid #ced4da; display:inline-block; flex-shrink:0;"></span>
                            Semana anterior
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="card border-0 rounded-3 h-100">
                <div class="card-body">
                    <p class="text-uppercase text-muted mb-0" style="font-size:0.65rem; letter-spacing:.06em; font-weight:700;">Distribución</p>
                    <p class="fw-medium mb-0" style="font-size:0.84rem; font-weight:800;">Servicios por categoría</p>
                    <p class="text-muted mb-3" style="font-size:0.75rem;">Distribución del mes</p>

                    <div class="d-flex align-items-center gap-3">
                        <svg width="88" height="88" viewBox="0 0 88 88" style="flex-shrink:0;">
                            <circle cx="44" cy="44" r="32" fill="none" stroke="#e9ecef" stroke-width="14"/>
                            <circle cx="44" cy="44" r="32" fill="none" stroke="#0d6efd" stroke-width="14"
                                    stroke-dasharray="80 121" stroke-dashoffset="0"
                                    transform="rotate(-90 44 44)"/>
                            <circle cx="44" cy="44" r="32" fill="none" stroke="#198754" stroke-width="14"
                                    stroke-dasharray="45 156" stroke-dashoffset="-80"
                                    transform="rotate(-90 44 44)"/>
                            <circle cx="44" cy="44" r="32" fill="none" stroke="#dc3545" stroke-width="14"
                                    stroke-dasharray="25 176" stroke-dashoffset="-125"
                                    transform="rotate(-90 44 44)"/>
                            <circle cx="44" cy="44" r="32" fill="none" stroke="#ffc107" stroke-width="14"
                                    stroke-dasharray="16 185" stroke-dashoffset="-150"
                                    transform="rotate(-90 44 44)"/>
                            <text x="44" y="41" text-anchor="middle" font-size="13" font-weight="700" fill="#212529">201</text>
                            <text x="44" y="53" text-anchor="middle" font-size="10" fill="#6c757d">órdenes</text>
                        </svg>

                        <div class="flex-fill d-flex flex-column gap-2">
                            @php
                                $cats = [
                                    ['name'=>'Mecánica',    'pct'=>40, 'color'=>'#0d6efd'],
                                    ['name'=>'Eléctrico',   'pct'=>22, 'color'=>'#198754'],
                                    ['name'=>'Hojalatería', 'pct'=>13, 'color'=>'#dc3545'],
                                    ['name'=>'Diagnóstico', 'pct'=> 8, 'color'=>'#ffc107'],
                                ];
                            @endphp
                            @foreach ($cats as $c)
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="d-flex align-items-center gap-1" style="font-size:0.78rem;">
                                        <span style="width:7px; height:7px; border-radius:50%; background:{{ $c['color'] }}; display:inline-block; flex-shrink:0;"></span>
                                        {{ $c['name'] }}
                                    </span>
                                    <span class="text-muted" style="font-size:0.75rem;">{{ $c['pct'] }}%</span>
                                </div>
                                <div style="height:3px; background:#e9ecef; border-radius:2px;">
                                    <div style="height:3px; width:{{ $c['pct'] }}%; background:{{ $c['color'] }}; border-radius:2px;"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- Fila 2: Órdenes recientes + Actividad                              --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div class="row g-3 mb-3">

        <div class="col-12 col-lg-7">
            <div class="card border-0 rounded-3 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <p class="text-uppercase text-muted mb-0" style="font-size:0.65rem; letter-spacing:.06em; font-weight:700;">Actividad</p>
                            <p class="fw-medium mb-0" style="font-size:0.84rem; font-weight:800;">Órdenes recientes</p>
                            <p class="text-muted mb-0" style="font-size:0.75rem;">Últimas 5 órdenes registradas</p>
                        </div>
                        <a href="{{ route('ordenes.index') }}" class="text-primary" style="font-size:0.75rem; text-decoration:none;">Ver todas →</a>
                    </div>

                    <table class="table table-hover mb-0 align-middle" style="font-size:0.78rem;">
                        <thead>
                            <tr>
                                <th class="border-top-0 text-uppercase text-muted fw-medium ps-0"
                                    style="font-size:0.65rem; letter-spacing:.06em; font-weight:700; border-bottom-width:1px;">Folio</th>
                                <th class="border-top-0 text-uppercase text-muted fw-medium"
                                    style="font-size:0.65rem; letter-spacing:.06em; font-weight:700; border-bottom-width:1px;">Cliente</th>
                                <th class="border-top-0 text-uppercase text-muted fw-medium"
                                    style="font-size:0.65rem; letter-spacing:.06em; font-weight:700; border-bottom-width:1px;">Servicio</th>
                                <th class="border-top-0 text-uppercase text-muted fw-medium"
                                    style="font-size:0.65rem; letter-spacing:.06em; font-weight:700; border-bottom-width:1px;">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentOrders as $order)
                            <tr>
                                <td class="ps-0" style="font-family:monospace; font-size:0.75rem;">#{{ $order['id'] }}</td>
                                <td style="font-size:0.78rem;">{{ $order['cliente'] }}</td>
                                <td class="text-muted" style="font-size:0.78rem;">{{ $order['servicio'] }}</td>
                                <td>
                                    @php
                                        $map = [
                                            'Completado' => ['bg'=>'#d1e7dd','color'=>'#0a3622'],
                                            'En proceso' => ['bg'=>'#cfe2ff','color'=>'#052c65'],
                                            'Pendiente'  => ['bg'=>'#e9ecef','color'=>'#495057'],
                                            'En espera'  => ['bg'=>'#fff3cd','color'=>'#664d03'],
                                        ];
                                        $s = $map[$order['status']] ?? ['bg'=>'#e9ecef','color'=>'#495057'];
                                    @endphp
                                    <span class="badge rounded-2"
                                          style="font-size:0.65rem; font-weight:700; background:{{ $s['bg'] }}; color:{{ $s['color'] }};">
                                        {{ $order['status'] }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-muted text-center py-3" style="font-size:0.78rem;">
                                    Sin órdenes recientes.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="card border-0 rounded-3 h-100">
                <div class="card-body">
                    <p class="text-uppercase text-muted mb-0" style="font-size:0.65rem; letter-spacing:.06em; font-weight:700;">Sistema</p>
                    <p class="fw-medium mb-3" style="font-size:0.84rem; font-weight:800;">Actividad reciente</p>

                    @php
                        $feed = [
                            ['color'=>'#0d6efd','title'=>'Orden #1084 completada',             'meta'=>'Carlos Ramírez · Hace 12 min'],
                            ['color'=>'#198754','title'=>'Nuevo cliente registrado',            'meta'=>'Sandra Vega · Hace 34 min'],
                            ['color'=>'#ffc107','title'=>'Inventario bajo: Filtros de aceite', 'meta'=>'Sistema · Hace 1 h'],
                            ['color'=>'#dc3545','title'=>'Orden #1079 cancelada',              'meta'=>'Miguel A. Flores · Hace 2 h'],
                        ];
                    @endphp

                    @foreach ($feed as $item)
                    <div class="d-flex gap-2 {{ $loop->last ? '' : 'mb-3' }}">
                        <div class="d-flex flex-column align-items-center" style="width:16px; padding-top:3px; flex-shrink:0;">
                            <div style="width:8px; height:8px; border-radius:50%; background:{{ $item['color'] }}; flex-shrink:0;"></div>
                            @if (!$loop->last)
                            <div style="width:1px; flex:1; background:#dee2e6; margin-top:4px; min-height:20px;"></div>
                            @endif
                        </div>
                        <div class="{{ $loop->last ? '' : 'pb-3' }} flex-fill" style="{{ $loop->last ? '' : 'border-bottom:.5px solid #f0f0f0;' }}">
                            <p class="mb-0 fw-medium" style="font-size:0.78rem; font-weight:800;">{{ $item['title'] }}</p>
                            <p class="mb-0 text-muted" style="font-size:0.75rem;">{{ $item['meta'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- Fila 3: Metas + Inventario crítico                                  --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div class="row g-3 mb-3">

        <div class="col-12 col-lg-6">
            <div class="card border-0 rounded-3 h-100">
                <div class="card-body">
                    <p class="text-uppercase text-muted mb-0" style="font-size:0.65rem; letter-spacing:.06em; font-weight:700;">Progreso</p>
                    <p class="fw-medium mb-3" style="font-size:0.84rem; font-weight:800;">Metas del mes</p>

                    @php
                        $targets = [
                            ['name'=>'Órdenes completadas',       'pct'=>78, 'color'=>'#0d6efd'],
                            ['name'=>'Ingresos totales',           'pct'=>62, 'color'=>'#198754'],
                            ['name'=>'Satisfacción de cliente',    'pct'=>91, 'color'=>'#1D9E75'],
                            ['name'=>'Nuevos clientes',            'pct'=>45, 'color'=>'#ffc107'],
                            ['name'=>'Tiempo promedio de entrega', 'pct'=>83, 'color'=>'#d63384'],
                        ];
                    @endphp

                    <div class="d-flex flex-column gap-3">
                        @foreach ($targets as $t)
                        <div>
                            <div class="d-flex justify-content-between mb-1">
                                <span style="font-size:0.78rem;">{{ $t['name'] }}</span>
                                <span class="text-muted" style="font-size:0.75rem;">{{ $t['pct'] }}%</span>
                            </div>
                            <div class="progress" style="height:5px; border-radius:3px;">
                                <div class="progress-bar" role="progressbar"
                                     style="width:{{ $t['pct'] }}%; background:{{ $t['color'] }};"
                                     aria-valuenow="{{ $t['pct'] }}" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card border-0 rounded-3 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <p class="text-uppercase text-muted mb-0" style="font-size:0.65rem; letter-spacing:.06em; font-weight:700;">Almacén</p>
                            <p class="fw-medium mb-0" style="font-size:0.84rem; font-weight:800;">Inventario crítico</p>
                            <p class="text-muted mb-0" style="font-size:0.75rem;">Artículos con stock bajo</p>
                        </div>
                        <a href="#" class="text-primary" style="font-size:0.75rem; text-decoration:none;">Gestionar →</a>
                    </div>

                    @php
                        $items = [
                            ['name'=>'Filtros de aceite',       'stock'=>4, 'max'=>25, 'color'=>'#dc3545'],
                            ['name'=>'Pastillas de freno',      'stock'=>7, 'max'=>25, 'color'=>'#ffc107'],
                            ['name'=>'Bujías NGK',              'stock'=>5, 'max'=>25, 'color'=>'#dc3545'],
                            ['name'=>'Líquido de frenos',       'stock'=>9, 'max'=>25, 'color'=>'#ffc107'],
                            ['name'=>'Correas de distribución', 'stock'=>3, 'max'=>25, 'color'=>'#dc3545'],
                        ];
                    @endphp

                    <div class="d-flex flex-column gap-3">
                        @foreach ($items as $item)
                        <div>
                            <div class="d-flex justify-content-between mb-1">
                                <span style="font-size:0.78rem;">{{ $item['name'] }}</span>
                                <span class="text-muted" style="font-size:0.75rem;">{{ $item['stock'] }} uds</span>
                            </div>
                            <div class="progress" style="height:4px; border-radius:3px;">
                                <div class="progress-bar" role="progressbar"
                                     style="width:{{ round($item['stock']/$item['max']*100) }}%; background:{{ $item['color'] }};"
                                     aria-valuenow="{{ $item['stock'] }}" aria-valuemin="0" aria-valuemax="{{ $item['max'] }}">
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- Listado operativo con buscador y paginación                         --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <section class="resource-panel">
        <div class="resource-panel__header">
            <div>
                <span class="resource-panel__eyebrow" style="font-size:0.78rem;">Órdenes</span>
                <h2 class="resource-panel__title" style="font-size:1.1rem; font-weight:800;">Listado operativo</h2>
                <p class="resource-panel__copy" style="font-size:0.84rem;">Filtra por cliente, vehículo, placas, servicio, responsable o folio.</p>
            </div>
        </div>

        <form action="{{ route('panel.index') }}" method="get" class="resource-toolbar mt-4">
            <div class="resource-toolbar__field">
                <label for="search" class="form-label" style="font-size:0.84rem; font-weight:600;">Buscar</label>
                <input id="search" type="text" name="search" class="form-control"
                       style="font-size:0.9rem;"
                       value="{{ $search }}" placeholder="Cliente, placas, servicio o folio">
            </div>
            <div class="resource-toolbar__field">
                <label for="limit" class="form-label" style="font-size:0.84rem; font-weight:600;">Mostrar</label>
                <select id="limit" name="limit" class="form-select" style="font-size:0.9rem;">
                    @foreach ([5, 10, 15, 25] as $option)
                        <option value="{{ $option }}" @selected((int) $limit === $option)>{{ $option }} registros</option>
                    @endforeach
                </select>
            </div>
            <div class="resource-toolbar__field">
                <label for="order" class="form-label" style="font-size:0.84rem; font-weight:600;">Orden</label>
                <select id="order" name="order" class="form-select" style="font-size:0.9rem;">
                    <option value="desc" @selected($order === 'desc')>Más recientes</option>
                    <option value="asc"  @selected($order === 'asc')>Más antiguas</option>
                </select>
            </div>
            <div class="resource-toolbar__actions">
                <button type="submit" class="btn btn-primary" style="font-size:0.9rem; font-weight:800;">
                    <i class="fas fa-search me-1"></i> Aplicar
                </button>
                <a href="{{ route('panel.index') }}" class="btn btn-outline-dark" style="font-size:0.9rem; font-weight:600;">
                    <i class="fas fa-undo-alt me-1"></i> Limpiar
                </a>
            </div>
        </form>

        <div class="resource-table-wrap mt-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size:0.9rem;">
                    <thead style="font-size:0.82rem;">
                        <tr>
                            <th>Folio</th>
                            <th>Cliente</th>
                            <th>Vehículo</th>
                            <th>Servicio</th>
                            <th>Estado</th>
                            <th class="text-end">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ordenes as $orden)
                        <tr>
                            <td style="font-size:0.9rem;">#{{ $orden->id_ordenes }}</td>
                            <td style="font-size:0.9rem;">{{ optional($orden->cliente)->nombreCompleto ?? 'Sin cliente' }}</td>
                            <td style="font-size:0.9rem;">
                                {{ optional($orden->vehiculo)->marca ?? 'Sin vehículo' }}
                                @if (!empty($orden->placas))
                                    <span class="d-block text-muted" style="font-size:0.84rem;">{{ $orden->placas }}</span>
                                @endif
                            </td>
                            <td style="font-size:0.9rem;">{{ optional($orden->servicio)->nombreServicio ?? 'Sin servicio' }}</td>
                            <td>
                                <span class="badge text-bg-secondary" style="font-size:0.78rem; font-weight:700;">{{ $orden->status ?? 'Sin estado' }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('ordenes.show', $orden->id_ordenes) }}"
                                   class="btn btn-outline-dark btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <div class="resource-empty mb-0" style="font-size:0.9rem;">No hay órdenes con los filtros actuales.</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center flex-wrap mt-4" style="gap:.75rem;">
            <p class="mb-0 text-muted" style="font-size:0.84rem;">Mostrando {{ $ordenes->count() }} orden(es) en esta página.</p>
            {{ $ordenes->links('pagination::bootstrap-5') }}
        </div>
    </section>

</div>
@endsection