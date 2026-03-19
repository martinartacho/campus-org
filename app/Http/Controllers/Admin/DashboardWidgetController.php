<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DashboardWidgetPermission;
use Illuminate\Http\Request;

class DashboardWidgetController extends Controller
{
    /**
     * Mostrar formulario de gestión de widgets
     */
    public function index()
    {
        $availableWidgets = [
            'recent_registrations' => 'Matriculaciones Recientes',
            'courses_status' => 'Estado de Cursos',
            'pending_registrations' => 'Matriculaciones Pendientes',
            'support_tickets' => 'Tickets de Soporte',
            'alerts' => 'Alertas y Notificaciones',
        ];

        $managerRoles = ['director', 'coordinacio', 'gestio', 'comunicacio', 'secretaria', 'editor'];

        // Obtener configuración actual
        $widgetPermissions = [];
        foreach ($managerRoles as $role) {
            $widgetPermissions[$role] = DashboardWidgetPermission::getWidgetsForRole($role);
        }

        return view('admin.dashboard_widgets.index', compact('availableWidgets', 'managerRoles', 'widgetPermissions'));
    }

    /**
     * Guardar configuración de widgets
     */
    public function update(Request $request)
    {
        $managerRoles = ['director', 'coordinacio', 'gestio', 'comunicacio', 'secretaria', 'editor'];
        
        // Obtener widgets seleccionados por rol
        $selectedWidgets = $request->input('widgets', []);

        // Procesar cada rol
        foreach ($managerRoles as $roleName) {
            $widgetNames = $selectedWidgets[$roleName] ?? [];
            DashboardWidgetPermission::syncRoleWidgets($roleName, $widgetNames);
        }

        return redirect()->route('admin.dashboard-widgets.index')
            ->with('success', 'Configuración de widgets actualizada correctamente');
    }

    /**
     * Obtener widgets para un rol específico (API)
     */
    public function getRoleWidgets($roleName)
    {
        $widgets = DashboardWidgetPermission::getWidgetsForRole($roleName);
        
        return response()->json([
            'widgets' => $widgets
        ]);
    }
}
