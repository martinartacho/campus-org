<?php

namespace Database\Seeders;

// use App\Models\User;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            UserSeeder::class,
            CampusCoursesSeeder::class,
            RegistrationImportSeeder::class,
            // NotificationSeeder::class,
            // CampusExempleSeeder::class,
            // Otros seeders que tengas...
        ]);
    }
}
