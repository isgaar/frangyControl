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
            DB::beginTransaction();
            $tipoVehiculo = new TipoVehiculo([
                'tipo' => $request['tipo']
            ]);

            $tipoVehiculo->save();

            DB::commit();
            Session::flash('status', 'Se ha agregado correctamente el tipo de vehiculo');
            Session::flash('status_type', 'success');
            return redirect(route('datosv.index'));

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

    public function edit($id_tvehiculo)
    {
        $tipoVehiculo = TipoVehiculo::findOrFail($id_tvehiculo);
        return view('admin.tipo_vehiculo.edit', ['tipoVehiculo' => $tipoVehiculo]);
    }

    public function update(Request $request, $id_tvehiculo)
    {
        $tipoVehiculo = TipoVehiculo::findOrFail($id_tvehiculo);

        $request->validate([
            'tipo' => 'required'
        ]);

        try {
            $tipoVehiculo->tipo = $request['tipo'];

            $tipoVehiculo->save();

            return redirect(route('datosv.index'))->with('status', 'Se ha editado correctamente el tipo de vehículo')->with('status_type', 'success');
        } catch (\Illuminate\Database\QueryException $ex) {
            return back()->with('status', $ex->getMessage())->with('status_type', 'error-Query');
        } catch (\Exception $e) {
            return back()->with('status', $e->getMessage())->with('status_type', 'error');
        }
    }

    public function delete($id_tvehiculo)
    {
        $tipoVehiculo = TipoVehiculo::findOrFail($id_tvehiculo);
        return view('admin.tipo_vehiculo.delete', ['tipoVehiculo' => $tipoVehiculo]);
    }

    public function destroy($id_tvehiculo)
    {
        $tipoVehiculo = TipoVehiculo::findOrFail($id_tvehiculo);

        try {
            $tipoVehiculo->delete();

            return redirect(route('datosv.index'))->with('status', 'Se ha eliminado correctamente el tipo de vehículo')->with('status_type', 'warning');
        } catch (\Illuminate\Database\QueryException $ex) {
            return back()->with('status', $ex->getMessage())->with('status_type', 'error-Query');
        } catch (\Exception $e) {
            return back()->with('status', $e->getMessage())->with('status_type', 'error');
        }
    }
}
