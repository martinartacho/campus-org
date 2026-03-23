{{-- FORMULARI 1: WAIVED (NO COBREN) --}}
<div id="waived-form" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6 @if(auth()->user()->teacherProfile?->payment_type !== 'waived') hidden @endif">
    <h4 class="text-md font-bold text-gray-800 mb-4 flex items-center">
        <i class="bi bi-x-circle mr-2 text-gray-600"></i>
        {{ __('Opció 1: No cobraré') }}
    </h4>
    
    <p class="text-sm text-gray-600 mb-4">
        {{ __('Si no cobraràs per la teva docència, només cal omplir les dades bàsiques obligatòries.') }}
    </p>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-4">
            <div>
                <x-input-label for="first_name" :value="__('Nom') . ' *'" />
                <x-text-input id="first_name" name="first_name" type="text" tabindex="0" 
                           class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                           :value="auth()->user()->teacherProfile?->first_name ?? ''"
                           required tabindex="0" />
                <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
            </div>
            
            <div>
                <x-input-label for="last_name" :value="__('Cognoms') . ' *'" />
                <x-text-input id="last_name" name="last_name" type="text" tabindex="0" 
                           class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                           :value="auth()->user()->teacherProfile?->last_name ?? ''"
                           required tabindex="0" />
                <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
            </div>
            
            <div>
                <x-input-label for="email" :value="__('Correu Electrònic') . ' *'" />
                <x-text-input id="email" name="email" type="email" tabindex="0" 
                           class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                           :value="auth()->user()->teacherProfile?->email ?? ''"
                           required tabindex="0" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />
            </div>
        </div>
        
        <div class="space-y-4">
            <div>
                <x-input-label for="phone" :value="__('Telèfon')" />
                <x-text-input id="phone" name="phone" type="tel" 
                           class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                           :value="auth()->user()->teacherProfile?->phone ?? ''" />
                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
            </div>
            
            <div>
                <x-input-label for="address" :value="__('Adreça')" />
                <x-text-input id="address" name="address" type="text" tabindex="0" 
                           class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                           :value="auth()->user()->teacherProfile?->address ?? ''" />
                <x-input-error class="mt-2" :messages="$errors->get('address')" />
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="postal_code" :value="__('Codi Postal')" />
                    <x-text-input id="postal_code" name="postal_code" type="text" tabindex="0" 
                               class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                               :value="auth()->user()->teacherProfile?->postal_code ?? ''" />
                    <x-input-error class="mt-2" :messages="$errors->get('postal_code')" />
                </div>
                
                <div>
                    <x-input-label for="city" :value="__('Ciutat')" />
                    <x-text-input id="city" name="city" type="text" tabindex="0" 
                               class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                               :value="auth()->user()->teacherProfile?->city ?? ''" />
                    <x-input-error class="mt-2" :messages="$errors->get('city')" />
                </div>
            </div>
        </div>
    </div>
    
    {{-- CONFIRMACIÓ --}}
    <div class="border-t pt-4 mt-6">
        <label class="flex items-start cursor-pointer">
            <input type="checkbox" tabindex="0" name="waived_confirmation" value="1" 
                   class="mt-1 mr-3" required
                   tabindex="0"
                   @if(auth()->user()->teacherProfile?->waived_confirmation) checked @endif>
            <div class="text-sm">
                <span class="font-semibold">{{ __('Confirmació obligatòria:') }}</span>
                <p class="text-gray-600">{{ __('Confirmo que NO cobraré per la meva docència durant aquest període.') }}</p>
            </div>
        </label>
    </div>
</div>

