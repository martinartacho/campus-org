<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DashboardWidgetsPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Crear permiso para gestión de widgets
        Permission::firstOrCreate([
            'name' => 'dashboard.widgets.manage',
            'guard_name' => 'web'
        ]);

        // Asignar permiso a roles admin y super-admin
        $adminRoles = ['admin', 'super-admin'];
        
        foreach ($adminRoles as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $role->givePermissionTo('dashboard.widgets.manage');
            }
        }

        $this->command->info('Dashboard widgets permission seeded successfully!');
    }
}
