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

        // ESTADÍSTIQUES DEL SISTEMA para roles manager (excepto secretaria)
        if (in_array($activeRole, ['director', 'manager', 'coordinacio', 'gestio', 'comunicacio', 'editor', 'admin', 'super-admin'])) {
            // Usuarios - usar datos completos de admin con fallback
            $stats['total_users'] = $adminData['stats']['total_users'] ?? 0;
            $stats['active_users'] = $adminData['stats']['active_users'] ?? \App\Models\User::where('email_verified_at', '!=', null)->count();
            $stats['new_users'] = $adminData['stats']['new_users'] ?? \App\Models\User::where('created_at', '>=', now()->subDays(30))->count();
            
            // Cursos - usar datos completos de admin con fallback
            $stats['total_courses'] = $adminData['stats']['total_courses'] ?? 0;
            $stats['active_courses'] = $adminData['stats']['active_courses'] ?? \App\Models\CampusCourse::where('is_active', true)->count();
            $stats['full_courses'] = $adminData['stats']['full_courses'] ?? 0;
            
            // Profesores - usar datos completos de admin con fallback
            $stats['total_teachers'] = $adminData['stats']['teacher_count'] ?? 0;
            $stats['active_teachers'] = $adminData['stats']['active_teachers'] ?? \App\Models\CampusTeacher::whereHas('courses')->count();
            $stats['pending_teachers'] = $adminData['stats']['pending_teachers'] ?? \App\Models\CampusTeacher::whereDoesntHave('courses')->count();
            
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
            
            // Temporadas - usar datos completos de admin con fallback
            $stats['total_seasons'] = $adminData['stats']['total_seasons'] ?? 0;
            $stats['current_season'] = $adminData['stats']['current_season'] ?? \App\Models\CampusSeason::where('slug', config('campus.current_season', 'curs-2025-26'))->count();
            $stats['past_seasons'] = $adminData['stats']['past_seasons'] ?? \App\Models\CampusSeason::where('slug', '!=', config('campus.current_season', 'curs-2025-26'))->count();
            
            // Eventos - usar datos completos de admin con fallback
            $stats['total_events'] = $adminData['stats']['total_events'] ?? 0;
            $stats['upcoming_events'] = $adminData['stats']['upcoming_events'] ?? 0;
            $stats['past_events'] = $adminData['stats']['past_events'] ?? 0;
            
            // Feedback - usar datos completos de admin con fallback
            $stats['total_feedback'] = $adminData['stats']['total_feedback'] ?? 0;
            $stats['pending_feedback'] = $adminData['stats']['pending_feedback'] ?? 0;
            $stats['resolved_feedback'] = $adminData['stats']['responded_feedback'] ?? 0;
            
            }

        // ESTADÍSTICAS ESPECÍFICAS PARA SECRETARIA (fuera del bloque general)
        if ($activeRole === 'secretaria') {
            // Estadísticas básicas de usuarios para el widget system_stats_users
            $stats['total_users'] = \App\Models\User::count();
            $stats['admin_count'] = \App\Models\User::role('admin')->count();
            $stats['teacher_count'] = \App\Models\CampusTeacher::count();
            $stats['student_count'] = \App\Models\CampusStudent::count();
            $stats['active_teachers'] = \App\Models\CampusTeacher::whereHas('courses')->count();
            
            // Documentos - estadísticas del módulo de documentación
            $stats['total_documents'] = \App\Models\Document::active()->count();
            
            $stats['documents_by_category'] = \App\Models\Document::active()
                ->with('category')
                ->get()
                ->groupBy('category.name')
                ->map->count();
            
            $stats['recent_documents'] = \App\Models\Document::active()
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
            
            $stats['total_downloads'] = \App\Models\DocumentDownload::where('downloaded_at', '>=', now()->subDays(30))->count();
            
            // Matriculaciones pendentes (específico para secretaria)
            $stats['pending_registrations'] = \App\Models\CampusCourseStudent::where('academic_status', 'pending')->count();
            
            $stats['recent_registrations'] = \App\Models\CampusCourseStudent::with(['student', 'course'])
                ->where('academic_status', 'pending')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
            
            // Certificados (simulado - se puede adaptar al modelo real)
            $stats['total_certificates'] = 0; // Placeholder
            $stats['certificates_by_type'] = []; // Placeholder
            $stats['recent_certificates'] = []; // Placeholder
            $stats['certificates_this_month'] = 0; // Placeholder
            $stats['certificates_this_year'] = 0; // Placeholder
        }

        // WIDGETS según configuración de la base de datos
        if ($activeRole) {
            $widgetNames = \App\Models\DashboardWidgetPermission::getWidgetsForRole($activeRole);
            
            // Mapeo de nombres de widgets a rutas de componentes
            $widgetMap = [
                'system_stats_users' => 'components.dashboard.widgets.system_stats_users',
                'system_stats_courses' => 'components.dashboard.widgets.system_stats_courses',
                'system_stats_registrations' => 'components.dashboard.widgets.system_stats_registrations',
                'system_stats_categories' => 'components.dashboard.widgets.system_stats_categories',
                'system_stats_seasons' => 'components.dashboard.widgets.system_stats_seasons',
                'system_stats_events' => 'components.dashboard.widgets.system_stats_events',
            ];
            
            // 📊 Widgets específicos para secretaria
            if ($activeRole === 'secretaria') {
                $widgetMap = array_merge($widgetMap, [
                    'secretaria_documents' => 'components.dashboard.widgets.secretaria_documents',
                    'secretaria_registrations' => 'components.dashboard.widgets.secretaria_registrations',
                ]);
            }
            
            // 🎯 PRIORIZAR WIDGETS DE ESTADÍSTICAS
            $prioritizedWidgets = [];
            
            // Añadir widgets de estadísticas del sistema
            foreach ($widgetNames as $widgetName) {
                if (isset($widgetMap[$widgetName])) {
                    $prioritizedWidgets[] = $widgetMap[$widgetName];
                }
            }
            
            $widgets = $prioritizedWidgets;
        } else {
            // Fallback: widgets de estadísticas por defecto
            $widgets = [
                'components.dashboard.widgets.system_stats_users',
                'components.dashboard.widgets.system_stats_courses',
                'components.dashboard.widgets.system_stats_registrations',
                'components.dashboard.widgets.system_stats_categories',
                'components.dashboard.widgets.system_stats_seasons',
                'components.dashboard.widgets.system_stats_events',
            ];
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
