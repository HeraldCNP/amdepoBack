<?php

namespace App\Http\Controllers;

use App\Models\Circular;
use App\Models\Convenio;
use App\Models\Documento;
use App\Models\Municipio;
use App\Models\Noticia;
use App\Models\Proyecto;
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

    public function getNoticias(Request $request): JsonResponse // O podrías usar un método llamado listarPublicos
    {
        try {
            $perPage = request()->query('per_page', 9);
            $searchTerm = request()->query('search');
            $limit = request()->query('limit'); // <-- Nuevo: Obtiene el parámetro 'limit'

            $query = Noticia::with(['user', 'categoria', 'imagenesNoticias'])
                ->orderBy('created_at', 'desc'); // Ya tienes el orden descendente, lo cual es bueno para "últimas"

            // Aplica el filtro de búsqueda si se proporciona un término
            if ($searchTerm) {
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('titulo', 'like', '%' . $searchTerm . '%')
                        ->orWhere('texto', 'like', '%' . $searchTerm . '%')
                        ->orWhereHas('categoria', function ($q2) use ($searchTerm) {
                            $q2->where('nombre', 'like', '%' . $searchTerm . '%');
                        })
                        ->orWhereHas('user', function ($q3) use ($searchTerm) {
                            $q3->where('name', 'like', '%' . $searchTerm . '%');
                        });
                });
            }

            // <-- Nuevo: Maneja el parámetro 'limit'
            if ($limit) {
                $noticias = $query->limit($limit)->get(); // Si hay límite, usa get() en lugar de paginate()
            } else {
                $noticias = $query->paginate($perPage); // Si no hay límite, usa paginación normal
            }

            return response()->json($noticias);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Error al listar las noticias.', 'message' => $e->getMessage()], 500);
        }
    }



    public function getMunicipio(string $slug): JsonResponse
    {
        try {
            $municipio = Municipio::where('slug', $slug)->with('imagenesTuristicas')->firstOrFail();
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


    public function getCirculares(Request $request)
    {

        try {
            $perPage = $request->input('per_page', 9); // Por defecto 9 ítems por página
            $circulares = Circular::orderBy('created_at', 'DESC') // Las más nuevas primero
                ->paginate($perPage);

            return response()->json($circulares);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Error al listar las circulares.', 'message' => $e->getMessage()], 500);
        }
    }


    public function getConvenios(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 9); // Por defecto 9 ítems por página
            $convenios = Convenio::orderBy('created_at', 'DESC') // Las más nuevas primero
                ->paginate($perPage);

            return response()->json($convenios);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Error al listar los convenios.', 'message' => $e->getMessage()], 500);
        }
    }

    public function getProyectos(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 9); // Por defecto 9 ítems por página
            $proyectos = Proyecto::orderBy('created_at', 'DESC') // Las más nuevas primero
                ->paginate($perPage);

            return response()->json($proyectos);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Error al listar los proyectos.', 'message' => $e->getMessage()], 500);
        }
    }
}
