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
            <div class="card-header bg-white">
                <h3 class="card-title">Información del cliente</h3>
            </div>
            {!! Form::open(['route' => 'ordenes.store', 'method' => 'post', 'enctype' => 'multipart/form-data', 'id' => 'formulario']) !!}
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

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="defaultCheck1">
                    {!! Form::checkbox('deshabilitar', 1, false, ['class' => 'form-check-input', 'id' => 'deshabilitarCampos']) !!}
                    <label class="form-check-label" for="deshabilitarCampos">El cliente existe</label>
                </div>

                <div class="form-group">
                    <label for="marca">Nombre del cliente</label>
                        <select class="form-control" id="selectCliente" disabled data-url="{{ route('cliente.list') }}">
                            <option value="">--seleccione cliente--</option>
                        </select>
                    <input type="hidden" id="clienteId" name="id_cliente">
                </div>
                </div>

                <script>
    document.addEventListener('DOMContentLoaded', function() {
        var deshabilitarCampos = document.getElementById('deshabilitarCampos');
        var nombreCompletoInput = document.getElementById('nombreCompleto');
        var telefonoInput = document.getElementById('telefono');
        var correoInput = document.getElementById('correo');
        var selectCliente = document.getElementById('selectCliente');
        var clienteIdInput = document.getElementById('clienteId');
        var url = selectCliente.getAttribute('data-url');

        // Obtener los datos de los clientes mediante AJAX
        function loadClientes() {
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    // Limpiar el select antes de llenarlo nuevamente
                    selectCliente.innerHTML = '';

                    // Llenar el select con los datos de los clientes
                    for (var i = 0; i < data.length; i++) {
                        var option = document.createElement('option');
                        option.value = data[i].id_cliente;
                        option.textContent = data[i].nombreCompleto; // Ajustar el nombre del campo de nombre completo
                        selectCliente.appendChild(option);
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Habilitar o deshabilitar los campos
        function toggleCampos() {
            var isChecked = deshabilitarCampos.checked;
            nombreCompletoInput.disabled = isChecked;
            telefonoInput.disabled = isChecked;
            correoInput.disabled = isChecked;
        }

        // Habilitar o deshabilitar el select en función del checkbox
        function toggleSelectCliente() {
            selectCliente.disabled = !this.checked;
            clienteIdInput.value = ""; // Limpiar el valor del ID del cliente
            if (this.checked) {
                loadClientes();
            } else {
                // Limpiar el select
                selectCliente.innerHTML = '<option value="">--seleccione cliente--</option>';
            }
        }

        // Guardar el ID del cliente seleccionado
        function saveSelectedCliente() {
            clienteIdInput.value = selectCliente.value;
        }

        // Escuchar los eventos de cambio del checkbox y del select
        deshabilitarCampos.addEventListener('change', toggleCampos);
        selectCliente.addEventListener('change', saveSelectedCliente);
        deshabilitarCampos.addEventListener('change', toggleSelectCliente);
    });
</script>



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
                        <label for="marca">Marca</label>
                        <select class="form-control" id="selectMarca">
                            <option value="" selected>--seleccione una marca--</option>
                        </select>
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            var selectMarca = document.getElementById('selectMarca');
                           var url = '{{ route("marca.list") }}';

        // Obtener los datos de las marcas mediante AJAX
                            function loadMarcas() {
                               fetch(url)
                                   .then(response => response.json())
                                   .then(data => {
                                       // Limpiar el select antes de llenarlo nuevamente
                                       selectMarca.innerHTML = '';

                    // Llenar el select con los datos de las marcas
                                       for (var i = 0; i < data.length; i++) {
                                           var option = document.createElement('option');
                                           option.value = data[i].id_vehiculo;
                                           option.textContent = data[i].marca;
                                           selectMarca.appendChild(option);
                                       }
                                    })
                                   .catch(error => console.error('Error:', error));
                           }

                            // Cargar las marcas al cargar la página
                            loadMarcas();
                        });
                    </script>

<div class="form-group">
    <label for="tipoVehiculo" id="selectTipo">Tipo de vehículo</label>
    <select class="form-control" id="selectTipoVehiculo">
        <option value="">--seleccione un tipo--</option>
    </select>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var selectTipoVehiculo = document.getElementById('selectTipoVehiculo');
        var url = '{{ route("tipov.list") }}';

        // Obtener los tipos de vehículo mediante AJAX
        function loadTiposVehiculo() {
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    // Limpiar el select antes de llenarlo nuevamente
                    selectTipoVehiculo.innerHTML = '';

                    // Llenar el select con los tipos de vehículo
                    for (var i = 0; i < data.length; i++) {
                        var option = document.createElement('option');
                        option.value = data[i].id_tvehiculo;
                        option.textContent = data[i].tipo;
                        selectTipoVehiculo.appendChild(option);
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Cargar los tipos de vehículo al cargar la página
        loadTiposVehiculo();
    });
