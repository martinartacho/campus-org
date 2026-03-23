<div class="space-y-6">
    <div>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <i class="bi bi-bank mr-2"></i>{{ __('Dades Bancàries de Cobrament') }}
        </h2>
    </div>

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
        <form method="POST" action="{{ route('profile.banking-data.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

        <!-- Informació del professor -->
        <div class="mb-6 p-4 border rounded-lg bg-gray-50">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                <i class="bi bi-person-badge mr-2"></i>{{ __('Informació del Professor') }}
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('Nom Complet') }}</label>
                    <div class="text-sm text-gray-900">
                        {{ auth()->user()->teacherProfile?->full_name ?? 'No disponible' }}
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('DNI') }}</label>
                    <div class="mt-1 text-sm text-gray-900">
                        {{ auth()->user()->teacherProfile?->dni ?? 'No disponible' }}
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('Correu') }}</label>
                    <div class="mt-1 text-sm text-gray-900">
                        {{ auth()->user()->teacherProfile?->email ?? auth()->user()->email }}
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('Telèfon') }}</label>
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
                        <label for="iban" class="block text-sm font-medium text-gray-700">
                            {{ __('IBAN') }} *
                        </label>
                        <input type="text" 
                               id="iban"
                               name="iban" 
                               value="{{ auth()->user()->teacherProfile?->iban ?? '' }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               placeholder="ES00 0000 0000 0000 0000"
                               pattern="^ES\d{2}\s?\d{4}\s?\d{4}\s?\d{2}\s?\d{10}$"
                               title="Format: ES00 0000 0000 0000 0000">
                        <p class="mt-1 text-xs text-gray-500">{{ __('Format: ES00 0000 0000 0000 0000') }}</p>
                        @error('iban')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="bank_titular" class="block text-sm font-medium text-gray-700">
                            {{ __('Titular del Compte') }} *
                        </label>
                        <input type="text" 
                               id="bank_titular"
                               name="bank_titular" 
                               value="{{ auth()->user()->teacherProfile?->bank_titular ?? '' }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               placeholder="{{ __('Nom i cognoms del titular') }}">
                        @error('bank_titular')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label for="fiscal_id" class="block text-sm font-medium text-gray-700">
                            {{ __('Identificació Fiscal') }}
                        </label>
                        <input type="text" 
                               id="fiscal_id"
                               name="fiscal_id" 
                               value="{{ auth()->user()->teacherProfile?->fiscal_id ?? '' }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               placeholder="{{ __('Nomes si és diferent del DNI') }}">
                        <p class="mt-1 text-xs text-gray-500">{{ __('Nomes si és diferent del DNI') }}</p>
                        @error('fiscal_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            {{ __('Situació Fiscal') }}
                        </label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" 
                                       name="fiscal_situation" 
                                       value="autonom" 
                                       class="border-gray-300 text-blue-600 focus:ring-blue-500"
                                       {{ auth()->user()->teacherProfile?->fiscal_situation == 'autonom' ? 'checked' : '' }}>
                                <span class="ml-2 text-sm">{{ __('Autònom/a') }}</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="radio" 
                                       name="fiscal_situation" 
                                       value="employee" 
                                       class="border-gray-300 text-blue-600 focus:ring-blue-500"
                                       {{ auth()->user()->teacherProfile?->fiscal_situation == 'employee' ? 'checked' : '' }}>
                                <span class="ml-2 text-sm">{{ __('Treballador/a per compte alié') }}</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="radio" 
                                       name="fiscal_situation" 
                                       value="pensioner" 
                                       class="border-gray-300 text-blue-600 focus:ring-blue-500"
                                       {{ auth()->user()->teacherProfile?->fiscal_situation == 'pensioner' ? 'checked' : '' }}>
                                <span class="ml-2 text-sm">{{ __('Pensionista o jubilat/jubilada') }}</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="radio" 
                                       name="fiscal_situation" 
                                       value="altre" 
                                       class="border-gray-300 text-blue-600 focus:ring-blue-500"
                                       {{ auth()->user()->teacherProfile?->fiscal_situation == 'altre' ? 'checked' : '' }}>
                                <span class="ml-2 text-sm">{{ __('Altre (no llistat)') }}</span>
                            </label>
                        </div>
                        @error('fiscal_situation')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   name="invoice" 
                                   value="1"
                                   class="border-gray-300 rounded text-blue-600 focus:ring-blue-500"
                                   {{ auth()->user()->teacherProfile?->invoice == '1' ? 'checked' : '' }}>
                            <span class="ml-2 text-sm">{{ __('Presentaré factura') }}</span>
                        </label>
                        <p class="mt-1 text-xs text-gray-500">{{ __('Marcar si presentarà factura') }}</p>
                        @error('invoice')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Botons d'acció -->
        <div class="flex justify-between items-center">
            <div class="text-sm text-gray-600">
                <i class="bi bi-info-circle mr-1"></i>
                {{ __('Els camps marcats amb * són obligatoris') }}
            </div>
            
            <div class="space-x-4">
                <button type="button" 
                        onclick="window.location.href='{{ route('dashboard') }}'"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="bi bi-arrow-left mr-2"></i>
                    {{ __('Cancel·lar') }}
                </button>
                
                <button type="submit" 
                        class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="bi bi-save mr-2"></i>
                    {{ __('Guardar Dades') }}
                </button>
                
                <button type="button" 
                        onclick="generateBankingPDF()"
                        class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
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
                        window.location.reload();
                    } else {
                        alert('{{ __("Error generant el PDF") }}: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('{{ __("Error generant el PDF") }}');
                });
            }
        }
    </script>
</div>
