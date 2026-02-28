<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,              // 1
            UserSeeder::class,                           // 2
            
            // Estructura base del campus
            IniciCategoriesSeasonSeeder::class,            // 3 - Categories i Temporades
            IniciSeasonsUpdateSeeder::class,              // 4 - Actualitzar a 3 temporades
            IniciTeachersCSVSeeder::class,                 // 5 - Teachers
            IniciCoursesMapeadoCSVSeeder::class,           // 6 - Cursos
            IniciCourseTeacherSeeder::class,                // 7 - Relacions Course-Teacher
            CampusSpaceSeeder::class,                       // 8 - Espais
            CampusTimeSlotSeeder::class,                    // 9 - Franges horàries
            CampusCourseScheduleSeeder::class,              // 10 - Horaris
            
            // Importació d'estudiants (només estudiants, sense matrícules)
            IniciStudentsOnlySeeder::class,                // 11 - Students
            
            // Seeders comentats (no farem servir)
            // IniciStudentsImprovedSeeder::class,         // (amb matrícules - no usar)
            // IniciSetupCompleteSeeder::class,             // (versió antiga)
        ]);
    }
}

