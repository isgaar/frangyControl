<?php

namespace App\Console\Commands;

use App\Models\CampanaPromocional;
use App\Models\Cliente;
use App\Models\Ordenes;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessPromotionalCampaigns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:process-campaigns';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process and send pending promotional WhatsApp campaigns';

    /**
     * Execute the console command.
     */
    public function handle(WhatsAppService $whatsappService)
    {
        $this->info('Buscando campañas promocionales programadas...');

        $campanas = CampanaPromocional::with('servicio')
            ->where('estado', 'pendiente')
            ->where('fecha_programada', '<=', Carbon::now())
            ->get();

        foreach ($campanas as $campana) {
            $this->info("Procesando campaña: {$campana->nombre}");
            
            $campana->estado = 'en_progreso';
            $campana->save();

            $clientesTarget = collect();

            if ($campana->servicio_id) {
                // Filtrar clientes por interés (que hayan tenido órdenes con este servicio)
                // Obtenemos los IDs de los clientes que coinciden
                $clienteIds = Ordenes::where('servicio_id', $campana->servicio_id)
                    ->pluck('cliente_id')
                    ->unique();
                
                $clientesTarget = Cliente::whereIn('id_cliente', $clienteIds)->get();
            } else {
                // Enviar a todos los clientes
                $clientesTarget = Cliente::all();
            }

            $enviados = 0;
            foreach ($clientesTarget as $cliente) {
                if (!$cliente->telefono) {
                    continue;
                }

                $nombre = explode(' ', $cliente->nombreCompleto)[0] ?? 'Cliente';
                
                // Reemplazar variables en el mensaje
                $mensaje = str_replace('{{nombre_cliente}}', $nombre, $campana->mensaje);
                $mensaje = str_replace('{{servicio_interes}}', $campana->servicio ? $campana->servicio->nombreServicio : '', $mensaje);

                $success = $whatsappService->sendMessage($cliente->telefono, $mensaje);

                if ($success) {
                    $enviados++;
                }
            }

            $campana->estado = 'completada';
            $campana->save();

            $this->info("Campaña completada. Enviados: {$enviados}");
        }

        $this->info('Todas las campañas procesadas.');
    }
}
