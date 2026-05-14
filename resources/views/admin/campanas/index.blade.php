@extends('layouts.dashboard')

@section('title', 'Campañas Promocionales')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Campañas y Ofertas</h1>
        <p class="text-muted">Administra el envío automático de promociones y recordatorios por WhatsApp.</p>
    </div>
    <a href="{{ route('campanas.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nueva Campaña
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Programación</th>
                        <th>Interés / Filtro</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($campanas as $campana)
                    <tr>
                        <td>{{ $campana->id_campana }}</td>
                        <td>{{ $campana->nombre }}</td>
                        <td>{{ $campana->fecha_programada->format('d/m/Y H:i') }}</td>
                        <td>{{ $campana->servicio ? $campana->servicio->nombreServicio : 'Todos los clientes' }}</td>
                        <td>
                            @if($campana->estado === 'pendiente')
                                <span class="badge bg-warning text-dark">Pendiente</span>
                            @elseif($campana->estado === 'en_progreso')
                                <span class="badge bg-info">En Progreso</span>
                            @else
                                <span class="badge bg-success">Completada</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('campanas.edit', $campana->id_campana) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('campanas.destroy', $campana->id_campana) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar esta campaña?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No hay campañas registradas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $campanas->links() }}
        </div>
    </div>
</div>
@endsection
