@extends('layouts.dashboard')

@section('title', 'Detalle de cotización')

@section('content')
    @php
        $estadoLabel = [
            'borrador' => 'Borrador',
            'enviada' => 'Enviada',
            'aceptada' => 'Aceptada',
            'rechazada' => 'Rechazada',
            'vencida' => 'Vencida',
        ];
    @endphp

    <div class="resource-page">
        <div class="page-header">
            <div>
                <span class="page-eyebrow">Cotización</span>
                <h1 class="page-title">{{ $cotizacion->folio }}</h1>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('cotizaciones.edit', $cotizacion->id_cotizacion) }}" class="btn btn-primary">
                    <i class="fas fa-pen me-1"></i> Editar
                </a>
                <a href="{{ route('cotizaciones.index') }}" class="btn btn-outline-dark">
                    <i class="fas fa-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>

        <div class="metrics-grid">
            <article class="metric-card">
                <span class="metric-card__label">Subtotal</span>
                <p class="metric-card__value">${{ number_format((float) $cotizacion->precio_base, 2) }}</p>
                <p class="metric-card__copy">Precio cotizado</p>
            </article>
            <article class="metric-card">
                <span class="metric-card__label">Rebaja</span>
                <p class="metric-card__value">{{ number_format((float) $cotizacion->descuento_porcentaje, 2) }}%</p>
                <p class="metric-card__copy">${{ number_format((float) $cotizacion->descuento_monto, 2) }} descontados</p>
            </article>
            <article class="metric-card">
                <span class="metric-card__label">Total</span>
                <p class="metric-card__value">${{ number_format((float) $cotizacion->total, 2) }}</p>
                <p class="metric-card__copy">Importe final</p>
            </article>
        </div>

        <section class="resource-panel">
            <div class="resource-kv">
                <div class="resource-kv__item">
                    <span class="resource-kv__label">Cliente</span>
                    <p class="resource-kv__value">{{ $cotizacion->cliente?->nombreCompleto ?? 'Sin cliente' }}</p>
                </div>
                <div class="resource-kv__item">
                    <span class="resource-kv__label">Servicio</span>
                    <p class="resource-kv__value">{{ $cotizacion->servicio?->nombreServicio ?? 'Sin servicio' }}</p>
                </div>
                <div class="resource-kv__item">
                    <span class="resource-kv__label">Responsable</span>
                    <p class="resource-kv__value">{{ $cotizacion->user?->name ?? 'Sin responsable' }}</p>
                </div>
                <div class="resource-kv__item">
                    <span class="resource-kv__label">Estado</span>
                    <p class="resource-kv__value">{{ $estadoLabel[$cotizacion->estado] ?? ucfirst($cotizacion->estado) }}</p>
                </div>
                <div class="resource-kv__item">
                    <span class="resource-kv__label">Vigencia</span>
                    <p class="resource-kv__value">{{ $cotizacion->vigencia ? $cotizacion->vigencia->format('d/m/Y') : 'Sin vigencia' }}</p>
                </div>
                <div class="resource-kv__item">
                    <span class="resource-kv__label">Creación</span>
                    <p class="resource-kv__value">{{ $cotizacion->created_at?->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <div class="resource-warning mt-4">
                <strong>Notas</strong>
                <p class="mb-0 mt-2">{{ $cotizacion->notas ?: 'Sin notas adicionales.' }}</p>
            </div>
        </section>
    </div>
@endsection
