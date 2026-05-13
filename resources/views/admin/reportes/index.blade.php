@extends('layouts.dashboard')

@section('title', 'Reportes Analíticos')

@section('content')
<div class="resource-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-0">Reportes Analíticos</h1>
            <p class="text-muted mb-0">Consulta métricas de ingresos y servicios por mes y año.</p>
        </div>
        <a href="{{ route('panel.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver al panel
        </a>
    </div>

    <div class="card border-0 rounded-3 mb-4 shadow-sm">
        <div class="card-body">
            <form action="{{ route('reportes.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="month" class="form-label fw-bold" style="font-size:0.85rem;">Mes</label>
                    <select name="month" id="month" class="form-select">
                        @php
                            $meses = [
                                1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                                5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                                9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                            ];
                        @endphp
                        @foreach($meses as $num => $nombre)
                            <option value="{{ $num }}" {{ $selectedMonth == $num ? 'selected' : '' }}>{{ $nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="year" class="form-label fw-bold" style="font-size:0.85rem;">Año</label>
                    <select name="year" id="year" class="form-select">
                        @foreach($availableYears as $year)
                            <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 d-flex gap-2">
                    <button type="submit" class="btn btn-primary fw-bold">
                        <i class="fas fa-filter me-1"></i> Filtrar
                    </button>
                    <a href="{{ route('reportes.pdf', ['month' => $selectedMonth, 'year' => $selectedYear]) }}" target="_blank" class="btn btn-danger fw-bold">
                        <i class="fas fa-file-pdf me-1"></i> Exportar PDF
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-7">
            <div class="card border-0 rounded-3 h-100 shadow-sm">
                <div class="card-body">
                    <p class="text-uppercase text-muted mb-0" style="font-size:0.65rem; letter-spacing:.06em; font-weight:700;">Gráfica</p>
                    <p class="fw-medium mb-0" style="font-size:1.1rem; font-weight:800;">Ingresos por semanas</p>
                    <p class="text-muted mb-4" style="font-size:0.85rem;">Mes actual ({{ ucfirst($monthName) }}) vs Mes anterior</p>

                    <div class="d-flex gap-2" style="height:200px; align-items:flex-end;">
                        @foreach ($weeklyBars as $b)
                        <div class="d-flex flex-column align-items-center flex-fill" style="height:100%; justify-content:flex-end; gap:2px;">
                            <div class="w-100 position-relative rounded-top" style="height:{{ max(2, $b['cur_pct']) }}%; background:var(--bs-primary);" title="{{ $b['cur'] }} órdenes actuales"></div>
                            <div class="w-100 position-relative rounded-top" style="height:{{ max(2, $b['pre_pct']) }}%; background:var(--bs-secondary-bg);" title="{{ $b['pre'] }} órdenes mes anterior"></div>
                            <span class="text-muted text-center fw-bold" style="font-size:0.75rem; margin-top:8px;">{{ $b['lbl'] }}</span>
                        </div>
                        @endforeach
                    </div>

                    <div class="d-flex gap-4 mt-4 justify-content-center">
                        <span class="d-flex align-items-center gap-2 text-muted fw-bold" style="font-size:0.85rem;">
                            <span style="width:12px; height:12px; border-radius:50%; background:var(--bs-primary); display:inline-block;"></span>
                            Este mes ({{ $totalCurrentMonth }})
                        </span>
                        <span class="d-flex align-items-center gap-2 text-muted fw-bold" style="font-size:0.85rem;">
                            <span style="width:12px; height:12px; border-radius:50%; background:var(--bs-secondary-bg); border:1px solid var(--bs-border-color); display:inline-block;"></span>
                            Mes anterior ({{ $totalPrevMonth }})
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="card border-0 rounded-3 h-100 shadow-sm">
                <div class="card-body">
                    <p class="text-uppercase text-muted mb-0" style="font-size:0.65rem; letter-spacing:.06em; font-weight:700;">Distribución</p>
                    <p class="fw-medium mb-0" style="font-size:1.1rem; font-weight:800;">Servicios por categoría</p>
                    <p class="text-muted mb-4" style="font-size:0.85rem;">Distribución en {{ ucfirst($monthName) }} {{ $selectedYear }}</p>

                    <div class="d-flex flex-column align-items-center gap-4">
                        <svg width="140" height="140" viewBox="0 0 88 88" style="flex-shrink:0;">
                            <circle cx="44" cy="44" r="32" fill="none" stroke="var(--bs-secondary-bg)" stroke-width="14"/>
                            @php
                                $circumference = 201.06;
                                $offset = 0;
                            @endphp
                            @foreach ($serviceDistribution as $c)
                                @php
                                    $dash = ($c['pct'] / 100) * $circumference;
                                @endphp
                                @if($dash > 0)
                                <circle cx="44" cy="44" r="32" fill="none" stroke="{{ $c['color'] }}" stroke-width="14"
                                        stroke-dasharray="{{ $dash }} {{ $circumference - $dash }}" stroke-dashoffset="{{ -$offset }}"
                                        transform="rotate(-90 44 44)"/>
                                @endif
                                @php
                                    $offset += $dash;
                                @endphp
                            @endforeach
                            <text x="44" y="41" text-anchor="middle" font-size="14" font-weight="800" fill="currentColor">{{ $serviceTotal }}</text>
                            <text x="44" y="55" text-anchor="middle" font-size="9" fill="#6c757d">ÓRDENES</text>
                        </svg>

                        <div class="w-100 d-flex flex-column gap-3 mt-2">
                            @forelse ($serviceDistribution as $c)
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="d-flex align-items-center gap-2 fw-bold" style="font-size:0.85rem;">
                                        <span style="width:10px; height:10px; border-radius:50%; background:{{ $c['color'] }}; display:inline-block; flex-shrink:0;"></span>
                                        {{ $c['name'] }}
                                    </span>
                                    <span class="text-muted fw-bold" style="font-size:0.85rem;">{{ $c['pct'] }}% ({{ $c['count'] }})</span>
                                </div>
                                <div style="height:4px; background:var(--bs-secondary-bg); border-radius:2px;">
                                    <div style="height:4px; width:{{ $c['pct'] }}%; background:{{ $c['color'] }}; border-radius:2px;"></div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center text-muted">
                                No se registraron servicios este mes.
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
