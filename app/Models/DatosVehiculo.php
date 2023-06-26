<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DatosVehiculo extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'datos_vehiculo';
    protected $primaryKey = "id_vehiculo";
    protected $fillable = [
        'marca'
    ];
    protected $dates = ['deleted_at'];
}
