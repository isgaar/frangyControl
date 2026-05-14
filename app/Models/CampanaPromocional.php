<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampanaPromocional extends Model
{
    use HasFactory;

    protected $table = 'campanas_promocionales';
    protected $primaryKey = 'id_campana';

    protected $fillable = [
        'nombre',
        'mensaje',
        'servicio_id',
        'fecha_programada',
        'estado',
    ];

    protected $casts = [
        'fecha_programada' => 'datetime',
    ];

    public function servicio()
    {
        return $this->belongsTo(TipoServicio::class, 'servicio_id', 'id_servicio');
    }
}
