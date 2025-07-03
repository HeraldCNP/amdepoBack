<?php

namespace App\Http\Requests\ImagenNoticia;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class StoreImagenNoticiaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'noticia_id' => 'required|exists:noticias,id',
            // CAMBIO CLAVE: 'imagen_files' ahora es un array de imágenes
            'imagen_files' => 'required|array|min:1', // Debe ser un array y contener al menos 1 archivo
            'imagen_files.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120', // Cada elemento del array debe ser una imagen
            'descripcion' => 'nullable|string|max:255', // Esta descripción se aplicará a todas las imágenes subidas en esta solicitud
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
            'imagen_files.required' => 'Debes seleccionar al menos un archivo de imagen.',
            'imagen_files.array' => 'Los archivos de imagen deben enviarse como un array.',
            'imagen_files.min' => 'Debes subir al menos una imagen.',
            'imagen_files.*.image' => 'Cada archivo debe ser una imagen válida.',
            'imagen_files.*.mimes' => 'Cada imagen debe ser de tipo: jpeg, png, jpg o gif.',
            'imagen_files.*.max' => 'Cada imagen no debe ser mayor de 5MB.',
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
