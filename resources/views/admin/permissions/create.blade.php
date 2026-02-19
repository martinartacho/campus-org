<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">
            {{ __('site.Create Permission') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded p-6">
                <form method="POST" action="{{ route('admin.permissions.store') }}">
                    @csrf
                    <div class="mb-4">
                        <x-input-label for="name" value="Nombre del Permiso" />
                        <x-text-input id="name" type="text" name="name" class="mt-1 block w-full" required autofocus />
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button>  <x-primary-button>{{ __('site.Save') }}</x-primary-button></x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
