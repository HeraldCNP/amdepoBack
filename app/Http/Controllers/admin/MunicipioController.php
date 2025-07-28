<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\municipio\StoreMunicipioRequest;
use App\Http\Requests\municipio\UpdateMunicipioRequest;
use App\Models\Municipio;
use Exception;

use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException; // Importa la excepción ModelNotFoundException
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\ImageManager; // Importa ImageManager
use Intervention\Image\Drivers\Gd\Driver; // Importa el Driver (Gd es común)

class MunicipioController extends Controller
{
    public function index(): JsonResponse
    {

        try {
            // $municipios = Municipio::all();
            $municipios = Municipio::with(['user', 'imagenesTuristicas'])->get();
            //  ->orderBy('created_at', 'desc')
            // $municipios = Municipio::select('id', 'nombre', 'slug')->get();

            return response()->json([
                'message' => 'Municipios recuperados exitosamente.',
                'data' => $municipios
            ], 200);
        } catch (Exception $e) {
            // Log the exception for debugging
            Log::error('Error al recuperar municipios: ' . $e->getMessage());

            return response()->json([
                'message' => 'Ocurrió un error al intentar recuperar los municipios.',
                'error' => $e->getMessage() // Solo para desarrollo, en producción evita exponer detalles
            ], 500); // 500 Internal Server Error
        }
    }

    public function list(): JsonResponse
    {

        try {
            // $municipios = Municipio::all(); // O Municipio::paginate(15);
            $municipios = Municipio::select('id', 'nombre', 'slug')->get();

            return response()->json([
                'message' => 'Municipios recuperados exitosamente.',
                'data' => $municipios
            ], 200);
        } catch (Exception $e) {
            // Log the exception for debugging
            Log::error('Error al recuperar municipios: ' . $e->getMessage());

            return response()->json([
                'message' => 'Ocurrió un error al intentar recuperar los municipios.',
                'error' => $e->getMessage() // Solo para desarrollo, en producción evita exponer detalles
            ], 500); // 500 Internal Server Error
        }
    }


