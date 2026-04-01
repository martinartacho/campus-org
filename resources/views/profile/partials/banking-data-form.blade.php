<section id="banking-data">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            <i class="bi bi-bank mr-2"></i>{{ __('Dades Bancàries de Cobrament') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __("Gestiona les teves dades bancàries per als cobraments de professor.") }}
        </p>
    </header>

    @if(session('status') == 'banking-data-updated')
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ __('Les teves dades bancàries s\'han actualitzat correctament!') }}
        </div>
    @endif

    @if(session('status') == 'pdf-generated')
        <div class="mb-4 p-4 bg-blue-100 border border-blue-400 text-blue-700 rounded">
            {{ __('PDF generat correctament! Pots descarregar-lo des de la secció de documents.') }}
        </div>
    @endif

    <!-- Alerta si no té CampusTeacher -->
    @if(!auth()->user()->teacherProfile)
        <div class="mb-6 p-4 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded">
            <div class="flex items-start">
                <i class="bi bi-exclamation-triangle-fill mr-3 mt-1"></i>
                <div>
                    <h3 class="font-medium text-yellow-800">{{ __('Avís: No tens perfil de professor') }}</h3>
                    <p class="mt-2 text-sm text-yellow-700">
                        {{ __('Per gestionar les teves dades bancàries, primer has de tenir un perfil de professor associat. Si ets professor/a i no tens accés, contacta amb l\'administració.') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    @if(auth()->user()->teacherProfile)
        <form method="POST" action="{{ route('profile.banking-data.update') }}" class="mt-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Informació del professor -->
            <div class="mb-6 p-4 border rounded-lg bg-gray-50">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="bi bi-person-badge mr-2"></i>{{ __('Informació del Professor') }}
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="display_name" :value="__('Nom Complet')" />
                        <div class="mt-1 text-sm text-gray-900">
                            {{ auth()->user()->teacherProfile?->full_name ?? 'No disponible' }}
                        </div>
                    </div>
                    
                    <div>
                        <x-input-label for="display_dni" :value="__('DNI')" />
                        <div class="mt-1 text-sm text-gray-900">
                            {{ auth()->user()->teacherProfile?->dni ?? 'No disponible' }}
                        </div>
                    </div>
                    
                    <div>
                        <x-input-label for="display_email" :value="__('Correu')" />
                        <div class="mt-1 text-sm text-gray-900">
                            {{ auth()->user()->teacherProfile?->email ?? auth()->user()->email }}
                        </div>
                    </div>
                    
                    <div>
                        <x-input-label for="display_phone" :value="__('Telèfon')" />
                        <div class="mt-1 text-sm text-gray-900">
                            {{ auth()->user()->teacherProfile?->phone ?? 'No disponible' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dades bancàries -->
            <div class="border rounded-lg p-6 bg-white shadow-sm">
                <h3 class="text-lg font-medium text-gray-900 mb-6">
                    <i class="bi bi-credit-card mr-2"></i>{{ __('Dades Bancàries') }}
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <x-input-label for="iban" :value="__('IBAN') . ' *'" />
                            <div class="mt-1 relative">
                                {{-- Camp IBAN ocult --}}
                                <x-text-input id="iban" name="iban" type="text" class="mt-1 block w-full pr-20" 
                                       @try
                                           :value="auth()->user()->teacherProfile?->masked_iban ?? ''"
                                       @catch (\Exception $e)
                                           value=""
                                           @enderror
                                           placeholder="ES00 0000 0000 0000 0000"
                                           pattern="^ES\d{2}\s?\d{4}\s?\d{4}\s?\d{2}\s?\d{10}$"
                                           title="Format: ES00 0000 0000 0000 0000" 
                                           oninput="validateIbanRealTime(this)" />
                                <div class="mt-1" id="iban-validation-feedback"></div>
                                
                                {{-- Botó per mostrar/ocultar --}}
                                <button type="button" 
                                        onclick="toggleIbanVisibility()"
                                        class="absolute inset-y-0 right-0 px-3 py-2 bg-gray-100 text-gray-600 hover:bg-gray-200 text-xs font-medium rounded-r-md">
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
                            <x-text-input id="bank_titular" name="bank_titular" type="text" class="mt-1 block w-full" 
                                       :value="auth()->user()->teacherProfile?->decrypted_bank_titular ?? ''"
                                       :placeholder="__('Nom i cognoms del titular')" />
                            <x-input-error class="mt-2" :messages="$errors->get('bank_titular')" />
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <x-input-label for="fiscal_id" :value="__('Identificació Fiscal')" />
                            <x-text-input id="fiscal_id" name="fiscal_id" type="text" class="mt-1 block w-full" 
                                       :value="auth()->user()->teacherProfile?->decrypted_fiscal_id ?? ''"
                                       :placeholder="__('Nomes si és diferent del DNI')" />
                            <p class="mt-1 text-xs text-gray-500">{{ __('Nomes si és diferent del DNI') }}</p>
                            <x-input-error class="mt-2" :messages="$errors->get('fiscal_id')" />
                        </div>

                        <div>
                            <x-input-label for="fiscal_situation" :value="__('Situació Fiscal') . ' *'" />
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="fiscal_situation" value="autonom" 
                                           class="border-gray-300 text-blue-600 focus:ring-blue-500"
                                           {{ auth()->user()->teacherProfile?->fiscal_situation == 'autonom' ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm">{{ __('Autònom/a') }}</span>
                                </label>
                                
                                <label class="flex items-center">
                                    <input type="radio" name="fiscal_situation" value="employee" 
                                           class="border-gray-300 text-blue-600 focus:ring-blue-500"
                                           {{ auth()->user()->teacherProfile?->fiscal_situation == 'employee' ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm">{{ __('Treballador/a per compte alié') }}</span>
                                </label>
                                
                                <label class="flex items-center">
                                    <input type="radio" name="fiscal_situation" value="pensioner" 
                                           class="border-gray-300 text-blue-600 focus:ring-blue-500"
                                           {{ auth()->user()->teacherProfile?->fiscal_situation == 'pensioner' ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm">{{ __('Pensionista o jubilat/jubilada') }}</span>
                                </label>
                                
                                <label class="flex items-center">
                                    <input type="radio" name="fiscal_situation" value="altre" 
                                           class="border-gray-300 text-blue-600 focus:ring-blue-500"
                                           {{ auth()->user()->teacherProfile?->fiscal_situation == 'altre' ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm">{{ __('Altre (no llistat)') }}</span>
                                </label>
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('fiscal_situation')" />
                        </div>

                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="invoice" value="1"
                                       class="border-gray-300 rounded text-blue-600 focus:ring-blue-500"
                                       {{ auth()->user()->teacherProfile?->invoice == '1' ? 'checked' : '' }}>
                                <span class="ml-2 text-sm">{{ __('Presentaré factura') }}</span>
                            </label>
                            <p class="mt-1 text-xs text-gray-500">{{ __('Marcar si presentarà factura') }}</p>
                            <x-input-error class="mt-2" :messages="$errors->get('invoice')" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botons d'acció -->
            <div class="mt-6 space-y-3">
                <p class="text-sm text-gray-600">
                    {{ __('Els camps marcats amb * són obligatoris') }}
                </p>
                
                <div class="flex flex-wrap gap-3">
                    <button type="button" 
                            onclick="window.location.href='{{ route('dashboard') }}'"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="bi bi-arrow-left mr-2"></i>
                        {{ __('Cancel·lar') }}
                    </button>
                    
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="bi bi-save mr-2"></i>
                        {{ __('Guardar Dades') }}
                    </button>
                    
                    <button type="button" 
                            onclick="generateBankingPDF()"
                            class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <i class="bi bi-file-pdf mr-2"></i>
                        {{ __('Generar PDF') }}
                    </button>
                </div>
            </div>
        </form>
    @endif

    <!-- Script per generar PDF -->
    <script>
        function generateBankingPDF() {
            if (confirm('{{ __("Estàs segur que vols generar el PDF amb les teves dades bancàries?") }}')) {
                fetch('{{ route('profile.banking-data.pdf') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('{{ __("PDF generat correctament!") }}');
                        window.open(data.pdf_url, '_blank');
                    } else {
                        alert('{{ __("Error al generar el PDF") }}: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('{{ __("Error al generar el PDF") }}');
                });
            }
        }
        
        // Funció per mostrar/ocultar IBAN
        function toggleIbanVisibility() {
            const ibanInput = document.getElementById('iban');
            const toggleText = document.getElementById('iban-toggle-text');
            const currentValue = ibanInput.value;
            
            if (currentValue && currentValue.includes('****')) {
                // Mostrar IBAN complet
                ibanInput.value = ibanInput.dataset.originalIban || '';
                toggleText.textContent = 'Ocultar';
            } else if (currentValue) {
                // Ocultar IBAN
                ibanInput.dataset.originalIban = currentValue;
                const formatted = formatIbanHidden(currentValue);
                ibanInput.value = formatted;
                toggleText.textContent = 'Mostrar';
            }
        }
        
        function formatIbanHidden(iban) {
            const clean = iban.replace(/\s/g, '');
            if (clean.length >= 24) {
                return clean.substring(0, 4) + ' **** ' + clean.substring(clean.length - 4);
            }
            return iban;
        }
        
        // Inicialitzar
        document.addEventListener('DOMContentLoaded', function() {
            const ibanInput = document.getElementById('iban');
            if (ibanInput && ibanInput.value && !ibanInput.value.includes('****')) {
                toggleIbanVisibility(); // Ocultar per defecte
            }
        });
        
        // Validació IBAN en temps real
        function validateIbanRealTime(input) {
            const feedback = document.getElementById('iban-validation-feedback');
            const value = input.value.replace(/\s/g, ''); // Remove spaces for validation
            
            if (value.length === 0) {
                feedback.innerHTML = '';
                input.classList.remove('border-green-500', 'border-red-500');
                return;
            }
            
            // Basic IBAN validation
            const ibanRegex = /^ES\d{2}\d{4}\d{4}\d{2}\d{10}$/;
            const isValid = ibanRegex.test(value);
            
            if (isValid) {
                feedback.innerHTML = '<span class="text-xs text-green-600">✅ Formato IBAN válido</span>';
                input.classList.remove('border-red-500');
                input.classList.add('border-green-500');
            } else {
                feedback.innerHTML = '<span class="text-xs text-red-600">❌ Formato IBAN inválido. Use: ES00 0000 0000 0000 0000</span>';
                input.classList.remove('border-green-500');
                input.classList.add('border-red-500');
            }
        }
    </script>
</section>
