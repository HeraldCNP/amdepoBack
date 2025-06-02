<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Convenio; // Asegúrate de importar el modelo Convenio
use Illuminate\Http\JsonResponse;
use App\Http\Requests\convenio\StoreConvenioRequest; // Importa los Request
use App\Http\Requests\convenio\UpdateConvenioRequest; // Importa los Request
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ConvenioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $convenios = Convenio::with('user')->orderBy('created_at', 'desc')->get();
            return response()->json($convenios, 200);
        } catch (Exception $e) {
            Log::error('Error al obtener convenios: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'message' => 'Ocurrió un error al intentar obtener los convenios.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreConvenioRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();

            $validatedData['user_id'] = Auth::id(); // Asignar el ID del usuario autenticado

            if ($request->hasFile('archivoPdf')) { // 'archivo' es el nombre del campo del formulario
                $tituloSlug = \Illuminate\Support\Str::slug($validatedData['titulo'] ?? 'sin-titulo');
                $extension = $request->file('archivoPdf')->getClientOriginalExtension();
                $fileName = $tituloSlug . '-' . time() . '.' . $extension;

                $filePath = $request->file('archivoPdf')->storeAs('convenios', $fileName, 'public');
                $validatedData['archivoPdf'] = $filePath; // Asignar al campo de la DB
            }

            $convenio = Convenio::create($validatedData);

            return response()->json([
                'message' => 'Convenio creado exitosamente.',
                'data' => $convenio
            ], 201);
        } catch (Exception $e) {
            Log::error('Error al crear convenio: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'message' => 'Ocurrió un error al intentar crear el convenio.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Convenio $convenio): JsonResponse
    {
        try {
            $convenio->load('user');
            return response()->json($convenio, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Convenio no encontrado.'], 404);
        } catch (Exception $e) {
            Log::error('Error al obtener convenio: ' . $e->getMessage(), ['exception' => $e, 'convenio_id' => $convenio->id]);
            return response()->json([
                'message' => 'Ocurrió un error al intentar obtener el convenio.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function eliminar($id): JsonResponse
    {
        try {
            $convenio = Convenio::findOrFail($id);
            // dd($circular->imagenCircular);

            if (Storage::disk('public')->exists($convenio->archivoPdf)) {
                Storage::disk('public')->delete($convenio->archivoPdf);
            }

            $convenio->delete();

            return response()->json(null, 204);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Documento no encontrado.'], 404);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Error al eliminar el documento.', 'message' => $e->getMessage()], 500);
        }
    }
}
