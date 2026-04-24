@extends('layouts.dashboard')

@section('title', 'Carga general')

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
                    <span class="resource-hero__eyebrow">Carga rápida</span>
                    <h1 class="resource-hero__title">Guardar datos generales</h1>
                    <p>Captura de una sola vez una marca, un tipo de vehículo y un servicio para arrancar o completar el catálogo base.</p>
                </div>

                <div class="resource-hero__actions">
                    <a href="{{ route('datosv.index') }}" class="btn btn-outline-light">
                        <i class="fas fa-arrow-left mr-1"></i> Volver al panel
                    </a>
                </div>
            </div>
        </section>

        <div class="resource-form-layout">
            <section class="resource-form-card">
                <div class="resource-form-card__header">
                    <div>
                        <span class="resource-form-card__eyebrow">Alta combinada</span>
                        <h2 class="resource-form-card__title">Datos básicos</h2>
                        <p class="resource-form-card__copy">Este formulario crea una entrada nueva en cada uno de los tres catálogos principales.</p>
                    </div>
                </div>

                {!! Form::open(['route' => 'datosv.store', 'method' => 'post', 'enctype' => 'multipart/form-data']) !!}
                    <div class="resource-kv mt-4">
                        <div class="form-group mb-0">
                            <label for="marca">Marca</label>
                            <input id="marca" type="text" class="form-control" name="marca" required oninput="formatInput(this)">
                        </div>

                        <div class="form-group mb-0">
                            <label for="tipo">Tipo de vehículo</label>
                            <input id="tipo" type="text" class="form-control" name="tipo" required oninput="formatInput(this)">
                        </div>

                        <div class="form-group mb-0">
                            <label for="nombre_servicio">Nombre del servicio</label>
                            <input id="nombre_servicio" type="text" class="form-control" name="nombre_servicio" required oninput="formatInput(this)">
                        </div>
                    </div>

                    <div class="resource-form-card__footer">
                        <div class="resource-footer-actions">
                            <a href="{{ route('datosv.index') }}" class="btn btn-outline-dark">Cancelar</a>
                            <button type="submit" class="btn btn-success">Guardar datos</button>
                        </div>
                    </div>
                {!! Form::close() !!}
            </section>

            <aside class="resource-side-card">
                <span class="resource-form-card__eyebrow">Cuándo usarlo</span>
                <h2 class="resource-form-card__title">Ideal para iniciar</h2>
                <p class="resource-side-card__copy">Esta vista funciona mejor cuando necesitas sumar un set base de datos en una sola captura.</p>

                <ul class="resource-side-card__list mt-4">
                    <li>Úsalo cuando una nueva marca, tipo y servicio aparezcan al mismo tiempo.</li>
                    <li>Si solo vas a agregar una marca o un servicio, conviene usar el módulo específico.</li>
                    <li>Revisa ortografía antes de guardar, porque cada campo impacta un catálogo distinto.</li>
                </ul>
            </aside>
        </div>
    </div>
@stop

<script>
    function formatInput(input) {
        input.value = input.value
            .replace(/[^A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]/g, '')
            .replace(/\s+/g, ' ')
            .trimStart()
            .replace(/\b\w/g, function(letter) {
                return letter.toUpperCase();
            });
    }
</script>

@section('js')
    <script src="{{ asset('js/validatorFields.js') }}"></script>
@endsection
