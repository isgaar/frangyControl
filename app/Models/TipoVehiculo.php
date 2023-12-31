<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoVehiculo extends Model
{
    use HasFactory;
    protected $table = 'tipo_vehiculo';

    protected $primaryKey = 'id_tvehiculo';

    protected $fillable = [
        'tipo',
    ];
}
