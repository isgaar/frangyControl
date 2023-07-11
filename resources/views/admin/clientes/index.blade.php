@extends('adminlte::page')

@section('content_header')

@if(Session::has('status'))
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
        <div class="d-flex justify-content-between align-items-center">
            <h1>Clientes</h1>
            <a type="button" href="{{ route('clientes.create') }}" class="btn btn-info add-new" title="Agregar cliente">
                <i class="fas fa-id-badge"></i> Nuevo cliente
            </a>
        </div>
    </div>
    <div class="card-body">

        <div class="row">
            <div class="col-lg-7 col-sm-7 col-7">
                @if ($data->isEmpty())
                <h5>No existe el cliente "{{$search}}"</h5>
                @else
                <h5>{{$data->total()}} tarjeta(s) encontrada(s).</h5>
                @endif
            </div>
            <div class="col-md-12 mb-3">
                <form action="{{ route('clientes.index') }}" method="GET">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Buscar..."
                            value="{{ $search }}">
                        <div class="input-group-append">
                            <button class="btn btn-outline-info" type="submit">Buscar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <div>
                                <label for="limit">Mostrar:</label>
                                <select name="limit" id="limit" class="form-select">
                                    <option value="10" {{ $data->perPage() == 10 ? 'selected' : '' }}>10</option>
                                    <option value="20" {{ $data->perPage() == 20 ? 'selected' : '' }}>20</option>
                                    <option value="50" {{ $data->perPage() == 50 ? 'selected' : '' }}>50</option>
                                </select>
                                <button type="submit" class="btn btn-outline-info"
                                    onclick="submitForm()">Aplicar</button>
                            </div>
                            <div>
                                <label for="sort">Ordenar por:</label>
                                <select name="sort_by" id="sort" class="form-select">
                                    <option value="nombreCompleto" {{ $sortBy == 'nombreCompleto' ? 'selected' : '' }}>
                                        Nombre</option>
                                    <option value="telefono" {{ $sortBy == 'telefono' ? 'selected' : '' }}>Teléfono
                                    </option>
                                    <option value="correo" {{ $sortBy == 'correo' ? 'selected' : '' }}>Correo</option>
                                    <option value="id_cliente" {{ $sortBy == 'id_cliente' ? 'selected' : '' }}>ID
                                    </option>
                                </select>
                                <button type="submit" class="btn btn-outline-info"
                                    onclick="submitForm()">Aplicar</button>
                            </div>

                            <!-- Resto del código -->

                        </div>
                        <div class="row">
                            @foreach($data as $cliente)
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col">
                                                <h5 class="h3 card-title mb-1">{{ ucwords($cliente->nombreCompleto) }}
                                                </h5>
                                            </div>
                                            <div class="col-auto">
                                                <i class="far fa-address-card"></i>
                                            </div>
                                        </div>
                                        <p>
                                            <span class="h3 font-weight-regular mb-0">{{ $cliente->telefono }}</span>
                                        </p>
                                        <p class="mt-3 mb-0 text-sm">
                                            <span><i class="fas fa-envelope"></i></span>
                                            <span class="h6 font-weight mb-0p">{{ $cliente->correo }}</span>
                                        </p>
                                    </div>
                                    <div class="card-footer text-center">
                                        <div class="d-flex justify-content-between">
                                            <a class="btn btn-outline-dark"
                                                href="{{ route('clientes.edit', $cliente->id_cliente) }}"
                                                title="Editar cliente">
                                                <i class="fas fa-user-edit"></i>
                                            </a>
                                            <!-- <button type="button" class="btn btn-outline-dark" title="Eliminar cliente" onclick="showDeleteConfirmation('{{ $cliente->nombreCompleto }}', '{{ route('clientes.destroy', $cliente->id_cliente) }}')">
                                <i class="fas fa-user-times"></i>
                            </button> -->
                                        </div>
                                    </div>
                                </div>

                            </div>

                            @endforeach
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            {{ $data->appends(['sort_by' => $sortBy, 'sort_order' => $sortOrder])->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="container">
            <div class="row">

            </div>
            <div class="text-right">
                {{ $data->appends(Request::except('page'))->links('pagination::bootstrap-4') }}
            </div>


        </div>



        <!-- Modal para confirmación de eliminación -->
        <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" role="dialog"
            aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <div class="card-header d-flex">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <div>
                                <h2 class="card-title">¡Advertencia!</h2>
                                <br>
                                <h8 class="card-text">Usted está eliminando un cliente.</h8>
                            </div>
                        </div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <p id="deleteConfirmationText"></p>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex justify-content-between w-100">
                            <button type="button" class="btn btn-outline-dark" data-dismiss="modal">Cancelar</button>
                            <form id="deleteForm" method="GET" action="">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger">Eliminar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
        function showDeleteConfirmation(nombreCompleto, id_cliente) {
            $('#deleteConfirmationText').text(`¿Está seguro que desea eliminar a "${nombreCompleto}"?`);
            $('#deleteForm').attr('action', id_cliente);
            $('#deleteConfirmationModal').modal('show');
        }
        </script>
        @endsection