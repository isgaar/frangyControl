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
    <div class="card">
        <div class="card-header">
            <ul>
                <b>Creando registro único</b>
            </ul>
        </div>
    </div>
@endsection

@section('content')
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
                    {!! Form::open(['route' => 'tipo_vehiculo.store', 'method' => 'post', 'enctype' => 'multipart/form-data']) !!}
                    <!-- Campos para TipoVehiculo -->
                    <div class="form-group row">
                        <label for="tipo" class="col-md-4 col-form-label text-md-right">Tipo de Vehículo</label>
                        <div class="col-md-6">
                            <input id="tipo" type="text" class="form-control" name="tipo" required>
                        </div>
                    </div>

                    <div class="card-footer text-center">
                        <div class="d-flex justify-content-between">
                            <a type="button" href="{{ route('datosv.index') }}" class="btn btn-outline-light">Retroceder</a>
                            <button type="submit" class="btn btn-outline-success">Guardar</button>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="{{ asset('js/validatorFields.js') }}"></script>
@endsection
