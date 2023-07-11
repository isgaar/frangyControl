@extends('adminlte::page')

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

    
@stop

@section('content')
<div class="card">
        <div class="card-header">
            <h4 class="card-title">
                <b>Editando cliente</b> <i class="fas fa-user-pen"></i>
            </h4>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-12 col-sm-12 col-12">
                <div class="card card-secondary">
                    {!! Form::model($cliente, ['route' => ['clientes.update', $cliente->id_cliente], 'method' => 'put']) !!}
                    <div class="card-body">
                        <div class="form-group">
                            <label for="nombreCompleto">Nombre completo</label>
                            {!! Form::text('nombreCompleto', null, ['class' => 'form-control', 'id_cliente' => 'nombreCompleto', 'oninput' => 'formatNameInput(this)']) !!}
                            @error('nombreCompleto')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="telefono">Teléfono</label>
                            {!! Form::text('telefono', null, ['class' => 'form-control', 'id_cliente' => 'telefono', 'oninput' => 'truncatePhoneNumber(this)']) !!}
                            @error('telefono')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="correo">Correo electrónico</label>
                            {!! Form::text('correo', null, ['class' => 'form-control', 'id_cliente' => 'correo', 'oninput' => 'validateEmail(this)']) !!}
                            @error('correo')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        
                    </div>

                    <div class="card-footer text-center">
                        <div class="d-flex justify-content-between">
                            <a type="button" href="{{ route('clientes.index') }}" class="btn btn-outline-dark">Retroceder</a>
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
    function cambiarNombre(nombre) {
  let regex = /^[a-zA-ZÀ-ÿ\u00f1\u00d1][a-zA-ZÀ-ÿ\u00f1\u00d1\s]*$/g;
  return regex.exec(nombre)[0];
}

function formatNameInput(input) {
  var name = input.value;
  // Reemplazar caracteres especiales, números, y símbolos con espacios
  name = cambiarNombre(name);
  // Convertir la primera letra de cada palabra a mayúscula y las demás letras a minúscula
  name = name.replace(/(?:^|\s)\S/g, function(l) { return l.toUpperCase(); });
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

</script>

@section('js')
    <script src="{{ asset('js/validatorFields.js') }}"></script>
@endsection


