<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DashboardQuickActionPermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'role_name',
        'action_name',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    /**
     * Obtener quick actions para un rol específico
     */
    public static function getQuickActionsForRole($roleName)
    {
        return self::where('role_name', $roleName)
            ->where('enabled', true)
            ->pluck('action_name')
            ->toArray();
    }

    /**
     * Sincronizar quick actions para un rol
     */
    public static function syncRoleQuickActions($roleName, array $actionNames)
    {
        // Deshabilitar todos los existentes
        self::where('role_name', $roleName)->update(['enabled' => false]);

        // Habilitar los especificados
        foreach ($actionNames as $actionName) {
            self::updateOrCreate(
                ['role_name' => $roleName, 'action_name' => $actionName],
                ['enabled' => true]
            );
        }
    }
}
