<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear permisos para gestión de usuarios
        $permissions = [
            'users.list',
            'users.create',
            'users.edit',
            'users.delete',
            'users.update-status'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Crear roles
        $superadminRole = Role::firstOrCreate(['name' => 'superadmin']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Asignar permisos
        // Superadmin: todos los permisos
        $superadminRole->givePermissionTo($permissions);

        // Admin: puede listar, crear y editar usuarios (no eliminar)
        $adminRole->givePermissionTo([
            'users.list',
            'users.create',
            'users.edit',
            'users.update-status'
        ]);

        // User: solo puede ver la lista (sin modificar)
        $userRole->givePermissionTo(['users.list']);
    }
}
