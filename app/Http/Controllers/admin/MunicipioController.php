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

            // Lógica para la imagen del mapa
            if ($request->hasFile('mapa_imagen')) { // El campo del formulario es 'mapa'
                $nombreSlug = \Illuminate\Support\Str::slug($validatedData['nombre'] ?? 'sin-nombre');
                $extension = $request->file('mapa_imagen')->getClientOriginalExtension();
                $imageName = $nombreSlug . '-mapa-' . time() . '.' . $extension;

                $imagePath = $request->file('mapa_imagen')->storeAs('municipios/mapas', $imageName, 'public');
                $validatedData['mapa_imagen'] = $imagePath; // Guardar la ruta en el campo de la DB
            }

            // Lógica existente para la foto del alcalde
            if ($request->hasFile('alcalde_foto')) {
                $nombreAlcaldeSlug = \Illuminate\Support\Str::slug($validatedData['alcalde_nombre'] ?? 'sin-alcalde');
                $extensionAlcalde = $request->file('alcalde_foto')->getClientOriginalExtension();
                $alcaldeImageName = $nombreAlcaldeSlug . '-foto-' . time() . '.' . $extensionAlcalde;

                $alcaldeImagePath = $request->file('alcalde_foto')->storeAs('municipios/alcaldes', $alcaldeImageName, 'public');
                $validatedData['alcalde_foto'] = $alcaldeImagePath;
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

            // Lógica para actualizar la imagen del mapa
            if ($request->hasFile('mapa_imagen')) { // El campo del formulario es 'mapa'
                // Eliminar imagen de mapa antigua si existe
                if ($municipio->mapa_imagen) {
                    if (Storage::disk('public')->exists($municipio->mapa_imagen)) {
                        Storage::disk('public')->delete($municipio->mapa_imagen);
                    }
                }

                // Guardar la nueva imagen de mapa
                $nombreSlug = \Illuminate\Support\Str::slug($validatedData['nombre'] ?? 'sin-nombre');
                $extension = $request->file('mapa_imagen')->getClientOriginalExtension();
                $imageName = $nombreSlug . '-mapa-' . time() . '.' . $extension;

                $imagePath = $request->file('mapa_imagen')->storeAs('municipios/mapas', $imageName, 'public');
                $validatedData['mapa_imagen'] = $imagePath;
            } elseif (array_key_exists('mapa', $request->all()) && is_null($request->input('mapa'))) {
                // Si 'mapa' se envió pero su valor es null, el cliente quiere eliminar la imagen.
                if ($municipio->mapa_imagen) {
                    if (Storage::disk('public')->exists($municipio->mapa_imagen)) {
                        Storage::disk('public')->delete($municipio->mapa_imagen);
                    }
                }
                $validatedData['mapa_imagen'] = null;
            } else {
                // Si no se envió una nueva imagen y no se pidió eliminar, mantener la existente
                $validatedData['mapa_imagen'] = $municipio->mapa_imagen;
            }


            // Lógica existente para actualizar la foto del alcalde
            if ($request->hasFile('alcalde_foto')) {
                if ($municipio->alcalde_foto) {
                    if (Storage::disk('public')->exists($municipio->alcalde_foto)) {
                        Storage::disk('public')->delete($municipio->alcalde_foto);
                    }
                }
                $nombreAlcaldeSlug = \Illuminate\Support\Str::slug($validatedData['alcalde_nombre'] ?? 'sin-alcalde');
                $extensionAlcalde = $request->file('alcalde_foto')->getClientOriginalExtension();
                $alcaldeImageName = $nombreAlcaldeSlug . '-foto-' . time() . '.' . $extensionAlcalde;

                $alcaldeImagePath = $request->file('alcalde_foto')->storeAs('municipios/alcaldes', $alcaldeImageName, 'public');
                $validatedData['alcalde_foto'] = $alcaldeImagePath;
            } elseif (array_key_exists('alcalde_foto', $request->all()) && is_null($request->input('alcalde_foto'))) {
                if ($municipio->alcalde_foto) {
                    if (Storage::disk('public')->exists($municipio->alcalde_foto)) {
                        Storage::disk('public')->delete($municipio->alcalde_foto);
                    }
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
