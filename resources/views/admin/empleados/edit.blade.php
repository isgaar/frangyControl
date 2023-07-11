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
            <h4 class="card-title">
                <b>Editando empleado</b> <i class="fas fa-user-pen"></i>
            </h4>
        </div>
    </div>
@stop

@section('content')
    <div class="card-body">
        <div class="row">
            <div class="col-lg-12 col-sm-12 col-12">
                <div class="card card-danger">
                    <div class="card-header">
                        <h3 class="card-title">Información Requerida</h3>
                    </div>
                    {!! Form::model($user, ['route' => ['users.update', $user->id], 'method' => 'put']) !!}
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Nombre del usuario</label>
                            {!! Form::text('name', $user->name, ['class' => 'form-control', 'placeholder' => 'Nombre del usuario', 'oninput' => 'capitalizeInput(this)']) !!}
                            @if ($errors->has('name'))
                                <span class="text-danger">{{ $errors->first('name') }}</span>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="email">Correo electrónico</label>
                            {!! Form::email('email', $user->email, ['class' => 'form-control', 'placeholder' => 'Correo electrónico']) !!}
                            @if ($errors->has('email'))
                                <span class="text-danger">{{ $errors->first('email') }}</span>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="password">Contraseña</label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" class="form-control" placeholder="Contraseña" required>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="showPasswordBtn" onclick="togglePasswordVisibility('password')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="roles">Actualice el privilegio</label>
                            {!! Form::select('roles', $roles->pluck('name', 'id'), null, ['class' => 'form-control']) !!}
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
                        <div class="d-flex justify-content-between">
                            <a type="button" href="{{ route('users.index') }}" class="btn btn-outline-dark">Retroceder</a>
                            <button type="submit" class="btn btn-warning">Actualizar</button>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@stop
<script>
function capitalizeInput(input) {
    var value = input.value;
    input.value = value.replace(/\b\w/g, function(l) { return l.toUpperCase(); });
}
</script>

@section('js')
    <script src="{{ asset('js/validatorFields.js') }}"></script>
@endsection


