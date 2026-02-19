<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
            <i class="bi bi-pencil-square mr-2"></i>
            {{ __('site.Edit User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 gap-6 mt-4">
                            <!-- Nombre -->
                            <div class="col-span-1">
                                <x-input-label for="name" :value="__('site.Name')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" 
                                    value="{{ old('name', $user->name) }}" required />
                            </div>

                            <!-- Email -->
                            <div class="col-span-1">
                                <x-input-label for="email" :value="__('site.Email')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" 
                                    value="{{ old('email', $user->email) }}" required />
                            </div>

                            <!-- Roles -->
                            <div class="col-span-1">
                                <x-input-label :value="__('site.Roles.')" />
                                <div class="mt-2 space-y-2">
                                    @foreach($roles as $role)
                                        <label class="flex items-center">
                                            <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                                {{ $user->hasRole($role->name) ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                            <span class="ml-2 text-sm text-gray-600">{{ $role->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Permisos Heredados por Roles Agrupados -->
                            <div class="col-span-1 mt-8">
                                <x-input-label :value="__('Permisos Heredados por Roles')" />
                                @php
                                    $inherited = $user->getPermissionsViaRoles()->groupBy(fn ($p) => explode('.', $p->name)[0]);
                                @endphp

                                @forelse ($inherited as $group => $groupPermissions)
                                    <div class="mt-4">
                                        <h4 class="font-semibold text-sm text-gray-700 uppercase">{{ ucfirst($group) }}</h4>
                                        <ul class="list-disc list-inside text-sm text-gray-600">
                                            @foreach ($groupPermissions as $permission)
                                                <li>{{ $permission->name }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @empty
                                    <p class="text-gray-500 italic mt-2">No tiene permisos heredados.</p>
                                @endforelse
                            </div>

                            <!-- Permisos Directos Agrupados -->
                            <div class="col-span-1 mt-6">
                                <x-input-label :value="__('Permisos Directos')" />
                                @foreach ($permissions as $group => $groupPermissions)
                                    <div class="mt-4">
                                        <h4 class="font-semibold text-sm text-gray-700 uppercase">{{ ucfirst($group) }}</h4>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2 mt-2">
                                            @foreach ($groupPermissions as $permission)
                                                <label class="flex items-center">
                                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                                    {{ $user->hasPermissionTo($permission->name) ? 'checked' : '' }}
                                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                                    <span class="ml-2 text-sm text-gray-600">{{ $permission->name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button class="ml-4">
                                {{ __('site.Update User') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
