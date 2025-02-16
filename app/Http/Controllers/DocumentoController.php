<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubirDocumentoRequest;
use App\Models\Documento;
use App\Models\Municipio;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str; // Importa la clase Str para generar slugs y cadenas aleatorias

class DocumentoController extends Controller
{
    public function subir(SubirDocumentoRequest $request, $slug)
    {
        try {
            $validatedData = $request->validated();


            // Busca el municipio por slug
            $municipio = Municipio::where('slug', $slug)->firstOrFail();

            $archivo = $request->file('archivo');

            // Genera un nombre de archivo personalizado con una cadena aleatoria
            $fecha = now()->format('YmdHis');
            $tituloSlug = Str::slug($validatedData['titulo']);
            $cadenaAleatoria = Str::random(5); // Genera una cadena aleatoria de 5 caracteres
            $nombreArchivo = $tituloSlug . '-' . $fecha . '-' . $cadenaAleatoria . '.' . $archivo->getClientOriginalExtension(); // Concatena la cadena aleatoria

            // Guarda el archivo con el nombre personalizado
            $rutaArchivo = $archivo->storeAs('documentos/' . $municipio->slug, $nombreArchivo, 'public');

            $documento = Documento::create([
                'municipio_id' => $municipio->id,
                'gestion' => $validatedData['gestion'],
                'titulo' => $validatedData['titulo'],
                'descripcion' => $validatedData['descripcion'] ?? null,
                'ruta_archivo' => $rutaArchivo,
                'tipo_archivo' => $archivo->getClientMimeType(),
                'user_id' => auth()->id(),
            ]);

            return response()->json($documento, 201);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Error al subir el documento.', 'message' => $e->getMessage()], 500);
        }
    }

    public function listarTodos(): JsonResponse
    {
        try {
            $documentos = Documento::with(['municipio'])
                ->orderBy('created_at', 'desc')
                ->get();


            return response()->json($documentos);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Error al listar los documentos.', 'message' => $e->getMessage()], 500);
        }
    }

    public function listar($municipioId): JsonResponse
    {
        try {
            $documentos = Documento::where('municipio_id', $municipioId)->get();
            return response()->json($documentos);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Error al listar los documentos.', 'message' => $e->getMessage()], 500);
        }
    }

    public function eliminar($id): JsonResponse
    {
        try {
            $documento = Documento::findOrFail($id);

            Storage::disk('public')->delete($documento->ruta_archivo);

            $documento->delete();

            return response()->json(null, 204);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Documento no encontrado.'], 404);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Error al eliminar el documento.', 'message' => $e->getMessage()], 500);
        }
    }
}
