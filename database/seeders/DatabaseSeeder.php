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
            RolesAndPermissionsSeeder::class,  // 1
            UserSeeder::class,                 // 2
            CampusSeeder::class,               // 3
            
            // s'ha de executar per crear registres durant la primera temporada, que esta condicionada per les dades de ordres ja executada
            StudentsCourseSeeder::class,       // 4
            
            // crea estructura minima de espais i horari per Re-Cursos
            CampusSpaceSeeder::class,          // 5
            CampusTimeSlotSeeder::class,       // 6
            CampusCourseScheduleSeeder::class, // 7
            
            // Sistema de importación de registros (de import_registrations)
            RegistrationImportSeeder::class,   // 8
            
            // NotificationSeeder::class,
            CampusExempleSeeder::class,
            // Otros seeders que tengas...
        ]);


    
        // User::factory(10)->create();

        
    }
}
