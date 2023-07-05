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
        $ordenes = Ordenes::with('cliente')->get();
    
        if ($data->isEmpty()) {
            $message = "No hay registros de \"$search\"";
            return view('admin.ordenes.index', ['data' => $data, 'search' => $search, 'page' => $currentPage, 'message' => $message, 'ordenes' => $ordenes]);
        } else {
            return view('admin.ordenes.index', ['data' => $data, 'search' => $search, 'page' => $currentPage, 'ordenes' => $ordenes]);
        }
    }
    
    public function cliente()
{
    return $this->belongsTo(Cliente::class, 'cliente_id');
}

public function vehiculo()
{
    return $this->belongsTo(Vehiculo::class, 'vehiculo_id');
}

public function servicio()
{
    return $this->belongsTo(TipoServicio::class, 'servicio_id');
}

    public function create()
    {
        return view('admin.ordenes.create');
    }

    public function registro()
{
    $datosVehiculo = DatosVehiculo::all();
    $tiposServicio = TipoServicio::all();
    $tiposVehiculo = TipoVehiculo::all();
    $users = User::all();


    $cliente_id = Cliente::all();

    return view('admin.ordenes.registro', [
        'datosVehiculo' => $datosVehiculo,
        'tiposVehiculo' => $tiposVehiculo,
        'tiposServicio' => $tiposServicio,
        'users' => $users,
        'cliente_id' => $cliente_id
    ]);
}



public function store(Request $request)
{
    try {
        DB::beginTransaction();



        $messages = [
            'nombreCompleto.required' => 'El campo nombre completo es obligatorio.',
            'telefono.required' => 'El campo teléfono es obligatorio.',
            'correo.required' => 'El campo correo electrónico es obligatorio.',
            'correo.email' => 'El campo correo electrónico debe ser una dirección de correo válida.',
            // Agrega aquí los mensajes de error para los demás campos
        ];

        $cliente = new Cliente([
            'nombreCompleto' => $request->nombreCompleto,
            'telefono' => $request->telefono,
            'correo' => $request->correo,
            // Otros campos de cliente aquí
        ]);

        $cliente->save(); // Guarda el cliente en la tabla 'clientes'

        $ordenes = new Ordenes([
            'yearVehiculo' => $request->input('yearVehiculo'),
            'color' => $request->input('color'),
            'placas' => $request->input('placas'),
            'kilometraje' => $request->input('kilometraje'),
            'motor' => $request->input('motor'),
            'cilindros' => $request->input('cilindros'),
            'noSerievehiculo' => $request->input('noSerievehiculo'),
            'fechaEntrega' => $request->input('fechaEntrega'),
            'observacionesInt' => $request->input('observacionesInt'),
            'recomendacionesCliente' => $request->input('recomendacionesCliente'),
            'detallesOrden' => $request->input('detallesOrden'),
            'retiroRefacciones' => $request->input('retiroRefacciones'),
            'cliente_id' => $request->input('cliente_id'), // Asigna el valor del cliente_id del formulario
            'vehiculo_id' => $request->input('vehiculo_id'),
            'servicio_id' => $request->input('servicio_id'),
            'tvehiculo_id' => $request->input('tvehiculo_id'),
            'id' => $request->input('user_id'),
        ]);

        $ordenes->cliente()->associate($cliente); // Asocia el cliente a la orden
        $ordenes->save(); // Guarda la orden en la tabla 'ordenes'

        DB::commit();
        session()->flash('status', 'Se ha agregado correctamente la orden.');
        session()->flash('status_type', 'success');
        return redirect()->route('ordenes.index');
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
