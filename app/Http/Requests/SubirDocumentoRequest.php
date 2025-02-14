<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SubirDocumentoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Si no necesitas autorización especial, puedes dejarlo en true
    }

    public function rules(): array
    {
        return [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'gestion' => 'string',
            'archivo' => 'required|file|mimes:pdf|max:2048', // Validar el archivo PDF
        ];
    }

    public function messages(): array
    {
        return [
            'titulo.required' => 'El título del documento es obligatorio.',
            'titulo.max' => 'El título del documento no puede tener más de 255 caracteres.',
            'descripcion.string' => 'La descripción debe ser una cadena de texto.',
            'gestion.string' => 'La gestión debe ser una cadena de texto.',
            'archivo.required' => 'El archivo es obligatorio.',
            'archivo.file' => 'El archivo debe ser un archivo.',
            'archivo.mimes' => 'El archivo debe ser de tipo PDF.',
            'archivo.max' => 'El archivo no puede pesar más de 2048 KB.',
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
