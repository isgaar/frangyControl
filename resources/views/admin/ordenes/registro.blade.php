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

    <div class="card">

    </div>
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
                {!! Form::open(['route' => 'ordenes.store', 'method' => 'post', 'enctype' => 'multipart/form-data', 'id' => 'formulario']) !!}
                <div class="card-header bg-white">
                    <h3 class="card-title">Información del cliente</h3>
                </div>
                <div class="card-body">
                <div class="form-group">
    <label for="nombreCompleto">Nombre completo</label>
    {!! Form::text('nombreCompleto', null, ['class' => 'form-control', 'id' => 'nombreCompleto']) !!}
        @error('nombreCompleto')
            <span class="text-danger">{{ $message }}</span>
        @enderror
</div>
<div class="form-group">
    <label for="telefono">Teléfono</label>
    {!! Form::text('telefono', null, ['class' => 'form-control', 'id' => 'telefono']) !!}
        @error('telefono')
            <span class="text-danger">{{ $message }}</span>
        @enderror
</div>
<div class="form-group">
    <label for="correo">Correo electrónico</label>
    {!! Form::text('correo', null, ['class' => 'form-control', 'id' => 'correo']) !!}
        @error('correo')
            <span class="text-danger">{{ $message }}</span>
        @enderror
</div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header bg-white">
                    <h3 class="card-title">Datos de la unidad</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                            <div class="form-group">
                                <label for="marcas">Marca</label>
                                {!! Form::select('datos_vehiculo', $marcas->pluck('marca', 'id_vehiculo'), null, ['class' => 'form-control']) !!}
                            </div>
                            </div>
                            <div class="form-group">
                                <label for="tipoVehiculo">Tipo de vehículo</label>
                                {!! Form::select('tipo_vehiculo', $tipov->pluck('tipo', 'id_tvehiculo'), null, ['class' => 'form-control']) !!}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="yearVehiculo">Año</label>
                                {!! Form::text('yearVehiculo', null, ['class' => 'form-control', 'id' => 'yearVehiculo']) !!}
                                @error('yearVehiculo')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="color">Color</label>
                                {!! Form::text('color', null, ['class' => 'form-control', 'id' => 'color']) !!}
                                @error('color')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="placas">Placas</label>
                                {!! Form::text('placas', null, ['class' => 'form-control', 'id' => 'placas']) !!}
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
                                {!! Form::text('kilometraje', null, ['class' => 'form-control', 'id' => 'kilometraje']) !!}
                                @error('kilometraje')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="motor">Motor</label>
                                {!! Form::text('motor', null, ['class' => 'form-control', 'id' => 'motor']) !!}
                                @error('motor')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cilindros">Cilindros</label>
                                {!! Form::text('cilindros', null, ['class' => 'form-control', 'id' => 'cilindros']) !!}
                                @error('cilindros')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="numSerie">No. Serie</label>
                                {!! Form::text('noSerievehiculo', null, ['class' => 'form-control', 'id' => 'noSerievehiculo']) !!}
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

    <div class="row justify-content-center mt-4">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header bg-white">
                    <h3 class="card-title">Datos de la orden</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tipoServicio">Tipo de servicio</label>
                                    {!! Form::select('datos_servicio', $tipos->pluck('nombreServicio', 'id_servicio'), null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="atiende">Atiende</label>
                                    {!! Form::select('users', $empleado->pluck('name', 'id'), null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="exampleFormControlTextarea1">Observaciones internas (Recepción)</label>
                                    <textarea class="form-control" ></textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="exampleFormControlTextarea1">Recomendaciones del cliente</label>
                                    <textarea class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="exampleFormControlTextarea1">Detalles del servicio</label>
                                    <textarea class="form-control"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="marca">Refacciones</label>
                                    <select class="form-control">
                                        <option>Retira</option>
                                        <option>No retira</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fechaEntrega">Fecha de entrega</label>
                                    <input type="text" id="fechaEntrega" class="form-control" placeholder="Fecha de entrega">
                                </div>
                            </div>
                        </div>
                        <div id="photoFieldsContainer" class="input-group mb-3" style="display: none;">
                            <div class="photoField input-group" id="photoField1">
                                <label class="input-group-text" for="photo1">Tomar fotografía:</label>
                                <input type="file" class="form-control" accept="image/*" capture="camera" id="photo1" name="photos[]" onchange="previewPhoto(event, 'photoPreview1', 1); toggleAddMorePhotosButton();">
                                <div class="mt-2">
                                    <img id="photoPreview1" src="#" alt="Previsualización" style="max-width: 200px; max-height: 200px;">
                                    <button type="button" class="btn btn-outline-danger" onclick="deletePhotoField(1); toggleAddMorePhotosButton();">Eliminar</button>
                                </div>
                            </div>
                        </div>

                        <div id="addMorePhotosContainer" class="mt-2" style="display: none;">
                            <button type="button" class="btn btn-outline-light" onclick="addPhotoField()">Agregar más fotos</button>
                        </div>
                        <div class="form-group">
                            <input type="checkbox" id="myCheckbox">
                            <label for="myCheckbox">El cliente acepta</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-footer text-center">
        <div class="d-flex justify-content-between">
            <a type="button" href="{{ route('ordenes.index') }}" class="btn btn-outline-light">Retroceder</a>
            <button type="submit" class="btn btn-primary" id="submitButton" disabled>Guardar orden</button>
        </div>
    </div>

    {!! Form::close() !!}
</div>


</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    flatpickr("#fechaEntrega", {
        enableTime: false,
        minDate: "today",
        dateFormat: "d/m/Y",
        plugins: [
            new minMaxTimePlugin({
                minTime: "12:00",
                maxTime: "23:59",
                getMinMaxDate: function () {
                    return {
                        minDate: new Date(),
                        maxDate: new Date().fp_incr(30)
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
                shorthand: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                longhand: [
                    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
                ]
            },
        },
        onChange: function (selectedDates, dateStr, instance) {
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
    var isFormValid = isChecked || (isClienteSelected && nombreCompletoInput.val() && telefonoInput.val() && correoInput.val());

    $('#submitButton').prop('disabled', !isFormValid);
}
</script>

<script>
        document.getElementById("myCheckbox").addEventListener("change", function () {
            var submitButton = document.getElementById("submitButton");
            submitButton.disabled = !this.checked;
        });
    </script>


        

    </div>
    
</div>
</div>
</div>

@stop

@section('js')
<script src="{{ asset('js/validatorFields.js') }}">

</script>
@endsection
