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
use App\Models\TipoServicio;
use App\Models\TipoVehiculo;


class DatovController extends Controller
{
    public function index()
    {
        // $search = "";
        // $limit = 10;

        // if ($request->has('search')) {
        //     $search = $request->input('search');

        //     if (trim($search) != '') {
        //         $data = DatosVehiculo::where('marca', 'like', "%$search%")->get();
        //     } else {
        //         $data = DatosVehiculo::all();
        //     }
        // } else {
        //     $data = DatosVehiculo::all();
        // }

        // $currentPage = Paginator::resolveCurrentPage() - 1;
        // $perPage = $limit;
        // $currentPageSearchResults = $data->slice($currentPage * $perPage, $perPage)->all();
        // $data = new LengthAwarePaginator($currentPageSearchResults, count($data), $perPage);

        // return view('admin.datos_vehiculo.index', ['data' => $data, 'search' => $search, 'page' => $currentPage]);
    
        $dataVehiculos = DatosVehiculo::all(); // Retrieve all data from the Vehiculo model
        $dataServicios = TipoServicio::all();
        $dataTiposVehiculos = TipoVehiculo::all();

        return view('admin.datos_vehiculo.index', compact('dataVehiculos', 'dataServicios', 'dataTiposVehiculos'));
   
    }

    public function create()
    {
        return view('admin.datos_vehiculo.create');
    }
    
    public function createunique()
    {
        return view('admin.datos_vehiculo.createunique');
    }

    public function storeunique(Request $request)
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
            Session::flash('status', 'Se ha guardado correctamente la marca');
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

            $tipoServicio = new TipoServicio([
                
                'nombreServicio' => $request['nombre_servicio']
            ]);

            $tipoServicio->save();

            $tipoVehiculo = new TipoVehiculo([
                'tipo' => $request['tipo']
            ]);

            $tipoVehiculo->save();

            DB::commit();
            Session::flash('status', 'Se han cargado correctamente los datos a las tablas');
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

    public function show($id)
    {
        $datoVehiculo = DatosVehiculo::findOrFail($id);
        return view('admin.datos_vehiculo.show', ['datoVehiculo' => $datoVehiculo]);
    }

    public function edit($id_vehiculo)
    {
        $datoVehiculo = DatosVehiculo::findOrFail($id_vehiculo);
        return view('admin.datos_vehiculo.edit', ['datoVehiculo' => $datoVehiculo]);
    }

    public function update(Request $request, $id_vehiculo)
    {
        $datoVehiculo = DatosVehiculo::findOrFail($id_vehiculo);

        $request->validate([
            'marca' => 'required'
        ]);

        try {
            DB::beginTransaction();

            $datoVehiculo->marca = $request['marca'];

            $datoVehiculo->save();

            DB::commit();
            Session::flash('status', 'Se ha editado correctamente la marca');
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

    public function delete($id_vehiculo)
    {
        $datoVehiculo = DatosVehiculo::findOrFail($id_vehiculo);
        return view('admin.datos_vehiculo.delete', ['datoVehiculo' => $datoVehiculo]);
    }

    public function destroy($id_vehiculo)
    {
        try {
            DB::beginTransaction();

            $datoVehiculo = DatosVehiculo::findOrFail($id_vehiculo);
            $datoVehiculo->delete();

            DB::commit();
            Session::flash('status', 'Se ha eliminado correctamente el nombre', 1);
            Session::flash('status_type', 'warning', 1);
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

}
