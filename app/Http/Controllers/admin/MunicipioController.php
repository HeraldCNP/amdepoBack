<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MunicipioRequest;
use App\Models\Municipio;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException; // Importa la excepción ModelNotFoundException


class MunicipioController extends Controller
{   
    public function index(): JsonResponse
    {
        $municipios = Municipio::all();
        return response()->json($municipios);
    }


    public function store(MunicipioRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $slug = Str::slug($validatedData['nombre']);
            $municipio = Municipio::create([
                'nombre' => $validatedData['nombre'],
                'descripcion' => $validatedData['descripcion'],
                'direccion' => $validatedData['direccion'] ?? null,
                'telefono' => $validatedData['telefono'] ?? null,
                'email' => $validatedData['email'] ?? null,
                'sitio_web' => $validatedData['sitio_web'] ?? null,
                'latitud' => $validatedData['latitud'] ?? null,
                'longitud' => $validatedData['longitud'] ?? null,
                'poblacion' => $validatedData['poblacion'] ?? null,
                'superficie' => $validatedData['superficie'] ?? null,
                'historia' => $validatedData['historia'] ?? null,
                'gentilicio' => $validatedData['gentilicio'] ?? null,
                'alcalde_nombre' => $validatedData['alcalde_nombre'] ?? null,
                'alcalde_foto' => $validatedData['alcalde_foto'] ?? null,
                'alcalde_descripcion' => $validatedData['alcalde_descripcion'] ?? null,
                'slug' => $slug, // <-- Aquí incluyes el slug
                'user_id' => auth()->id(),
            ]);
    
            return response()->json($municipio, 201);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Error al crear el municipio.', 'message' => $e->getMessage()], 500);
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

    public function update(MunicipioRequest $request, $id): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $slug = Str::slug($validatedData['nombre']);

            $municipio = Municipio::findOrFail($id);
            $municipio->update([
                'nombre' => $validatedData['nombre'],
                'descripcion' => $validatedData['descripcion'],
                // ... otros campos
                'slug' => $slug,
            ]);

            return response()->json($municipio);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Error al actualizar el municipio.'], 500);
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
