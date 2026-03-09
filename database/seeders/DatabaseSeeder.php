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
            InitialUsersSeeder::class,                    // 2.5 - Usuarios iniciales desde CSV
            
            // Estructura base del campus
            IniciCategoriesSeasonSeeder::class,            // 3 - Categories i Temporades
            IniciSeasonsUpdateSeeder::class,              // 4 - Actualitzar a 3 temporades
            IniciTeachersCSVSeeder::class,                 // 5 - Teachers
            IniciCoursesMapeadoCSVSeeder::class,           // 6 - Cursos
            IniciCourseTeacherSeeder::class,                // 7 - Relacions Course-Teacher
            CampusSpaceSeeder::class,                       // 8 - Espais
            CampusTimeSlotSeeder::class,                    // 9 - Franges horàries
            CampusCourseScheduleSeeder::class,              // 10 - Horaris
            
            SupportPermissionsSeeder::class,                // 11 - Permisos per gestió de suport
            IniciUsersDirSeeder::class,                     // 12 - Usuaris diretori UPGdesde CSV
            // Importació d'estudiants (només estudiants, sense matrícules)
            // IniciStudentsOnlySeeder::class,                // 11 - Students
            
            // Articles d'ajuda sobre el sistema
            HelpArticlesSeeder::class,                      // 12 - Articles d'ajuda
            HelpArticlesSeeder::class,                      // 13 - Articles d'ajuda
            
            
            // Seeders comentats (no farem servir)
            // IniciStudentsImprovedSeeder::class,         // (amb matrícules - no usar)

        ]);
    }
}

