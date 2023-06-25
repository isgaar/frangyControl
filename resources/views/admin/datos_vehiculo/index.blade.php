@extends('adminlte::page')
@section('content_header')
    
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
    <div class="card">
        <div class="card-header">
        <ul>
            <li style="display: inline-block;">
                <h1 style="display: inline-block;">Datos generales</h1>
            </li>
            <li style="display: inline-block; float: right;">
                <a type="button" href="{{ route('datosv.create') }}" class="btn btn-dark" title="Agregar datos para que aparezcan en el formulario">
                    <i class="fas fa-puzzle-piece"></i> Nuevos datos
                </a>
            </li>
        </ul>

        </div>
        <div class="card-body">
            <div class="row">
    <div class="col-md-4">
        <h5 class="text-center">Marcas de vehículos</h5>
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
                        <a class="btn btn-outline-warning" href="{{ route('datosv.edit', $row->id_vehiculo) }}" title="Editar empleado"><i class="fas fa-solid fa-pen"></i></a>
                        <a class="btn btn-outline-danger" href="{{ route('datosv.delete', $row->id_vehiculo) }}" title="Eliminar empleado"><i class="fas fa-solid fa-trash"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="col-md-4">
        <h5 class="text-center">Tipos de vehículos</h5>
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
                        <a class="btn btn-outline-warning" href="{{ route('datosv.edit', $row->id_tvehiculo) }}" title="Editar empleado"><i class="fas fa-solid fa-pen"></i></a>
                        <a class="btn btn-outline-danger" href="{{ route('datosv.delete', $row->id_tvehiculo) }}" title="Eliminar empleado"><i class="fas fa-solid fa-trash"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="col-md-4">
        <h5 class="text-center">Nombres de servicios</h5>
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
                        <a class="btn btn-outline-warning" href="{{ route('datosv.edit', $row->id_servicio) }}" title="Editar empleado"><i class="fas fa-solid fa-pen"></i></a>
                        <a class="btn btn-outline-danger" href="{{ route('datosv.delete', $row->id_servicio) }}" title="Eliminar empleado"><i class="fas fa-solid fa-trash"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop
