<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\Models\DatosVehiculo;

class DatovController extends Controller
{
    public function index(Request $request)
    {
        $search = "";
        $limit = 10;

        if ($request->has('search')) {
            $search = $request->input('search');

            if (trim($search) != '') {
                $data = DatosVehiculo::where('marca', 'like', "%$search%")->get();
            } else {
                $data = DatosVehiculo::all();
            }
        } else {
            $data = DatosVehiculo::all();
        }

        $currentPage = Paginator::resolveCurrentPage() - 1;
        $perPage = $limit;
        $currentPageSearchResults = $data->slice($currentPage * $perPage, $perPage)->all();
        $data = new LengthAwarePaginator($currentPageSearchResults, count($data), $perPage);

        return view('admin.datos_vehiculo.index', ['data' => $data, 'search' => $search, 'page' => $currentPage]);
    }

    public function create()
    {
        return view('admin.datos_vehiculo.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'marca' => 'required'
        ]);

        try {
            DB::beginTransaction();

            $datosVehiculo = new DatosVehiculo([
                'marca' => $request['marca']
            ]);

            $datosVehiculo->save();

            DB::commit();
            Session::flash('status', 'Se ha agregado correctamente el nombre');
            Session::flash('status_type', 'success');
            return redirect(route('datos.index'));

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

    public function show($id)
    {
        $datoVehiculo = DatosVehiculo::findOrFail($id);
        return view('admin.datos_vehiculo.show', ['datoVehiculo' => $datoVehiculo]);
    }

    public function edit($id)
    {
        $datoVehiculo = DatosVehiculo::findOrFail($id);
        return view('admin.datos_vehiculo.edit', ['datoVehiculo' => $datoVehiculo]);
    }

    public function update(Request $request, $id)
    {
        $datoVehiculo = DatosVehiculo::findOrFail($id);

        $request->validate([
            'marca' => 'required'
        ]);

        try {
            DB::beginTransaction();

            $datoVehiculo->marca = $request['marca'];
            $datoVehiculo->tipo = $request['tipo'];

            $datoVehiculo->save();

            DB::commit();
            Session::flash('status', 'Se ha editado correctamente el registro');
            Session::flash('status_type', 'success');
            return redirect(route('datos.index'));

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

    public function delete($id)
    {
        $datoVehiculo = DatosVehiculo::findOrFail($id);
        return view('admin.datos_vehiculo.delete', ['datoVehiculo' => $datoVehiculo]);
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $datoVehiculo = DatosVehiculo::findOrFail($id);
            $datoVehiculo->delete();

            DB::commit();
            Session::flash('status', 'Se ha eliminado correctamente el registro');
            Session::flash('status_type', 'success');
            return redirect(route('datos.index'));

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

}
