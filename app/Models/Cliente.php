<?php

namespace App\Models;

use App\Support\ClientDataSecurity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Cliente extends Model
{
    protected $table = 'clientes';
    protected $primaryKey = 'id_cliente';
    protected $fillable = ['nombreCompleto', 'telefono', 'correo', 'rfc'];
    protected $hidden = [
        'nombreCompleto_hash',
        'telefono_hash',
        'correo_hash',
        'rfc_hash',
    ];
    public $timestamps = true;

    protected function nombreCompleto(): Attribute
    {
        return $this->sensitiveAttribute('nombreCompleto');
    }

    protected function telefono(): Attribute
    {
        return $this->sensitiveAttribute('telefono');
    }

    protected function correo(): Attribute
    {
        return $this->sensitiveAttribute('correo');
    }

    protected function rfc(): Attribute
    {
        return $this->sensitiveAttribute('rfc');
    }

    public static function sensitiveHash(string $field, ?string $value): ?string
    {
        return ClientDataSecurity::hash($field, $value);
    }

    public static function sensitiveHashColumn(string $field): string
    {
        return ClientDataSecurity::hashColumn($field);
    }

    public static function existsWithSensitiveValue(string $field, ?string $value, ?int $ignoreId = null): bool
    {
        $hash = static::sensitiveHash($field, $value);

        if (!$hash) {
            return false;
        }

        return static::query()
            ->where(static::sensitiveHashColumn($field), $hash)
            ->when($ignoreId, fn (Builder $query) => $query->where('id_cliente', '!=', $ignoreId))
            ->exists();
    }

    public static function matchingSearchIds(string $search): Collection
    {
        $search = trim($search);

        if ($search === '') {
            return collect();
        }

        return static::query()
            ->get(['id_cliente', 'nombreCompleto', 'telefono', 'correo', 'rfc'])
            ->filter(fn (Cliente $cliente) => ClientDataSecurity::matchesSearch([
                $cliente->nombreCompleto,
                $cliente->telefono,
                $cliente->correo,
                $cliente->rfc,
            ], $search))
            ->pluck('id_cliente')
            ->values();
    }

    private function sensitiveAttribute(string $field): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => ClientDataSecurity::decrypt($value),
            set: function (?string $value) use ($field) {
                $prepared = ClientDataSecurity::prepareForStorage($field, $value);

                return [
                    $field => ClientDataSecurity::encrypt($prepared),
                    ClientDataSecurity::hashColumn($field) => ClientDataSecurity::hash($field, $prepared),
                ];
            }
        );
    }
}
