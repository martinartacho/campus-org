<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DashboardWidgetPermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'widget_name',
        'role_name',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    /**
     * Obtener widgets disponibles para un rol específico
     */
    public static function getWidgetsForRole(string $roleName): array
    {
        return self::where('role_name', $roleName)
            ->where('enabled', true)
            ->pluck('widget_name')
            ->toArray();
    }

    /**
     * Obtener todos los roles que tienen un widget específico
     */
    public static function getRolesForWidget(string $widgetName): array
    {
        return self::where('widget_name', $widgetName)
            ->where('enabled', true)
            ->pluck('role_name')
            ->toArray();
    }

    /**
     * Sincronizar permisos de widgets para un rol
     */
    public static function syncRoleWidgets(string $roleName, array $widgetNames): void
    {
        // Desactivar todos los widgets existentes para este rol
        self::where('role_name', $roleName)->update(['enabled' => false]);

        // Activar los widgets especificados
        foreach ($widgetNames as $widgetName) {
            self::updateOrCreate(
                ['widget_name' => $widgetName, 'role_name' => $roleName],
                ['enabled' => true]
            );
        }
    }
}
