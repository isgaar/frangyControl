@extends('layouts.dashboard')

@section('title', 'Detalle de orden')

@section('content_header')
@if (Session::has('status'))
<div class="col-md-12 alert-section">
    <div class="alert alert-{{ Session::get('status_type') }} dashboard-legacy-alert">
        <span class="dashboard-legacy-alert__text">
            {{ Session::get('status') }}
            @php
            Session::forget('status');
            @endphp
        </span>
    </div>
</div>
@endif
@stop

@section('content')
@php
    $cliente = $orden->cliente;
    $fechaEntrega = $orden->fechaEntrega
        ? \Carbon\Carbon::parse($orden->fechaEntrega)->format('d/m/Y')
        : 'Sin fecha';
    $statusTone = match ($orden->status) {
        'finalizada' => 'success',
        'cancelada' => 'danger',
        default => 'info',
    };

    $clientFields = [
        ['label' => 'Nombre completo', 'value' => $cliente?->nombreCompleto ?? 'Sin cliente'],
        ['label' => 'Teléfono', 'value' => $cliente?->telefono ?? 'Sin teléfono'],
        ['label' => 'Correo electrónico', 'value' => $cliente?->correo ?? 'Sin correo'],
        ['label' => 'RFC', 'value' => $cliente?->rfc ?: 'Sin RFC'],
    ];

    $vehicleFields = [
        ['label' => 'Marca', 'value' => $orden->vehiculo?->marca ?? 'Sin marca'],
        ['label' => 'Tipo de vehículo', 'value' => $orden->tipoVehiculo?->tipo ?? 'Sin tipo'],
        ['label' => 'Línea', 'value' => $orden->modelo ?: 'Sin línea'],
        ['label' => 'Año', 'value' => $orden->yearVehiculo ?: 'Sin año'],
        ['label' => 'Color', 'value' => $orden->color ?: 'Sin color'],
        ['label' => 'Placas', 'value' => $orden->placas ?: 'Sin placas'],
        ['label' => 'Kilometraje', 'value' => $orden->kilometraje ? $orden->kilometraje . ' km' : 'Sin kilometraje'],
        ['label' => 'Motor', 'value' => $orden->motor ?: 'Sin motor'],
        ['label' => 'Cilindros', 'value' => $orden->cilindros ?: 'Sin dato'],
        ['label' => 'No. serie', 'value' => $orden->noSerievehiculo ?: 'Sin serie'],
    ];

    $orderFields = [
        ['label' => 'Tipo de servicio', 'value' => $orden->servicio?->nombreServicio ?? 'Sin servicio'],
        ['label' => 'Atiende', 'value' => $orden->user?->name ?? 'Sin responsable'],
        ['label' => 'Refacciones', 'value' => $orden->retiroRefacciones ? 'Retiró refacciones' : 'No retiró refacciones'],
        ['label' => 'Fecha de entrega', 'value' => $fechaEntrega],
        ['label' => 'Estado', 'value' => ucfirst($orden->status ?? 'sin estado'), 'tone' => $statusTone],
    ];

    if (!is_null($orden->motivo)) {
        $orderFields[] = ['label' => 'Motivo', 'value' => $orden->motivo];
    }

    $notes = [
        ['label' => 'Observaciones internas', 'value' => $orden->observacionesInt ?: 'Sin observaciones internas.'],
        ['label' => 'Recomendaciones del cliente', 'value' => $orden->recomendacionesCliente ?: 'Sin recomendaciones.'],
        ['label' => 'Detalles del servicio', 'value' => $orden->detallesOrden ?: 'Sin detalles.'],
    ];
@endphp