</script>


                        

                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="correo">Año</label>
                            @error('correo')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="correo">Color</label>
                            @error('correo')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="correo">Placas</label>
                            @error('correo')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="correo">Kilómetraje</label>
                            @error('correo')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="correo">Motor</label>
                            @error('correo')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="correo">Cilindros</label>
                            @error('correo')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="correo">No. Serie</label>
                            {!! Form::text('correo', null, ['class' => 'form-control', 'id_cliente' => 'correo']) !!}
                            @error('correo')
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
                <h3 class="card-title">Datos de la órden</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                        <div class="form-group">
    <label for="tipoServicio">Tipo de servicio</label>
    <select class="form-control" id="selectTipoServicio">
        <option value="">--seleccione el servicio--</option>
    </select>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var selectTipoServicio = document.getElementById('selectTipoServicio');
        var url = '{{ route("tipos.list") }}';

        // Obtener los tipos de servicio mediante AJAX
        function loadTiposServicio() {
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    // Limpiar el select antes de llenarlo nuevamente
                    selectTipoServicio.innerHTML = '';

                    // Llenar el select con los tipos de servicio
                    for (var i = 0; i < data.length; i++) {
                        var option = document.createElement('option');
                        option.value = data[i].id_servicio;
                        option.textContent = data[i].nombreServicio;
                        selectTipoServicio.appendChild(option);
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Cargar los tipos de servicio al cargar la página
        loadTiposServicio();
    });
</script>

                        </div>
                        <div class="col-md-6">
                        <div class="form-group">
    <label for="atiende">Atiende</label>
    <select class="form-control" id="selectAtiende">
        <option value="">--seleccione el empleado--</option>
    </select>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var selectAtiende = document.getElementById('selectAtiende');
        var url = '{{ route("user.list") }}';

        // Obtener los empleados mediante AJAX
        function loadEmpleados() {
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    // Limpiar el select antes de llenarlo nuevamente
                    selectAtiende.innerHTML = '';

                    // Llenar el select con los empleados
                    for (var i = 0; i < data.length; i++) {
                        var option = document.createElement('option');
                        option.value = data[i].id;
                        option.textContent = data[i].name;
                        selectAtiende.appendChild(option);
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Cargar los empleados al cargar la página
        loadEmpleados();
    });
</script>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="exampleFormControlTextarea1">Observaciones internas (Recepción)</label>
                                <textarea class="form-control"></textarea>
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
                        </script>
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

<script>
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

            var previewDiv = document.createElement('div');

            var img = document.createElement('img');
            img.id = 'photoPreview' + photoFieldCount;
            img.src = '#';
            img.alt = 'Previsualización';
            img.style.maxWidth = '200px';
            img.style.maxHeight = '200px';

            var deleteButton = document.createElement('button');
            deleteButton.type = 'button';
            deleteButton.className = 'btn btn-outline-danger';
            deleteButton.textContent = 'Eliminar';
            deleteButton.onclick = function() {
                deletePhotoField(photoFieldCount);
                toggleAddMorePhotosButton();
            };

            previewDiv.appendChild(img);
            previewDiv.appendChild(deleteButton);

            photoField.appendChild(label);
            photoField.appendChild(input);
            photoField.appendChild(previewDiv);

            container.appendChild(photoField);

            toggleAddMorePhotosButton();
        }

        function deletePhotoField(fieldIndex) {
            var fieldId = 'photoField' + fieldIndex;
            var field = document.getElementById(fieldId);
            field.parentNode.removeChild(field);
        }

        function previewPhoto(event, previewId, fieldIndex) {
            var photo = document.getElementById(previewId);
            photo.src = URL.createObjectURL(event.target.files[0]);
        }

        function toggleAddMorePhotosButton() {
            var photosCount = document.getElementsByClassName('photoField').length;
            var addMorePhotosContainer = document.getElementById('addMorePhotosContainer');
            addMorePhotosContainer.style.display = (photosCount > 0) ? 'block' : 'none';
        }
    }
</script>



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
        <button id="submitButton" type="submit" class="btn btn-success" disabled>Guardar</button>
    </div>
</div>

<script>
    document.getElementById("myCheckbox").addEventListener("change", function () {
        var submitButton = document.getElementById("submitButton");
        submitButton.disabled = !this.checked;
    });
</script>

        

    </div>
    {!! Form::close() !!}
</div>
</div>
</div>
@stop

@section('js')
<script src="{{ asset('js/validatorFields.js') }}">

</script>
@endsection
