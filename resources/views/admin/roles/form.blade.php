<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ isset($role) ? 'Editar Rol' : 'Crear Rol' }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded p-6">
                <form method="POST" action="{{ isset($role) ? route('admin.roles.update', $role) : route('admin.roles.store') }}">
                    @csrf
                    @if(isset($role)) @method('PUT') @endif

                    <div class="mb-4">
                        <x-input-label for="name" value="Nombre del Rol" />
                        <x-text-input id="name" type="text" name="name" value="{{ old('name', $role->name ?? '') }}" class="mt-1 block w-full" required />
                    </div>

                    <div class="mb-6">
                        <x-input-label value="Permisos" />
                        <div class="grid grid-cols-2 gap-2 mt-2">
                            @foreach($permissions as $permission)
                                <label class="flex items-center">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                           @if(isset($role) && $role->permissions->contains($permission->id)) checked @endif
                                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-600">{{ $permission->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button>
                            {{ isset($role) ? 'Actualizar' : 'Crear' }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
