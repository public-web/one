<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PreviabilizacionSocialRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the previabilizacion-social role
        $role = Role::firstOrCreate(
            ['name' => 'previabilizacion-social'],
            ['guard_name' => 'web']
        );

        // Assign only view permission for banco proyectos (read-only access)
        $viewPermission = Permission::where('name', 'view_banco_proyectos')->first();

        if ($viewPermission) {
            $role->syncPermissions([$viewPermission]);
            $this->command->info('✓ Rol "previabilizacion-social" creado con permiso de solo lectura para banco proyectos');
        } else {
            $this->command->error('✗ Permiso "view_banco_proyectos" no encontrado');
        }
    }
}
