<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImagenTuristica;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\imagenTuristica\StoreImagenTuristicaRequest;
use App\Http\Requests\imagenTuristica\UpdateImagenTuristicaRequest;
use App\Models\Municipio;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager; // Importa ImageManager
use Intervention\Image\Drivers\Gd\Driver; // Importa el Driver (Gd es común)
use Illuminate\Support\Str;

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

            $manager = new ImageManager(new Driver()); // Instancia el ImageManager

            // Guardar la imagen
            if ($request->hasFile('imagen_file')) {
                $imageFile = $request->file('imagen_file');
                $descripcionSlug = Str::slug($validatedData['descripcion'] ?? 'sin-descripcion');
                $imageName = $descripcionSlug . '-' . time() . '.jpg'; // Siempre .jpg

                // Obtener el municipio para usar su nombre en la carpeta
                $municipioId = $validatedData['municipio_id'];
                $municipio = Municipio::findOrFail($municipioId); // Busca el municipio por ID
                $municipioSlug = Str::slug($municipio->nombre); // Genera un slug del nombre del municipio

                // Define el directorio con el slug del nombre del municipio
                $directory = "imagenes_turisticas/{$municipioSlug}"; // <-- Directorio con el slug del nombre del municipio

                $img = $manager->read($imageFile->getRealPath()); // Lee el archivo
                $img->scale(width: 1280); // Escala a un ancho máximo de 1280px
                $encodedImage = $img->toJpeg(75); // Convierte y comprime a JPEG

                // Guarda la imagen en la nueva estructura de carpetas
                Storage::disk('public')->put("{$directory}/{$imageName}", $encodedImage);
                $validatedData['ruta_imagen'] = "{$directory}/{$imageName}"; // Guarda la ruta completa
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
