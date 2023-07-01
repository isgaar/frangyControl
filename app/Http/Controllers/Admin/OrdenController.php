<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\Models\Ordenes;
use App\Models\Cliente;
use App\Models\DatosVehiculo;
use App\Models\TipoVehiculo;
use App\Models\TipoServicio;
use App\Models\User;

class OrdenController extends Controller
{
    public function index(Request $request)
    {
        $search = "";
        $limit = 5;
        
        if ($request->has('search')) {
            $search = $request->input('search');
        
            if (trim($search) != '') {
                $data = Ordenes::where('placas', 'like', "%$search%")
                    ->orWhereHas('cliente', function ($query) use ($search) {
                        $query->where('nombreCompleto', 'like', "%$search%");
                    })->orWhereHas('servicio', function ($query) use ($search) {
                        $query->where('nombreServicio', 'like', "%$search%");
                    })->get();
            } else {
                $data = Ordenes::all();
            }
        } else {
            $data = Ordenes::all();
        }
        
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage() - 1;
        $perPage = $limit;
        $currentPageSearchResults = $data->slice($currentPage * $perPage, $perPage)->all();
        $data = new \Illuminate\Pagination\LengthAwarePaginator($currentPageSearchResults, count($data), $perPage);
        

        if ($data->isEmpty()) {
            $message = "No hay registros de \"$search\"";
            return view('admin.ordenes.index', ['data' => $data, 'search' => $search, 'page' => $currentPage, 'message' => $message]);
        } else {
            return view('admin.ordenes.index', ['data' => $data, 'search' => $search, 'page' => $currentPage]);
        }
    }
    public function create()
    {
        return view('admin.ordenes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required',
            'vehiculo_id' => 'required',
            'servicio_id' => 'required',
            'tvehiculo_id' => 'required',
            'id' => 'required',
            'nombreCompleto' => 'required',
            'telefono' => 'required',
            'correo' => 'required',
            'marca' => 'required',
            'tipo' => 'required',
            'yearVehiculo' => 'required',
            'color' => 'required',
            'placas' => 'required',
            'kilometraje' => 'required',
            'motor' => 'required',
            'cilindros' => 'required',
            'noSerievehiculo' => 'required',
            'fechaRegistro' => 'required',
        ]);

        try {
            DB::beginTransaction();
            
            $orden = new Orden();
            $orden->cliente_id = $request->cliente_id;
            $orden->vehiculo_id = $request->vehiculo_id;
            $orden->servicio_id = $request->servicio_id;
            $orden->tvehiculo_id = $request->tvehiculo_id;
            $orden->id = $request->id;
            $orden->nombreCompleto = $request->nombreCompleto;
            $orden->telefono = $request->telefono;
            $orden->correo = $request->correo;
            $orden->marca = $request->marca;
            $orden->tipo = $request->tipo;
            $orden->yearVehiculo = $request->yearVehiculo;
            $orden->color = $request->color;
            $orden->placas = $request->placas;
            $orden->kilometraje = $request->kilometraje;
            $orden->motor = $request->motor;
            $orden->cilindros = $request->cilindros;
            $orden->noSerievehiculo = $request->noSerievehiculo;
            $orden->fechaRegistro = $request->fechaRegistro;
            $orden->save();
    
            DB::commit();
            session()->flash('status', 'Se ha agregado correctamente la orden.');
            session()->flash('status_type', 'success');
            return redirect()->route('ordenes.index');
    
        } catch (\Illuminate\Database\QueryException $ex) {
            DB::rollBack();
            session()->flash('status', 'Error en la consulta: ' . $ex->getMessage());
            session()->flash('status_type', 'error-Query');
            return back();
    
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('status', 'Error: ' . $e->getMessage());
            session()->flash('status_type', 'error');
            return back();
        }
    }

    public function clienteList(Request $request)
    {
        $clientes = Cliente::select('id_cliente', 'nombreCompleto')->get();

        $clientes->prepend(['id_cliente' => '', 'nombreCompleto' => '--seleccione cliente--']);
        return response()->json($clientes);
    }

    public function marcaList(Request $request)
    {
        $marca = DatosVehiculo::select('id_vehiculo', 'marca')->get();

        $marca->prepend(['id_vehiculo' => '', 'marca' => '--seleccione una marca--']);

        return response()->json($marca);
    }

    public function tipovList(Request $request)
    {
        $tiposVehiculo = TipoVehiculo::select('id_tvehiculo', 'tipo')->get();
        $tiposVehiculo->prepend(['id_vehiculo' => '', 'tipo' => '--seleccione un tipo--']);

        return response()->json($tiposVehiculo);
    }

    public function tiposList(Request $request)
    {
        $tiposServicio = TipoServicio::select('id_servicio', 'nombreServicio')->get();
        $tiposServicio->prepend(['id_servicio' => '', 'nombreServicio' => '--seleccione el servicio--']);

        return response()->json($tiposServicio);
    }

    public function userList(Request $request)
    {
        $user = User::select('id', 'name')->get();
        $user->prepend(['id' => '', 'name' => '--seleccione el empleado--']);

        return response()->json($user);
    }

    

}
