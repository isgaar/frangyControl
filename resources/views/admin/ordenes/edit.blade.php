@extends('layouts.dashboard')

@section('title', 'Editar orden #' . $orden->id_ordenes)

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

@section('content')
<style>
    .order-panel {
        border-radius: 16px;
        border: 1px solid var(--dashboard-border);
        background: var(--dashboard-surface);
        box-shadow: var(--dashboard-shadow);
        overflow: hidden;
    }

    .order-panel__header {
        border-bottom: 1px solid var(--dashboard-border);
        background: linear-gradient(135deg, color-mix(in srgb, var(--dashboard-primary) 16%, var(--dashboard-surface)), var(--dashboard-surface));
        color: var(--dashboard-text);
    }

    .order-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .order-header-copy h3 {
        font-weight: 700;
    }

    .order-header-copy p {
        margin: 0.35rem 0 0;
        color: var(--dashboard-muted);
    }

    .order-help-toggle {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        border-radius: 999px;
        border: 1px solid var(--dashboard-border);
        background: var(--dashboard-surface);
        font-weight: 600;
        color: var(--dashboard-text);
    }

    .order-help-toggle.is-active,
    .order-help-toggle:hover {
        border-color: color-mix(in srgb, var(--dashboard-primary) 44%, var(--dashboard-border));
        background: var(--dashboard-primary-soft);
        color: var(--dashboard-primary-strong);
    }

    .order-flow {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: .75rem;
        margin-top: 1rem;
    }

    .order-flow__item {
        display: flex;
        align-items: center;
        gap: .65rem;
        min-height: 48px;
        padding: .75rem;
        border: 1px solid var(--dashboard-border);
        border-radius: 12px;
        background: var(--dashboard-surface);
        color: var(--dashboard-muted);
        font-weight: 700;
    }

    .order-flow__item span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: var(--dashboard-primary);
        color: #fff;
        font-size: .85rem;
    }

    .order-intake-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        gap: 1rem;
    }

    .order-form-main {
        min-width: 0;
    }

    .order-section {
        padding: 1.25rem 0;
        border-top: 1px solid var(--dashboard-border);
    }

    .order-section:first-child {
        padding-top: 0;
        border-top: 0;
    }

    .order-section__header {
        display: flex;
        align-items: flex-start;
        gap: .85rem;
        margin-bottom: 1rem;
    }

    .order-section__badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        width: 38px;
        height: 38px;
        border-radius: 12px;
        background: var(--dashboard-primary-soft);
        color: var(--dashboard-primary);
        font-weight: 900;
    }

    .order-section__title {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--dashboard-text);
    }

    .order-section__copy {
        margin: .2rem 0 0;
        color: var(--dashboard-muted);
    }

    .order-id-badge {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .3rem .8rem;
        border-radius: 999px;
        background: var(--dashboard-primary-soft);
        color: var(--dashboard-primary-strong);
        font-size: .85rem;
        font-weight: 800;
        border: 1px solid color-mix(in srgb, var(--dashboard-primary) 30%, var(--dashboard-border));
    }

    .order-status-badge {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .25rem .7rem;
        border-radius: 999px;
        font-size: .8rem;
        font-weight: 700;
    }

    .order-status-badge.is-proceso {
        background: color-mix(in srgb, #f59e0b 14%, var(--dashboard-surface));
        color: #b45309;
        border: 1px solid color-mix(in srgb, #f59e0b 30%, var(--dashboard-border));
    }

    .order-status-badge.is-finalizada {
        background: color-mix(in srgb, #10b981 14%, var(--dashboard-surface));
        color: #065f46;
        border: 1px solid color-mix(in srgb, #10b981 30%, var(--dashboard-border));
    }

    .order-status-badge.is-cancelada {
        background: color-mix(in srgb, #ef4444 14%, var(--dashboard-surface));
        color: #991b1b;
        border: 1px solid color-mix(in srgb, #ef4444 30%, var(--dashboard-border));
    }

    .order-submit-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .45rem;
        min-height: 40px;
        padding-inline: 1rem;
        border-color: var(--dashboard-primary);
        background: var(--dashboard-primary);
        color: #fff;
        font-weight: 800;
        box-shadow: 0 10px 24px color-mix(in srgb, var(--dashboard-primary) 22%, transparent);
    }

    .order-submit-button:hover,
    .order-submit-button:focus {
        border-color: var(--dashboard-primary-strong);
        background: var(--dashboard-primary-strong);
        color: #fff;
    }

    .order-submit-button:disabled,
    .order-submit-button.disabled {
        border-color: color-mix(in srgb, var(--dashboard-primary) 34%, var(--dashboard-border));
        background: color-mix(in srgb, var(--dashboard-primary) 12%, var(--dashboard-surface));
        color: color-mix(in srgb, var(--dashboard-primary-strong) 72%, var(--dashboard-muted));
        box-shadow: none;
        opacity: 1;
    }

    .help-panel {
        margin-bottom: 1.5rem;
        padding: 1.25rem;
        border: 1px solid var(--dashboard-border);
        border-radius: 16px;
        background: var(--dashboard-surface-soft);
    }

    .help-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 1rem;
        flex-wrap: wrap;
    }

    .help-panel-title {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--dashboard-text);
    }

    .help-panel-copy {
        margin: 0;
        color: var(--dashboard-muted);
    }

    .help-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
    }

    .help-block {
        padding: 1rem;
        border-radius: 14px;
        border: 1px solid rgba(148, 163, 184, 0.22);
        background: var(--dashboard-surface);
    }

    .help-block h4 {
        margin-bottom: 0.75rem;
        font-size: 0.98rem;
        font-weight: 700;
        color: var(--dashboard-text);
    }

    .help-list {
        padding-left: 1rem;
        margin-bottom: 0;
    }

    .help-list li + li {
        margin-top: 0.5rem;
    }

    .helper-copy {
        display: none !important;
        margin-top: .35rem;
        color: var(--dashboard-muted);
        font-size: .85rem;
        line-height: 1.35;
    }

    .order-panel.help-enabled .helper-copy {
        display: block !important;
    }

    .motivo-section {
        margin-top: .75rem;
        padding: .85rem 1rem;
        border: 1px solid color-mix(in srgb, #ef4444 34%, var(--dashboard-border));
        border-radius: 10px;
        background: color-mix(in srgb, #ef4444 6%, var(--dashboard-surface));
    }

    .motivo-section label {
        color: #991b1b;
        font-weight: 700;
    }

    .form-group label {
        font-weight: 600;
        color: var(--dashboard-text);
    }

    .form-check-label {
        font-weight: 600;
    }

    .order-section .row {
        row-gap: .35rem;
    }

    /* ── Fotos existentes ── */
    .existing-photos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: 10px;
        margin-top: .75rem;
    }

    .existing-photo-card {
        position: relative;
        border: 1px solid var(--dashboard-border);
        border-radius: 10px;
        overflow: hidden;
        background: var(--dashboard-surface-soft);
        transition: opacity .2s ease;
    }

    /* Cuando está marcada para borrar */
    .existing-photo-card.is-marked {
        opacity: .45;
        border-color: #dc3545;
    }

    .existing-photo-card img {
        width: 100%;
        height: 100px;
        object-fit: cover;
        display: block;
    }

    .existing-photo-card__label {
        padding: .3rem .5rem;
        font-size: .78rem;
        color: var(--dashboard-muted);
        font-weight: 700;
        text-align: center;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .existing-photo-card a {
    display: block;
    cursor: zoom-in;
}

.existing-photo-card a:hover img {
    opacity: .88;
    transition: opacity .15s ease;
}

    .existing-photo-card__remove {
        position: absolute;
        top: 6px;
        right: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        border: 0;
        border-radius: 50%;
        background: rgba(15, 23, 42, .72);
        color: #fff;
        font-size: .75rem;
    }

    .existing-photo-card__remove:hover,
    .existing-photo-card.is-marked .existing-photo-card__remove {
        background: #dc3545;
    }

    /* ── Uploader de nuevas fotos ── */
    .photo-uploader {
        display: grid;
        gap: .75rem;
    }

    .photo-uploader__input {
        position: absolute;
        width: 1px;
        height: 1px;
        opacity: 0;
        pointer-events: none;
    }

    .photo-uploader__drop {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr) auto;
        align-items: center;
        gap: 1rem;
        min-height: 112px;
        margin: 0;
        padding: 1rem;
        border: 1px solid var(--dashboard-border);
        border-radius: 12px;
        background: var(--dashboard-surface-soft);
        color: var(--dashboard-text);
        cursor: pointer;
        transition: border-color .2s ease, background-color .2s ease, transform .2s ease;
    }

    .photo-uploader__drop:hover,
    .photo-uploader__drop.is-dragover {
        border-color: color-mix(in srgb, var(--dashboard-primary) 46%, var(--dashboard-border));
        background: var(--dashboard-primary-soft);
        transform: translateY(-1px);
    }

    .photo-uploader__icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 46px;
        height: 46px;
        border-radius: 12px;
        background: var(--dashboard-surface);
        color: var(--dashboard-primary);
        font-size: 1.1rem;
    }

    .photo-uploader__copy {
        display: grid;
        gap: .2rem;
        min-width: 0;
    }

    .photo-uploader__copy strong {
        color: var(--dashboard-text);
        font-size: 1rem;
    }

    .photo-uploader__copy span,
    .photo-uploader__status {
        color: var(--dashboard-muted);
        font-size: .9rem;
    }

    .photo-uploader__action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 38px;
        padding: 0 .9rem;
        border-radius: 999px;
        background: var(--dashboard-primary);
        color: #fff;
        font-weight: 800;
        white-space: nowrap;
    }

    .photo-token-fields {
        display: none;
    }

    .preview-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 12px;
    }

    .preview-card {
        position: relative;
        display: grid;
        gap: .45rem;
        background: var(--dashboard-surface);
        border: 1px solid var(--dashboard-border);
        border-radius: 12px;
        padding: 8px;
        min-width: 0;
    }

    .preview-card.is-uploading {
        border-color: color-mix(in srgb, var(--dashboard-primary) 45%, var(--dashboard-border));
    }

    .preview-card.is-error {
        border-color: #dc3545;
    }

    .preview-card img {
        width: 100%;
        height: 112px;
        object-fit: cover;
        border-radius: 8px;
    }

    .preview-card__name {
        overflow: hidden;
        color: var(--dashboard-text);
        font-size: .82rem;
        font-weight: 700;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .preview-card__status {
        color: var(--dashboard-muted);
        font-size: .78rem;
    }

    .preview-card__remove {
        position: absolute;
        top: 12px;
        right: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border: 0;
        border-radius: 50%;
        background: rgba(15, 23, 42, .78);
        color: #fff;
    }

    .preview-card__remove:hover,
    .preview-card__remove:focus {
        background: #dc3545;
        color: #fff;
    }

    @media (max-width: 767.98px) {
        .order-header { align-items: flex-start; }
        .order-help-toggle { width: 100%; justify-content: center; }
    }

    @media (max-width: 575.98px) {
        .photo-uploader__drop {
            grid-template-columns: 1fr;
            text-align: center;
        }
        .photo-uploader__icon,
        .photo-uploader__action { justify-self: center; }
    }
</style>

@if ($errors->any())
<div class="alert alert-danger">
    <strong>Hay datos pendientes por corregir.</strong>
    <ul class="mb-0 mt-2 ps-3">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('ordenes.update', $orden->id_ordenes) }}" enctype="multipart/form-data"
    id="ordenEditForm" novalidate>
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-12">
            <div class="card order-panel {{ $errors->any() ? 'help-enabled' : '' }}" id="ordenPanel">

                {{-- ── HEADER ── --}}
                <div class="card-header order-panel__header">
                    <div class="order-header">
                        <div class="order-header-copy">
                            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                <h3 class="card-title mb-0">Editar orden</h3>
                                <span class="order-id-badge">
                                    <i class="fas fa-hashtag" style="font-size:.75rem"></i>
                                    {{ $orden->id_ordenes }}
                                </span>
                                @php
                                    $statusClass = match($orden->status) {
                                        'finalizada' => 'is-finalizada',
                                        'cancelada'  => 'is-cancelada',
                                        default      => 'is-proceso',
                                    };
                                    $statusIcon = match($orden->status) {
                                        'finalizada' => 'fa-check-circle',
                                        'cancelada'  => 'fa-times-circle',
                                        default      => 'fa-clock',
                                    };
                                @endphp
                                <span class="order-status-badge {{ $statusClass }}">
                                    <i class="fas {{ $statusIcon }}" style="font-size:.72rem"></i>
                                    {{ ucfirst($orden->status) }}
                                </span>
                            </div>
                            <p>Actualiza los datos del cliente, la unidad y el servicio sin perder el contexto de la orden.</p>
                        </div>

                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <a href="{{ route('ordenes.show', $orden->id_ordenes) }}" class="btn btn-sm btn-outline-dark">
                                <i class="fas fa-eye me-1"></i> Ver detalle
                            </a>
                            <button type="button"
                                class="btn btn-sm order-help-toggle {{ $errors->any() ? 'is-active' : '' }}"
                                id="ordenHelpToggle"
                                data-bs-toggle="collapse" data-bs-target="#ordenHelpPanel"
                                aria-expanded="{{ $errors->any() ? 'true' : 'false' }}"
                                aria-pressed="{{ $errors->any() ? 'true' : 'false' }}"
                                aria-controls="ordenHelpPanel">
                                <i class="fas fa-life-ring"></i> Ayuda rápida
                            </button>
                        </div>
                    </div>

                    <div class="order-flow" aria-label="Flujo de captura">
                        <div class="order-flow__item"><span>1</span> Cliente</div>
                        <div class="order-flow__item"><span>2</span> Unidad</div>
                        <div class="order-flow__item"><span>3</span> Servicio</div>
                        <div class="order-flow__item"><span>4</span> Confirmación</div>
                    </div>
                </div>

                {{-- ── BODY ── --}}
                <div class="card-body">

                    {{-- Panel de ayuda --}}
                    <div class="collapse mb-4 {{ $errors->any() ? 'show' : '' }}" id="ordenHelpPanel">
                        <div class="help-panel">
                            <div class="help-panel-header">
                                <div>
                                    <p class="help-panel-title">Guía rápida para editar la orden</p>
                                    <p class="help-panel-copy">Modifica solo lo que necesites; los datos no tocados se conservan.</p>
                                </div>
                            </div>
                            <div class="help-grid">
                                <div class="help-block">
                                    <h4>Cliente</h4>
                                    <ul class="help-list">
                                        <li>Corrige nombre, teléfono, correo o RFC si hubo un error en el registro.</li>
                                        <li>El RFC debe tener 12 o 13 caracteres sin espacios.</li>
                                    </ul>
                                </div>
                                <div class="help-block">
                                    <h4>Unidad</h4>
                                    <ul class="help-list">
                                        <li>Verifica marca, tipo, línea, año y número de serie.</li>
                                        <li>Actualiza el kilometraje si ya cambió desde la recepción.</li>
                                    </ul>
                                </div>
                                <div class="help-block">
                                    <h4>Orden y estado</h4>
                                    <ul class="help-list">
                                        <li>Cambia el estado a "Finalizada" cuando el trabajo esté listo.</li>
                                        <li>Si cancelas la orden, escribe el motivo en el campo que aparece.</li>
                                        <li>La fecha de entrega puede ajustarse hasta 60 días desde hoy.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="order-intake-layout">
                        <div class="order-form-main">

                            {{-- ────────── SECCIÓN 1: CLIENTE ────────── --}}
                            <section class="order-section" id="orden-cliente">
                                <div class="order-section__header">
                                    <span class="order-section__badge">1</span>
                                    <div>
                                        <h4 class="order-section__title">Cliente</h4>
                                        <p class="order-section__copy">Datos de contacto vinculados a esta orden.</p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nombreCompleto">Nombre completo</label>
                                            <input type="text" name="nombreCompleto" id="nombreCompleto"
                                                class="form-control @error('nombreCompleto') is-invalid @enderror"
                                                value="{{ old('nombreCompleto', $orden->cliente->nombreCompleto) }}"
                                                maxlength="100" required>
                                            <small class="helper-copy">Captura nombre y apellidos, solo letras.</small>
                                            <div class="invalid-feedback">Escribe el nombre completo del cliente.</div>
                                            @error('nombreCompleto')
                                            <span class="text-danger d-block mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="telefono">Teléfono</label>
                                            <input type="text" name="telefono" id="telefono"
                                                class="form-control @error('telefono') is-invalid @enderror"
                                                value="{{ old('telefono', $orden->cliente->telefono) }}"
                                                inputmode="numeric" maxlength="10" required>
                                            <small class="helper-copy">10 dígitos del contacto principal.</small>
                                            <div class="invalid-feedback">Escribe un teléfono de 10 dígitos.</div>
                                            @error('telefono')
                                            <span class="text-danger d-block mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="correo">Correo electrónico</label>
                                            <input type="email" name="correo" id="correo"
                                                class="form-control @error('correo') is-invalid @enderror"
                                                value="{{ old('correo', $orden->cliente->correo) }}"
                                                maxlength="30" required>
                                            <small class="helper-copy">Formato nombre@dominio.com.</small>
                                            <div class="invalid-feedback">Escribe un correo electrónico válido.</div>
                                            @error('correo')
                                            <span class="text-danger d-block mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="rfc">RFC</label>
                                            <input type="text" name="rfc" id="rfc"
                                                class="form-control @error('rfc') is-invalid @enderror"
                                                value="{{ old('rfc', $orden->cliente->rfc) }}"
                                                maxlength="13" required>
                                            <small class="helper-copy">12 o 13 caracteres, sin espacios ni guiones.</small>
                                            <div class="invalid-feedback">Escribe un RFC válido de 12 o 13 caracteres.</div>
                                            @error('rfc')
                                            <span class="text-danger d-block mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </section>

                            {{-- ────────── SECCIÓN 2: UNIDAD ────────── --}}
                            <section class="order-section" id="orden-unidad">
                                <div class="order-section__header">
                                    <span class="order-section__badge">2</span>
                                    <div>
                                        <h4 class="order-section__title">Unidad</h4>
                                        <p class="order-section__copy">Vehículo asociado a esta orden de servicio.</p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="vehiculo_id">Marca</label>
                                            <select name="vehiculo_id" id="vehiculo_id"
                                                class="form-control @error('vehiculo_id') is-invalid @enderror" required>
                                                <option value="">Selecciona una marca</option>
                                                @foreach ($datosVehiculo as $vehiculo)
                                                <option value="{{ $vehiculo->id_vehiculo }}"
                                                    {{ (string) old('vehiculo_id', $orden->vehiculo_id) === (string) $vehiculo->id_vehiculo ? 'selected' : '' }}>
                                                    {{ $vehiculo->marca }}
                                                </option>
                                                @endforeach
                                            </select>
                                            <small class="helper-copy">Selecciona la marca registrada de la unidad.</small>
                                            <div class="invalid-feedback">Selecciona una marca.</div>
                                            @error('vehiculo_id')
                                            <span class="text-danger d-block mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="tvehiculo_id">Tipo de vehículo</label>
                                            <select name="tvehiculo_id" id="tvehiculo_id"
                                                class="form-control @error('tvehiculo_id') is-invalid @enderror" required>
                                                <option value="">Selecciona un tipo</option>
                                                @foreach ($tiposVehiculo as $tipoVehiculo)
                                                <option value="{{ $tipoVehiculo->id_tvehiculo }}"
                                                    {{ (string) old('tvehiculo_id', $orden->tipoVehiculo->id_tvehiculo) === (string) $tipoVehiculo->id_tvehiculo ? 'selected' : '' }}>
                                                    {{ $tipoVehiculo->tipo }}
                                                </option>
                                                @endforeach
                                            </select>
                                            <small class="helper-copy">Auto, camioneta u otra categoría.</small>
                                            <div class="invalid-feedback">Selecciona un tipo de vehículo.</div>
                                            @error('tvehiculo_id')
                                            <span class="text-danger d-block mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="modelo">Línea</label>
                                            <input type="text" name="modelo" id="modelo"
                                                class="form-control @error('modelo') is-invalid @enderror"
                                                value="{{ old('modelo', $orden->modelo) }}" maxlength="100" required>
                                            <small class="helper-copy">Ejemplo: Civic, Hilux, NP300.</small>
                                            <div class="invalid-feedback">Escribe la línea o modelo de la unidad.</div>
                                            @error('modelo')
                                            <span class="text-danger d-block mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="yearVehiculo">Año</label>
                                            <input type="text" name="yearVehiculo" id="yearVehiculo"
                                                class="form-control @error('yearVehiculo') is-invalid @enderror"
                                                value="{{ old('yearVehiculo', $orden->yearVehiculo) }}"
                                                inputmode="numeric" maxlength="4" required>
                                            <small class="helper-copy">4 dígitos.</small>
                                            <div class="invalid-feedback">Escribe un año válido de 4 dígitos.</div>
                                            @error('yearVehiculo')
                                            <span class="text-danger d-block mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="color">Color</label>
                                            <input type="text" name="color" id="color"
                                                class="form-control @error('color') is-invalid @enderror"
                                                value="{{ old('color', $orden->color) }}" maxlength="30" required>
                                            <small class="helper-copy">Color principal de la unidad.</small>
                                            <div class="invalid-feedback">Escribe el color de la unidad.</div>
                                            @error('color')
                                            <span class="text-danger d-block mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="placas">Placas</label>
                                            <input type="text" name="placas" id="placas"
                                                class="form-control @error('placas') is-invalid @enderror"
                                                value="{{ old('placas', $orden->placas) }}" maxlength="7" required>
                                            <small class="helper-copy">Letras y números, sin espacios.</small>
                                            <div class="invalid-feedback">Escribe las placas de la unidad.</div>
                                            @error('placas')
                                            <span class="text-danger d-block mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="kilometraje">Kilometraje</label>
                                            <div class="input-group">
                                                <input type="text" name="kilometraje" id="kilometraje"
                                                    class="form-control @error('kilometraje') is-invalid @enderror"
                                                    value="{{ old('kilometraje', $orden->kilometraje) }}"
                                                    inputmode="decimal" maxlength="8" required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Km</span>
                                                </div>
                                            </div>
                                            <small class="helper-copy">Kilometraje actual sin texto adicional.</small>
                                            <div class="invalid-feedback">Escribe el kilometraje actual.</div>
                                            @error('kilometraje')
                                            <span class="text-danger d-block mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="motor">Motor</label>
                                            <input type="text" name="motor" id="motor"
                                                class="form-control @error('motor') is-invalid @enderror"
                                                value="{{ old('motor', $orden->motor) }}" maxlength="10" required>
                                            <small class="helper-copy">Ejemplo: 2.0 o V6.</small>
                                            <div class="invalid-feedback">Escribe la información del motor.</div>
                                            @error('motor')
                                            <span class="text-danger d-block mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="cilindros">Cilindros</label>
                                            <input type="text" name="cilindros" id="cilindros"
                                                class="form-control @error('cilindros') is-invalid @enderror"
                                                value="{{ old('cilindros', $orden->cilindros) }}"
                                                inputmode="numeric" maxlength="4" required>
                                            <small class="helper-copy">Cantidad de cilindros.</small>
                                            <div class="invalid-feedback">Escribe el número de cilindros.</div>
                                            @error('cilindros')
                                            <span class="text-danger d-block mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="noSerievehiculo">Número de serie</label>
                                            <input type="text" name="noSerievehiculo" id="noSerievehiculo"
                                                class="form-control @error('noSerievehiculo') is-invalid @enderror"
                                                value="{{ old('noSerievehiculo', $orden->noSerievehiculo) }}"
                                                maxlength="17" required>
                                            <small class="helper-copy">VIN o número de serie, sin espacios.</small>
                                            <div class="invalid-feedback">Escribe un número de serie válido.</div>
                                            @error('noSerievehiculo')
                                            <span class="text-danger d-block mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </section>

                            {{-- ────────── SECCIÓN 3: SERVICIO ────────── --}}
                            <section class="order-section" id="orden-servicio">
                                <div class="order-section__header">
                                    <span class="order-section__badge">3</span>
                                    <div>
                                        <h4 class="order-section__title">Servicio y entrega</h4>
                                        <p class="order-section__copy">Trabajo, responsable, estado, fecha y notas del taller.</p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="servicio_id">Tipo de servicio</label>
                                            <select name="servicio_id" id="servicio_id"
                                                class="form-control @error('servicio_id') is-invalid @enderror" required>
                                                <option value="">Selecciona un servicio</option>
                                                @foreach ($tiposServicio as $tipoServicio)
                                                <option value="{{ $tipoServicio->id_servicio }}"
                                                    {{ (string) old('servicio_id', $orden->servicio->id_servicio) === (string) $tipoServicio->id_servicio ? 'selected' : '' }}>
                                                    {{ $tipoServicio->nombreServicio }}
                                                </option>
                                                @endforeach
                                            </select>
                                            <small class="helper-copy">Trabajo principal solicitado.</small>
                                            <div class="invalid-feedback">Selecciona el tipo de servicio.</div>
                                            @error('servicio_id')
                                            <span class="text-danger d-block mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="user_id">Atiende</label>
                                            <select name="user_id" id="user_id"
                                                class="form-control @error('user_id') is-invalid @enderror" required>
                                                <option value="">Selecciona un empleado</option>
                                                @foreach ($users as $user)
                                                @if ($user->id !== 1)
                                                <option value="{{ $user->id }}"
                                                    {{ (string) old('user_id', $orden->user->id) === (string) $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }}
                                                </option>
                                                @endif
                                                @endforeach
                                            </select>
                                            <small class="helper-copy">Responsable principal de la orden.</small>
                                            <div class="invalid-feedback">Selecciona quién atenderá la orden.</div>
                                            @error('user_id')
                                            <span class="text-danger d-block mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="status">Estado</label>
                                            <select name="status" id="status"
                                                class="form-control @error('status') is-invalid @enderror" required>
                                                <option value="">Selecciona un estado</option>
                                                <option value="en proceso" {{ old('status', $orden->status) === 'en proceso' ? 'selected' : '' }}>En proceso</option>
                                                <option value="finalizada"  {{ old('status', $orden->status) === 'finalizada'  ? 'selected' : '' }}>Finalizada</option>
                                                <option value="cancelada"   {{ old('status', $orden->status) === 'cancelada'   ? 'selected' : '' }}>Cancelada</option>
                                            </select>
                                            <small class="helper-copy">Cambia a "Finalizada" cuando el trabajo esté listo.</small>
                                            <div class="invalid-feedback">Selecciona el estado de la orden.</div>
                                            @error('status')
                                            <span class="text-danger d-block mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Campo motivo (solo visible si estado = cancelada) --}}
                                    <div class="col-md-12 {{ old('status', $orden->status) === 'cancelada' ? '' : 'd-none' }}" id="motivoWrapper">
                                        <div class="motivo-section">
                                            <label for="motivo">
                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                Motivo de cancelación
                                            </label>
                                            <input type="text" name="motivo" id="motivo"
                                                class="form-control mt-1 @error('motivo') is-invalid @enderror"
                                                value="{{ old('motivo', $orden->motivo ?? '') }}"
                                                placeholder="Describe brevemente el motivo de la cancelación">
                                            <small class="helper-copy">Campo requerido al cancelar una orden.</small>
                                            @error('motivo')
                                            <span class="text-danger d-block mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="retiroRefacciones">Refacciones</label>
                                            <select name="retiroRefacciones" id="retiroRefacciones"
                                                class="form-control @error('retiroRefacciones') is-invalid @enderror" required>
                                                <option value="">Selecciona una opción</option>
                                                <option value="0" {{ (string) old('retiroRefacciones', $orden->retiroRefacciones) === '0' ? 'selected' : '' }}>No retira</option>
                                                <option value="1" {{ (string) old('retiroRefacciones', $orden->retiroRefacciones) === '1' ? 'selected' : '' }}>Retira</option>
                                            </select>
                                            <small class="helper-copy">¿El cliente retira sus refacciones?</small>
                                            <div class="invalid-feedback">Selecciona una opción para refacciones.</div>
                                            @error('retiroRefacciones')
                                            <span class="text-danger d-block mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="fechaEntrega">Fecha estimada de entrega</label>
                                            <input type="text" name="fechaEntrega" id="fechaEntrega"
                                                class="form-control @error('fechaEntrega') is-invalid @enderror"
                                                value="{{ old('fechaEntrega', $orden->fechaEntrega) }}"
                                                placeholder="Selecciona una fecha" required>
                                            <small class="helper-copy">Hoy o fecha posterior, hasta 60 días.</small>
                                            <div class="invalid-feedback">Selecciona la fecha estimada de entrega.</div>
                                            @error('fechaEntrega')
                                            <span class="text-danger d-block mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="observacionesInt">Observaciones internas</label>
                                            <textarea name="observacionesInt" id="observacionesInt" rows="5"
                                                class="form-control @error('observacionesInt') is-invalid @enderror"
                                                required>{{ old('observacionesInt', $orden->observacionesInt) }}</textarea>
                                            <small class="helper-copy">Recepción, fallas detectadas o notas del taller.</small>
                                            <div class="invalid-feedback">Agrega las observaciones internas.</div>
                                            @error('observacionesInt')
                                            <span class="text-danger d-block mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="recomendacionesCliente">Recomendaciones del cliente</label>
                                            <textarea name="recomendacionesCliente" id="recomendacionesCliente" rows="5"
                                                class="form-control @error('recomendacionesCliente') is-invalid @enderror"
                                                required>{{ old('recomendacionesCliente', $orden->recomendacionesCliente) }}</textarea>
                                            <small class="helper-copy">Lo que pide o autoriza el cliente.</small>
                                            <div class="invalid-feedback">Agrega las recomendaciones del cliente.</div>
                                            @error('recomendacionesCliente')
                                            <span class="text-danger d-block mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="detallesOrden">Detalles del servicio</label>
                                            <textarea name="detallesOrden" id="detallesOrden" rows="5"
                                                class="form-control @error('detallesOrden') is-invalid @enderror"
                                                required>{{ old('detallesOrden', $orden->detallesOrden) }}</textarea>
                                            <small class="helper-copy">Trabajo que se realizará en esta orden.</small>
                                            <div class="invalid-feedback">Agrega los detalles del servicio.</div>
                                            @error('detallesOrden')
                                            <span class="text-danger d-block mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </section>

                            {{-- ────────── SECCIÓN 4: EVIDENCIA ────────── --}}
                            <section class="order-section" id="orden-cierre">
                                <div class="order-section__header">
                                    <span class="order-section__badge">4</span>
                                    <div>
                                        <h4 class="order-section__title">Evidencia y confirmación</h4>
                                        <p class="order-section__copy">Revisa las fotos existentes o agrega nuevas, y confirma los cambios.</p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Fotografías de apoyo</label>

                                            {{--
                                                ── CORRECCIÓN ──
                                                Se cambió $orden->photos  → $orden->fotografias
                                                Se cambió $photo->id      → $foto->id_fotografia
                                                Se cambió $photo->path    → eliminado: la imagen se sirve
                                                                            a través de showPhoto() igual
                                                                            que en show.blade.php
                                                El campo hidden ahora se llama delete_photo_ids[] y solo
                                                aparece cuando el usuario pulsa el botón de eliminar
                                                (no se envía nada si la foto se conserva).
                                            --}}
                                            @if ($orden->fotografias->isNotEmpty())
    <div class="existing-photos-grid" id="existingPhotosGrid">
        @foreach ($orden->fotografias as $foto)
        <div class="existing-photo-card"
             id="existingPhoto{{ $foto->id }}">
            {{-- Imagen clickeable que abre en nueva pestaña --}}
            <a href="{{ route('ordenes.photos.show', [$orden->id_ordenes, $foto->id]) }}"
               target="_blank" rel="noopener" title="Ver foto {{ $loop->iteration }} en tamaño completo">
                <img
                    src="{{ route('ordenes.photos.show', [$orden->id_ordenes, $foto->id]) }}"
                    alt="Foto de orden {{ $loop->iteration }}"
                    loading="lazy">
            </a>
            <button type="button"
                class="existing-photo-card__remove"
                aria-label="Eliminar fotografía {{ $loop->iteration }}"
                onclick="markPhotoForDelete({{ $foto->id }}, this)">
                <i class="fas fa-times"></i>
            </button>
            <span class="existing-photo-card__label">
                Foto {{ $loop->iteration }}
                <i class="fas fa-external-link-alt ms-1" style="font-size:.6rem; opacity:.6;"></i>
            </span>
        </div>
        @endforeach
    </div>
