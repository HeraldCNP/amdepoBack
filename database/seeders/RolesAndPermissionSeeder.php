<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear roles
        $adminRole = Role::create(['name' => 'admin']);
        $editorRole = Role::create(['name' => 'editor']);
        $userRole = Role::create(['name' => 'user']);

        // Crear Usuarios
        $createUser = Permission::create(['name' => 'user.create', 'description' => 'Crear Usuario']);
        $showUser = Permission::create(['name' => 'user.read', 'description' => 'Ver Usuario']);
        $editUser = Permission::create(['name' => 'user.update', 'description' => 'Editar Usuario']);
        $deleteUser = Permission::create(['name' => 'user.delete', 'description' => 'Eliminar Usuario']);
        $listUser = Permission::create(['name' => 'user.index', 'description' => 'Listar Usuarios']);

        $createRol = Permission::create(['name' => 'rol.create', 'description' => 'Crear Rol']);
        $deleteRol = Permission::create(['name' => 'rol.delete', 'description' => 'Eliminar Rol']);
        $listRol = Permission::create(['name' => 'rol.index', 'description' => 'Listar Roles']);


        // Asignar permisos a roles
        $adminRole->givePermissionTo($createUser);
        $adminRole->givePermissionTo($editUser);
        $adminRole->givePermissionTo($deleteUser);
        $adminRole->givePermissionTo($showUser);
        $adminRole->givePermissionTo($listUser);

        $editorRole->givePermissionTo($createUser);
        $editorRole->givePermissionTo($editUser);

        // O asignar todos los permisos a un rol directamente:
        //$adminRole->syncPermissions(Permission::all());

        // Asignar un rol a un usuario (ejemplo)
        $user = \App\Models\User::find(1); // Encuentra un usuario existente
        if ($user) {
            $user->assignRole('admin');
        }
    }
}
