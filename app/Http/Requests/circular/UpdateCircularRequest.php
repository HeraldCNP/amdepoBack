<?php

namespace App\Http\Requests\circular;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateCircularRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Asegúrate de que el usuario esté autenticado para actualizar una circular
        // Podrías añadir lógica adicional para verificar si el usuario es el creador de la circular
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Log::info('--- UpdateCircularRequest RULES (Start) ---'); // Puedes descomentar para depurar
        // Log::info('Method: ' . $this->method());
        // Log::info('Has file imagen? ' . ($this->hasFile('imagen') ? 'Yes' : 'No'));

        // if ($this->hasFile('imagen')) {
        //     $file = $this->file('imagen');
        //     Log::info('File imagen details:');
        //     Log::info('  Original Name: ' . $file->getClientOriginalName());
        //     Log::info('  Mime Type: ' . $file->getMimeType());
        //     Log::info('  Size: ' . $file->getSize() . ' bytes');
        //     Log::info('  Is Valid: ' . ($file->isValid() ? 'Yes' : 'No'));
        //     Log::info('  Error Code: ' . $file->getError());
        //     Log::info('  Error Message: ' . $file->getErrorMessage());
        // }

        return [
            'titulo' => 'required|string|max:255',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 'nullable' porque la imagen podría no cambiarse
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
            'titulo.required' => 'El título de la circular es obligatorio.',
            'titulo.string' => 'El título debe ser una cadena de texto.',
            'titulo.max' => 'El título no debe exceder los :max caracteres.',
            'imagen.image' => 'El archivo debe ser una imagen.',
            'imagen.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif.',
            'imagen.max' => 'La imagen no debe ser mayor de 5MB.',
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
