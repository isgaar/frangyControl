<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventario;
use Illuminate\Http\Request;

class InventarioController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Inventario::query();

        if ($search) {
            $query->where('nombre', 'like', "%{$search}%")
                  ->orWhere('codigo_barras', 'like', "%{$search}%");
        }

        $inventarios = $query->orderBy('nombre')->paginate(10);
        
        return view('admin.inventario.index', compact('inventarios', 'search'));
    }

    public function create()
    {
        return view('admin.inventario.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo_barras' => 'nullable|string|unique:inventarios,codigo_barras|max:255',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio_venta' => 'required|numeric|min:0',
            'costo' => 'required|numeric|min:0',
            'cantidad_stock' => 'required|integer|min:0',
        ]);

        Inventario::create($request->all());

        return redirect()->route('inventario.index')->with('success', 'Producto agregado al inventario.');
    }

    public function edit(Inventario $inventario)
    {
        return view('admin.inventario.edit', compact('inventario'));
    }

    public function update(Request $request, Inventario $inventario)
    {
        $request->validate([
            'codigo_barras' => 'nullable|string|max:255|unique:inventarios,codigo_barras,' . $inventario->id_inventario . ',id_inventario',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio_venta' => 'required|numeric|min:0',
            'costo' => 'required|numeric|min:0',
            'cantidad_stock' => 'required|integer|min:0',
        ]);

        $inventario->update($request->all());

        return redirect()->route('inventario.index')->with('success', 'Producto actualizado exitosamente.');
    }

    public function destroy(Inventario $inventario)
    {
        $inventario->delete();
        return redirect()->route('inventario.index')->with('success', 'Producto eliminado del inventario.');
    }
}
