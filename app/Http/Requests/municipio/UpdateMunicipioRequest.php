<?php

namespace App\Http\Requests\municipio;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log; // Mantén esto para tus logs en rules()

// No necesitas Symfony\Component\HttpFoundation\File\UploadedFile aquí
// si el middleware se encarga de parsear los archivos por ti.
// use Symfony\Component\HttpFoundation\File\UploadedFile;

class UpdateMunicipioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }


    public function rules(): array
    {



        $municipio = $this->route('municipio');
        $municipioId = $municipio ? $municipio->id : null;
        return [
            'nombre' => ['required', 'string', 'max:255', Rule::unique('municipios')->ignore($municipioId)],
            'descripcion' => ['nullable', 'string'],
            'provincia' => ['nullable', 'string', 'max:255'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'sitio_web' => ['nullable', 'max:255'],
            'latitud' => ['nullable', 'numeric', 'between:-90,90'],
            'longitud' => ['nullable', 'numeric', 'between:-180,180'],
            'mapa_imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // (max 5MB)
            'poblacion' => ['nullable', 'integer', 'min:0'],
            'superficie' => ['nullable', 'numeric', 'min:0'],
            'historia' => ['nullable', 'string'],
            'gentilicio' => ['nullable', 'string', 'max:255'],
            'alcalde_nombre' => ['nullable', 'string', 'max:255'],
            'alcalde_foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'circuscripcion' => ['nullable', 'string', 'max:255'],
            'comunidades' => ['nullable', 'string'],
            'aniversario' => ['nullable', 'string'],
            'fiestaPatronal' => ['nullable', 'string'],
            'ferias' => ['nullable', 'string'],
            'facebook' => ['nullable', 'url', 'max:255'],
            '_method' => ['sometimes', 'string', Rule::in(['PUT', 'PATCH'])],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('nombre')) {
            $this->merge([
                'slug' => Str::slug($this->nombre),
            ]);
        }
    }

    public function messages(): array
    {
        return [
            // ... otros mensajes ...
            'alcalde_foto.image' => 'El archivo para la foto del alcalde debe ser una imagen válida.',
            'alcalde_foto.mimes' => 'La foto del alcalde debe ser un archivo de tipo: :values (jpeg, png, jpg, gif, svg).',
            'alcalde_foto.max' => 'La foto del alcalde no debe exceder los :max kilobytes de tamaño.',
            // ...
            'nombre.required' => 'El nombre del municipio es obligatorio.',
            'nombre.unique' => 'Ya existe otro municipio con este nombre.',
            'nombre.max' => 'El nombre no puede exceder los :max caracteres.',
            'email.email' => 'El formato del email no es válido.',
            // 'sitio_web.url' => 'El formato del sitio web no es válido.',
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
