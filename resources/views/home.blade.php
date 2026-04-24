@extends('adminlte::page')

@section('title', 'Dashboard')

@php
    $currentHour = (int) date('H');
    $timeOfDay = $currentHour >= 5 && $currentHour < 12 ? 'morning' : ($currentHour >= 12 && $currentHour < 18 ? 'afternoon' : 'evening');
    $timeOfDayClass = $timeOfDay;
    $greetingMessage = $timeOfDay === 'morning' ? 'Buenos días' : ($timeOfDay === 'afternoon' ? 'Buenas tardes' : 'Buenas noches');
    $greetingIcon = $timeOfDay === 'evening' ? asset('moon.svg') : asset('sun.svg');
    $username = auth()->user()->name ?? 'Usuario';
    $statusClasses = [
        'cancelada' => 'badge-danger',
        'finalizada' => 'badge-success',
        'en proceso' => 'badge-info',
    ];
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
@endphp

@section('content')
<style>
.morning {
    background-image: url('https://cdn.wallpapersafari.com/25/78/fHPIeb.png');
    background-size: cover;
    background-position: center;
}

.afternoon {
    background-image: url('https://images.hdqwalls.com/download/deer-forest-fox-sun-red-trees-birds-4k-30-1920x1080.jpg');
    background-size: cover;
    background-position: center;
}

.evening {
    background-image: url('https://i.pinimg.com/originals/f7/e8/6e/f7e86eb60f387760f268530e3f69eb50.png');
    background-size: cover;
    background-position: center;
}

.greeting-icon {
    width: 70px;
    height: 70px;
    margin-bottom: 10px;
}

.dashboard-hero {
    overflow: hidden;
    border: 0;
    position: relative;
}

.dashboard-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(11, 18, 32, 0.72), rgba(30, 58, 138, 0.42));
}

.dashboard-hero .card-body,
.dashboard-hero .card-footer {
    position: relative;
    z-index: 1;
}

.dashboard-hero .card-footer {
    background: rgba(11, 18, 32, 0.24);
    border-top: 1px solid rgba(255, 255, 255, 0.16);
}

.hero-meta {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.45rem 0.9rem;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.12);
    font-size: 0.9rem;
}

.stat-card {
    height: 100%;
    border: 0;
}

.stat-card .card-body {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}

.stat-card .stat-icon {
    width: 3rem;
    height: 3rem;
    border-radius: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 1.15rem;
    box-shadow: 0 12px 20px rgba(15, 23, 42, 0.18);
}

.stat-card .stat-value {
    font-size: 1.85rem;
    font-weight: 700;
    line-height: 1;
    color: #0f172a;
}

.stat-card .stat-label {
    margin-bottom: 0.2rem;
    font-weight: 600;
    color: #0f172a;
}

.stat-card .stat-caption {
    margin-bottom: 0;
    font-size: 0.88rem;
    color: #64748b;
}

