<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PreviabilizacionRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Roles to create
        $roles = [
            'previabilizacion-sig',
            'previabilizacion-tecnico',
            'previabilizacion-ambiental',
        ];

        // Get the view permission for banco proyectos
        $viewPermission = Permission::where('name', 'view_banco_proyectos')->first();

        if (!$viewPermission) {
            $this->command->error('✗ Permiso "view_banco_proyectos" no encontrado');
            return;
        }

        // Create each role
        foreach ($roles as $roleName) {
            $role = Role::firstOrCreate(
                ['name' => $roleName],
                ['guard_name' => 'web']
            );

            // Assign view permission (read-only access)
            $role->syncPermissions([$viewPermission]);

            $this->command->info("✓ Rol \"{$roleName}\" creado con permiso de solo lectura para banco proyectos");
        }

        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════');
        $this->command->info('  ROLES DE PREVIABILIZACIÓN CREADOS');
        $this->command->info('═══════════════════════════════════════════');
        $this->command->info('  • previabilizacion-sig');
        $this->command->info('  • previabilizacion-tecnico');
        $this->command->info('  • previabilizacion-ambiental');
        $this->command->info('═══════════════════════════════════════════');
        $this->command->info('');
    }
}
