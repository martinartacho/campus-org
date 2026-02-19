<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">
            Permisos
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <a href="{{ route('admin.permissions.create') }}" class="mb-4 inline-block bg-indigo-600 text-white px-4 py-2 rounded">Crear Permiso</a>

            <div class="bg-white shadow-md rounded p-4">
                <table class="min-w-full table-auto">
                    <thead>
                        <tr>
                            <th class="text-left px-4 py-2">Nombre</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($permissions as $permission)
                            <tr class="border-b">
                                <td class="px-4 py-2">{{ $permission->name }}</td>
                                <td class="px-4 py-2 flex gap-2">
                                    <a href="{{ route('admin.permissions.edit', $permission) }}" class="text-blue-600">Editar</a>
                                    <form method="POST" action="{{ route('admin.permissions.destroy', $permission) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('¿Eliminar este permiso?')" class="text-red-600">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $permissions->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
