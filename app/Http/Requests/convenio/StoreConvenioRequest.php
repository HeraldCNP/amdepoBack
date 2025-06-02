<?php

namespace App\Http\Requests\convenio;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class StoreConvenioRequest extends FormRequest
{
    public function authorize(): bool
    {
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
            'titulo' => 'required|string|max:255|unique:convenios,titulo', // Título único
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
            'titulo.required' => 'El título del convenio es obligatorio.',
            'titulo.string' => 'El título debe ser una cadena de texto.',
            'titulo.max' => 'El título no debe exceder los :max caracteres.',
            'titulo.unique' => 'Ya existe un convenio con este título.',
            'archivoPdf.required' => 'El archivo PDF del convenio es obligatorio.',
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
