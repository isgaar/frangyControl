<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\TipoVehiculo;

class TipoVehiculoController extends Controller
{
    public function index(Request $request)
    {
        $query = [];

        if ($request->filled('search')) {
            $query['type_search'] = $request->input('search');
        }

        return redirect(route('catalogos.index', $query) . '#tipos');
    }

    public function create()
    {
        return redirect(route('catalogos.index', ['open' => 'type']) . '#tipos');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipos.*' => 'required' // Validar todos los campos de tipo de vehículo
        ]);
    
        try {
            DB::beginTransaction();
    
            // Guardar el tipo de vehículo principal
            $tipoVehiculo = new TipoVehiculo([
                'tipo' => $request->input('tipos.0') // Obtener el primer valor del arreglo de tipos
            ]);
            $tipoVehiculo->save();
    
            // Guardar los tipos de vehículo adicionales
            $tiposAdicionales = $request->input('tipos');
            unset($tiposAdicionales[0]); // Eliminar el primer valor, ya que ya se guardó anteriormente
    
            foreach ($tiposAdicionales as $tipo) {
                $tipoVehiculoAdicional = new TipoVehiculo([
                    'tipo' => $tipo
                ]);
                $tipoVehiculoAdicional->save();
            }
    
            DB::commit();
            Session::flash('status', 'Se ha agregado correctamente el tipo de vehículo');
            Session::flash('status_type', 'success');
            return redirect(route('catalogos.index') . '#tipos');
    
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

            return redirect(route('catalogos.index') . '#tipos')->with('status', 'Se ha editado correctamente el tipo de vehículo')->with('status_type', 'success');
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

            return redirect(route('catalogos.index') . '#tipos')->with('status', 'Se ha eliminado correctamente el tipo de vehículo')->with('status_type', 'warning');
        } catch (\Illuminate\Database\QueryException $ex) {
            return back()->with('status', $ex->getMessage())->with('status_type', 'error-Query');
        } catch (\Exception $e) {
            return back()->with('status', $e->getMessage())->with('status_type', 'error');
        }
    }
}
