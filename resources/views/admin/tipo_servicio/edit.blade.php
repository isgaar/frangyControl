@extends('adminlte::page')

@section('content_header')
    @if (Session::has('status'))
        <div class="col-md-12 alert-section">
            <div class="alert alert-{{ Session::get('status_type') }}" style="text-align: center; padding: 5px; margin-bottom: 5px;">
                <span style="font-size: 10px; font-weight: bold;">
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
                <b>Editando Nombres</b>
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
                        <h8 class="card-text">Usted está editando datos en la tabla "Nombres de servicio".</h8>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12 col-sm-12 col-12">
                            <!-- Campos para DatosVehiculo -->
                            {!! Form::model($tipoServicio, ['route' => ['tipo_servicio.update', $tipoServicio->id_servicio], 'method' => 'put']) !!}
                                <fieldset>
                                    <div class="form-group row">
                                        <label for="nombreServicio" class="col-md-4 col-form-label text-md-right">Nombre del servicio</label>
                                        <div class="col-md-6">
                                            {!! Form::UTTextOnly('nombreServicio', '', 'nombreServicio', $tipoServicio->nombreServicio, $errors, 40, true) !!}
                                        </div>
                                    </div>
                                    <div class="card-footer text-center">
                                        <div class="d-flex justify-content-between">
                                            <a type="button" href="{{ route('datosv.index') }}" class="btn btn-light">Retroceder</a>
                                            <button type="submit" class="btn btn-warning">Actualizar</button>
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



