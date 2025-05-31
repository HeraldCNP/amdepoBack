<?php

namespace App\Http\Requests\proyecto;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class StoreProyectoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function authorize(): bool
    {
        return Auth::id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'titulo' => 'required|string|max:255|unique:proyectos,titulo',
            'archivoPdf' => 'required|file|mimes:pdf|max:10240', // Máximo 10MB (10240 KB)
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
            'titulo.required' => 'El título del proyecto es obligatorio.',
            'titulo.unique' => 'Ya existe otro proyecto con este título.',
            'titulo.string' => 'El título debe ser una cadena de texto.',
            'titulo.max' => 'El título no debe exceder los :max caracteres.',
            'archivoPdf.required' => 'El archivo PDF del proyecto es obligatorio.',
            'archivoPdf.file' => 'El campo debe ser un archivo.',
            'archivoPdf.mimes' => 'El archivo debe ser de tipo PDF.',
            'archivoPdf.max' => 'El archivo no debe ser mayor de 10MB.',
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