<div class="resource-page order-detail-page">
    <section class="resource-hero">
        <div class="resource-hero__top">
            <div class="resource-hero__copy">
                <span class="resource-hero__eyebrow">Órdenes</span>
                <h1 class="resource-hero__title">Detalle de la orden #{{ $orden->id_ordenes }}</h1>
                <p>Consulta cliente, unidad, servicio, estado y evidencia fotográfica desde una vista limpia.</p>
            </div>

            <div class="resource-hero__actions">
                <a href="{{ route('ordenes.edit', $orden->id_ordenes) }}" class="btn btn-primary">
                    <i class="fas fa-pen me-1"></i> Editar orden
                </a>
                <a href="{{ route('ordenes.export', $orden->id_ordenes) }}" class="btn btn-outline-dark">
                    <i class="fas fa-file-pdf me-1"></i> Exportar PDF
                </a>
                <a href="{{ route('ordenes.index') }}" class="btn btn-outline-light">
                    <i class="fas fa-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>

        <div class="resource-metrics">
            <article class="resource-metric">
                <span class="resource-metric__label">Estado</span>
                <p class="resource-metric__value order-metric-status order-metric-status--{{ $statusTone }}">
                    {{ ucfirst($orden->status ?? 'sin estado') }}
                </p>
                <p class="resource-metric__copy">Seguimiento actual de la orden.</p>
            </article>
            <article class="resource-metric">
                <span class="resource-metric__label">Entrega</span>
                <p class="resource-metric__value">{{ $fechaEntrega }}</p>
                <p class="resource-metric__copy">Fecha comprometida con el cliente.</p>
            </article>
            <article class="resource-metric">
                <span class="resource-metric__label">Evidencia</span>
                <p class="resource-metric__value">{{ $orden->fotografias->count() }}</p>
                <p class="resource-metric__copy">Fotografia(s) cargadas.</p>
            </article>
        </div>
    </section>

    <div class="order-detail-grid">
        <section class="resource-panel">
            <div class="resource-panel__header">
                <div>
                    <span class="resource-panel__eyebrow">Cliente</span>
                    <h2 class="resource-panel__title">Información del cliente</h2>
                    <p class="resource-panel__copy">Datos de contacto asociados a esta orden.</p>
                </div>
            </div>

            <div class="order-readonly-grid mt-4">
                @foreach ($clientFields as $field)
                <div class="order-readonly-field">
                    <span>{{ $field['label'] }}</span>
                    <strong>{{ $field['value'] }}</strong>
                </div>
                @endforeach
            </div>
        </section>

        <section class="resource-panel">
            <div class="resource-panel__header">
                <div>
                    <span class="resource-panel__eyebrow">Unidad</span>
                    <h2 class="resource-panel__title">Datos del vehículo</h2>
                    <p class="resource-panel__copy">Identificación y características de la unidad recibida.</p>
                </div>
            </div>

            <div class="order-readonly-grid mt-4">
                @foreach ($vehicleFields as $field)
                <div class="order-readonly-field">
                    <span>{{ $field['label'] }}</span>
                    <strong>{{ $field['value'] }}</strong>
                </div>
                @endforeach
            </div>
        </section>
    </div>

    <section class="resource-panel">
        <div class="resource-panel__header">
            <div>
                <span class="resource-panel__eyebrow">Servicio</span>
                <h2 class="resource-panel__title">Datos de la orden</h2>
                <p class="resource-panel__copy">Responsable, estado, fechas y notas operativas del servicio.</p>
            </div>
        </div>

        <div class="order-readonly-grid mt-4">
            @foreach ($orderFields as $field)
            <div class="order-readonly-field">
                <span>{{ $field['label'] }}</span>
                @if (!empty($field['tone']))
                <strong class="order-status-pill order-status-pill--{{ $field['tone'] }}">{{ $field['value'] }}</strong>
                @else
                <strong>{{ $field['value'] }}</strong>
                @endif
            </div>
            @endforeach
            <div class="order-readonly-field order-readonly-field--check">
                <span>Autorizacion</span>
                <strong><i class="fas fa-check-circle me-1"></i> El cliente acepto</strong>
            </div>
        </div>

        <div class="order-note-grid mt-4">
            @foreach ($notes as $note)
            <div class="order-note-box">
                <span>{{ $note['label'] }}</span>
                <p>{{ $note['value'] }}</p>
            </div>
            @endforeach
        </div>
    </section>

    <section class="resource-panel">
        <div class="resource-panel__header">
            <div>
                <span class="resource-panel__eyebrow">Evidencia</span>
                <h2 class="resource-panel__title">Evidencia fotografica</h2>
                <p class="resource-panel__copy">Imágenes cargadas para documentar la recepción o el estado de la unidad.</p>
            </div>
        </div>

        @if ($orden->fotografias->isNotEmpty())
        <div class="order-photo-gallery mt-4">
            @foreach ($orden->fotografias as $index => $fotografia)
            <a class="order-photo-card"
                href="{{ route('ordenes.photos.show', [$orden->id_ordenes, $fotografia->id]) }}"
                target="_blank" rel="noopener">
                <span class="order-photo-card__media">
                    <img src="{{ route('ordenes.photos.show', [$orden->id_ordenes, $fotografia->id]) }}"
                        alt="Fotografia de evidencia {{ $index + 1 }} de la orden {{ $orden->id_ordenes }}">
                </span>
                <span class="order-photo-card__footer">
                    <span>
                        <strong>Evidencia {{ $index + 1 }}</strong>
                        <small>Click para abrir en tamaño completo</small>
                    </span>
                    <i class="fas fa-external-link-alt" aria-hidden="true"></i>
                </span>
            </a>
            @endforeach
        </div>
        @else
        <div class="resource-empty mt-4">
            Esta orden todavía no tiene fotografías cargadas.
        </div>
        @endif
    </section>

    <section class="resource-panel">
        <div class="resource-footer-actions justify-content-end">
            <a href="{{ route('ordenes.index') }}" class="btn btn-outline-dark">
                <i class="fas fa-arrow-left me-1"></i> Retroceder
            </a>
            <a href="{{ route('ordenes.edit', $orden->id_ordenes) }}" class="btn btn-primary">
                <i class="fas fa-pen me-1"></i> Editar o actualizar
            </a>
            <a href="{{ route('ordenes.export', $orden->id_ordenes) }}" class="btn btn-outline-dark">
                <i class="fas fa-file-pdf me-1"></i> Exportar a PDF
            </a>
        </div>
    </section>
</div>
@endsection
