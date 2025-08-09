<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Circular;
use App\Models\Convenio;
use App\Models\Documento;
use App\Models\Municipio;
use App\Models\Noticia;
use App\Models\Proyecto;
use App\Models\Publicacion;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

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

    public function getNoticias(Request $request): JsonResponse
    {
        try {
            $perPage = request()->query('per_page', 9);
            $searchTerm = request()->query('search');
            $limit = request()->query('limit');
            // <-- Nuevo: Obtiene el parámetro 'categoria' de la solicitud
            $categorySlug = request()->query('categoria');

            $query = Noticia::with(['user', 'categoria', 'imagenesNoticias'])
                ->orderBy('created_at', 'desc');

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

            // <-- Nuevo: Aplica el filtro de categoría si se proporciona el slug
            if ($categorySlug) {
                $query->whereHas('categoria', function ($q) use ($categorySlug) {
                    $q->where('slug', $categorySlug);
                });
            }

            // Aplica el límite o la paginación según corresponda
            if ($limit) {
                $noticias = $query->limit($limit)->get();
            } else {
                $noticias = $query->paginate($perPage);
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

    public function getNoticiaForSlug($slug): JsonResponse // Asegúrate de que el parámetro se llame $slug
    {
        try {
            $noticia = Noticia::with(['user', 'categoria', 'imagenesNoticias'])
                ->where('slug', $slug) // Buscar por slug
                ->firstOrFail(); // Lanza 404 si no se encuentra

            return response()->json(['data' => $noticia], 200);
        } catch (ModelNotFoundException $e) {
            // Si la noticia no se encuentra, devuelve un 404
            return response()->json(['message' => 'Noticia no encontrada.'], 404);
        } catch (Exception $e) {
            // Para cualquier otro error inesperado
            Log::error('Error al obtener noticia por slug: ' . $e->getMessage(), ['exception' => $e, 'slug' => $slug]);
            return response()->json([
                'message' => 'Ocurrió un error al intentar obtener la noticia.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getCategorias(): JsonResponse
    {
        try {
            // Obtener todas las categorías, ordenadas por nombre
            $categorias = Categoria::orderBy('nombre')->get();

            return response()->json(['data' => $categorias], 200);
        } catch (Exception $e) {
            Log::error('Error al obtener categorías en HomeController: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'message' => 'Ocurrió un error al intentar obtener las categorías.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getDashboardTotals(): JsonResponse
    {
        try {
            $totals = [
                'totalUsuarios' => User::count(),
                'totalRoles' => Role::count(),
                'totalMunicipios' => Municipio::count(),
                'totalDocumentos' => Documento::count(), // Debes especificar el modelo para "Documentos"
                'totalCirculares' => Circular::count(),
                'totalConvenios' => Convenio::count(),
                'totalCategorias' => Categoria::count(),
                'totalPublicaciones' => Noticia::count(), // Asumiendo que "Publicaciones" es el modelo Noticia
            ];

            return response()->json(['data' => $totals], 200);
        } catch (Exception $e) {
            Log::error('Error al obtener totales del dashboard: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'message' => 'Ocurrió un error al intentar obtener los totales del dashboard.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
