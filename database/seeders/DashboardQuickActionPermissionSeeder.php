<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DashboardQuickActionPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Definir roles manager
        $managerRoles = ['director', 'manager', 'coordinacio', 'gestio', 'comunicacio', 'secretaria', 'editor'];
        
        // Definir quick actions disponibles
        $availableQuickActions = [
            'add_user' => [
                'name' => 'Afegir Usuari',
                'description' => 'Crear nuevo usuario en el sistema',
                'icon' => 'bi-person-plus',
                'route' => 'admin.users.create'
            ],
            'add_course' => [
                'name' => 'Afegir Curs',
                'description' => 'Crear nuevo curso',
                'icon' => 'bi-plus-circle',
                'route' => 'admin.courses.create'
            ],
            'add_season' => [
                'name' => 'Afegir Temporada',
                'description' => 'Crear nueva temporada académica',
                'icon' => 'bi-calendar-plus',
                'route' => 'admin.seasons.create'
            ],
            'manage_resources' => [
                'name' => 'Recursos',
                'description' => 'Gestionar recursos del campus',
                'icon' => 'bi-folder',
                'route' => 'admin.resources.index'
            ],
            'help_management' => [
                'name' => 'Gestió d\'Ajuda',
                'description' => 'Gestionar sistema de ayuda',
                'icon' => 'bi-question-circle',
                'route' => 'admin.help.index'
            ],
            'help_center' => [
                'name' => 'Centre d\'Ajuda',
                'description' => 'Acceder al centro de ayuda',
                'icon' => 'bi-life-preserver',
                'route' => 'help.index'
            ],
            'manage_widgets' => [
                'name' => 'Widgets',
                'description' => 'Gestionar Dashboard Widgets dels perfils manager',
                'icon' => 'bi-grid-3x3-gap',
                'route' => 'admin.dashboard_widgets.index'
            ],
            'support_management' => [
                'name' => 'Suport',
                'description' => 'Gestió de Suport',
                'icon' => 'bi-headset',
                'route' => 'admin.support-requests.index'
            ],
            'calendar_management' => [
                'name' => 'Gestió de Calendari',
                'description' => 'Gestionar propera temporada fent servir el model calendari de recursos',
                'icon' => 'bi-calendar-week',
                'route' => 'campus.resources.calendar'
            ],
            'releases_management' => [
                'name' => 'Releases',
                'description' => 'Gestionar Releases',
                'icon' => 'bi-box-seam',
                'route' => 'admin.releases.index'
            ],
            'task_management' => [
                'name' => 'Tasques',
                'description' => 'Gestió de projectes i tasques del campus',
                'icon' => 'bi-kanban',
                'route' => 'tasks.boards.index'
            ]
        ];

        // Asignar quick actions a roles manager
        foreach ($managerRoles as $role) {
            foreach ($availableQuickActions as $actionKey => $actionData) {
                DB::table('dashboard_quick_action_permissions')->updateOrInsert([
                    'role_name' => $role,
                    'action_name' => $actionKey,
                ], [
                    'enabled' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Configuración específica por rol
        $roleQuickActions = [
            'director' => array_keys($availableQuickActions), // Director: todos
            'manager' => [
                'add_course', 'add_season', 'manage_resources', 
                'calendar_management', 'releases_management', 
                'support_management', 'help_management', 'manage_widgets',
                'task_management'
            ], // Manager: 9 accions
            'coordinacio' => [
                'add_course', 'add_season', 'help_management', 
                'calendar_management', 'support_management', 'help_center',
                'task_management'
            ], // Coordinación: 7 accions
            'gestio' => [
                'add_course', 'add_season', 'manage_resources',
                'task_management'
            ], // Gestió: 4 accions
            'comunicacio' => [
                'add_course', 'help_management', 'support_management',
                'task_management'
            ], // Comunicació: 4 accions
            'secretaria' => [
                'add_course', 'help_management', 'task_management'
            ], // Secretaria: 3 accions
            'editor' => [
                'add_course', 'task_management'
            ], // Editor: 2 accions
        ];

        // Aplicar configuración específica
        foreach ($roleQuickActions as $role => $allowedActions) {
            // Deshabilitar actions no permitidos
            $allActions = array_keys($availableQuickActions);
            $disabledActions = array_diff($allActions, $allowedActions);
            
            foreach ($disabledActions as $action) {
                DB::table('dashboard_quick_action_permissions')
                    ->where('role_name', $role)
                    ->where('action_name', $action)
                    ->update(['enabled' => false]);
            }
        }

        $this->command->info('Dashboard Quick Action Permissions seeded successfully!');
    }
}
