<?php

namespace Database\Seeders;

use App\Models\DashboardWidgetPermission;
use Illuminate\Database\Seeder;

class DashboardWidgetPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Widgets disponibles
        $availableWidgets = [
            'recent_registrations' => 'Matriculaciones Recientes',
            'courses_status' => 'Estado de Cursos',
            'pending_registrations' => 'Matriculaciones Pendientes',
            'support_tickets' => 'Tickets de Soporte',
            'alerts' => 'Alertas y Notificaciones',
        ];

        // Roles Manager Group
        $managerRoles = ['director', 'coordinacio', 'gestio', 'comunicacio', 'secretaria', 'editor'];

        // Configuración inicial por rol
        $roleWidgets = [
            'director' => ['recent_registrations', 'courses_status'],
            'coordinacio' => ['recent_registrations', 'courses_status', 'pending_registrations', 'support_tickets', 'alerts'],
            'gestio' => ['courses_status', 'support_tickets'],
            'comunicacio' => ['recent_registrations', 'support_tickets'],
            'secretaria' => ['recent_registrations', 'pending_registrations'],
            'editor' => ['courses_status'],
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
