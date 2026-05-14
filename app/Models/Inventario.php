<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    use HasFactory;

    protected $table = 'inventarios';
    protected $primaryKey = 'id_inventario';
    public $timestamps = true;

    protected $fillable = [
        'codigo_barras',
        'nombre',
        'descripcion',
        'precio_venta',
        'costo',
        'cantidad_stock',
    ];

    public function ordenes()
    {
        return $this->belongsToMany(Ordenes::class, 'orden_inventario', 'inventario_id', 'orden_id')
            ->withPivot('cantidad', 'precio_unitario')
            ->withTimestamps();
    }
}
