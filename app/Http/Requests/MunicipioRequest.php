<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class MunicipioRequest extends FormRequest
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
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'sitio_web' => 'nullable|url|max:255',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
            'poblacion' => 'nullable|integer',
            'superficie' => 'nullable|numeric',
            'historia' => 'nullable|string',
            'gentilicio' => 'nullable|string|max:255',
            'alcalde_nombre' => 'nullable|string|max:255',
            'alcalde_foto' => 'nullable|string|max:255',
            'alcalde_descripcion' => 'nullable|string',
            'slug' => 'unique:municipios,slug',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del municipio es obligatorio.',
            'nombre.max' => 'El nombre del municipio no puede tener más de 255 caracteres.',
            'descripcion.string' => 'La descripción debe ser una cadena de texto.',
            'direccion.max' => 'La dirección no puede tener más de 255 caracteres.',
            'telefono.max' => 'El teléfono no puede tener más de 20 caracteres.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'email.max' => 'El correo electrónico no puede tener más de 255 caracteres.',
            'sitio_web.url' => 'El sitio web debe ser una URL válida.',
            'sitio_web.max' => 'El sitio web no puede tener más de 255 caracteres.',
            'latitud.numeric' => 'La latitud debe ser un número.',
            'longitud.numeric' => 'La longitud debe ser un número.',
            'poblacion.integer' => 'La población debe ser un número entero.',
            'superficie.numeric' => 'La superficie debe ser un número.',
            'historia.string' => 'La historia debe ser una cadena de texto.',
            'gentilicio.max' => 'El gentilicio no puede tener más de 255 caracteres.',
            'alcalde_nombre.max' => 'El nombre del alcalde no puede tener más de 255 caracteres.',
            'alcalde_foto.max' => 'La ruta de la foto del alcalde no puede tener más de 255 caracteres.',
            'alcalde_descripcion.string' => 'La descripción del alcalde debe ser una cadena de texto.',
            'slug.unique' => 'El slug debe de ser unico',
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