.accent-primary {
    background: linear-gradient(135deg, #1e3a8a, #2563eb);
}

.accent-info {
    background: linear-gradient(135deg, #155e75, #0891b2);
}

.accent-success {
    background: linear-gradient(135deg, #166534, #22c55e);
}

.accent-warning {
    background: linear-gradient(135deg, #9a3412, #f59e0b);
}

.accent-dark {
    background: linear-gradient(135deg, #0f172a, #334155);
}

.recent-card {
    height: 100%;
}

.recent-card .card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.recent-list {
    display: grid;
    gap: 0.9rem;
}

.recent-item {
    display: block;
    padding: 0.95rem 1rem;
    border: 1px solid rgba(219, 227, 234, 0.9);
    border-radius: 14px;
    background: rgba(248, 250, 252, 0.82);
    color: inherit;
    transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
}

.recent-item:hover {
    transform: translateY(-2px);
    border-color: rgba(37, 99, 235, 0.28);
    box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08);
    color: inherit;
    text-decoration: none;
}

.recent-item-title {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    margin-bottom: 0.35rem;
    font-weight: 600;
}

.recent-item-meta {
    margin: 0;
    color: #64748b;
    font-size: 0.9rem;
}

.recent-item-subtitle {
    margin: 0.35rem 0 0;
    color: #334155;
    font-size: 0.92rem;
}

.section-link {
    font-size: 0.9rem;
    font-weight: 600;
}

.orders-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
}

@media (max-width: 767.98px) {
    .dashboard-hero .card-body {
        text-align: center;
    }

    .dashboard-hero .d-flex {
        justify-content: center !important;
    }

    .recent-item-title {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>

<div class="row">
    <div class="col-12 p-1">
        <div class="card text-white {{ $timeOfDayClass }} dashboard-hero">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between">
                    <div>
                        <img src="{{ $greetingIcon }}" alt="Greeting icon" class="greeting-icon">
                        <p class="text-uppercase mb-2 small font-weight-bold" style="letter-spacing: 0.12em;">Panel principal</p>
                        <h2 class="mb-2">{{ $greetingMessage }}, {{ $username }}</h2>
                        <p class="mb-0 text-white-50">Aquí tienes un resumen rápido de actividad, registros recientes y accesos directos del sistema.</p>
                    </div>
                    <div class="mt-3 mt-lg-0">
                        <span class="hero-meta">
                            <i class="far fa-clock"></i>
                            {{ now()->timezone(config('app.timezone'))->format('d/m/Y H:i') }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex flex-wrap justify-content-between">
                <span class="small text-white-50">Última actualización del panel en tiempo real</span>
                <a href="{{ route('ordenes.index') }}" class="text-white font-weight-semibold">Ver órdenes <i class="fas fa-arrow-right ml-1"></i></a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    @foreach ($stats as $stat)
        <div class="col-xl col-md-6 mb-3">
            <a href="{{ $stat['url'] }}" class="text-decoration-none">
                <div class="card stat-card">
                    <div class="card-body">
                        <div>
                            <p class="stat-label">{{ $stat['label'] }}</p>
                            <div class="stat-value">{{ number_format($stat['value']) }}</div>
                            <p class="stat-caption">{{ $stat['caption'] }}</p>
                        </div>
                        <span class="stat-icon accent-{{ $stat['accent'] }}">
                            <i class="{{ $stat['icon'] }}"></i>
                        </span>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>

@if (strpos($userAgent, 'Firefox') === false)
<div class="card">
    <div class="card-body" style="background-color: #FF7139; color: #fff;">
        <h5 class="card-title">
            <img src="https://play-lh.googleusercontent.com/l6ftn6BTu7Kfe8OdE4Itrdw5bTRVO3F_mTZH8xDa-FHO4m-lZAXmz5GxkXTMhqcF_y0"
                alt="Firefox Logo" width="30" height="30">
            Utiliza Firefox para una mejor experiencia gráfica
        </h5>
        <p class="card-text">Se recomienda utilizar el navegador Mozilla Firefox para aprovechar al máximo las
            características visuales de este sitio.</p>
    </div>
</div>
@endif

<div class="row">
    <div class="col-lg-4 mb-3">
        <div class="card recent-card">
            <div class="card-header">
                <h3 class="card-title mb-0">Últimas órdenes guardadas</h3>
                <a href="{{ route('ordenes.index') }}" class="section-link">Ver todas</a>
            </div>
            <div class="card-body">
                <div class="recent-list">
                    @forelse ($recentOrders as $recentOrder)
                        <a href="{{ $recentOrder['url'] }}" class="recent-item">
                            <div class="recent-item-title">
                                <span>Orden #{{ $recentOrder['id'] }}</span>
                                <span class="badge {{ $statusClasses[$recentOrder['status']] ?? 'badge-secondary' }}">{{ ucwords($recentOrder['status']) }}</span>
                            </div>
                            <p class="recent-item-meta">{{ $recentOrder['created_at'] }}</p>
                            <p class="recent-item-subtitle">{{ $recentOrder['cliente'] }} · {{ $recentOrder['vehiculo'] }}</p>
                            <p class="recent-item-meta">{{ $recentOrder['servicio'] }}</p>
                        </a>
                    @empty
                        <p class="text-muted mb-0">Todavía no hay órdenes registradas.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-3">
        <div class="card recent-card">
            <div class="card-header">
                <h3 class="card-title mb-0">Últimos clientes guardados</h3>
                <a href="{{ route('clientes.index') }}" class="section-link">Ver clientes</a>
            </div>
            <div class="card-body">
                <div class="recent-list">
                    @forelse ($recentClients as $client)
                        <a href="{{ route('clientes.show', $client->id_cliente) }}" class="recent-item">
                            <div class="recent-item-title">
                                <span>{{ $client->nombreCompleto }}</span>
                                <span class="badge badge-light">{{ optional($client->created_at)->format('d/m/Y') }}</span>
                            </div>
                            <p class="recent-item-meta">{{ optional($client->created_at)->format('H:i') ?: 'Sin hora' }}</p>
                            <p class="recent-item-subtitle">{{ $client->telefono }}</p>
                            <p class="recent-item-meta">{{ $client->correo }}</p>
                        </a>
                    @empty
                        <p class="text-muted mb-0">Todavía no hay clientes registrados.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-3">
        <div class="card recent-card">
            <div class="card-header">
                <h3 class="card-title mb-0">Últimos vehículos guardados</h3>
                <a href="{{ route('datosv.index') }}" class="section-link">Ver catálogo</a>
            </div>
            <div class="card-body">
                <div class="recent-list">
                    @forelse ($recentVehicles as $vehicle)
                        <a href="{{ route('datosv.index') }}" class="recent-item">
                            <div class="recent-item-title">
                                <span>{{ $vehicle->marca }}</span>
                                <span class="badge badge-light">{{ optional($vehicle->created_at)->format('d/m/Y') }}</span>
                            </div>
                            <p class="recent-item-meta">{{ optional($vehicle->created_at)->format('H:i') ?: 'Sin hora' }}</p>
                            <p class="recent-item-subtitle">Registro #{{ $vehicle->id_vehiculo }}</p>
                            <p class="recent-item-meta">Marca disponible en el catálogo</p>
                        </a>
                    @empty
                        <p class="text-muted mb-0">Todavía no hay vehículos registrados.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <h3 class="card-title mb-0">Detalle de últimas órdenes</h3>
            <a href="{{ route('ordenes.index') }}" class="section-link">Ir al panel completo</a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col" class="text-center">#</th>
                        <th scope="col" class="text-center">Estado</th>
                        <th scope="col" class="text-center">Cliente</th>
                        <th scope="col" class="text-center">Tipo de Servicio</th>
                        <th scope="col" class="text-center">Vehículo</th>
                        <th scope="col" class="text-center">Encargado/a</th>
                    </tr>
                </thead>
                <tbody class="table-group-divider">
                    @forelse ($ordenes as $row)
                        <tr>
                            <th scope="row">{{ $row->id_ordenes }}</th>
                            <td class="text-center">
                                <span class="badge {{ $statusClasses[$row->status] ?? 'badge-secondary' }}">{{ ucwords($row->status) }}</span>
                            </td>
                            <td class="text-center">{{ ucwords(optional($row->cliente)->nombreCompleto ?? 'Sin cliente') }}</td>
                            <td class="text-center">{{ optional($row->servicio)->nombreServicio ?? 'Sin servicio' }}</td>
                            <td class="text-center">{{ optional($row->vehiculo)->marca ?? 'Sin vehículo' }}</td>
                            <td class="text-center">{{ optional($row->user)->name ?? 'Sin asignar' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No hay órdenes para mostrar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer orders-card-footer">
        <div>
            <span class="text-muted">Mostrando {{ $ordenes->count() }} resultados recientes</span>
        </div>
        <a href="{{ route('ordenes.index') }}" class="font-weight-semibold">Ir al panel de órdenes <i class="fas fa-arrow-circle-right ml-1"></i></a>
    </div>
    @if ($ordenes->hasPages())
        <div class="card-footer bg-white">
            {{ $ordenes->links() }}
        </div>
    @endif
</div>

@stop

@section('css')
<link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
@stop
