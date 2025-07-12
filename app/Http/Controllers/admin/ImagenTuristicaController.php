<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImagenTuristica;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\imagenTuristica\StoreImagenTuristicaRequest;
use App\Http\Requests\imagenTuristica\UpdateImagenTuristicaRequest;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class ImagenTuristicaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $imagenes = ImagenTuristica::with('municipio')->orderBy('created_at', 'desc')->get();
            return response()->json($imagenes, 200);
        } catch (Exception $e) {
            Log::error('Error al obtener imágenes turísticas: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'message' => 'Ocurrió un error al intentar obtener las imágenes turísticas.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreImagenTuristicaRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();

            // Guardar la imagen
            if ($request->hasFile('imagen_file')) {
                // Usamos la descripción para generar el nombre del archivo
                $descripcionSlug = \Illuminate\Support\Str::slug($validatedData['descripcion'] ?? 'sin-descripcion'); // <-- ¡Cambiado aquí!
                $extension = $request->file('imagen_file')->getClientOriginalExtension();
                $imageName = $descripcionSlug . '-' . time() . '.' . $extension;

                $imagePath = $request->file('imagen_file')->storeAs('imagenes_turisticas', $imageName, 'public');
                $validatedData['ruta_imagen'] = $imagePath;
            }

            $imagenTuristica = ImagenTuristica::create($validatedData);

            return response()->json([
                'message' => 'Imagen turística creada exitosamente.',
                'data' => $imagenTuristica
            ], 201);
        } catch (Exception $e) {
            Log::error('Error al crear imagen turística: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'message' => 'Ocurrió un error al intentar crear la imagen turística.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ImagenTuristica $imagenTuristica): JsonResponse
    {
        try {
            $imagenTuristica->load('municipio');
            return response()->json($imagenTuristica, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Imagen turística no encontrada.'], 404);
        } catch (Exception $e) {
            Log::error('Error al obtener imagen turística: ' . $e->getMessage(), ['exception' => $e, 'imagen_id' => $imagenTuristica->id]);
            return response()->json([
                'message' => 'Ocurrió un error al intentar obtener la imagen turística.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function destroy($id): JsonResponse
    {
        try {
            $imagenTuristica = ImagenTuristica::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Imagen turística no encontrada.'], 404);
        }
        try {
            if ($imagenTuristica->ruta_imagen && Storage::disk('public')->exists($imagenTuristica->ruta_imagen)) {
                Storage::disk('public')->delete($imagenTuristica->ruta_imagen);
            }

            $imagenTuristica->delete();

            return response()->json([
                'message' => 'Imagen turística eliminada exitosamente.',
                'data' => null
            ], 200);
        } catch (Exception $e) {
            Log::error('Error al eliminar imagen turística: ' . $e->getMessage(), ['exception' => $e, 'imagen_id' => $imagenTuristica->id]);
            return response()->json([
                'message' => 'Ocurrió un error al intentar eliminar la imagen turística.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
