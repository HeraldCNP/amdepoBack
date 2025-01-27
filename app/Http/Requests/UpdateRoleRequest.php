<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'name' => 'required', // El nombre es obligatorio.
            'permissions' => 'array', // Los permisos deben ser un array.
            'permissions.*' => 'exists:permissions,name', // Cada permiso debe existir en la tabla 'permissions'.
        ];
    }
        public function messages()
    {
        return [
            'name.required' => 'El nombre del rol es obligatorio.',
            'permissions.array' => 'Los permisos deben ser un array.',
            'permissions.*.exists' => 'Uno o más permisos no existen.',
        ];
    }
}
