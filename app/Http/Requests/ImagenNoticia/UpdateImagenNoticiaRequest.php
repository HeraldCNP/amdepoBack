<?php

namespace App\Http\Requests\ImagenNoticia;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class UpdateImagenNoticiaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Solo los usuarios autenticados pueden actualizar imágenes de noticias
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'noticia_id' => 'required|exists:noticias,id', // El ID de la noticia debe seguir siendo válido
            'imagen_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // La imagen es opcional al actualizar
            'descripcion' => 'nullable|string|max:255', // Descripción opcional
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
            'noticia_id.required' => 'El ID de la noticia es obligatorio.',
            'noticia_id.exists' => 'La noticia asociada no es válida.',
            'imagen_file.image' => 'El archivo debe ser una imagen válida.',
            'imagen_file.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg o gif.',
            'imagen_file.max' => 'La imagen no debe ser mayor de 5MB.',
            'descripcion.string' => 'La descripción debe ser una cadena de texto.',
            'descripcion.max' => 'La descripción no debe exceder los :max caracteres.',
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
