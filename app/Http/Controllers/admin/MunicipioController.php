<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Municipio;
use Illuminate\Http\Request;

class MunicipioController extends Controller
{
    public function index()
    {
        $municipios = Municipio::all(); // Obtener todos los municipios
        return response()->json($municipios); // Devolver los municipios en formato JSON
    }

    public function store(Request $request)
    {
        $municipio = Municipio::create($request->all()); // Crear un nuevo municipio
        return response()->json($municipio, 201); // Devolver el municipio creado con código 201 (Created)
    }

    
}
