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
use Intervention\Image\ImageManager; // Importa ImageManager
use Intervention\Image\Drivers\Gd\Driver; // Importa el Driver (Gd es común)

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
            $descripcionGeneral = $validatedData['descripcion'] ?? null; // Obtener una única descripción para todas las imágenes

            $uploadedImages = [];
            $manager = new ImageManager(new Driver()); // Instancia el ImageManager para el store

            if ($request->hasFile('imagen_files')) { // 'imagen_files' es el nombre del campo del array de archivos
                foreach ($request->file('imagen_files') as $index => $file) {
                    $descripcionParaEstaImagen = $descripcionGeneral;

                    // Generar un slug basado en la descripción general y un índice para asegurar unicidad en el nombre del archivo
                    $descripcionSlug = Str::slug($descripcionGeneral ?? 'imagen') . '-' . ($index + 1);
                    // Siempre guardaremos como JPG
                    $imageName = "noticia-{$noticiaId}-{$descripcionSlug}-" . time() . '.jpg';

                    $img = $manager->read($file->getRealPath()); // Lee el archivo
                    $img->scale(width: 1280); // Escala a un ancho máximo de 1280px
                    $encodedImage = $img->toJpeg(75); // Convierte y comprime a JPEG

                    // Guarda la imagen procesada en 'public/noticias/imagenes' directory
                    Storage::disk('public')->put("noticias/imagenes/{$imageName}", $encodedImage);

                    $imagenNoticia = ImagenNoticia::create([
                        'noticia_id' => $noticiaId,
                        'ruta_imagen' => "noticias/imagenes/{$imageName}", // Guarda la ruta completa
                        'descripcion' => $descripcionParaEstaImagen,
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
            ], 201); // 201 Created
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

            $manager = new ImageManager(new Driver()); // Instancia el ImageManager para el update

            // Handle image file replacement/deletion
            // El campo de archivo en el request de update sigue siendo 'imagen_file' (singular)
            if ($request->hasFile('imagen_file')) {
                // Delete old image if it exists
                if ($imagenNoticia->ruta_imagen && Storage::disk('public')->exists($imagenNoticia->ruta_imagen)) {
                    Storage::disk('public')->delete($imagenNoticia->ruta_imagen);
                }

                // Store the new image
                $noticiaId = $validatedData['noticia_id'];
                $descripcionSlug = Str::slug($validatedData['descripcion'] ?? 'imagen');
                // Siempre guardaremos como JPG
                $imageName = "noticia-{$noticiaId}-{$descripcionSlug}-" . time() . '.jpg';

                $imageFile = $request->file('imagen_file');
                $img = $manager->read($imageFile->getRealPath()); // Lee el archivo
                $img->scale(width: 1280); // Escala a un ancho máximo de 1280px
                $encodedImage = $img->toJpeg(75); // Convierte y comprime a JPEG

                Storage::disk('public')->put("noticias/imagenes/{$imageName}", $encodedImage);
                $validatedData['ruta_imagen'] = "noticias/imagenes/{$imageName}"; // Guarda la ruta completa
            } elseif (array_key_exists('imagen_file', $request->all()) && is_null($request->input('imagen_file'))) {
                // If 'imagen_file' was sent as null, it means the client wants to delete the image
                if ($imagenNoticia->ruta_imagen && Storage::disk('public')->exists($imagenNoticia->ruta_imagen)) {
                    Storage::disk('public')->delete($imagenNoticia->ruta_imagen);
                }
                $validatedData['ruta_imagen'] = null;
            } else {
                // If no new file was uploaded and no deletion was requested, keep the existing path
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
