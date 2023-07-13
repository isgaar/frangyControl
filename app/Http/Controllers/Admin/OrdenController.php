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
use App\Models\Fotografia;
use Illuminate\Support\Facades\Storage;
use PDF;
use Carbon\Carbon;


class OrdenController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $limit = $request->input('limit', 5);
        $order = $request->input('order', 'desc');

        $query = Ordenes::query()->with('cliente');

        if (trim($search) != '') {
            $query->where(function ($query) use ($search) {
                $query->where('id_ordenes', 'like', "%$search%")
                    ->orWhereHas('cliente', function ($query) use ($search) {
                        $query->where('nombreCompleto', 'like', "%$search%");
                    })
                    ->orWhereHas('vehiculo', function ($query) use ($search) {
                        $query->where('marca', 'like', "%$search%");
                    })
                    ->orWhere('placas', 'like', "%$search%")
                    ->orWhereHas('servicio', function ($query) use ($search) {
                        $query->where('nombreServicio', 'like', "%$search%");
                    })
                    ->orWhereHas('user', function ($query) use ($search) {
                        $query->where('name', 'like', "%$search%");
                    });
            });
        }



        $ordenes = $query->orderBy('id_ordenes', $order)->paginate($limit);

        if ($ordenes->isEmpty()) {
            $message = "No hay registros de \"$search\"";
            return view('admin.ordenes.index', [
                'ordenes' => $ordenes,
                'search' => $search,
                'limit' => $limit,
                'order' => $order,
            ]);

        } else {
            return view('admin.ordenes.index', [
                'ordenes' => $ordenes,
                'search' => $search,
                'limit' => $limit,
                'order' => $order,
            ]);

        }
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
                'rfc.required' => 'El campo correo electrónico debe ser una dirección de correo válida.',
                // Agrega aquí los mensajes de error para los demás campos
            ];

            $cliente = new Cliente([
                'nombreCompleto' => $request->nombreCompleto,
                'telefono' => $request->telefono,
                'correo' => $request->correo,
                'rfc' => $request->rfc,

                $request->validate([
                    'nombreCompleto' => 'required|unique:clientes,nombreCompleto',
                    'telefono' => 'required|unique:clientes,telefono',
                    'correo' => 'required|unique:clientes,correo',
                    'rfc' => 'required|unique:clientes,rfc',
                ], [
                    'nombreCompleto.required' => 'El campo Nombre es requerido.',
                    'nombreCompleto.unique' => 'Ya existe un cliente con este nombre.',
                    'telefono.required' => 'El campo Teléfono es requerido.',
                    'telefono.unique' => 'Ya existe un cliente con este teléfono.',
                    'correo.required' => 'El campo Correo electrónico es requerido.',
                    'correo.unique' => 'Ya existe un cliente con este correo electrónico.',
                    'rfc.unique' => 'Ya existe un cliente con este rfc.',
                ])
            ]);

            $cliente->save(); // Guarda el cliente en la tabla 'clientes'

            $ordenes = new Ordenes([
                'yearVehiculo' => $request->input('yearVehiculo'),
                'color' => $request->input('color'),
                'placas' => $request->input('placas'),
                'kilometraje' => $request->input('kilometraje'),
                'motor' => $request->input('motor'),
                'status' => $request->input('status'),
                'modelo' => $request->input('modelo'),
                'cilindros' => $request->input('cilindros'),
                'noSerievehiculo' => $request->input('noSerievehiculo'),
                'fechaEntrega' => $request->input('fechaEntrega'),
                'observacionesInt' => $request->input('observacionesInt'),
                'recomendacionesCliente' => $request->input('recomendacionesCliente'),
                'detallesOrden' => $request->input('detallesOrden'),
                'retiroRefacciones' => $request->input('retiroRefacciones'),
                'cliente_id' => $request->input('cliente_id'),
                // Asigna el valor del cliente_id del formulario
                'vehiculo_id' => $request->input('vehiculo_id'),
                'servicio_id' => $request->input('servicio_id'),
                'tvehiculo_id' => $request->input('tvehiculo_id'),
                'id' => $request->input('user_id'),

                $request->validate([
                    'yearVehiculo' => 'required',
                    'color' => 'required',
                    'placas' => 'required',
                    'kilometraje' => 'required',
                    'motor' => 'required',
                    'status' => 'required',
                    'modelo' => 'required',
                    'cilindros' => 'required',
                    'noSerievehiculo' => 'required',
                    'fechaEntrega' => 'required',
                    'observacionesInt' => 'required',
                    'recomendacionesCliente' => 'required',
                    'detallesOrden' => 'required',
                    'retiroRefacciones' => 'required',
                    'cliente_id' => 'required',
                    // Asigna el valor del cliente_id del formulario
                    'vehiculo_id' => 'required',
                    'servicio_id' => 'required',
                    'tvehiculo_id' => 'required',
                ], [
                    'yearVehiculo.required' => 'El campo Año del Vehículo es requerido.',
                    'color.required' => 'El campo Color es requerido.',
                    'placas.required' => 'El campo Placas es requerido.',
                    'kilometraje.required' => 'El campo Kilometraje es requerido.',
                    'motor.required' => 'El campo Motor es requerido.',
                    'modelo.required' => 'El campo Modelo es requerido.',
                    'cilindros.required' => 'El campo Cilindros es requerido.',
                    'noSerievehiculo.required' => 'El campo Número de Serie del Vehículo es requerido.',
                    'fechaEntrega.required' => 'El campo Fecha de Entrega es requerido.',
                    'observacionesInt.required' => 'El campo Observaciones Internas es requerido.',
                    'recomendacionesCliente.required' => 'El campo Recomendaciones del Cliente es requerido.',
                    'detallesOrden.required' => 'El campo Detalles de la Orden es requerido.',
                    'retiroRefacciones.required' => 'El campo Retiro de Refacciones es requerido.',
                    'status.required' => 'Seleccione un estado de orden.',
                    'cliente_id.required' => 'Seleccione el cliente porfavor.',
                    'vehiculo_id.required' => 'Porfavor, seleccione una marca.',
                    'servicio_id.required' => 'Porfavor, seleccione un servicio.',
                    'tvehiculo_id.required' => 'Porfavor, seleccione el tipo de vehiculo.',
                ])

            ]);

            $ordenes->cliente()->associate($cliente); // Asocia el cliente a la orden
            $ordenes->save(); // Guarda la orden en la tabla 'ordenes'


            if ($request->hasFile('photos')) {
                $photos = $request->file('photos');

                foreach ($photos as $photo) {
                    $destinoRuta = 'storage/app/images/photos/';

                    // Validar la imagen
                    $validatedData = $request->validate([
                        'photos.*' => 'required|image|mimes:png,jpg,jpeg|max:2048'
                    ]);

                    try {
                        $filename = time() . '-' . $photo->getClientOriginalName();
                        $uploadSuccess = $photo->move($destinoRuta, $filename);
                        $ruta = $destinoRuta . $filename;

                        // Crear una nueva instancia de Fotografia
                        $fotografia = new Fotografia();
                        $fotografia->ruta = $ruta;
                        $fotografia->ordenes_id = $ordenes->id;

                        // Guardar la fotografia
                        $fotografia->save();
                    } catch (\Exception $e) {
                        // Capturar y manejar la excepción
                        dd($e->getMessage());
                    }
                }
            }

            // Capturar imágenes desde la cámara
            if ($request->has('capturedPhotos')) {
                $capturedPhotos = $request->input('capturedPhotos');

                foreach ($capturedPhotos as $capturedPhoto) {
                    $destinoRuta = 'images/photos/';

                    // Validar la imagen
                    $validatedData = $request->validate([
                        'capturedPhotos.*' => 'required|string' // Ajusta la validación según tus necesidades
                    ]);

                    try {
                        $filename = time() . '-' . Str::random(10) . '.jpg'; // Nombre de archivo aleatorio
                        $photoData = base64_decode($capturedPhoto);
                        $uploadSuccess = file_put_contents($destinoRuta . $filename, $photoData);
                        $ruta = $destinoRuta . $filename;

                        // Crear una nueva instancia de Fotografia
                        $fotografia = new Fotografia();
                        $fotografia->ruta = $ruta;
                        $fotografia->ordenes_id = $ordenes->id;

                        // Guardar la fotografia
                        $fotografia->save();
                    } catch (\Exception $e) {
                        // Capturar y manejar la excepción
                        dd($e->getMessage());
                    }
                }
            }


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

    public function asigne()
    {
        $datosVehiculo = DatosVehiculo::all();
        $tiposServicio = TipoServicio::all();
        $tiposVehiculo = TipoVehiculo::all();
        $users = User::all();
        $cliente = Cliente::all();

        return view('admin.ordenes.asigne', [
            'datosVehiculo' => $datosVehiculo,
            'tiposVehiculo' => $tiposVehiculo,
            'tiposServicio' => $tiposServicio,
            'users' => $users,
            'cliente' => $cliente
        ]);
    }

    public function store2(Request $request)
    {
        try {
            DB::beginTransaction();

            $ordenes = new Ordenes([
                'yearVehiculo' => $request->input('yearVehiculo'),
                'color' => $request->input('color'),
                'placas' => $request->input('placas'),
                'kilometraje' => $request->input('kilometraje'),
                'motor' => $request->input('motor'),
                'status' => $request->input('status'),
                'modelo' => $request->input('modelo'),
                'cilindros' => $request->input('cilindros'),
                'noSerievehiculo' => $request->input('noSerievehiculo'),
                'fechaEntrega' => $request->input('fechaEntrega'),
                'observacionesInt' => $request->input('observacionesInt'),
                'recomendacionesCliente' => $request->input('recomendacionesCliente'),
                'detallesOrden' => $request->input('detallesOrden'),
                'retiroRefacciones' => $request->input('retiroRefacciones'),
                'cliente_id' => $request->input('cliente_id'),
                // Asigna el valor seleccionado del campo cliente_id
                'vehiculo_id' => $request->input('vehiculo_id'),
                'servicio_id' => $request->input('servicio_id'),
                'tvehiculo_id' => $request->input('tvehiculo_id'),
                'id' => $request->input('user_id'),
            ]);

            $ordenes->save(); // Guarda la orden en la tabla 'ordenes'

            // Resto del código...

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

    public function show($id_ordenes)
    {
        $orden = Ordenes::findOrFail($id_ordenes); // Obtén la orden según el ID proporcionado
        $datosVehiculo = DatosVehiculo::all();
        $tiposServicio = TipoServicio::all();
        $tiposVehiculo = TipoVehiculo::all();
        $users = User::all();
        $cliente_id = Cliente::all();

        return view('admin.ordenes.show', [
            'orden' => $orden,
            'datosVehiculo' => $datosVehiculo,
            'tiposVehiculo' => $tiposVehiculo,
            'tiposServicio' => $tiposServicio,
            'users' => $users,
            'cliente_id' => $cliente_id
        ]);
    }

    public function edit($id_ordenes)
    {
        $orden = Ordenes::findOrFail($id_ordenes); // Obtén la orden según el ID proporcionado
        $datosVehiculo = DatosVehiculo::all();
        $tiposServicio = TipoServicio::all();
        $tiposVehiculo = TipoVehiculo::all();
        $users = User::all();
        $cliente = Cliente::all(); // Obtén el cliente asociado a la orden

        return view('admin.ordenes.edit', [
            'orden' => $orden,
            'datosVehiculo' => $datosVehiculo,
            'tiposVehiculo' => $tiposVehiculo,
            'tiposServicio' => $tiposServicio,
            'users' => $users,
            'cliente' => $cliente
        ]);
    }


    public function update(Request $request, $id_ordenes)
    {
        try {
            DB::beginTransaction();

            $orden = Ordenes::findOrFail($id_ordenes); // Obtén la instancia de la orden existente

            // Actualiza los campos de la orden
            $orden->yearVehiculo = $request->input('yearVehiculo');
            $orden->color = $request->input('color');
            $orden->placas = $request->input('placas');
            $orden->kilometraje = $request->input('kilometraje');
            $orden->motor = $request->input('motor');
            $orden->modelo = $request->input('modelo');
            $orden->status = $request->input('status');
            $orden->cilindros = $request->input('cilindros');
            $orden->noSerievehiculo = $request->input('noSerievehiculo');
            $orden->fechaEntrega = $request->input('fechaEntrega');
            $orden->observacionesInt = $request->input('observacionesInt');
            $orden->recomendacionesCliente = $request->input('recomendacionesCliente');
            $orden->detallesOrden = $request->input('detallesOrden');
            $orden->retiroRefacciones = $request->input('retiroRefacciones');
            $orden->vehiculo_id = $request->input('vehiculo_id');
            $orden->servicio_id = $request->input('servicio_id');
            $orden->tvehiculo_id = $request->input('tvehiculo_id');

            // Actualiza los campos relacionados con el cliente
            $cliente = $orden->cliente;
            $cliente->nombreCompleto = $request->input('nombreCompleto');
            $cliente->telefono = $request->input('telefono');
            $cliente->correo = $request->input('correo');
            $cliente->rfc = $request->input('rfc');
            $cliente->save(); // Guarda los cambios en la tabla 'clientes'

            $orden->save(); // Guarda los cambios en la tabla 'ordenes'

            DB::commit();
            session()->flash('status', 'Se ha actualizado correctamente la orden.');
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


    public function exportToPDF($id_ordenes)
    {
        $orden = Ordenes::findOrFail($id_ordenes);
        $datosVehiculo = DatosVehiculo::all();
        $tiposServicio = TipoServicio::all();
        $tiposVehiculo = TipoVehiculo::all();
        $users = User::all();
        $cliente_id = Cliente::all();

        $html = view('admin.ordenes.export', compact('orden', 'datosVehiculo', 'tiposServicio', 'tiposVehiculo', 'users', 'cliente_id'))->render();

        $pdf = PDF::loadHTML($html);
        $pdf->setPaper('A4', 'portrait'); // Establecer tamaño de papel A4 y orientación vertical

        $filename = 'orden_' . Carbon::now()->format('Ymd_His') . '.pdf';

        return $pdf->download($filename);
    }




}