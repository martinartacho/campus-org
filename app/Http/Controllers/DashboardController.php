<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        \Log::info('DashboardController - User access', [
            'user' => $user->email,
            'roles' => $user->roles->pluck('name')->toArray(),
            'hasAnyRole_manager' => $user->hasAnyRole(['director', 'manager', 'coordinacio', 'gestio', 'comunicacio', 'secretaria', 'editor']),
            'hasAnyRole_admin' => $user->hasAnyRole(['admin', 'super-admin']),
        ]);

        // Usar rol activo si existe, sino verificar todos los roles
        $activeRole = session('active_role');
        
        if ($activeRole && $user->hasRole($activeRole)) {
            // Usar rol activo específico
            \Log::info('DashboardController - Using active role', ['user' => $user->email, 'active_role' => $activeRole]);
            
            if ($activeRole === 'admin' || $activeRole === 'super-admin') {
                // Admin y Super-Admin ahora usan widgets
                $adminData = app(\App\Services\Dashboard\AdminDashboardData::class)->build();
                $managerData = app(\App\Services\Dashboard\ManagerDashboardData::class)->build($user, $activeRole);
                
                // Combinar datos: stats de admin + widgets de manager
                $data = array_merge($adminData, [
                    'widgets' => $managerData['widgets'] ?? []
                ]);
                
                return view('dashboard', $data);
            } elseif (in_array($activeRole, ['director', 'manager', 'coordinacio', 'gestio', 'comunicacio', 'secretaria', 'editor'])) {
                // Manager Group: Widgets específicos por sub-rol
                $data = app(\App\Services\Dashboard\ManagerDashboardData::class)->build($user, $activeRole);
                return view('dashboard', $data);
            } elseif ($activeRole === 'treasury') {
                $data = app(\App\Services\Dashboard\TreasuryDashboardData::class)->build($user);
                return view('dashboard', $data);
            } elseif ($activeRole === 'teacher') {
                $data = app(\App\Services\Dashboard\TeacherDashboardData::class)->build($user);
                return view('dashboard', $data);
            } elseif ($activeRole === 'student') {
                $data = app(\App\Services\Dashboard\StudentDashboardData::class)->build($user);
                return view('dashboard', $data);
            }
        }
        
        // Fallback: si no hay rol activo, usar comportamiento original
        if ($user->hasAnyRole(['admin', 'super-admin'])) {
            \Log::info('DashboardController - Admin user (fallback)', ['user' => $user->email, 'active_role' => $activeRole]);
            $data = app(\App\Services\Dashboard\AdminDashboardData::class)->build();
            \Log::info('DashboardController - Admin data', ['data_keys' => array_keys($data)]);
            return view('dashboard', $data);
        
        } elseif ($user->hasAnyRole(['director', 'manager', 'coordinacio', 'gestio', 'comunicacio', 'secretaria', 'editor'])) {
            // Manager Group fallback: Widgets específicos por sub-rol
            $activeRole = $user->roles->whereIn('name', ['director', 'manager', 'coordinacio', 'gestio', 'comunicacio', 'secretaria', 'editor'])->first()->name;
            $data = app(\App\Services\Dashboard\ManagerDashboardData::class)->build($user, $activeRole);
            \Log::info('DashboardController - Manager data (fallback)', [
                'user' => $user->email,
                'active_role' => $activeRole,
                'data_keys' => array_keys($data),
                'stats_count' => count($data['stats'] ?? []),
                'widgets_count' => count($data['widgets'] ?? []),
            ]);
            return view('dashboard', $data);
        } elseif ($user->hasAnyRole(['treasury'])) {
            $data = app(\App\Services\Dashboard\TreasuryDashboardData::class)
                ->build($user);
            return view('dashboard', $data);
        } elseif ($user->hasRole('teacher')) {
            $data = app(\App\Services\Dashboard\TeacherDashboardData::class)
                ->build($user);
            return view('dashboard', $data);
        } elseif ($user->hasRole('student')) {
            $data = app(\App\Services\Dashboard\StudentDashboardData::class)
                ->build($user);
            return view('dashboard', $data);
        }

        return view('dashboard', []);
    }

    public function switchRole($role)
    {
        $user = auth()->user();
        
        // Verificar que el usuario tiene este rol
        if (!$user->hasRole($role)) {
            abort(403, 'No tienes acceso a este rol');
        }

        // Guardar el rol activo en sesión
        session(['active_role' => $role]);

        return redirect()->route('dashboard')->with('success', 'Has cambiado al rol: ' . ucfirst($role));
    }
}
