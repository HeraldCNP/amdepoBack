<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImagenNoticia; // Importa el modelo ImagenNoticia
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ImagenNoticia\StoreImagenNoticiaRequest; // Importa el Store Request
use App\Http\Requests\ImagenNoticia\UpdateImagenNoticiaRequest; // Importa el Update Request
use Illuminate\Support\Facades\Storage; // Para manejar el almacenamiento de archivos
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log; // Para logs de error
use Illuminate\Support\Str; // Para generar slugs para nombres de archivos

class ImagenNoticiaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $query = ImagenNoticia::with('noticia');

            if (request()->has('noticia_id')) {
                $query->where('noticia_id', request('noticia_id'));
            }

            $imagenes = $query->orderBy('created_at', 'desc')->get();
            return response()->json(['data' => $imagenes], 200);
        } catch (Exception $e) {
            Log::error('Error al obtener imágenes de noticias: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'message' => 'Ocurrió un error al intentar obtener las imágenes de noticias.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     * Almacena una o varias nuevas imágenes de noticia en la base de datos y el almacenamiento.
     *
     * @param StoreImagenNoticiaRequest $request
     * @return JsonResponse
     */
    public function store(StoreImagenNoticiaRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $noticiaId = $validatedData['noticia_id'];
            // CAMBIO CLAVE: Obtener una única descripción para todas las imágenes
            $descripcionGeneral = $validatedData['descripcion'] ?? null;

            $uploadedImages = [];

            if ($request->hasFile('imagen_files')) {
                foreach ($request->file('imagen_files') as $index => $file) {
                    // Usar la descripción general para todas las imágenes
                    $descripcionParaEstaImagen = $descripcionGeneral;

                    // Generar un slug basado en la descripción general y un índice para asegurar unicidad en el nombre del archivo
                    $descripcionSlug = Str::slug($descripcionGeneral ?? 'imagen') . '-' . ($index + 1);
                    $extension = $file->getClientOriginalExtension();
                    $imageName = "noticia-{$noticiaId}-{$descripcionSlug}-" . time() . '.' . $extension;

                    $imagePath = $file->storeAs('noticias/imagenes', $imageName, 'public');

                    $imagenNoticia = ImagenNoticia::create([
                        'noticia_id' => $noticiaId,
                        'ruta_imagen' => $imagePath,
                        'descripcion' => $descripcionParaEstaImagen, // Guardar la descripción general
                    ]);
                    $uploadedImages[] = $imagenNoticia;
                }
            }

            if (empty($uploadedImages)) {
                return response()->json([
                    'message' => 'No se subieron imágenes válidas.',
                ], 400);
            }

            return response()->json([
                'message' => 'Imágenes de noticia creadas exitosamente.',
                'data' => $uploadedImages
            ], 201);
        } catch (Exception $e) {
            Log::error('Error al crear imágenes de noticia: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'message' => 'Ocurrió un error al intentar crear las imágenes de noticia.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ImagenNoticia $imagenNoticia): JsonResponse
    {
        try {
            $imagenNoticia->load('noticia');
            return response()->json(['data' => $imagenNoticia], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Imagen de noticia no encontrada.'], 404);
        } catch (Exception $e) {
            Log::error('Error al obtener imagen de noticia: ' . $e->getMessage(), ['exception' => $e, 'imagen_id' => $imagenNoticia->id]);
            return response()->json([
                'message' => 'Ocurrió un error al intentar obtener la imagen de noticia.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateImagenNoticiaRequest $request, ImagenNoticia $imagenNoticia): JsonResponse
    {
        try {
            $validatedData = $request->validated();

            // La lógica de actualización de una sola imagen permanece igual
            if ($request->hasFile('imagen_file')) {
                if ($imagenNoticia->ruta_imagen && Storage::disk('public')->exists($imagenNoticia->ruta_imagen)) {
                    Storage::disk('public')->delete($imagenNoticia->ruta_imagen);
                }

                $noticiaId = $validatedData['noticia_id'];
                $descripcionSlug = Str::slug($validatedData['descripcion'] ?? 'imagen');
                $extension = $request->file('imagen_file')->getClientOriginalExtension();
                $imageName = "noticia-{$noticiaId}-{$descripcionSlug}-" . time() . '.' . $extension;

                $imagePath = $request->file('imagen_file')->storeAs('noticias/imagenes', $imageName, 'public');
                $validatedData['ruta_imagen'] = $imagePath;
            } elseif (array_key_exists('imagen_file', $request->all()) && is_null($request->input('imagen_file'))) {
                if ($imagenNoticia->ruta_imagen && Storage::disk('public')->exists($imagenNoticia->ruta_imagen)) {
                    Storage::disk('public')->delete($imagenNoticia->ruta_imagen);
                }
                $validatedData['ruta_imagen'] = null;
            } else {
                $validatedData['ruta_imagen'] = $imagenNoticia->ruta_imagen;
            }

            $imagenNoticia->update($validatedData);

            return response()->json([
                'message' => 'Imagen de noticia actualizada exitosamente.',
                'data' => $imagenNoticia
            ], 200);
        } catch (Exception $e) {
            Log::error('Error al actualizar imagen de noticia: ' . $e->getMessage(), ['exception' => $e, 'imagen_id' => $imagenNoticia->id]);
            return response()->json([
                'message' => 'Ocurrió un error al intentar actualizar la imagen de noticia.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ImagenNoticia $imagenNoticia): JsonResponse
    {
        try {
            if ($imagenNoticia->ruta_imagen && Storage::disk('public')->exists($imagenNoticia->ruta_imagen)) {
                Storage::disk('public')->delete($imagenNoticia->ruta_imagen);
            }

            $imagenNoticia->delete();

            return response()->json([
                'message' => 'Imagen de noticia eliminada exitosamente.',
                'data' => null
            ], 200);
        } catch (Exception $e) {
            Log::error('Error al eliminar imagen de noticia: ' . $e->getMessage(), ['exception' => $e, 'imagen_id' => $imagenNoticia->id]);
            return response()->json([
                'message' => 'Ocurrió un error al intentar eliminar la imagen de noticia.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
