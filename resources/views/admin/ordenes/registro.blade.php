@extends('adminlte::page')

@section('content_header')
@if (Session::has('status'))
<div class="col-md-12 alert-section">
    <div class="alert alert-{{ Session::get('status_type') }}"
        style="text-align: center; padding: 5px; margin-bottom: 5px;">
        <span style="font-size: 20px; font-weight: bold;">
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
<style>
    .order-panel {
        border-radius: 16px;
        border: 1px solid rgba(13, 110, 253, 0.1);
        box-shadow: 0 18px 36px rgba(15, 23, 42, 0.08);
        overflow: hidden;
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
        color: rgba(255, 255, 255, 0.85);
    }

    .order-help-toggle {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        border: none;
        font-weight: 600;
        color: #0f172a;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.14);
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
    }

    .help-panel {
        margin-bottom: 1.5rem;
        padding: 1.25rem;
        border: 1px solid rgba(13, 148, 136, 0.18);
        border-radius: 16px;
        background: linear-gradient(135deg, rgba(6, 182, 212, 0.08), rgba(255, 255, 255, 0.96));
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
        color: #0f172a;
    }

    .help-panel-copy {
        margin: 0;
        color: #475569;
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
        background: rgba(255, 255, 255, 0.9);
    }

    .help-block h4 {
        margin-bottom: 0.75rem;
        font-size: 0.98rem;
        font-weight: 700;
        color: #0f172a;
    }

    .preview-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
        gap: 12px;
        margin-top: 1rem;
    }

    .preview-card {
        background: #f8fafc;
        border: 1px solid #dee2e6;
        border-radius: 12px;
        padding: 8px;
        text-align: center;
    }

    .preview-card img {
        width: 100%;
        height: 95px;
        object-fit: cover;
        border-radius: 8px;
        margin-bottom: 6px;
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
        color: #1e293b;
    }

    .custom-switch .custom-control-label,
    .custom-checkbox .custom-control-label {
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
    }
</style>

