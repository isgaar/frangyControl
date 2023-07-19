@extends('adminlte::page')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/minMaxTimePlugin.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
.form-control[disabled] {
    background-color: #f8f9fa;
    opacity: 0.7;
    cursor: not-allowed;
}
</style>


<div class="card">
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary">
                {!! Form::model($orden, ['route' => ['ordenes.update', $orden->id_ordenes], 'method' => 'put', 'enctype'
                => 'multipart/form-data']) !!}



                @csrf
                <div class="card-header bg-danger">
                    <h3 class="card-title">Información del cliente</h3>
                </div>
                <div class="card-body">

                    <div class="form-group">
                        <label for="nombreCompleto">Nombre completo</label>
                        <input type="text" name="nombreCompleto" class="form-control" maxlength="100"
                            id="nombreCompleto" oninput="formatNameInput(this)"
                            value="{{ $orden->cliente->nombreCompleto }}">
                        @error('nombreCompleto')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>


                    <div class="form-group">
                        <label for="telefono">Teléfono</label>
                        <input type="text" name="telefono" class="form-control" id="telefono" pattern="[0-9]{0,10}"
                            onkeypress="return isNumberKey(event)" oninput="truncatePhoneNumber(this)"
                            value="{{ $orden->cliente->telefono ?? '' }}">
                        @error('telefono')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>


                    <div class="form-group">
                        <label for="correo">Correo electrónico</label>
                        <input type="text" name="correo" class="form-control" maxlength="30" id="correo"
                            oninput="validateEmail(this)" value="{{ $orden->cliente->correo ?? '' }}">
                        @error('correo')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="rfc">RFC</label>
                        <input type="text" name="rfc" class="form-control" maxlength="13" id="rfc"
                            oninput="formatRFC(event)" value="{{ $orden->cliente->rfc ?? '' }}">
                        @error('rfc')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>



                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header bg-danger">
                    <h3 class="card-title">Datos de la unidad</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="form-group">

                                    <label for="vehiculo_id">Marca</label>
                                    <select name="vehiculo_id" class="form-control" id="vehiculo_id">
                                        <option value="">-- Seleccione una marca --</option>
                                        @foreach ($datosVehiculo as $vehiculo)
                                        <option value="{{ $vehiculo->id_vehiculo }}" @if ($vehiculo->id_vehiculo ==
                                            $orden->vehiculo_id) selected @endif>
                                            {{ $vehiculo->marca }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                            <div class="form-group">
                                <label for="tvehiculo_id">Tipo de Vehículo</label>
                                <select name="tvehiculo_id" class="form-control" id="tvehiculo_id">
                                    <option value="">-- Seleccione un tipo --</option>
                                    @foreach ($tiposVehiculo as $tipoVehiculo)
                                    <option value="{{ $tipoVehiculo->id_tvehiculo }}" @if ($tipoVehiculo->id_tvehiculo
                                        == $orden->tipoVehiculo->id_tvehiculo) selected @endif>
                                        {{ $tipoVehiculo->tipo }}
                                    </option>
                                    @endforeach
                                </select>


                            </div>
                            <div class="form-group">
                                <label for="modelo">Línea</label>
                                {!! Form::text('modelo', null, ['class' => 'form-control', 'id' => 'modelo', 'maxlength'
                                => '100', 'oninput' => 'capitalizeFirstLetter(event)']) !!}
                                @error('modelo')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="yearVehiculo">Año</label>
                                {!! Form::text('yearVehiculo', null, ['class' => 'form-control', 'id' => 'yearVehiculo',
                                'oninput' => 'validateYearInput(this)']) !!}
                                @error('yearVehiculo')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="color">Color</label>
                                {!! Form::text('color', null, ['class' => 'form-control', 'id' => 'color', 'maxlength'
                                => '80', 'oninput' => 'formatColorInput(this)']) !!}
                                @error('color')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="placas">Placas</label>
                                {!! Form::text('placas', null, ['class' => 'form-control', 'id' => 'placas', 'oninput'
                                => 'limitInputLength(this, 7); formatPlacasInput(this);']) !!}
                                @error('placas')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="kilometraje">Kilometraje</label>

                                <div class="input-group">
                                    {!! Form::text('kilometraje', null, ['class' => 'form-control', 'id' =>
                                    'kilometraje', 'maxlength' => '8', 'oninput' => 'formatKilometrajeInput(this)']) !!}
                                    <div class="input-group-append">
                                        <span class="input-group-text">Km</span>
                                    </div>
                                </div>
                                @error('kilometraje')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="motor">Motor</label>
                                {!! Form::text('motor', null, ['class' => 'form-control', 'id' => 'motor', 'maxlength'
                                => '8', 'oninput' => 'formatMotorInput(this)']) !!}
                                @error('motor')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>


                        </div>

                        <div class="col-md-6">

                            <div class="form-group">

                                <div class="form-group">
                                    <label for="cilindros">Cilindros</label>
                                    {!! Form::text('cilindros', null, ['class' => 'form-control', 'id' => 'cilindros',
                                    'maxlength' => '20', 'oninput' => 'formatCilindrosInput(this)']) !!}
                                    @error('cilindros')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="numSerie">No. Serie</label>
                                    {!! Form::text('noSerievehiculo', null, ['class' => 'form-control', 'id' =>
                                    'noSerievehiculo', 'oninput' => 'validateInput(this)']) !!}
                                    @error('noSerievehiculo')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header bg-danger">
                    <h3 class="card-title">Datos de la orden #{{$orden->id_ordenes}}</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="servicio_id">Tipo de Servicio</label>
                                    <select name="servicio_id" class="form-control" id="servicio_id">
                                        <option value="">-- Seleccione un servicio --</option>
                                        @foreach ($tiposServicio as $tipoServicio)
                                        <option value="{{ $tipoServicio->id_servicio }}" @if ($tipoServicio->id_servicio
                                            == $orden->servicio->id_servicio) selected @endif>
                                            {{ $tipoServicio->nombreServicio }}
                                        </option>
                                        @endforeach
                                    </select>


                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user_id">Atiende</label>
                                    <select name="user_id" class="form-control" id="user_id">
                                        <option value="">-- Seleccione empleado --</option>
                                        @foreach ($users as $user)
                                        @if ($user->id !== 1)
                                        <option value="{{ $user->id }}" @if ($user->id == $orden->user->id) selected
                                            @endif>
                                            {{ $user->name }}
                                        </option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="observacionesInt">Observaciones internas (Recepción)</label>
                                    {!! Form::textarea('observacionesInt', null, ['class' => 'form-control', 'id' =>
                                    'observacionesInt']) !!}
                                </div>

                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="recomendacionesCliente">Recomendaciones del cliente</label>
                                    {!! Form::textarea('recomendacionesCliente', null, ['class' => 'form-control', 'id'
                                    => 'recomendacionesCliente']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="detallesOrden">Detalles del servicio</label>
                                    {!! Form::textarea('detallesOrden', null, ['class' => 'form-control', 'id' =>
                                    'detallesOrden']) !!}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="retiroRefacciones">Refacciones</label>
                                    {!! Form::select('retiroRefacciones', [false => 'No retira', true => 'Retira'],
                                    null, ['class' => 'form-control', 'id' => 'retiroRefacciones']) !!}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fechaEntrega">Fecha de entrega</label>
                                    {!! Form::text('fechaEntrega', null, ['class' => 'form-control', 'id' =>
                                    'fechaEntrega', 'placeholder' => 'Fecha de entrega']) !!}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Estado</label>
                                    <select name="status" class="form-control" id="status">
                                        <option value="">Seleccione un estado</option>
                                        <option value="en proceso"
                                            {{ $orden->status == 'en proceso' ? 'selected' : '' }}>En proceso</option>
                                        <option value="cancelada" {{ $orden->status == 'cancelada' ? 'selected' : '' }}>
                                            Cancelada</option>
                                        <option value="finalizada"
                                            {{ $orden->status == 'finalizada' ? 'selected' : '' }}>Finalizada</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6" id="motivo-field"
                                style="{{ $orden->status == 'cancelada' ? 'display: block;' : 'display: none;' }}">
                                <div class="form-group">
                                    <label for="motivo">Motivo</label>
                                    {!! Form::text('motivo', null, ['class' => 'form-control', 'id' => 'motivo']) !!}
                                    @error('motivo')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>
                        </div>
                        <div id="photoFieldsContainer" class="input-group mb-3" style="display: none;">
                            <div class="photoField input-group" id="photoField1">
                                <label class="input-group-text" for="photo1">Tomar fotografía:</label>
                                <input type="file" class="form-control" accept="image/*" capture="camera" id="photo1"
                                    name="photos[]"
                                    onchange="previewPhoto(event, 'photoPreview1', 1); toggleAddMorePhotosButton();">
                                <div class="mt-2">
                                    <img id="photoPreview1" src="#" alt="Previsualización"
                                        style="max-width: 200px; max-height: 200px;">
                                    <button type="button" class="btn btn-outline-danger"
                                        onclick="deletePhotoField(1); toggleAddMorePhotosButton();">Eliminar</button>
                                </div>
                            </div>
                        </div>

                        <div id="addMorePhotosContainer" class="mt-2" style="display: none;">
                            <button type="button" class="btn btn-outline-light" onclick="addPhotoField()">Agregar más
                                fotos</button>
                        </div>
                        <div class="form-group">
                            <input type="checkbox" id="myCheckbox">
                            <label for="myCheckbox">El cliente acepta</label>
                        </div>
                    </div>
                </div>

                <div class="card-footer text-center">
                    <div class="d-flex justify-content-between">
                        <a type="button" href="{{ route('ordenes.index') }}" class="btn btn-outline-dark">Retroceder</a>
                        <button type="submit" class="btn btn-info" id="submitButton" disabled>Actualizar orden</button>
                    </div>
                </div>

                {!! Form::close() !!}


                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                <script>
                function validateYearInput(input) {
                    var year = input.value;
                    year = year.replace(/[^0-9]/g, ''); // Eliminar caracteres no numéricos
                    year = year.slice(0, 4); // Limitar a 4 dígitos
                    input.value = year;
                }

                function cambiarNombre(nombre) {
                    let regex = /^[a-zA-ZÀ-ÿ\u00f1\u00d1][a-zA-ZÀ-ÿ\u00f1\u00d1\s]*$/g;
                    return regex.exec(nombre)[0];
                }

                function formatNameInput(input) {
                    var name = input.value;
                    // Reemplazar caracteres especiales, números, y símbolos con espacios
                    name = cambiarNombre(name);
                    // Convertir la primera letra de cada palabra a mayúscula y las demás letras a minúscula
                    name = name.replace(/(?:^|\s)\S/g, function(l) {
                        return l.toUpperCase();
                    });
                    input.value = name;
                }

                const statusSelect = document.getElementById('status');
                const motivoField = document.getElementById('motivo-field');

                statusSelect.addEventListener('change', function() {
                    if (statusSelect.value === 'cancelada') {
                        motivoField.style.display = 'block';
                    } else {
                        motivoField.style.display = 'none';
                        document.getElementById('motivo').value =
                            ''; // Establece el campo de motivo como una cadena vacía
                    }
                });

                function isNumberKey(evt) {
                    var charCode = (evt.which) ? evt.which : event.keyCode;
                    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                        return false;
                    }
                    return true;
                }

                function truncatePhoneNumber(input) {
                    var phoneNumber = input.value;
                    if (phoneNumber.length > 10) {
                        phoneNumber = phoneNumber.slice(0, 10);
                    }
                    input.value = phoneNumber;
                }

                function validateEmail(input) {
                    var email = input.value;
                    var regex = /^[a-z0-9@]+$/;

                    if (!regex.test(email)) {
                        input.value = email.slice(0, -1);
                    }

                    if (email.split("@").length > 2) {
                        input.value = email.slice(0, -1);
                    }

                    if (email.length > 25) {
                        input.value = email.slice(0, 25);
                    }
                    input.value = email.toLowerCase();
                }

                function formatColorInput(input) {
                    var color = input.value;
                    // Eliminar caracteres especiales y números
                    color = color.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
                    // Convertir la primera letra a mayúscula y las demás a minúscula
                    color = color.charAt(0).toUpperCase() + color.slice(1).toLowerCase();
                    input.value = color;
                }

                function formatMotorInput(input) {
                    var motor = input.value;
                    // Reemplazar cualquier carácter que no sea número, letra o punto decimal
                    motor = motor.replace(/[^A-Za-z0-9.]/g, '');
                    // Convertir a mayúsculas
                    motor = motor.toUpperCase();
                    // Limitar el número de dígitos a 8
                    motor = motor.slice(0, 8);
                    input.value = motor;
                }

                function formatCilindrosInput(input) {
                    var cilindros = input.value;
                    // Reemplazar cualquier carácter que no sea número o punto decimal
                    cilindros = cilindros.replace(/[^0-9.]/g, '');
                    // Limitar el número de dígitos a 20
                    cilindros = cilindros.slice(0, 20);
                    input.value = cilindros;
                }



                flatpickr("#fechaEntrega", {
                    enableTime: false,
                    minDate: "today",
                    dateFormat: "Y/m/d",
                    plugins: [
                        new minMaxTimePlugin({
                            minTime: "12:00",
                            maxTime: "23:59",
                            getMinMaxDate: function() {
                                return {
                                    minDate: new Date(),
                                    maxDate: new Date().fp_incr(60)
                                }
                            }
                        })
                    ],
                    locale: {
                        weekdays: {
                            shorthand: ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'],
                            longhand: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado']
                        },
                        months: {
                            shorthand: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct',
                                'Nov', 'Dic'
                            ],
                            longhand: [
                                'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                                'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
                            ]
                        },
                    },
                    onChange: function(selectedDates, dateStr, instance) {
                        var currentDate = new Date();
                        var maxDate = new Date();
                        maxDate.setMonth(currentDate.getMonth() + 1);

                        if (selectedDates[0] > maxDate) {
                            instance.setDate("");
                        }
                    }
                });

                var isMobile = window.matchMedia("only screen and (max-width: 1080px)").matches;

                if (isMobile) {
                    // Mostrar el campo en dispositivos móviles
                    var photoFieldsContainer = document.getElementById('photoFieldsContainer');
                    var addMorePhotosContainer = document.getElementById('addMorePhotosContainer');

                    photoFieldsContainer.style.display = 'block';
                    addMorePhotosContainer.style.display = 'block';

                    var photoFieldCount = 1;

                    function addPhotoField() {
                        photoFieldCount++;

                        var container = document.getElementById('photoFieldsContainer');

                        var photoField = document.createElement('div');
                        photoField.className = 'photoField input-group';
                        photoField.id = 'photoField' + photoFieldCount;

                        var label = document.createElement('label');
                        label.className = 'input-group-text';
                        label.textContent = 'Tomar fotografía:';

                        var input = document.createElement('input');
                        input.type = 'file';
                        input.className = 'form-control';
                        input.accept = 'image/*';
                        input.capture = 'camera';
                        input.id = 'photo' + photoFieldCount;
                        input.name = 'photos[]';
                        input.onchange = function(event) {
                            previewPhoto(event, 'photoPreview' + photoFieldCount, photoFieldCount);
                            toggleAddMorePhotosButton();
                        };

                        var preview = document.createElement('img');
                        preview.className = 'photoPreview';
                        preview.id = 'photoPreview' + photoFieldCount;

                        photoField.appendChild(label);
                        photoField.appendChild(input);
                        photoField.appendChild(preview);

                        container.appendChild(photoField);

                        toggleAddMorePhotosButton();
                    }

                    function removePhotoField() {
                        if (photoFieldCount > 1) {
                            var container = document.getElementById('photoFieldsContainer');
                            var photoField = document.getElementById('photoField' + photoFieldCount);

                            container.removeChild(photoField);

                            photoFieldCount--;

                            toggleAddMorePhotosButton();
                        }
                    }

                    function toggleAddMorePhotosButton() {
                        var addMorePhotosButton = document.getElementById('addMorePhotosButton');
                        var removePhotoButton = document.getElementById('removePhotoButton');

                        if (photoFieldCount === 1) {
                            removePhotoButton.style.display = 'none';
                        } else if (photoFieldCount === 3) {
                            addMorePhotosButton.style.display = 'none';
                        } else {
                            addMorePhotosButton.style.display = 'block';
                            removePhotoButton.style.display = 'block';
                        }
                    }

                    function previewPhoto(event, previewId, photoIndex) {
                        var preview = document.getElementById(previewId);
                        var file = event.target.files[0];
                        var reader = new FileReader();

                        reader.onloadend = function() {
                            preview.src = reader.result;
                        }

                        if (file) {
                            reader.readAsDataURL(file);
                        } else {
                            preview.src = "";
                        }
                    }
                }

                function toggleSubmitButton() {
                    var isChecked = deshabilitarCampos.prop('checked');
                    var isClienteSelected = selectCliente.val();
                    var isFormValid = isChecked || (isClienteSelected && nombreCompletoInput.val() && telefonoInput
                        .val() && correoInput.val());

                    $('#submitButton').prop('disabled', !isFormValid);
                }

                function formatKilometrajeInput(input) {
                    var kilometraje = input.value;
                    // Reemplazar cualquier carácter que no sea número o punto decimal
                    kilometraje = kilometraje.replace(/[^0-9.]/g, '');
                    // Limitar el número de dígitos a 6
                    kilometraje = kilometraje.slice(0, 8);
                    input.value = kilometraje;
                }


                function limitInputLength(input, maxLength) {
                    if (input.value.length > maxLength) {
                        input.value = input.value.slice(0, maxLength);
                    }
                }

                function formatPlacasInput(input) {
                    var placas = input.value;
                    // Reemplazar caracteres especiales con espacios
                    placas = placas.replace(/[^a-zA-Z0-9]/g, '');
                    // Convertir a mayúsculas
                    placas = placas.toUpperCase();
                    input.value = placas;
                }

                function validateInput(input) {
                    var value = input.value;
                    // Eliminar cualquier caracter que no sea número o letra
                    value = value.replace(/[^A-Za-z0-9]/g, '');
                    // Convertir a mayúsculas
                    value = value.toUpperCase();
                    // Limitar la longitud a 17 caracteres
                    if (value.length > 17) {
                        value = value.slice(0, 17);
                    }
                    // Actualizar el valor del campo de entrada
                    input.value = value;
                }

                function formatRFC(event) {
                    var input = event.target;
                    var value = input.value;

                    // Verificar la longitud del valor y ajustar si es necesario
                    if (value.length > 13) {
                        input.value = value.slice(0, 13); // Limitar a 13 caracteres
                    }
                    // Obtén el campo de entrada del RFC
                    const rfcInput = event.target;

                    // Obtén el valor actual del campo de entrada
                    let rfcValue = rfcInput.value;

                    // Elimina los caracteres especiales utilizando una expresión regular
                    rfcValue = rfcValue.replace(/[^\w\s]/gi, '');

                    // Convierte las letras a mayúsculas
                    rfcValue = rfcValue.toUpperCase();

                    // Asigna el valor modificado al campo de entrada
                    rfcInput.value = rfcValue;
                }

                function capitalizeFirstLetter(event) {
                    var input = event.target;
                    var value = input.value;

                    // Verificar si hay al menos un carácter y convertir la primera letra en mayúscula
                    if (value.length > 0) {
                        input.value = value.charAt(0).toUpperCase() + value.slice(1);
                    }
                }
                </script>

                <script>
                document.getElementById("myCheckbox").addEventListener("change", function() {
                    var submitButton = document.getElementById("submitButton");
                    submitButton.disabled = !this.checked;
                });
                </script>

                @stop

                @section('js')
                <script src="{{ asset('js/validatorFields.js') }}">

                </script>
                @endsection