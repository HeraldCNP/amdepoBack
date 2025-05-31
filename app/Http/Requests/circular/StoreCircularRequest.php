<?php

namespace App\Http\Requests\circular;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class StoreCircularRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Asegúrate de que el usuario esté autenticado para crear una circular
        // return Auth::check();
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
            'titulo' => 'required|string|max:255',
            'imagenCircular' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // Máximo 5MB
            // user_id no se valida aquí, se asigna automáticamente en el controlador
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'titulo.required' => 'El título de la circular es obligatorio.',
            'titulo.string' => 'El título debe ser una cadena de texto.',
            'titulo.max' => 'El título no debe exceder los :max caracteres.',
            'imagenCircular.required' => 'La imagen de la circular es obligatoria.',
            'imagenCircular.image' => 'El archivo debe ser una imagen.',
            'imagenCircular.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif.',
            'imagenCircular.max' => 'La imagen no debe ser mayor de 5MB.',
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
