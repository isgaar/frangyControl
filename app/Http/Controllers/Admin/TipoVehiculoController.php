<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\Models\TipoVehiculo;

class TipoVehiculoController extends Controller
{
    public function index(Request $request)
    {
        $search = "";
        $limit = 10;

        if ($request->has('search')) {
            $search = $request->input('search');

            if (trim($search) != '') {
                $data = TipoVehiculo::where('tipo', 'like', "%$search%")->get();
            } else {
                $data = TipoVehiculo::all();
            }
        } else {
            $data = TipoVehiculo::all();
        }

        $currentPage = Paginator::resolveCurrentPage() - 1;
        $perPage = $limit;
        $currentPageSearchResults = $data->slice($currentPage * $perPage, $perPage)->all();
        $data = new LengthAwarePaginator($currentPageSearchResults, count($data), $perPage);

        return view('admin.tipo_vehiculo.index', ['data' => $data, 'search' => $search, 'page' => $currentPage]);
    }

    public function create()
    {
        return view('admin.tipo_vehiculo.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipo' => 'required'
        ]);

        try {
            $tipoVehiculo = new TipoVehiculo([
                'tipo' => $request['tipo']
            ]);

            $tipoVehiculo->save();

            return redirect(route('tipo_vehiculo.index'))->with('status', 'Se ha agregado correctamente el tipo de vehículo')->with('status_type', 'success');
        } catch (\Illuminate\Database\QueryException $ex) {
            return back()->with('status', $ex->getMessage())->with('status_type', 'error-Query');
        } catch (\Exception $e) {
            return back()->with('status', $e->getMessage())->with('status_type', 'error');
        }
    }

    public function show($id)
    {
        $tipoVehiculo = TipoVehiculo::findOrFail($id);
        return view('tipo_vehiculo.show', ['tipoVehiculo' => $tipoVehiculo]);
    }

    public function edit($id)
    {
        $tipoVehiculo = TipoVehiculo::findOrFail($id);
        return view('tipo_vehiculo.edit', ['tipoVehiculo' => $tipoVehiculo]);
    }

    public function update(Request $request, $id)
    {
        $tipoVehiculo = TipoVehiculo::findOrFail($id);

        $request->validate([
            'tipo' => 'required'
        ]);

        try {
            $tipoVehiculo->tipo = $request['tipo'];

            $tipoVehiculo->save();

            return redirect(route('tipo_vehiculo.index'))->with('status', 'Se ha editado correctamente el tipo de vehículo')->with('status_type', 'success');
        } catch (\Illuminate\Database\QueryException $ex) {
            return back()->with('status', $ex->getMessage())->with('status_type', 'error-Query');
        } catch (\Exception $e) {
            return back()->with('status', $e->getMessage())->with('status_type', 'error');
        }
    }

    public function delete($id)
    {
        $tipoVehiculo = TipoVehiculo::findOrFail($id);
        return view('tipo_vehiculo.delete', ['tipoVehiculo' => $tipoVehiculo]);
    }

    public function destroy($id)
    {
        $tipoVehiculo = TipoVehiculo::findOrFail($id);

        try {
            $tipoVehiculo->delete();

            return redirect(route('tipo_vehiculo.index'))->with('status', 'Se ha eliminado correctamente el tipo de vehículo')->with('status_type', 'success');
        } catch (\Illuminate\Database\QueryException $ex) {
            return back()->with('status', $ex->getMessage())->with('status_type', 'error-Query');
        } catch (\Exception $e) {
            return back()->with('status', $e->getMessage())->with('status_type', 'error');
        }
    }
}