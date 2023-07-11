@extends('adminlte::page')
@include('landing.include.head')

@section('content_header')
    @if (Session::has('status'))
        <div class="col-md-12 alert-section">
            <div class="alert alert-{{ Session::get('status_type') }}" style="text-align: center; padding: 5px; margin-bottom: 5px;">
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
                <b>Eliminar</b> <i class="fa-solid fa-user-minus"></i>
            </ul>
        </div>
    </div>
@stop

@section('content')
    <div class="card-body">
        <div class="row">
            <div class="col-lg-12 col-sm-12 col-12">
                {!! Form::open(['route' => ['users.destroy', $user->id], 'method' => 'get']) !!}
                <fieldset>
                    <div class="card card-warning">
                        <div class="card-header d-flex">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <div>
                                <h2 class="card-title">¡Advertencia!</h2>
                                <br>
                                <h8 class="card-text">Usted está eliminando un empleado.</h8>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                {!! Form::label('name', 'Nombre del usuario') !!}
                                {!! Form::UTTextOnly('name', null, 'Nombre del usuario', $user->name, $errors, 40, true, true) !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('email', 'Correo electrónico') !!}
                                {!! Form::UTEmail('email', null, 'Correo electrónico', $user->email, $errors, 50, true, true) !!}
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <div class="d-flex justify-content-between">
                            <a type="button" href="{{ route('users.index') }}" class="btn btn-outline-dark">Retroceder</a>
                            <button type="submit" class="btn btn-danger">Eliminar</button>
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


