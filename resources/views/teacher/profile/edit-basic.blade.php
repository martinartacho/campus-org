<x-app-layout>
    <x-slot name="header">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                {{ __('Perfil del Professor') }}
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                {{ __('Gestiona la teva informació professional i dades de cobrament.') }}
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

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    {{ __('Dades Bàsiques del Professor') }}
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    {{ __('Formulari bàsic per provar el guardat de dades.') }}
                </p>
            </div>

            <div class="px-4 py-5 sm:p-6">
                <form method="PUT" action="{{ route('teacher.profile.update') }}" class="space-y-6">
                    @csrf
                    
                    {{-- DADES BÀSIQUES --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="first_name" :value="__('Nom') . ' *'" />
                                <x-text-input id="first_name" name="first_name" type="text" 
                                           class="mt-1 block w-full" 
                                           value="{{ $teacher->first_name ?? '' }}" />
                                <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
                            </div>
                            
                            <div>
                                <x-input-label for="last_name" :value="__('Cognoms') . ' *'" />
                                <x-text-input id="last_name" name="last_name" type="text" 
                                           class="mt-1 block w-full" 
                                           value="{{ $teacher->last_name ?? '' }}" />
                                <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="email" :value="__('Correu Electrònic') . ' *'" />
                                <x-text-input id="email" name="email" type="email" 
                                           class="mt-1 block w-full" 
                                           value="{{ $teacher->email ?? '' }}" />
                                <x-input-error class="mt-2" :messages="$errors->get('email')" />
                            </div>
                            
                            <div>
                                <x-input-label for="phone" :value="__('Telèfon')" />
                                <x-text-input id="phone" name="phone" type="tel" 
                                           class="mt-1 block w-full" 
                                           value="{{ $teacher->phone ?? '' }}" />
                                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="address" :value="__('Adreça')" />
                            <x-text-input id="address" name="address" type="text" 
                                       class="mt-1 block w-full" 
                                       value="{{ $teacher->address ?? '' }}" />
                            <x-input-error class="mt-2" :messages="$errors->get('address')" />
                        </div>
                        
                        <div>
                            <x-input-label for="city" :value="__('Ciutat')" />
                            <x-text-input id="city" name="city" type="text" 
                                       class="mt-1 block w-full" 
                                       value="{{ $teacher->city ?? '' }}" />
                            <x-input-error class="mt-2" :messages="$errors->get('city')" />
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="postal_code" :value="__('Codi Postal')" />
                            <x-text-input id="postal_code" name="postal_code" type="text" 
                                       class="mt-1 block w-full" 
                                       value="{{ $teacher->postal_code ?? '' }}" />
                            <x-input-error class="mt-2" :messages="$errors->get('postal_code')" />
                        </div>
                        
                        <div>
                            <x-input-label for="payment_type" :value="__('Tipus de Cobrament') . ' *'" />
                            <select id="payment_type" name="payment_type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
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
                            <x-input-error class="mt-2" :messages="$errors->get('payment_type')" />
                        </div>
                    </div>
                    
                    {{-- BOTONS D'ACCIÓ --}}
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
        </div>
    </div>
</x-app-layout>
