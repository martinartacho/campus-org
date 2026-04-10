<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\TaskBoard;
use App\Models\TaskList;
use App\Models\Task;
use Illuminate\Support\Facades\DB;

class TaskSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Desactivar checks de claus externes temporalment
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Netejar taules existents
        Task::query()->delete();
        TaskList::query()->delete();
        TaskBoard::query()->delete();
        
        // Reactivar checks de claus externes
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Obtenir usuaris de prova
        $admin = User::where('email', 'campus@upg.cat')->first(); // Superadmin
        $teacher = User::whereHas('teacher')->first(); // Primer professor
        $student = User::whereHas('student')->first(); // Primer estudiant

        if (!$admin) {
            $this->command->error('No s\'ha trobat l\'usuari admin.');
            return;
        }

        // Si no hi ha professor o estudiant, utilitzar l'admin
        $teacher = $teacher ?: $admin;
        $student = $student ?: $admin;

        // Crear taulers de prova
        $boards = [
            [
                'name' => 'Coordinació General',
                'description' => 'Tasques de coordinació del campus',
                'type' => 'global',
                'visibility' => 'public',
                'created_by' => $admin->id,
                'lists' => [
                    ['name' => 'Pendents', 'color' => '#6B7280'],
                    ['name' => 'En curs', 'color' => '#3B82F6'],
                    ['name' => 'Revisió', 'color' => '#F59E0B'],
                    ['name' => 'Completat', 'color' => '#10B981']
                ],
                'tasks' => [
                    [
                        'title' => 'Preparar reunió de setmana',
                        'description' => 'Preparar ordre del dia i documents per la reunió setmanal',
                        'list_name' => 'Pendents',
                        'priority' => 'high',
                        'assigned_to' => $admin->id,
                        'due_date' => now()->addDays(2)->toDateString()
                    ],
                    [
                        'title' => 'Revisar informes mensuals',
                        'description' => 'Revisar i aprovar els informes d\'activitats del mes',
                        'list_name' => 'En curs',
                        'priority' => 'medium',
                        'assigned_to' => $admin->id,
                        'due_date' => now()->addDays(5)->toDateString()
                    ],
                    [
                        'title' => 'Actualitzar documentació',
                        'description' => 'Actualitzar guies i manuals del campus',
                        'list_name' => 'Pendents',
                        'priority' => 'low',
                        'assigned_to' => $teacher->id,
                        'due_date' => now()->addWeek()->toDateString()
                    ]
                ]
            ],
            [
                'name' => 'Curs de Programació 2026',
                'description' => 'Tasques relacionades amb el curs de programació',
                'type' => 'course',
                'visibility' => 'team',
                'created_by' => $teacher->id,
                'lists' => [
                    ['name' => 'Planificació', 'color' => '#8B5CF6'],
                    ['name' => 'Desenvolupament', 'color' => '#3B82F6'],
                    ['name' => 'Revisió', 'color' => '#F59E0B'],
                    ['name' => 'Entregat', 'color' => '#10B981']
                ],
                'tasks' => [
                    [
                        'title' => 'Dissenyar mòdul 1',
                        'description' => 'Crear contingut i exercicis per al primer mòdul',
                        'list_name' => 'Planificació',
                        'priority' => 'high',
                        'assigned_to' => $teacher->id,
                        'due_date' => now()->addDays(3)->toDateString()
                    ],
                    [
                        'title' => 'Preparar avaluació inicial',
                        'description' => 'Crear test diagnòstic per als estudiants',
                        'list_name' => 'Planificació',
                        'priority' => 'medium',
                        'assigned_to' => $teacher->id,
                        'due_date' => now()->addDays(7)->toDateString()
                    ],
                    [
                        'title' => 'Configurar entorn de desenvolupament',
                        'description' => 'Preparar entorns virtuals per als estudiants',
                        'list_name' => 'Desenvolupament',
                        'priority' => 'high',
                        'assigned_to' => $teacher->id,
                        'due_date' => now()->addDays(4)->toDateString()
                    ],
                    [
                        'title' => 'Revisar projecte estudiant',
                        'description' => 'Corregir i donar feedback sobre el projecte entregat',
                        'list_name' => 'Revisió',
                        'priority' => 'medium',
                        'assigned_to' => $teacher->id,
                        'due_date' => now()->addDay()->toDateString()
                    ]
                ]
            ],
            [
                'name' => 'Incidències Actives',
                'description' => 'Seguiment d\'incidències i problemes tècnics',
                'type' => 'team',
                'visibility' => 'team',
                'created_by' => $admin->id,
                'lists' => [
                    ['name' => 'Nova', 'color' => '#EF4444'],
                    ['name' => 'En procés', 'color' => '#F59E0B'],
                    ['name' => 'Espera resposta', 'color' => '#6B7280'],
                    ['name' => 'Resolta', 'color' => '#10B981']
                ],
                'tasks' => [
                    [
                        'title' => 'Error en inscripcions',
                        'description' => 'Els estudiants no poden completar el formulari d\'inscripció',
                        'list_name' => 'En procés',
                        'priority' => 'urgent',
                        'assigned_to' => $admin->id,
                        'due_date' => now()->addDay()->toDateString()
                    ],
                    [
                        'title' => 'Actualitzar certificat SSL',
                        'description' => 'El certificat SSL expira en 15 dies',
                        'list_name' => 'Nova',
                        'priority' => 'high',
                        'assigned_to' => $admin->id,
                        'due_date' => now()->addDays(10)->toDateString()
                    ],
                    [
                        'title' => 'Optimitzar càrrega de pàgina',
                        'description' => 'El temps de càrrega és superior a 5 segons',
                        'list_name' => 'Espera resposta',
                        'priority' => 'medium',
                        'assigned_to' => null,
                        'due_date' => now()->addWeeks(2)->toDateString()
                    ]
                ]
            ]
        ];

        // Crear taulers i les seves dades
        foreach ($boards as $boardData) {
            $board = TaskBoard::create([
                'name' => $boardData['name'],
                'description' => $boardData['description'],
                'type' => $boardData['type'],
                'visibility' => $boardData['visibility'],
                'created_by' => $boardData['created_by'],
            ]);

            // Crear llistes
            $listMap = [];
            foreach ($boardData['lists'] as $index => $listData) {
                $list = TaskList::create([
                    'board_id' => $board->id,
                    'name' => $listData['name'],
                    'color' => $listData['color'],
                    'order' => $index + 1,
                    'is_default' => $index === 0,
                ]);
                $listMap[$listData['name']] = $list->id;
            }

            // Crear tasques
            foreach ($boardData['tasks'] as $index => $taskData) {
                Task::create([
                    'list_id' => $listMap[$taskData['list_name']],
                    'title' => $taskData['title'],
                    'description' => $taskData['description'],
                    'assigned_to' => $taskData['assigned_to'],
                    'priority' => $taskData['priority'],
                    'due_date' => $taskData['due_date'],
                    'status' => 'pending',
                    'order_in_list' => $index + 1,
                    'created_by' => $boardData['created_by'],
                ]);
            }
        }

        $this->command->info('Sistema de tasques inicialitzat correctament.');
        $this->command->info('Taulers creats: ' . count($boards));
        $this->command->info('Tasques totals: ' . Task::count());
    }
}
