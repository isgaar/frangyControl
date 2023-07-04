@extends('adminlte::page')

@section('content_header')
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

    <div class="card">
        <div class="card-header">
            <li style="display: inline-block;">
                    <h1 style="display: inline-block;">Órdenes</h1>
                </li>
        </div>
        <div class="card-body">
            <ul class="flex">
                
                <div class="row">
                    <div class="col-lg-7 col-sm-7 col-7" style="text-align: left;">
                        <form action="{{ route('ordenes.index') }}" method="get">
                            <div class="input-group">
                                <input type="text" class="form-control" name="search" value="{{ $search }}" placeholder="Buscar">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-light" type="submit"><i class="fas fa-search"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-5 col-sm-5 col-5" style="text-align: right;">
                        <a type="button" href="#" class="btn btn-dark" title="Crear una orden" onclick="mostrarModal()">
                            <i class="fas fa-sd-card"></i> Registrar Orden
                        </a>
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
            <button type="button" class="btn btn-outline-warning" onclick="redirigirANo()">No</button>
            <button type="button" class="btn btn-outline-success" onclick="redirigirASi()">Sí</button>
        </div>
      </div>
    </div>
  </div>
</div>

</div>
                
            </ul>

            <div class="table-responsive">
    <table class="table table-hover table-rounded table-white">
        <thead class="thead-dark">
            <tr>
                <th style="width: 20%;" class="text-center">Cliente</th>
                <th style="width: 30%;" class="text-center">Vehículo</th>
                <th style="width: 10%;" class="text-center">Placas</th>
                <th style="width: 20%;" class="text-center">Tipo de Servicio</th>
                <th style="width: 40%;" class="text-center">Operaciones</th>
            </tr>
        </thead>
        <tbody class="tbody-light">
            <tr>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <td class="text-center">
                    <a class="btn btn-outline-light" title="Visualizar"><i class="fas fa-solid fa-eye"></i></a>
                    <a class="btn btn-outline-light" title="Editar orden"><i class="fas fa-undo-alt"></i></a>
                    <a class="btn btn-outline-light" title="Exportar a pdf"><i class="fas fa-file-pdf"></i></a>
                </td>
            </tr>
        </tbody>
        <tfoot>
    <tr>
        <td colspan="5" class="text-center">
            <div class="d-flex justify-content-between w-100">
                @if ($data->isEmpty())
                    <h5>No hay registros de "{{ $search }}"</h5>
                @else
                    <h5>{{ $data->total() }} Registro(s) encontrado(s). Página
                        {{ ($data->total() == 0) ? '0' : $data->currentPage() }} de
                        {{ $data->lastPage() }} Registros por página.
                        {{ ($data->total() == 0) ? '0' : $data->perPage() }}</h5>
                @endif
            </div>
            <div class="text-right">
                {{ $data->setPath(route('ordenes.index'))->appends(Request::except('page'))->links('pagination::bootstrap-4') }}
            </div>
        </td>
    </tr>
</tfoot>


    </table>
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
}

function redirigirANo() {
  window.location.href = "{{ route('ordenes.registro') }}";
}
</script>



@stop

