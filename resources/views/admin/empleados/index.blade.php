@extends('adminlte::page')

@section('content_header')
@if(Session::has('status'))
<div class="col-md-12 alert-section">
    <div class="alert alert-{{Session::get('status_type')}}"
        style="text-align: center; padding: 5px; margin-bottom: 5px;">
        <span style="font-size: 20px; font-weight: bold;">
            {{Session::get('status')}}
            @php
            Session::forget('status');
            @endphp
        </span>
    </div>
</div>
@endif

@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <ul class="flex">
            <li style="display: inline-block;">
                <h1 style="display: inline-block;">Empleados</h1>
            </li>
            <li style="display: inline-block; float: right;">
                <a type="button" href="{{ route('users.create') }}" class="btn btn-info" title="Agregar empleado">
                    <i class="fas fa-user-plus"></i> Nuevo empleado
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-7 col-sm-7 col-7">
                @if ($data->isEmpty())
                <h5>No hay registros de "{{$search}}"</h5>
                @else
                <h5>{{$data->total()}} Registro(s) encontrado(s). Página
                    {{($data->total() == 0) ? '0' : $data->currentPage()}} de
                    {{$data->lastPage()}} Registros por página.
                    {{($data->total() == 0) ? '0' : $data->perPage()}}</h5>
                @endif
            </div>
            <div class="col-lg-5 col-sm-5 col-5" style="text-align: right;">
                <form action="{{route('users.index')}}" method="get">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" value="{{$search}}" placeholder="Buscar">
                        <div class="input-group-append">
                            <button class="btn btn-outline-info" type="submit">Buscar </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <br>
        {{ $data->setPath(route('users.index'))->appends(Request::except('page'))->links('pagination::bootstrap-4') }}
        <table class="table table-hover">
            <thead class="thead-light">
                <tr>
                    <th></th>
                    <th>Nombre</th>
                    <th>Correo electrónico</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $row)
                <tr>
                    <td class="text-center"><i class="fas fa-user-circle"></i></td>
                    <td>{{ $row->name }}</td>
                    <td>{{ $row->email }}</td>
                    <td>
                        <a class="btn btn-outline-dark @if($row->id === 1) disabled @endif"
                            href="{{ route('users.show', $row->id) }}" title="Detalle de usuario/empleado"><i
                                class="fas fa-solid fa-eye"></i></a>
                        <a class="btn btn-outline-dark @if($row->id === 1) disabled @endif"
                            href="{{ route('users.edit', $row->id) }}" title="Editar empleado"><i
                                class="fas fa-solid fa-pen"></i></a>
                        <a class="btn btn-outline-dark @if($row->id === 1) disabled @endif"
                            href="{{ route('users.delete', $row->id) }}" title="Eliminar empleado"><i
                                class="fas fa-solid fa-trash"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>


    </div>
</div>
@stop