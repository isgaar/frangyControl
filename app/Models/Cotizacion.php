<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cotizacion extends Model
{
    protected $table = 'cotizaciones';
    protected $primaryKey = 'id_cotizacion';

    protected $fillable = [
        'cliente_id',
        'servicio_id',
        'user_id',
        'folio',
        'precio_base',
        'descuento_porcentaje',
        'descuento_monto',
        'total',
        'vigencia',
        'estado',
        'notas',
    ];

    protected $casts = [
        'precio_base' => 'decimal:2',
        'descuento_porcentaje' => 'decimal:2',
        'descuento_monto' => 'decimal:2',
        'total' => 'decimal:2',
        'vigencia' => 'date',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id', 'id_cliente');
    }

    public function servicio()
    {
        return $this->belongsTo(TipoServicio::class, 'servicio_id', 'id_servicio');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
