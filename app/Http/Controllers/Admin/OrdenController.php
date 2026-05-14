<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use App\Models\Ordenes;
use App\Models\Cliente;
use App\Models\DatosVehiculo;
use App\Models\TipoVehiculo;
use App\Models\TipoServicio;
use App\Models\Fotografia;
use App\Models\Cotizacion;
use App\Models\Inventario;
use App\Models\User;
use PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use App\Http\Requests\StoreOrdenRequest;
use App\Http\Requests\UpdateOrdenRequest;
use App\Services\OrdenService;
use App\Services\PhotoService;

class OrdenController extends Controller
{
    protected OrdenService $ordenService;
    protected PhotoService $photoService;

    public function __construct(OrdenService $ordenService, PhotoService $photoService)
    {
        $this->ordenService = $ordenService;
        $this->photoService = $photoService;
    }

    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $limit = $request->input('limit', 5);
        $order = $request->input('order', 'desc');
        $status = $request->input('status', null);
        $filter = $request->input('filter', null);
        $query = Ordenes::query()->with(['cliente', 'vehiculo', 'servicio', 'user']);

        if (trim($search) != '') {
            $matchingClientIds = Cliente::matchingSearchIds($search);

            $query->where(function ($query) use ($search, $matchingClientIds) {
                $query->where('id_ordenes', 'like', "%$search%")
                    ->orWhereHas('vehiculo', function ($query) use ($search) {
                        $query->where('marca', 'like', "%$search%");
                    })
                    ->orWhere('placas', 'like', "%$search%")
                    ->orWhere('modelo', 'like', "%$search%")
                    ->orWhereHas('servicio', function ($query) use ($search) {
                        $query->where('nombreServicio', 'like', "%$search%");
                    })
                    ->orWhereHas('user', function ($query) use ($search) {
                        $query->where('name', 'like', "%$search%");
                    });

                if ($matchingClientIds->isNotEmpty()) {
                    $query->orWhereIn('cliente_id', $matchingClientIds->all());
                }
            });
        }

        if (!is_null($status)) {
            $query->where('status', $status);
        }

        if ($filter === 'vencidas') {
            $query->where('status', '!=', 'finalizada')
                  ->whereNotNull('fechaEntrega')
                  ->whereDate('fechaEntrega', '<', Carbon::today());
        } elseif ($filter === 'sin_asignar') {
            $query->whereNull('id');
        }

        $ordenes = $query->orderBy('id_ordenes', $order)->paginate($limit)->withQueryString();

