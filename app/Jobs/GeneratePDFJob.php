<?php

namespace App\Jobs;

use App\Models\Cliente;
use App\Models\DatosVehiculo;
use App\Models\Ordenes;
use App\Models\TipoServicio;
use App\Models\TipoVehiculo;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PDF;
use Illuminate\Support\Facades\Storage;


class GeneratePDFJob implements ShouldQueue
{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $orderId;

    public function __construct($orderId)
    {
        $this->orderId = $orderId;
        $this->onQueue('pdf'); // Coloca el job en la cola dedicada para los trabajos de generación de PDF
    }

    public function handle()
    {
        $orden = Ordenes::findOrFail($this->orderId);
        $datosVehiculo = DatosVehiculo::all();
        $tiposServicio = TipoServicio::all();
        $tiposVehiculo = TipoVehiculo::all();
        $users = User::all();
        $cliente_id = Cliente::all();
    
        $pdf = PDF::loadView('admin.ordenes.export', compact('orden', 'datosVehiculo', 'tiposServicio', 'tiposVehiculo', 'users', 'cliente_id'));

        $filename = 'orden_' . Carbon::now()->format('Ymd_His') . '.pdf';

        // Guardar el PDF en el almacenamiento
        $pdf->save(storage_path('app/public/' . $filename));

        // Descargar el PDF
        return response()->download(storage_path('app/public/' . $filename))->deleteFileAfterSend();
    }
}
