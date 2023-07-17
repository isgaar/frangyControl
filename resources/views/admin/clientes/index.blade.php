@extends('adminlte::page')

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
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-center mt-3">
                            {{ $data->appends(['search' => $search])->links() }}
                        </div>
                    </div>
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