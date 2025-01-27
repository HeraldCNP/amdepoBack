<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Resources\RoleResource;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB; 


class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get(); // Carga los permisos con eager loading
        return RoleResource::collection($roles);
    }

    public function store(StoreRoleRequest $request)
    {
        try {
            DB::beginTransaction();
            $role = Role::create(['name' => $request->name]);
            $role->syncPermissions($request->input('permissions', []));
            DB::commit();
            return response()->json(['message' => 'Rol creado correctamente', 'role' => new RoleResource($role)], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return response()->json(['message' => 'Error al crear el rol: ' . $e->getMessage()], 500);
            
        }
    }

    public function show(string $roleId) // Cambia el tipo de dato a string para el id
    {
        try {
            $role = Role::findOrFail($roleId); // Intenta encontrar el rol, lanza excepción si no existe
            $role->load('permissions');
            return new RoleResource($role);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Rol no existe'], 404); // Respuesta personalizada
        }
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        // dd($role);
        try {
            DB::beginTransaction();
            $role->update(['name' => $request->name]);
            $role->syncPermissions($request->input('permissions', []));
            DB::commit();
            return response()->json(['message' => 'Rol actualizado correctamente', 'role' => new RoleResource($role)]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al actualizar el rol: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(Role $role)
    {
        try {
            $role->delete();
            return response()->json(['message' => 'Rol eliminado correctamente']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar el rol: ' . $e->getMessage()], 500);
        }
    }

    public function getAllPermissions()
    {
        $permissions = Permission::all();
        return response()->json($permissions);
    }

    public function assignPermissions(Request $request, Role $role)
    {
        $role->syncPermissions($request->input('permissions', []));
        return response()->json(['message' => 'Permisos asignados correctamente', 'role' => new RoleResource($role)]);
    }
}
