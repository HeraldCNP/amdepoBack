<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Circular;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\circular\StoreCircularRequest;
use App\Http\Requests\circular\UpdateCircularRequest;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager; // <-- Importa ImageManager
use Intervention\Image\Drivers\Gd\Driver; // <-- Importa el Driver que vas a usar (Gd es común)

class CircularController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $circulares = Circular::with('user')->orderBy('created_at', 'desc')->get();
            return response()->json($circulares, 200);
        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error al obtener circulares: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'message' => 'Ocurrió un error al intentar obtener las circulares.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(StoreCircularRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $validatedData['user_id'] = Auth::id();

            if ($request->hasFile('imagenCircular')) {
                $imageFile = $request->file('imagenCircular');
                $tituloSlug = \Illuminate\Support\Str::slug($validatedData['titulo'] ?? 'sin-titulo');
                // La extensión original ya no importa tanto, siempre guardaremos como JPG
                $imageName = $tituloSlug . '-' . time() . '.jpg'; // <-- Siempre .jpg
                $directory = 'circulares';

                $manager = new ImageManager(new Driver());
                $img = $manager->read($imageFile->getRealPath());

                // Redimensionar si es necesario, manteniendo el aspecto y sin agrandar
                $img->scale(width: 1280);

                // Convertir y comprimir a JPEG
                $encodedImage = $img->toJpeg(90); // <-- Siempre a JPEG con calidad 75

                Storage::disk('public')->put("{$directory}/{$imageName}", $encodedImage);

                $validatedData['imagenCircular'] = "{$directory}/{$imageName}";
            }

            $circular = Circular::create($validatedData);

            return response()->json([
                'message' => 'Circular creada exitosamente.',
                'data' => $circular
            ], 201);
        } catch (Exception $e) {
            Log::error('Error al crear circular: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'message' => 'Ocurrió un error al intentar crear la circular.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Circular $circular): JsonResponse
    {
        try {
            $circular->load('user');
            return response()->json($circular, 200);
        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error al obtener circular: ' . $e->getMessage(), ['exception' => $e, 'circular_id' => $circular->id]);
            return response()->json([
                'message' => 'Ocurrió un error al intentar obtener la circular.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(UpdateCircularRequest $request, Circular $circular): JsonResponse
    {
        try {
            $validatedData = $request->validated();

            if ($request->hasFile('imagen')) {
                // Eliminar imagen antigua si existe
                if ($circular->imagenCircular) {
                    if (Storage::disk('public')->exists($circular->imagenCircular)) {
                        Storage::disk('public')->delete($circular->imagenCircular);
                    }
                }

                // Guardar la nueva imagen
                $tituloSlug = \Illuminate\Support\Str::slug($validatedData['titulo'] ?? 'sin-titulo');
                $extension = $request->file('imagen')->getClientOriginalExtension();
                $imageName = $tituloSlug . '-' . time() . '.' . $extension;

                // $imagePath = $request->file('imagen')->storeAs('circulares', $imageName, 'public');
                $rutaArchivo = $request->file('imagen')->storeAs('circulares/' . $imageName, 'public');
                $validatedData['imagenCircular'] = $rutaArchivo;
            } elseif (array_key_exists('imagen', $request->all()) && is_null($request->input('imagen'))) {
                // Si 'imagen' se envió pero su valor es null, el cliente quiere eliminar la imagen.
                if ($circular->imagenCircular) {
                    if (Storage::disk('public')->exists($circular->imagenCircular)) {
                        Storage::disk('public')->delete($circular->imagenCircular);
                    }
                }
                $validatedData['imagenCircular'] = null;
            } else {
                $validatedData['imagenCircular'] = $circular->imagenCircular;
            }

            $circular->update($validatedData);

            return response()->json([
                'message' => 'Circular actualizada exitosamente.',
                'data' => $circular
            ], 200);
        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error al actualizar circular: ' . $e->getMessage(), ['exception' => $e, 'circular_id' => $circular->id]);
            return response()->json([
                'message' => 'Ocurrió un error al intentar actualizar la circular.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function eliminar($id): JsonResponse
    {
        try {
            $circular = Circular::findOrFail($id);
            // dd($circular->imagenCircular);

            if (Storage::disk('public')->exists($circular->imagenCircular)) {
                Storage::disk('public')->delete($circular->imagenCircular);
            }

            $circular->delete();

            return response()->json(null, 204);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Documento no encontrado.'], 404);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Error al eliminar el documento.', 'message' => $e->getMessage()], 500);
        }
    }
}
