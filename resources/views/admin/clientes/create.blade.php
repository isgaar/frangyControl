@extends('layouts.dashboard')

@section('title', 'Nuevo cliente')

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
    <div class="resource-page">
        <section class="resource-hero">
            <div class="resource-hero__top">
                <div class="resource-hero__copy">
                    <span class="resource-hero__eyebrow">Alta de clientes</span>
                    <h1 class="resource-hero__title">Registrar nuevo cliente</h1>
                    <p>Captura la información básica del contacto para que quede lista en órdenes, búsquedas y seguimiento.</p>
                </div>

                <div class="resource-hero__actions">
                    <a href="{{ route('clientes.index') }}" class="btn btn-outline-light">
                        <i class="fas fa-arrow-left mr-1"></i> Volver al directorio
                    </a>
                </div>
            </div>
        </section>

        <div class="resource-form-layout">
            <section class="resource-form-card">
                <div class="resource-form-card__header">
                    <div>
                        <span class="resource-form-card__eyebrow">Formulario</span>
                        <h2 class="resource-form-card__title">Información requerida</h2>
                        <p class="resource-form-card__copy">Completa los cuatro campos para crear una tarjeta válida del cliente.</p>
                    </div>
                </div>

                {!! Form::open(['route' => 'clientes.store', 'method' => 'post', 'enctype' => 'multipart/form-data']) !!}
                    <div class="resource-kv mt-4">
                        <div class="form-group mb-0">
                            <label for="nombreCompleto">Nombre completo</label>
                            <input type="text" name="nombreCompleto" class="form-control" maxlength="100"
                                id="nombreCompleto" oninput="formatNameInput(this)" value="{{ old('nombreCompleto') }}">
                            @error('nombreCompleto')
                                <span class="text-danger d-block mt-2">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <label for="telefono">Teléfono</label>
                            <input type="text" name="telefono" class="form-control" id="telefono"
                                oninput="truncatePhoneNumber(this)" value="{{ old('telefono') }}">
                            @error('telefono')
                                <span class="text-danger d-block mt-2">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <label for="correo">Correo electrónico</label>
                            <input type="text" name="correo" class="form-control" maxlength="30" id="correo"
                                oninput="validateEmail(this)" value="{{ old('correo') }}">
                            @error('correo')
                                <span class="text-danger d-block mt-2">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <label for="rfc">RFC</label>
                            <input type="text" name="rfc" class="form-control" maxlength="13" id="rfc"
                                oninput="formatRFC(event)" value="{{ old('rfc') }}">
                            @error('rfc')
                                <span class="text-danger d-block mt-2">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="resource-form-card__footer">
                        <div class="resource-footer-actions">
                            <a href="{{ route('clientes.index') }}" class="btn btn-outline-dark">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Guardar cliente</button>
                        </div>
                    </div>
                {!! Form::close() !!}
            </section>

            <aside class="resource-side-card">
                <span class="resource-form-card__eyebrow">Recomendaciones</span>
                <h2 class="resource-form-card__title">Antes de guardar</h2>
                <p class="resource-side-card__copy">Un registro limpio evita duplicados y mejora las búsquedas en órdenes.</p>

                <ul class="resource-side-card__list mt-4">
                    <li>Usa el nombre completo tal como debe aparecer en el directorio.</li>
                    <li>Captura un teléfono de 10 dígitos sin espacios ni guiones.</li>
                    <li>El correo se normaliza en minúsculas para facilitar búsquedas.</li>
                    <li>El RFC se guarda en mayúsculas y sin caracteres especiales.</li>
                </ul>
            </aside>
        </div>
    </div>
@stop

<script>
    function formatNameInput(input) {
        input.value = input.value
            .replace(/[^A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]/g, '')
            .replace(/\s+/g, ' ')
            .trimStart()
            .replace(/\b\w/g, function(letter) {
                return letter.toUpperCase();
            });
    }

    function truncatePhoneNumber(input) {
        input.value = input.value.replace(/\D/g, '').slice(0, 10);
    }

    function validateEmail(input) {
        input.value = input.value
            .replace(/[^a-zA-Z0-9@._-]/g, '')
            .toLowerCase()
            .slice(0, 30);
    }

    function formatRFC(event) {
        event.target.value = event.target.value
            .replace(/[^A-Za-z0-9]/g, '')
            .toUpperCase()
            .slice(0, 13);
    }
</script>

@section('js')
    <script src="{{ asset('js/validatorFields.js') }}"></script>
@endsection
