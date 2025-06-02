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
            $municipios = Municipio::all(); // O Municipio::paginate(15);
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

            if ($request->hasFile('alcalde_foto')) {

                // Generar el nombre del archivo basado en el nombre del alcalde
                $alcaldeNombre = Str::slug($validatedData['alcalde_nombre'] ?? 'sin-nombre');
                $extension = $request->file('alcalde_foto')->getClientOriginalExtension();
                $imageName = $alcaldeNombre . '-' . time() . '.' . $extension;

                // Guardar la nueva foto con el nombre generado
                $imagePath = $request->file('alcalde_foto')->storeAs('municipios/alcaldes', $imageName, 'public');
                $validatedData['alcalde_foto'] = $imagePath;
            }

            // --- ASIGNAR user_id DEL USUARIO LOGUEADO ---
            $validatedData['user_id'] = Auth::id(); // O auth()->id();
            $validatedData['slug'] = Str::slug($validatedData['nombre']);
            // Si el user_id ya está en fillable y no quieres enviarlo en el request,
            // esta es la forma correcta de asignarlo.
            // --- FIN ASIGNAR user_id ---

            $municipio = Municipio::create($validatedData);

            return response()->json([
                'message' => 'Municipio creado exitosamente.',
                'data' => $municipio
            ], 201);
        } catch (Exception $e) {
            Log::error('Error al crear municipio: ' . $e->getMessage());

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
            // Obtén los datos ya validados por el FormRequest
            $validatedData = $request->validated();

            // Lógica para manejar la foto (eliminar la anterior y guardar la nueva)
            if ($request->hasFile('alcalde_foto')) {
                // Eliminar foto antigua si existe
                if ($municipio->alcalde_foto) {
                    if (Storage::disk('public')->exists($municipio->alcalde_foto)) {
                        Storage::disk('public')->delete($municipio->alcalde_foto);
                    }
                }

                // Generar el nombre del archivo basado en el nombre del alcalde
                $alcaldeNombre = Str::slug($validatedData['alcalde_nombre'] ?? 'sin-nombre');
                $extension = $request->file('alcalde_foto')->getClientOriginalExtension();
                $imageName = $alcaldeNombre . '-' . time() . '.' . $extension;

                // Guardar la nueva foto con el nombre generado
                $imagePath = $request->file('alcalde_foto')->storeAs('municipios/alcaldes', $imageName, 'public');
                $validatedData['alcalde_foto'] = $imagePath;
            } elseif (array_key_exists('alcalde_foto', $request->all()) && is_null($request->input('alcalde_foto'))) {
                // Si 'alcalde_foto' se envió pero su valor es null, el cliente quiere eliminar la foto.
                if ($municipio->alcalde_foto) {
                    if (Storage::disk('public')->exists($municipio->alcalde_foto)) {
                        Storage::disk('public')->delete($municipio->alcalde_foto);
                    }
                }
                $validatedData['alcalde_foto'] = null;
            } else {
                // Si no se envió una nueva imagen y no se pidió eliminar,
                // asegúrate de que el valor existente se mantenga en $validatedData.
                $validatedData['alcalde_foto'] = $municipio->alcalde_foto;
            }

            // Actualizar el municipio con los datos validados
            $municipio->update($validatedData);

            return response()->json([
                'message' => 'Municipio actualizado exitosamente.',
                'data' => $municipio
            ], 200);
        } catch (Exception $e) {
            // Se mantiene el log de error para capturar excepciones inesperadas en producción.
            Log::error('Error al actualizar municipio: ' . $e->getMessage(), ['exception' => $e]);

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
