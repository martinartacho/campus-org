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

        @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            <div class="flex">
                <svg class="flex-shrink-0 h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 0L3 11.586 1.707 13.293a1 1 0 001.414 1.414l3-3z" clip-rule="evenodd"></path>
                </svg>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                        Hi ha hagut errors en el formulari
                    </h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- GRUP 1: DADES PERSONALS -->
        <div class="bg-white shadow sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    1. Dades Personals
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    {{ __('Informació personal i de contacte del professor.') }}
                </p>
            </div>

           
            <form method="POST" action="{{ route('teacher.profile.update') }}" class="space-y-6">
                @csrf
                @method('PUT')
                <div class="px-4 py-5 sm:p-6">        
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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
                        
                        <div>
                            <x-input-label for="postal_code" :value="__('Codi Postal')" />
                            <x-text-input id="postal_code" name="postal_code" type="text" 
                                       class="mt-1 block w-full" 
                                       value="{{ $teacher->postal_code ?? '' }}" />
                            <x-input-error class="mt-2" :messages="$errors->get('postal_code')" />
                        </div>
                    </div>
                <div>
                    <hr class="my-6">
                </div>
                

                <!-- GRUP 2: DADES DE COBRAMENT -->
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            2. Dades de Cobrament
                        </h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">
                            {{ __('Seleccioneu el tipus de cobrament i completeu les dades corresponents.') }}
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <x-input-label for="payment_type" :value="__('Tipus de Cobrament') . ' *'" />
                                <select id="payment_type" name="payment_type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="waived" {{ ($teacher->payment_type ?? 'waived') == 'waived' ? 'selected' : '' }}>
                                        {{ __('campus.payment_type_waived') }}
                                    </option>
                                    <option value="own" {{ ($teacher->payment_type ?? 'waived') == 'own' ? 'selected' : '' }}>
                                        {{ __('campus.payment_type_own') }}
                                    </option>
                                    <option value="ceded" {{ ($teacher->payment_type ?? 'waived') == 'ceded' ? 'selected' : '' }}>
                                        {{ __('campus.payment_type_ceded') }}
                                    </option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('payment_type')" />
                            </div>
                        </div>

                        <!-- CAMPS BANCARIS (només si és 'own') -->
                        <div id="banking-fields">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                                <div>
                                    <x-input-label for="dni" :value="__('DNI') . ' *'" />
                                    <x-text-input id="dni" name="dni" type="text" 
                                            class="mt-1 block w-full" 
                                            value="{{ $teacher->dni ?? '' }}" />
                                    <x-input-error class="mt-2" :messages="$errors->get('dni')" />
                                </div>
                                
                                <div>
                                    <x-input-label for="iban" :value="__('IBAN') . ' *'" />
                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ __('IBAN actual: :iban', ['iban' => $teacher->masked_iban]) }}
                                    </p>
                                    <x-text-input id="iban" name="iban" type="text" 
                                            class="mt-1 block w-full" 
                                            value="" />
                                    <x-input-error class="mt-2" :messages="$errors->get('iban')" />
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="bank_titular" :value="__('Titular del Compte') . ' *'" />
                                    <x-text-input id="bank_titular" name="bank_titular" type="text" 
                                            class="mt-1 block w-full" 
                                            value="{{ $teacher->decrypted_bank_titular ?? $teacher->bank_titular ?? '' }}" />
                                    <x-input-error class="mt-2" :messages="$errors->get('bank_titular')" />
                                </div>
                                
                                <div>
                                    <x-input-label for="fiscal_situation" :value="__('Situació Fiscal') . ' *'" />
                                    <select id="fiscal_situation" name="fiscal_situation" 
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="autonom" {{ ($teacher->fiscal_situation ?? '') == 'autonom' ? 'selected' : '' }}>
                                            {{ __('Autònom') }}
                                        </option>
                                        <option value="employee" {{ ($teacher->fiscal_situation ?? '') == 'employee' ? 'selected' : '' }}>
                                            {{ __('Treballador per compte aliè') }}
                                        </option>
                                        <option value="pensioner" {{ ($teacher->fiscal_situation ?? '') == 'pensioner' ? 'selected' : '' }}>
                                            {{ __('Pensionista') }}
                                        </option>
                                        <option value="other" {{ ($teacher->fiscal_situation ?? '') == 'other' ? 'selected' : '' }}>
                                            {{ __('Altres') }}
                                        </option>
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('fiscal_situation')" />
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="invoice" value="1" 
                                            {{ ($teacher->invoice ?? false) ? 'checked' : '' }}
                                            >
                                        
                                    </label>
                                            <span class="ml-2 text-sm text-gray-700">{{ __('Vull emetre factures') }}</span>
                                    </div>
                                    <x-input-error class="mt-2" :messages="$errors->get('invoice')" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
               
                <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <div class="md:col-span-3">
                        <x-input-label for="observacions" :value="__('Observacions')" />
                           
                         <!-- MISSATGE INFORMATIU -->
                        {{-- En un   textarea  el contingut ha d anar a la mateixa línia, així: --}}                      
                        <textarea name="observacions" rows="4" class="border p-2 w-full">{{ $teacher->observacions ?? old('observacions') ?? '' }}</textarea>                    
                        <x-input-error class="mt-2" :messages="$errors->get('observacions')" />
                        <hr>
                         <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mt-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">
                                        {{ __('Nota sobre el cobrament cedit') }}
                                    </h3>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <p>{{ __('campus.ceded_payment_instructions') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                <!-- </div> -->
                
                <!-- Autorización de datos del beneficiario -->
                <!-- <div class="bg-white shadow sm:rounded-lg"> -->
                <div class="mt-6 p-4 border rounded bg-blue-50">
                    <span class="text-sm text-gray-700 mb-2 block">
                        <strong>Necessari:</strong> Autoritzo el tractament de les meves dades personals amb finalitats fiscals 
                        i administratives, d'acord amb la normativa vigent de protecció de dades.
                    </span>
                    <div class="flex space-x-4">
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="data_consent" 
                                   value="1"
                                   {{ ($teacher->data_consent ?? false) ? 'checked' : '' }}
                                   class="border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm">{{ __('Sí') }}</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="data_consent" 
                                   value="0"
                                   {{ !($teacher->data_consent ?? false) ? 'checked' : '' }}
                                   class="border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm">{{ __('No') }}</span>
                        </label>
                    </div>
                    @error('data_consent')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Declaración fiscal del beneficiario -->
                <div class="bg-yellow-50 p-3 rounded border border-yellow-200">
                    <span class="text-sm text-gray-700 mb-2 block">
                        <strong>Necessari:</strong> Declaro que les dades facilitades són certes i que sóc coneixedor/a de la fiscalitat corresponent als ingressos previstos.
                    </span>
                    <div class="flex space-x-4">
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="fiscal_responsibility" 
                                   value="1"
                                   {{ ($teacher->fiscal_responsibility ?? false) ? 'checked' : '' }}
                                   class="border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm">{{ __('Sí') }}</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="fiscal_responsibility" 
                                   value="0"
                                   {{ !($teacher->fiscal_responsibility ?? false) ? 'checked' : '' }}
                                   class="border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm">{{ __('No') }}</span>
                        </label>
                    </div>
                    
                    @error('fiscal_responsibility')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>  
                <div>
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
                    <hr class="my-6">
                </div>
            </div>
        </form>
        

        <div class="bg-green-100 border-2 border-green-400 text-green-700 px-6 py-4 rounded-lg mb-6 shadow-md">            
            <div class="flex items-start">
                @if ($teacher->fiscal_responsibility == 1 || $teacher->fiscal_responsibility == '1')
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        @if($latestPdf)
                            <h3 class="text-lg font-medium text-green-800">✅ PDF ja generat</h3>
                            
                            <div class="mt-3 text-xs text-green-600 bg-green-50 p-2 rounded">
                                <div class="mb-2">
                                    <strong>📄 El teu consentiment ja està registrat</strong>
                                </div>
                                
                                <p class="text-xs text-green-600">
                                    📄 Últim PDF generat: <a href="{{ $latestPdf['download_url'] }}" class="text-blue-600 hover:underline font-semibold" target="_blank">{{ $latestPdf['filename'] }}</a>
                                    <br><small class="text-gray-500">Generat el {{ $latestPdf['created_at'] }}</small>
                                </p>
                                
                                <div class="mt-3 p-2 bg-yellow-50 border border-yellow-200 rounded">
                                    <p class="text-xs text-yellow-700">
                                        <strong>⚠️ Nota:</strong> Si has actualitzat les teves dades després de generar el PDF, pots tornar a generar-lo per reflectir els canvis.
                                    </p>
                                    <form method="POST" action="{{ route('teacher.profile.pdf') }}" class="mt-2">
                                        @csrf
                                        <button type="submit" id="regenerate-pdf-btn"
                                            class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700 text-xs font-medium shadow">
                                            🔄 Tornar a generar PDF
                                        </button>
                                    </form>
                                    <p>
                                        📄 Llistat de PDF generats (màxim 3)
                                    </p>
                                    
                                    @if($allPdfs && count($allPdfs) > 0)
                                        <div class="mt-3 space-y-2">
                                            @foreach($allPdfs as $pdf)
                                                <div class="flex items-center justify-between p-2 bg-white border border-gray-200 rounded text-xs">
                                                    <div class="flex items-center space-x-2">
                                                        <span class="text-gray-500">📄</span>
                                                        <a href="{{ $pdf['download_url'] }}" 
                                                           class="text-blue-600 hover:underline font-medium" 
                                                           target="_blank">
                                                            {{ $pdf['filename'] }}
                                                        </a>
                                                        <span class="text-gray-400 text-xs">({{ $pdf['size'] }})</span>
                                                    </div>
                                                    <div class="text-right text-xs text-gray-500">
                                                        <div>{{ $pdf['created_at'] }}</div>
                                                        @if($pdf['filename'] === ($latestPdf['filename'] ?? ''))
                                                            <span class="text-green-600 font-medium">✅ Més recent</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        
                                        @if(count($allPdfs) >= 3)
                                            <div class="mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded text-xs text-yellow-700">
                                                <strong>⚠️ Límit assol:</strong> S'han mostrat els 3 PDFs més recents. 
                                                Els PDFs més antics es mantenen per historial però no es mostren per optimitzar l'espai.
                                            </div>
                                        @endif
                                    @else
                                        <div class="mt-2 p-2 bg-gray-50 border border-gray-200 rounded text-xs text-gray-600">
                                            📭 No s'ha trobat cap PDF generat. Fes clic al botó superior per crear el primer document.
                                        </div>
                                    @endif
                                    
                                </div>
                            </div>
                        @else
                            <h3 class="text-lg font-medium text-green-800">✅ estàs punt per finalitzar</h3>
                            
                            <div class="mt-3 text-xs text-green-600 bg-green-50 p-2 rounded">
                                <form method="POST" action="{{ route('teacher.profile.pdf') }}">
                                @csrf
                                <button type="submit" id="generate-pdf-btn"
                                    class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 font-bold text-lg shadow border-2 border-blue-600">
                                    ✅ Comunicar a l'equip de Tresoreria de l'UPG i crear PDF
                                </button>
                            </form>

                            <p class="mt-2 text-xs text-green-600">
                                📄 Encara no has generat cap PDF. Fes clic al botó superior per crear-lo.
                            </p>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">

                        <h3 class="text-lg font-medium text-blue-800">Has revisat les teves dades... </h3>
                        <div class="mt-3 text-xs text-red-600 bg-red-50 p-2 rounded">
                        
                        🎯 <strong>estàs punt per finalitzar </strong> marca les autoritzacions i fes clic a "Guardar dades"

                        </div>
                        
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