        return view('admin.ordenes.index', [
            'ordenes' => $ordenes,
            'search'  => $search,
            'limit'   => $limit,
            'order'   => $order,
            'status'  => $status,
        ]);
    }

    public function verificarNombreUsuario(Request $request)
    {
        $nombreCompleto = trim((string) $request->input('nombreCompleto'));
        $exists = $nombreCompleto !== '' && Cliente::existsWithSensitiveValue('nombreCompleto', $nombreCompleto);

        return response()->json(['exists' => $exists]);
    }

    public function create(Request $request)
    {
        return $this->registro($request);
    }

    public function registro(?Request $request = null)
    {
        $cotizacion = $this->acceptedCotizacionForOrder($request);
        $catalogos = $this->registroCatalogos();

        return view('admin.ordenes.registro', array_merge($catalogos, [
            'preferExistingClient' => $cotizacion ? true : ($catalogos['preferExistingClient'] ?? false),
            'orderPrefill' => $cotizacion ? $this->orderPrefillFromCotizacion($cotizacion) : [],
        ]));
    }

    public function store(StoreOrdenRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $validatedData['user_id'] = $validatedData['user_id'] ?? $request->user()?->id;

            $this->ordenService->storeOrden($validatedData, $request);

            session()->flash('status', 'Se ha agregado correctamente la orden.');
            session()->flash('status_type', 'success');
            return redirect()->route('ordenes.index');
        } catch (\Illuminate\Database\QueryException $ex) {
            Session::flash('status', $ex->getMessage());
            Session::flash('status_type', 'error-Query');
            return back()->withInput();
        } catch (\Exception $e) {
            Session::flash('status', $e->getMessage());
            Session::flash('status_type', 'error');
            return back()->withInput();
        }
    }

    public function asigne()
    {
        return view('admin.ordenes.registro', array_merge($this->registroCatalogos(), [
            'preferExistingClient' => true,
        ]));
    }

    public function store2(StoreOrdenRequest $request)
    {
        $request->merge([
            'usar_cliente_existente' => true,
            'cliente_existente_id'   => $request->input('cliente_existente_id', $request->input('cliente_id')),
        ]);

        return $this->store($request);
    }

    public function storeTemporaryPhoto(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $photo     = $validated['photo'];
        $token     = (string) Str::uuid();
        $extension = $this->photoService->safePhotoExtension($photo);
        $path      = 'ordenes/fotografias/temporales/' . $token . '.' . $extension . '.enc';

        $this->photoService->putEncryptedPhoto($photo, $path);

        $tempPhotos          = $request->session()->get(PhotoService::TEMP_PHOTO_SESSION_KEY, []);
        $tempPhotos[$token]  = [
            'path'        => $path,
            'extension'   => $extension,
            'name'        => $photo->getClientOriginalName(),
            'size'        => $photo->getSize(),
            'mime'        => $photo->getMimeType(),
            'uploaded_at' => now()->toDateTimeString(),
        ];
        $request->session()->put(PhotoService::TEMP_PHOTO_SESSION_KEY, $tempPhotos);

        return response()->json([
            'token' => $token,
            'name'  => $photo->getClientOriginalName(),
            'size'  => $photo->getSize(),
            'mime'  => $photo->getMimeType(),
        ], 201);
    }

    public function destroyTemporaryPhoto(Request $request): JsonResponse
    {
        $validated  = $request->validate(['token' => ['required', 'uuid']]);
        $tempPhotos = $request->session()->get(PhotoService::TEMP_PHOTO_SESSION_KEY, []);
        $photo      = $tempPhotos[$validated['token']] ?? null;

        if ($photo && !empty($photo['path'])) {
            Storage::disk('local')->delete($photo['path']);
            unset($tempPhotos[$validated['token']]);
            $request->session()->put(PhotoService::TEMP_PHOTO_SESSION_KEY, $tempPhotos);
        }

        return response()->json(['deleted' => true]);
    }

    public function clienteList()
    {
        return response()->json(
            Cliente::query()
                ->select(['id_cliente', 'nombreCompleto', 'telefono', 'correo', 'rfc'])
                ->get()
                ->sortBy(fn (Cliente $cliente) => mb_strtolower($cliente->nombreCompleto))
                ->values()
        );
    }

    public function marcaList()
    {
        return response()->json(
            DatosVehiculo::query()->select(['id_vehiculo', 'marca'])->orderBy('marca')->get()
        );
    }

    public function tipovList()
    {
        return response()->json(
            TipoVehiculo::query()->select(['id_tvehiculo', 'tipo'])->orderBy('tipo')->get()
        );
    }

    public function tiposList()
    {
        return response()->json(
            TipoServicio::query()->select(['id_servicio', 'nombreServicio'])->orderBy('nombreServicio')->get()
        );
    }

    public function userList()
    {
        return response()->json(
            User::query()->select(['id', 'name'])->where('id', '!=', 1)->orderBy('name')->get()
        );
    }

    private function registroCatalogos(): array
    {
        $authUser = auth()->user();
        $users    = Cache::remember('catalogos.ordenes.users', now()->addMinutes(10), function () {
            return User::query()->select(['id', 'name'])->where('id', '!=', 1)->orderBy('name')->get();
        });

        if ($authUser) {
            $users = $users
                ->reject(fn ($user) => (int) $user->id === (int) $authUser->id)
                ->push($authUser)
                ->sortBy('name')
                ->values();
        }

        return [
            'datosVehiculo' => Cache::remember('catalogos.ordenes.vehiculos', now()->addMinutes(10), fn () =>
                DatosVehiculo::query()->select(['id_vehiculo', 'marca'])->orderBy('marca')->get()
            ),
            'tiposVehiculo' => Cache::remember('catalogos.ordenes.tipos_vehiculo', now()->addMinutes(10), fn () =>
                TipoVehiculo::query()->select(['id_tvehiculo', 'tipo'])->orderBy('tipo')->get()
            ),
            'tiposServicio' => Cache::remember('catalogos.ordenes.tipos_servicio', now()->addMinutes(10), fn () =>
                TipoServicio::query()->select(['id_servicio', 'nombreServicio'])->orderBy('nombreServicio')->get()
            ),
            'users'          => $users,
            'attendingUserId' => $authUser?->id,
            'clientes'       => Cache::remember('catalogos.ordenes.clientes', now()->addMinutes(10), fn () =>
                Cliente::query()->select(['id_cliente', 'nombreCompleto', 'telefono', 'correo', 'rfc'])->get()
            )->sortBy(fn (Cliente $c) => mb_strtolower($c->nombreCompleto))->values(),
        ];
    }

    private function acceptedCotizacionForOrder(?Request $request): ?Cotizacion
    {
        $id = $request?->integer('cotizacion_id');

        if (!$id) {
            return null;
        }

        return Cotizacion::with(['servicio', 'user', 'conceptos'])
            ->where('estado', 'aceptada')
            ->find($id);
    }

    private function orderPrefillFromCotizacion(Cotizacion $cotizacion): array
    {
        $conceptos = $cotizacion->conceptos
            ->map(fn ($concepto) => '- ' . $concepto->descripcion . ' (' . number_format((float) $concepto->cantidad, 2) . ' x $' . number_format((float) $concepto->precio_unitario, 2) . ' = $' . number_format((float) $concepto->subtotal, 2) . ')')
            ->implode("\n");

        $detalle = trim(implode("\n", array_filter([
            'Cotización aceptada: ' . $cotizacion->folio,
            'Servicio principal: ' . ($cotizacion->servicio?->nombreServicio ?? 'Sin servicio'),
            'Total cotizado: $' . number_format((float) $cotizacion->total, 2),
            $conceptos ? "Conceptos adicionales:\n" . $conceptos : null,
            $cotizacion->notas ? "Notas de cotización:\n" . $cotizacion->notas : null,
        ])));

        return [
            'cotizacion_id' => $cotizacion->id_cotizacion,
            'servicio_id' => $cotizacion->servicio_id,
            'user_id' => $cotizacion->user_id,
            'detallesOrden' => $detalle,
            'recomendacionesCliente' => 'Cotización ' . $cotizacion->folio . ' aceptada por $' . number_format((float) $cotizacion->total, 2) . '.',
            'observacionesInt' => 'Orden generada desde cotización aceptada ' . $cotizacion->folio . '.',
        ];
    }

    public function show($id_ordenes)
    {
        $orden = Ordenes::with('fotografias')->findOrFail($id_ordenes);

        return view('admin.ordenes.show', [
            'orden'        => $orden,
            'datosVehiculo' => DatosVehiculo::all(),
            'tiposVehiculo' => TipoVehiculo::all(),
            'tiposServicio' => TipoServicio::all(),
            'users'        => User::all(),
            'cliente_id'   => Cliente::all(),
        ]);
    }

    public function showPhoto($id_ordenes, Fotografia $fotografia)
    {
        abort_unless((int) $fotografia->ordenes_id === (int) $id_ordenes, 404);

        if (Str::endsWith($fotografia->ruta, '.enc')) {
            abort_unless(Storage::disk('local')->exists($fotografia->ruta), 404);

            $encrypted = Storage::disk('local')->get($fotografia->ruta);
            $decoded   = base64_decode(Crypt::decryptString($encrypted), true);

            abort_if($decoded === false, 404);

            return response($decoded, 200)
                ->header('Content-Type', $this->photoService->mimeTypeFromEncryptedPath($fotografia->ruta))
                ->header('Cache-Control', 'private, max-age=600');
        }

        $legacyPath = public_path($fotografia->ruta);
        abort_unless(is_file($legacyPath), 404);

        return response()->file($legacyPath);
    }

    public function edit($id_ordenes)
    {
        $orden = Ordenes::with('fotografias')->findOrFail($id_ordenes);

        return view('admin.ordenes.edit', [
            'orden'         => $orden,
            'datosVehiculo' => DatosVehiculo::all(),
            'tiposVehiculo' => TipoVehiculo::all(),
            'tiposServicio' => TipoServicio::all(),
            'users'         => User::all(),
            'cliente'       => Cliente::all(),
        ]);
    }

    public function update(UpdateOrdenRequest $request, $id_ordenes)
    {
        try {
            $orden = Ordenes::with('fotografias')->findOrFail($id_ordenes);
            $validatedData = $request->validated();
            // Motivo in original code was not validated, we take it from request if exists.
            if ($request->has('motivo')) {
                $validatedData['motivo'] = $request->input('motivo');
            }

            $this->ordenService->updateOrden($orden, $validatedData, $request);

            session()->flash('status', 'Se ha actualizado correctamente la orden.');
            session()->flash('status_type', 'success');
            return redirect()->route('ordenes.index');
        } catch (\Illuminate\Database\QueryException $ex) {
            Session::flash('status', $ex->getMessage());
            Session::flash('status_type', 'error-Query');
            return back();
        } catch (\Exception $e) {
            Session::flash('status', $e->getMessage());
            Session::flash('status_type', 'error');
            return back();
        }
    }

    public function exportToPDF($id_ordenes)
    {
        $orden = Ordenes::findOrFail($id_ordenes);

        $html = view('admin.ordenes.export', [
            'orden'         => $orden,
            'datosVehiculo' => DatosVehiculo::all(),
            'tiposServicio' => TipoServicio::all(),
            'tiposVehiculo' => TipoVehiculo::all(),
            'users'         => User::all(),
            'cliente_id'    => Cliente::all(),
        ])->render();

        $pdf = PDF::loadHTML($html);
        $pdf->setPaper('letter', 'portrait');

        return $pdf->download('orden_' . Carbon::now()->format('Ymd_His') . '.pdf');
    }

    public function imprimirTicket($id_ordenes)
    {
        $orden = Ordenes::with(['cliente', 'vehiculo', 'servicio', 'user'])->findOrFail($id_ordenes);

        return view('admin.ordenes.ticket', [
            'orden' => $orden,
        ]);
    }

    public function addInventario(Request $request, $id_ordenes)
    {
        $request->validate([
            'inventario_id' => 'required|exists:inventarios,id_inventario',
            'cantidad' => 'required|integer|min:1'
        ]);

        $orden = Ordenes::findOrFail($id_ordenes);
        $inventario = Inventario::findOrFail($request->inventario_id);
        $cantidad = $request->cantidad;

        if ($inventario->cantidad_stock < $cantidad) {
            return back()->with('status', 'Stock insuficiente para este producto.')->with('status_type', 'danger');
        }

        // Descontar inventario
        $inventario->decrement('cantidad_stock', $cantidad);

        // Relacionar
        $orden->belongsToMany(Inventario::class, 'orden_inventario', 'orden_id', 'inventario_id')
              ->attach($inventario->id_inventario, [
                  'cantidad' => $cantidad,
                  'precio_unitario' => $inventario->precio_venta
              ]);

        return back()->with('status', 'Producto agregado a la orden y stock descontado.')->with('status_type', 'success');
    }

    public function destroy($id_ordenes)
    {
        $orden = Ordenes::with('fotografias')->findOrFail($id_ordenes);

        foreach ($orden->fotografias as $fotografia) {
            $this->photoService->deleteStoredPhoto($fotografia);
            $fotografia->delete();
        }

        $orden->delete();

        session()->flash('status', 'Se ha borrado exitosamente la orden.');
        session()->flash('status_type', 'success');
        return redirect()->route('ordenes.index');
    }
}
