<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Http\UploadedFile;
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
    private const TEMP_PHOTO_SESSION_KEY = 'ordenes_temp_photos';

    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $limit = $request->input('limit', 5);
        $order = $request->input('order', 'desc');
        $status = $request->input('status', null);
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
        $request->merge(array_merge(
            $this->normalizedOrderInput($request),
            ['user_id' => $request->input('user_id') ?: $request->user()?->id]
        ));

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
                    'telefono'       => $validated['telefono'],
                    'correo'         => $validated['correo'],
                    'rfc'            => $validated['rfc'],
                ]);

                Cache::forget('catalogos.ordenes.clientes');
            }

            $ordenes = new Ordenes([
                'yearVehiculo'           => $validated['yearVehiculo'],
                'color'                  => trim($validated['color']),
                'placas'                 => $validated['placas'],
                'kilometraje'            => $validated['kilometraje'],
                'motor'                  => $validated['motor'],
                'status'                 => $validated['status'],
                'modelo'                 => trim($validated['modelo']),
                'cilindros'              => $validated['cilindros'],
                'noSerievehiculo'        => $validated['noSerievehiculo'],
                'fechaEntrega'           => $validated['fechaEntrega'],
                'observacionesInt'       => trim($validated['observacionesInt']),
                'recomendacionesCliente' => trim($validated['recomendacionesCliente']),
                'detallesOrden'          => trim($validated['detallesOrden']),
                'retiroRefacciones'      => (bool) $validated['retiroRefacciones'],
                'vehiculo_id'            => $validated['vehiculo_id'],
                'servicio_id'            => $validated['servicio_id'],
                'tvehiculo_id'           => $validated['tvehiculo_id'],
                'id'                     => $validated['user_id'],
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
        return view('admin.ordenes.registro', array_merge($this->registroCatalogos(), [
            'preferExistingClient' => true,
        ]));
    }

    public function store2(Request $request)
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

        /** @var UploadedFile $photo */
        $photo     = $validated['photo'];
        $token     = (string) Str::uuid();
        $extension = $this->safePhotoExtension($photo);
        $path      = 'ordenes/fotografias/temporales/' . $token . '.' . $extension . '.enc';

        $this->putEncryptedPhoto($photo, $path);

        $tempPhotos          = $request->session()->get(self::TEMP_PHOTO_SESSION_KEY, []);
        $tempPhotos[$token]  = [
            'path'        => $path,
            'extension'   => $extension,
            'name'        => $photo->getClientOriginalName(),
            'size'        => $photo->getSize(),
            'mime'        => $photo->getMimeType(),
            'uploaded_at' => now()->toDateTimeString(),
        ];
        $request->session()->put(self::TEMP_PHOTO_SESSION_KEY, $tempPhotos);

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
        $tempPhotos = $request->session()->get(self::TEMP_PHOTO_SESSION_KEY, []);
        $photo      = $tempPhotos[$validated['token']] ?? null;

        if ($photo && !empty($photo['path'])) {
            Storage::disk('local')->delete($photo['path']);
            unset($tempPhotos[$validated['token']]);
            $request->session()->put(self::TEMP_PHOTO_SESSION_KEY, $tempPhotos);
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

    private function normalizedOrderInput(Request $request): array
    {
        return [
            'usar_cliente_existente' => $request->boolean('usar_cliente_existente'),
            'nombreCompleto'         => $request->filled('nombreCompleto') ? preg_replace('/\s+/', ' ', trim($request->input('nombreCompleto'))) : null,
            'telefono'               => $request->filled('telefono') ? preg_replace('/\D+/', '', $request->input('telefono')) : null,
            'correo'                 => $request->filled('correo') ? strtolower(trim($request->input('correo'))) : null,
            'rfc'                    => $request->filled('rfc') ? strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $request->input('rfc'))) : null,
            'yearVehiculo'           => $request->filled('yearVehiculo') ? preg_replace('/\D+/', '', $request->input('yearVehiculo')) : null,
            'color'                  => $request->filled('color') ? preg_replace('/\s+/', ' ', trim($request->input('color'))) : null,
            'placas'                 => $request->filled('placas') ? strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $request->input('placas'))) : null,
            'kilometraje'            => $request->filled('kilometraje') ? preg_replace('/[^0-9.]/', '', $request->input('kilometraje')) : null,
            'motor'                  => $request->filled('motor') ? strtoupper(preg_replace('/[^A-Za-z0-9.]/', '', $request->input('motor'))) : null,
            'cilindros'              => $request->filled('cilindros') ? preg_replace('/[^0-9.]/', '', $request->input('cilindros')) : null,
            'noSerievehiculo'        => $request->filled('noSerievehiculo') ? strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $request->input('noSerievehiculo'))) : null,
            'fechaEntrega'           => $this->normalizeDate($request->input('fechaEntrega')),
            'retiroRefacciones'      => $request->input('retiroRefacciones'),
        ];
    }

    private function orderValidationRules(bool $usingExistingClient): array
    {
        $currentYear = (int) now()->format('Y') + 1;

        return [
            'usar_cliente_existente' => ['nullable', 'boolean'],
            'cliente_existente_id'   => [$usingExistingClient ? 'required' : 'nullable', 'exists:clientes,id_cliente'],
            'nombreCompleto'         => $usingExistingClient ? ['nullable'] : ['required', 'string', 'max:100', $this->uniqueClientValueRule('nombreCompleto', 'Ese cliente ya existe. Usa la opción de cliente registrado.')],
            'telefono'               => $usingExistingClient ? ['nullable'] : ['required', 'digits:10', $this->uniqueClientValueRule('telefono', 'Ese teléfono ya está registrado.')],
            'correo'                 => $usingExistingClient ? ['nullable'] : ['required', 'email', 'max:30', $this->uniqueClientValueRule('correo', 'Ese correo electrónico ya está registrado.')],
            'rfc'                    => $usingExistingClient ? ['nullable'] : ['required', 'string', 'min:12', 'max:13', $this->uniqueClientValueRule('rfc', 'Ese RFC ya está registrado.')],
            'vehiculo_id'            => ['required', 'exists:datos_vehiculo,id_vehiculo'],
            'tvehiculo_id'           => ['required', 'exists:tipo_vehiculo,id_tvehiculo'],
            'servicio_id'            => ['required', 'exists:tipo_servicio,id_servicio'],
            'user_id'                => ['required', 'exists:users,id'],
            'modelo'                 => ['required', 'string', 'max:100'],
            'yearVehiculo'           => ['required', 'integer', 'digits:4', 'between:1900,' . $currentYear],
            'color'                  => ['required', 'string', 'max:30'],
            'placas'                 => ['required', 'string', 'max:7'],
            'kilometraje'            => ['required', 'numeric', 'min:0'],
            'motor'                  => ['required', 'string', 'max:10'],
            'cilindros'              => ['required', 'numeric', 'min:1'],
            'noSerievehiculo'        => ['required', 'string', 'min:5', 'max:17'],
            'status'                 => ['required', Rule::in(['en proceso', 'finalizada', 'cancelada'])],
            'fechaEntrega'           => ['required', 'date', 'after_or_equal:today'],
            'observacionesInt'       => ['required', 'string'],
            'recomendacionesCliente' => ['required', 'string'],
            'detallesOrden'          => ['required', 'string'],
            'retiroRefacciones'      => ['required', 'boolean'],
            'photo_tokens'           => ['nullable', 'array'],
            'photo_tokens.*'         => ['string', 'uuid'],
            'photos'                 => ['nullable', 'array'],
            'photos.*'               => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    private function orderValidationMessages(): array
    {
        return [
            'cliente_existente_id.required'  => 'Selecciona un cliente registrado.',
            'cliente_existente_id.exists'    => 'El cliente seleccionado no es válido.',
            'nombreCompleto.required'        => 'Escribe el nombre completo del cliente.',
            'nombreCompleto.unique'          => 'Ese cliente ya existe. Usa la opción de cliente registrado.',
            'telefono.required'              => 'Escribe el teléfono del cliente.',
            'telefono.digits'                => 'El teléfono debe tener 10 dígitos.',
            'telefono.unique'                => 'Ese teléfono ya está registrado.',
            'correo.required'                => 'Escribe el correo electrónico del cliente.',
            'correo.email'                   => 'El correo electrónico no tiene un formato válido.',
            'correo.unique'                  => 'Ese correo electrónico ya está registrado.',
            'rfc.required'                   => 'Escribe el RFC del cliente.',
            'rfc.min'                        => 'El RFC debe tener al menos 12 caracteres.',
            'rfc.max'                        => 'El RFC no puede exceder 13 caracteres.',
            'rfc.unique'                     => 'Ese RFC ya está registrado.',
            'vehiculo_id.required'           => 'Selecciona la marca de la unidad.',
            'tvehiculo_id.required'          => 'Selecciona el tipo de vehículo.',
            'servicio_id.required'           => 'Selecciona el tipo de servicio.',
            'user_id.required'               => 'Selecciona quién atenderá la orden.',
            'modelo.required'                => 'Escribe la línea o modelo de la unidad.',
            'yearVehiculo.required'          => 'Indica el año de la unidad.',
            'yearVehiculo.digits'            => 'El año debe llevar 4 dígitos.',
            'yearVehiculo.between'           => 'Ingresa un año válido para la unidad.',
            'color.required'                 => 'Escribe el color de la unidad.',
            'placas.required'                => 'Escribe las placas de la unidad.',
            'kilometraje.required'           => 'Indica el kilometraje actual.',
            'motor.required'                 => 'Escribe la información del motor.',
            'cilindros.required'             => 'Indica los cilindros de la unidad.',
            'noSerievehiculo.required'       => 'Escribe el número de serie de la unidad.',
            'fechaEntrega.required'          => 'Selecciona una fecha estimada de entrega.',
            'fechaEntrega.after_or_equal'    => 'La fecha de entrega no puede ser anterior a hoy.',
            'status.required'                => 'Selecciona el estado de la orden.',
            'observacionesInt.required'      => 'Agrega las observaciones internas.',
            'recomendacionesCliente.required'=> 'Agrega las recomendaciones del cliente.',
            'detallesOrden.required'         => 'Agrega los detalles del servicio.',
            'retiroRefacciones.required'     => 'Indica si el cliente retira refacciones.',
            'photo_tokens.*.uuid'            => 'Una fotografía temporal no se pudo validar. Vuelve a seleccionarla.',
            'photos.*.image'                 => 'Cada archivo debe ser una imagen.',
            'photos.*.mimes'                 => 'Las fotografías deben estar en formato JPG o PNG.',
            'photos.*.max'                   => 'Cada fotografía puede pesar hasta 2 MB.',
        ];
    }

    private function uniqueClientValueRule(string $field, string $message): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail) use ($field, $message): void {
            if (Cliente::existsWithSensitiveValue($field, (string) $value)) {
                $fail($message);
            }
        };
    }

    private function orderValidationAttributes(): array
    {
        return [
            'nombreCompleto'         => 'nombre completo',
            'telefono'               => 'teléfono',
            'correo'                 => 'correo electrónico',
            'rfc'                    => 'RFC',
            'vehiculo_id'            => 'marca',
            'tvehiculo_id'           => 'tipo de vehículo',
            'servicio_id'            => 'tipo de servicio',
            'user_id'                => 'atiende',
            'modelo'                 => 'línea',
            'yearVehiculo'           => 'año',
            'color'                  => 'color',
            'placas'                 => 'placas',
            'kilometraje'            => 'kilometraje',
            'motor'                  => 'motor',
            'cilindros'              => 'cilindros',
            'noSerievehiculo'        => 'número de serie',
            'fechaEntrega'           => 'fecha de entrega',
            'observacionesInt'       => 'observaciones internas',
            'recomendacionesCliente' => 'recomendaciones del cliente',
            'detallesOrden'          => 'detalles del servicio',
            'retiroRefacciones'      => 'retiro de refacciones',
        ];
    }

    private function storeUploadedPhotos(Request $request, Ordenes $orden): void
    {
        $this->storeTemporaryPhotos($request, $orden);

        if (!$request->hasFile('photos')) {
            return;
        }

        foreach ($request->file('photos') as $photo) {
            if (!$photo) continue;

            $token     = (string) Str::uuid();
            $extension = $this->safePhotoExtension($photo);
            $path      = $this->finalPhotoPath($orden, $token, $extension);

            $this->putEncryptedPhoto($photo, $path);

            Fotografia::create([
                'ruta'       => $path,
                'ordenes_id' => $orden->id_ordenes,
            ]);
        }
    }

    private function storeTemporaryPhotos(Request $request, Ordenes $orden): void
    {
        $tokens = collect($request->input('photo_tokens', []))->filter()->unique()->values();

        if ($tokens->isEmpty()) return;

        $tempPhotos = $request->session()->get(self::TEMP_PHOTO_SESSION_KEY, []);

        foreach ($tokens as $token) {
            $photo = $tempPhotos[$token] ?? null;

            if (!$photo || empty($photo['path']) || !Storage::disk('local')->exists($photo['path'])) {
                throw new \RuntimeException('No se pudo recuperar una fotografía temporal. Vuelve a seleccionarla.');
            }

            $extension = $photo['extension'] ?? 'jpg';
            $finalPath = $this->finalPhotoPath($orden, $token, $extension);

            Storage::disk('local')->makeDirectory(dirname($finalPath));
            Storage::disk('local')->move($photo['path'], $finalPath);

            Fotografia::create([
                'ruta'       => $finalPath,
                'ordenes_id' => $orden->id_ordenes,
            ]);

            unset($tempPhotos[$token]);
        }

        $request->session()->put(self::TEMP_PHOTO_SESSION_KEY, $tempPhotos);
    }

    private function putEncryptedPhoto(UploadedFile $photo, string $path): void
    {
        $contents = file_get_contents($photo->getRealPath());

        if ($contents === false) {
            throw new \RuntimeException('No se pudo leer una fotografía seleccionada.');
        }

        // Smart image compression without losing quality
        $imageResource = @imagecreatefromstring($contents);
        if ($imageResource !== false) {
            $width = imagesx($imageResource);
            $height = imagesy($imageResource);
            $maxWidth = 1920;
            $maxHeight = 1080;

            if ($width > $maxWidth || $height > $maxHeight) {
                $ratio = min($maxWidth / $width, $maxHeight / $height);
                $newWidth = (int) ($width * $ratio);
                $newHeight = (int) ($height * $ratio);

                $newImage = imagecreatetruecolor($newWidth, $newHeight);
                if ($photo->getMimeType() === 'image/png') {
                    imagealphablending($newImage, false);
                    imagesavealpha($newImage, true);
                    $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
                    imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
                }

                imagecopyresampled($newImage, $imageResource, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagedestroy($imageResource);
                $imageResource = $newImage;
            }

            ob_start();
            if ($photo->getMimeType() === 'image/png') {
                imagepng($imageResource, null, 9);
            } else {
                imagejpeg($imageResource, null, 85);
            }
            $contents = ob_get_clean();
            imagedestroy($imageResource);
        }

        Storage::disk('local')->makeDirectory(dirname($path));
        Storage::disk('local')->put($path, Crypt::encryptString(base64_encode($contents)));
    }

    private function finalPhotoPath(Ordenes $orden, string $token, string $extension): string
    {
        return 'ordenes/fotografias/' . $orden->id_ordenes . '/' . $token . '.' . $extension . '.enc';
    }

    private function safePhotoExtension(UploadedFile $photo): string
    {
        $extension = strtolower($photo->guessExtension() ?: $photo->getClientOriginalExtension() ?: 'jpg');
        return in_array($extension, ['jpg', 'jpeg', 'png'], true) ? $extension : 'jpg';
    }

    private function mimeTypeFromEncryptedPath(string $path): string
    {
        return Str::contains($path, '.png.enc') ? 'image/png' : 'image/jpeg';
    }

    private function deleteStoredPhoto(Fotografia $fotografia): void
    {
        if (Str::endsWith($fotografia->ruta, '.enc')) {
            Storage::disk('local')->delete($fotografia->ruta);
            return;
        }

        $legacyPath = public_path($fotografia->ruta);
        if (is_file($legacyPath)) {
            @unlink($legacyPath);
        }
    }

    private function normalizeDate(?string $date): ?string
    {
        if (!$date) return null;

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
                ->header('Content-Type', $this->mimeTypeFromEncryptedPath($fotografia->ruta))
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

    public function update(Request $request, $id_ordenes)
    {
        try {
            DB::beginTransaction();

            $orden = Ordenes::with('fotografias')->findOrFail($id_ordenes);

            $orden->yearVehiculo           = $request->input('yearVehiculo');
            $orden->color                  = $request->input('color');
            $orden->placas                 = $request->input('placas');
            $orden->kilometraje            = $request->input('kilometraje');
            $orden->motor                  = $request->input('motor');
            $orden->motivo                 = $request->input('motivo');
            $orden->modelo                 = $request->input('modelo');
            $orden->status                 = $request->input('status');
            $orden->cilindros              = $request->input('cilindros');
            $orden->noSerievehiculo        = $request->input('noSerievehiculo');
            $orden->fechaEntrega           = $request->input('fechaEntrega');
            $orden->observacionesInt       = $request->input('observacionesInt');
            $orden->recomendacionesCliente = $request->input('recomendacionesCliente');
            $orden->detallesOrden          = $request->input('detallesOrden');
            $orden->retiroRefacciones      = $request->input('retiroRefacciones');
            $orden->vehiculo_id            = $request->input('vehiculo_id');
            $orden->servicio_id            = $request->input('servicio_id');
            $orden->tvehiculo_id           = $request->input('tvehiculo_id');
            $orden->id                     = $request->input('user_id');

            $cliente = $orden->cliente;
            $cliente->nombreCompleto = $request->input('nombreCompleto');
            $cliente->telefono       = $request->input('telefono');
            $cliente->correo         = $request->input('correo');
            $cliente->rfc            = $request->input('rfc');
            $cliente->save();
            Cache::forget('catalogos.ordenes.clientes');

            $orden->save();

            // ── Eliminar fotos marcadas ──────────────────────────────────────
            $toDelete = collect($request->input('delete_photo_ids', []))->filter()->unique();

            foreach ($toDelete as $fotoId) {
                // ← CORRECCIÓN: usar 'id' en lugar de 'id_fotografia'
                $fotografia = $orden->fotografias->firstWhere('id', (int) $fotoId);

                if ($fotografia) {
                    $this->deleteStoredPhoto($fotografia);
                    $fotografia->delete();
                }
            }

            // ── Guardar fotos nuevas ─────────────────────────────────────────
            $this->storeUploadedPhotos($request, $orden);

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

    public function destroy($id_ordenes)
    {
        $orden = Ordenes::with('fotografias')->findOrFail($id_ordenes);

        try {
            DB::beginTransaction();

            if (!auth()->user()->hasRole('Administrador')) {
                throw new AuthorizationException('No tienes permiso para realizar esta acción.');
            }

            foreach ($orden->fotografias as $fotografia) {
                $this->deleteStoredPhoto($fotografia);
                $fotografia->delete();
            }

            $orden->delete();
            auth()->user()->syncPermissions();

            DB::commit();
            Session::flash('status', 'Se ha eliminado correctamente el registro');
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