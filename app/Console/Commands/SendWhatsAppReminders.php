<?php

namespace App\Console\Commands;

use App\Models\Ordenes;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendWhatsAppReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send WhatsApp reminders for upcoming order deliveries';

    /**
     * Execute the console command.
     */
    public function handle(WhatsAppService $whatsappService)
    {
        $this->info('Buscando órdenes próximas a su entrega para enviar recordatorios...');

        // Órdenes que se entregan en las próximas 24 horas y no se les ha enviado recordatorio
        $ordenes = Ordenes::with(['cliente', 'vehiculo', 'servicio'])
            ->where('recordatorio_enviado', false)
            ->whereNotNull('fechaEntrega')
            ->where('fechaEntrega', '<=', Carbon::now()->addDay())
            ->where('fechaEntrega', '>', Carbon::now()->subDays(2)) // Para no enviar a muy atrasadas
            ->whereNotIn('status', ['Entregado', 'Cancelado'])
            ->get();

        $count = 0;

        foreach ($ordenes as $orden) {
            $cliente = $orden->cliente;
            if (!$cliente || !$cliente->telefono) {
                continue;
            }

            $telefono = $cliente->telefono;
            $nombre = explode(' ', $cliente->nombreCompleto)[0] ?? 'Cliente';
            $servicio = $orden->servicio->nombreServicio ?? 'Servicio Automotriz';
            
            $mensaje = "Hola {$nombre}, te recordamos que la entrega de tu vehículo para el servicio de *{$servicio}* está programada para pronto. Por favor contáctanos para cualquier duda. ¡Gracias por confiar en FrangyControl!";

            $this->info("Enviando recordatorio a {$nombre} ({$telefono})...");

            $success = $whatsappService->sendMessage($telefono, $mensaje);

            if ($success) {
                $orden->recordatorio_enviado = true;
                $orden->save();
                $count++;
            }
        }

        $this->info("Se enviaron {$count} recordatorios exitosamente.");
    }
}
