<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class AuditSpatie extends Command
{
    protected $signature = 'app:audit-spatie';
    protected $description = 'Auditoría completa de Spatie Permissions';

    public function handle()
    {
        $this->info('=== AUDITORÍA COMPLETA SPATIE ===');
        
        // 1. Configuración
        $teamsEnabled = config('permission.teams', false);
        $this->info("✅ Teams habilitado: " . ($teamsEnabled ? 'SÍ' : 'NO'));
        
        // 2. Tablas
        $tables = [
            'roles',
            'permissions', 
            'model_has_roles',
            'model_has_permissions',
            'role_has_permissions'
        ];
        
        $this->info("\n=== TABLAS ===");
        foreach ($tables as $table) {
            $exists = Schema::hasTable($table);
            $this->info(($exists ? '✅' : '❌') . " Tabla {$table}: " . ($exists ? 'EXISTE' : 'NO EXISTE'));
        }
        
        // 3. Datos
        $this->info("\n=== DATOS ===");
        $this->info("👥 Roles: " . Role::count());
        $this->info("🔑 Permisos: " . Permission::count());
        $this->info("👤 Usuarios con roles: " . User::role(Role::all())->count());
        
        // 4. Roles específicos
        $this->info("\n=== ROLES DETALLADOS ===");
        foreach (Role::all() as $role) {
            $this->info("🎯 {$role->name}: " . $role->permissions->count() . " permisos");
        }
        
        // 5. Usuario admin
        $adminUser = User::find(1);
        if ($adminUser) {
            $this->info("\n=== USUARIO ADMIN (ID:1) ===");
            $this->info("Nombre: " . $adminUser->name);
            $this->info("Roles: " . $adminUser->getRoleNames()->implode(', '));
            $this->info("Permisos directos: " . $adminUser->getDirectPermissions()->count());
            $this->info("Total permisos: " . $adminUser->getAllPermissions()->count());
        }
        
        $this->info("\n=== CONCLUSIÓN ===");
        $this->info("📊 Estás usando Spatie SOLO para permisos, con sistema propio de roles");
        $this->info("🔧 Teams: NO habilitado (correcto para tu caso)");
        $this->info("💡 Recomendación: Unificar los seeders y mantener solo RolesAndPermissionsSeeder");
    }
}