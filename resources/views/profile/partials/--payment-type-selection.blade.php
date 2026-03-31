{{-- SELECCIÓ DE TIPUS DE COBRAMENT --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
        <i class="bi bi-cash-stack mr-2 text-blue-600"></i>
        {{ __('Política Econòmica i Cobrament') }}
    </h3>
    
    <p class="text-sm text-gray-600 mb-4">
        {{ __('Selecciona la teva opció de cobrament per aquest curs. Aquesta configuració s\'aplicarà a tots els teus cursos actius.') }}
    </p>
    
    {{-- OPCIÓ 1: WAIVED (NO COBREN) --}}
    <div class="border rounded-lg p-4 mb-3 hover:bg-gray-50 transition-colors cursor-pointer"
         onclick="selectPaymentType('waived')">
        <label class="flex items-start cursor-pointer">
            <input type="radio" name="payment_type" value="waived" 
                   class="mt-1 mr-3" 
                   @if(auth()->user()->teacherProfile?->payment_type === 'waived') checked @endif>
            <div class="flex-1">
                <div class="flex items-center mb-2">
                    <span class="font-semibold text-gray-800">🚫 {{ __('No cobraré') }}</span>
                    <span class="ml-2 text-xs bg-gray-100 px-2 py-1 rounded">{{ __('Opció 1') }}</span>
                </div>
                <p class="text-sm text-gray-600">{{ __('No rebré cap pagament per la meva docència') }}</p>
                
                <div class="mt-2 text-xs text-blue-600">
                    <i class="bi bi-info-circle"></i> 
                    {{ __('Només caldrà omplir dades bàsiques: nom, cognoms, correu') }}
                </div>
            </div>
        </label>
    </div>
    
    {{-- OPCIÓ 2: OWN (COBREN ELLS) --}}
    <div class="border rounded-lg p-4 mb-3 hover:bg-gray-50 transition-colors cursor-pointer"
         onclick="selectPaymentType('own')">
        <label class="flex items-start cursor-pointer">
            <input type="radio" name="payment_type" value="own" 
                   class="mt-1 mr-3"
                   @if(auth()->user()->teacherProfile?->payment_type === 'own') checked @endif>
            <div class="flex-1">
                <div class="flex items-center mb-2">
                    <span class="font-semibold text-gray-800">💰 {{ __('Cobraré jo mateix/a') }}</span>
                    <span class="ml-2 text-xs bg-green-100 px-2 py-1 rounded">{{ __('Opció 2') }}</span>
                </div>
                <p class="text-sm text-gray-600">{{ __('El pagament anirà directament al meu compte bancari') }}</p>
                
                <div class="mt-2 text-xs text-blue-600">
                    <i class="bi bi-info-circle"></i> 
                    {{ __('Caldrà omplir dades bancàries completes') }}
                </div>
            </div>
        </label>
    </div>
    
    {{-- OPCIÓ 3: CEDED (CEDEIXEN COBRAMENT) --}}
    <div class="border rounded-lg p-4 mb-3 hover:bg-gray-50 transition-colors cursor-pointer"
         onclick="selectPaymentType('ceded')">
        <label class="flex items-start cursor-pointer">
            <input type="radio" name="payment_type" value="ceded" 
                   class="mt-1 mr-3"
                   @if(auth()->user()->teacherProfile?->payment_type === 'ceded') checked @endif>
            <div class="flex-1">
                <div class="flex items-center mb-2">
                    <span class="font-semibold text-gray-800">🤝 {{ __('Cedeixo el cobrament') }}</span>
                    <span class="ml-2 text-xs bg-purple-100 px-2 py-1 rounded">{{ __('Opció 3') }}</span>
                </div>
                <p class="text-sm text-gray-600">{{ __('Una altra persona o entitat cobrarà en el meu lloc') }}</p>
                
                <div class="mt-2 text-xs text-blue-600">
                    <i class="bi bi-info-circle"></i> 
                    {{ __('Caldrà omplir dades del beneficiari') }}
                </div>
            </div>
        </label>
    </div>
    
    {{-- AVÍS DE PERÍODE --}}
    @if(isset($coursePeriod) && $coursePeriod['confirmation_closed'])
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-3">
        <div class="flex items-center">
            <i class="bi bi-exclamation-triangle text-yellow-600 mr-2"></i>
            <div>
                <p class="text-sm font-semibold text-yellow-800">{{ __('Període de confirmació tancat') }}</p>
                <p class="text-xs text-yellow-700">{{ __('Els canvis realitzats no s\'aplicaran al proper pagament. Es guardaran per al següent període.') }}</p>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
function selectPaymentType(type) {
    // Marcar el radio button
    document.querySelector(`input[name="payment_type"][value="${type}"]`).checked = true;
    
    // Amagar tots els formularis
    document.getElementById('waived-form').classList.add('hidden');
    document.getElementById('own-form').classList.add('hidden');
    document.getElementById('ceded-form').classList.add('hidden');
    
    // Mostrar el formulari corresponent
    document.getElementById(`${type}-form`).classList.remove('hidden');
    
    // Scroll al formulari
    document.getElementById(`${type}-form`).scrollIntoView({ behavior: 'smooth' });
}
</script>
