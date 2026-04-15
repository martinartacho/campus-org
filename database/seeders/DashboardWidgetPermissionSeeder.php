<?php

namespace Database\Seeders;

use App\Models\DashboardWidgetPermission;
use Illuminate\Database\Seeder;

class DashboardWidgetPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Widgets disponibles (nuevos system_stats + secretaria + manager)
        $availableWidgets = [
            'manager_visio_general' => 'Visión General Manager',
            'system_stats_users' => 'Estadísticas de Usuarios',
            'system_stats_courses' => 'Estadísticas de Cursos',
            'system_stats_registrations' => 'Estadísticas de Matriculaciones',
            'system_stats_categories' => 'Estadísticas de Categorías',
            'system_stats_seasons' => 'Estadísticas de Temporadas',
            'system_stats_events' => 'Estadísticas de Eventos',
                        'secretaria_documents' => 'Documentos de Secretaría',
            'secretaria_registrations' => 'Matriculaciones de Secretaría',
            'secretaria_certificates' => 'Certificados de Secretaría',
            'task_board' => 'Tauler de Tasques',
        ];

        // Roles Manager Group
        $managerRoles = ['director', 'manager', 'coordinacio', 'gestio', 'comunicacio', 'secretaria', 'editor'];

        // Configuración inicial por rol (todos los widgets habilitados por defecto)
        $roleWidgets = [
            'director' => ['manager_visio_general', 'system_stats_users', 'system_stats_courses', 'system_stats_registrations', 'system_stats_categories', 'system_stats_seasons', 'system_stats_events'],
            'manager' => ['manager_visio_general', 'system_stats_users', 'system_stats_courses', 'system_stats_registrations', 'system_stats_categories', 'system_stats_seasons', 'system_stats_events'],
            'coordinacio' => ['manager_visio_general', 'system_stats_users', 'system_stats_courses', 'system_stats_registrations', 'system_stats_categories', 'system_stats_seasons', 'system_stats_events'],
            'gestio' => ['manager_visio_general', 'system_stats_users', 'system_stats_courses', 'system_stats_registrations', 'task_board'],
            'coordinacio' => ['manager_visio_general', 'system_stats_users', 'system_stats_courses', 'system_stats_registrations', 'task_board'],
            'comunicacio' => ['manager_visio_general', 'system_stats_users', 'system_stats_events', 'task_board'],
            'secretaria' => [
                'secretaria_documents',      // Widget principal
                'secretaria_registrations', // Widget secundari
                'secretaria_certificates',  // Widget opcional
                'system_stats_users',        // Complementari
                'system_stats_registrations',
                'task_board',            // Gestió de tasques
            ],
            'editor' => ['manager_visio_general', 'system_stats_courses', 'system_stats_categories'],
        ];

        // Crear permisos para cada rol
        foreach ($managerRoles as $role) {
            $widgets = $roleWidgets[$role] ?? [];

            foreach ($availableWidgets as $widgetKey => $widgetName) {
                DashboardWidgetPermission::updateOrCreate(
                    [
                        'widget_name' => $widgetKey,
                        'role_name' => $role,
                    ],
                    [
                        'enabled' => in_array($widgetKey, $widgets),
                    ]
                );
            }
        }

        $this->command->info('Dashboard widget permissions seeded successfully!');
    }
}
