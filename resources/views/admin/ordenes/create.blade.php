@extends('adminlte::page')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/minMaxTimePlugin.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

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
            {!! Form::open(['route' => 'clientes.store', 'method' => 'post', 'enctype' => 'multipart/form-data', 'id' => 'formulario']) !!}
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
                    <select class="form-control" id="selectCliente" disabled>
                        <option>--seleccione cliente--</option>
                    </select>
                </div>

                <script>
                    document.getElementById('deshabilitarCampos').addEventListener('change', function () {
                        var campos = document.querySelectorAll('#nombreCompleto, #telefono, #correo');
                        campos.forEach(function (campo) {
                            campo.disabled = this.checked;
                            campo.classList.toggle('form-control-disabled', this.checked);
                        }, this);
                        document.getElementById('selectCliente').disabled = !this.checked;
                    });
                </script>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card card-primary">
            <div class="card-header bg-white">
                <h3 class="card-title">Datos de la unidad</h3>
            </div>
            {!! Form::open(['route' => 'clientes.store', 'method' => 'post', 'enctype' => 'multipart/form-data']) !!}
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="marca">Marca</label>
                            <select class="form-control">
                                <option>--seleccione una marca--</option>
                                <option>--seleccione una marca--</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="marca">Tipo de vehículo</label>
                            <select class="form-control">
                                <option>--seleccione un tipo--</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="correo">Año</label>
                            {!! Form::text('correo', null, ['class' => 'form-control', 'id_cliente' => 'correo']) !!}
                            @error('correo')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="correo">Color</label>
                            {!! Form::text('correo', null, ['class' => 'form-control', 'id_cliente' => 'correo']) !!}
                            @error('correo')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="correo">Placas</label>
                            {!! Form::text('correo', null, ['class' => 'form-control', 'id_cliente' => 'correo']) !!}
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
                            {!! Form::text('correo', null, ['class' => 'form-control', 'id_cliente' => 'correo']) !!}
                            @error('correo')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="correo">Motor</label>
                            {!! Form::text('correo', null, ['class' => 'form-control', 'id_cliente' => 'correo']) !!}
                            @error('correo')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="correo">Cilindros</label>
                            {!! Form::text('correo', null, ['class' => 'form-control', 'id_cliente' => 'correo']) !!}
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
                                <label for="marca">Tipo de servicio</label>
                                <select class="form-control">
                                    <option>--seleccione el servicio--</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="marca">Atiende</label>
                                <select class="form-control">
                                    <option>--seleccione empleado--</option>
                                </select>
                            </div>
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
