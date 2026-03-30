<x-app-layout>
    <x-slot name="header">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                {{ __('Perfil del Professor') }}
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                {{ __('Formulari organitzat en 3 grups de dades.') }}
            </p>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            <div class="flex">
                <svg class="flex-shrink-0 h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 016 0zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 0l-2 2a1 1 0 001.414 1.414l2-2z" clip-rule="evenodd"></path>
                </svg>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
        </div>
        @endif
        
        @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            <div class="flex">
                <svg class="flex-shrink-0 h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 016 0zM8.707 7.293a1 1 0 00-1.414 0L3 11.586 1.707 13.293a1 1 0 001.414 1.414l3-3z" clip-rule="evenodd"></path>
                </svg>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">
                        {{ session('error') }}
                    </p>
                </div>
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route('teacher.profile.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- GRUP 1: DADES BÀSIQUES -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        {{ __('1. Dades Bàsiques') }}
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        {{ __('Informació personal bàsica del professor.') }}
                    </p>
                </div>

                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700">
                                {{ __('Nom') }} *
                            </label>
                            <input type="text" id="first_name" name="first_name" 
                                   class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                   value="{{ $teacher->first_name ?? '' }}" />
                            @error('first_name')
                                <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700">
                                {{ __('Cognoms') }} *
                            </label>
                            <input type="text" id="last_name" name="last_name" 
                                   class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                   value="{{ $teacher->last_name ?? '' }}" />
                            @error('last_name')
                                <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">
                                {{ __('Correu Electrònic') }} *
                            </label>
                            <input type="email" id="email" name="email" 
                                   class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                   value="{{ $teacher->email ?? '' }}" readonly />
                            <p class="mt-1 text-xs text-gray-500">{{ __('No es pot modificar. Ha de coincidir amb el correu d\'usuari.') }}</p>
                            @error('email')
                                <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- GRUP 2: DADES DE CONTACTE -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        {{ __('2. Dades de Contacte') }}
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        {{ __('Informació de contacte i ubicació.') }}
                    </p>
                </div>

                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">
                                {{ __('Telèfon') }}
                            </label>
                            <input type="tel" id="phone" name="phone" 
                                   class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                   value="{{ $teacher->phone ?? '' }}" />
                            @error('phone')
                                <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700">
                                {{ __('Adreça') }}
                            </label>
                            <input type="text" id="address" name="address" 
                                   class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                   value="{{ $teacher->address ?? '' }}" />
                            @error('address')
                                <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700">
                                {{ __('Ciutat') }}
                            </label>
                            <input type="text" id="city" name="city" 
                                   class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                   value="{{ $teacher->city ?? '' }}" />
                            @error('city')
                                <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="postal_code" class="block text-sm font-medium text-gray-700">
                                {{ __('Codi Postal') }}
                            </label>
                            <input type="text" id="postal_code" name="postal_code" 
                                   class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                   value="{{ $teacher->postal_code ?? '' }}" />
                            @error('postal_code')
                                <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- GRUP 3: DADES DE COBRAMENT -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        {{ __('3. Dades de Cobrament') }}
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        {{ __('Seleccioneu el tipus de cobrament. Les dades específiques es gestionaran en una altra secció.') }}
                    </p>
                </div>

                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="payment_type" class="block text-sm font-medium text-gray-700">
                                {{ __('Tipus de Cobrament') }} *
                            </label>
                            <select id="payment_type" name="payment_type" 
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="waived" {{ ($teacher->payment_type ?? 'waived') == 'waived' ? 'selected' : '' }}>
                                    {{ __('No cobraré') }}
                                </option>
                                <option value="own" {{ ($teacher->payment_type ?? 'waived') == 'own' ? 'selected' : '' }}>
                                    {{ __('Cobraré jo mateix') }}
                                </option>
                                <option value="ceded" {{ ($teacher->payment_type ?? 'waived') == 'ceded' ? 'selected' : '' }}>
                                    {{ __('Cedir el cobrament') }}
                                </option>
                            </select>
                            @error('payment_type')
                                <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="md:col-span-2">
                            <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-blue-800">{{ __('Informació') }}</h3>
                                        <div class="mt-2 text-sm text-blue-700">
                                            <p>{{ __('Les dades bancàries específiques es gestionaran en una secció separada segons el tipus de cobrament seleccionat.') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BOTONS D'ACCIÓ -->
            <div class="flex flex-wrap gap-3 pt-6 border-t">
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="bi bi-save mr-2"></i>
                    {{ __('Guardar Dades') }}
                </button>
                
                <a href="{{ route('dashboard') }}" 
                   class="px-6 py-2 bg-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    {{ __('Cancel·lar') }}
                </a>
            </div>
        </form>
    </div>
</x-app-layout>
