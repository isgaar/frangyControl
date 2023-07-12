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
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <ul>
            <b>Creando registro único</b>
        </ul>
    </div>
</div>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card card-warning">
            <div class="card-header d-flex">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <div>
                    <h2 class="card-title">¡Advertencia!</h2>
                    <br>
                    <h8 class="card-text">Usted está guardando datos en la tabla "Tipos de vehiculo".</h8>
                </div>
            </div>

            <div class="card-body">
                <!-- Campos para DatosVehiculo -->
                {!! Form::open(['route' => 'tipo_vehiculo.store', 'method' => 'post', 'enctype' =>
                'multipart/form-data']) !!}
                <!-- Campos para TipoVehiculo -->
                <div class="form-group row">
                    <label for="tipo" class="col-md-4 col-form-label text-md-right">Tipo de Vehículo</label>
                    <div class="col-md-6">
                        {!! Form::text('tipos[]', null, ['class' => 'form-control', 'required', 'oninput' =>
                        'formatInput(this)']) !!}
                    </div>
                </div>

                <div id="camposTipoContainer">
                    <!-- Contenedor para campos de tipo de vehículo -->
                </div>

                <div class="form-group row">
                    <div class="col-md-6 offset-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="agregarTipo" name="agregarTipo">
                            <label class="form-check-label" for="agregarTipo">
                                ¿Desea agregar otro tipo de vehículo?
                            </label>
                        </div>
                    </div>
                </div>

                <div id="botonAgregarContainer" style="display: none;">
                    <div class="form-group row">
                        <div class="col-md-6 offset-md-4">
                            <button type="button" class="btn btn-outline-primary" onclick="agregarCampoTipo()">
                                Agregar Tipo de Vehículo
                            </button>
                        </div>
                    </div>
                </div>

                <script>
                document.getElementById('agregarTipo').addEventListener('change', function(e) {
                    var checkbox = e.target;
                    var botonAgregarContainer = document.getElementById('botonAgregarContainer');

                    if (checkbox.checked) {
                        botonAgregarContainer.style.display = 'block';
                    } else {
                        botonAgregarContainer.style.display = 'none';
                    }
                });

                function formatInput(input) {
                    var value = input.value;
                    var formattedValue = value.charAt(0).toUpperCase() + value.slice(1).toLowerCase();
                    formattedValue = formattedValue.replace(/[^a-zA-Z0-9\s]/g, ''); // Eliminar caracteres especiales
                    input.value = formattedValue;
                }

                function agregarCampoTipo() {
                    var camposTipoContainer = document.getElementById('camposTipoContainer');

                    // Crear campo de tipo de vehículo utilizando Laravel Collective
                    var tipoField = document.createElement('div');
                    tipoField.classList.add('form-group', 'row');
                    tipoField.innerHTML = `
                <label class="col-md-4 col-form-label text-md-right">Tipo de Vehículo</label>
                <div class="col-md-6">
                    {!! Form::text('tipos[]', null, ['class' => 'form-control', 'required', 'oninput' => 'formatInput(this)']) !!}
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="eliminarCampoTipo(this)">
                        Borrar
                    </button>
                </div>
            `;
                    camposTipoContainer.appendChild(tipoField);
                }

                function eliminarCampoTipo(btn) {
                    var campoTipo = btn.closest('.form-group.row');
                    campoTipo.remove();
                }
                </script>

                <div class="card-footer text-center">
                    <div class="d-flex justify-content-between">
                        <a type="button" href="{{ route('datosv.index') }}" class="btn btn-outline-dark">Retroceder</a>
                        <button type="submit" class="btn btn-outline-success">Guardar</button>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>

        </div>

    </div>

</div>
</div>
</div>
</div>
@stop

@section('js')
<script src="{{ asset('js/validatorFields.js') }}"></script>
@endsection