    public function store(StoreMunicipioRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $validatedData['user_id'] = Auth::id();

            $manager = new ImageManager(new Driver()); // Instancia el ImageManager

            // Lógica para la imagen del mapa
            if ($request->hasFile('mapa_imagen')) {
                $imageFile = $request->file('mapa_imagen');
                $nombreSlug = \Illuminate\Support\Str::slug($validatedData['nombre'] ?? 'sin-nombre');
                $imageName = $nombreSlug . '-mapa-' . time() . '.jpg'; // <-- Siempre .jpg
                $directory = 'municipios/mapas';

                $img = $manager->read($imageFile->getRealPath());

                // Redimensionar usando scale para mantener aspecto y ancho máximo
                $img->scale(width: 1280);

                // Convertir y comprimir a JPEG
                $encodedImage = $img->toJpeg(75); // Calidad 75 para JPEG

                Storage::disk('public')->put("{$directory}/{$imageName}", $encodedImage);
                $validatedData['mapa_imagen'] = "{$directory}/{$imageName}";
            }

            // Lógica para la foto del alcalde
            if ($request->hasFile('alcalde_foto')) {
                $imageFile = $request->file('alcalde_foto'); // Obtener el archivo de la solicitud
                $nombreAlcaldeSlug = \Illuminate\Support\Str::slug($validatedData['alcalde_nombre'] ?? 'sin-alcalde');
                $alcaldeImageName = $nombreAlcaldeSlug . '-foto-' . time() . '.jpg'; // <-- Siempre .jpg
                $directory = 'municipios/alcaldes';

                $img = $manager->read($imageFile->getRealPath());

                // Redimensionar usando scale para mantener aspecto y ancho máximo
                $img->scale(width: 1280);

                // Convertir y comprimir a JPEG
                $encodedImage = $img->toJpeg(75); // Calidad 75 para JPEG

                Storage::disk('public')->put("{$directory}/{$alcaldeImageName}", $encodedImage);
                $validatedData['alcalde_foto'] = "{$directory}/{$alcaldeImageName}";
            }

            $municipio = Municipio::create($validatedData);

            return response()->json([
                'message' => 'Municipio creado exitosamente.',
                'data' => $municipio
            ], 201);
        } catch (Exception $e) {
            Log::error('Error al crear municipio: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'message' => 'Ocurrió un error al intentar crear el municipio.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function show($slug): JsonResponse
    {
        try {
            $municipio = Municipio::where('slug', $slug)->firstOrFail(); // Busca por slug
            return response()->json($municipio);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Municipio no encontrado.', 'message' => $e->getMessage()], 404);
        }
    }

    public function update(UpdateMunicipioRequest $request, Municipio $municipio): JsonResponse
    {
        try {
            $validatedData = $request->validated();

            $manager = new ImageManager(new Driver()); // Instancia el ImageManager

            // Lógica para la imagen del mapa
            if ($request->hasFile('mapa_imagen')) {
                // Eliminar imagen antigua si existe
                if ($municipio->mapa_imagen && Storage::disk('public')->exists($municipio->mapa_imagen)) {
                    Storage::disk('public')->delete($municipio->mapa_imagen);
                }

                $imageFile = $request->file('mapa_imagen');
                $nombreSlug = \Illuminate\Support\Str::slug($validatedData['nombre'] ?? 'sin-nombre');
                $imageName = $nombreSlug . '-mapa-' . time() . '.jpg'; // Siempre .jpg
                $directory = 'municipios/mapas';

                $img = $manager->read($imageFile->getRealPath());
                $img->scale(width: 1280);
                $encodedImage = $img->toJpeg(75);

                Storage::disk('public')->put("{$directory}/{$imageName}", $encodedImage);
                $validatedData['mapa_imagen'] = "{$directory}/{$imageName}";
            } elseif (array_key_exists('mapa_imagen', $request->all()) && is_null($request->input('mapa_imagen'))) {
                if ($municipio->mapa_imagen && Storage::disk('public')->exists($municipio->mapa_imagen)) {
                    Storage::disk('public')->delete($municipio->mapa_imagen);
                }
                $validatedData['mapa_imagen'] = null;
            } else {
                $validatedData['mapa_imagen'] = $municipio->mapa_imagen;
            }

            // Lógica para la foto del alcalde
            if ($request->hasFile('alcalde_foto')) {
                // Eliminar foto antigua si existe
                if ($municipio->alcalde_foto && Storage::disk('public')->exists($municipio->alcalde_foto)) {
                    Storage::disk('public')->delete($municipio->alcalde_foto);
                }

                $imageFile = $request->file('alcalde_foto');
                $nombreAlcaldeSlug = \Illuminate\Support\Str::slug($validatedData['alcalde_nombre'] ?? 'sin-alcalde');
                $alcaldeImageName = $nombreAlcaldeSlug . '-foto-' . time() . '.jpg'; // Siempre .jpg
                $directory = 'municipios/alcaldes';

                $img = $manager->read($imageFile->getRealPath());
                $img->scale(width: 1280);
                $encodedImage = $img->toJpeg(75);

                Storage::disk('public')->put("{$directory}/{$alcaldeImageName}", $encodedImage);
                $validatedData['alcalde_foto'] = "{$directory}/{$alcaldeImageName}";
            } elseif (array_key_exists('alcalde_foto', $request->all()) && is_null($request->input('alcalde_foto'))) {
                if ($municipio->alcalde_foto && Storage::disk('public')->exists($municipio->alcalde_foto)) {
                    Storage::disk('public')->delete($municipio->alcalde_foto);
                }
                $validatedData['alcalde_foto'] = null;
            } else {
                $validatedData['alcalde_foto'] = $municipio->alcalde_foto;
            }

            $municipio->update($validatedData);

            return response()->json([
                'message' => 'Municipio actualizado exitosamente.',
                'data' => $municipio
            ], 200);
        } catch (Exception $e) {
            Log::error('Error al actualizar municipio: ' . $e->getMessage(), ['exception' => $e, 'municipio_id' => $municipio->id]);
            return response()->json([
                'message' => 'Ocurrió un error al intentar actualizar el municipio.',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function destroy(Municipio $municipio): JsonResponse
    {
        try {
            // 1. Eliminar la foto del alcalde si existe
            if ($municipio->alcalde_foto) {
                if (Storage::disk('public')->exists($municipio->alcalde_foto)) {
                    Storage::disk('public')->delete($municipio->alcalde_foto);
                    // Opcional: Logear que la foto fue eliminada, si es muy importante para tu monitoreo.
                    // \Illuminate\Support\Facades\Log::info('Foto de alcalde eliminada para el municipio ' . $municipio->id . ': ' . $municipio->alcalde_foto);
                }
            }

            // 2. Eliminar el registro del municipio de la base de datos
            $municipio->delete();

            return response()->json([
                'message' => 'Municipio eliminado exitosamente.',
                'data' => null // No hay datos que devolver después de una eliminación exitosa
            ], 200);
        } catch (Exception $e) {
            // Captura cualquier excepción inesperada durante el proceso de eliminación.
            Log::error('Error al eliminar municipio: ' . $e->getMessage(), ['exception' => $e, 'municipio_id' => $municipio->id]);

            return response()->json([
                'message' => 'Ocurrió un error al intentar eliminar el municipio.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
