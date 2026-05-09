<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CotizacionConcepto extends Model
{
    protected $table = 'cotizacion_conceptos';
    protected $primaryKey = 'id_concepto';

    protected $fillable = [
        'cotizacion_id',
        'tipo',
        'descripcion',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'orden',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class, 'cotizacion_id', 'id_cotizacion');
    }
}
