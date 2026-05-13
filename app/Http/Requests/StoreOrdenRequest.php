<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Cliente;
use Illuminate\Validation\Rule;

class StoreOrdenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'usar_cliente_existente' => $this->boolean('usar_cliente_existente'),
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
        $usingExistingClient = $this->boolean('usar_cliente_existente');
        $currentYear = (int) now()->format('Y') + 1;

        return [
            'usar_cliente_existente' => ['nullable', 'boolean'],
            'cliente_existente_id'   => [$usingExistingClient ? 'required' : 'nullable', 'exists:clientes,id_cliente'],
            'nombreCompleto'         => $usingExistingClient ? ['nullable'] : ['required', 'string', 'max:100', $this->uniqueClientValueRule('nombreCompleto', 'Ese cliente ya existe. Usa la opción de cliente registrado.')],
            'telefono'               => $usingExistingClient ? ['nullable'] : ['required', 'digits:10', $this->uniqueClientValueRule('telefono', 'Ese teléfono ya está registrado.')],
            'correo'                 => $usingExistingClient ? ['nullable'] : ['required', 'email', 'max:30', $this->uniqueClientValueRule('correo', 'Ese correo electrónico ya está registrado.')],
            'rfc'                    => $usingExistingClient ? ['nullable'] : ['required', 'string', 'min:12', 'max:13', $this->uniqueClientValueRule('rfc', 'Ese RFC ya está registrado.')],
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
            'fechaEntrega'           => ['required', 'date', 'after_or_equal:today'],
            'observacionesInt'       => ['required', 'string'],
            'recomendacionesCliente' => ['required', 'string'],
            'detallesOrden'          => ['required', 'string'],
            'retiroRefacciones'      => ['required', 'boolean'],
            'photo_tokens'           => ['nullable', 'array'],
            'photo_tokens.*'         => ['string', 'uuid'],
            'photos'                 => ['nullable', 'array'],
            'photos.*'               => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'cliente_existente_id.required'  => 'Selecciona un cliente registrado.',
            'cliente_existente_id.exists'    => 'El cliente seleccionado no es válido.',
            'nombreCompleto.required'        => 'Escribe el nombre completo del cliente.',
            'telefono.required'              => 'Escribe el teléfono del cliente.',
            'telefono.digits'                => 'El teléfono debe tener 10 dígitos.',
            'correo.required'                => 'Escribe el correo electrónico del cliente.',
            'correo.email'                   => 'El correo electrónico no tiene un formato válido.',
            'rfc.required'                   => 'Escribe el RFC del cliente.',
            'rfc.min'                        => 'El RFC debe tener al menos 12 caracteres.',
            'rfc.max'                        => 'El RFC no puede exceder 13 caracteres.',
            'vehiculo_id.required'           => 'Selecciona la marca de la unidad.',
            'tvehiculo_id.required'          => 'Selecciona el tipo de vehículo.',
            'servicio_id.required'           => 'Selecciona el tipo de servicio.',
            'user_id.required'               => 'Selecciona quién atenderá la orden.',
            'modelo.required'                => 'Escribe la línea o modelo de la unidad.',
            'yearVehiculo.required'          => 'Indica el año de la unidad.',
            'yearVehiculo.digits'            => 'El año debe llevar 4 dígitos.',
            'yearVehiculo.between'           => 'Ingresa un año válido para la unidad.',
            'color.required'                 => 'Escribe el color de la unidad.',
            'placas.required'                => 'Escribe las placas de la unidad.',
            'kilometraje.required'           => 'Indica el kilometraje actual.',
            'motor.required'                 => 'Escribe la información del motor.',
            'cilindros.required'             => 'Indica los cilindros de la unidad.',
            'noSerievehiculo.required'       => 'Escribe el número de serie de la unidad.',
            'fechaEntrega.required'          => 'Selecciona una fecha estimada de entrega.',
            'fechaEntrega.after_or_equal'    => 'La fecha de entrega no puede ser anterior a hoy.',
            'status.required'                => 'Selecciona el estado de la orden.',
            'observacionesInt.required'      => 'Agrega las observaciones internas.',
            'recomendacionesCliente.required'=> 'Agrega las recomendaciones del cliente.',
            'detallesOrden.required'         => 'Agrega los detalles del servicio.',
            'retiroRefacciones.required'     => 'Indica si el cliente retira refacciones.',
            'photo_tokens.*.uuid'            => 'Una fotografía temporal no se pudo validar. Vuelve a seleccionarla.',
            'photos.*.image'                 => 'Cada archivo debe ser una imagen.',
            'photos.*.mimes'                 => 'Las fotografías deben estar en formato JPG o PNG.',
            'photos.*.max'                   => 'Cada fotografía puede pesar hasta 2 MB.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nombreCompleto'         => 'nombre completo',
            'telefono'               => 'teléfono',
            'correo'                 => 'correo electrónico',
            'rfc'                    => 'RFC',
            'vehiculo_id'            => 'marca',
            'tvehiculo_id'           => 'tipo de vehículo',
            'servicio_id'            => 'tipo de servicio',
            'user_id'                => 'atiende',
            'modelo'                 => 'línea',
            'yearVehiculo'           => 'año',
            'color'                  => 'color',
            'placas'                 => 'placas',
            'kilometraje'            => 'kilometraje',
            'motor'                  => 'motor',
            'cilindros'              => 'cilindros',
            'noSerievehiculo'        => 'número de serie',
            'fechaEntrega'           => 'fecha de entrega',
            'observacionesInt'       => 'observaciones internas',
            'recomendacionesCliente' => 'recomendaciones del cliente',
            'detallesOrden'          => 'detalles del servicio',
            'retiroRefacciones'      => 'retiro de refacciones',
        ];
    }

    private function uniqueClientValueRule(string $field, string $message): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail) use ($field, $message): void {
            if (Cliente::existsWithSensitiveValue($field, (string) $value)) {
                $fail($message);
            }
        };
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
