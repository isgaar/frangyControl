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
                <b>Editando cliente</b> <i class="fas fa-user-pen"></i>
            </h4>
        </div>
    </div>
@stop

@section('content')
    <div class="card-body">
        <div class="row">
            <div class="col-lg-12 col-sm-12 col-12">
                <div class="card card-secondary">
                    {!! Form::model($cliente, ['route' => ['clientes.update', $cliente->id_cliente], 'method' => 'put']) !!}
                    <div class="card-body">
                        <div class="form-group">
                            <label for="nombreCompleto">Nombre completo</label>
                            {!! Form::text('nombreCompleto', null, ['class' => 'form-control', 'id_cliente' => 'nombreCompleto']) !!}
                            @error('nombreCompleto')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="telefono">Teléfono</label>
                            {!! Form::text('telefono', null, ['class' => 'form-control', 'id_cliente' => 'telefono']) !!}
                            @error('telefono')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="correo">Correo electrónico</label>
                            {!! Form::text('correo', null, ['class' => 'form-control', 'id_cliente' => 'correo']) !!}
                            @error('correo')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        
                    </div>

                    <div class="card-footer text-center">
                        <div class="d-flex justify-content-between">
                            <a type="button" href="{{ route('clientes.index') }}" class="btn btn-outline-light">Retroceder</a>
                            <button type="submit" class="btn btn-success">Guardar</button>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="{{ asset('js/validatorFields.js') }}"></script>
@endsection


