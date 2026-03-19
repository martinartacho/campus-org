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

        // 📊 ESTADÍSTIQUES DEL SISTEMA para coordinacio
        if ($activeRole === 'coordinacio') {
            // Usuarios - usar datos existentes de admin
            $stats['total_users'] = $adminData['stats']['total_users'] ?? 0;
            $stats['active_users'] = \App\Models\User::where('email_verified_at', '!=', null)->count();
            $stats['new_users'] = \App\Models\User::where('created_at', '>=', now()->subDays(30))->count();
            
            // Cursos - usar datos existentes de admin y añadir subtotales por status
            $stats['total_courses'] = $adminData['stats']['total_courses'] ?? 0;
            $stats['active_courses'] = \App\Models\CampusCourse::where('is_active', true)->count();
            $stats['full_courses'] = \App\Models\CampusCourse::where('max_students', '>', 0)
                ->withCount('students')
                ->get()
                ->filter(function($course) {
                    return $course->students_count >= $course->max_students;
                })->count();
            
            // Subtotales de cursos por status
            $stats['courses_by_status'] = [];
            $courseStatuses = ['planning', 'draft', 'active', 'completed', 'archived'];
            foreach ($courseStatuses as $status) {
                $stats['courses_by_status'][$status] = \App\Models\CampusCourse::where('status', $status)->count();
            }
            
            // Profesores - usar datos existentes de admin
            $stats['total_teachers'] = $adminData['stats']['teacher_count'] ?? 0;
            $stats['active_teachers'] = \App\Models\CampusTeacher::whereHas('courses')->count();
            $stats['pending_teachers'] = \App\Models\CampusTeacher::whereDoesntHave('courses')->count();
            
            // Estudiantes - usar datos existentes de admin y añadir subtotales por status
            $stats['total_students'] = $adminData['stats']['student_count'] ?? 0;
            $stats['active_registrations'] = $adminData['stats']['active_registrations'] ?? 0;
            $stats['completed_registrations'] = $adminData['stats']['completed_registrations'] ?? 0;
            
            // Subtotales de matrículas por academic_status
            $stats['registrations_by_status'] = [];
            $registrationStatuses = ['pending', 'enrolled', 'completed', 'cancelled', 'dropped'];
            foreach ($registrationStatuses as $status) {
                $stats['registrations_by_status'][$status] = \App\Models\CampusCourseStudent::where('academic_status', $status)->count();
            }
            
            // Temporadas - usar datos existentes de admin
            $stats['total_seasons'] = $adminData['stats']['total_seasons'] ?? 0;
            $stats['current_season'] = \App\Models\CampusSeason::where('slug', config('campus.current_season', 'curs-2025-26'))->count();
            $stats['past_seasons'] = \App\Models\CampusSeason::where('slug', '!=', config('campus.current_season', 'curs-2025-26'))->count();
            
            // Eventos - usar datos existentes de admin
            $stats['total_events'] = $adminData['stats']['total_events'] ?? 0;
            $stats['upcoming_events'] = 0; // Por ahora, no hay datos de fechas
            $stats['past_events'] = 0; // Por ahora, no hay datos de fechas
            
            // Feedback - usar datos existentes de admin
            $stats['total_feedback'] = $adminData['stats']['total_feedback'] ?? 0;
            $stats['pending_feedback'] = $adminData['stats']['pending_feedback'] ?? 0;
            $stats['resolved_feedback'] = $adminData['stats']['responded_feedback'] ?? 0;
        }

        // 🧠 WIDGETS según configuración de la base de datos
        if ($activeRole) {
            $widgetNames = \App\Models\DashboardWidgetPermission::getWidgetsForRole($activeRole);
            
            // Mapeo de nombres de widgets a rutas de componentes
            $widgetMap = [
                'courses_status' => 'components.dashboard.widgets.courses_status',
                'support_tickets' => 'components.dashboard.widgets.support_tickets',
                'alerts' => 'components.dashboard.widgets.alerts',
            ];
            
            // 🚨 PRIORIZAR ALERTAS: Siempre primero si está habilitado
            $prioritizedWidgets = [];
            if (in_array('alerts', $widgetNames)) {
                $prioritizedWidgets[] = 'components.dashboard.widgets.alerts';
                // Eliminar alerts del array original para no duplicar
                $widgetNames = array_diff($widgetNames, ['alerts']);
            }
            
            // Añadir el resto de widgets
            foreach ($widgetNames as $widgetName) {
                if (isset($widgetMap[$widgetName])) {
                    $prioritizedWidgets[] = $widgetMap[$widgetName];
                }
            }
            
            $widgets = $prioritizedWidgets;
        } else {
            // Fallback: comportamiento original si no hay rol activo
            // 🚨 PRIORIZAR ALERTAS siempre primero
            $widgets[] = 'components.dashboard.widgets.alerts';
            
            if ($user->can('campus.courses.view')) {
                $widgets[] = 'components.dashboard.widgets.courses_status';
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
