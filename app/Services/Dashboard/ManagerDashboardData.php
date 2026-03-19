<?php

namespace App\Services\Dashboard;

use App\Models\User;


class ManagerDashboardData
{
    public function build(User $user, string $activeRole = null): array
    {
        $adminData = app(AdminDashboardData::class)->raw();

        // DEBUG: Ver qué datos tenemos
        \Log::info('ManagerDashboardData DEBUG', [
            'user' => $user->email,
            'roles' => $user->roles->pluck('name')->toArray(),
            'activeRole' => $activeRole,
            'adminData_keys' => array_keys($adminData),
        ]);

        $stats = [];
        $widgets = [];

        // 📊 STATS según permisos
        if ($user->can('campus.courses.view')) {
            $stats['courses'] = $adminData['total_courses'];
        }

        if ($user->can('campus.teachers.view')) {
            $stats['teachers'] = $adminData['teacher_count'];
        }

        if ($user->can('campus.students.view')) {
            $stats['students'] = $adminData['student_count'];
        }

        if ($user->can('campus.registrations.view')) {
            $stats['registrations'] = $adminData['total_registrations'];
        }

        // 🧠 WIDGETS según configuración de la base de datos
        if ($activeRole) {
            $widgetNames = \App\Models\DashboardWidgetPermission::getWidgetsForRole($activeRole);
            
            // Mapeo de nombres de widgets a rutas de componentes
            $widgetMap = [
                'recent_registrations' => 'components.dashboard.widgets.recent_registrations',
                'courses_status' => 'components.dashboard.widgets.courses_status',
                'pending_registrations' => 'components.dashboard.widgets.pending_registrations',
                'support_tickets' => 'components.dashboard.widgets.support_tickets',
                'alerts' => 'components.dashboard.widgets.alerts',
            ];
            
            foreach ($widgetNames as $widgetName) {
                if (isset($widgetMap[$widgetName])) {
                    $widgets[] = $widgetMap[$widgetName];
                }
            }
        } else {
            // Fallback: comportamiento original si no hay rol activo
            if ($user->can('campus.courses.view')) {
                $widgets[] = 'components.dashboard.widgets.courses_status';
            }
            if ($user->can('campus.registrations.view')) {
                $widgets[] = 'components.dashboard.widgets.recent_registrations';
            }
            if ($user->can('campus.registrations.manage')) {
                $widgets[] = 'components.dashboard.widgets.pending_registrations';
            }
            if ($user->can('campus.support.view')) {
                $widgets[] = 'components.dashboard.widgets.support_tickets';
            }
            if ($user->can('campus.courses.manage')) {
                $widgets[] = 'components.dashboard.widgets.alerts';
            }
        }

        // DEBUG: Ver qué estamos devolviendo
        \Log::info('ManagerDashboardData RETURN', [
            'stats_count' => count($stats),
            'widgets_count' => count($widgets),
            'stats_keys' => array_keys($stats),
            'widgets' => $widgets,
        ]);

        return [
            'stats' => $stats,
            'widgets' => $widgets,
        ];
    }
}
