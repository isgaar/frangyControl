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
@section('content')
<div class="card">
    <div class="card-header">
        <ul>
            <b>Creando registro generál</b>
        </ul>
    </div>
</div>
<div class="row justify-content-center">

    <div class="col-md-8">
        <div class="card card-warning">
            <div class="card-header d-flex">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <div>
                    <h2 class="card-title">¡Advertencia!</h3>
                        <br>
                        <h8 class="card-text">Utilice esta tabla para agregar los contenidos de las distintas tablas,
                            asegurándose de no dejar campos vacíos.</h8>
                </div>
            </div>


            <div class="card-body">

                <!-- Campos para DatosVehiculo -->
                {!! Form::open(['route' => 'datosv.store', 'method' => 'post', 'enctype' => 'multipart/form-data']) !!}
                <div class="form-group row">
                    <label for="marca" class="col-md-4 col-form-label text-md-right">Marca</label>
                    <div class="col-md-6">
                        <input id="marca" type="text" class="form-control" name="marca" required>
                    </div>
                </div>


                <!-- Campos para TipoVehiculo -->
                <div class="form-group row">
                    <label for="tipo" class="col-md-4 col-form-label text-md-right">Tipo de Vehículo</label>
                    <div class="col-md-6">
                        <input id="tipo" type="text" class="form-control" name="tipo" required
                            oninput="formatInput(this)">
                    </div>
                </div>

                <!-- Campos para TipoServicio -->
                <div class="form-group row">
                    <label for="nombre_servicio" class="col-md-4 col-form-label text-md-right">Nombre del
                        Servicio</label>
                    <div class="col-md-6">
                        <input id="nombre_servicio" type="text" class="form-control" name="nombre_servicio" required>
                    </div>
                </div>

                <div class="card-footer text-center">
                    <div class="d-flex justify-content-between">
                        <a type="button" href="{{ route('datosv.index') }}" class="btn btn-outline-dark">Retroceder</a>
                        <button type="submit" class="btn btn-success">Guardar</button>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    <script>
    var marcaInput = document.getElementById('marca');

    // Función para convertir la primera letra a mayúscula y mantener el resto en minúscula
    function formatFirstLetterToUpper(value) {
        return value.charAt(0).toUpperCase() + value.slice(1).toLowerCase();
    }

    // Evento input para convertir la primera letra en mayúscula al ingresar texto
    marcaInput.addEventListener('input', function(e) {
        var value = e.target.value;
        var formattedValue = formatFirstLetterToUpper(value);

        marcaInput.value = formattedValue;
    });

    // Convertir el valor inicial a la carga de la página
    marcaInput.value = formatFirstLetterToUpper(marcaInput.value);
    document.getElementById('tipo').addEventListener('input', function(e) {
        var input = e.target;
        var value = input.value;
        var regex =
            /^[a-zA-Z0-9\s]*$/; // Expresión regular para permitir solo letras, números y espacios

        if (!regex.test(value)) {
            input.value = value.replace(/[^a-zA-Z0-9\s]/g, ''); // Remover caracteres especiales
        }
    });
    function formatInput(input) {
        var value = input.value;

        // Eliminar caracteres especiales y convertir la primera letra a mayúscula y mantener el resto en minúscula
        var formattedValue = value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '').charAt(0).toUpperCase() + value.slice(1).toLowerCase();

        // Actualizar el valor del campo
        input.value = formattedValue;
    }
    </script>
</div>
@stop
@section('js')
<script src="{{ asset('js/validatorFields.js') }}"></script>
@endsection