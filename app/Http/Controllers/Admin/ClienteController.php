<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cliente;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Collection;


class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    public function index(Request $request)
    {
        $search = $request->input('search');
        $limit = $request->input('limit', 10); // Obtén el valor seleccionado del número de resultados por página
        $sortBy = $request->input('sort_by', 'id_cliente'); // Campo de ordenamiento predeterminado
        $sortOrder = $request->input('sort_order', 'asc'); // Orden predeterminado

        // Obtener los registros según el campo de búsqueda y los parámetros de ordenamiento
        $dataQuery = Cliente::query();

        if ($search) {
            $dataQuery->where('nombreCompleto', 'like', "%$search%")
                ->orWhere('telefono', 'like', "%$search%")
                ->orWhere('correo', 'like', "%$search%");
        }

        $dataQuery->orderBy($sortBy, $sortOrder);

        // Obtener una colección paginada con los registros ordenados
        $data = $dataQuery->paginate($limit);

        // Enviar los datos a la vista
        return view('admin.clientes.index', [
            'data' => $data,
            'search' => $search,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
        ]);
    }





    public function create()
    {
        return view('admin.clientes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombreCompleto' => 'required|unique:clientes,nombreCompleto',
            'telefono' => 'required|unique:clientes,telefono',
            'correo' => 'required|unique:clientes,correo',
            'rfc' => 'required|unique:clientes,correo'
        ], [
            'nombreCompleto.required' => 'El campo Nombre es requerido.',
            'nombreCompleto.unique' => 'Este nombre ya existe, porfavor ingrese uno nuevo.',
            'telefono.required' => 'El campo Teléfono es requerido.',
            'correo.required' => 'El campo Correo electrónico es requerido.',
            'rfc.required' => 'El rfc es requerido.'
        ]);


        try {
            DB::beginTransaction();

            $cliente = Cliente::create([
                'nombreCompleto' => $request->input('nombreCompleto'),
                'nombreCompleto.unique' => 'Este nombre ya existe, porfavor ingrese uno nuevo.',
                'telefono' => $request->input('telefono'),
                'correo' => $request->input('correo'),
                'rfc' => $request->input('rfc')
            ]);

            $cliente->save();

            DB::commit();

            Session::flash('status', 'Se ha agregado exitosamente el cliente');
            Session::flash('status_type', 'success');
            return redirect()->route('clientes.index');

        } catch (\Illuminate\Database\QueryException $ex) {
            DB::rollBack();
            Session::flash('status', $ex->getMessage());
            Session::flash('status_type', 'error-Query');
            return back();

        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('status', $e->getMessage());
            Session::flash('status_type', 'error');
            return back();
        }
    }


    public function show($id_cliente)
    {
        $cliente = Cliente::findOrFail($id_cliente);

        return view('admin.clientes.show', ['cliente' => $cliente]);
    }

    public function edit($id_cliente)
    {
        $cliente = Cliente::findOrFail($id_cliente);

        return view('admin.clientes.edit', ['cliente' => $cliente]);
    }


    public function update(Request $request, $id_cliente)
    {
        $cliente = Cliente::findOrFail($id_cliente);

        $request->validate([
            'nombreCompleto' => 'required|unique:clientes,nombreCompleto',
            'telefono' => 'required|unique:clientes,telefono',
            'correo' => 'required|unique:clientes,correo',
            'rfc' => 'required|unique:clientes,correo'
        ]);

        try {
            $cliente->nombreCompleto = $request->input('nombreCompleto');
            $cliente->telefono = $request->input('telefono');
            $cliente->correo = $request->input('correo');
            $cliente->rfc = $request->input('rfc');

            $cliente->save();

            return redirect(route('clientes.index'))->with('status', 'Se ha editado correctamente el cliente')->with('status_type', 'success');
        } catch (\Illuminate\Database\QueryException $ex) {
            return back()->with('status', $ex->getMessage())->with('status_type', 'error-Query');
        } catch (\Exception $e) {
            return back()->with('status', $e->getMessage())->with('status_type', 'error');
        }
    }


    public function delete($id_cliente)
    {
        $cliente = Cliente::findOrFail($id_cliente);

        return view('admin.clientes.delete', ['cliente' => $cliente]);
    }


    public function destroy($id_cliente)
    {
        $cliente = Cliente::findOrFail($id_cliente);

        // Eliminar el cliente
        try {
            $cliente->delete();
            return redirect(route('clientes.index'))->with('status', 'Se ha eliminado correctamente el cliente')->with('status_type', 'warning');
        } catch (\Illuminate\Database\QueryException $ex) {
            return back()->with('status', $ex->getMessage())->with('status_type', 'error-Query');
        } catch (\Exception $e) {
            return back()->with('status', $e->getMessage())->with('status_type', 'error');
        }
    }
}