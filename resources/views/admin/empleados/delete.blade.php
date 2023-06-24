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
                <b>Eliminar</b> <i class="fa-solid fa-user-minus"></i>
            </ul>
        </div>
        <div class="card-body">
            <div class="row">
            <div class="col-lg-12 col-sm-12 col-12">
                {!! Form::open(['route' => ['users.destroy', $user->id], 'method' => 'get']) !!}
                <fieldset>
                    <div class="card card-warning">
                        <div class="card-header">
                            <h2>¿Está seguro de eliminar el empleado?</h2>
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
                    <div class="card-footer text-center">
                        <a type="button" href="{{ route('users.index') }}" class="btn btn-primary">Regresar</a>
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </div>
                </fieldset>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

@stop
@section('js')
    <script src="{{ asset('js/validatorFields.js') }}"></script>
    <
@endsection()
