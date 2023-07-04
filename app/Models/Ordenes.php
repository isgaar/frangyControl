<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ordenes extends Model
{
    protected $table = 'ordenes';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'cliente_id',
        'vehiculo_id',
        'servicio_id',
        'tvehiculo_id',
        'id',
        'nombreCompleto',
        'telefono',
        'correo',
        'marca',
        'tipo',
        'yearVehiculo',
        'color',
        'placas',
        'kilometraje',
        'motor',
        'cilindros',
        'noSerievehiculo',
        'fechaEntrega',
        'observacionesInt',
        'recomendacionesCliente',
        'detallesOrden',
        'retiroRefacciones',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function vehiculo()
    {
        return $this->belongsTo(DatosVehiculo::class, 'vehiculo_id');
    }

    public function servicio()
    {
        return $this->belongsTo(TipoServicio::class, 'servicio_id');
    }

    public function tipoVehiculo()
    {
        return $this->belongsTo(TipoVehiculo::class, 'tvehiculo_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }
}
