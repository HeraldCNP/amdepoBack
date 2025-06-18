<?php

namespace App\Http\Requests\imagenTuristica;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class StoreImagenTuristicaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check(); // Asegúrate de que el usuario esté autenticado
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'descripcion' => 'required|string|max:255', // <-- ¡Cambiado aquí!
            'municipio_id' => 'required|exists:municipios,id',
            'imagen_file' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
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
            'descripcion.required' => 'La descripción de la imagen es obligatoria.', // <-- Mensaje cambiado
            'descripcion.string' => 'La descripción debe ser una cadena de texto.', // <-- Mensaje cambiado
            'descripcion.max' => 'La descripción no debe exceder los :max caracteres.', // <-- Mensaje cambiado
            'municipio_id.required' => 'El municipio es obligatorio.',
            'municipio_id.exists' => 'El municipio seleccionado no es válido.',
            'imagen_file.required' => 'La imagen es obligatoria.',
            'imagen_file.image' => 'El archivo debe ser una imagen válida.',
            'imagen_file.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif.',
            'imagen_file.max' => 'La imagen no debe ser mayor de 5MB.',
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
