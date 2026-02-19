<?php

namespace Database\Seeders\Test;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class TreasuryTestSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'teachers.view',
            'teachers.financial_data.view',
            'consents.request',
            'consents.view',
            'payments.export',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $role = Role::firstOrCreate(['name' => 'treasury']);
        $role->syncPermissions($permissions);
    }
}
