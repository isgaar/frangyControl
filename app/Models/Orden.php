<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orden extends Model
{
    use HasFactory;

    protected $table = 'orden';
    protected $primaryKey = 'no_orden';
    public $timestamps = true;

    protected $fillable = [
        'id',
        'cliente_id',
        'vehiculo_id',
        'servicio_id',
        'tvehiculo_id',
        'yearVehiculo',
        'noSerievehiculo',
        'modeloVehiculo',
        'placas',
        'color',
        'kilometraje',
        'motor',
        'cilindros',
        'observacionesInt',
        'recomendacionesCliente',
        'detallesOrden',
        'retiroRefacciones',
        'fechaRegistro',
        'fechaEntrega',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function servicio()
    {
        return $this->belongsTo(TipoServicio::class, 'servicio_id');
    }

    public function vehiculo()
    {
        return $this->belongsTo(DatosVehiculo::class, 'vehiculo_id');
    }

    public function tipoVehiculo()
    {
        return $this->belongsTo(TipoVehiculo::class, 'tvehiculo_id');
    }
}
