<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreUserProfileRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'], // Validación única para el email
            'password' => ['required', 'string', 'min:8'], 
            'lastName' => ['required', 'string', 'max:255'], 
            'ci' => ['required', 'string', 'min:7', 'max:12'], // Ejemplo de validación para ci
            'phone' => ['nullable', 'string', 'max:11'], // Ejemplo de validación para teléfono
            'address' => ['nullable', 'string'],
        ];
    }

      /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.unique' => 'Este correo electrónico ya está en uso.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos :min caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'lastName.required' => 'Los Apellidos son obligatorios.',
            'ci.required' => 'El Carnet de Identidad es obligatorio.',
            'ci.min' => 'El Carnet de Identidad debe tener al menos :min caracteres.',
            'ci.max' => 'El Carnet de Identidad no debe tener más de :max caracteres.',
            'phone.max' => 'El teléfono no debe tener más de :max caracteres.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Error de validación',
            'errors' => $validator->errors()
        ], 422));
    }

}
