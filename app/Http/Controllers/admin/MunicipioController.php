<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\municipio\StoreMunicipioRequest;
use App\Http\Requests\municipio\UpdateMunicipioRequest;
use App\Http\Requests\MunicipioRequest;
use App\Models\Municipio;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException; // Importa la excepción ModelNotFoundException
use Illuminate\Support\Facades\Log;

class MunicipioController extends Controller
{
    public function index(): JsonResponse
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
        // StoreMunicipioRequest ya maneja la validación y errores 422.
        // Aquí solo capturamos errores que puedan ocurrir durante la creación en DB.
        try {
            $municipio = Municipio::create($request->validated());

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
        // UpdateMunicipioRequest ya maneja la validación y errores 422.
        try {
            $municipio->update($request->validated());

            return response()->json([
                'message' => 'Municipio actualizado exitosamente.',
                'data' => $municipio
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Municipio no encontrado para actualizar.',
                'error' => $e->getMessage()
            ], 404);
        } catch (Exception $e) {
            Log::error('Error al actualizar municipio: ' . $e->getMessage());

            return response()->json([
                'message' => 'Ocurrió un error al intentar actualizar el municipio.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $municipio = Municipio::findOrFail($id);
            $municipio->delete();

            return response()->json(['message' => 'Municipio eliminado correctamente'], 410);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Error al eliminar el municipio.', 'message' => $e->getMessage()], 500);
        }
    }
}
