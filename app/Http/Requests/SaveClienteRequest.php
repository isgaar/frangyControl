<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Cliente;
use App\Support\ClientDataSecurity;

class SaveClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nombreCompleto' => ClientDataSecurity::prepareForStorage('nombreCompleto', $this->input('nombreCompleto')),
            'telefono'       => ClientDataSecurity::prepareForStorage('telefono', $this->input('telefono')),
            'correo'         => ClientDataSecurity::prepareForStorage('correo', $this->input('correo')),
            'rfc'            => ClientDataSecurity::prepareForStorage('rfc', $this->input('rfc')),
        ]);
    }

    public function rules(): array
    {
        $ignoreId = $this->route('id_cliente');

        return [
            'nombreCompleto' => ['required', 'string', 'max:100', $this->uniqueClientValueRule('nombreCompleto', 'Este nombre ya existe, por favor ingresa uno nuevo.', $ignoreId ? (int) $ignoreId : null)],
            'telefono'       => ['required', 'digits:10', $this->uniqueClientValueRule('telefono', 'Este teléfono ya está registrado.', $ignoreId ? (int) $ignoreId : null)],
            'correo'         => ['required', 'email', 'max:30', $this->uniqueClientValueRule('correo', 'Este correo ya está registrado.', $ignoreId ? (int) $ignoreId : null)],
            'rfc'            => ['required', 'string', 'min:12', 'max:13', $this->uniqueClientValueRule('rfc', 'Este RFC ya está registrado.', $ignoreId ? (int) $ignoreId : null)],
        ];
    }

    public function messages(): array
    {
        return [
            'nombreCompleto.required' => 'El campo Nombre es requerido.',
            'telefono.required'       => 'El campo Teléfono es requerido.',
            'telefono.digits'         => 'El teléfono debe tener 10 dígitos.',
            'correo.required'         => 'El campo Correo electrónico es requerido.',
            'correo.email'            => 'El correo electrónico no tiene un formato válido.',
            'rfc.required'            => 'El rfc es requerido.',
            'rfc.min'                 => 'El RFC debe tener al menos 12 caracteres.',
            'rfc.max'                 => 'El RFC no puede exceder 13 caracteres.',
        ];
    }

    private function uniqueClientValueRule(string $field, string $message, ?int $ignoreId = null): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail) use ($field, $message, $ignoreId): void {
            if (Cliente::existsWithSensitiveValue($field, (string) $value, $ignoreId)) {
                $fail($message);
            }
        };
    }
}
