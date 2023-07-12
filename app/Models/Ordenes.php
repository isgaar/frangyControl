<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ordenes extends Model
{
    protected $table = 'ordenes';
    protected $primaryKey = 'id_ordenes';
    public $timestamps = true;

    protected $fillable = [
        'yearVehiculo',
        'color',
        'placas',
        'modelo',
        'kilometraje',
        'motor',
        'cilindros',
        'noSerievehiculo',
        'fechaEntrega',
        'observacionesInt',
        'recomendacionesCliente',
        'status',
        'detallesOrden',
        'retiroRefacciones',
        'cliente_id',
        'vehiculo_id',
        'servicio_id',
        'tvehiculo_id',
        'id',
    ];

    public function getCreatedAtAttribute($value)
    {
        // Formatear el valor de created_at como desees
        return date('Y-m-d H:i:s', strtotime($value));
    }
    public function getUpdatedAtAttribute($value)
    {
        return date('Y-m-d H:i:s', strtotime($value));
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id', 'id_cliente');
    }

    public function vehiculo()
    {
        return $this->belongsTo(DatosVehiculo::class, 'vehiculo_id', 'id_vehiculo');
    }

    public function servicio()
    {
        return $this->belongsTo(TipoServicio::class, 'servicio_id', 'id_servicio');
    }

    public function tipoVehiculo()
    {
        return $this->belongsTo(TipoVehiculo::class, 'tvehiculo_id', 'id_tvehiculo');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function fotografias()
    {
        return $this->hasMany(Fotografia::class, 'ordenes_id', 'id_ordenes');
    }
}