<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoServicio extends Model
{
    use HasFactory;

    protected $table = 'tipo_servicio';
    protected $primaryKey = 'id_servicio';
    protected $fillable = [
        // Aquí coloca los nombres de los campos que deseas permitir que sean asignables masivamente (en caso de que aplique)
    'nombreServicio'
    ];
}
