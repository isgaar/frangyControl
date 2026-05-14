<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket POS - Orden #{{ $orden->id_ordenes }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            margin: 0;
            padding: 0;
            background-color: #fff;
            color: #000;
        }
        .ticket-container {
            width: 58mm;
            max-width: 58mm;
            margin: 0 auto;
            padding: 5px;
            font-size: 12px;
            line-height: 1.2;
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .border-top { border-top: 1px dashed #000; padding-top: 5px; margin-top: 5px; }
        .border-bottom { border-bottom: 1px dashed #000; padding-bottom: 5px; margin-bottom: 5px; }
        
        .logo {
            max-width: 40px;
            margin-bottom: 5px;
            filter: grayscale(100%);
        }
        h1, h2, h3, p { margin: 2px 0; }
        
        table { width: 100%; border-collapse: collapse; }
        table td { padding: 2px 0; font-size: 11px; }

        @media print {
            body { margin: 0; padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align:center; padding: 10px; background:#f0f0f0; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">🖨️ Imprimir Ticket</button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">Cerrar</button>
    </div>

    <div class="ticket-container">
        <div class="text-center border-bottom">
            @if(file_exists(public_path('franlogo.png')))
                <img src="{{ asset('franlogo.png') }}" alt="Logo" class="logo">
            @endif
            <h2 style="font-size: 14px;">{{ config('app.name', 'Frangy Control') }}</h2>
            <p>Taller Mecánico</p>
            <p>Ticket de Servicio</p>
        </div>

        <div>
            <p><span class="font-bold">Folio:</span> #{{ $orden->id_ordenes }}</p>
            <p><span class="font-bold">Fecha:</span> {{ \Carbon\Carbon::parse($orden->created_at)->format('d/m/Y H:i') }}</p>
            <p><span class="font-bold">Atendió:</span> {{ $orden->user->name ?? 'N/A' }}</p>
        </div>

        <div class="border-top border-bottom">
            <p class="font-bold">Datos del Cliente:</p>
            <p>{{ explode(' ', $orden->cliente->nombreCompleto ?? 'Público General')[0] }}</p>
            @if($orden->vehiculo)
                <p>{{ $orden->vehiculo->marca }} {{ $orden->modelo }}</p>
                <p>Placas: {{ $orden->placas }}</p>
            @endif
        </div>

        <div class="border-bottom">
            <p class="font-bold">Servicio Requerido:</p>
            <p>{{ $orden->servicio->nombreServicio ?? 'Servicio General' }}</p>
            <p style="margin-top: 5px;">Estado: <span class="font-bold">{{ $orden->status }}</span></p>
        </div>

        <div class="text-center border-bottom" style="font-size: 10px; padding: 10px 0;">
            <p>¡Gracias por su preferencia!</p>
            <p>Conserve este ticket para cualquier aclaración o recoger su vehículo.</p>
        </div>
        
        <div class="text-center" style="font-size: 9px; margin-top: 10px;">
            <p>Generado por FrangyControl v{{ config('frangy.version', '2.0.14') }}</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            setTimeout(() => {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>
