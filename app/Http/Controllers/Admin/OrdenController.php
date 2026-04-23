<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use App\Models\Ordenes;
use App\Models\Cliente;
use App\Models\DatosVehiculo;
use App\Models\TipoVehiculo;
use App\Models\TipoServicio;
use App\Models\User;
use App\Models\Fotografia;
use PDF;
use Carbon\Carbon;


class OrdenController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $limit = $request->input('limit', 5);
        $order = $request->input('order', 'desc');
        $status = $request->input('status', null);
        $query = Ordenes::query()->with(['cliente', 'vehiculo', 'servicio', 'user']);

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

        if (!is_null($status)) {
            $query->where('status', $status);
        }

        $ordenes = $query->orderBy('id_ordenes', $order)->paginate($limit)->withQueryString();

        if ($ordenes->isEmpty()) {
            $message = "No hay registros de \"$search\"";
            return view('admin.ordenes.index', [
                'ordenes' => $ordenes,
                'search' => $search,
                'limit' => $limit,
                'order' => $order,
                'status' => $status,
            ]);

        } else {
            return view('admin.ordenes.index', [
                'ordenes' => $ordenes,
                'search' => $search,
                'limit' => $limit,
                'order' => $order,
                'status' => $status,
            ]);

        }
    }

    public function verificarNombreUsuario(Request $request)
    {
        $nombreCompleto = trim((string) $request->input('nombreCompleto'));
        $exists = $nombreCompleto !== '' && Cliente::where('nombreCompleto', $nombreCompleto)->exists();

        return response()->json(['exists' => $exists]);
    }

    public function create()
    {
        return $this->registro();
    }

    public function registro()
    {
        return view('admin.ordenes.registro', $this->registroCatalogos());
    }



    public function store(Request $request)
    {
        $request->merge($this->normalizedOrderInput($request));

        $validated = $request->validate(
            $this->orderValidationRules($request->boolean('usar_cliente_existente')),
            $this->orderValidationMessages(),
            $this->orderValidationAttributes()
        );

        try {
            DB::beginTransaction();

            if (!empty($validated['usar_cliente_existente'])) {
                $cliente = Cliente::findOrFail($validated['cliente_existente_id']);
            } else {
                $cliente = Cliente::create([
                    'nombreCompleto' => trim($validated['nombreCompleto']),
                    'telefono' => $validated['telefono'],
                    'correo' => $validated['correo'],
                    'rfc' => $validated['rfc'],
                ]);

                Cache::forget('catalogos.ordenes.clientes');
            }

            $ordenes = new Ordenes([
                'yearVehiculo' => $validated['yearVehiculo'],
                'color' => trim($validated['color']),
                'placas' => $validated['placas'],
                'kilometraje' => $validated['kilometraje'],
                'motor' => $validated['motor'],
                'status' => $validated['status'],
                'modelo' => trim($validated['modelo']),
                'cilindros' => $validated['cilindros'],
                'noSerievehiculo' => $validated['noSerievehiculo'],
                'fechaEntrega' => $validated['fechaEntrega'],
                'observacionesInt' => trim($validated['observacionesInt']),
                'recomendacionesCliente' => trim($validated['recomendacionesCliente']),
                'detallesOrden' => trim($validated['detallesOrden']),
                'retiroRefacciones' => (bool) $validated['retiroRefacciones'],
                'vehiculo_id' => $validated['vehiculo_id'],
                'servicio_id' => $validated['servicio_id'],
                'tvehiculo_id' => $validated['tvehiculo_id'],
                'id' => $validated['user_id'],
            ]);

            $ordenes->cliente()->associate($cliente);
            $ordenes->save();

            $this->storeUploadedPhotos($request, $ordenes);

            DB::commit();
            session()->flash('status', 'Se ha agregado correctamente la orden.');
            session()->flash('status_type', 'success');
            return redirect()->route('ordenes.index');
        } catch (\Illuminate\Database\QueryException $ex) {
            DB::rollBack();
            Session::flash('status', $ex->getMessage());
            Session::flash('status_type', 'error-Query');
            return back()->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('status', $e->getMessage());
            Session::flash('status_type', 'error');
            return back()->withInput();
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

    public function clienteList()
    {
        return response()->json(
            Cliente::query()
                ->select(['id_cliente', 'nombreCompleto', 'telefono', 'correo', 'rfc'])
                ->orderBy('nombreCompleto')
                ->get()
        );
    }

    public function marcaList()
    {
        return response()->json(
            DatosVehiculo::query()
                ->select(['id_vehiculo', 'marca'])
                ->orderBy('marca')
                ->get()
        );
    }

    public function tipovList()
    {
        return response()->json(
            TipoVehiculo::query()
                ->select(['id_tvehiculo', 'tipo'])
                ->orderBy('tipo')
                ->get()
        );
    }

    public function tiposList()
    {
        return response()->json(
            TipoServicio::query()
                ->select(['id_servicio', 'nombreServicio'])
                ->orderBy('nombreServicio')
                ->get()
        );
    }

    public function userList()
    {
        return response()->json(
            User::query()
                ->select(['id', 'name'])
                ->where('id', '!=', 1)
                ->orderBy('name')
                ->get()
        );
    }

    private function registroCatalogos(): array
    {
        return [
            'datosVehiculo' => Cache::remember('catalogos.ordenes.vehiculos', now()->addMinutes(10), function () {
                return DatosVehiculo::query()
                    ->select(['id_vehiculo', 'marca'])
                    ->orderBy('marca')
                    ->get();
            }),
            'tiposVehiculo' => Cache::remember('catalogos.ordenes.tipos_vehiculo', now()->addMinutes(10), function () {
                return TipoVehiculo::query()
                    ->select(['id_tvehiculo', 'tipo'])
                    ->orderBy('tipo')
                    ->get();
            }),
            'tiposServicio' => Cache::remember('catalogos.ordenes.tipos_servicio', now()->addMinutes(10), function () {
                return TipoServicio::query()
                    ->select(['id_servicio', 'nombreServicio'])
                    ->orderBy('nombreServicio')
                    ->get();
            }),
            'users' => Cache::remember('catalogos.ordenes.users', now()->addMinutes(10), function () {
                return User::query()
                    ->select(['id', 'name'])
                    ->where('id', '!=', 1)
                    ->orderBy('name')
                    ->get();
            }),
            'clientes' => Cache::remember('catalogos.ordenes.clientes', now()->addMinutes(10), function () {
                return Cliente::query()
                    ->select(['id_cliente', 'nombreCompleto', 'telefono', 'correo', 'rfc'])
                    ->orderBy('nombreCompleto')
                    ->get();
            }),
        ];
    }

    private function normalizedOrderInput(Request $request): array
    {
        return [
            'usar_cliente_existente' => $request->boolean('usar_cliente_existente'),
            'nombreCompleto' => $request->filled('nombreCompleto') ? preg_replace('/\s+/', ' ', trim($request->input('nombreCompleto'))) : null,
            'telefono' => $request->filled('telefono') ? preg_replace('/\D+/', '', $request->input('telefono')) : null,
            'correo' => $request->filled('correo') ? strtolower(trim($request->input('correo'))) : null,
            'rfc' => $request->filled('rfc') ? strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $request->input('rfc'))) : null,
            'yearVehiculo' => $request->filled('yearVehiculo') ? preg_replace('/\D+/', '', $request->input('yearVehiculo')) : null,
            'color' => $request->filled('color') ? preg_replace('/\s+/', ' ', trim($request->input('color'))) : null,
            'placas' => $request->filled('placas') ? strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $request->input('placas'))) : null,
            'kilometraje' => $request->filled('kilometraje') ? preg_replace('/[^0-9.]/', '', $request->input('kilometraje')) : null,
            'motor' => $request->filled('motor') ? strtoupper(preg_replace('/[^A-Za-z0-9.]/', '', $request->input('motor'))) : null,
            'cilindros' => $request->filled('cilindros') ? preg_replace('/[^0-9.]/', '', $request->input('cilindros')) : null,
            'noSerievehiculo' => $request->filled('noSerievehiculo') ? strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $request->input('noSerievehiculo'))) : null,
            'fechaEntrega' => $this->normalizeDate($request->input('fechaEntrega')),
            'retiroRefacciones' => $request->input('retiroRefacciones'),
        ];
    }

    private function orderValidationRules(bool $usingExistingClient): array
    {
        $currentYear = (int) now()->format('Y') + 1;

        return [
            'usar_cliente_existente' => ['nullable', 'boolean'],
            'cliente_existente_id' => [$usingExistingClient ? 'required' : 'nullable', 'exists:clientes,id_cliente'],
            'nombreCompleto' => [$usingExistingClient ? 'nullable' : 'required', 'string', 'max:100', Rule::unique('clientes', 'nombreCompleto')],
            'telefono' => [$usingExistingClient ? 'nullable' : 'required', 'digits:10', Rule::unique('clientes', 'telefono')],
            'correo' => [$usingExistingClient ? 'nullable' : 'required', 'email', 'max:30', Rule::unique('clientes', 'correo')],
            'rfc' => [$usingExistingClient ? 'nullable' : 'required', 'string', 'min:12', 'max:13', Rule::unique('clientes', 'rfc')],
            'vehiculo_id' => ['required', 'exists:datos_vehiculo,id_vehiculo'],
            'tvehiculo_id' => ['required', 'exists:tipo_vehiculo,id_tvehiculo'],
            'servicio_id' => ['required', 'exists:tipo_servicio,id_servicio'],
            'user_id' => ['required', 'exists:users,id'],
            'modelo' => ['required', 'string', 'max:100'],
            'yearVehiculo' => ['required', 'integer', 'digits:4', 'between:1900,' . $currentYear],
            'color' => ['required', 'string', 'max:30'],
            'placas' => ['required', 'string', 'max:7'],
            'kilometraje' => ['required', 'numeric', 'min:0'],
            'motor' => ['required', 'string', 'max:10'],
            'cilindros' => ['required', 'numeric', 'min:1'],
            'noSerievehiculo' => ['required', 'string', 'min:5', 'max:17'],
            'status' => ['required', Rule::in(['en proceso', 'finalizada'])],
            'fechaEntrega' => ['required', 'date', 'after_or_equal:today'],
            'observacionesInt' => ['required', 'string'],
            'recomendacionesCliente' => ['required', 'string'],
            'detallesOrden' => ['required', 'string'],
            'retiroRefacciones' => ['required', 'boolean'],
            'photos.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    private function orderValidationMessages(): array
    {
        return [
            'cliente_existente_id.required' => 'Selecciona un cliente registrado.',
            'cliente_existente_id.exists' => 'El cliente seleccionado no es válido.',
            'nombreCompleto.required' => 'Escribe el nombre completo del cliente.',
            'nombreCompleto.unique' => 'Ese cliente ya existe. Usa la opción de cliente registrado.',
            'telefono.required' => 'Escribe el teléfono del cliente.',
            'telefono.digits' => 'El teléfono debe tener 10 dígitos.',
            'telefono.unique' => 'Ese teléfono ya está registrado.',
            'correo.required' => 'Escribe el correo electrónico del cliente.',
            'correo.email' => 'El correo electrónico no tiene un formato válido.',
            'correo.unique' => 'Ese correo electrónico ya está registrado.',
            'rfc.required' => 'Escribe el RFC del cliente.',
            'rfc.min' => 'El RFC debe tener al menos 12 caracteres.',
            'rfc.max' => 'El RFC no puede exceder 13 caracteres.',
            'rfc.unique' => 'Ese RFC ya está registrado.',
            'vehiculo_id.required' => 'Selecciona la marca de la unidad.',
            'tvehiculo_id.required' => 'Selecciona el tipo de vehículo.',
            'servicio_id.required' => 'Selecciona el tipo de servicio.',
            'user_id.required' => 'Selecciona quién atenderá la orden.',
            'modelo.required' => 'Escribe la línea o modelo de la unidad.',
            'yearVehiculo.required' => 'Indica el año de la unidad.',
            'yearVehiculo.digits' => 'El año debe llevar 4 dígitos.',
            'yearVehiculo.between' => 'Ingresa un año válido para la unidad.',
            'color.required' => 'Escribe el color de la unidad.',
            'placas.required' => 'Escribe las placas de la unidad.',
            'kilometraje.required' => 'Indica el kilometraje actual.',
            'motor.required' => 'Escribe la información del motor.',
            'cilindros.required' => 'Indica los cilindros de la unidad.',
            'noSerievehiculo.required' => 'Escribe el número de serie de la unidad.',
            'fechaEntrega.required' => 'Selecciona una fecha estimada de entrega.',
            'fechaEntrega.after_or_equal' => 'La fecha de entrega no puede ser anterior a hoy.',
            'status.required' => 'Selecciona el estado de la orden.',
            'observacionesInt.required' => 'Agrega las observaciones internas.',
            'recomendacionesCliente.required' => 'Agrega las recomendaciones del cliente.',
            'detallesOrden.required' => 'Agrega los detalles del servicio.',
            'retiroRefacciones.required' => 'Indica si el cliente retira refacciones.',
            'photos.*.image' => 'Cada archivo debe ser una imagen.',
            'photos.*.mimes' => 'Las fotografías deben estar en formato JPG o PNG.',
            'photos.*.max' => 'Cada fotografía puede pesar hasta 2 MB.',
        ];
    }

    private function orderValidationAttributes(): array
    {
        return [
            'nombreCompleto' => 'nombre completo',
            'telefono' => 'teléfono',
            'correo' => 'correo electrónico',
            'rfc' => 'RFC',
            'vehiculo_id' => 'marca',
            'tvehiculo_id' => 'tipo de vehículo',
            'servicio_id' => 'tipo de servicio',
            'user_id' => 'atiende',
            'modelo' => 'línea',
            'yearVehiculo' => 'año',
            'color' => 'color',
            'placas' => 'placas',
            'kilometraje' => 'kilometraje',
            'motor' => 'motor',
            'cilindros' => 'cilindros',
            'noSerievehiculo' => 'número de serie',
            'fechaEntrega' => 'fecha de entrega',
            'observacionesInt' => 'observaciones internas',
            'recomendacionesCliente' => 'recomendaciones del cliente',
            'detallesOrden' => 'detalles del servicio',
            'retiroRefacciones' => 'retiro de refacciones',
        ];
    }

    private function storeUploadedPhotos(Request $request, Ordenes $orden): void
    {
        if (!$request->hasFile('photos')) {
            return;
        }

        $destinationPath = public_path('images/photos');

        if (!is_dir($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        foreach ($request->file('photos') as $photo) {
            if (!$photo) {
                continue;
            }

            $filename = now()->format('YmdHis') . '-' . uniqid() . '.' . $photo->getClientOriginalExtension();
            $photo->move($destinationPath, $filename);

            Fotografia::create([
                'ruta' => 'images/photos/' . $filename,
                'ordenes_id' => $orden->id_ordenes,
            ]);
        }
    }

    private function normalizeDate(?string $date): ?string
    {
        if (!$date) {
            return null;
        }

        foreach (['Y-m-d', 'Y/m/d', 'd/m/Y'] as $format) {
            try {
                return Carbon::createFromFormat($format, $date)->format('Y-m-d');
            } catch (\Throwable $exception) {
                continue;
            }
        }

        return $date;
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
            $orden->motivo = $request->input('motivo');
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
            $orden->id = $request->input('user_id');

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
        $pdf->setPaper('letter', 'portrait'); // Usar tamaño carta de Estados Unidos en vertical

        $filename = 'orden_' . Carbon::now()->format('Ymd_His') . '.pdf';

        return $pdf->download($filename);
    }

    public function destroy($id_ordenes)
    {
        $orden = Ordenes::findOrFail($id_ordenes);
    
        try {
            DB::beginTransaction();
    
            // Verificar el rol del usuario
            if (!auth()->user()->hasRole('Administrador')) {
                throw new AuthorizationException('No tienes permiso para realizar esta acción.');
            }
    
            $orden->delete();
    
            // Sincronizar los permisos del usuario
            $user = auth()->user();
            $user->syncPermissions();
    
            DB::commit();
    
            Session::flash('status', "Se ha eliminado correctamente el registro");
            Session::flash('status_type', 'success');
    
            return redirect(route('ordenes.index'));
        } catch (AuthorizationException $e) {
            DB::rollBack();
    
            Session::flash('status', $e->getMessage());
            Session::flash('status_type', 'error');
    
            return back();
        }
    }
    
    
    

}
