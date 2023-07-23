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
@stop

@section('content')
<div class="card-body">
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-12">
            <div class="card card-primary">
                <div class="card-header bg-danger">
                    <h3 class="card-title">Perfil de nuevo empleado</h3>
                </div>

                {!! Form::open(['route' => 'users.store', 'method' => 'post', 'enctype' => 'multipart/form-data']) !!}
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Nombre del usuario</label>
                        {!! Form::text('name', null, ['class' => 'form-control', 'id' => 'name', 'oninput' =>
                        'capitalizeInput(this)']) !!}
                        @error('name')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror

                    </div>
                    <div class="form-group">
                        <label for="email">Correo electrónico</label>
                        {!! Form::email('email', null, ['class' => 'form-control', 'id' => 'email']) !!}
                        @error('email')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="float-container">
                        <div class="form-group">
                            <label for="password">Contraseña</label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" class="form-control"
                                    placeholder="Contraseña" required>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="showPasswordBtn"
                                        onclick="togglePasswordVisibility('password')">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="float-child">
                        <div class="form-group">
                            <label for="confirmPassword">Confirmar contraseña</label>
                            <div class="input-group">
                                <input type="password" name="confirmPassword" id="confirmPassword" class="form-control"
                                    placeholder="Confirmar contraseña" required>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="showConfirmPasswordBtn"
                                        onclick="togglePasswordVisibility('confirmPassword')">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="roles">Asigne un privilegio</label>
                            {!! Form::select('roles', $roles->pluck('name', 'id'), null, ['class' => 'form-control'])
                            !!}
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
                    <div class="d-flex justify-content-between">
                        <a type="button" href="{{ route('users.index') }}" class="btn btn-outline-dark">Retroceder</a>
                        <button type="submit" class="btn btn-success">Guardar</button>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    <script>
    function capitalizeInput(input) {
        var value = input.value;

        // Dividir el valor en palabras
        var words = value.split(' ');

        // Capitalizar cada palabra
        for (var i = 0; i < words.length; i++) {
            var word = words[i];

            // Convertir la primera letra a mayúscula y mantener el resto en minúscula
            var capitalizedWord = word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();

            // Reemplazar la palabra en el array
            words[i] = capitalizedWord;
        }

        // Unir las palabras nuevamente en un solo valor
        var formattedValue = words.join(' ');

        // Actualizar el valor del campo
        input.value = formattedValue;
    }
    </script>

</div>
@stop

<script>
function capitalizeInput(input) {
    var value = input.value;
    input.value = value.replace(/\b\w/g, function(l) {
        return l.toUpperCase();
    });
}
</script>

@section('js')
<script src="{{ asset('js/validatorFields.js') }}"></script>
@endsection