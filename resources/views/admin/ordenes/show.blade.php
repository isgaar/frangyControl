@extends('layouts.dashboard')

@section('title', 'Detalle de orden')

@section('content_header')
@if (Session::has('status'))
<div class="col-md-12 alert-section">
    <div class="alert alert-{{ Session::get('status_type') }} dashboard-legacy-alert">
        <span class="dashboard-legacy-alert__text">
            {{ Session::get('status') }}
            @php Session::forget('status'); @endphp
        </span>
    </div>
</div>
@endif
@stop

@section('css')
<style>
    /* ── Contenedor de sección ──────────────────────────────────── */
    .od-card {
        background: var(--dashboard-surface);
        border: 1px solid var(--dashboard-border);
        border-radius: 10px;
        margin-bottom: 1.25rem;
        overflow: hidden;
    }
    .od-card__head {
        padding: .85rem 1.25rem;
        border-bottom: 1px solid var(--dashboard-border);
        background: var(--dashboard-surface-soft, var(--dashboard-surface));
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: .5rem;
    }
    .od-card__eyebrow {
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .05em;
        text-transform: uppercase;
        color: var(--dashboard-primary);
        display: block;
        margin-bottom: 2px;
    }
    .od-card__title {
        font-size: 1rem;
        font-weight: 700;
        margin: 0 0 2px;
        color: var(--dashboard-text);
    }
    .od-card__copy {
        font-size: .8rem;
        color: var(--dashboard-muted);
        margin: 0;
    }
    .od-card__body {
        padding: 1rem 1.25rem;
    }

    /* ── Hero métricas ──────────────────────────────────────────── */
    .od-metrics {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: .6rem;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid var(--dashboard-border);
    }
    .od-metric {
        background: var(--dashboard-surface-soft, var(--dashboard-surface));
        border: 1px solid var(--dashboard-border);
        border-radius: 6px;
        padding: .5rem .9rem;
    }
    .od-metric__label {
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: var(--dashboard-muted);
        display: block;
        margin-bottom: 3px;
    }
    .od-metric__value {
        font-size: 1rem;
        font-weight: 700;
        margin: 0;
        line-height: 1.3;
        color: var(--dashboard-text);
    }
    .od-metric__copy {
        font-size: .75rem;
        color: var(--dashboard-muted);
        margin: 2px 0 0;
    }

    /* ── Campo label + valor ────────────────────────────────────── */
    .od-field-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: .6rem;
    }
    .od-field {
        display: flex;
        flex-direction: column;
        gap: 3px;
        padding: .6rem .75rem;
        background: var(--dashboard-surface-soft, var(--dashboard-surface));
        border: 1px solid var(--dashboard-border);
        border-radius: 6px;
        min-height: 54px;
    }
    .od-field__label {
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: var(--dashboard-muted);
    }
    .od-field__value {
        font-size: .875rem;
        font-weight: 500;
        color: var(--dashboard-text);
        word-break: break-word;
        overflow-wrap: break-word;
        line-height: 1.4;
    }

    /* ── Pills de estado ────────────────────────────────────────── */
    .od-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: .8rem;
        font-weight: 600;
        padding: 2px 10px;
        border-radius: 20px;
    }
    .od-pill::before {
        content: '';
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: currentColor;
        display: inline-block;
        flex-shrink: 0;
    }
    .od-pill--success { background: var(--color-background-success, #d1e7dd); color: var(--color-text-success, #0a3622); }
    .od-pill--danger  { background: var(--color-background-danger,  #f8d7da); color: var(--color-text-danger,  #58151c); }
    .od-pill--info    { background: var(--color-background-info,    #cff4fc); color: var(--color-text-info,    #055160); }

    /* ── Notas ──────────────────────────────────────────────────── */
    .od-notes {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: .75rem;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid var(--dashboard-border);
    }
    .od-note {
        background: var(--dashboard-surface-soft, var(--dashboard-surface));
        border: 1px solid var(--dashboard-border);
        border-left: 3px solid var(--dashboard-primary);
        border-radius: 0 6px 6px 0;
        padding: .75rem 1rem;
        display: flex;
        flex-direction: column;
        gap: 6px;
        min-height: 120px;
    }
    .od-note__label {
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .05em;
        text-transform: uppercase;
        color: var(--dashboard-primary);
        display: block;
    }
    .od-note__text {
        font-size: .875rem;
        color: var(--dashboard-text);
        margin: 0;
        line-height: 1.6;
        word-break: break-word;
        overflow-wrap: break-word;
        white-space: pre-wrap;
        flex: 1;
    }

    /* ── Galería ────────────────────────────────────────────────── */
    .od-gallery-scroll {
        max-height: 460px;
        overflow-y: auto;
        overflow-x: hidden;
    }
    .od-gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: .65rem;
    }
    .od-photo {
        display: flex;
        flex-direction: column;
        border: 1px solid var(--dashboard-border);
        border-radius: 6px;
        overflow: hidden;
        text-decoration: none;
        color: var(--dashboard-text);
        transition: border-color .15s;
    }
    .od-photo:hover {
        border-color: var(--dashboard-primary);
        text-decoration: none;
        color: var(--dashboard-text);
    }
    .od-photo__img {
        display: block;
        width: 100%;
        height: 115px;
        overflow: hidden;
        background: var(--dashboard-surface-soft, var(--dashboard-surface));
        flex-shrink: 0;
    }
    .od-photo__img img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
    }
    .od-photo__foot {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 5px 8px;
        background: var(--dashboard-surface-soft, var(--dashboard-surface));
        border-top: 1px solid var(--dashboard-border);
        gap: 4px;
        flex-shrink: 0;
    }
    .od-photo__foot strong {
        font-size: .7rem;
        font-weight: 700;
        display: block;
        color: var(--dashboard-text);
        line-height: 1.2;
    }
    .od-photo__foot small {
        font-size: .6rem;
        color: var(--dashboard-muted);
        display: block;
    }

    /* ── Hero título ────────────────────────────────────────────── */
    .od-hero-eyebrow {
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .05em;
        text-transform: uppercase;
        color: var(--dashboard-primary);
    }
    .od-hero-title {
        font-size: 1.3rem;
        font-weight: 700;
        margin: 2px 0 4px;
        color: var(--dashboard-text);
    }
    .od-hero-copy {
        font-size: .8rem;
        color: var(--dashboard-muted);
        margin: 0;
    }

    /* ── Badge conteo fotos ─────────────────────────────────────── */
    .od-photo-badge {
        font-size: .7rem;
        color: var(--dashboard-muted);
        background: var(--dashboard-surface-soft, var(--dashboard-surface));
        border: 1px solid var(--dashboard-border);
        border-radius: 20px;
        padding: 2px 10px;
    }

    /* ── Footer acciones ────────────────────────────────────────── */
    .od-actions {
        display: flex;
        flex-wrap: wrap;
        gap: .5rem;
        justify-content: flex-end;
    }

    /* ── Vacío galería ──────────────────────────────────────────── */
    .od-empty {
        color: var(--dashboard-muted);
    }

    /* ── Check verde ────────────────────────────────────────────── */
    .od-check-success {
        color: var(--dashboard-success, #198754);
    }

    /* ── Responsive ─────────────────────────────────────────────── */
    @media (max-width: 992px) {
        .od-notes { grid-template-columns: 1fr; }
    }
    @media (max-width: 768px) {
        .od-metrics { grid-template-columns: repeat(2, 1fr); }
        .od-gallery-grid { grid-template-columns: repeat(2, 1fr); }
        .od-actions { justify-content: stretch; }
        .od-actions .btn { flex: 1; text-align: center; }
        .od-notes { grid-template-columns: 1fr; }
    }
    @media (max-width: 480px) {
        .od-field-grid { grid-template-columns: 1fr 1fr; }
        .od-metrics { grid-template-columns: 1fr 1fr; }
    }
</style>
@endsection

@section('content')
@php
    $cliente      = $orden->cliente;
    $fechaEntrega = $orden->fechaEntrega
        ? \Carbon\Carbon::parse($orden->fechaEntrega)->format('d/m/Y')
        : 'Sin fecha';
    $statusTone   = match ($orden->status) {
        'finalizada' => 'success',
        'cancelada'  => 'danger',
        default      => 'info',
    };
    $clientFields = [
        ['label' => 'Nombre completo',    'value' => $cliente?->nombreCompleto ?? 'Sin cliente'],
        ['label' => 'Teléfono',           'value' => $cliente?->telefono       ?? 'Sin teléfono'],
        ['label' => 'Correo electrónico', 'value' => $cliente?->correo         ?? 'Sin correo'],
        ['label' => 'RFC',                'value' => $cliente?->rfc            ?: 'Sin RFC'],
    ];
    $vehicleFields = [
        ['label' => 'Marca',          'value' => $orden->vehiculo?->marca      ?? 'Sin marca'],
        ['label' => 'Tipo',           'value' => $orden->tipoVehiculo?->tipo   ?? 'Sin tipo'],
        ['label' => 'Línea / Modelo', 'value' => $orden->modelo               ?: 'Sin línea'],
        ['label' => 'Año',            'value' => $orden->yearVehiculo          ?: 'Sin año'],
        ['label' => 'Color',          'value' => $orden->color                ?: 'Sin color'],
        ['label' => 'Placas',         'value' => $orden->placas               ?: 'Sin placas'],
        ['label' => 'Kilometraje',    'value' => $orden->kilometraje ? $orden->kilometraje . ' km' : 'Sin km'],
        ['label' => 'Motor',          'value' => $orden->motor                ?: 'Sin motor'],
        ['label' => 'Cilindros',      'value' => $orden->cilindros            ?: 'Sin dato'],
        ['label' => 'No. serie',      'value' => $orden->noSerievehiculo      ?: 'Sin serie'],
    ];
    $orderFields = [
        ['label' => 'Tipo de servicio', 'value' => $orden->servicio?->nombreServicio ?? 'Sin servicio'],
        ['label' => 'Responsable',      'value' => $orden->user?->name               ?? 'Sin responsable'],
        ['label' => 'Refacciones',      'value' => $orden->retiroRefacciones ? 'Retiró refacciones' : 'No retiró'],
        ['label' => 'Fecha de entrega', 'value' => $fechaEntrega],
    ];
    if (!is_null($orden->motivo)) {
        $orderFields[] = ['label' => 'Motivo', 'value' => $orden->motivo];
    }
    $notes = [
        ['label' => 'Observaciones internas',     'value' => $orden->observacionesInt       ?: 'Sin observaciones.'],
        ['label' => 'Recomendaciones del cliente', 'value' => $orden->recomendacionesCliente ?: 'Sin recomendaciones.'],
        ['label' => 'Detalles del servicio',       'value' => $orden->detallesOrden          ?: 'Sin detalles.'],
    ];
    $totalFotos = $orden->fotografias->count();
@endphp

<div class="container-fluid px-0">

    {{-- ══ HERO ══════════════════════════════════════════════════ --}}
    <div class="od-card">
        <div class="od-card__body">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-2">
                <div>
                    <span class="od-hero-eyebrow">Órdenes</span>
                    <h1 class="od-hero-title">
                        Orden <span style="color: var(--dashboard-primary);">#{{ $orden->id_ordenes }}</span>
                    </h1>
                    <p class="od-hero-copy">
                        Consulta cliente, unidad, servicio, estado y evidencia fotográfica.
                    </p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('ordenes.edit', $orden->id_ordenes) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-pen me-1"></i> Editar
                    </a>
                    <a href="{{ route('ordenes.export', $orden->id_ordenes) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-file-pdf me-1"></i> PDF
                    </a>
                    <a href="{{ route('ordenes.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Volver
                    </a>
                </div>
            </div>

            {{-- Chips de métricas --}}
            <div class="od-metrics">
                <div class="od-metric">
                    <span class="od-metric__label">Estado</span>
                    <p class="od-metric__value">
                        <span class="od-pill od-pill--{{ $statusTone }}">{{ ucfirst($orden->status ?? 'sin estado') }}</span>
                    </p>
                    <p class="od-metric__copy">Seguimiento de la orden</p>
                </div>
                <div class="od-metric">
                    <span class="od-metric__label">Entrega</span>
                    <p class="od-metric__value">{{ $fechaEntrega }}</p>
                    <p class="od-metric__copy">Fecha comprometida</p>
                </div>
                <div class="od-metric">
                    <span class="od-metric__label">Evidencia</span>
                    <p class="od-metric__value">{{ $totalFotos }}</p>
                    <p class="od-metric__copy">Fotografía(s) cargadas</p>
                </div>
                <div class="od-metric">
                    <span class="od-metric__label">Autorización</span>
                    <p class="od-metric__value" style="font-size:.875rem;">
                        <i class="fas fa-check-circle od-check-success me-1"></i> Aceptada
                    </p>
                    <p class="od-metric__copy">El cliente autorizó</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ CLIENTE + VEHÍCULO ══════════════════════════════════════ --}}
    <div class="row g-3 mb-0">

        <div class="col-lg-5">
            <div class="od-card h-100">
                <div class="od-card__head">
                    <div>
                        <span class="od-card__eyebrow">Cliente</span>
                        <h2 class="od-card__title">Información del cliente</h2>
                        <p class="od-card__copy">Datos de contacto asociados a esta orden.</p>
                    </div>
                </div>
                <div class="od-card__body">
                    <div class="od-field-grid">
                        @foreach ($clientFields as $f)
                        <div class="od-field">
                            <span class="od-field__label">{{ $f['label'] }}</span>
                            <span class="od-field__value">{{ $f['value'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="od-card h-100">
                <div class="od-card__head">
                    <div>
                        <span class="od-card__eyebrow">Unidad</span>
                        <h2 class="od-card__title">Datos del vehículo</h2>
                        <p class="od-card__copy">Identificación y características de la unidad recibida.</p>
                    </div>
                </div>
                <div class="od-card__body">
                    <div class="od-field-grid">
                        @foreach ($vehicleFields as $f)
                        <div class="od-field">
                            <span class="od-field__label">{{ $f['label'] }}</span>
                            <span class="od-field__value">{{ $f['value'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ══ DATOS DE LA ORDEN ═══════════════════════════════════════ --}}
    <div class="od-card">
        <div class="od-card__head">
            <div>
                <span class="od-card__eyebrow">Servicio</span>
                <h2 class="od-card__title">Datos de la orden</h2>
                <p class="od-card__copy">Responsable, estado, fechas y notas operativas.</p>
            </div>
        </div>
        <div class="od-card__body">

            <div class="od-field-grid">
                @foreach ($orderFields as $f)
                <div class="od-field">
                    <span class="od-field__label">{{ $f['label'] }}</span>
                    <span class="od-field__value">{{ $f['value'] }}</span>
                </div>
                @endforeach
                <div class="od-field">
                    <span class="od-field__label">Estado</span>
                    <span class="od-field__value">
                        <span class="od-pill od-pill--{{ $statusTone }}">{{ ucfirst($orden->status ?? 'sin estado') }}</span>
                    </span>
                </div>
                <div class="od-field">
                    <span class="od-field__label">Autorización</span>
                    <span class="od-field__value">
                        <i class="fas fa-check-circle od-check-success me-1"></i> El cliente aceptó
                    </span>
                </div>
            </div>

            {{-- Notas operativas en 3 columnas iguales --}}
            <div class="od-notes">
                @foreach ($notes as $n)
                <div class="od-note">
                    <span class="od-note__label">{{ $n['label'] }}</span>
                    <p class="od-note__text">{{ $n['value'] }}</p>
                </div>
                @endforeach
            </div>
        
        </div>
    </div>

    {{-- ══ EVIDENCIA FOTOGRÁFICA ════════════════════════════════════ --}}
    <div class="od-card">
        <div class="od-card__head">
            <div>
                <span class="od-card__eyebrow">Evidencia</span>
                <h2 class="od-card__title">Evidencia fotográfica</h2>
                <p class="od-card__copy">Imágenes cargadas para documentar la recepción o el estado de la unidad.</p>
            </div>
            @if ($totalFotos > 0)
            <span class="od-photo-badge">
                <i class="fas fa-images me-1"></i>
                {{ $totalFotos }} fotografía{{ $totalFotos !== 1 ? 's' : '' }}
            </span>
            @endif
        </div>

        <div class="od-card__body">
            @if ($orden->fotografias->isNotEmpty())
            <div class="od-gallery-scroll">
                <div class="od-gallery-grid">
                    @foreach ($orden->fotografias as $index => $fotografia)
                    <a class="od-photo"
                        href="{{ route('ordenes.photos.show', [$orden->id_ordenes, $fotografia->id], false) }}"
                        target="_blank" rel="noopener">
                        <div class="od-photo__img">
                            <img src="{{ route('ordenes.photos.show', [$orden->id_ordenes, $fotografia->id], false) }}"
                                alt="Evidencia {{ $index + 1 }}"
                                loading="lazy">
                        </div>
                        <div class="od-photo__foot">
                            <span>
                                <strong>Evidencia {{ $index + 1 }}</strong>
                                <small>Abrir en tamaño completo</small>
                            </span>
                            <i class="fas fa-external-link-alt" style="font-size:.6rem; color: var(--dashboard-muted);" aria-hidden="true"></i>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @else
            <div class="text-center py-4 od-empty">
                <i class="fas fa-image fa-2x mb-2 d-block opacity-50"></i>
                <p class="mb-0" style="font-size:.825rem;">Esta orden todavía no tiene fotografías cargadas.</p>
            </div>
            @endif
        </div>
    </div>

    {{-- ══ ACCIONES DE PIE ═════════════════════════════════════════ --}}
    <div class="od-card">
        <div class="od-card__body py-3">
            <div class="od-actions">
                <a href="{{ route('ordenes.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Retroceder
                </a>
                <a href="{{ route('ordenes.edit', $orden->id_ordenes) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-pen me-1"></i> Editar orden
                </a>
                <a href="{{ route('ordenes.export', $orden->id_ordenes) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-file-pdf me-1"></i> Exportar a PDF
                </a>
            </div>
        </div>
    </div>

</div>
@endsection
