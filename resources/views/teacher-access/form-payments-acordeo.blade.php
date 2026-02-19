@extends('campus.shared.layout')

@section('title', __('Professorat'))
@section('subtitle', __('campusAccés a la zona de cobrament'))

@section('content')

<div class="container mx-auto py-8">
    @if(session('error'))
        <div class="bg-red-100 border-2 border-red-400 text-red-700 px-6 py-4 rounded-lg mb-6 shadow-md">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 2.502-2.308V7.308c0-1.641-1.962-2.308-3.502-2.308H5.084c-1.54 0-2.502 1.667-2.502 2.308v8.384c0 1.641 1.962 2.308 3.502 2.308z"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-medium text-red-800">Error en el formulario</h3>
                    <div class="mt-2 text-sm text-red-700">
                        {{ session('error') }}
                    </div>
                </div>
            </div>
        </div>
    @endif

    

    <!-- Formulari únic: Dades de cobrament -->
    <div class="mb-8 p-4 border rounded-lg bg-gray-50" id="payment-data-form">

        <h2 class="text-xl font-semibold mb-4">Dades de cobrament</h2>

        <!-- Informació del curs -->
        <div class="mb-6 p-4 border rounded bg-gray-100">
            <div class="font-medium mb-2">📚 Detalls de l'activitat formativa:</div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                <div class="bg-white p-2 rounded">
                    <div class="font-medium text-gray-500">Curs acadèmic</div>
                    <div class="text-gray-800">[{{ $season->slug ?? '' }}] {{ $season->name ?? '----' }}</div>
                </div>

                <div class="bg-white p-2 rounded">
                    <div class="font-medium text-gray-500">Activitat</div>
                    <div class="text-gray-800">[{{ $course->code ?? '--' }}] {{ $course->title ?? '----' }}</div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('teacher.access.personal-data.update', $token->token) }}">
            @csrf

            <input type="hidden" name="course_id" value="{{ $course->id }}">
            <input type="hidden" name="season_id" value="{{ $season->id ?? '' }}">
            <input type="hidden" name="course_title" value="{{ $course->title ?? '' }}">
            <input type="hidden" name="course_code" value="{{ $course->code ?? '' }}">

            <h3 class="text-xl font-bold mb-6 text-center text-gray-800">
                👤 Verifica les teves dades personals
            </h3>
             <p class="text-sm text-gray-600 mb-4">
            Les dades marcades amb * són obligatories.
            </p>
            @php
                $needs = old('needs_payment', $teacher->needs_payment ?? '');
            @endphp

            
            <div class="border rounded-lg p-6 mb-6 bg-gray-50">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <!-- Nom -->
                    <div>
                        <label class="block font-medium">Nom *</label>
                        <input type="text" name="first_name"
                            value="{{ old('first_name', $teacher->first_name ?? '') }}"
                            class="border p-2 w-full" required>
                        @error('first_name')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Cognoms -->
                    <div>
                        <label class="block font-medium">Cognoms *</label>
                        <input type="text" name="last_name"
                            value="{{ old('last_name', $teacher->last_name ?? '') }}"
                            class="border p-2 w-full" required>
                        @error('last_name')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block font-medium">Correu electrònic *</label>
                        <input type="email" name="email"
                            value="{{ old('email', $user->email ?? '') }}"
                            class="border p-2 w-full" required>
                        @error('email')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Telèfon -->
                    <div>
                        <label class="block font-medium">Telèfon *</label>
                        <input type="text" name="phone"
                            value="{{ old('phone', $teacher->phone ?? $user->phone ?? '') }}"
                            class="border p-2 w-full" required>
                        @error('phone')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- DNI -->
                    <div>
                        <label class="block font-medium">DNI / NIF *</label>
                        <input type="text" name="dni"
                            value="{{ old('dni', $teacher->dni ?? '') }}"
                            class="border p-2 w-full" required>
                        @error('dni')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Adreça -->
                    <div>
                        <label class="block font-medium">Adreça</label>
                        <input type="text" name="address"
                            value="{{ old('address', $teacher->address ?? '') }}"
                            class="border p-2 w-full">
                    </div>

                    <div>
                        <label class="block font-medium">Codi postal</label>
                        <input type="text" name="postal_code"
                            value="{{ old('postal_code', $teacher->postal_code ?? '') }}"
                            class="border p-2 w-full">
                    </div>

                    <div>
                        <label class="block font-medium">Ciutat</label>
                        <input type="text" name="city"
                            value="{{ old('city', $teacher->city ?? '') }}"
                            class="border p-2 w-full">
                    </div>

                </div>

            </div>
            <div class="space-y-2">
                <label class="flex items-center">
                    <input type="radio" name="needs_payment" value="waived_fee"
                        class="mr-2" {{ $needs == 'waived_fee' ? 'checked' : '' }}>
                    Renuncio al cobrament
                </label>
            </div>
            </div>
            


            <!-- Bloc fiscal i bancari -->
            <div class="border rounded-lg p-6 bg-white space-y-6">
                <label class="flex items-center">
                    <input type="radio" name="needs_payment" value="own_fee"
                        class="mr-2" {{ $needs == 'own_fee' ? 'checked' : '' }}>
                        Accepto cobrament
                </label>
                <h3 class="font-semibold text-lg">💳 Dades bancàries i fiscals</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Datos bancarios del beneficiario -->
                    <div class="mb-6 p-4 border rounded bg-yellow-50">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                
                                <label class="block font-medium">Identificació fiscal</label>
                                <input type="text" name="fiscal_id"
                                    value="{{ old('fiscal_id', $teacher->fiscal_id ?? '') }}"
                                    class="border p-2 w-full">
                                    <p class="text-xs text-gray-500 mt-1">Nomes si es diferent del DNI</p>
                                @error('fiscal_id')
                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                @enderror

                                <label class="block font-medium">IBAN (24 dígits):</label>
                                <input type="text" name="iban" 
                                    value="{{ old('iban', $payment?->iban ?? '') }}"
                                    class="border p-2 w-full" 
                                    placeholder="ESXX XXXX XXXX XXXX XXXX XXXX"
                                    pattern="[A-Z]{2}[0-9]{22}"
                                    title="Format: 2 lletres + 22 dígits">
                                <p class="text-xs text-gray-500 mt-1">Format: ES + 22 dígits</p>
                                @error('iban')
                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                @enderror
                            
                                <label class="block font-medium">Titular del compte:</label>
                                <input type="text" name="bank_titular" 
                                    value="{{ old('bank_titular', $payment?->bank_titular ?? '') }}"
                                    class="border p-2 w-full"
                                    placeholder="Nom i cognoms del titular">
                                @error('bank_titular')
                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                @enderror

                                
                                <label class="block font-medium">Factura (opcional):</label>
                                <div class="flex items-center mt-2">
                                    <input type="checkbox" name="invoice" value="1" 
                                        {{ old('invoice', $payment?->invoice) == '1' ? 'checked' : '' }}
                                        class="mr-2">
                                    <span class="text-sm">Si</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Marcar si presentarà factura</p>
                                @error('invoice')
                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Datos fiscales del beneficiario -->
                    <div class="mb-6 p-4 border rounded bg-purple-50">
                        <h5 class="font-medium mb-3 text-purple-800">📊 Situació fiscal</h5>
                        
                        <div class="space-y-2 mb-4">
                            <label class="flex items-center">
                                <input type="radio" name="fiscal_situation" value="autonom" 
                                    class="mr-2" 
                                    {{ old('fiscal_situation') == 'autonom' ? 'checked' : '' }}>
                                <span>Autònom/a</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="radio" name="fiscal_situation" value="employee" 
                                    class="mr-2" 
                                    {{ old('fiscal_situation') == 'employee' ? 'checked' : '' }}>
                                <span>Treballador/a per compte alié</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="radio" name="fiscal_situation" value="pensioner" 
                                    class="mr-2" 
                                    {{ old('fiscal_situation') == 'pensioner' ? 'checked' : '' }}>
                                <span>Pensionista o jubilat/jubilada</span>
                            </label>

                            <label class="flex items-center">
                                <input type="radio" name="fiscal_situation" value="pensioner" 
                                    class="mr-2" 
                                    {{ old('fiscal_situation') == 'pensioner' ? 'checked' : '' }}>
                                <span>Jubilat/jubilada amb conveni especial amb la Seguretat Social o amb jubilació activa</span>
                            </label>
                            
                            <label class="flex items-center">
                            <input type="radio" name="fiscal_situation" value="altre"
                                class="mr-2" {{ old('fiscal_situation') == 'altre' ? 'checked' : '' }}>
                            Altre (no llistat)
                        </label>
                        </div>
                    </div>
                    {{-- END Accepto cobrament (own_fee) --}}

                </div>
                
                </div>

                <div class="mb-6 p-4 border rounded bg-blue-50">
                    <div>
                    <label class="flex items-center">
                        <input type="radio" name="needs_payment" value="ceded_fee"
                            class="mr-2" {{ $needs == 'ceded_fee' ? 'checked' : '' }}>
                            Derivo el cobrament a altra persona o entitat 
                    </label>
                    <br>
                    </div>

                    <h5 class="font-medium mb-3 text-blue-800">🏠 Dades de contacte del perceptor</h5>
                    <p class="text-sm text-gray-600 mb-4">
                        Completa les dades. Imprescindibles les dades per contactar si és necessari.
                    </p>
        
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium">Nom *</label>
                        <input type="text" name="beneficiary_first_name"
                            value="{{ old('beneficiary_first_name', $payment?->first_name ?? $teacher->first_name ?? '') }}"
                            class="border p-2 w-full" >
                        @error('beneficiary_first_name')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

 

                    <!-- Email -->
                    <div>
                        <label class="block font-medium">Correu electrònic *</label>
                        <input type="email" name="beneficiary_email"
                            value="{{ old('beneficiary_email', $user->email ?? '') }}"
                            class="border p-2 w-full" >
                        @error('beneficiary_email')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Telèfon -->
                    <div>
                        <label class="block font-medium">Telèfon *</label>
                        <input type="text" name="beneficiary_phone"
                            value="{{ old('beneficiary_phone', $teacher->phone ?? $user->phone ?? '') }}"
                            class="border p-2 w-full" >
                        @error('beneficiary_phone')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block font-medium">Adreça postal :</label>
                        <input type="text" name="beneficiary_address" 
                            value="{{ old('beneficiary_address', $payment?->address ?? $teacher->address ?? '') }}"
                            class="border p-2 w-full"
                            placeholder="Carrer, número, pis">
                        @error('beneficiary_address')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block font-medium">Codi postal :</label>
                        <input type="text" name="beneficiary_postal_code" 
                            value="{{ old('beneficiary_postal_code', $payment?->postal_code ?? $teacher->postal_code ?? '') }}"
                            class="border p-2 w-full"
                            placeholder="08000">
                        @error('beneficiary_postal_code')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block font-medium">Població :</label>
                        <input type="text" name="beneficiary_city" 
                            value="{{ old('beneficiary_city', $payment?->city ?? $teacher->city ?? '') }}"
                            class="border p-2 w-full"
                            placeholder="Granollers, Barcelona, etc.">
                        @error('beneficiary_city')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block font-medium">Identificació fiscal:</label>
                        <input type="text" name="beneficiary_fiscal_id" 
                            value="{{ old('beneficiary_fiscal_id', $payment?->fiscal_id ?? '') }}"
                            class="border p-2 w-full"
                            placeholder="DNI/NIF">
                        @error('beneficiary_fiscal_id')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <!-- Datos bancarios o -->
                    <div class="mb-6 p-4 border rounded bg-yellow-50">
                        <h5 class="font-medium mb-3 text-yellow-800">💳 Dades bancàries </h5>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-medium">IBAN  (24 dígits):</label>
                                <input type="text" name="beneficiary_iban" 
                                    value="{{ old('beneficiary_iban', $payment?->iban ?? '') }}"
                                    class="border p-2 w-full" 
                                    placeholder="ESXX XXXX XXXX XXXX XXXX XXXX"
                                    pattern="[A-Z]{2}[0-9]{22}"
                                    title="Format: 2 lletres + 22 dígits">
                                <p class="text-xs text-gray-500 mt-1">Format: ES + 22 dígits</p>
                                @error('beneficiary_iban')
                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                @enderror
                            
                                <label class="block font-medium">Titular del compte (beneficiari):</label>
                                <input type="text" name="beneficiary_bank_titular" 
                                    value="{{ old('beneficiary_bank_titular', $payment?->bank_titular ?? '') }}"
                                    class="border p-2 w-full"
                                    placeholder="Nom i cognoms del titular">
                                @error('beneficiary_bank_titular')
                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                @enderror

                                
                                <label class="block font-medium">Factura (opcional):</label>
                                <div class="flex items-center mt-2">
                                    <input type="checkbox" name="beneficiary_invoice" value="1" 
                                        {{ old('beneficiary_invoice', $payment?->invoice) == '1' ? 'checked' : '' }}
                                        class="mr-2">
                                    <span class="text-sm">Si</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Marcar si presentarà factura</p>
                                @error('beneficiary_invoice')
                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Datos fiscales o -->
                    <!-- <div class="mb-6 p-4 border rounded bg-purple-50">
                        <h5 class="font-medium mb-3 text-purple-800">📊 Situació fiscal</h5>
                        
                        <div class="space-y-2 mb-4">
                            <label class="flex items-center">
                                <input type="radio" name="beneficiary_fiscal_situation" value="autonom" 
                                    class="mr-2" 
                                    {{ old('beneficiary_fiscal_situation') == 'autonom' ? 'checked' : '' }}>
                                <span>Autònom</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="radio" name="beneficiary_fiscal_situation" value="employee" 
                                    class="mr-2" 
                                    {{ old('beneficiary_fiscal_situation') == 'employee' ? 'checked' : '' }}>
                                <span>Assalariat</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="radio" name="beneficiary_fiscal_situation" value="pensioner" 
                                    class="mr-2" 
                                    {{ old('beneficiary_fiscal_situation') == 'pensioner' ? 'checked' : '' }}>
                                <span>Pensionista</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="radio" name="beneficiary_fiscal_situation" value="unemployed" 
                                    class="mr-2" 
                                    {{ old('beneficiary_fiscal_situation') == 'unemployed' ? 'checked' : '' }}>
                                <span>Aturat</span>
                            </label>

                            <label class="flex items-center">
                            <input type="radio" name="beneficiary_fiscal_situation" value="altre"
                                class="mr-2" {{ old('beneficiary_fiscal_situation') == 'altre' ? 'checked' : '' }}>
                            Altre (no llistat)
                        </label>
                        </div>
                    </div> -->
                    
                   
                </div>
                </div>
                