{{-- FORMULARI 2: OWN (COBREN ELLS) --}}
<div id="own-form" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6 @if(auth()->user()->teacherProfile?->payment_type !== 'own') hidden @endif">
    <h4 class="text-md font-bold text-gray-800 mb-4 flex items-center">
        <i class="bi bi-cash-coin mr-2 text-green-600"></i>
        {{ __('Opció 2: Cobraré jo mateix/a') }}
    </h4>
    
    <p class="text-sm text-gray-600 mb-4">
        {{ __('Si cobraràs directament, cal omplir les teves dades bancàries completes.') }}
    </p>
    
    {{-- DADES BÀSIQUES (reutilitzar) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="space-y-4">
            <div>
                <x-input-label for="first_name" :value="__('Nom') . ' *'" />
                <x-text-input id="first_name" name="first_name" type="text" tabindex="0" 
                           class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                           :value="auth()->user()->teacherProfile?->first_name ?? ''"
                           required tabindex="0" />
                <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
            </div>
            
            <div>
                <x-input-label for="last_name" :value="__('Cognoms') . ' *'" />
                <x-text-input id="last_name" name="last_name" type="text" tabindex="0" 
                           class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                           :value="auth()->user()->teacherProfile?->last_name ?? ''"
                           required tabindex="0" />
                <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
            </div>
            
            <div>
                <x-input-label for="email" :value="__('Correu Electrònic') . ' *'" />
                <x-text-input id="email" name="email" type="email" tabindex="0" 
                           class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                           :value="auth()->user()->teacherProfile?->email ?? ''"
                           required tabindex="0" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />
            </div>
        </div>
        
        <div class="space-y-4">
            <div>
                <x-input-label for="dni" :value="__('NIF/DNI') . ' *'" />
                <x-text-input id="dni" name="dni" type="text" tabindex="0" 
                           class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                           :value="auth()->user()->teacherProfile?->dni ?? ''"
                           required tabindex="0" />
                <x-input-error class="mt-2" :messages="$errors->get('dni')" />
            </div>
            
            <div>
                <x-input-label for="phone" :value="__('Telèfon')" />
                <x-text-input id="phone" name="phone" type="tel" 
                           class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                           :value="auth()->user()->teacherProfile?->phone ?? ''" />
                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
            </div>
            
            <div>
                <x-input-label for="address" :value="__('Adreça')" />
                <x-text-input id="address" name="address" type="text" tabindex="0" 
                           class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                           :value="auth()->user()->teacherProfile?->address ?? ''" />
                <x-input-error class="mt-2" :messages="$errors->get('address')" />
            </div>
        </div>
    </div>
    
    {{-- DADES BANCÀRIES --}}
    <div class="border-t pt-4 mt-6">
        <h5 class="font-semibold text-gray-800 mb-4">{{ __('Dades Bancàries') }}</h5>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <x-input-label for="iban" :value="__('IBAN') . ' *'" />
                    <div class="mt-1 relative">
                        <x-text-input id="iban" name="iban" type="password" tabindex="0" 
                                   class="mt-1 block w-full pr-20 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                   :value="auth()->user()->teacherProfile?->masked_iban ?? ''"
                                   placeholder="ES00 0000 0000 0000 0000"
                                   pattern="^ES\d{2}\s?\d{4}\s?\d{4}\s?\d{2}\s?\d{10}$"
                                   title="Format: ES00 0000 0000 0000 0000"
                                   tabindex="0" />
                        
                        <button type="button" 
                                onclick="toggleIbanVisibility()"
                                class="absolute inset-y-0 right-0 px-3 py-2 bg-gray-100 text-gray-600 hover:bg-gray-200 text-xs font-medium rounded-r-md"
                                tabindex="0">
                            @php
                                $hasIban = false;
                                try {
                                    $hasIban = auth()->user()->teacherProfile && !empty(auth()->user()->teacherProfile->iban);
                                } catch (\Exception $e) {
                                    $hasIban = false;
                                }
                            @endphp
                            <span id="iban-toggle-text">{{ $hasIban ? 'Mostrar' : 'Editar' }}</span>
                        </button>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">{{ __('Format: ES00 0000 0000 0000 0000') }}</p>
                    <x-input-error class="mt-2" :messages="$errors->get('iban')" />
                </div>
                
                <div>
                    <x-input-label for="bank_titular" :value="__('Titular del Compte') . ' *'" />
                    <x-text-input id="bank_titular" name="bank_titular" type="text" tabindex="0" 
                               class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                               :value="auth()->user()->teacherProfile?->bank_titular ?? ''"
                               required tabindex="0" />
                    <x-input-error class="mt-2" :messages="$errors->get('bank_titular')" />
                </div>
            </div>
            
            <div class="space-y-4">
                <div>
                    <x-input-label for="fiscal_situation" :value="__('Situació Fiscal') . ' *'" />
                    <select tabindex="0" id="fiscal_situation" name="fiscal_situation" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                            required>
                        <option value="">{{ __('Selecciona una opció') }}</option>
                        <option value="autonom" @if(auth()->user()->teacherProfile?->fiscal_situation === 'autonom') selected @endif>
                            {{ __('Autònom/a') }}
                        </option>
                        <option value="employee" @if(auth()->user()->teacherProfile?->fiscal_situation === 'employee') selected @endif>
                            {{ __('Treballador/a per compte alié') }}
                        </option>
                        <option value="pensioner" @if(auth()->user()->teacherProfile?->fiscal_situation === 'pensioner') selected @endif>
                            {{ __('Pensionista o jubilat/jubilada') }}
                        </option>
                        <option value="other" @if(auth()->user()->teacherProfile?->fiscal_situation === 'other') selected @endif>
                            {{ __('Altre (no llistat)') }}
                        </option>
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('fiscal_situation')" />
                </div>
                
                <div>
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" tabindex="0" name="invoice" value="1" 
                               class="mr-3"
                               @if(auth()->user()->teacherProfile?->invoice) checked @endif>
                        <div class="text-sm">
                            <span class="font-semibold">{{ __('Presentaré factura') }}</span>
                            <p class="text-gray-600">{{ __('Marcar si presentarà factura') }}</p>
                        </div>
                    </label>
                </div>
            </div>
        </div>
    </div>
    
    {{-- CONFIRMACIÓ --}}
    <div class="border-t pt-4 mt-6">
        <label class="flex items-start cursor-pointer">
            <input type="checkbox" tabindex="0" name="own_confirmation" value="1" 
                   class="mt-1 mr-3" required
                   tabindex="0"
                   @if(auth()->user()->teacherProfile?->own_confirmation) checked @endif>
            <div class="text-sm">
                <span class="font-semibold">{{ __('Confirmació obligatòria:') }}</span>
                <p class="text-gray-600">{{ __('Accepto cobrar i proporcionar les meves dades bancàries per al cobrament de la meva docència.') }}</p>
            </div>
        </label>
    </div>
</div>

{{-- FORMULARI 3: CEDED (CEDEIXEN COBRAMENT) --}}
<div id="ceded-form" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6 @if(auth()->user()->teacherProfile?->payment_type !== 'ceded') hidden @endif">
    <h4 class="text-md font-bold text-gray-800 mb-4 flex items-center">
        <i class="bi bi-people mr-2 text-purple-600"></i>
        {{ __('Opció 3: Cedeixo el cobrament') }}
    </h4>
    
    <p class="text-sm text-gray-600 mb-4">
        {{ __('Si cedeixes el cobrament, caldrà dades del beneficiari que rebrà els pagaments.') }}
    </p>
    
    {{-- DADES BÀSIQUES DEL PROFESSOR --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="space-y-4">
            <div>
                <x-input-label for="first_name" :value="__('Nom') . ' *'" />
                <x-text-input id="first_name" name="first_name" type="text" tabindex="0" 
                           class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                           :value="auth()->user()->teacherProfile?->first_name ?? ''"
                           required tabindex="0" />
                <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
            </div>
            
            <div>
                <x-input-label for="last_name" :value="__('Cognoms') . ' *'" />
                <x-text-input id="last_name" name="last_name" type="text" tabindex="0" 
                           class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                           :value="auth()->user()->teacherProfile?->last_name ?? ''"
                           required tabindex="0" />
                <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
            </div>
            
            <div>
                <x-input-label for="email" :value="__('Correu Electrònic') . ' *'" />
                <x-text-input id="email" name="email" type="email" tabindex="0" 
                           class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                           :value="auth()->user()->teacherProfile?->email ?? ''"
                           required tabindex="0" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />
            </div>
        </div>
        
        <div class="space-y-4">
            <div>
                <x-input-label for="phone" :value="__('Telèfon')" />
                <x-text-input id="phone" name="phone" type="tel" 
                           class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                           :value="auth()->user()->teacherProfile?->phone ?? ''" />
                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
            </div>
            
            <div>
                <x-input-label for="address" :value="__('Adreça')" />
                <x-text-input id="address" name="address" type="text" tabindex="0" 
                           class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                           :value="auth()->user()->teacherProfile?->address ?? ''" />
                <x-input-error class="mt-2" :messages="$errors->get('address')" />
            </div>
            
            <div>
                <x-input-label for="city" :value="__('Ciutat') . ' *'" />
                <x-text-input id="city" name="city" type="text" tabindex="0" 
                           class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                           :value="auth()->user()->teacherProfile?->city ?? ''"
                           required tabindex="0" />
                <x-input-error class="mt-2" :messages="$errors->get('city')" />
            </div>
        </div>
    </div>
    
    {{-- DADES DEL BENEFICIARI --}}
    <div class="border-t pt-4 mt-6">
        <h5 class="font-semibold text-gray-800 mb-4">{{ __('Dades del Beneficiari') }}</h5>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <x-input-label for="beneficiary_dni" :value="__('NIF/DNI del Beneficiari') . ' *'" />
                    <x-text-input id="beneficiary_dni" name="beneficiary_dni" type="text" tabindex="0" 
                               class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                               :value="auth()->user()->teacherProfile?->beneficiary_dni ?? ''"
                               required tabindex="0" />
                    <x-input-error class="mt-2" :messages="$errors->get('beneficiary_dni')" />
                </div>
                
                <div>
                    <x-input-label for="beneficiary_city" :value="__('Ciutat del Beneficiari') . ' *'" />
                    <x-text-input id="beneficiary_city" name="beneficiary_city" type="text" tabindex="0" 
                               class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                               :value="auth()->user()->teacherProfile?->beneficiary_city ?? ''"
                               required tabindex="0" />
                    <x-input-error class="mt-2" :messages="$errors->get('beneficiary_city')" />
                </div>
            </div>
            
            <div class="space-y-4">
                <div>
                    <x-input-label for="beneficiary_postal_code" :value="__('Codi Postal del Beneficiari')" />
                    <x-text-input id="beneficiary_postal_code" name="beneficiary_postal_code" type="text" tabindex="0" 
                               class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                               :value="auth()->user()->teacherProfile?->beneficiary_postal_code ?? ''" />
                    <x-input-error class="mt-2" :messages="$errors->get('beneficiary_postal_code')" />
                </div>
                
                <div>
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" tabindex="0" name="beneficiary_invoice" value="1" 
                               class="mr-3"
                               @if(auth()->user()->teacherProfile?->beneficiary_invoice) checked @endif>
                        <div class="text-sm">
                            <span class="font-semibold">{{ __('El beneficiari presentarà factura') }}</span>
                            <p class="text-gray-600">{{ __('Marcar si el beneficiari presentarà factura') }}</p>
                        </div>
                    </label>
                </div>
            </div>
        </div>
    </div>
    
    {{-- DADES BANCÀRIES DEL BENEFICIARI --}}
    <div class="border-t pt-4 mt-6">
        <h5 class="font-semibold text-gray-800 mb-4">{{ __('Dades Bancàries del Beneficiari') }}</h5>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <x-input-label for="beneficiary_iban" :value="__('IBAN del Beneficiari') . ' *'" />
                    <x-text-input id="beneficiary_iban" name="beneficiary_iban" type="text" tabindex="0" 
                               class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                               :value="auth()->user()->teacherProfile?->beneficiary_iban ?? ''"
                               placeholder="ES00 0000 0000 0000 0000"
                               pattern="^ES\d{2}\s?\d{4}\s?\d{4}\s?\d{2}\s?\d{10}$"
                               title="Format: ES00 0000 0000 0000 0000" />
                    <p class="mt-1 text-xs text-gray-500">{{ __('Format: ES00 0000 0000 0000 0000') }}</p>
                    <x-input-error class="mt-2" :messages="$errors->get('beneficiary_iban')" />
                </div>
                
                <div>
                    <x-input-label for="beneficiary_titular" :value="__('Titular del Compte') . ' *'" />
                    <x-text-input id="beneficiary_titular" name="beneficiary_titular" type="text" tabindex="0" 
                               class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                               :value="auth()->user()->teacherProfile?->beneficiary_titular ?? ''"
                               required tabindex="0" />
                    <x-input-error class="mt-2" :messages="$errors->get('beneficiary_titular')" />
                </div>
            </div>
            
            <div class="space-y-4">
                <div>
                    <x-input-label for="beneficiary_fiscal_situation" :value="__('Situació Fiscal del Beneficiari') . ' *'" />
                    <select tabindex="0" id="beneficiary_fiscal_situation" name="beneficiary_fiscal_situation" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                            required>
                        <option value="">{{ __('Selecciona una opció') }}</option>
                        <option value="autonom" @if(auth()->user()->teacherProfile?->beneficiary_fiscal_situation === 'autonom') selected @endif>
                            {{ __('Autònom/a') }}
                        </option>
                        <option value="employee" @if(auth()->user()->teacherProfile?->beneficiary_fiscal_situation === 'employee') selected @endif>
                            {{ __('Treballador/a per compte alié') }}
                        </option>
                        <option value="pensioner" @if(auth()->user()->teacherProfile?->beneficiary_fiscal_situation === 'pensioner') selected @endif>
                            {{ __('Pensionista o jubilat/jubilada') }}
                        </option>
                        <option value="other" @if(auth()->user()->teacherProfile?->beneficiary_fiscal_situation === 'other') selected @endif>
                            {{ __('Altre (no llistat)') }}
                        </option>
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('beneficiary_fiscal_situation')" />
                </div>
            </div>
        </div>
    </div>
    
    {{-- CONFIRMACIÓ --}}
    <div class="border-t pt-4 mt-6">
        <label class="flex items-start cursor-pointer">
            <input type="checkbox" tabindex="0" name="ceded_confirmation" value="1" 
                   class="mt-1 mr-3" required
                   tabindex="0"
                   @if(auth()->user()->teacherProfile?->ceded_confirmation) checked @endif>
            <div class="text-sm">
                <span class="font-semibold">{{ __('Confirmació obligatòria:') }}</span>
                <p class="text-gray-600">{{ __('Confirmo que cedeixo el cobrament al beneficiari indicat i que les dades proporcionades són correctes.') }}</p>
            </div>
        </label>
    </div>
</div>