@if ($errors->any())
<div class="alert alert-danger">
    <strong>Hay datos pendientes por corregir.</strong>
    <ul class="mb-0 mt-2 pl-3">
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
            <div class="card order-panel">
                <div class="card-header bg-info">
                    <div class="order-header">
                        <div class="order-header-copy">
                            <h3 class="card-title mb-0">Registrar orden</h3>
                            <p>Captura cliente, unidad y servicio sin saturar la pantalla de instrucciones.</p>
                        </div>

                        <button type="button" class="btn btn-light btn-sm order-help-toggle" data-toggle="collapse"
                            data-target="#ordenHelpPanel" aria-expanded="{{ $errors->any() ? 'true' : 'false' }}"
                            aria-controls="ordenHelpPanel">
                            <i class="fas fa-life-ring mr-1"></i> Ayuda rápida
                        </button>
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

                    <div class="custom-control custom-switch mb-4">
                        <input type="checkbox" class="custom-control-input" id="usarClienteExistente"
                            name="usar_cliente_existente" value="1" {{ old('usar_cliente_existente') ? 'checked' : '' }}>
                        <label class="custom-control-label" for="usarClienteExistente">Usar cliente ya registrado</label>
                        <div class="helper-copy ml-4">Activa esta opción si el cliente ya existe y quieres rellenar sus
                            datos automáticamente.</div>
                    </div>

                    <div class="form-group {{ old('usar_cliente_existente') ? '' : 'd-none' }}" id="clienteExistenteBox">
                        <label for="cliente_existente_id">Cliente registrado</label>
                        <select name="cliente_existente_id" id="cliente_existente_id"
                            class="form-control @error('cliente_existente_id') is-invalid @enderror">
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
                        <small class="helper-copy">El sistema cargará nombre, teléfono, correo y RFC del cliente
                            seleccionado.</small>
                        <div class="invalid-feedback">Selecciona un cliente registrado.</div>
                        @error('cliente_existente_id')
                        <span class="text-danger d-block mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="section-heading mt-4">Información del cliente</div>
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

                    <div class="section-heading mt-4">Datos de la unidad</div>
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

                    <div class="section-heading mt-4">Datos de la orden</div>
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
                                        {{ (string) old('user_id') === (string) $user->id ? 'selected' : '' }}>
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

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="photos">Fotografías de apoyo</label>
                                <input type="file" name="photos[]" id="photos"
                                    class="form-control-file @error('photos.*') is-invalid @enderror"
                                    accept="image/png,image/jpeg" multiple>
                                <small class="helper-copy">Puedes subir varias imágenes JPG o PNG de hasta 2 MB cada
                                    una.</small>
                                @error('photos.*')
                                <span class="text-danger d-block mt-1">{{ $message }}</span>
                                @enderror
                                <div id="photoPreviewContainer" class="preview-grid"></div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="custom-control custom-checkbox mt-2">
                                <input type="checkbox" class="custom-control-input" id="clienteAcepta">
                                <label class="custom-control-label" for="clienteAcepta">
                                    Confirmo que el cliente acepta la orden y la información capturada.
                                </label>
                                <small class="helper-copy d-block ml-4 mt-1">Este paso habilita el envío y reduce
                                    registros accidentales.</small>
                                <div class="text-danger mt-2 d-none" id="clienteAceptaError">
                                    Debes confirmar la aceptación del cliente antes de guardar.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer text-center">
                    <div class="d-flex justify-content-between flex-wrap" style="gap: 0.75rem;">
                        <a href="{{ route('ordenes.index') }}" class="btn btn-outline-dark">Retroceder</a>
                        <button type="submit" class="btn btn-info" id="submitButton" disabled>Guardar orden</button>
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
        var form = document.getElementById('ordenRegistroForm');
        var submitButton = document.getElementById('submitButton');
        var acceptCheckbox = document.getElementById('clienteAcepta');
        var acceptError = document.getElementById('clienteAceptaError');
        var existingToggle = document.getElementById('usarClienteExistente');
        var existingClientBox = document.getElementById('clienteExistenteBox');
        var existingClientSelect = document.getElementById('cliente_existente_id');
        var duplicateHint = document.getElementById('clienteExistenteHint');
        var photoInput = document.getElementById('photos');
        var photoPreviewContainer = document.getElementById('photoPreviewContainer');

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

        function updateClientMode() {
            var usingExistingClient = existingToggle.checked;

            existingClientBox.classList.toggle('d-none', !usingExistingClient);
            existingClientSelect.required = usingExistingClient;

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
        }

        function populateExistingClient() {
            var selectedOption = existingClientSelect.options[existingClientSelect.selectedIndex];

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
            submitButton.disabled = !accepted;
            acceptError.classList.toggle('d-none', accepted || !showError);
        }

        function validateAllFields() {
            var validators = [
                function () {
                    if (existingToggle.checked) {
                        return validateField(existingClientSelect, function (field) {
                            return {
                                valid: !!field.value,
                                message: 'Selecciona un cliente registrado.'
                            };
                        });
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

            fetch('{{ route('verificar_nombre_usuario') }}', {
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
            validateAllFields();
        });

        acceptCheckbox.addEventListener('change', function () {
            updateAcceptanceState(false);
        });

        photoInput.addEventListener('change', function () {
            photoPreviewContainer.innerHTML = '';

            Array.from(photoInput.files || []).forEach(function (file) {
                if (!file.type.match(/^image\//)) {
                    return;
                }

                var reader = new FileReader();
                reader.onload = function (event) {
                    var card = document.createElement('div');
                    card.className = 'preview-card';
                    card.innerHTML =
                        '<img src="' + event.target.result + '" alt="Vista previa">' +
                        '<small>' + file.name + '</small>';
                    photoPreviewContainer.appendChild(card);
                };
                reader.readAsDataURL(file);
            });
        });

        form.addEventListener('submit', function (event) {
            var valid = validateAllFields();
            updateAcceptanceState(true);

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

        updateClientMode();
        updateAcceptanceState(false);
        validateAllFields();
    });
</script>
@endsection
