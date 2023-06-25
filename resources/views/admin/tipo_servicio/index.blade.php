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
                <div class="col-lg-12 col-sm-12 col-12">
                    <div class="body table-responsive">
                        <div class="row">
                            <div class="col-lg-7 col-sm-7 col-7">
                                <h5>{{$data->total()}} Registro(s) encontrado(s). Página
                                {{($data->total() ==0) ? '0' : $data->currentPage()}} de
                                {{$data->lastPage()}} Registros por página.
                                {{($data->total()==0) ? '0' : $data->perPage()}}</h5>
                            </div>
                            <div class="col-lg-5 col-sm-5 col-5">
                                <form action="{{route('datosv.index')}}" method="get">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="search"
                                        value="{{$search}}" placeholder="Buscar">
                                        <span class="input-group-text">
                                            <button class="btn btn-primary" type="submit">Buscar</button>
                                        </span>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th style="width: 10%;">ID</th>
                                <th style="width: 40%;">Nombre</th>
                                <th style="width: 20%;">Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $row)
                            <tr>
                                <td>{{ $row->id_servicio }}</td>
                                <td>{{ $row->nombreServicio }}</td>
                                <td>
                                    <a class="btn btn-outline-warning" href="{{route('datosv.edit',$row->id_servicio)}}" title="Editar empleado"><i class="fas fa-solid fa-pen"></i></a>
                                    <a class="btn btn-outline-danger" href="{{route('datosv.delete',$row->id_servicio)}}" title="Eliminar empleado"><i class="fas fa-solid fa-trash"></i></a>
                                </td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                       {{ $data->setPath(route('tipo_servicio.index'))->appends(Request::except('page'))->render('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