<!--                 <div class="border rounded-lg p-6 bg-white space-y-6">
                    <label class="block font-medium mb-2">Observacions </label>
                    {{-- En un <textarea> el contingut ha d’anar a la mateixa línia, així: --}}
                    <textarea name="beneficiary_observacions2" rows="4" class="border p-2 w-full">{{ old('observacions', $payment->metadata['observacions'] ?? null ?? '') }}</textarea>
                </div> -->


                
                    <!-- Observacions -->
                 <div>
                    <label class="block font-medium mb-2">Observacions</label>
                    {{-- En un <textarea> el contingut ha d’anar a la mateixa línia, així: --}}
                    <textarea name="observacions" rows="4" class="border p-2 w-full">{{ old('observacions', $payment?->observacions ?? $teacher->observacions ?? '') }}</textarea>
                </div>

                <div class="mt-6 text-center">
                        <div class="mb-3 text-sm text-gray-600">
                            ⚠️ Pots guardar les teves dades com a esborrany i completar el procés més tard
                        </div>
                        <button type="submit"
                            class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 font-medium text-lg shadow">
                            📝 Guardar esborrany
                        </button>
                    </div>
                </form>

                <form method="POST" action="{{ route('teacher.access.personal-data.update', $token->token) }}" 
                    onsubmit="return validateFinalForm();">
                    @csrf

                <!-- Autorización de datos del beneficiario -->
                    <div class="mt-6 p-4 border rounded bg-blue-50">
                        <label class="flex items-start">
                            <input type="checkbox" name="end_autoritzacio_dades" value="1" 
                                class="mr-2 mt-1" @checked(old('end_autoritzacio_dades', $payment?->metadata['end_autoritzacio_dades'] ?? false))
                                @if(old('end_autoritzacio_dades', $payment?->metadata['end_autoritzacio_dades'] ?? false)) checked @endif>
                            <span class="text-sm">
                                <strong>Necessari:</strong> Autoritzo el tractament de les meves dades personals amb finalitats fiscals 
                                i administratives, d'acord amb la normativa vigent de protecció de dades.
                            </span>
                        </label>
                        @error('end_autoritzacio_dades')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                     <!-- Declaración fiscal del beneficiario -->
                        <div class="bg-yellow-50 p-3 rounded border border-yellow-200">
                            <label class="flex items-start">
                                <input type="checkbox" name="end_declaracio_fiscal" value="1" 
                                    class="mr-2 mt-1" @checked(old('end_declaracio_fiscal', $payment?->metadata['end_declaracio_fiscal'] ?? false))
                                    @if(old('end_declaracio_fiscal', $payment?->metadata['end_declaracio_fiscal'] ?? false)) checked @endif>
                                <span class="text-sm">
                                    <strong>Necessari:</strong> Declaro que les dades facilitades són certes i que sóc coneixedor/a de la fiscalitat corresponent                                     als ingressos previstos.
                                </span>
                            </label>
                            
                            @error('end_declaracio_fiscal')
                                <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>  
                        <div>
                      
                        <input type="hidden" name="course_id" value="{{ $course->id }}">
                        <input type="hidden" name="season_id" value="{{ $season->id ?? 'null' }}">

                        <div class="mb-6 p-4 border rounded bg-gray-100">
            <div class="font-medium mb-2">📋 Resum del procés de finalització:</div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                <div class="bg-white p-2 rounded">
                    <div class="font-medium text-gray-500">Opció de cobrament seleccionada</div>
                    <div class="text-gray-800">
                        @php
                            $paymentOptions = [
                                'waived_fee' => '🚫 Renuncio al cobrament',
                                'own_fee' => '✅ Accepto el cobrament',
                                'ceded_fee' => '📄 Cedeixo la titularitat'
                            ];
                        @endphp
                        {{ $paymentOptions[$teacher->needs_payment ?? ''] ?? 'No seleccionada' }}
                    </div>
                </div>
                
                <div class="bg-white p-2 rounded">
                    <div class="font-medium text-gray-500">Estat del procés</div>
                    <div class="text-gray-800">🎯 A punt per finalitzar</div>
                </div>
            </div>
            
            <div class="mt-3 text-xs text-gray-600 bg-blue-50 p-2 rounded">
                <strong>⚠️ Important:</strong> Al fer clic a "Guardar dades, crear PDF i finalitzar" es generarà el document PDF final amb totes les dades i no es podrà modificar.
            </div>
        </div>
                        </div>

            </div>

            <div class="mt-6 text-center">
                <div class="mb-3 text-sm text-green-700 font-medium">
                    🎯 Comprova que tot és correcte abans de finalitzar. Aquesta acció generarà el PDF final i no es podrà modificar.
                </div>
                <button type="submit"
                    class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 font-bold text-lg shadow border-2 border-blue-600">
                    ✅ Guardar dades, crear PDF i finalitzar
                </button>
            </div>

        </form>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const paymentRadios = document.querySelectorAll('input[name="needs_payment"]');

    const beneficiaryRequiredFields = [
        'beneficiary_first_name',
        'beneficiary_last_name',
        'beneficiary_email',
        'beneficiary_phone'
    ];

    const allBeneficiaryFields = document.querySelectorAll('[name^="beneficiary_"]');

    function clearBeneficiaryFields() {
        allBeneficiaryFields.forEach(field => {

            if (field.type === 'radio' || field.type === 'checkbox') {
                field.checked = false;
            } else {
                field.value = '';
            }

            field.removeAttribute('required');
        });
    }

    function activateBeneficiaryRequired() {
        beneficiaryRequiredFields.forEach(name => {
            const field = document.querySelector(`[name="${name}"]`);
            if (field) {
                field.setAttribute('required', 'required');
            }
        });
    }

    function handlePaymentChange(value) {

        if (value === 'ceded_fee') {
            activateBeneficiaryRequired();
        } else {
            clearBeneficiaryFields();
        }
    }

    // Inicialització
    const selected = document.querySelector('input[name="needs_payment"]:checked');
    if (selected) {
        handlePaymentChange(selected.value);
    }

    // Listeners
    paymentRadios.forEach(radio => {
        radio.addEventListener('change', function () {
            handlePaymentChange(this.value);
        });
    });
});


/* Validació del formulari final */
function validateFinalForm() {
    const autoritzacioCheckbox = document.querySelector('input[name="end_autoritzacio_dades"]');
    const declaracioCheckbox = document.querySelector('input[name="end_declaracio_fiscal"]');

    if (!autoritzacioCheckbox.checked || !declaracioCheckbox.checked) {
        alert('⚠️ Per finalitzar el procés, has de marcar ambdues autoritzacions.');
        return false;
    }

    return true;
}
</script>

@endsection
