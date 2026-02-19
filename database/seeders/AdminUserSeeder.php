<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str; 

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            // Crear usuario admin si no existe
        $admin = User::firstOrCreate(
            ['email' => 'admin@upg.test'],
            [
                'name' => 'Administrador',
                'password' => Hash::make(env('SEEDER_DEFAULT_PASSWORD')),
                'email_verified_at' => Carbon::now(),
            ]
        );
        
        $admin->assignRole('admin');
    }
}
