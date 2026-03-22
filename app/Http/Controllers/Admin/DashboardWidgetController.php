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
            'support_stats' => [
                'name' => __('campus.support_statistics'),
                'description' => __('campus.support_statistics') . ' - ' . __('campus.support_dashboard_subtitle'),
                'icon' => 'bi-headset'
            ]
        ];
        
        $quickActions = [
            'add_user' => [
                'name' => 'Afegir Usuari',
                'description' => 'Permet crear nous usuaris en el sistema',
                'icon' => 'bi-person-plus'
            ],
            'add_course' => [
                'name' => 'Afegir Curs',
                'description' => 'Permet crear nous cursos',
                'icon' => 'bi-plus-circle'
            ],
            'add_season' => [
                'name' => 'Afegir Temporada',
                'description' => 'Permet crear noves temporades acadèmiques',
                'icon' => 'bi-calendar-plus'
            ],
            'manage_resources' => [
                'name' => 'Gestionar Recursos',
                'description' => 'Permet gestionar recursos del campus',
                'icon' => 'bi-folder'
            ],
            'help_management' => [
                'name' => 'Gestió d\'Ajuda',
                'description' => 'Permet gestionar el sistema d\'ajuda',
                'icon' => 'bi-question-circle'
            ],
            'help_center' => [
                'name' => 'Centre d\'Ajuda',
                'description' => 'Permet accedir al centre d\'ajuda',
                'icon' => 'bi-life-preserver'
            ],
            'support_management' => [
                'name' => __('campus.support_management'),
                'description' => __('campus.support_management') . ' - ' . __('campus.support_dashboard_subtitle'),
                'icon' => 'bi-headset'
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
