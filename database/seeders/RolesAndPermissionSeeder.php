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
        $createUser = Permission::create(['name' => 'create user']);
        $editUser = Permission::create(['name' => 'edit user']);
        $deleteUser = Permission::create(['name' => 'delete user']);
        $showUser = Permission::create(['name' => 'publish user']);

        // Asignar permisos a roles
        $adminRole->givePermissionTo($createUser);
        $adminRole->givePermissionTo($editUser);
        $adminRole->givePermissionTo($deleteUser);
        $adminRole->givePermissionTo($showUser);

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
