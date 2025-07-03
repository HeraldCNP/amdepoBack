<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Noticia; // Importa el modelo Noticia
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Noticia\StoreNoticiaRequest; // Importa el Store Request para Noticias
use App\Http\Requests\Noticia\UpdateNoticiaRequest; // Importa el Update Request para Noticias
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log; // Para logs de error
use Illuminate\Support\Facades\Auth; // Para obtener el usuario autenticado

class NoticiaController extends Controller
{
    /**
     * Display a listing of the resource.
     * Muestra una lista de todas las noticias con sus relaciones cargadas.
     */
    public function index(): JsonResponse
    {
        try {
            // Cargar relaciones 'user', 'categoria' e 'imagenesNoticias' para evitar problemas N+1
            $noticias = Noticia::with(['user', 'categoria', 'imagenesNoticias'])
                               ->orderBy('created_at', 'desc')
                               ->get();
            return response()->json(['data' => $noticias], 200);
        } catch (Exception $e) {
            Log::error('Error al obtener noticias: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'message' => 'Ocurrió un error al intentar obtener las noticias.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     * Almacena una nueva noticia en la base de datos.
     *
     * @param StoreNoticiaRequest $request
     * @return JsonResponse
     */
    public function store(StoreNoticiaRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $validatedData['user_id'] = Auth::id(); // Asigna el ID del usuario autenticado

            // El slug se genera automáticamente en el modelo Noticia (método boot)
            // 'texto', 'categoria_id' y 'video_url' se guardan directamente de los datos validados

            $noticia = Noticia::create($validatedData);

            return response()->json([
                'message' => 'Noticia creada exitosamente.',
                'data' => $noticia
            ], 201); // Código 201 para "Created"
        } catch (Exception $e) {
            Log::error('Error al crear noticia: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'message' => 'Ocurrió un error al intentar crear la noticia.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     * Muestra una noticia específica por su ID o slug (si se usa Route Model Binding por slug).
     *
     * @param Noticia $noticia (Inyección de modelo por Route Model Binding)
     * @return JsonResponse
     */
    public function show(Noticia $noticia): JsonResponse
    {
        try {
            // Cargar relaciones 'user', 'categoria' e 'imagenesNoticias'
            $noticia->load(['user', 'categoria', 'imagenesNoticias']);
            return response()->json(['data' => $noticia], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Noticia no encontrada.'], 404);
        } catch (Exception $e) {
            Log::error('Error al obtener noticia: ' . $e->getMessage(), ['exception' => $e, 'noticia_id' => $noticia->id]);
            return response()->json([
                'message' => 'Ocurrió un error al intentar obtener la noticia.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     * Actualiza una noticia existente en la base de datos.
     *
     * @param UpdateNoticiaRequest $request
     * @param Noticia $noticia (Inyección de modelo por Route Model Binding)
     * @return JsonResponse
     */
    public function update(UpdateNoticiaRequest $request, Noticia $noticia): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            // El slug se actualiza automáticamente en el modelo Noticia si el título cambia
            // El resto de campos se actualizan directamente

            $noticia->update($validatedData);

            return response()->json([
                'message' => 'Noticia actualizada exitosamente.',
                'data' => $noticia
            ], 200);
        } catch (Exception $e) {
            Log::error('Error al actualizar noticia: ' . $e->getMessage(), ['exception' => $e, 'noticia_id' => $noticia->id]);
            return response()->json([
                'message' => 'Ocurrió un error al intentar actualizar la noticia.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * Elimina una noticia de la base de datos.
     *
     * @param Noticia $noticia (Inyección de modelo por Route Model Binding)
     * @return JsonResponse
     */
    public function destroy(Noticia $noticia): JsonResponse
    {
        try {
            // Si la relación 'imagenesNoticias' en el modelo Noticia tiene onDelete('cascade')
            // en la migración de 'imagenes_noticias', las imágenes se eliminarán automáticamente.
            $noticia->delete();

            return response()->json([
                'message' => 'Noticia eliminada exitosamente.',
                'data' => null // No hay datos que devolver después de una eliminación exitosa
            ], 200);
        } catch (Exception $e) {
            Log::error('Error al eliminar noticia: ' . $e->getMessage(), ['exception' => $e, 'noticia_id' => $noticia->id]);
            return response()->json([
                'message' => 'Ocurrió un error al intentar eliminar la noticia.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
