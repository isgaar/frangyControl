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
                    <h8 class="card-text">Usted está guardando datos en la tabla "Marcas de vehículos".</h8>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12 col-sm-12 col-12">
                        <!-- Campos para DatosVehiculo -->
                        {!! Form::open(['route' => 'datosv.storeunique', 'method' => 'post', 'enctype' =>
                        'multipart/form-data']) !!}
                        <fieldset>
                            <div class="form-group row">
                                <label for="marca" class="col-md-4 col-form-label text-md-right">Marca</label>
                                <div class="col-md-6">
                                    <input id="marca" type="text" class="form-control" name="marcas[]" required
                                        oninput="formatInput(this)">
                                </div>
                            </div>

                            <script>
                            function formatInput(input) {
                                var value = input.value;

                                // Eliminar caracteres especiales y convertir la primera letra a mayúscula y mantener el resto en minúscula
                                var formattedValue = value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '').charAt(0)
                                    .toUpperCase() + value.slice(1).toLowerCase();

                                // Actualizar el valor del campo
                                input.value = formattedValue;
                            }
                            </script>

                            <div id="camposMarcaContainer">
                                <!-- Contenedor para campos de marca -->
                            </div>

                            <div class="form-group row">
                                <div class="col-md-6 offset-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="agregarMarca"
                                            name="agregarMarca">
                                        <label class="form-check-label" for="agregarMarca">
                                            ¿Desea agregar otra marca?
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div id="botonAgregarMarcaContainer" style="display: none;">
                                <div class="form-group row">
                                    <div class="col-md-6 offset-md-4">
                                        <button type="button" class="btn btn-outline-primary"
                                            onclick="agregarCampoMarca()">
                                            Agregar Marca
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <script>
                            document.getElementById('agregarMarca').addEventListener('change', function(e) {
                                var checkbox = e.target;
                                var botonAgregarMarcaContainer = document.getElementById(
                                    'botonAgregarMarcaContainer');

                                if (checkbox.checked) {
                                    botonAgregarMarcaContainer.style.display = 'block';
                                } else {
                                    botonAgregarMarcaContainer.style.display = 'none';
                                }
                            });

                            function agregarCampoMarca() {
                                var camposMarcaContainer = document.getElementById('camposMarcaContainer');

                                // Crear campo de marca utilizando Laravel Collective
                                var marcaField = document.createElement('div');
                                marcaField.classList.add('form-group', 'row');
                                marcaField.innerHTML = `
                        <label class="col-md-4 col-form-label text-md-right">Marca</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="marcas[]" required oninput="formatInput(this)">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="eliminarCampoMarca(this)">
                                Borrar
                            </button>
                        </div>
                    `;
                                camposMarcaContainer.appendChild(marcaField);

                                // Asignar evento input al nuevo campo de marca
                                var nuevoCampoMarcaInput = marcaField.querySelector('input');
                                nuevoCampoMarcaInput.addEventListener('input', function(e) {
                                    var value = e.target.value;
                                    var formattedValue = formatInput(value);

                                    e.target.value = formattedValue;
                                });
                            }

                            function eliminarCampoMarca(btn) {
                                var campoMarca = btn.closest('.form-group.row');
                                campoMarca.remove();
                            }
                            </script>

                            <div class="card-footer text-center">
                                <div class="d-flex justify-content-between">
                                    <a type="button" href="{{ route('datosv.index') }}"
                                        class="btn btn-outline-dark">Retroceder</a>
                                    <button type="submit" class="btn btn-success">Guardar</button>
                                </div>
                            </div>
                        </fieldset>
                        {!! Form::close() !!}
                    </div>

        </div>
    </div>
    @stop

    @section('js')
    <script src="{{ asset('js/validatorFields.js') }}"></script>
    @endsection