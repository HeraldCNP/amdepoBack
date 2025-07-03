<?php

namespace App\Http\Requests\Noticia;


use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class StoreNoticiaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Solo los usuarios autenticados pueden crear noticias
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
            'titulo' => 'required|string|max:255|unique:noticias,titulo', // Título único
            'texto' => 'required|string', // Contenido principal de la noticia
            'categoria_id' => 'required|exists:categorias,id', // Debe ser una categoría existente
            // La URL de video es opcional, debe ser una URL válida y con un máximo de 500 caracteres
            // La expresión regular intenta cubrir YouTube, Facebook y TikTok
            'video_url' => 'nullable|url|max:500',
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
            'titulo.required' => 'El título de la noticia es obligatorio.',
            'titulo.string' => 'El título debe ser una cadena de texto.',
            'titulo.max' => 'El título no debe exceder los :max caracteres.',
            'titulo.unique' => 'Ya existe una noticia con este título.',
            'texto.required' => 'El contenido de la noticia es obligatorio.',
            'texto.string' => 'El contenido de la noticia debe ser una cadena de texto.',
            'categoria_id.required' => 'La categoría de la noticia es obligatoria.',
            'categoria_id.exists' => 'La categoría seleccionada no es válida.',
            'video_url.url' => 'El campo de URL de video debe ser una URL válida.',
            'video_url.max' => 'La URL del video no debe exceder los :max caracteres.',
            'video_url.regex' => 'La URL del video debe ser de YouTube, Facebook o TikTok.',
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
