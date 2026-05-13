@extends('layouts.dashboard')

@section('title', 'Editar cliente')

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
                    <span class="resource-hero__eyebrow">Mantenimiento del directorio</span>
                    <h1 class="resource-hero__title">Actualizar cliente</h1>
                    <p>Corrige o completa los datos del contacto para que el equipo siga trabajando con información limpia y consistente.</p>
                </div>

                <div class="resource-hero__actions">
                    <a href="{{ route('clientes.index') }}" class="btn btn-outline-light">
                        <i class="fas fa-arrow-left me-1"></i> Volver al directorio
                    </a>
                </div>
            </div>
        </section>

        <div class="resource-form-layout">
            <section class="resource-form-card">
                <div class="resource-form-card__header">
                    <div>
                        <span class="resource-form-card__eyebrow">Edición</span>
                        <h2 class="resource-form-card__title">{{ $cliente->nombreCompleto }}</h2>
                        <p class="resource-form-card__copy">Ajusta los datos del cliente y guarda los cambios cuando queden correctos.</p>
                    </div>
                </div>

                <form action="{{ route('clientes.update', $cliente->id_cliente) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="resource-kv mt-4">
                        <div class="form-group mb-0">
                            <label for="nombreCompleto">Nombre completo</label>
                            <input type="text" name="nombreCompleto" id="nombreCompleto" class="form-control" oninput="formatNameInput(this)" value="{{ old('nombreCompleto', $cliente->nombreCompleto) }}">
                            @error('nombreCompleto')
                                <span class="text-danger d-block mt-2">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <label for="telefono">Teléfono</label>
                            <input type="text" name="telefono" id="telefono" class="form-control" maxlength="10" inputmode="numeric" oninput="validatePhoneNumber(this)" value="{{ old('telefono', $cliente->telefono) }}">
                            @error('telefono')
                                <span class="text-danger d-block mt-2">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <label for="correo">Correo electrónico</label>
                            <input type="email" name="correo" id="correo" class="form-control" oninput="validateEmail(this)" value="{{ old('correo', $cliente->correo) }}">
                            @error('correo')
                                <span class="text-danger d-block mt-2">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <label for="rfc">RFC</label>
                            <input type="text" name="rfc" id="rfc" class="form-control" oninput="formatRFC(event)" value="{{ old('rfc', $cliente->rfc) }}">
                            @error('rfc')
                                <span class="text-danger d-block mt-2">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="resource-form-card__footer">
                        <div class="resource-footer-actions">
                            <a href="{{ route('clientes.index') }}" class="btn btn-outline-dark">Cancelar</a>
                            <button type="submit" class="btn btn-warning">Actualizar cliente</button>
                        </div>
                    </div>
                </form>
            </section>

            <aside class="resource-side-card">
                <span class="resource-form-card__eyebrow">Checklist</span>
                <h2 class="resource-form-card__title">Qué revisar</h2>
                <p class="resource-side-card__copy">Un ajuste pequeño aquí evita errores repetidos al registrar nuevas órdenes.</p>

                <ul class="resource-side-card__list mt-4">
                    <li>Confirma que el nombre siga coincidiendo con el expediente del cliente.</li>
                    <li>Verifica teléfono y correo antes de guardar para evitar contactos duplicados.</li>
                    <li>El RFC se normaliza a mayúsculas y sin símbolos extra.</li>
                    <li>Si solo corriges formato, el cambio se verá reflejado inmediatamente en el directorio.</li>
                </ul>
            </aside>
        </div>
    </div>
@stop

@section('js')
    <script src="{{ asset('js/validatorFields.js') }}"></script>
    <script>
        function validatePhoneNumber(input) {
            input.value = input.value.replace(/\D/g, '').slice(0, 10);
        }

        function formatRFC(event) {
            event.target.value = event.target.value
                .replace(/[^A-Za-z0-9]/g, '')
                .toUpperCase()
                .slice(0, 13);
        }

        function formatNameInput(input) {
            input.value = input.value
                .replace(/[^A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]/g, '')
                .replace(/\s+/g, ' ')
                .trimStart()
                .replace(/\b\w/g, function(letter) {
                    return letter.toUpperCase();
                });
        }

        function validateEmail(input) {
            input.value = input.value
                .replace(/[^a-zA-Z0-9@._-]/g, '')
                .toLowerCase()
                .slice(0, 30);
        }
    </script>
@endsection
