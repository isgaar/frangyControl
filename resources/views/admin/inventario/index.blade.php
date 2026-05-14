@extends('layouts.dashboard')

@section('title', 'Inventario y Almacén')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Inventario y Almacén</h1>
        <p class="text-muted">Gestiona las refacciones, productos y códigos de barras.</p>
    </div>
    <a href="{{ route('inventario.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nuevo Producto
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <form action="{{ route('inventario.index') }}" method="GET" class="d-flex w-50">
            <input type="text" name="search" class="form-control me-2" placeholder="Buscar por nombre o código de barras..." value="{{ request('search') }}">
            <button class="btn btn-outline-secondary" type="submit">Buscar</button>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Código de Barras</th>
                        <th>Nombre</th>
                        <th>Costo</th>
                        <th>Precio Público</th>
                        <th>Stock</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inventarios as $item)
                    <tr>
                        <td>
                            @if($item->codigo_barras)
                                <span class="badge bg-secondary"><i class="fas fa-barcode"></i> {{ $item->codigo_barras }}</span>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>{{ $item->nombre }}</td>
                        <td>${{ number_format($item->costo, 2) }}</td>
                        <td>${{ number_format($item->precio_venta, 2) }}</td>
                        <td>
                            @if($item->cantidad_stock <= 5)
                                <span class="badge bg-danger">{{ $item->cantidad_stock }}</span>
                            @else
                                <span class="badge bg-success">{{ $item->cantidad_stock }}</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('inventario.edit', $item->id_inventario) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('inventario.destroy', $item->id_inventario) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar producto del inventario?');">
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
                        <td colspan="6" class="text-center">No se encontraron productos en el inventario.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $inventarios->links() }}
        </div>
    </div>
</div>
@endsection
