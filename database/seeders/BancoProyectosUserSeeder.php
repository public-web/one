<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class BancoProyectosUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear permisos para banco de proyectos
        $permissions = [
            'view_banco_proyectos',
            'create_banco_proyectos',
            'edit_banco_proyectos',
            'delete_banco_proyectos',
            'restore_banco_proyectos',
            'force_delete_banco_proyectos',
            'export_banco_proyectos',
            'import_banco_proyectos',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission],
                ['guard_name' => 'web']
            );
        }

        // Crear o obtener el rol banco-proyectos
        $role = Role::firstOrCreate(
            ['name' => 'banco-proyectos'],
            ['guard_name' => 'web']
        );

        // Asignar todos los permisos al rol
        $role->syncPermissions($permissions);

        // Crear el usuario
        $user = User::firstOrCreate(
            ['email' => 'bancoproyectos@example.com'],
            [
                'name' => 'Banco de Proyectos',
                'password' => Hash::make('BancoProyectos2024!'),
                'email_verified_at' => now(),
            ]
        );

        // Asignar el rol al usuario
        $user->assignRole('banco-proyectos');

        $this->command->info('✓ Usuario de Banco de Proyectos creado exitosamente');
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════');
        $this->command->info('  CREDENCIALES DE ACCESO');
        $this->command->info('═══════════════════════════════════════════');
        $this->command->info('  Email:    bancoproyectos@example.com');
        $this->command->info('  Password: BancoProyectos2024!');
        $this->command->info('  Rol:      banco-proyectos');
        $this->command->info('═══════════════════════════════════════════');
        $this->command->info('');
    }
}
