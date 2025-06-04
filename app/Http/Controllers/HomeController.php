<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\Municipio;
use App\Models\Publicacion;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function allDocumentos(): JsonResponse // O podrías usar un método llamado listarPublicos
    {
        try {
            $documentos = Documento::with(['municipio' => function ($query) {
                $query->select('id', 'nombre');
            }])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($documentos);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Error al listar los documentos.', 'message' => $e->getMessage()], 500);
        }
    }

    public function allPublicaciones(): JsonResponse // O podrías usar un método llamado listarPublicos
    {
        try {
            $publicaciones = Publicacion::orderBy('created_at', 'desc') // Ordena por fecha de creación, las más nuevas primero
                ->take(3) // Limita los resultados a 3
                ->get(); // Ejecuta la consulta y obtiene los resultados


            return response()->json($publicaciones);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Error al listar los publicaciones.', 'message' => $e->getMessage()], 500);
        }
    }

    public function getMunicipio(string $slug): JsonResponse
    {
        try {
            $municipio = Municipio::where('slug', $slug)->firstOrFail();
            return response()->json($municipio, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Municipio no encontrado.'], 404);
        } catch (Exception $e) {
            Log::error('Error al obtener municipio por slug: ' . $e->getMessage(), ['exception' => $e, 'slug' => $slug]);
            return response()->json([
                'message' => 'Ocurrió un error al intentar obtener el municipio.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
