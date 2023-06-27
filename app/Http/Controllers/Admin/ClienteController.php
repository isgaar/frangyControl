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

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
    $search = "";
    $limit = 10;

    if ($request->has('search')) {
        $search = $request->input('search');

        if (trim($search) != '') {
            $data = Cliente::where('nombreCompleto', 'like', "%$search%")
                ->orWhere('telefono', 'like', "%$search%")
                ->orWhere('correo', 'like', "%$search%")
                ->get();
        } else {
            $data = Cliente::all();
        }
    } else {
        $data = Cliente::all();
    }

    $currentPage = LengthAwarePaginator::resolveCurrentPage() - 1;
    $perPage = $limit;
    $currentPageSearchResults = $data->slice($currentPage * $perPage, $perPage)->all();
    $data = new LengthAwarePaginator($currentPageSearchResults, count($data), $perPage);

    return view('admin.clientes.index', ['data' => $data, 'search' => $search, 'page' => $currentPage]);
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
            'nombreCompleto' => 'required',
            'telefono' => 'required',
            'correo' => 'required'
        ]);
    
        try {
            DB::beginTransaction();
    
            $cliente = Cliente::create([
                'nombreCompleto' => $request->input('nombreCompleto'),
                'telefono' => $request->input('telefono'),
                'correo' => $request->input('correo')
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
            'nombreCompleto' => 'required',
            'telefono' => 'required',
            'correo' => 'required'
        ]);
    
        try {
            $cliente->nombreCompleto = $request->input('nombreCompleto');
            $cliente->telefono = $request->input('telefono');
            $cliente->correo = $request->input('correo');
    
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
