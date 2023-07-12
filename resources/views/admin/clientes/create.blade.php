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


@stop

@section('content')

<div class="card">
    <div class="card-header">
        <ul>
            <b>Agregando cliente</b> <i class="fas fa-user-plus"></i>
        </ul>
    </div>
</div>
<div class="card-body">
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-12">
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">Información Requerida</h3>
                </div>

                {!! Form::open(['route' => 'clientes.store', 'method' => 'post', 'enctype' => 'multipart/form-data'])
                !!}
                <div class="card-body">
                    <div class="form-group">
                        <label for="nombreCompleto">Nombre completo</label>
                        <input type="text" name="nombreCompleto" class="form-control" maxlength="100"
                            id="nombreCompleto" oninput="formatNameInput(this)">
                        @error('nombreCompleto')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="telefono">Teléfono</label>
                        <input type="text" name="telefono" class="form-control" id="telefono" pattern="[0-9]{0,10}"
                            onkeypress="return isNumberKey(event)" oninput="truncatePhoneNumber(this)">
                        @error('telefono')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="correo">Correo electrónico</label>
                        <input type="text" name="correo" class="form-control" maxlength="30" id="correo"
                            oninput="validateEmail(this)">
                        @error('correo')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="rfc">RFC</label>
                        <input type="text" name="rfc" class="form-control" maxlength="13" id="rfc"
                            oninput="formatRFC(event)">
                        @error('rfc')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                </div>


            </div>
        </div>
        <div class="card-footer text-center">
            <div class="d-flex justify-content-between">
                <a type="button" href="{{ route('clientes.index') }}" class="btn btn-outline-dark">Retroceder</a>
                <button type="submit" class="btn btn-info">Guardar</button>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>
@stop

<script>
function cambiarNombre(nombre) {
    let regex = /^[a-zA-ZÀ-ÿ\u00f1\u00d1][a-zA-ZÀ-ÿ\u00f1\u00d1\s]*$/g;
    return regex.exec(nombre)[0];
}

function formatNameInput(input) {
    var name = input.value;
    // Reemplazar caracteres especiales, números, y símbolos con espacios
    name = cambiarNombre(name);
    // Convertir la primera letra de cada palabra a mayúscula y las demás letras a minúscula
    name = name.replace(/(?:^|\s)\S/g, function(l) {
        return l.toUpperCase();
    });
    input.value = name;
}

function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}

function truncatePhoneNumber(input) {
    var phoneNumber = input.value;
    if (phoneNumber.length > 10) {
        phoneNumber = phoneNumber.slice(0, 10);
    }
    input.value = phoneNumber;
}

function validateEmail(input) {
    var email = input.value;
    var regex = /^[a-z0-9@]+$/;

    if (!regex.test(email)) {
        input.value = email.slice(0, -1);
    }

    if (email.split("@").length > 2) {
        input.value = email.slice(0, -1);
    }

    if (email.length > 25) {
        input.value = email.slice(0, 25);
    }
    input.value = email.toLowerCase();
}

function formatRFC(event) {
    // Obtén el campo de entrada del RFC
    const rfcInput = event.target;

    // Obtén el valor actual del campo de entrada
    let rfcValue = rfcInput.value;

    // Elimina los caracteres especiales utilizando una expresión regular
    rfcValue = rfcValue.replace(/[^\w\s]/gi, '');

    // Convierte las letras a mayúsculas
    rfcValue = rfcValue.toUpperCase();

    // Asigna el valor modificado al campo de entrada
    rfcInput.value = rfcValue;
}
</script>

@section('js')
<script src="{{ asset('js/validatorFields.js') }}"></script>
@endsection