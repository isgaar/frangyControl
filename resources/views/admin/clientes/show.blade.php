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
                <b>Visualizar</b> <i class="fas fa-solid fa-eye"></i>
            </ul>
        </div>
    </div>
@stop

@section('content')
    <div class="card-body">
        <div class="row">
            <div class="col-lg-12 col-sm-12 col-12">
                <div class="card card-primary">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Nombre del usuario</label>
                            {!! Form::text('name', $user->name, ['class' => 'form-control', 'placeholder' => 'Nombre del usuario', 'readonly' => true]) !!}
                        </div>
                        <div class="form-group">
                            <label for="email">Correo electrónico</label>
                            {!! Form::email('email', $user->email, ['class' => 'form-control', 'placeholder' => 'Correo electrónico', 'readonly' => true]) !!}
                        </div>
                        <div class="form-group">
                            <label for="password">Contraseña</label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" class="form-control" placeholder="Contraseña" required>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="showPasswordBtn" onclick="togglePasswordVisibility('password')">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="roles">Privilegio</label>
                            @if ($user->roles->isNotEmpty())
                                <input type="text" name="roles" class="form-control" value="{{ $user->roles->first()->name }}" readonly>
                            @else
                                <input type="text" name="roles" class="form-control" value="Sin privilegio" readonly>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-center">
            <a type="button" href="{{ route('users.index') }}" class="btn btn-outline-light btn-lg btn-block">Regresar</a>
        </div>
    </div>
@stop

@section('js')
    <script src="{{ asset('js/validatorFields.js') }}"></script>
@endsection

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



