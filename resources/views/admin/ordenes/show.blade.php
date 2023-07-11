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
        opacity: 9.7;
        cursor: not-allowed;
    }
</style>


<div class="card">
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header bg-danger">
                    <h3 class="card-title">Información del cliente</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="nombreCompleto">Nombre completo</label>
                        <input type="text" name="nombreCompleto" class="form-control"
                            value="{{ $orden->cliente->nombreCompleto }}" disabled>
                    </div>

                    <div class="form-group">
                        <label for="telefono">Teléfono</label>
                        <input type="text" name="telefono" class="form-control" value="{{ $orden->cliente->telefono }}"
                            disabled>
                    </div>

                    <div class="form-group">
                        <label for="correo">Correo electrónico</label>
                        <input type="text" name="correo" class="form-control" value="{{ $orden->cliente->correo }}"
                            disabled>
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
                                    <select name="vehiculo_id" class="form-control" id="vehiculo_id" disabled>

                                        @foreach ($datosVehiculo as $vehiculo)
                                        <option value="{{ $vehiculo->id_vehiculo }}">{{ $orden->vehiculo->marca }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                            <div class="form-group">
                                <label for="tvehiculo_id">Tipo de Vehículo</label>
                                <select name="tvehiculo_id" class="form-control" id="tvehiculo_id" disabled>
                                    @foreach ($tiposVehiculo as $tipoVehiculo)
                                    <option value="{{ $tipoVehiculo->id_tvehiculo }}">{{ $orden->tipoVehiculo->tipo }}
                                    </option>
                                    @endforeach
                                </select>

                            </div>
                            <div class="form-group">
                                <label for="motor">Modelo</label>
                                {!! Form::text('modelo', $orden->modelo, ['class' => 'form-control', 'id' => 'modelo',
                                'disabled']) !!}

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="yearVehiculo">Año</label>
                                {!! Form::text('yearVehiculo', $orden->yearVehiculo, ['class' => 'form-control', 'id' =>
                                'yearVehiculo',
                                'disabled']) !!}
                            </div>
                            <div class="form-group">
                                <label for="color">Color</label>
                                {!! Form::text('color', $orden->color, ['class' => 'form-control', 'id' => 'color',
                                'disabled']) !!}
                            </div>
                            <div class="form-group">
                                <label for="placas">Placas</label>
                                {!! Form::text('placas', $orden->placas, ['class' => 'form-control', 'id' => 'placas',
                                'disabled']) !!}
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
                                    {!! Form::text('kilometraje', $orden->kilometraje, ['class' => 'form-control', 'id'
                                    =>
                                    'kilometraje', 'disabled']) !!}
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
                                {!! Form::text('motor', $orden->motor, ['class' => 'form-control', 'id' => 'motor',
                                'disabled']) !!}
                            </div>
                        </div>

                        <div class="col-md-6">

                            <div class="form-group">

                                <div class="form-group">
                                    <label for="cilindros">Cilindros</label>
                                    {!! Form::text('cilindros', $orden->cilindros, ['class' => 'form-control', 'id' =>
                                    'cilindros', 'disabled']) !!}

                                </div>

                                <div class="form-group">
                                    <label for="numSerie">No. Serie</label>
                                    {!! Form::text('noSerievehiculo', $orden->noSerievehiculo, ['class' =>
                                    'form-control', 'id' =>
                                    'noSerievehiculo', 'disabled']) !!}
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
                    <h3 class="card-title">Datos de la orden</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="servicio_id">Tipo de Servicio</label>
                                    <select name="servicio_id" class="form-control" id="servicio_id" disabled>
                                        @foreach ($tiposServicio as $tipoServicio)
                                        <option value="{{ $tipoServicio->id_servicio }}">
                                            {{  $orden->servicio->nombreServicio }}
                                        </option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user_id">Atiende</label>
                                    <select name="user_id" class="form-control" id="user_id" disabled>
                                        @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $orden->user->name }}</option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="observacionesInt">Observaciones internas (Recepción)</label>
                                    {!! Form::textarea('observacionesInt', $orden->observacionesInt, ['class' =>
                                    'form-control', 'id' =>
                                    'observacionesInt', 'disabled']) !!}
                                </div>

                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="recomendacionesCliente">Recomendaciones del cliente</label>
                                    {!! Form::textarea('recomendacionesCliente', $orden->recomendacionesCliente,
                                    ['class' => 'form-control', 'id'
                                    => 'recomendacionesCliente', 'disabled']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="detallesOrden">Detalles del servicio</label>
                                    {!! Form::textarea('detallesOrden', $orden->detallesOrden, ['class' =>
                                    'form-control', 'id' =>
                                    'detallesOrden', 'disabled']) !!}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="retiroRefacciones">Refacciones</label>
                                    {!! Form::select('retiroRefacciones', [false => 'No retiró', true => 'Retiró'],
                                    $orden->retiroRefacciones ? true : false, ['class' => 'form-control', 'id' =>
                                    'retiroRefacciones', 'disabled']) !!}

                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fechaEntrega">Fecha de entrega</label>
                                    <?php
                                    $fechaEntrega = \Carbon\Carbon::createFromFormat('Y-m-d', $orden->fechaEntrega)->format('d/m/Y');
                                    ?>
                                    {!! Form::text('fechaEntrega', $fechaEntrega, ['class' => 'form-control', 'id' =>
                                    'fechaEntrega', 'disabled']) !!}
                                </div>
                            </div>

                        </div>
                        <div class="form-group">
                            <input type="checkbox" id="myCheckbox" disabled checked>
                            <label for="myCheckbox">El cliente aceptó</label>
                        </div>

                    </div>
                </div>

                <div class="card-footer text-center">
                    <div class="d-flex justify-content-between">
                        <a type="button" href="{{ route('ordenes.index') }}" class="btn btn-outline-dark">Retroceder</a>
                        <button type="button" class="btn btn-warning" onclick="window.location.href = '{{ route('ordenes.edit', ['id_ordenes' => $orden->id_ordenes]) }}'">
    <i class="fas fa-undo-alt"></i> Editar o actualizar esta orden
</button>

                        <button type="button" class="btn btn-info"> <i class="fas fa-file-pdf"></i>Exportar a
                            PDF</button>
                    </div>
                </div>
                @stop

                @section('js')
                <script src="{{ asset('js/validatorFields.js') }}">

                </script>
                @endsection