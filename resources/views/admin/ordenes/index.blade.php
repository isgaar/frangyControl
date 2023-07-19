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
@stop

@section('content')
<style>
.modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.4);
}

.modal-content {
    background-color: #fefefe;
    margin: 0 auto;
    margin-top: 10%;
    padding: 20px;
    border: 1px solid #888;
    max-width: 400px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
    border-radius: 4px;
    text-align: center;
    animation: modal-show 0.3s ease-out;
}

@keyframes modal-show {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.close {
    color: #aaa;
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}
</style>
@php
$userAgent = $_SERVER['HTTP_USER_AGENT'];
@endphp

@if (strpos($userAgent, 'Firefox') === false)
<div class="card">
    <div class="card-body" style="background-color: #8b5ccc; color: #fff;">
        <h5 class="card-title">
            <img src="https://play-lh.googleusercontent.com/l6ftn6BTu7Kfe8OdE4Itrdw5bTRVO3F_mTZH8xDa-FHO4m-lZAXmz5GxkXTMhqcF_y0"
                alt="Firefox Logo" width="30" height="30">
            Utiliza Firefox para una mejor experiencia gráfica
        </h5>
        <p class="card-text">Se recomienda utilizar el navegador Mozilla Firefox para aprovechar al máximo las
            características visuales de este sitio.</p>
    </div>
</div>
@endif

<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-lg-7 col-sm-7 col-7">
                <h1 class="mb-0">Órdenes</h1>
            </div>
            <div class="col-lg-5 col-sm-5 col-5 text-right">
                <button href="#" class="btn btn-info add-new" title="Crear una orden" onclick="mostrarModal()">
                    <i class="fas fa-sd-card"></i> Registrar Orden
                </button>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="row">


            <div class="col-lg-7 col-sm-7 col-7 text-right">
                <form action="{{ route('ordenes.index') }}" method="get">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" value="{{ $search }}"
                            placeholder="Buscar">
                        <div class="input-group-append">
                            <button class="btn btn-outline-info" type="submit"
                                title="Busque un nombre de cliente, carro, placas, servicio o encargado/a"><i
                                    class="fas fa-search"></i></button>
                        </div>
                    </div>
            </div>
            <div class="col-lg-5 col-sm-5 col-5 text-right">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">

                            <select name="limit" id="limit" class="form-control">
                                <option value="5" {{ $limit == 5 ? 'selected' : '' }}>5</option>
                                <option value="10" {{ $limit == 10 ? 'selected' : '' }}>10</option>
                                <option value="15" {{ $limit == 15 ? 'selected' : '' }}>15</option>
                            </select>
                            <label for="limit" class="placeholder-label">Limite de órdenes</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">

                            <select name="order" id="order" class="form-control">
                                <option value="asc" {{ $order == 'asc' ? 'selected' : '' }}>Primeros registros</option>
                                <option value="desc" {{ $order == 'desc' ? 'selected' : '' }}>Últimos agregados</option>
                            </select>
                            <label for="order" class="placeholder-label">Ordenar por</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <select name="status" id="status" class="form-control">
                                <option value="">Seleccione un estado</option>
                                <option value="en proceso" {{ $status == 'en proceso' ? 'selected' : '' }}>En proceso
                                </option>
                                <option value="cancelada" {{ $status == 'cancelada' ? 'selected' : '' }}>Cancelada
                                </option>
                                <option value="finalizada" {{ $status == 'finalizada' ? 'selected' : '' }}>Finalizada
                                </option>
                            </select>
                            <label for="status" class="placeholder-label">Estados</label>
                        </div>
                    </div>




                    <div class="col-md-4">
                        <div class="form-group">
                            <button type="submit" class="btn btn-outline-info"
                                title="Aplica cambios para ordenamiento">Aplicar</button>
                        </div>
                    </div>
                </div>
                </form>
            </div>
        </div>

        <div class="table-responsive mt-4">
            <table class="table table-striped mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col" class="text-center"># en sistema</th>
                        <th scope="col" class="text-center">Estado</th>
                        <th scope="col" class="text-center">Cliente</th>
                        <th scope="col" class="text-center">Tipo de Servicio</th>
                        <th scope="col" class="text-center">Vehículo</th>
                        <th scope="col" class="text-center">Placas</th>
                        <th scope="col" class="text-center">Encargado/a</th>
                        <th scope="col" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="table-group-divider">
                    @foreach ($ordenes as $row)
                    <tr>
                        <th scope="row">{{ $row->id_ordenes }}</th>
                        <td class="text-center
                            @if ($row->status == 'cancelada')
                                text-danger
                            @elseif ($row->status == 'finalizada')
                                text-success
                            @elseif ($row->status == 'en proceso')
                                text-info
                            @endif
                        ">{{ ucwords($row->status) }}</td>

                        <td class="text-center">{{ ucwords($row->cliente->nombreCompleto) }}</td>
                        <td class="text-center">{{ $row->servicio->nombreServicio }}</td>
                        <td class="text-center">{{ $row->vehiculo->marca }}</td>
                        <td class="text-center">{{ $row->placas }}</td>
                        <td class="text-center">{{ $row->user->name }}</td>
                        <td class="text-center">
                            <a class="btn btn-outline-dark" href="{{route('ordenes.show',$row->id_ordenes)}}"
                                title="Visualizar a detalle"><i class="fas fa-solid fa-eye"></i></a>
                            <a class="btn btn-outline-dark" href="{{route('ordenes.edit',$row->id_ordenes)}}"
                                title="Actualizar o editar orden"><i class="fas fa-undo-alt"></i></a>
                            @if(auth()->user()->can('admin.orden.destroy'))
                            <button class="btn btn-outline-danger" title="Eliminar orden" data-toggle="modal"
                                data-target="#deleteConfirmationModal{{ $row->id_ordenes }}">
                                <i class="fas fa-solid fa-trash"></i>
                            </button>
                            @endif
                            <a class="btn btn-outline-dark" title="Exportar a PDF"
                                href="{{ route('ordenes.export', $row->id_ordenes) }}">
                                <i class="fas fa-file-pdf"></i>
                            </a>


                        </td>
                    </tr>
                    <div class="modal fade" id="deleteConfirmationModal{{ $row->id_ordenes }}" tabindex="-1"
                        aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title">¡Advertencia!</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body">
                                    <p class="mb-0">¿Está seguro de eliminar la orden número {{ $row->id_ordenes }}?</p>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-dark"
                                        data-dismiss="modal">Cancelar</button>
                                    <form id="deleteForm{{ $row->id_ordenes }}" method="POST"
                                        action="{{ route('ordenes.destroy', $row->id_ordenes) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger">Eliminar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>




                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="text-center">
                            <div class="d-flex justify-content-between w-100">
                                @if ($ordenes->isEmpty())
                                <h5>No hay coincidencias de "{{ $search }}"</h5>
                                @else
                                <h7>{{ $ordenes->total() }} registro(s) encontrado(s). Página
                                    {{ ($ordenes->total() == 0) ? '0' : $ordenes->currentPage() }} de
                                    {{ $ordenes->lastPage() }} registros por página.
                                    Limite de: {{ ($ordenes->total() == 0) ? '0' : $ordenes->perPage() }}</h7>
                                @endif
                            </div>
                            <div class="text-right">
                                {{ $ordenes->appends(Request::except('page'))->links('pagination::bootstrap-4') }}
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>


    <!-- Modal -->
    <div id="myModal" class="modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <span class="close" onclick="cerrarModal()">&times;</span>
                <div class="modal-body">
                    <div class="user-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <p id="deleteConfirmationText">¿El cliente ya está registrado?</p>
                </div>
                <div class="modal-footer">
                    <div class="d-flex justify-content-between w-100">
                        <button type="button" class="btn btn-outline-dark" onclick="redirigirANo()">No</button>
                        <button type="button" class="btn btn-outline-dark" onclick="redirigirASi()">Sí</button>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <script>
    function mostrarModal() {
        var modal = document.getElementById("myModal");
        modal.style.display = "block";
    }

    function cerrarModal() {
        var modal = document.getElementById("myModal");
        modal.style.display = "none";
    }

    function redirigirASi() {
        window.location.href = "{{ route('ordenes.asigne') }}";
    }

    function redirigirANo() {
        window.location.href = "{{ route('ordenes.registro') }}";
    }
    </script>



    @stop