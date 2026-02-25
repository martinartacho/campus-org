<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use App\Services\FCMService;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles');
        
        // Filtro por nombre
        if ($request->filled('search_name')) {
            $query->where('name', 'like', '%' . $request->search_name . '%');
        }
        
        // Filtro por email
        if ($request->filled('search_email')) {
            $query->where('email', 'like', '%' . $request->search_email . '%');
        }
        
        // Filtro por rol
        if ($request->filled('search_role')) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->search_role);
            });
        }
        
        // Filtro por fecha de registro
        if ($request->filled('search_date_from')) {
            $query->whereDate('created_at', '>=', $request->search_date_from);
        }
        
        if ($request->filled('search_date_to')) {
            $query->whereDate('created_at', '<=', $request->search_date_to);
        }
        
        // Ordenamiento
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (in_array($sortBy, ['name', 'email', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        }
        
        $users = $query->paginate(10)->withQueryString();
        $roles = \Spatie\Permission\Models\Role::all();
        
        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array'
        ]);

        $user = User::create($request->only(['name', 'email', 'password']));
        $user->assignRole($request->roles);
        $user->syncPermissions($request->input('permissions', []));

        return redirect()->route('admin.users.index')
            ->with('success', __('site.user_created'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();

        // Agrupar permisos por prefijo (ej: notifications, users, roles, etc.)
        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        });

        return view('admin.users.edit', compact('user', 'roles', 'permissions'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'roles' => 'array',
            'permissions' => 'array',
        ]);

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        // Protección para el admin (usuario ID 1)
        if ($user->id === 1) {
            // Forzamos siempre el rol admin y todos los permisos
            if (!auth()->user()->hasRole('admin')) {
                $user->assignRole('admin');
            }

            if (!$user->hasAllPermissions(Permission::all())) {
                $user->syncPermissions(Permission::all());
            }

            return redirect()->route('admin.users.index')
                ->with('error', 'El usuario administrador principal no puede perder privilegios.');
        }

        // Usuarios normales: sincronizar roles y permisos
        $user->syncRoles($request->input('roles', []));
        $user->syncPermissions($request->input('permissions', []));

        return redirect()->route('admin.users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', __('site.user_deleted'));
    }
    /*
    para limpiar
    public function sendTestNotification(Request $request, FCMService $fcmService)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->user_id);

        // Verificar tokens válidos
        if (!$user->fcmTokens()->where('is_valid', true)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'El usuario no tiene tokens FCM válidos'
            ], 400);
        }

        $title = "Notificación de Prueba";
        $body = "¡Funciona! Hora: " . now()->format('H:i:s');

        $result = $fcmService->sendToUser($user, $title, $body);

        return response()->json([
            'success' => (bool) $result,
            'message' => $result ? 'Notificación enviada' : 'Error al enviar',
            'fcm_response' => $result
        ]);
    }
    */

}
