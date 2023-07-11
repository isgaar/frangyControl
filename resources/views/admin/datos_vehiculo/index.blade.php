@extends('adminlte::page')
@section('content_header')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>



    <style>
        .flex{
            display: flex;
        }
    </style>
@if(Session::has('status'))
    <div class="col-md-12 alert-section">
        <div class="alert alert-{{Session::get('status_type')}}" style="text-align: center; padding: 5px; margin-bottom: 5px;">
            <span style="font-size: 20px; font-weight: bold;">
                {{Session::get('status')}}
                @php
                    Session::forget('status');
                @endphp
            </span>
        </div>
    </div>
@endif

@section('content')
    <div class="card">
        <div class="card-header">
        <ul>
            <li style="display: inline-block;">
                <h1 style="display: inline-block;">Datos generales</h1>
            </li>
            <li style="display: inline-block; float: right;">
                <a type="button" href="{{ route('datosv.create') }}" class="btn btn-info" title="Agregar datos de manera general en las distintas tablas">
                    <i class="fas fa-save"></i> Guardado general
                </a>
            </li>
        </ul>

        </div>
        <div class="card-body">
            <div class="row">
    <div class="col-md-4">
        <div style="display: flex; align-items: center;">
            <h5 style="flex: 1;">Marcas de vehículos</h5>
            <li style="list-style: none;">
                    <a type="button" href="{{ route('datosv.createunique') }}" class="btn btn-info" title="Agregar datos por separado">
                        <i class="fas fa-plus"></i>
                    </a>
            </li>
        </div>

        <table class="table table-hover">
            <thead>
                <tr>
                    <th style="width: 40%;">Marca</th>
                    <th style="width: 20%;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dataVehiculos as $row)
                <tr>
                    <td>{{ $row->marca }}</td>
                    <td>
                        <a class="btn btn-outline-dark" href="{{ route('datosv.edit', $row->id_vehiculo) }}" title="Editar marca"><i class="fas fa-solid fa-pen"></i></a>
                        <a class="btn btn-outline-dark" href="{{ route('datosv.delete', $row->id_vehiculo) }}" title="Eliminar marca"><i class="fas fa-solid fa-trash"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="col-md-4">
        <div style="display: flex; align-items: center;">
            <h5 style="flex: 1;">Tipos de vehículos</h5>
            <li style="list-style: none;">
                    <a type="button" href="{{ route('tipo_vehiculo.create') }}" class="btn btn-info" title="Agregar datos por separado">
                        <i class="fas fa-plus"></i>
                    </a>
            </li>
        </div>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th style="width: 40%;">Tipo</th>
                    <th style="width: 20%;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dataTiposVehiculos as $row)
                <tr>
                    <td>{{ $row->tipo }}</td>
                    <td>
                        <a class="btn btn-outline-dark" href="{{ route('tipo_vehiculo.edit', $row->id_tvehiculo) }}" title="Editar tipo"><i class="fas fa-solid fa-pen"></i></a>
                        <a class="btn btn-outline-dark" href="{{ route('tipo_vehiculo.delete', $row->id_tvehiculo) }}" title="Eliminar tipo"><i class="fas fa-solid fa-trash"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="col-md-4">
    <div style="display: flex; align-items: center;">
            <h5 style="flex: 1;">Nombres de servicios</h5>
            <li style="list-style: none;">
                    <a type="button" href="{{ route('tipo_servicio.create') }}" class="btn btn-info" title="Agregar datos por separado">
                        <i class="fas fa-plus"></i>
                    </a>
            </li>
        </div>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th style="width: 40%;">Nombre</th>
                    <th style="width: 20%;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dataServicios as $row)
                <tr>
                    <td>{{ $row->nombreServicio }}</td>
                    <td>
                        <a class="btn btn-outline-dark" href="{{ route('tipo_servicio.edit', $row->id_servicio) }}" title="Editar nombre"><i class="fas fa-solid fa-pen"></i></a>
                        <a class="btn btn-outline-dark" href="{{ route('tipo_servicio.delete', $row->id_servicio) }}" title="Eliminar nombre"><i class="fas fa-solid fa-trash"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop
