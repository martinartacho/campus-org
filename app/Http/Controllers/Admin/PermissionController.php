<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::latest()->paginate(10);
        return view('admin.permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('admin.permissions.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->validationRules());

        Permission::create([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'] ?? 'web', // Permite flexibilidad
        ]);

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', 'Permiso creado correctamente.');
    }

    public function edit(Permission $permission)
    {
        return view('admin.permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $data = $request->validate($this->validationRules($permission));

        $permission->update([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'] ?? $permission->guard_name,
        ]);

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', 'Permiso actualizado.');
    }

    public function destroy(Permission $permission)
    {
        try {
            $permission->delete();
            return redirect()
                ->route('admin.permissions.index')
                ->with('success', 'Permiso eliminado.');
        } catch (QueryException $e) {
            return redirect()
                ->route('admin.permissions.index')
                ->with('error', 'No se puede eliminar el permiso porque está en uso.');
        }
    }

    /**
     * Reglas de validación reutilizables
     */
    private function validationRules(Permission $permission = null): array
    {
        return [
            'name' => 'required|string|unique:permissions,name,' . ($permission ? $permission->id : ''),
            'guard_name' => 'sometimes|string|in:web,api', // Solo permite guards válidos
        ];
    }
}