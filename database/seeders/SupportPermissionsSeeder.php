<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SupportPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Crear permisos para gestión de solicitudes de soporte
        $permissions = [
            'support-requests.view' => 'Ver solicitudes de soporte',
            'support-requests.create' => 'Crear solicitudes de soporte',
            'support-requests.edit' => 'Editar solicitudes de soporte',
            'support-requests.delete' => 'Eliminar solicitudes de soporte',
            'support-requests.resolve' => 'Resolver solicitudes de soporte',
            'support-requests.bulk-update' => 'Actualización masiva de solicitudes',
        ];

        foreach ($permissions as $name => $description) {
            Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);
        }

        // Asignar permisos a roles administrativos
        $adminRoles = ['admin', 'superadmin', 'director'];
        
        foreach ($adminRoles as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $role->givePermissionTo([
                    'support-requests.view',
                    'support-requests.edit',
                    'support-requests.delete',
                    'support-requests.resolve',
                    'support-requests.bulk-update',
                ]);
            }
        }

        // Asignar permisos básicos a otros roles
        $basicRoles = ['secretaria', 'gestor'];
        
        foreach ($basicRoles as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $role->givePermissionTo([
                    'support-requests.view',
                    'support-requests.resolve',
                ]);
            }
        }

        // Todos los usuarios autenticados pueden crear solicitudes
        $allRoles = Role::all();
        foreach ($allRoles as $role) {
            $role->givePermissionTo('support-requests.create');
        }
    }
}