@else
    <p class="text-muted" style="font-size:.875rem">
        Esta orden no tiene fotografías registradas.
    </p>
@endif
                                            {{-- Contenedor donde JS inserta los hidden delete_photo_ids[] --}}
                                            <div id="deletePhotoInputs"></div>

                                            {{-- Uploader de nuevas fotos --}}
                                            <div class="photo-uploader mt-3" id="photoUploader"
                                                data-upload-url="{{ route('ordenes.photos.temporary.store') }}"
                                                data-delete-url="{{ route('ordenes.photos.temporary.destroy') }}">
                                                <input type="file" name="photos[]" id="photos"
                                                    class="photo-uploader__input"
                                                    accept="image/png,image/jpeg" multiple>
                                                <label for="photos" class="photo-uploader__drop" id="photoDropzone">
                                                    <span class="photo-uploader__icon" aria-hidden="true">
                                                        <i class="fas fa-camera"></i>
                                                    </span>
                                                    <span class="photo-uploader__copy">
                                                        <strong>Agregar fotografías nuevas</strong>
                                                        <span>JPG o PNG, máximo 2 MB por imagen.</span>
                                                    </span>
                                                    <span class="photo-uploader__action">Seleccionar</span>
                                                </label>
                                                <div class="photo-uploader__status" id="photoUploadStatus">
                                                    Sin fotografías nuevas.
                                                </div>
                                                <div class="photo-token-fields" id="photoTokenContainer">
                                                    @foreach ((array) old('photo_tokens', []) as $photoToken)
                                                    <input type="hidden" name="photo_tokens[]"
                                                        value="{{ $photoToken }}"
                                                        data-photo-token="{{ $photoToken }}">
                                                    @endforeach
                                                </div>
                                                <div id="photoPreviewContainer" class="preview-grid"></div>
                                            </div>

                                            <small class="helper-copy">
                                                Las fotos marcadas con X se eliminarán al guardar.
                                                Las nuevas se cifran antes de guardarse.
                                            </small>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-check mt-2">
                                            <input type="checkbox" class="form-check-input" id="clienteAcepta">
                                            <label class="form-check-label" for="clienteAcepta">
                                                Confirmo que los cambios son correctos y están autorizados.
                                            </label>
                                            <small class="helper-copy d-block mt-1">Este paso habilita el botón de actualización.</small>
                                            <div class="text-danger mt-2 d-none" id="clienteAceptaError">
                                                Debes confirmar los cambios antes de guardar.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>

                        </div>
                    </div>
                </div>

                {{-- ── FOOTER ── --}}
                <div class="card-footer text-center">
                    <div class="d-flex justify-content-between flex-wrap dashboard-inline-gap">
                        <a href="{{ route('ordenes.index') }}" class="btn btn-outline-dark">Retroceder</a>
                        <button type="submit" class="btn order-submit-button" id="submitButton" disabled>
                            <i class="fas fa-save"></i> Actualizar orden
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</form>
@stop

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
<script src="{{ asset('js/validatorFields.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var form           = document.getElementById('ordenEditForm');
    var submitButton   = document.getElementById('submitButton');
    var acceptCheckbox = document.getElementById('clienteAcepta');
    var acceptError    = document.getElementById('clienteAceptaError');
    var statusSelect   = document.getElementById('status');
    var motivoWrapper  = document.getElementById('motivoWrapper');
    var orderPanel     = document.getElementById('ordenPanel');
    var helpToggle     = document.getElementById('ordenHelpToggle');
    var helpPanel      = document.getElementById('ordenHelpPanel');
    var photoInput     = document.getElementById('photos');
    var photoUploader  = document.getElementById('photoUploader');
    var photoDropzone  = document.getElementById('photoDropzone');
    var photoStatus    = document.getElementById('photoUploadStatus');
    var photoTokenContainer   = document.getElementById('photoTokenContainer');
    var photoPreviewContainer = document.getElementById('photoPreviewContainer');
    // Contenedor donde se insertan los hidden delete_photo_ids[]
    var deletePhotoInputs = document.getElementById('deletePhotoInputs');
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var pendingPhotoUploads = 0;

    var fields = {
        nombreCompleto:         document.getElementById('nombreCompleto'),
        telefono:               document.getElementById('telefono'),
        correo:                 document.getElementById('correo'),
        rfc:                    document.getElementById('rfc'),
        modelo:                 document.getElementById('modelo'),
        yearVehiculo:           document.getElementById('yearVehiculo'),
        color:                  document.getElementById('color'),
        placas:                 document.getElementById('placas'),
        kilometraje:            document.getElementById('kilometraje'),
        motor:                  document.getElementById('motor'),
        cilindros:              document.getElementById('cilindros'),
        noSerievehiculo:        document.getElementById('noSerievehiculo'),
        vehiculo_id:            document.getElementById('vehiculo_id'),
        tvehiculo_id:           document.getElementById('tvehiculo_id'),
        servicio_id:            document.getElementById('servicio_id'),
        user_id:                document.getElementById('user_id'),
        status:                 document.getElementById('status'),
        retiroRefacciones:      document.getElementById('retiroRefacciones'),
        fechaEntrega:           document.getElementById('fechaEntrega'),
        observacionesInt:       document.getElementById('observacionesInt'),
        recomendacionesCliente: document.getElementById('recomendacionesCliente'),
        detallesOrden:          document.getElementById('detallesOrden'),
    };

    // ── Flatpickr ──
    flatpickr('#fechaEntrega', {
        locale: 'es',
        altInput: true,
        altFormat: 'd/m/Y',
        dateFormat: 'Y-m-d',
        minDate: 'today',
        maxDate: new Date().fp_incr(60)
    });

    // ── Help toggle ──
    function syncHelpMode() {
        if (!orderPanel || !helpToggle || !helpPanel) return;
        var enabled = helpPanel.classList.contains('show') || helpToggle.getAttribute('aria-expanded') === 'true';
        orderPanel.classList.toggle('help-enabled', enabled);
        helpToggle.classList.toggle('is-active', enabled);
        helpToggle.setAttribute('aria-pressed', enabled ? 'true' : 'false');
    }

    if (helpPanel) {
        helpPanel.addEventListener('shown.bs.collapse',  syncHelpMode);
        helpPanel.addEventListener('hidden.bs.collapse', syncHelpMode);
    }
    if (helpToggle) {
        helpToggle.addEventListener('click', function () { window.setTimeout(syncHelpMode, 250); });
    }

    // ── Estado → campo motivo ──
    function syncMotivoVisibility() {
        if (!motivoWrapper) return;
        var isCancelada = statusSelect.value === 'cancelada';
        motivoWrapper.classList.toggle('d-none', !isCancelada);
        if (!isCancelada) {
            var motivoInput = document.getElementById('motivo');
            if (motivoInput) motivoInput.value = '';
        }
    }

    statusSelect.addEventListener('change', function () {
        syncMotivoVisibility();
        validateAllFields();
    });

    // ── Sanitizers ──
    function sanitizeName(v)        { return v.replace(/[^A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]/g,'').replace(/\s+/g,' ').trimStart().replace(/\b\w/g,l=>l.toUpperCase()); }
    function sanitizeAlphaText(v)   { return v.replace(/[^A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]/g,'').replace(/\s+/g,' ').trimStart(); }
    function sanitizeModel(v)       { return v.replace(/[^A-Za-z0-9ÁÉÍÓÚÜÑáéíóúüñ\s-]/g,'').replace(/\s+/g,' ').trimStart(); }
    function sanitizeDigits(v)      { return v.replace(/\D+/g,''); }
    function sanitizeAlphaNumeric(v){ return v.replace(/[^A-Za-z0-9]/g,'').toUpperCase(); }
    function sanitizeDecimal(v)     { return v.replace(/[^0-9.]/g,''); }

    fields.nombreCompleto.addEventListener('input', function () { this.value = sanitizeName(this.value); validateAllFields(); });
    fields.telefono.addEventListener('input',       function () { this.value = sanitizeDigits(this.value).slice(0,10); validateAllFields(); });
    fields.correo.addEventListener('input',         function () { this.value = this.value.toLowerCase().trim(); validateAllFields(); });
    fields.rfc.addEventListener('input',            function () { this.value = sanitizeAlphaNumeric(this.value).slice(0,13); validateAllFields(); });
    fields.modelo.addEventListener('input',         function () { this.value = sanitizeModel(this.value); validateAllFields(); });
    fields.yearVehiculo.addEventListener('input',   function () { this.value = sanitizeDigits(this.value).slice(0,4); validateAllFields(); });
    fields.color.addEventListener('input',          function () { this.value = sanitizeAlphaText(this.value).slice(0,30); validateAllFields(); });
    fields.placas.addEventListener('input',         function () { this.value = sanitizeAlphaNumeric(this.value).slice(0,7); validateAllFields(); });
    fields.kilometraje.addEventListener('input',    function () { this.value = sanitizeDecimal(this.value).slice(0,8); validateAllFields(); });
    fields.motor.addEventListener('input',          function () { this.value = this.value.replace(/[^A-Za-z0-9.]/g,'').toUpperCase().slice(0,10); validateAllFields(); });
    fields.cilindros.addEventListener('input',      function () { this.value = sanitizeDigits(this.value).slice(0,4); validateAllFields(); });
    fields.noSerievehiculo.addEventListener('input',function () { this.value = sanitizeAlphaNumeric(this.value).slice(0,17); validateAllFields(); });

    [fields.vehiculo_id, fields.tvehiculo_id, fields.servicio_id, fields.user_id,
     fields.status, fields.retiroRefacciones, fields.fechaEntrega,
     fields.observacionesInt, fields.recomendacionesCliente, fields.detallesOrden
    ].forEach(function (f) {
        f.addEventListener('change', validateAllFields);
        f.addEventListener('input',  validateAllFields);
    });

    // ── Validación ──
    function validateField(field, validator) {
        if (!field || typeof validator !== 'function') return true;
        var result = validator(field);
        field.setCustomValidity(result.valid ? '' : result.message);
        return result.valid;
    }

    function validateAllFields() {
        var validators = [
            function () { return validateField(fields.nombreCompleto, function (f) {
                var v = f.value.trim();
                return { valid: v.length >= 5 && /^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]+$/.test(v), message: 'Escribe nombre y apellidos usando solo letras.' };
            }); },
            function () { return validateField(fields.telefono, function (f) {
                return { valid: /^\d{10}$/.test(f.value), message: 'El teléfono debe llevar 10 dígitos.' };
            }); },
            function () { return validateField(fields.correo, function (f) {
                return { valid: /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(f.value.trim()), message: 'Captura un correo con formato nombre@dominio.com.' };
            }); },
            function () { return validateField(fields.rfc, function (f) {
                return { valid: /^[A-Z0-9]{12,13}$/.test(f.value.trim()), message: 'El RFC debe tener 12 o 13 caracteres alfanuméricos.' };
            }); },
            function () { return validateField(fields.modelo, function (f) {
                return { valid: f.value.trim().length >= 2, message: 'Escribe la línea o modelo de la unidad.' };
            }); },
            function () { return validateField(fields.yearVehiculo, function (f) {
                return { valid: /^\d{4}$/.test(f.value), message: 'Captura el año con 4 dígitos.' };
            }); },
            function () { return validateField(fields.color, function (f) {
                return { valid: f.value.trim().length >= 3, message: 'Escribe el color principal de la unidad.' };
            }); },
            function () { return validateField(fields.placas, function (f) {
                return { valid: /^[A-Z0-9]{5,7}$/.test(f.value), message: 'Las placas deben tener entre 5 y 7 caracteres.' };
            }); },
            function () { return validateField(fields.kilometraje, function (f) {
                return { valid: f.value.trim() !== '' && !isNaN(f.value), message: 'Captura un kilometraje válido.' };
            }); },
            function () { return validateField(fields.motor, function (f) {
                return { valid: f.value.trim().length >= 2, message: 'Escribe la referencia del motor.' };
            }); },
            function () { return validateField(fields.cilindros, function (f) {
                return { valid: f.value.trim() !== '' && !isNaN(f.value), message: 'Captura la cantidad de cilindros.' };
            }); },
            function () { return validateField(fields.noSerievehiculo, function (f) {
                return { valid: /^[A-Z0-9]{5,17}$/.test(f.value), message: 'El número de serie debe tener entre 5 y 17 caracteres.' };
            }); },
            function () { return validateField(fields.vehiculo_id,       function (f) { return { valid: !!f.value, message: 'Selecciona una marca.' }; }); },
            function () { return validateField(fields.tvehiculo_id,      function (f) { return { valid: !!f.value, message: 'Selecciona un tipo de vehículo.' }; }); },
            function () { return validateField(fields.servicio_id,       function (f) { return { valid: !!f.value, message: 'Selecciona un tipo de servicio.' }; }); },
            function () { return validateField(fields.user_id,           function (f) { return { valid: !!f.value, message: 'Selecciona al responsable.' }; }); },
            function () { return validateField(fields.status,            function (f) { return { valid: !!f.value, message: 'Selecciona un estado.' }; }); },
            function () { return validateField(fields.retiroRefacciones, function (f) { return { valid: f.value === '0' || f.value === '1', message: 'Indica si el cliente retira refacciones.' }; }); },
            function () { return validateField(fields.fechaEntrega,      function (f) { return { valid: !!f.value, message: 'Selecciona la fecha de entrega.' }; }); },
            function () { return validateField(fields.observacionesInt,       function (f) { return { valid: f.value.trim().length >= 10, message: 'Describe al menos una observación interna.' }; }); },
            function () { return validateField(fields.recomendacionesCliente, function (f) { return { valid: f.value.trim().length >= 10, message: 'Describe la recomendación del cliente.' }; }); },
            function () { return validateField(fields.detallesOrden,          function (f) { return { valid: f.value.trim().length >= 10, message: 'Describe el detalle del servicio.' }; }); },
        ];

        var valid = true;
        validators.forEach(function (run) { if (!run()) valid = false; });
        return valid;
    }

    // ── Checkbox de aceptación ──
    function updateAcceptanceState(showError) {
        var accepted = acceptCheckbox.checked;
        submitButton.disabled = !accepted || pendingPhotoUploads > 0;
        acceptError.classList.toggle('d-none', accepted || !showError);
    }

    acceptCheckbox.addEventListener('change', function () { updateAcceptanceState(false); });

    // ── CORRECCIÓN: marcar foto para borrar en lugar de ocultar la tarjeta ──
    // Togglea el estado: primer clic = marcar, segundo clic = desmarcar
    window.markPhotoForDelete = function (fotoId, btn) {
        var card  = document.getElementById('existingPhoto' + fotoId);
        var input = document.getElementById('deleteInput' + fotoId);

        if (!card) return;

        var isMarked = card.classList.toggle('is-marked');

        if (isMarked) {
            // Crear el hidden input que le dice al controlador que borre esta foto
            if (!input) {
                input = document.createElement('input');
                input.type  = 'hidden';
                input.name  = 'delete_photo_ids[]';
                input.value = fotoId;
                input.id    = 'deleteInput' + fotoId;
                deletePhotoInputs.appendChild(input);
            }
            btn.setAttribute('aria-label', 'Deshacer eliminación');
            btn.title = 'Clic para deshacer';
        } else {
            // Desmarcar: quitar el hidden input
            if (input) input.remove();
            btn.setAttribute('aria-label', 'Eliminar fotografía');
            btn.title = '';
        }
    };

    // ── Upload de nuevas fotos (sin cambios) ──
    function formatFileSize(bytes) {
        if (!bytes) return '0 KB';
        if (bytes < 1024 * 1024) return Math.round(bytes / 1024) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }

    function setPhotoStatus(message, isError) {
        if (!photoStatus) return;
        photoStatus.textContent = message;
        photoStatus.classList.toggle('text-danger', !!isError);
    }

    function savedPhotoCount() {
        return photoTokenContainer ? photoTokenContainer.querySelectorAll('input[name="photo_tokens[]"]').length : 0;
    }

    function refreshPhotoStatus() {
        var savedCount = savedPhotoCount();
        if (pendingPhotoUploads > 0) {
            setPhotoStatus('Cifrando y guardando ' + pendingPhotoUploads + ' fotografía(s)...', false);
        } else if (savedCount > 0) {
            setPhotoStatus(savedCount + ' fotografía(s) nuevas listas.', false);
        } else {
            setPhotoStatus('Sin fotografías nuevas.', false);
        }
        updateAcceptanceState(false);
    }

    function addPhotoToken(token) {
        if (!photoTokenContainer || !token) return;
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'photo_tokens[]';
        input.value = token;
        input.dataset.photoToken = token;
        photoTokenContainer.appendChild(input);
    }

    function removePhotoToken(token) {
        if (!photoTokenContainer || !token) return;
        var t = photoTokenContainer.querySelector('[data-photo-token="' + token + '"]');
        if (t) t.remove();
    }

    function deleteTemporaryPhoto(token) {
        if (!photoUploader || !token) return;
        fetch(photoUploader.dataset.deleteUrl, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ token: token })
        }).catch(function () {});
    }

    function cardStatusMessage(error) {
        if (error && error.errors && error.errors.photo && error.errors.photo.length) return error.errors.photo[0];
        return error && error.message ? error.message : 'No se pudo guardar esta fotografía.';
    }

    function createPhotoCard(file) {
        var card   = document.createElement('div');
        card.className = 'preview-card is-uploading';

        var image  = document.createElement('img');
        image.alt  = 'Vista previa';
        image.src  = URL.createObjectURL(file);
        image.addEventListener('load', function () { URL.revokeObjectURL(image.src); });

        var removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.className = 'preview-card__remove';
        removeButton.setAttribute('aria-label', 'Quitar fotografía');
        removeButton.disabled = true;
        removeButton.innerHTML = '<i class="fas fa-times"></i>';

        var name   = document.createElement('span'); name.className = 'preview-card__name'; name.textContent = file.name;
        var status = document.createElement('span'); status.className = 'preview-card__status'; status.textContent = 'Cifrando ' + formatFileSize(file.size);

        removeButton.addEventListener('click', function () {
            var token = card.dataset.token;
            removePhotoToken(token);
            deleteTemporaryPhoto(token);
            card.remove();
            refreshPhotoStatus();
        });

        card.appendChild(image);
        card.appendChild(removeButton);
        card.appendChild(name);
        card.appendChild(status);
        photoPreviewContainer.appendChild(card);
        return { card, status, removeButton };
    }

    function uploadPhoto(file) {
        if (!photoUploader || !photoPreviewContainer) return;
        if (!/^image\/(jpeg|png)$/.test(file.type)) { setPhotoStatus('Solo puedes agregar imágenes JPG o PNG.', true); return; }
        if (file.size > 2 * 1024 * 1024)             { setPhotoStatus('Cada fotografía puede pesar hasta 2 MB.', true); return; }

        var preview = createPhotoCard(file);
        var formData = new FormData();
        formData.append('photo', file);
        pendingPhotoUploads += 1;
        refreshPhotoStatus();

        fetch(photoUploader.dataset.uploadUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: formData
        })
        .then(function (r) { return r.json().then(function (d) { if (!r.ok) throw d; return d; }); })
        .then(function (data) {
            preview.card.dataset.token = data.token;
            preview.card.classList.remove('is-uploading', 'is-error');
            preview.status.textContent = 'Cifrada y lista';
            preview.removeButton.disabled = false;
            addPhotoToken(data.token);
        })
        .catch(function (error) {
            preview.card.classList.remove('is-uploading');
            preview.card.classList.add('is-error');
            preview.status.textContent = cardStatusMessage(error);
            preview.removeButton.disabled = false;
            setPhotoStatus(cardStatusMessage(error), true);
        })
        .finally(function () {
            pendingPhotoUploads = Math.max(0, pendingPhotoUploads - 1);
            photoInput.value = '';
            refreshPhotoStatus();
        });
    }

    function handlePhotoFiles(files) { Array.from(files || []).forEach(uploadPhoto); }

    photoInput.addEventListener('change', function () { handlePhotoFiles(photoInput.files); });

    if (photoDropzone) {
        ['dragenter','dragover'].forEach(function (e) {
            photoDropzone.addEventListener(e, function (ev) { ev.preventDefault(); photoDropzone.classList.add('is-dragover'); });
        });
        ['dragleave','drop'].forEach(function (e) {
            photoDropzone.addEventListener(e, function (ev) { ev.preventDefault(); photoDropzone.classList.remove('is-dragover'); });
        });
        photoDropzone.addEventListener('drop', function (ev) { handlePhotoFiles(ev.dataTransfer.files); });
    }

    // ── Submit ──
    form.addEventListener('submit', function (event) {
        var valid = validateAllFields();
        updateAcceptanceState(true);

        if (pendingPhotoUploads > 0) {
            event.preventDefault();
            event.stopPropagation();
            setPhotoStatus('Espera a que terminen de cifrarse las fotografías.', true);
        }
        if (!acceptCheckbox.checked) { event.preventDefault(); event.stopPropagation(); }
        if (!valid || !form.checkValidity()) { event.preventDefault(); event.stopPropagation(); }

        form.classList.add('was-validated');
    });

    if (window.FormHelpers) {
        window.FormHelpers.attachSubmitLoading(form, '#submitButton', 'Actualizando orden...');
    }

    // ── Init ──
    syncHelpMode();
    syncMotivoVisibility();
    refreshPhotoStatus();
    updateAcceptanceState(false);
    validateAllFields();
});
</script>
@endsection