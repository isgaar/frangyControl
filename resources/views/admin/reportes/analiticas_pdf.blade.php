<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Analíticas - {{ $monthName }} {{ $selectedYear }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.4;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        h1, h2, h3 {
            color: #1a1a1a;
            margin-top: 0;
        }
        h1 {
            font-size: 24px;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 5px;
            margin-bottom: 20px;
        }
        h2 {
            font-size: 18px;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 4px;
            margin-top: 30px;
            margin-bottom: 15px;
        }
        .header-meta {
            text-align: right;
            font-size: 11px;
            color: #666;
            margin-bottom: 20px;
        }
        .metrics-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .metrics-grid td {
            width: 50%;
            padding: 15px;
            vertical-align: top;
            border: 1px solid #dee2e6;
            background-color: #f8f9fa;
        }
        .metric-value {
            font-size: 28px;
            font-weight: bold;
            color: #0d6efd;
            display: block;
            margin-bottom: 5px;
        }
        .metric-label {
            font-size: 13px;
            text-transform: uppercase;
            color: #495057;
            font-weight: bold;
        }
        
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
        }
        table.data-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
            color: #495057;
        }
        table.data-table tr:nth-child(even) {
            background-color: #fcfcfc;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="header-meta">
        Fecha de generación: {{ now()->format('d/m/Y H:i') }}
    </div>

    <h1>Reporte de Analíticas: {{ ucfirst($monthName) }} {{ $selectedYear }}</h1>

    <h2>Resumen General del Mes</h2>
    <table class="metrics-grid">
        <tr>
            <td>
                <span class="metric-label">Total Órdenes Registradas</span>
                <span class="metric-value">{{ $totalCurrentMonth }}</span>
                En el mes actual ({{ ucfirst($monthName) }})
            </td>
            <td>
                <span class="metric-label">Órdenes Mes Anterior</span>
                <span class="metric-value" style="color:#6c757d;">{{ $totalPrevMonth }}</span>
                Para comparación
            </td>
        </tr>
    </table>

    <h2>Progreso Semanal</h2>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 40%;">Semana del Mes</th>
                <th style="width: 30%; text-align: center;">Órdenes (Mes Actual)</th>
                <th style="width: 30%; text-align: center;">Órdenes (Mes Anterior)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($weeklyBars as $bar)
            <tr>
                <td>{{ $bar['lbl'] }}</td>
                <td class="text-center" style="font-weight:bold; color:#0d6efd;">{{ $bar['cur'] }}</td>
                <td class="text-center" style="color:#6c757d;">{{ $bar['pre'] }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th style="text-align:right;">TOTAL</th>
                <th class="text-center">{{ $totalCurrentMonth }}</th>
                <th class="text-center">{{ $totalPrevMonth }}</th>
            </tr>
        </tfoot>
    </table>

    <h2>Distribución de Servicios</h2>
    @if(count($serviceDistribution) > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th>Categoría de Servicio</th>
                    <th style="width: 20%; text-align: right;">Cantidad</th>
                    <th style="width: 20%; text-align: right;">Porcentaje</th>
                </tr>
            </thead>
            <tbody>
                @foreach($serviceDistribution as $dist)
                <tr>
                    <td>{{ $dist['name'] }}</td>
                    <td class="text-right">{{ $dist['count'] }}</td>
                    <td class="text-right">{{ $dist['pct'] }}%</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th style="text-align:right;">TOTAL BASE DE SERVICIOS</th>
                    <th class="text-right">{{ $serviceTotal }}</th>
                    <th class="text-right">100%</th>
                </tr>
            </tfoot>
        </table>
    @else
        <p>No hay datos de servicios registrados en este mes.</p>
    @endif

</body>
</html>
