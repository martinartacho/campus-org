<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">
             {{ __('site.Edit Perminion') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded p-6">
                <form method="POST" action="{{ route('admin.permissions.update', $permission) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <x-input-label for="name" value="Nombre del Permiso" />
                        <x-text-input id="name" type="text" name="name" value="{{ old('name', $permission->name) }}" class="mt-1 block w-full" required />
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button>{{ __('site.Edit') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
