<?php

namespace App\Http\Requests\municipio;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateMunicipioRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Define tu lógica de autorización aquí.
        // Por ejemplo: return auth()->check() && auth()->user()->can('update', $this->municipio);
        return true; // Para pruebas, lo dejamos en true.
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        // Obtiene el ID del municipio de la ruta para la regla unique.
        // Asume que usas Route Model Binding (e.g., public function update(UpdateMunicipioRequest $request, Municipio $municipio))
        $municipioId = $this->route('municipio') ? $this->route('municipio')->id : null;

        return [
            // El nombre es obligatorio y único, pero debe ignorar el ID del municipio actual
            'nombre' => ['required', 'string', 'max:255', Rule::unique('municipios')->ignore($municipioId)],
            'descripcion' => ['nullable', 'string'],
            'provincia' => ['nullable', 'string', 'max:255'],
            // 'slug' se generará automáticamente si se envía 'nombre'
            'direccion' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'sitio_web' => ['nullable', 'url', 'max:255'],
            'latitud' => ['nullable', 'numeric', 'between:-90,90'],
            'longitud' => ['nullable', 'numeric', 'between:-180,180'],
            'poblacion' => ['nullable', 'integer', 'min:0'],
            'superficie' => ['nullable', 'numeric', 'min:0'],
            'historia' => ['nullable', 'string'],
            'gentilicio' => ['nullable', 'string', 'max:255'],
            'alcalde_nombre' => ['nullable', 'string', 'max:255'],
            'alcalde_foto' => ['nullable', 'string', 'max:255'],
            'circuscripcion' => ['nullable', 'string', 'max:255'],
            'comunidades' => ['nullable', 'string'],
            'aniversario' => ['nullable', 'date_format:m-d'],
            'fiestaPatronal' => ['nullable', 'date_format:m-d'],
            'ferias' => ['nullable', 'string'],
            'facebook' => ['nullable', 'url', 'max:255'],
            // 'user_id' no suele ser actualizable en una operación PUT/PATCH para un recurso ya creado,
            // pero si tu lógica de negocio lo requiere, podrías añadirlo aquí con ['nullable', 'exists:users,id']
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Regenera el slug si se ha enviado un nuevo 'nombre' en la actualización.
        if ($this->has('nombre')) {
            $this->merge([
                'slug' => Str::slug($this->nombre),
            ]);
        }
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del municipio es obligatorio.',
            'nombre.unique' => 'Ya existe otro municipio con este nombre.',
            'nombre.max' => 'El nombre no puede exceder los :max caracteres.',
            'email.email' => 'El formato del email no es válido.',
            'sitio_web.url' => 'El formato del sitio web no es válido.',
            'facebook.url' => 'El formato de la URL de Facebook no es válido.',
            'latitud.between' => 'La latitud debe estar entre -90 y 90.',
            'longitud.between' => 'La longitud debe estar entre -180 y 180.',
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
