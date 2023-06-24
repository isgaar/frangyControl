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
                <b>Editando empleado</b> <i class="fa-solid fa-user-pen"></i>
            </ul>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-12 col-sm-12 col-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Información Requerida</h3>
                        </div>
                        {!! Form::model($user, ['route' => ['users.update', $user->id], 'method' => 'put']) !!}
                        <div class="card-body">
    <div class="form-group">
        <label for="name">Nombre del usuario</label>
        {!! Form::UTTextOnly('name', '', 'Nombre del usuario', $user->name, $errors, 40, true) !!}
    </div>
    <div class="form-group">
        <label for="email">Correo electrónico</label>
        {!! Form::UTEmail('email', '', 'Correo electrónico', $user->email, $errors, 50, true) !!}
    </div>
    <div class="form-group">
        <label for="password">Contraseña</label>
        <div class="input-group">
            <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="button" id="showPasswordBtn" onclick="togglePasswordVisibility('password')">
                    <i class="fa fa-eye"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePasswordVisibility(fieldId) {
        var field = document.getElementById(fieldId);
        if (field.type === "password") {
            field.type = "text";
        } else {
            field.type = "password";
        }
    }
</script>


                        <div class="card-footer text-center">
                            <div class="float-child">
                            <a type="button" href="{{ route('users.index') }}" class="btn btn-danger">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    <script src="{{ asset('js/validatorFields.js') }}"></script>
<@endsection()
