<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Ordenes;

class UpdateOrdenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nombreCompleto'         => $this->filled('nombreCompleto') ? preg_replace('/\s+/', ' ', trim($this->input('nombreCompleto'))) : null,
            'telefono'               => $this->filled('telefono') ? preg_replace('/\D+/', '', $this->input('telefono')) : null,
            'correo'                 => $this->filled('correo') ? strtolower(trim($this->input('correo'))) : null,
            'rfc'                    => $this->filled('rfc') ? strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $this->input('rfc'))) : null,
            'yearVehiculo'           => $this->filled('yearVehiculo') ? preg_replace('/\D+/', '', $this->input('yearVehiculo')) : null,
            'color'                  => $this->filled('color') ? preg_replace('/\s+/', ' ', trim($this->input('color'))) : null,
            'placas'                 => $this->filled('placas') ? strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $this->input('placas'))) : null,
            'kilometraje'            => $this->filled('kilometraje') ? preg_replace('/[^0-9.]/', '', $this->input('kilometraje')) : null,
            'motor'                  => $this->filled('motor') ? strtoupper(preg_replace('/[^A-Za-z0-9.]/', '', $this->input('motor'))) : null,
            'cilindros'              => $this->filled('cilindros') ? preg_replace('/[^0-9.]/', '', $this->input('cilindros')) : null,
            'noSerievehiculo'        => $this->filled('noSerievehiculo') ? strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $this->input('noSerievehiculo'))) : null,
            'retiroRefacciones'      => (bool) $this->input('retiroRefacciones'),
        ]);
        
        if ($this->filled('fechaEntrega')) {
            $this->merge(['fechaEntrega' => $this->normalizeDate($this->input('fechaEntrega'))]);
        }
    }

    public function rules(): array
    {
        $currentYear = (int) now()->format('Y') + 1;

        return [
            'nombreCompleto'         => ['required', 'string', 'max:100'],
            'telefono'               => ['required', 'digits:10'],
            'correo'                 => ['required', 'email', 'max:30'],
            'rfc'                    => ['required', 'string', 'min:12', 'max:13'],
            'vehiculo_id'            => ['required', 'exists:datos_vehiculo,id_vehiculo'],
            'tvehiculo_id'           => ['required', 'exists:tipo_vehiculo,id_tvehiculo'],
            'servicio_id'            => ['required', 'exists:tipo_servicio,id_servicio'],
            'user_id'                => ['required', 'exists:users,id'],
            'modelo'                 => ['required', 'string', 'max:100'],
            'yearVehiculo'           => ['required', 'integer', 'digits:4', 'between:1900,' . $currentYear],
            'color'                  => ['required', 'string', 'max:30'],
            'placas'                 => ['required', 'string', 'max:7'],
            'kilometraje'            => ['required', 'numeric', 'min:0'],
            'motor'                  => ['required', 'string', 'max:10'],
            'cilindros'              => ['required', 'numeric', 'min:1'],
            'noSerievehiculo'        => ['required', 'string', 'min:5', 'max:17'],
            'status'                 => ['required', Rule::in(['en proceso', 'finalizada', 'cancelada'])],
            'fechaEntrega'           => ['required', 'date'],
            'observacionesInt'       => ['required', 'string'],
            'recomendacionesCliente' => ['required', 'string'],
            'detallesOrden'          => ['required', 'string'],
            'retiroRefacciones'      => ['required', 'boolean'],
            'photo_tokens'           => ['nullable', 'array'],
            'photo_tokens.*'         => ['string', 'uuid'],
            'photos'                 => ['nullable', 'array'],
            'photos.*'               => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'delete_photo_ids'       => ['nullable', 'array'],
            'delete_photo_ids.*'     => ['integer'],
        ];
    }
    
    private function normalizeDate(?string $date): ?string
    {
        if (!$date) return null;

        foreach (['Y-m-d', 'Y/m/d', 'd/m/Y'] as $format) {
            try {
                return \Carbon\Carbon::createFromFormat($format, $date)->format('Y-m-d');
            } catch (\Throwable $exception) {
                continue;
            }
        }

        return $date;
    }
}
