@extends('adminlte::page')
@include('landing.include.head')

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
                <b>Eliminando</b>
            </ul>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-danger">
                <div class="card-header d-flex">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <div>
                        <h2 class="card-title">¡Advertencia!</h2>
                        <br>
                        <h8 class="card-text">Usted está eliminando un nombre de la tabla "Tipos de vehículos".</h8>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12 col-sm-12 col-12">
                            {!! Form::open(['route' => ['tipo_vehiculo.destroy', $tipoVehiculo->id_tvehiculo], 'method' => 'get']) !!}
                            <fieldset>
                                <div class="form-group row">
                                    <label for="marca" class="col-md-4 col-form-label text-md-right">Tipo de vehículo</label>
                                    <div class="col-md-6">
                                    {!! Form::UTTextOnly('tipo', '', 'tipo', $tipoVehiculo->tipo, $errors, 40, true) !!}
                                    </div>
                                </div>

                                <div class="card-footer text-center">
                                    <div class="d-flex justify-content-between">
                                        <a type="button" href="{{ route('datosv.index') }}" class="btn btn-outline-light">Retroceder</a>
                                            <button type="submit" class="btn btn-outline-danger">Eliminar</button>
                                    </div>
                                 </div>
                            </fieldset>
                            {!! Form::close() !!}
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

