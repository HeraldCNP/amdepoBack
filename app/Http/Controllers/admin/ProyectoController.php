<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\proyecto\StoreProyectoRequest;
use App\Http\Requests\proyecto\UpdateProyectoRequest;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class ProyectoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $proyectos = Proyecto::with('user')->orderBy('created_at', 'desc')->get();
            return response()->json($proyectos, 200);
        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error al obtener proyectos: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'message' => 'Ocurrió un error al intentar obtener los proyectos.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProyectoRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();

            $validatedData['user_id'] = Auth::id(); // Asignar el ID del usuario autenticado

            if ($request->hasFile('archivoPdf')) { // 'archivo' es el nombre del campo del formulario
                $tituloSlug = \Illuminate\Support\Str::slug($validatedData['titulo'] ?? 'sin-titulo');
                $extension = $request->file('archivoPdf')->getClientOriginalExtension();
                $fileName = $tituloSlug . '-' . time() . '.' . $extension;

                $filePath = $request->file('archivoPdf')->storeAs('proyectos', $fileName, 'public');
                $validatedData['archivoPdf'] = $filePath; // Asignar al campo de la DB
            }

            $proyecto = Proyecto::create($validatedData);

            return response()->json([
                'message' => 'Proyecto creado exitosamente.',
                'data' => $proyecto
            ], 201);
        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error al crear proyecto: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'message' => 'Ocurrió un error al intentar crear el proyecto.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Proyecto $proyecto): JsonResponse
    {
        try {
            $proyecto->load('user');
            return response()->json($proyecto, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Proyecto no encontrado.'], 404);
        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error al obtener proyecto: ' . $e->getMessage(), ['exception' => $e, 'proyecto_id' => $proyecto->id]);
            return response()->json([
                'message' => 'Ocurrió un error al intentar obtener el proyecto.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */


    /**
     * Remove the specified resource from storage.
     */
    public function eliminar($id): JsonResponse
    {
        try {
            $proyecto = Proyecto::findOrFail($id);
            // dd($circular->imagenCircular);

            if (Storage::disk('public')->exists($proyecto->archivoPdf)) {
                Storage::disk('public')->delete($proyecto->archivoPdf);
            }

            $proyecto->delete();

            return response()->json(null, 204);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Documento no encontrado.'], 404);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Error al eliminar el documento.', 'message' => $e->getMessage()], 500);
        }
    }
}
