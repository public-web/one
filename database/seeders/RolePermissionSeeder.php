<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear permisos para gestión de usuarios
        $userPermissions = [
            'users.list',
            'users.create',
            'users.edit',
            'users.delete',
            'users.update-status',
        ];

        // Crear permisos para gestión de roles
        $rolePermissions = [
            'roles.list',
            'roles.create',
            'roles.edit',
            'roles.delete',
        ];

        // Crear permisos para gestión de permisos
        $permissionPermissions = [
            'permissions.list',
            'permissions.create',
            'permissions.edit',
            'permissions.delete',
        ];

        // Combinar todos los permisos
        $allPermissions = array_merge($userPermissions, $rolePermissions, $permissionPermissions);

        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Crear roles
        $superadminRole = Role::firstOrCreate(['name' => 'superadmin']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Asignar permisos
        // Superadmin: todos los permisos
        $superadminRole->givePermissionTo($allPermissions);

        // Admin: puede gestionar usuarios, roles y permisos (excepto eliminar)
        $adminRole->givePermissionTo([
            'users.list',
            'users.create',
            'users.edit',
            'users.update-status',
            'roles.list',
            'roles.create',
            'roles.edit',
            'permissions.list',
        ]);

        // User: solo puede ver listas (sin modificar)
        $userRole->givePermissionTo([
            'users.list',
            'roles.list',
            'permissions.list',
        ]);
    }
}
