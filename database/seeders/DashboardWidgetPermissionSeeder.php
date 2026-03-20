<?php

namespace Database\Seeders;

use App\Models\DashboardWidgetPermission;
use Illuminate\Database\Seeder;

class DashboardWidgetPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Widgets disponibles (nuevos system_stats)
        $availableWidgets = [
            'system_stats_users' => 'Estadísticas de Usuarios',
            'system_stats_courses' => 'Estadísticas de Cursos',
            'system_stats_registrations' => 'Estadísticas de Matriculaciones',
            'system_stats_categories' => 'Estadísticas de Categorías',
            'system_stats_seasons' => 'Estadísticas de Temporadas',
            'system_stats_events' => 'Estadísticas de Eventos',
        ];

        // Roles Manager Group
        $managerRoles = ['director', 'manager', 'coordinacio', 'gestio', 'comunicacio', 'secretaria', 'editor'];

        // Configuración inicial por rol (todos los widgets habilitados por defecto)
        $roleWidgets = [
            'director' => ['system_stats_users', 'system_stats_courses', 'system_stats_registrations', 'system_stats_categories', 'system_stats_seasons', 'system_stats_events'],
            'manager' => ['system_stats_users', 'system_stats_courses', 'system_stats_registrations', 'system_stats_categories', 'system_stats_seasons', 'system_stats_events'],
            'coordinacio' => ['system_stats_users', 'system_stats_courses', 'system_stats_registrations', 'system_stats_categories', 'system_stats_seasons', 'system_stats_events'],
            'gestio' => ['system_stats_users', 'system_stats_courses', 'system_stats_registrations'],
            'comunicacio' => ['system_stats_users', 'system_stats_events'],
            'secretaria' => ['system_stats_users', 'system_stats_registrations'],
            'editor' => ['system_stats_courses', 'system_stats_categories'],
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
