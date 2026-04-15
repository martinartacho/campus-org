<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DashboardWidgetPermission;
use App\Models\DashboardQuickActionPermission;
use Illuminate\Http\Request;

class DashboardWidgetController extends Controller
{
    /**
     * Mostrar configuración de widgets y quick actions por rol
     */
    public function index()
    {
        $roles = [
            'director', 'manager', 'coordinacio', 'gestio', 
            'comunicacio', 'secretaria', 'editor'
        ];
        
        $widgets = [
            'system_stats_users' => [
                'name' => 'Estadístiques d\'Usuaris',
                'description' => 'Mostra estadístiques generals d\'usuaris del sistema',
                'icon' => 'bi-people-fill'
            ],
            'system_stats_courses' => [
                'name' => 'Estadístiques de Cursos',
                'description' => 'Mostra estadístiques de cursos actius i inactius',
                'icon' => 'bi-book-fill'
            ],
            'system_stats_registrations' => [
                'name' => 'Estadístiques de Matriculacions',
                'description' => 'Mostra estadístiques de matriculacions per estat',
                'icon' => 'bi-person-check-fill'
            ],
            'system_stats_categories' => [
                'name' => 'Estadístiques de Categories',
                'description' => 'Mostra estadístiques de categories de cursos',
                'icon' => 'bi-tags-fill'
            ],
            'system_stats_seasons' => [
                'name' => 'Estadístiques de Temporades',
                'description' => 'Mostra estadístiques de temporades acadèmiques',
                'icon' => 'bi-calendar-fill'
            ],
            'system_stats_events' => [
                'name' => 'Estadístiques d\'Esdeveniments',
                'description' => 'Mostra estadístiques d\'esdeveniments del campus',
                'icon' => 'bi-calendar-event-fill'
            ],
                        'secretaria_documents' => [
                'name' => 'Documents de Secretaria',
                'description' => 'Gestió de documents amb estadístiques completes',
                'icon' => 'bi-file-earmark-text'
            ],
            'secretaria_registrations' => [
                'name' => 'Matriculacions de Secretaria',
                'description' => 'Matriculacions pendents/actives/completades',
                'icon' => 'bi-person-check'
            ],
            'secretaria_certificates' => [
                'name' => 'Certificats de Secretaria',
                'description' => 'Gestió de certificats i títols',
                'icon' => 'bi-award'
            ],
            'task_board' => [
                'name' => 'Tauler de Tasques',
                'description' => 'Gestió de tasques i projectes',
                'icon' => 'bi-kanban'
            ]
        ];
        
        $quickActions = [
            'add_user' => [
                'name' => 'Afegir Usuari',
                'description' => 'Crear nou usuari al sistema',
                'icon' => 'bi-person-plus',
                'route' => 'admin.users.index'
            ],
            'add_course' => [
                'name' => 'Afegir Curs',
                'description' => 'Crear nou curs al campus',
                'icon' => 'bi-plus-circle',
                'route' => 'campus.courses.create'
            ],
            'add_season' => [
                'name' => 'Afegir Temporada',
                'description' => 'Crear nova temporada acadèmica',
                'icon' => 'bi-calendar-plus',
                'route' => 'campus.seasons.create'
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
                'route' => 'support.index'
            ],
            'manage_resources' => [
                'name' => 'Re-Cursos',
                'description' => 'Gestionar recursos del campus',
                'icon' => 'bi-folder',
                'route' => 'campus.resources.index'
            ],
            'help_management' => [
                'name' => 'Gestió d\'Ajuda',
                'description' => 'Crear, editar, eliminar documents d\'Ajuda',
                'icon' => 'bi-question-circle',
                'route' => 'admin.help.index'
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
        
        // Obtener configuración actual
        $widgetPermissions = [];
        $quickActionPermissions = [];
        
        foreach ($roles as $role) {
            $widgetPermissions[$role] = DashboardWidgetPermission::where('role_name', $role)
                ->pluck('enabled', 'widget_name')
                ->toArray();
                
            $quickActionPermissions[$role] = DashboardQuickActionPermission::where('role_name', $role)
                ->pluck('enabled', 'action_name')
                ->toArray();
        }
        
        \Log::info('DashboardWidgetController::index', [
            'widget_permissions_count' => array_sum(array_map('count', $widgetPermissions)),
            'quick_action_permissions_count' => array_sum(array_map('count', $quickActionPermissions)),
            'widget_permissions_sample' => array_slice($widgetPermissions, 0, 2),
            'quick_action_permissions_sample' => array_slice($quickActionPermissions, 0, 2),
        ]);
        
        return view('admin.dashboard_widgets.index', compact(
            'roles', 
            'widgets', 
            'quickActions',
            'widgetPermissions',
            'quickActionPermissions'
        ));
    }
    
    /**
     * Actualizar permisos de widgets
     */
    public function updateWidgets(Request $request)
    {
        \Log::info('DashboardWidgetController::updateWidgets START', [
            'request_data' => $request->all(),
            'widgets_input' => $request->input('widgets'),
        ]);

        $validated = $request->validate([
            'widgets' => 'required|array',
            'widgets.*' => 'required|array',
            'widgets.*.*' => 'required|boolean'
        ]);
        
        \Log::info('DashboardWidgetController::updateWidgets VALIDATED', [
            'validated' => $validated,
        ]);
        
        $updatedCount = 0;
        foreach ($validated['widgets'] as $role => $widgets) {
            \Log::info('DashboardWidgetController::updateWidgets PROCESSING ROLE', [
                'role' => $role,
                'widgets' => $widgets,
            ]);
            
            foreach ($widgets as $widgetName => $enabled) {
                $permission = DashboardWidgetPermission::updateOrCreate(
                    ['role_name' => $role, 'widget_name' => $widgetName],
                    ['enabled' => $enabled]
                );
                
                \Log::info('DashboardWidgetController::updateWidgets UPDATED', [
                    'role' => $role,
                    'widget_name' => $widgetName,
                    'enabled' => $enabled,
                    'permission_id' => $permission->id,
                    'was_created' => $permission->wasRecentlyCreated,
                ]);
                
                $updatedCount++;
            }
        }
        
        \Log::info('DashboardWidgetController::updateWidgets COMPLETE', [
            'updated_count' => $updatedCount,
            'total_permissions' => DashboardWidgetPermission::count(),
        ]);
        
        return redirect()->route('admin.dashboard_widgets.index')
            ->with('success', "Permisos de widgets actualizados correctamente ({$updatedCount} cambios)");
    }
    
    /**
     * Actualizar permisos de quick actions
     */
    public function updateQuickActions(Request $request)
    {
        \Log::info('DashboardWidgetController::updateQuickActions START', [
            'request_data' => $request->all(),
            'quick_actions_input' => $request->input('quick_actions'),
        ]);

        $validated = $request->validate([
            'quick_actions' => 'required|array',
            'quick_actions.*' => 'required|array',
            'quick_actions.*.*' => 'required|boolean'
        ]);
        
        \Log::info('DashboardWidgetController::updateQuickActions VALIDATED', [
            'validated' => $validated,
        ]);
        
        $updatedCount = 0;
        foreach ($validated['quick_actions'] as $role => $actions) {
            \Log::info('DashboardWidgetController::updateQuickActions PROCESSING ROLE', [
                'role' => $role,
                'actions' => $actions,
            ]);
            
            foreach ($actions as $actionName => $enabled) {
                $permission = DashboardQuickActionPermission::updateOrCreate(
                    ['role_name' => $role, 'action_name' => $actionName],
                    ['enabled' => $enabled]
                );
                
                \Log::info('DashboardWidgetController::updateQuickActions UPDATED', [
                    'role' => $role,
                    'action_name' => $actionName,
                    'enabled' => $enabled,
                    'permission_id' => $permission->id,
                    'was_created' => $permission->wasRecentlyCreated,
                ]);
                
                $updatedCount++;
            }
        }
        
        \Log::info('DashboardWidgetController::updateQuickActions COMPLETE', [
            'updated_count' => $updatedCount,
            'total_permissions' => DashboardQuickActionPermission::count(),
        ]);
        
        return redirect()->route('admin.dashboard_widgets.index')
            ->with('success', "Permisos de acciones rápidas actualizados correctamente ({$updatedCount} cambios)");
    }
}
