<?php

namespace App\Services;

use App\Models\Ordenes;
use App\Models\Cliente;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class OrdenService
{
    protected PhotoService $photoService;

    public function __construct(PhotoService $photoService)
    {
        $this->photoService = $photoService;
    }

    public function storeOrden(array $validatedData, Request $request): Ordenes
    {
        return DB::transaction(function () use ($validatedData, $request) {
            if (!empty($validatedData['usar_cliente_existente'])) {
                $cliente = Cliente::findOrFail($validatedData['cliente_existente_id']);
            } else {
                $cliente = Cliente::create([
                    'nombreCompleto' => trim($validatedData['nombreCompleto']),
                    'telefono'       => $validatedData['telefono'],
                    'correo'         => $validatedData['correo'],
                    'rfc'            => $validatedData['rfc'],
                ]);

                Cache::forget('catalogos.ordenes.clientes');
            }

            $ordenes = new Ordenes([
                'yearVehiculo'           => $validatedData['yearVehiculo'],
                'color'                  => trim($validatedData['color']),
                'placas'                 => $validatedData['placas'],
                'kilometraje'            => $validatedData['kilometraje'],
                'motor'                  => $validatedData['motor'],
                'status'                 => $validatedData['status'],
                'modelo'                 => trim($validatedData['modelo']),
                'cilindros'              => $validatedData['cilindros'],
                'noSerievehiculo'        => $validatedData['noSerievehiculo'],
                'fechaEntrega'           => $validatedData['fechaEntrega'],
                'observacionesInt'       => trim($validatedData['observacionesInt']),
                'recomendacionesCliente' => trim($validatedData['recomendacionesCliente']),
                'detallesOrden'          => trim($validatedData['detallesOrden']),
                'retiroRefacciones'      => (bool) $validatedData['retiroRefacciones'],
                'vehiculo_id'            => $validatedData['vehiculo_id'],
                'servicio_id'            => $validatedData['servicio_id'],
                'tvehiculo_id'           => $validatedData['tvehiculo_id'],
                'id'                     => $validatedData['user_id'],
            ]);

            $ordenes->cliente()->associate($cliente);
            $ordenes->save();

            $this->storePhotos($request, $ordenes);

            return $ordenes;
        });
    }

    public function updateOrden(Ordenes $orden, array $validatedData, Request $request): Ordenes
    {
        return DB::transaction(function () use ($orden, $validatedData, $request) {
            $orden->yearVehiculo           = $validatedData['yearVehiculo'];
            $orden->color                  = $validatedData['color'];
            $orden->placas                 = $validatedData['placas'];
            $orden->kilometraje            = $validatedData['kilometraje'];
            $orden->motor                  = $validatedData['motor'];
            // motivo could be passed or empty
            if (isset($validatedData['motivo'])) {
                $orden->motivo = $validatedData['motivo'];
            }
            $orden->modelo                 = $validatedData['modelo'];
            $orden->status                 = $validatedData['status'];
            $orden->cilindros              = $validatedData['cilindros'];
            $orden->noSerievehiculo        = $validatedData['noSerievehiculo'];
            $orden->fechaEntrega           = $validatedData['fechaEntrega'];
            $orden->observacionesInt       = $validatedData['observacionesInt'];
            $orden->recomendacionesCliente = $validatedData['recomendacionesCliente'];
            $orden->detallesOrden          = $validatedData['detallesOrden'];
            $orden->retiroRefacciones      = $validatedData['retiroRefacciones'];
            $orden->vehiculo_id            = $validatedData['vehiculo_id'];
            $orden->servicio_id            = $validatedData['servicio_id'];
            $orden->tvehiculo_id           = $validatedData['tvehiculo_id'];
            $orden->id                     = $validatedData['user_id'];

            $cliente = $orden->cliente;
            $cliente->nombreCompleto = $validatedData['nombreCompleto'];
            $cliente->telefono       = $validatedData['telefono'];
            $cliente->correo         = $validatedData['correo'];
            $cliente->rfc            = $validatedData['rfc'];
            $cliente->save();
            
            Cache::forget('catalogos.ordenes.clientes');

            $orden->save();

            // Eliminar fotos marcadas
            $toDelete = collect($validatedData['delete_photo_ids'] ?? [])->filter()->unique();

            foreach ($toDelete as $fotoId) {
                $fotografia = $orden->fotografias->firstWhere('id', (int) $fotoId);

                if ($fotografia) {
                    $this->photoService->deleteStoredPhoto($fotografia);
                    $fotografia->delete();
                }
            }

            // Guardar fotos nuevas
            $this->storePhotos($request, $orden);

            return $orden;
        });
    }

    private function storePhotos(Request $request, Ordenes $orden): void
    {
        $tokens = $request->input('photo_tokens', []);
        $tempPhotos = $request->session()->get(PhotoService::TEMP_PHOTO_SESSION_KEY, []);
        
        $this->photoService->storeTemporaryPhotos($tokens, $tempPhotos, $orden);
        $request->session()->put(PhotoService::TEMP_PHOTO_SESSION_KEY, $tempPhotos);

        if ($request->hasFile('photos')) {
            $this->photoService->processAndStoreUploadedPhotos($request->file('photos'), $orden);
        }
    }
}
