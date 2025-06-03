<?php

namespace App\Http\Requests\municipio;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StoreMunicipioRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        // Define aquí la lógica de autorización.
        // Por ejemplo, solo los usuarios autenticados pueden crear municipios.
        // return auth()->check();
        // return true;
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // dd($this->all());

        return [
            'nombre' => ['required', 'string', 'max:255', 'unique:municipios,nombre'],
            'descripcion' => ['nullable', 'string'], // 'text' en DB se mapea a 'string' en validación
            'provincia' => ['nullable', 'string', 'max:255'],
            // 'slug' se generará automáticamente, no se valida directamente de la entrada
            'direccion' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:255'], // Asumiendo que el 'string' en DB es suficiente, ajusta max si es necesario
            'email' => ['nullable', 'email', 'max:255'],
            'sitio_web' => ['nullable', 'max:255'],
            'latitud' => ['nullable', 'numeric', 'between:-90,90'], // 'decimal' en DB
            'longitud' => ['nullable', 'numeric', 'between:-180,180'], // 'decimal' en DB
            'mapa_imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // (max 5MB)
            'poblacion' => ['nullable', 'integer', 'min:0'], // 'integer' en DB
            'superficie' => ['nullable', 'numeric', 'min:0'], // 'decimal' en DB
            'historia' => ['nullable', 'string'], // 'text' en DB se mapea a 'string' en validación
            'gentilicio' => ['nullable', 'string', 'max:255'],
            'alcalde_nombre' => ['nullable', 'string', 'max:255'],
            'alcalde_foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'circuscripcion' => ['nullable', 'string', 'max:255'],
            'comunidades' => ['nullable', 'string'], // Asumiendo que puede ser un texto largo
            'aniversario' => ['nullable', 'string'], // 'string' en DB, validamos formato MM-DD
            'fiestaPatronal' => ['nullable', 'string'], // 'string' en DB, validamos formato MM-DD
            'ferias' => ['nullable', 'string'], // 'text' en DB se mapea a 'string' en validación
            'facebook' => ['nullable', 'url', 'max:255'],
        ];
    }

    protected function prepareForValidation(): void
    {
        // Genera el slug automáticamente antes de la validación.
        // Esto asegura que el slug esté presente y sea válido para la DB.
        $this->merge([
            'slug' => Str::slug($this->nombre),
        ]);
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'alcalde_foto.image' => 'El archivo debe ser una imagen.',
            'alcalde_foto.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif, svg.',
            'alcalde_foto.max' => 'La imagen no debe exceder los 2MB de tamaño.',
            'nombre.required' => 'El nombre del municipio es obligatorio.',
            'nombre.unique' => 'Ya existe un municipio con este nombre.',
            'nombre.max' => 'El nombre no puede exceder los :max caracteres.',
            'email.email' => 'El formato del email no es válido.',
            'sitio_web.url' => 'El formato del sitio web no es válido.',
            'facebook.url' => 'El formato de la URL de Facebook no es válido.',
            'latitud.between' => 'La latitud debe estar entre -90 y 90.',
            'longitud.between' => 'La longitud debe estar entre -180 y 180.',
            'mapa_imagen.image' => 'El archivo del mapa debe ser una imagen.',
            'mapa_imagen.mimes' => 'El archivo del mapa debe ser de tipo: jpeg, png, jpg, gif.',
            'mapa_imagen.max' => 'El archivo del mapa no debe ser mayor de 5MB.',
            'poblacion.integer' => 'La población debe ser un número entero.',
            'poblacion.min' => 'La población no puede ser negativa.',
            'superficie.numeric' => 'La superficie debe ser un valor numérico.',
            'superficie.min' => 'La superficie no puede ser negativa.',
            'aniversario.date_format' => 'El formato del aniversario debe ser MM-DD (ej. 10-11).',
            'fiestaPatronal.date_format' => 'El formato de la fiesta patronal debe ser MM-DD (ej. 08-15).',
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
