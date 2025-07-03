<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Categoria\StoreCategoriaRequest;
use App\Models\Categoria;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoriaController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            // Ordenar por fecha de creación descendente para ver las más recientes primero
            $categorias = Categoria::orderBy('created_at', 'desc')->get();
            return response()->json(['data' => $categorias], 200);
        } catch (Exception $e) {
            Log::error('Error al obtener categorías: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'message' => 'Ocurrió un error al intentar obtener las categorías.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     * Almacena una nueva categoría en la base de datos.
     *
     * @param StoreCategoriaRequest $request
     * @return JsonResponse
     */
    public function store(StoreCategoriaRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();

            // El slug se genera automáticamente en el modelo Categoria (método boot)
            $categoria = Categoria::create($validatedData);

            return response()->json([
                'message' => 'Categoría creada exitosamente.',
                'data' => $categoria
            ], 201); // Código 201 para "Created"
        } catch (Exception $e) {
            Log::error('Error al crear categoría: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'message' => 'Ocurrió un error al intentar crear la categoría.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     * Muestra una categoría específica por su ID.
     *
     * @param Categoria $categoria (Inyección de modelo por Route Model Binding)
     * @return JsonResponse
     */
    public function show(Categoria $categoria): JsonResponse
    {
        try {
            return response()->json(['data' => $categoria], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Categoría no encontrada.'], 404);
        } catch (Exception $e) {
            Log::error('Error al obtener categoría: ' . $e->getMessage(), ['exception' => $e, 'categoria_id' => $categoria->id]);
            return response()->json([
                'message' => 'Ocurrió un error al intentar obtener la categoría.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     * Actualiza una categoría existente en la base de datos.
     *
     * @param UpdateCategoriaRequest $request
     * @param Categoria $categoria (Inyección de modelo por Route Model Binding)
     * @return JsonResponse
     */
    // public function update(UpdateCategoriaRequest $request, Categoria $categoria): JsonResponse
    // {
    //     try {
    //         $validatedData = $request->validated();

    //         // El slug se actualiza automáticamente en el modelo Categoria (método boot)
    //         $categoria->update($validatedData);

    //         return response()->json([
    //             'message' => 'Categoría actualizada exitosamente.',
    //             'data' => $categoria
    //         ], 200);
    //     } catch (Exception $e) {
    //         Log::error('Error al actualizar categoría: ' . $e->getMessage(), ['exception' => $e, 'categoria_id' => $categoria->id]);
    //         return response()->json([
    //             'message' => 'Ocurrió un error al intentar actualizar la categoría.',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    /**
     * Remove the specified resource from storage.
     * Elimina una categoría de la base de datos.
     *
     * @param Categoria $categoria (Inyección de modelo por Route Model Binding)
     * @return JsonResponse
     */
    public function destroy(Categoria $categoria): JsonResponse
    {
        try {
            // onDelete('set null') en la migración de noticias asegura que las noticias
            // asociadas a esta categoría no se eliminen, solo su categoria_id se pondrá a null.
            $categoria->delete();

            return response()->json([
                'message' => 'Categoría eliminada exitosamente.',
                'data' => null // No hay datos que devolver después de una eliminación exitosa
            ], 200);
        } catch (Exception $e) {
            Log::error('Error al eliminar categoría: ' . $e->getMessage(), ['exception' => $e, 'categoria_id' => $categoria->id]);
            return response()->json([
                'message' => 'Ocurrió un error al intentar eliminar la categoría.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
