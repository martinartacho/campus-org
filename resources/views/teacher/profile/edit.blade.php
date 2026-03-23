<x-app-layout>
    @section('breadcrumbs')
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}">
                    <i class="bi bi-house-door me-1"></i> {{ __('Inici') }}
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <i class="bi bi-person-badge me-1"></i> {{ __('Perfil del Professor') }}
            </li>
        </ol>
    @endsection
    
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Perfil del Professor') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Informació Personal -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-4xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                <i class="bi bi-person mr-2"></i>{{ __('Informació Personal') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ __("Actualitza la teva informació personal com a professor.") }}
                            </p>
                        </header>

                        @if(session('status') == 'profile-updated')
                            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                                {{ __('El teu perfil s\'ha actualitzat correctament!') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('teacher.profile.update') }}" class="mt-6 space-y-6">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="first_name" :value="__('Nom')" />
                                    <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full" 
                                               :value="old('first_name', $teacher->first_name)" required autofocus />
                                    <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
                                </div>

                                <div>
                                    <x-input-label for="last_name" :value="__('Cognoms')" />
                                    <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full" 
                                               :value="old('last_name', $teacher->last_name)" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
                                </div>

                                <div>
                                    <x-input-label for="email" :value="__('Correu Electrònic')" />
                                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" 
                                               :value="old('email', $teacher->email)" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                                </div>

                                <div>
                                    <x-input-label for="phone" :value="__('Telèfon')" />
                                    <x-text-input id="phone" name="phone" type="tel" class="mt-1 block w-full" 
                                               :value="old('phone', $teacher->phone)" />
                                    <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                                </div>

                                <div>
                                    <x-input-label for="dni" :value="__('DNI')" />
                                    <x-text-input id="dni" name="dni" type="text" class="mt-1 block w-full" 
                                               :value="old('dni', $teacher->dni)" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('dni')" />
                                </div>

                                <div>
                                    <x-input-label for="postal_code" :value="__('Codi Postal')" />
                                    <x-text-input id="postal_code" name="postal_code" type="text" class="mt-1 block w-full" 
                                               :value="old('postal_code', $teacher->postal_code)" />
                                    <x-input-error class="mt-2" :messages="$errors->get('postal_code')" />
                                </div>

                                <div class="md:col-span-2">
                                    <x-input-label for="address" :value="__('Adreça')" />
                                    <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" 
                                               :value="old('address', $teacher->address)" />
                                    <x-input-error class="mt-2" :messages="$errors->get('address')" />
                                </div>

                                <div>
                                    <x-input-label for="city" :value="__('Ciutat')" />
                                    <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" 
                                               :value="old('city', $teacher->city)" />
                                    <x-input-error class="mt-2" :messages="$errors->get('city')" />
                                </div>
                            </div>

                            <div class="flex items-center justify-end mt-6">
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    {{ __('Guardar Canvis') }}
                                </button>
                            </div>
                        </form>
                    </section>
                </div>
            </div>

            <!-- Dades Bancàries -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-4xl">
                    @include('profile.partials.banking-data-form')
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
