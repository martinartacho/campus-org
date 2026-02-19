<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CampusCategory;
use App\Models\CampusSeason;
use App\Models\CampusCourse;
use App\Models\CampusRegistration;
use Carbon\Carbon;

class CampusSeeder extends Seeder
{
    public function run()
    {
        // 1. CREAR CATEGORIES
        $categories = [
            [
                'name' => 'Informàtica i Tecnologia',
                'slug' => 'informatica-tecnologia',
                'description' => 'Cursos de programació, desenvolupament web i tecnologies de la informació',
                'color' => '#3b82f6',
                'icon' => 'laptop-code',
                'order' => 1,
            ],
            [
                'name' => 'Idiomes',
                'slug' => 'idiomes',
                'description' => 'Cursos d\'idiomes estrangers per a tots els nivells',
                'color' => '#10b981',
                'icon' => 'language',
                'order' => 2,
            ],
            [
                'name' => 'Negocis i Administració',
                'slug' => 'negocis-administracio',
                'description' => 'Cursos de gestió empresarial, administració i finances',
                'color' => '#8b5cf6',
                'icon' => 'briefcase',
                'order' => 3,
            ],
            [
                'name' => 'Disseny Gràfic i Web',
                'slug' => 'disseny-grafic-web',
                'description' => 'Cursos de disseny gràfic, disseny web i multimèdia',
                'color' => '#f59e0b',
                'icon' => 'palette',
                'order' => 4,
            ],
            [
                'name' => 'Salut i Benestar',
                'slug' => 'salut-benestar',
                'description' => 'Cursos relacionats amb la salut, nutrició i benestar personal',
                'color' => '#ef4444',
                'icon' => 'heart',
                'order' => 5,
            ],
        ];

        foreach ($categories as $categoryData) {
            CampusCategory::firstOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }

        // 2. CREAR TEMPORADES
        $seasons = [
            [
                'name' => 'Curs 2024-25',
                'slug' => '2024-25',
                'academic_year' => '2024-2025',
                'registration_start' => '2024-08-01',
                'registration_end' => '2024-09-30',
                'season_start' => '2024-09-16',
                'season_end' => '2025-06-30',
                'type' => 'annual',
                'is_current' => true,
                'is_active' => true,
                'periods' => [
                    ['name' => 'Primer Quadrimestre', 'start' => '2024-09-16', 'end' => '2025-01-31'],
                    ['name' => 'Segon Quadrimestre', 'start' => '2025-02-01', 'end' => '2025-06-30'],
                ],
            ],
            [
                'name' => 'Curs 2025-26',
                'slug' => '2025-26',
                'academic_year' => '2025-2026',
                'registration_start' => '2025-08-01',
                'registration_end' => '2025-09-30',
                'season_start' => '2025-09-15',
                'season_end' => '2026-06-30',
                'type' => 'annual',
                'is_current' => false,
                'is_active' => true,
            ],
        ];

        foreach ($seasons as $seasonData) {
            CampusSeason::firstOrCreate(
                ['slug' => $seasonData['slug']],
                $seasonData
            );
        }

        // 3. CREAR CURSOS
        $currentSeason = CampusSeason::where('is_current', true)->first();
        $categoryIT = CampusCategory::where('slug', 'informatica-tecnologia')->first();
        $categoryIdiomes = CampusCategory::where('slug', 'idiomes')->first();
        $categoryNegocis = CampusCategory::where('slug', 'negocis-administracio')->first();

        $courses = [
            [
                'season_id' => $currentSeason->id,
                'category_id' => $categoryIT->id,
                'code' => 'IT101',
                'title' => 'Introducció a la Programació',
                'slug' => 'introduccio-a-la-programacio',
                'description' => 'Curs bàsic de programació utilitzant Python. Perfecte per a principiants.',
                'credits' => 6,
                'hours' => 60,
                'max_students' => 25,
                'price' => 299.99,
                'level' => 'beginner',
                'schedule' => [
                    ['day' => 'Dilluns', 'start' => '16:00', 'end' => '18:00'],
                    ['day' => 'Dimecres', 'start' => '16:00', 'end' => '18:00'],
                ],
                'start_date' => '2024-09-16',
                'end_date' => '2025-01-31',
                'requirements' => ['Conèixements bàsics d\'informàtica', 'Motivació per aprendre'],
                'objectives' => [
                    'Aprenentatge dels fonaments de la programació',
                    'Desenvolupament d\'algorismes bàsics',
                    'Resolució de problemes amb Python',
                ],
            ],
            [
                'season_id' => $currentSeason->id,
                'category_id' => $categoryIT->id,
                'code' => 'IT201',
                'title' => 'Desenvolupament Web Full Stack',
                'slug' => 'desenvolupament-web-full-stack',
                'description' => 'Curs complet de desenvolupament web amb HTML, CSS, JavaScript i Laravel.',
                'credits' => 12,
                'hours' => 120,
                'max_students' => 20,
                'price' => 599.99,
                'level' => 'intermediate',
                'schedule' => [
                    ['day' => 'Dimarts', 'start' => '18:00', 'end' => '20:00'],
                    ['day' => 'Dijous', 'start' => '18:00', 'end' => '20:00'],
                ],
                'start_date' => '2024-09-16',
                'end_date' => '2025-06-30',
                'requirements' => [
                    'Conèixements bàsics de programació',
                    'Nocions d\'HTML i CSS',
                    'Ordinador portàtil',
                ],
                'objectives' => [
                    'Crear aplicacions web completes',
                    'Implementar frontend i backend',
                    'Desplegar projectes web',
                    'Treballar amb bases de dades',
                ],
            ],
            [
                'season_id' => $currentSeason->id,
                'category_id' => $categoryIdiomes->id,
                'code' => 'ID101',
                'title' => 'Anglès Bàsic A1',
                'slug' => 'angles-basic-a1',
                'description' => 'Curs d\'anglès per a principiants. Des de zero fins al nivell A1.',
                'credits' => 4,
                'hours' => 40,
                'max_students' => 18,
                'price' => 199.99,
                'level' => 'beginner',
                'schedule' => [
                    ['day' => 'Dilluns', 'start' => '10:00', 'end' => '12:00'],
                    ['day' => 'Divendres', 'start' => '10:00', 'end' => '12:00'],
                ],
                'start_date' => '2024-09-16',
                'end_date' => '2025-01-31',
                'requirements' => ['Cap coneixement previ requerit'],
                'objectives' => [
                    'Aprenentatge de vocabulari bàsic',
                    'Comprensió de converses simples',
                    'Expressió oral i escrita bàsica',
                    'Preparació per a l\'examen A1',
                ],
            ],
            [
                'season_id' => $currentSeason->id,
                'category_id' => $categoryNegocis->id,
                'code' => 'NA201',
                'title' => 'Gestió Empresarial',
                'slug' => 'gestio-empresarial',
                'description' => 'Curs de gestió i administració d\'empreses per a emprenedors.',
                'credits' => 8,
                'hours' => 80,
                'max_students' => 22,
                'price' => 449.99,
                'level' => 'intermediate',
                'schedule' => [
                    ['day' => 'Dimarts', 'start' => '16:00', 'end' => '18:00'],
                    ['day' => 'Dijous', 'start' => '16:00', 'end' => '18:00'],
                ],
                'start_date' => '2024-09-16',
                'end_date' => '2025-06-30',
                'requirements' => ['Experiència laboral o acadèmica relacionada'],
                'objectives' => [
                    'Planificació estratègica empresarial',
                    'Gestió de recursos humans',
                    'Control financer bàsic',
                    'Desenvolupament de plans de negoci',
                ],
            ],
        ];

        foreach ($courses as $courseData) {
            CampusCourse::firstOrCreate(
                ['code' => $courseData['code']],
                $courseData
            );
        }

        // 4. ASSIGNAR PROFESSORS A CURSOS (si ja existeixen professors)
        $teachers = \App\Models\CampusTeacher::all();
        $courses = CampusCourse::all();

        if ($teachers->count() > 0 && $courses->count() > 0) {
            // Professor 1 al curs 1 (Introducció a la Programació)
            if (isset($teachers[0]) && isset($courses[0])) {
                $courses[0]->teachers()->attach($teachers[0]->id, [
                    'role' => 'teacher',
                    'hours_assigned' => 60,
                    'assigned_at' => now(),
                ]);
            }

            // Professora 2 al curs 2 (Desenvolupament Web)
            if (isset($teachers[1]) && isset($courses[1])) {
                $courses[1]->teachers()->attach($teachers[1]->id, [
                    'role' => 'teacher',
                    'hours_assigned' => 120,
                    'assigned_at' => now(),
                ]);
            }

            // Professor 1 també al curs 4 (Gestió Empresarial)
            if (isset($teachers[0]) && isset($courses[3])) {
                $courses[3]->teachers()->attach($teachers[0]->id, [
                    'role' => 'coordinator',
                    'hours_assigned' => 40,
                    'assigned_at' => now(),
                ]);
            }
        }

        // 5. CREAR ALGUNES MATRICULACIONS D'EXEMPLE
        $students = \App\Models\CampusStudent::all();
        
        if ($students->count() > 1 && $courses->count() > 0) {
            // Estudiant 1 es matricula al curs 1
            if (isset($students[0]) && isset($courses[0])) {
                CampusRegistration::firstOrCreate(
                    [
                        'student_id' => $students[0]->id,
                        'course_id' => $courses[0]->id,
                    ],
                    [
                        'registration_code' => 'MAT' . strtoupper(uniqid()),
                        'registration_date' => '2024-09-01',
                        'status' => 'confirmed',
                        'amount' => $courses[0]->price,
                        'payment_status' => 'paid',
                        'payment_due_date' => '2024-09-15',
                        'payment_method' => 'transferencia',
                        'notes' => 'Matriculació completa amb pagament per transferència',
                    ]
                );
            }

            // Estudiant 2 es matricula al curs 2
            if (isset($students[1]) && isset($courses[1])) {
                CampusRegistration::firstOrCreate(
                    [
                        'student_id' => $students[1]->id,
                        'course_id' => $courses[1]->id,
                    ],
                    [
                        'registration_code' => 'MAT' . strtoupper(uniqid()),
                        'registration_date' => '2024-09-02',
                        'status' => 'confirmed',
                        'amount' => $courses[1]->price,
                        'payment_status' => 'partial',
                        'payment_due_date' => '2024-09-20',
                        'payment_method' => 'targeta',
                        'payment_history' => [
                            [
                                'date' => now()->toISOString(),
                                'amount' => 300.00,
                                'method' => 'targeta',
                                'reference' => 'PAY' . uniqid(),
                                'notes' => 'Primer pagament',
                            ]
                        ],
                        'notes' => 'Matriculació amb pagament fraccionat',
                    ]
                );
            }

            // Estudiant 1 també al curs 3 (Anglès)
            if (isset($students[0]) && isset($courses[2])) {
                CampusRegistration::firstOrCreate(
                    [
                        'student_id' => $students[0]->id,
                        'course_id' => $courses[2]->id,
                    ],
                    [
                        'registration_code' => 'MAT' . strtoupper(uniqid()),
                        'registration_date' => '2024-09-03',
                        'status' => 'pending',
                        'amount' => $courses[2]->price,
                        'payment_status' => 'pending',
                        'payment_due_date' => '2024-09-25',
                        'payment_method' => 'efectiu',
                        'notes' => 'Matriculació pendent de confirmació',
                    ]
                );
            }
        }
    }
}