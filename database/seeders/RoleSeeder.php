<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ['admin', 'gestor', 'treasury', 'editor', 'teacher', 'student',  'user', 'invited'];
    
        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }
        
        // Asignar rol 'admin' al primer usuario (opcional)
        $admin = \App\Models\User::first();
        if ($admin) {
            $admin->assignRole('admin');
        }
    }
}
