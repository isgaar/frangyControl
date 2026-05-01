@extends('layouts.dashboard')

@section('title', 'Registrar orden')

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
    $usingExistingClient = (bool) old('usar_cliente_existente', $preferExistingClient ?? false);
@endphp
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

    .order-mode-card {
        margin-bottom: 1rem;
        padding: .9rem 1rem;
        border: 1px solid var(--dashboard-border);
        border-radius: 12px;
        background: var(--dashboard-surface-soft);
    }

    .order-mode-card__content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .order-mode-card__actions {
        display: flex;
        align-items: center;
        gap: .5rem;
        margin-left: auto;
    }

    .order-client-check {
        display: grid;
        gap: .35rem;
        min-width: min(100%, 310px);
    }

    .order-client-check__button {
        display: inline-flex;
        align-items: center;
        justify-content: flex-start;
        gap: .55rem;
        width: max-content;
        max-width: 100%;
        min-height: 40px;
        padding: .5rem .85rem;
        border: 1px solid var(--dashboard-border);
        border-radius: 999px;
        background: var(--dashboard-surface);
        color: var(--dashboard-text);
        font-weight: 800;
        text-align: left;
        white-space: normal;
    }

    .order-client-check__button:hover,
    .order-client-check__button:focus {
        border-color: color-mix(in srgb, var(--dashboard-primary) 40%, var(--dashboard-border));
        background: var(--dashboard-primary-soft);
        color: var(--dashboard-primary-strong);
    }

    .order-client-check__icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: var(--dashboard-primary-soft);
        color: var(--dashboard-primary);
        font-size: .78rem;
    }

    .btn-check:checked + .order-client-check__button {
        border-color: var(--dashboard-primary);
        background: var(--dashboard-primary);
        color: #fff;
        box-shadow: 0 10px 24px color-mix(in srgb, var(--dashboard-primary) 26%, transparent);
    }

    .btn-check:checked + .order-client-check__button .order-client-check__icon {
        background: rgba(255, 255, 255, .18);
        color: #fff;
    }

    .btn-check:focus + .order-client-check__button {
        border-color: var(--dashboard-primary);
        box-shadow: 0 0 0 .2rem color-mix(in srgb, var(--dashboard-primary) 22%, transparent);
    }

    .selected-client-summary {
        display: grid;
        gap: .2rem;
        margin-top: .75rem;
        padding: .75rem .85rem;
        border: 1px solid color-mix(in srgb, var(--dashboard-primary) 32%, var(--dashboard-border));
        border-radius: 8px;
        background: var(--dashboard-primary-soft);
        color: var(--dashboard-text);
    }

    .selected-client-summary strong {
        font-size: .95rem;
        line-height: 1.25;
    }

    .selected-client-summary span {
        color: var(--dashboard-muted);
        font-size: .85rem;
    }

    .client-search-modal .modal-body {
        display: grid;
        gap: .85rem;
    }

    .client-search-modal__eyebrow {
        display: block;
        color: var(--dashboard-muted);
        font-size: .78rem;
        font-weight: 800;
        letter-spacing: .02em;
        text-transform: uppercase;
    }

    .client-search-input .input-group-text {
        border-color: var(--dashboard-border);
        background: var(--dashboard-surface-soft);
        color: var(--dashboard-primary);
    }

    .client-search-meta {
        min-height: 1.25rem;
        color: var(--dashboard-muted);
        font-size: .86rem;
        font-weight: 700;
    }

    .client-search-results {
        display: grid;
        gap: .55rem;
        max-height: 420px;
        overflow: auto;
        padding-right: .15rem;
    }

    .client-search-item {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: .75rem;
        align-items: center;
        padding: .8rem;
        border: 1px solid var(--dashboard-border);
        border-radius: 8px;
        background: var(--dashboard-surface);
    }

    .client-search-item.is-selected {
        border-color: color-mix(in srgb, var(--dashboard-primary) 48%, var(--dashboard-border));
        background: var(--dashboard-primary-soft);
    }

    .client-search-item__content {
        display: grid;
        gap: .35rem;
        min-width: 0;
    }

    .client-search-item__name {
        overflow: hidden;
        color: var(--dashboard-text);
        font-weight: 800;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .client-search-item__meta {
        display: flex;
        flex-wrap: wrap;
        gap: .35rem .7rem;
        color: var(--dashboard-muted);
        font-size: .84rem;
    }

    .client-search-empty {
        padding: 1rem;
        border: 1px dashed var(--dashboard-border);
        border-radius: 8px;
        color: var(--dashboard-muted);
        text-align: center;
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

    .order-section .row {
        row-gap: .35rem;
    }

    .section-heading {
        margin: 2rem 0 1rem;
        padding-bottom: 0.65rem;
        border-bottom: 1px solid #e2e8f0;
        font-size: 1.1rem;
        font-weight: 700;
        color: #0f172a;
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

    .preview-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
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

    .readonly-field {
        background-color: #f8f9fa;
    }

    .help-list {
        padding-left: 1rem;
        margin-bottom: 0;
    }

    .help-list li + li {
        margin-top: 0.5rem;
    }

    .form-group label {
        font-weight: 600;
        color: var(--dashboard-text);
    }

    .form-check-label {
        font-weight: 600;
    }

    @media (max-width: 767.98px) {
        .order-header {
            align-items: flex-start;
        }

        .order-help-toggle {
            width: 100%;
            justify-content: center;
        }

        .client-search-item {
            grid-template-columns: 1fr;
        }

        .client-search-item .btn {
            width: 100%;
        }
    }

    @media (max-width: 575.98px) {
        .photo-uploader__drop {
            grid-template-columns: 1fr;
            text-align: center;
        }

        .photo-uploader__icon,
        .photo-uploader__action {
            justify-self: center;
        }
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

<form method="POST" action="{{ route('ordenes.store') }}" enctype="multipart/form-data" id="ordenRegistroForm"
    novalidate>
    @csrf

    <div class="row">
        <div class="col-12">
            <div class="card order-panel {{ $errors->any() ? 'help-enabled' : '' }}" id="ordenPanel">
                <div class="card-header order-panel__header">
                    <div class="order-header">
                        <div class="order-header-copy">
                            <h3 class="card-title mb-0">Registrar orden</h3>
                            <p>Avanza por bloques: identifica al cliente, describe la unidad y cierra los datos del servicio.</p>
                        </div>

                        <button type="button" class="btn btn-sm order-help-toggle {{ $errors->any() ? 'is-active' : '' }}"
                            id="ordenHelpToggle" data-bs-toggle="collapse" data-bs-target="#ordenHelpPanel"
                            aria-expanded="{{ $errors->any() ? 'true' : 'false' }}" aria-pressed="{{ $errors->any() ? 'true' : 'false' }}"
                            aria-controls="ordenHelpPanel">
                            <i class="fas fa-life-ring"></i> Ayuda rápida
                        </button>
                    </div>

                    <div class="order-flow" aria-label="Flujo de captura">
                        <div class="order-flow__item"><span>1</span> Cliente</div>
                        <div class="order-flow__item"><span>2</span> Unidad</div>
                        <div class="order-flow__item"><span>3</span> Servicio</div>
                        <div class="order-flow__item"><span>4</span> Confirmación</div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="collapse mb-4 {{ $errors->any() ? 'show' : '' }}" id="ordenHelpPanel">
                        <div class="help-panel">
                            <div class="help-panel-header">
                                <div>
                                    <p class="help-panel-title">Guía rápida para capturar la orden</p>
                                    <p class="help-panel-copy">La ayuda queda disponible solo cuando la necesites.</p>
                                </div>
                            </div>

                            <div class="help-grid">
                                <div class="help-block">
                                    <h4>Cliente</h4>
                                    <ul class="help-list">
                                        <li>Si ya existe, activa el selector para evitar duplicados.</li>
                                        <li>Captura nombre, teléfono, correo y RFC solo si es un cliente nuevo.</li>
                                    </ul>
                                </div>

                                <div class="help-block">
                                    <h4>Unidad</h4>
                                    <ul class="help-list">
                                        <li>Completa marca, tipo, línea, año y número de serie.</li>
                                        <li>Agrega placas, kilometraje, motor y cilindros para dejarla bien identificada.</li>
                                    </ul>
                                </div>

                                <div class="help-block">
                                    <h4>Orden</h4>
                                    <ul class="help-list">
                                        <li>Describe observaciones, recomendaciones y detalle del servicio.</li>
                                        <li>La fecha de entrega debe ser hoy o una fecha posterior.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="order-intake-layout">
                        <div class="order-form-main">
                            <section class="order-section" id="orden-cliente">
                                <div class="order-section__header">
                                    <span class="order-section__badge">1</span>
                                    <div>
                                        <h4 class="order-section__title">Cliente</h4>
                                        <p class="order-section__copy">Busca al cliente si ya existe o captura sus datos de contacto.</p>
                                    </div>
                                </div>

                                <div class="order-mode-card">
                                    <div class="order-mode-card__content">
                                        <div class="order-client-check">
                                            <input type="checkbox" class="btn-check" id="usarClienteExistente"
                                                name="usar_cliente_existente" value="1" autocomplete="off" {{ $usingExistingClient ? 'checked' : '' }}>
                                            <label class="btn order-client-check__button" for="usarClienteExistente">
                                                <span class="order-client-check__icon" aria-hidden="true">
                                                    <i class="fas fa-user-check"></i>
                                                </span>
                                                <span>Usar cliente ya registrado</span>
                                            </label>
                                            <div class="helper-copy">Activa esta opción si el cliente ya existe y quieres rellenar sus
                                                datos automáticamente.</div>
                                        </div>
                                        <div class="order-mode-card__actions">
                                            <button type="button" class="btn btn-outline-dark btn-sm" id="abrirBuscadorCliente"
                                                data-bs-toggle="modal" data-bs-target="#clienteSearchModal"
                                                {{ $usingExistingClient ? '' : 'disabled' }}>
                                                <i class="fas fa-search me-1"></i> Buscar cliente
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group {{ $usingExistingClient ? '' : 'd-none' }}" id="clienteExistenteBox">
                                    <input type="hidden" name="cliente_existente_id" id="cliente_existente_id"
                                        value="{{ old('cliente_existente_id') }}">
                                    <div class="selected-client-summary d-none" id="selectedClientSummary" aria-live="polite"></div>
                                    <small class="helper-copy">El sistema cargará nombre, teléfono, correo y RFC del cliente
                                        seleccionado.</small>
                                    <div class="text-danger mt-2 d-none" id="clienteExistenteError">Selecciona un cliente registrado.</div>
                                    @error('cliente_existente_id')
                                    <span class="text-danger d-block mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <select id="clienteDirectorySource" class="d-none" hidden aria-hidden="true" tabindex="-1">
                                    <option value="">Selecciona un cliente</option>
                                    @foreach ($clientes as $cliente)
                                    <option value="{{ $cliente->id_cliente }}"
                                        data-nombre="{{ $cliente->nombreCompleto }}"
                                        data-telefono="{{ $cliente->telefono }}"
                                        data-correo="{{ $cliente->correo }}"
                                        data-rfc="{{ $cliente->rfc }}"
                                        {{ (string) old('cliente_existente_id') === (string) $cliente->id_cliente ? 'selected' : '' }}>
                                        {{ $cliente->nombreCompleto }} - {{ $cliente->telefono }}
                                    </option>
                                    @endforeach
                                </select>

                                <div id="clienteExistenteHint" class="alert alert-warning d-none py-2">
                                    Ya existe un cliente con ese nombre. Puedes activar "Usar cliente ya registrado" para cargarlo
                                    automáticamente.
                                </div>

                                <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombreCompleto">Nombre completo</label>
                                <input type="text" name="nombreCompleto" id="nombreCompleto"
                                    class="form-control @error('nombreCompleto') is-invalid @enderror"
                                    value="{{ old('nombreCompleto') }}" maxlength="100" required>
                                <small class="helper-copy">Captura nombre y apellidos, sin números ni símbolos.</small>
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
                                    value="{{ old('telefono') }}" inputmode="numeric" maxlength="10" required>
                                <small class="helper-copy">Ingresa 10 dígitos del contacto principal.</small>
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
                                    value="{{ old('correo') }}" maxlength="30" required>
                                <small class="helper-copy">Usa formato tipo nombre@dominio.com.</small>
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
                                    class="form-control @error('rfc') is-invalid @enderror" value="{{ old('rfc') }}"
                                    maxlength="13" required>
                                <small class="helper-copy">Captura 12 o 13 caracteres, sin espacios ni guiones.</small>
                                <div class="invalid-feedback">Escribe un RFC válido de 12 o 13 caracteres.</div>
                                @error('rfc')
                                <span class="text-danger d-block mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                                </div>
                            </section>

                            <section class="order-section" id="orden-unidad">
                                <div class="order-section__header">
                                    <span class="order-section__badge">2</span>
                                    <div>
                                        <h4 class="order-section__title">Unidad</h4>
                                        <p class="order-section__copy">Identifica el vehículo con los datos que después aparecerán en la orden.</p>
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
                                        {{ (string) old('vehiculo_id') === (string) $vehiculo->id_vehiculo ? 'selected' : '' }}>
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
                                        {{ (string) old('tvehiculo_id') === (string) $tipoVehiculo->id_tvehiculo ? 'selected' : '' }}>
                                        {{ $tipoVehiculo->tipo }}
                                    </option>
                                    @endforeach
                                </select>
                                <small class="helper-copy">Indica si es auto, camioneta u otra categoría disponible.</small>
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
                                    value="{{ old('modelo') }}" maxlength="100" required>
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
                                    value="{{ old('yearVehiculo') }}" inputmode="numeric" maxlength="4" required>
                                <small class="helper-copy">Captura el año con 4 dígitos.</small>
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
                                    value="{{ old('color') }}" maxlength="30" required>
                                <small class="helper-copy">Describe el color principal de la unidad.</small>
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
                                    value="{{ old('placas') }}" maxlength="7" required>
                                <small class="helper-copy">Usa letras y números, sin espacios.</small>
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
                                        value="{{ old('kilometraje') }}" inputmode="decimal" maxlength="8" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">Km</span>
                                    </div>
                                </div>
                                <small class="helper-copy">Ingresa el kilometraje actual sin texto adicional.</small>
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
                                    value="{{ old('motor') }}" maxlength="10" required>
                                <small class="helper-copy">Captura la referencia del motor, por ejemplo 2.0 o V6.</small>
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
                                    value="{{ old('cilindros') }}" inputmode="numeric" maxlength="4" required>
                                <small class="helper-copy">Ingresa la cantidad de cilindros de la unidad.</small>
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
                                    value="{{ old('noSerievehiculo') }}" maxlength="17" required>
                                <small class="helper-copy">Usa letras y números del VIN o número de serie, sin
                                    espacios.</small>
                                <div class="invalid-feedback">Escribe un número de serie válido.</div>
                                @error('noSerievehiculo')
                                <span class="text-danger d-block mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                                </div>
                            </section>

                            <section class="order-section" id="orden-servicio">
                                <div class="order-section__header">
                                    <span class="order-section__badge">3</span>
                                    <div>
                                        <h4 class="order-section__title">Servicio y entrega</h4>
                                        <p class="order-section__copy">Define el trabajo, responsable, estado, fecha estimada y notas del taller.</p>
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
                                        {{ (string) old('servicio_id') === (string) $tipoServicio->id_servicio ? 'selected' : '' }}>
                                        {{ $tipoServicio->nombreServicio }}
                                    </option>
                                    @endforeach
                                </select>
                                <small class="helper-copy">Elige el trabajo principal solicitado por el cliente.</small>
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
                                    <option value="{{ $user->id }}"
                                        {{ (string) old('user_id', $attendingUserId ?? '') === (string) $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                    @endforeach
                                </select>
                                <small class="helper-copy">Selecciona al responsable principal de la orden.</small>
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
                                    <option value="en proceso" {{ old('status') === 'en proceso' ? 'selected' : '' }}>
                                        En proceso</option>
                                    <option value="finalizada" {{ old('status') === 'finalizada' ? 'selected' : '' }}>
                                        Finalizada</option>
                                </select>
                                <small class="helper-copy">Define si la orden inicia en proceso o ya fue finalizada.</small>
                                <div class="invalid-feedback">Selecciona el estado de la orden.</div>
                                @error('status')
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
                                    <option value="0" {{ old('retiroRefacciones') === '0' ? 'selected' : '' }}>No
                                        retira</option>
                                    <option value="1" {{ old('retiroRefacciones') === '1' ? 'selected' : '' }}>Retira
                                    </option>
                                </select>
                                <small class="helper-copy">Indica si el cliente retira o no sus refacciones.</small>
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
                                    value="{{ old('fechaEntrega') }}" placeholder="Selecciona una fecha" required>
                                <small class="helper-copy">Elige una fecha igual o posterior al día actual.</small>
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
                                    required>{{ old('observacionesInt') }}</textarea>
                                <small class="helper-copy">Describe recepción, fallas detectadas o notas del taller.</small>
                                <div class="invalid-feedback">Agrega las observaciones internas de la orden.</div>
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
                                    required>{{ old('recomendacionesCliente') }}</textarea>
                                <small class="helper-copy">Anota exactamente lo que pide o autoriza el cliente.</small>
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
                                    required>{{ old('detallesOrden') }}</textarea>
                                <small class="helper-copy">Resume el trabajo que se realizará en esta orden.</small>
                                <div class="invalid-feedback">Agrega los detalles del servicio.</div>
                                @error('detallesOrden')
                                <span class="text-danger d-block mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                            </section>

                            <section class="order-section" id="orden-cierre">
                                <div class="order-section__header">
                                    <span class="order-section__badge">4</span>
                                    <div>
                                        <h4 class="order-section__title">Evidencia y confirmación</h4>
                                        <p class="order-section__copy">Adjunta fotografías si ayudan a documentar la recepción y confirma la autorización.</p>
                                    </div>
                                </div>

                                <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="photos">Fotografías de apoyo</label>
                                <div class="photo-uploader" id="photoUploader"
                                    data-upload-url="{{ route('ordenes.photos.temporary.store') }}"
                                    data-delete-url="{{ route('ordenes.photos.temporary.destroy') }}">
                                    <input type="file" name="photos[]" id="photos"
                                        class="photo-uploader__input @error('photos.*') is-invalid @enderror"
                                        accept="image/png,image/jpeg" multiple>
                                    <label for="photos" class="photo-uploader__drop" id="photoDropzone">
                                        <span class="photo-uploader__icon" aria-hidden="true">
                                            <i class="fas fa-camera"></i>
                                        </span>
                                        <span class="photo-uploader__copy">
                                            <strong>Agregar fotografías</strong>
                                            <span>JPG o PNG, máximo 2 MB por imagen.</span>
                                        </span>
                                        <span class="photo-uploader__action">Seleccionar</span>
                                    </label>
                                    <div class="photo-uploader__status" id="photoUploadStatus">
                                        Sin fotografías agregadas.
                                    </div>
                                    <div class="photo-token-fields" id="photoTokenContainer">
                                        @foreach ((array) old('photo_tokens', []) as $photoToken)
                                        <input type="hidden" name="photo_tokens[]" value="{{ $photoToken }}"
                                            data-photo-token="{{ $photoToken }}">
                                        @endforeach
                                    </div>
                                    <div id="photoPreviewContainer" class="preview-grid"></div>
                                </div>
                                <small class="helper-copy">Al seleccionarlas se guardan cifradas de forma privada.</small>
                                @error('photos.*')
                                <span class="text-danger d-block mt-1">{{ $message }}</span>
                                @enderror
                                @error('photo_tokens.*')
                                <span class="text-danger d-block mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-check mt-2">
                                <input type="checkbox" class="form-check-input" id="clienteAcepta">
                                <label class="form-check-label" for="clienteAcepta">
                                    Confirmo que el cliente acepta la orden y la información capturada.
                                </label>
                                <small class="helper-copy d-block mt-1">Este paso habilita el envío y reduce
                                    registros accidentales.</small>
                                <div class="text-danger mt-2 d-none" id="clienteAceptaError">
                                    Debes confirmar la aceptación del cliente antes de guardar.
                                </div>
                            </div>
                        </div>
                    </div>
                            </section>
                        </div>

                    </div>
                </div>

                <div class="card-footer text-center">
                    <div class="d-flex justify-content-between flex-wrap dashboard-inline-gap">
                        <a href="{{ route('ordenes.index') }}" class="btn btn-outline-dark">Retroceder</a>
                        <button type="submit" class="btn order-submit-button" id="submitButton" disabled>
                            <i class="fas fa-save"></i> Guardar orden
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="modal fade" id="clienteSearchModal" tabindex="-1" aria-labelledby="clienteSearchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content client-search-modal">
            <div class="modal-header">
                <div>
                    <span class="client-search-modal__eyebrow">Clientes registrados</span>
                    <h2 class="modal-title fs-5" id="clienteSearchModalLabel">Buscar cliente</h2>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div>
                    <label class="form-label" for="clienteSearchInput">Buscar</label>
                    <div class="input-group client-search-input">
                        <span class="input-group-text" aria-hidden="true">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="search" class="form-control" id="clienteSearchInput"
                            placeholder="Nombre, teléfono, correo o RFC" autocomplete="off">
                    </div>
                </div>
                <div class="client-search-meta" id="clienteSearchCount" aria-live="polite"></div>
                <div class="client-search-results" id="clienteSearchResults" role="list"></div>
                <div class="client-search-empty d-none" id="clienteSearchEmpty">Sin coincidencias.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
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
        var form = document.getElementById('ordenRegistroForm');
        var submitButton = document.getElementById('submitButton');
        var acceptCheckbox = document.getElementById('clienteAcepta');
        var acceptError = document.getElementById('clienteAceptaError');
        var existingToggle = document.getElementById('usarClienteExistente');
        var existingClientBox = document.getElementById('clienteExistenteBox');
        var existingClientSelect = document.getElementById('cliente_existente_id');
        var clientDirectorySource = document.getElementById('clienteDirectorySource');
        var existingClientError = document.getElementById('clienteExistenteError');
        var duplicateHint = document.getElementById('clienteExistenteHint');
        var selectedClientSummary = document.getElementById('selectedClientSummary');
        var openClientSearchButton = document.getElementById('abrirBuscadorCliente');
        var clientSearchModal = document.getElementById('clienteSearchModal');
        var clientSearchInput = document.getElementById('clienteSearchInput');
        var clientSearchResults = document.getElementById('clienteSearchResults');
        var clientSearchEmpty = document.getElementById('clienteSearchEmpty');
        var clientSearchCount = document.getElementById('clienteSearchCount');
        var photoInput = document.getElementById('photos');
        var photoUploader = document.getElementById('photoUploader');
        var photoDropzone = document.getElementById('photoDropzone');
        var photoStatus = document.getElementById('photoUploadStatus');
        var photoTokenContainer = document.getElementById('photoTokenContainer');
        var photoPreviewContainer = document.getElementById('photoPreviewContainer');
        var orderPanel = document.getElementById('ordenPanel');
        var helpToggle = document.getElementById('ordenHelpToggle');
        var helpPanel = document.getElementById('ordenHelpPanel');
        var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        var pendingPhotoUploads = 0;
        var clientDirectory = [];

        var fields = {
            nombreCompleto: document.getElementById('nombreCompleto'),
            telefono: document.getElementById('telefono'),
            correo: document.getElementById('correo'),
            rfc: document.getElementById('rfc'),
            modelo: document.getElementById('modelo'),
            yearVehiculo: document.getElementById('yearVehiculo'),
            color: document.getElementById('color'),
            placas: document.getElementById('placas'),
            kilometraje: document.getElementById('kilometraje'),
            motor: document.getElementById('motor'),
            cilindros: document.getElementById('cilindros'),
            noSerievehiculo: document.getElementById('noSerievehiculo'),
            vehiculo_id: document.getElementById('vehiculo_id'),
            tvehiculo_id: document.getElementById('tvehiculo_id'),
            servicio_id: document.getElementById('servicio_id'),
            user_id: document.getElementById('user_id'),
            status: document.getElementById('status'),
            retiroRefacciones: document.getElementById('retiroRefacciones'),
            fechaEntrega: document.getElementById('fechaEntrega'),
            observacionesInt: document.getElementById('observacionesInt'),
            recomendacionesCliente: document.getElementById('recomendacionesCliente'),
            detallesOrden: document.getElementById('detallesOrden')
        };

        flatpickr('#fechaEntrega', {
            locale: 'es',
            altInput: true,
            altFormat: 'd/m/Y',
            dateFormat: 'Y-m-d',
            minDate: 'today',
            maxDate: new Date().fp_incr(60)
        });

        function syncHelpMode() {
            if (!orderPanel || !helpToggle || !helpPanel) {
                return;
            }

            var enabled = helpPanel.classList.contains('show') || helpToggle.getAttribute('aria-expanded') === 'true';
            orderPanel.classList.toggle('help-enabled', enabled);
            helpToggle.classList.toggle('is-active', enabled);
            helpToggle.setAttribute('aria-pressed', enabled ? 'true' : 'false');
        }

        function sanitizeName(value) {
            return value
                .replace(/[^A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]/g, '')
                .replace(/\s+/g, ' ')
                .trimStart()
                .replace(/\b\w/g, function (letter) {
                    return letter.toUpperCase();
                });
        }

        function sanitizeAlphaText(value) {
            return value
                .replace(/[^A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]/g, '')
                .replace(/\s+/g, ' ')
                .trimStart();
        }

        function sanitizeModel(value) {
            return value
                .replace(/[^A-Za-z0-9ÁÉÍÓÚÜÑáéíóúüñ\s-]/g, '')
                .replace(/\s+/g, ' ')
                .trimStart();
        }

        function sanitizeDigits(value) {
            return value.replace(/\D+/g, '');
        }

        function sanitizeAlphaNumeric(value) {
            return value.replace(/[^A-Za-z0-9]/g, '').toUpperCase();
        }

        function sanitizeDecimal(value) {
            return value.replace(/[^0-9.]/g, '');
        }

        function normalizeClientName(value) {
            return (value || '')
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/\s+/g, ' ')
                .trim();
        }

        function normalizeSearchText(value) {
            return normalizeClientName(value);
        }

        function findClientOptionByName(name) {
            var normalizedName = normalizeClientName(name);

            if (!normalizedName) {
                return null;
            }

            return Array.from(clientDirectorySource.options).find(function (option) {
                return option.value && normalizeClientName(option.dataset.nombre) === normalizedName;
            }) || null;
        }

        function findClientOptionById(clientId) {
            if (!clientId) {
                return null;
            }

            return Array.from(clientDirectorySource.options).find(function (option) {
                return option.value === String(clientId);
            }) || null;
        }

        function optionToClient(option) {
            var client = {
                id: option.value,
                nombre: option.dataset.nombre || option.textContent.trim(),
                telefono: option.dataset.telefono || '',
                correo: option.dataset.correo || '',
                rfc: option.dataset.rfc || ''
            };

            client.searchText = normalizeSearchText([
                client.nombre,
                client.telefono,
                client.correo,
                client.rfc
            ].join(' '));

            return client;
        }

        function refreshClientDirectory() {
            clientDirectory = Array.from(clientDirectorySource.options)
                .filter(function (option) {
                    return !!option.value;
                })
                .map(optionToClient);
        }

        function clientMetaParts(client) {
            return [
                client.telefono,
                client.correo,
                client.rfc
            ].filter(function (value) {
                return !!value;
            });
        }

        function updateSelectedClientSummary() {
            if (!selectedClientSummary) {
                return;
            }

            var selectedOption = findClientOptionById(existingClientSelect.value);

            selectedClientSummary.innerHTML = '';

            if (!existingToggle.checked || !selectedOption || !selectedOption.value) {
                selectedClientSummary.classList.add('d-none');
                return;
            }

            var client = optionToClient(selectedOption);
            var name = document.createElement('strong');
            var meta = document.createElement('span');

            name.textContent = client.nombre;
            meta.textContent = clientMetaParts(client).join(' - ');

            selectedClientSummary.appendChild(name);

            if (meta.textContent) {
                selectedClientSummary.appendChild(meta);
            }

            selectedClientSummary.classList.remove('d-none');
        }

        function renderClientSearchResults() {
            if (!clientSearchResults || !clientSearchEmpty || !clientSearchCount) {
                return;
            }

            var query = normalizeSearchText(clientSearchInput ? clientSearchInput.value : '');
            var terms = query.split(' ').filter(function (term) {
                return !!term;
            });
            var matches = clientDirectory.filter(function (client) {
                return terms.every(function (term) {
                    return client.searchText.indexOf(term) !== -1;
                });
            });
            var visibleMatches = matches.slice(0, 40);

            clientSearchResults.innerHTML = '';

            visibleMatches.forEach(function (client) {
                var item = document.createElement('div');
                var content = document.createElement('div');
                var name = document.createElement('div');
                var meta = document.createElement('div');
                var button = document.createElement('button');
                var selected = existingClientSelect.value === client.id;

                item.className = 'client-search-item' + (selected ? ' is-selected' : '');
                item.setAttribute('role', 'listitem');

                content.className = 'client-search-item__content';
                name.className = 'client-search-item__name';
                meta.className = 'client-search-item__meta';
                name.textContent = client.nombre;

                clientMetaParts(client).forEach(function (part) {
                    var metaItem = document.createElement('span');
                    metaItem.textContent = part;
                    meta.appendChild(metaItem);
                });

                content.appendChild(name);
                content.appendChild(meta);

                button.type = 'button';
                button.className = selected ? 'btn btn-outline-dark btn-sm' : 'btn btn-primary btn-sm';
                button.innerHTML = selected
                    ? '<i class="fas fa-check me-1"></i> Seleccionado'
                    : '<i class="fas fa-user-check me-1"></i> Elegir';
                button.addEventListener('click', function () {
                    selectClientById(client.id);
                });

                item.appendChild(content);
                item.appendChild(button);
                clientSearchResults.appendChild(item);
            });

            if (clientDirectory.length === 0) {
                clientSearchCount.textContent = '0 clientes registrados';
                clientSearchEmpty.textContent = 'No hay clientes registrados.';
            } else if (terms.length > 0) {
                clientSearchCount.textContent = matches.length + ' coincidencia' + (matches.length === 1 ? '' : 's');
                clientSearchEmpty.textContent = 'Sin coincidencias.';
            } else {
                clientSearchCount.textContent = 'Mostrando ' + visibleMatches.length + ' de ' + clientDirectory.length;
                clientSearchEmpty.textContent = 'No hay clientes registrados.';
            }

            clientSearchEmpty.classList.toggle('d-none', visibleMatches.length > 0);
        }

        function selectClientById(clientId) {
            if (!clientId) {
                return;
            }

            existingToggle.checked = true;
            existingClientSelect.value = clientId;
            updateClientMode();
            duplicateHint.classList.add('d-none');
            validateAllFields();
            renderClientSearchResults();

            if (clientSearchModal && window.bootstrap) {
                bootstrap.Modal.getOrCreateInstance(clientSearchModal).hide();
            }
        }

        function updateClientMode() {
            var usingExistingClient = existingToggle.checked;

            existingClientBox.classList.toggle('d-none', !usingExistingClient);
            existingClientSelect.required = false;

            if (openClientSearchButton) {
                openClientSearchButton.disabled = !usingExistingClient;
                openClientSearchButton.setAttribute('aria-disabled', usingExistingClient ? 'false' : 'true');
            }

            ['nombreCompleto', 'telefono', 'correo', 'rfc'].forEach(function (key) {
                fields[key].readOnly = usingExistingClient;
                fields[key].required = !usingExistingClient;
                fields[key].classList.toggle('readonly-field', usingExistingClient);
                fields[key].setCustomValidity('');
            });

            if (usingExistingClient && existingClientSelect.value) {
                populateExistingClient();
            }

            if (!usingExistingClient) {
                existingClientSelect.value = '';
            }

            if (existingClientError) {
                existingClientError.classList.add('d-none');
            }

            updateSelectedClientSummary();
            renderClientSearchResults();
        }

        function populateExistingClient() {
            var selectedOption = findClientOptionById(existingClientSelect.value);

            if (!selectedOption || !selectedOption.value) {
                return;
            }

            fields.nombreCompleto.value = selectedOption.dataset.nombre || '';
            fields.telefono.value = selectedOption.dataset.telefono || '';
            fields.correo.value = selectedOption.dataset.correo || '';
            fields.rfc.value = selectedOption.dataset.rfc || '';
        }

        function validateField(field, validator) {
            if (!field || typeof validator !== 'function') {
                return true;
            }

            var result = validator(field);
            field.setCustomValidity(result.valid ? '' : result.message);
            return result.valid;
        }

        function updateAcceptanceState(showError) {
            var accepted = acceptCheckbox.checked;
            submitButton.disabled = !accepted || pendingPhotoUploads > 0;
            acceptError.classList.toggle('d-none', accepted || !showError);
        }

        function formatFileSize(bytes) {
            if (!bytes) {
                return '0 KB';
            }

            if (bytes < 1024 * 1024) {
                return Math.round(bytes / 1024) + ' KB';
            }

            return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
        }

        function setPhotoStatus(message, isError) {
            if (!photoStatus) {
                return;
            }

            photoStatus.textContent = message;
            photoStatus.classList.toggle('text-danger', !!isError);
        }

        function savedPhotoCount() {
            return photoTokenContainer
                ? photoTokenContainer.querySelectorAll('input[name="photo_tokens[]"]').length
                : 0;
        }

        function refreshPhotoStatus() {
            var savedCount = savedPhotoCount();

            if (pendingPhotoUploads > 0) {
                setPhotoStatus('Cifrando y guardando ' + pendingPhotoUploads + ' fotografía(s)...', false);
            } else if (savedCount > 0) {
                setPhotoStatus(savedCount + ' fotografía(s) listas para esta orden.', false);
            } else {
                setPhotoStatus('Sin fotografías agregadas.', false);
            }

            updateAcceptanceState(false);
        }

        function addPhotoToken(token) {
            if (!photoTokenContainer || !token) {
                return;
            }

            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'photo_tokens[]';
            input.value = token;
            input.dataset.photoToken = token;
            photoTokenContainer.appendChild(input);
        }

        function removePhotoToken(token) {
            if (!photoTokenContainer || !token) {
                return;
            }

            var tokenInput = photoTokenContainer.querySelector('[data-photo-token="' + token + '"]');

            if (tokenInput) {
                tokenInput.remove();
            }
        }

        function deleteTemporaryPhoto(token) {
            if (!photoUploader || !token) {
                return;
            }

            fetch(photoUploader.dataset.deleteUrl, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    token: token
                })
            }).catch(function () {
                return;
            });
        }

        function cardStatusMessage(error) {
            if (error && error.errors && error.errors.photo && error.errors.photo.length) {
                return error.errors.photo[0];
            }

            return error && error.message ? error.message : 'No se pudo guardar esta fotografía.';
        }

        function createPhotoCard(file) {
            var card = document.createElement('div');
            card.className = 'preview-card is-uploading';

            var image = document.createElement('img');
            image.alt = 'Vista previa';
            image.src = URL.createObjectURL(file);
            image.addEventListener('load', function () {
                URL.revokeObjectURL(image.src);
            });

            var removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.className = 'preview-card__remove';
            removeButton.setAttribute('aria-label', 'Quitar fotografía');
            removeButton.disabled = true;
            removeButton.innerHTML = '<i class="fas fa-times"></i>';

            var name = document.createElement('span');
            name.className = 'preview-card__name';
            name.textContent = file.name;

            var status = document.createElement('span');
            status.className = 'preview-card__status';
            status.textContent = 'Cifrando ' + formatFileSize(file.size);

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

            return {
                card: card,
                status: status,
                removeButton: removeButton
            };
        }

        function uploadPhoto(file) {
            if (!photoUploader || !photoPreviewContainer) {
                return;
            }

            if (!/^image\/(jpeg|png)$/.test(file.type)) {
                setPhotoStatus('Solo puedes agregar imágenes JPG o PNG.', true);
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                setPhotoStatus('Cada fotografía puede pesar hasta 2 MB.', true);
                return;
            }

            var preview = createPhotoCard(file);
            var formData = new FormData();
            formData.append('photo', file);
            pendingPhotoUploads += 1;
            refreshPhotoStatus();

            fetch(photoUploader.dataset.uploadUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            })
                .then(function (response) {
                    return response.json().then(function (data) {
                        if (!response.ok) {
                            throw data;
                        }

                        return data;
                    });
                })
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

        function handlePhotoFiles(files) {
            Array.from(files || []).forEach(uploadPhoto);
        }

        function validateAllFields() {
            var validators = [
                function () {
                    if (existingToggle.checked) {
                        var hasClient = !!existingClientSelect.value;

                        if (existingClientError) {
                            existingClientError.classList.toggle('d-none', hasClient);
                        }

                        return hasClient;
                    }

                    if (existingClientError) {
                        existingClientError.classList.add('d-none');
                    }

                    return true;
                },
                function () {
                    if (existingToggle.checked) {
                        return true;
                    }

                    return validateField(fields.nombreCompleto, function (field) {
                        var value = field.value.trim();
                        return {
                            valid: value.length >= 5 && /^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]+$/.test(value),
                            message: 'Escribe nombre y apellidos usando solo letras.'
                        };
                    });
                },
                function () {
                    if (existingToggle.checked) {
                        return true;
                    }

                    return validateField(fields.telefono, function (field) {
                        return {
                            valid: /^\d{10}$/.test(field.value),
                            message: 'El teléfono debe llevar 10 dígitos.'
                        };
                    });
                },
                function () {
                    if (existingToggle.checked) {
                        return true;
                    }

                    return validateField(fields.correo, function (field) {
                        var valid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(field.value.trim());
                        return {
                            valid: valid,
                            message: 'Captura un correo con formato nombre@dominio.com.'
                        };
                    });
                },
                function () {
                    if (existingToggle.checked) {
                        return true;
                    }

                    return validateField(fields.rfc, function (field) {
                        return {
                            valid: /^[A-Z0-9]{12,13}$/.test(field.value.trim()),
                            message: 'El RFC debe tener 12 o 13 caracteres alfanuméricos.'
                        };
                    });
                },
                function () {
                    return validateField(fields.modelo, function (field) {
                        return {
                            valid: field.value.trim().length >= 2,
                            message: 'Escribe la línea o modelo de la unidad.'
                        };
                    });
                },
                function () {
                    return validateField(fields.yearVehiculo, function (field) {
                        return {
                            valid: /^\d{4}$/.test(field.value),
                            message: 'Captura el año con 4 dígitos.'
                        };
                    });
                },
                function () {
                    return validateField(fields.color, function (field) {
                        return {
                            valid: field.value.trim().length >= 3,
                            message: 'Escribe el color principal de la unidad.'
                        };
                    });
                },
                function () {
                    return validateField(fields.placas, function (field) {
                        return {
                            valid: /^[A-Z0-9]{5,7}$/.test(field.value),
                            message: 'Las placas deben tener entre 5 y 7 caracteres.'
                        };
                    });
                },
                function () {
                    return validateField(fields.kilometraje, function (field) {
                        return {
                            valid: field.value.trim() !== '' && !isNaN(field.value),
                            message: 'Captura un kilometraje válido.'
                        };
                    });
                },
                function () {
                    return validateField(fields.motor, function (field) {
                        return {
                            valid: field.value.trim().length >= 2,
                            message: 'Escribe la referencia del motor.'
                        };
                    });
                },
                function () {
                    return validateField(fields.cilindros, function (field) {
                        return {
                            valid: field.value.trim() !== '' && !isNaN(field.value),
                            message: 'Captura la cantidad de cilindros.'
                        };
                    });
                },
                function () {
                    return validateField(fields.noSerievehiculo, function (field) {
                        return {
                            valid: /^[A-Z0-9]{5,17}$/.test(field.value),
                            message: 'El número de serie debe tener entre 5 y 17 caracteres.'
                        };
                    });
                },
                function () {
                    return validateField(fields.vehiculo_id, function (field) {
                        return {
                            valid: !!field.value,
                            message: 'Selecciona una marca.'
                        };
                    });
                },
                function () {
                    return validateField(fields.tvehiculo_id, function (field) {
                        return {
                            valid: !!field.value,
                            message: 'Selecciona un tipo de vehículo.'
                        };
                    });
                },
                function () {
                    return validateField(fields.servicio_id, function (field) {
                        return {
                            valid: !!field.value,
                            message: 'Selecciona un tipo de servicio.'
                        };
                    });
                },
                function () {
                    return validateField(fields.user_id, function (field) {
                        return {
                            valid: !!field.value,
                            message: 'Selecciona al responsable.'
                        };
                    });
                },
                function () {
                    return validateField(fields.status, function (field) {
                        return {
                            valid: !!field.value,
                            message: 'Selecciona un estado.'
                        };
                    });
                },
                function () {
                    return validateField(fields.retiroRefacciones, function (field) {
                        return {
                            valid: field.value === '0' || field.value === '1',
                            message: 'Indica si el cliente retira refacciones.'
                        };
                    });
                },
                function () {
                    return validateField(fields.fechaEntrega, function (field) {
                        return {
                            valid: !!field.value,
                            message: 'Selecciona la fecha de entrega.'
                        };
                    });
                },
                function () {
                    return validateField(fields.observacionesInt, function (field) {
                        return {
                            valid: field.value.trim().length >= 10,
                            message: 'Describe al menos una observación interna.'
                        };
                    });
                },
                function () {
                    return validateField(fields.recomendacionesCliente, function (field) {
                        return {
                            valid: field.value.trim().length >= 10,
                            message: 'Describe la recomendación o solicitud del cliente.'
                        };
                    });
                },
                function () {
                    return validateField(fields.detallesOrden, function (field) {
                        return {
                            valid: field.value.trim().length >= 10,
                            message: 'Describe el detalle del servicio.'
                        };
                    });
                }
            ];

            var valid = true;

            validators.forEach(function (runValidator) {
                if (!runValidator()) {
                    valid = false;
                }
            });

            return valid;
        }

        fields.nombreCompleto.addEventListener('input', function () {
            this.value = sanitizeName(this.value);
            validateAllFields();
        });

        fields.telefono.addEventListener('input', function () {
            this.value = sanitizeDigits(this.value).slice(0, 10);
            validateAllFields();
        });

        fields.correo.addEventListener('input', function () {
            this.value = this.value.toLowerCase().trim();
            validateAllFields();
        });

        fields.rfc.addEventListener('input', function () {
            this.value = sanitizeAlphaNumeric(this.value).slice(0, 13);
            validateAllFields();
        });

        fields.modelo.addEventListener('input', function () {
            this.value = sanitizeModel(this.value);
            validateAllFields();
        });

        fields.yearVehiculo.addEventListener('input', function () {
            this.value = sanitizeDigits(this.value).slice(0, 4);
            validateAllFields();
        });

        fields.color.addEventListener('input', function () {
            this.value = sanitizeAlphaText(this.value).slice(0, 30);
            validateAllFields();
        });

        fields.placas.addEventListener('input', function () {
            this.value = sanitizeAlphaNumeric(this.value).slice(0, 7);
            validateAllFields();
        });

        fields.kilometraje.addEventListener('input', function () {
            this.value = sanitizeDecimal(this.value).slice(0, 8);
            validateAllFields();
        });

        fields.motor.addEventListener('input', function () {
            this.value = this.value.replace(/[^A-Za-z0-9.]/g, '').toUpperCase().slice(0, 10);
            validateAllFields();
        });

        fields.cilindros.addEventListener('input', function () {
            this.value = sanitizeDigits(this.value).slice(0, 4);
            validateAllFields();
        });

        fields.noSerievehiculo.addEventListener('input', function () {
            this.value = sanitizeAlphaNumeric(this.value).slice(0, 17);
            validateAllFields();
        });

        [
            fields.vehiculo_id,
            fields.tvehiculo_id,
            fields.servicio_id,
            fields.user_id,
            fields.status,
            fields.retiroRefacciones,
            fields.fechaEntrega,
            fields.observacionesInt,
            fields.recomendacionesCliente,
            fields.detallesOrden
        ].forEach(function (field) {
            field.addEventListener('change', validateAllFields);
            field.addEventListener('input', validateAllFields);
        });

        fields.nombreCompleto.addEventListener('blur', function () {
            if (existingToggle.checked || this.value.trim().length < 5) {
                duplicateHint.classList.add('d-none');
                return;
            }

            var matchedClient = findClientOptionByName(this.value);

            if (matchedClient) {
                existingToggle.checked = true;
                existingClientSelect.value = matchedClient.value;
                updateClientMode();
                duplicateHint.classList.add('d-none');
                validateAllFields();
                return;
            }

            fetch('{{ route('clientes.verificar_nombre') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    nombreCompleto: this.value.trim()
                })
            })
                .then(function (response) {
                    return response.json();
                })
                .then(function (data) {
                    duplicateHint.classList.toggle('d-none', !data.exists);
                })
                .catch(function () {
                    duplicateHint.classList.add('d-none');
                });
        });

        existingToggle.addEventListener('change', function () {
            updateClientMode();
            duplicateHint.classList.add('d-none');
            validateAllFields();
        });

        existingClientSelect.addEventListener('change', function () {
            populateExistingClient();
            updateSelectedClientSummary();
            renderClientSearchResults();
            validateAllFields();
        });

        if (clientSearchInput) {
            clientSearchInput.addEventListener('input', renderClientSearchResults);
        }

        if (clientSearchModal) {
            clientSearchModal.addEventListener('shown.bs.modal', function () {
                renderClientSearchResults();

                if (clientSearchInput) {
                    clientSearchInput.focus();
                    clientSearchInput.select();
                }
            });
        }

        acceptCheckbox.addEventListener('change', function () {
            updateAcceptanceState(false);
        });

        if (helpPanel) {
            helpPanel.addEventListener('shown.bs.collapse', syncHelpMode);
            helpPanel.addEventListener('hidden.bs.collapse', syncHelpMode);
        }

        if (helpToggle) {
            helpToggle.addEventListener('click', function () {
                window.setTimeout(syncHelpMode, 250);
            });
        }

        photoInput.addEventListener('change', function () {
            handlePhotoFiles(photoInput.files);
        });

        if (photoDropzone) {
            ['dragenter', 'dragover'].forEach(function (eventName) {
                photoDropzone.addEventListener(eventName, function (event) {
                    event.preventDefault();
                    photoDropzone.classList.add('is-dragover');
                });
            });

            ['dragleave', 'drop'].forEach(function (eventName) {
                photoDropzone.addEventListener(eventName, function (event) {
                    event.preventDefault();
                    photoDropzone.classList.remove('is-dragover');
                });
            });

            photoDropzone.addEventListener('drop', function (event) {
                handlePhotoFiles(event.dataTransfer.files);
            });
        }

        form.addEventListener('submit', function (event) {
            var valid = validateAllFields();
            updateAcceptanceState(true);

            if (pendingPhotoUploads > 0) {
                event.preventDefault();
                event.stopPropagation();
                setPhotoStatus('Espera a que terminen de cifrarse las fotografías.', true);
            }

            if (!acceptCheckbox.checked) {
                event.preventDefault();
                event.stopPropagation();
            }

            if (!valid || !form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }

            form.classList.add('was-validated');
        });

        if (window.FormHelpers) {
            window.FormHelpers.attachSubmitLoading(form, '#submitButton', 'Guardando orden...');
        }

        refreshClientDirectory();
        updateClientMode();
        syncHelpMode();
        refreshPhotoStatus();
        updateAcceptanceState(false);
        validateAllFields();
    });
</script>
@endsection
