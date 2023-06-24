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
                <b> Usuarios || Eliminar registro</b>
            </ul>
        </div>
        <div class="card-body">
            <div class="row">
                {!! Form::open(['route' => ['users.destroy', $user->id], 'method' => 'get']) !!}
                <div class="col-lg-12 col-sm-12 col-12">
                    <fieldset>
                        <div class="row">
                            <h2>¿Seguro de eliminar el registro?</h2>
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title"> Bonjorno</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        {!! Form::UTTextOnly('name', 'Nombre del usuario', 'Nombre del usuario', $user->name, $errors, 40, true, true) !!}
                                    </div>
                                    <div class="form-group">
                                        {!! Form::UTEmail('email', 'Correo electrónico', 'Correo electrónico', $user->email, $errors, 50, true, true) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a type="button" href="{{ route('users.index') }}" class="btn btn-primary">Regresar</a>
                            <button type="submit" class="btn btn-danger">Eliminar</button>
                        </div>
                    </fieldset>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@stop
@section('js')
    <script src="{{ asset('js/validatorFields.js') }}"></script>
    <
@endsection()
