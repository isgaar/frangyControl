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
        $tipoConceptoLabel = [
            'servicio' => 'Servicio adicional',
            'inventario' => 'Inventario/refacción',
            'mano_obra' => 'Mano de obra',
            'otro' => 'Otro concepto',
        ];
        $conceptosTotal = $cotizacion->conceptos->sum(fn ($concepto) => (float) $concepto->subtotal);
    @endphp

    <style>
        .quote-detail-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(280px, 360px);
            gap: 1rem;
            align-items: start;
        }

        .quote-detail-main {
            display: grid;
            gap: 1rem;
            min-width: 0;
        }

        .quote-detail-section {
            border: 1px solid var(--dashboard-border);
            border-radius: 12px;
            background: var(--dashboard-surface);
            padding: 1rem;
        }

        .quote-detail-section__title {
            margin: 0 0 .85rem;
            font-weight: 800;
            color: var(--dashboard-text);
        }

        .quote-detail-summary {
            position: sticky;
            top: 1rem;
            border: 1px solid var(--dashboard-border);
            border-radius: 12px;
            background: var(--dashboard-surface);
            padding: 1rem;
        }

        .quote-detail-total {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            padding: .7rem 0;
            border-bottom: 1px solid var(--dashboard-border);
        }

        .quote-detail-total:last-child {
            border-bottom: 0;
        }

        .quote-detail-total span {
            color: var(--dashboard-muted);
            font-weight: 700;
        }

        .quote-detail-total strong {
            color: var(--dashboard-text);
            font-weight: 800;
        }

        .quote-detail-total.is-final strong {
            font-size: 1.35rem;
        }

        .quote-detail-actions {
            display: grid;
            gap: .5rem;
            margin-top: 1rem;
        }

        .quote-concept-list {
            display: grid;
            gap: .65rem;
        }

        .quote-concept {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: .75rem;
            align-items: center;
            padding: .85rem;
            border: 1px solid var(--dashboard-border);
            border-radius: 12px;
            background: var(--dashboard-surface-soft);
        }

        .quote-concept strong,
        .quote-concept span {
            display: block;
        }

        .quote-concept span {
            color: var(--dashboard-muted);
            font-size: .86rem;
        }

        @media (max-width: 992px) {
            .quote-detail-layout {
                grid-template-columns: 1fr;
            }

            .quote-detail-summary {
                position: static;
            }
        }
    </style>

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

        <div class="quote-detail-layout">
            <div class="quote-detail-main">
                <section class="quote-detail-section">
                    <h2 class="quote-detail-section__title">Datos principales</h2>
                    <div class="resource-kv">
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
                </section>

                <section class="quote-detail-section">
                    <h2 class="quote-detail-section__title">Notas</h2>
                    <p class="mb-0">{{ $cotizacion->notas ?: 'Sin notas adicionales.' }}</p>
                </section>

                <section class="quote-detail-section">
                    <h2 class="quote-detail-section__title">Conceptos cotizados</h2>
                    <div class="quote-concept-list">
                        <div class="quote-concept">
                            <div>
                                <strong>{{ $cotizacion->servicio?->nombreServicio ?? 'Servicio principal' }}</strong>
                                <span>Servicio principal · Precio base ${{ number_format((float) $cotizacion->precio_base, 2) }} · Rebaja ${{ number_format((float) $cotizacion->descuento_monto, 2) }}</span>
                            </div>
                            <strong>${{ number_format((float) $cotizacion->precio_base - (float) $cotizacion->descuento_monto, 2) }}</strong>
                        </div>

                        @forelse ($cotizacion->conceptos as $concepto)
                            <div class="quote-concept">
                                <div>
                                    <strong>{{ $concepto->descripcion }}</strong>
                                    <span>{{ $tipoConceptoLabel[$concepto->tipo] ?? 'Concepto' }} · {{ number_format((float) $concepto->cantidad, 2) }} x ${{ number_format((float) $concepto->precio_unitario, 2) }}</span>
                                </div>
                                <strong>${{ number_format((float) $concepto->subtotal, 2) }}</strong>
                            </div>
                        @empty
                            <p class="mb-0 text-muted">Sin conceptos adicionales.</p>
                        @endforelse
                    </div>
                </section>
            </div>

            <aside class="quote-detail-summary">
                <div class="quote-detail-total">
                    <span>Subtotal</span>
                    <strong>${{ number_format((float) $cotizacion->precio_base, 2) }}</strong>
                </div>
                <div class="quote-detail-total">
                    <span>Rebaja {{ number_format((float) $cotizacion->descuento_porcentaje, 2) }}%</span>
                    <strong>${{ number_format((float) $cotizacion->descuento_monto, 2) }}</strong>
                </div>
                <div class="quote-detail-total">
                    <span>Conceptos extra</span>
                    <strong>${{ number_format((float) $conceptosTotal, 2) }}</strong>
                </div>
                <div class="quote-detail-total is-final">
                    <span>Total</span>
                    <strong>${{ number_format((float) $cotizacion->total, 2) }}</strong>
                </div>

                <div class="quote-detail-actions">
                    <a href="{{ route('cotizaciones.edit', $cotizacion->id_cotizacion) }}" class="btn btn-primary">
                        <i class="fas fa-pen me-1"></i> Editar cotización
                    </a>
                    @if ($cotizacion->estado === 'aceptada')
                        <a href="{{ route('cotizaciones.create_order', $cotizacion->id_cotizacion) }}" class="btn btn-success">
                            <i class="fas fa-clipboard-check me-1"></i> Crear orden
                        </a>
                    @else
                        <button class="btn btn-outline-dark" type="button" disabled title="Disponible cuando la cotización esté aceptada">
                            <i class="fas fa-clipboard-check me-1"></i> Crear orden
                        </button>
                    @endif
                    <a href="{{ route('cotizaciones.create') }}" class="btn btn-outline-dark">
                        <i class="fas fa-plus me-1"></i> Nueva cotización
                    </a>
                </div>
            </aside>
        </div>
    </div>
@endsection
