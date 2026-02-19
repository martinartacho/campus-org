<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Roles') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <a href="{{ route('admin.roles.create') }}" class="inline-block mb-4 px-4 py-2 bg-indigo-600 text-white rounded">
                {{ __('Crear Rol') }}
            </a>

            <div class="bg-white shadow-md rounded overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permisos</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                        <tr class="border-b">
                            <td class="px-6 py-4">{{ $role->name }}</td>
                            <td class="px-6 py-4">{{ $role->permissions->pluck('name')->join(', ') }}</td>
                            <td class="px-6 py-4 flex space-x-2">
                                <a href="{{ route('admin.roles.edit', $role) }}" class="text-blue-600 hover:underline">Editar</a>
                                <form method="POST" action="{{ route('admin.roles.destroy', $role) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('¿Eliminar este rol?')">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="p-4">
                    {{ $roles->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
