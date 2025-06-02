<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Publicacion; // Asegúrate de importar el modelo Publicacion
use Illuminate\Http\JsonResponse;
use App\Http\Requests\publicacion\StorePublicacionRequest; // Importa los Request
use App\Http\Requests\publicacion\UpdatePublicacionRequest; // Importa los Request
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PublicacionController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $publicaciones = Publicacion::with('user')->orderBy('created_at', 'desc')->get();
            return response()->json($publicaciones, 200);
        } catch (Exception $e) {
            Log::error('Error al obtener publicaciones: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'message' => 'Ocurrió un error al intentar obtener las publicaciones.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePublicacionRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $validatedData['user_id'] = Auth::id(); // Asignar el ID del usuario autenticado

            $publicacion = Publicacion::create($validatedData);

            return response()->json([
                'message' => 'Publicación creada exitosamente.',
                'data' => $publicacion
            ], 201);
        } catch (Exception $e) {
            Log::error('Error al crear publicación: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'message' => 'Ocurrió un error al intentar crear la publicación.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Publicacion $publicacion): JsonResponse
    {
        try {
            $publicacion->load('user');
            return response()->json($publicacion, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Publicación no encontrada.'], 404);
        } catch (Exception $e) {
            Log::error('Error al obtener publicación: ' . $e->getMessage(), ['exception' => $e, 'publicacion_id' => $publicacion->id]);
            return response()->json([
                'message' => 'Ocurrió un error al intentar obtener la publicación.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(UpdatePublicacionRequest $request, Publicacion $publicacion): JsonResponse
    // {
    //     try {
    //         $validatedData = $request->validated();
    //         // No hay manejo de archivos, solo se actualizan los campos de texto
    //         $publicacion->update($validatedData);

    //         return response()->json([
    //             'message' => 'Publicación actualizada exitosamente.',
    //             'data' => $publicacion
    //         ], 200);
    //     } catch (Exception $e) {
    //         Log::error('Error al actualizar publicación: ' . $e->getMessage(), ['exception' => $e, 'publicacion_id' => $publicacion->id]);
    //         return response()->json([
    //             'message' => 'Ocurrió un error al intentar actualizar la publicación.',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    /**
     * Remove the specified resource from storage.
     */
    public function eliminar($id): JsonResponse
    {
        try {
            $publicacion = Publicacion::findOrFail($id);
            // dd($circular->imagenCircular);

            if (Storage::disk('public')->exists($publicacion->contenido_iframe)) {
                Storage::disk('public')->delete($publicacion->contenido_iframe);
            }

            $publicacion->delete();

            return response()->json(null, 204);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Documento no encontrado.'], 404);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Error al eliminar el documento.', 'message' => $e->getMessage()], 500);
        }
    }
}
