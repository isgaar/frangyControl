<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Operativo - Panel de Control</title>
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
            width: 33.33%;
            padding: 10px;
            vertical-align: top;
            border: 1px solid #dee2e6;
            background-color: #f8f9fa;
        }
        .metric-value {
            font-size: 24px;
            font-weight: bold;
            color: #0d6efd;
            display: block;
            margin-bottom: 5px;
        }
        .metric-label {
            font-size: 12px;
            text-transform: uppercase;
            color: #495057;
            font-weight: bold;
        }
        .metric-danger { color: #dc3545; }
        .metric-success { color: #198754; }
        .metric-warning { color: #ffc107; }
        
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
        .badge {
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            background-color: #e9ecef;
            color: #495057;
        }
    </style>
</head>
<body>

    <div class="header-meta">
        Fecha de generación: {{ $date }}
    </div>

    <h1>Resumen Operativo del Taller</h1>

    <h2>Métricas Principales</h2>
    <table class="metrics-grid">
        <tr>
            <td>
                <span class="metric-label">Órdenes Registradas</span>
                <span class="metric-value">{{ $totalOrders }}</span>
                Histórico total
            </td>
            <td>
                <span class="metric-label">En Proceso</span>
                <span class="metric-value metric-success">{{ $ordersInProgress }}</span>
                Trabajo activo
            </td>
            <td>
                <span class="metric-label">Finalizadas</span>
                <span class="metric-value">{{ $finishedOrders }}</span>
                Órdenes cerradas
            </td>
        </tr>
        <tr>
            <td>
                <span class="metric-label">Vencidas</span>
                <span class="metric-value metric-danger">{{ $overdueOrdersCount }}</span>
                Fecha de entrega vencida
            </td>
            <td>
                <span class="metric-label">Sin Asignar</span>
                <span class="metric-value metric-warning">{{ $unassignedOrdersCount }}</span>
                Pendientes de responsable
            </td>
            <td>
                <span class="metric-label">Clientes del Mes</span>
                <span class="metric-value">{{ $newClientsInPeriod }}</span>
                Altas del periodo actual
            </td>
        </tr>
    </table>

    <h2>Distribución de Servicios (Top {{ count($serviceDistribution) }})</h2>
    @if(count($serviceDistribution) > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th>Servicio</th>
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
                    <th>Total Base</th>
                    <th class="text-right">{{ $serviceTotal }}</th>
                    <th class="text-right">100%</th>
                </tr>
            </tfoot>
        </table>
    @else
        <p>No hay datos de servicios registrados.</p>
    @endif

    <h2>Órdenes Recientes (Últimas 10)</h2>
    @if($recentOrders->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 10%;">Folio</th>
                    <th style="width: 30%;">Cliente</th>
                    <th style="width: 25%;">Vehículo</th>
                    <th style="width: 20%;">Servicio</th>
                    <th style="width: 15%;">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentOrders as $orden)
                <tr>
                    <td>#{{ $orden->id_ordenes }}</td>
                    <td>{{ optional($orden->cliente)->nombreCompleto ?? 'Sin cliente' }}</td>
                    <td>
                        {{ optional($orden->vehiculo)->marca ?? 'Sin vehículo' }}
                        @if (!empty($orden->placas))
                            <br><small style="color: #6c757d;">{{ $orden->placas }}</small>
                        @endif
                    </td>
                    <td>{{ optional($orden->servicio)->nombreServicio ?? 'Sin servicio' }}</td>
                    <td><span class="badge">{{ $orden->status ?? 'Sin estado' }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No hay órdenes registradas.</p>
    @endif

</body>
</html>
