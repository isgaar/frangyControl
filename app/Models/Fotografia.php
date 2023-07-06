<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Ordenes;


class Fotografia extends Model
{
    use HasFactory;

    protected $table = 'fotografias';
    protected $primaryKey = 'id';

    protected $fillable = ['ordenes_id', 'ruta'];

    public function orden()
    {
        return $this->belongsTo(Ordenes::class, 'ordenes_id', 'id_ordenes');
    }

}
