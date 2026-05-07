<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TipoServicio extends Model
{
    use HasFactory;

    protected $table = 'tipo_servicio';
    protected $primaryKey = 'id_servicio';
    protected $fillable = [
        'nombreServicio',
        'precio_base',
        'descuento_porcentaje',
        'descuento_inicio',
        'descuento_fin',
    ];

    protected $casts = [
        'precio_base' => 'decimal:2',
        'descuento_porcentaje' => 'decimal:2',
        'descuento_inicio' => 'date',
        'descuento_fin' => 'date',
    ];

    public function cotizaciones()
    {
        return $this->hasMany(Cotizacion::class, 'servicio_id', 'id_servicio');
    }

    public function descuentoActivo(?string $date = null): bool
    {
        if ((float) $this->descuento_porcentaje <= 0) {
            return false;
        }

        $date = $date ? Carbon::parse($date)->toDateString() : now()->toDateString();
        $starts = $this->descuento_inicio?->toDateString();
        $ends = $this->descuento_fin?->toDateString();

        return (!$starts || $starts <= $date) && (!$ends || $ends >= $date);
    }

    public function precioCotizado(?string $date = null): array
    {
        $precioBase = round((float) $this->precio_base, 2);
        $descuentoPorcentaje = $this->descuentoActivo($date) ? (float) $this->descuento_porcentaje : 0.0;
        $descuentoMonto = round($precioBase * ($descuentoPorcentaje / 100), 2);

        return [
            'precio_base' => $precioBase,
            'descuento_porcentaje' => $descuentoPorcentaje,
            'descuento_monto' => $descuentoMonto,
            'total' => max(round($precioBase - $descuentoMonto, 2), 0),
        ];
    }
}
