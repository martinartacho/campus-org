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
        
        @section('breadcrumbs')
            <nav class="flex mb-6" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li>
                        <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-gray-900 inline-flex items-center">
                            <svg class="w-5 h-5 mr-2.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                            </svg>
                            {{ __('Inici') }}
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <a href="{{ route('teacher.profile') }}" class="text-gray-700 hover:text-gray-900 ml-1 md:ml-2">
                                {{ __('Perfil del Professor') }}
                            </a>
                        </div>
                    </li>
                </ol>
            </nav>
        @endsection

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    {{ __('Dades del Professor') }}
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    {{ __('Gestiona la teva informació professional i dades de cobrament.') }}
                </p>
            </div>

            <div class="px-4 py-5 sm:p-6">
                <form method="POST" action="{{ route('teacher.profile.update') }}" class="space-y-6">
                    @csrf
                    
                    {{-- SELECCIÓ DE TIPUS DE COBRAMENT --}}
                    @include('profile.partials.payment-type-selection')
                    
                    {{-- FORMULARIS CONDICIONALS --}}
                    @include('profile.partials.payment-forms')
                    
                    {{-- BOTONS D'ACCIÓ --}}
                    <div class="flex flex-wrap gap-3 pt-6 border-t">
                        <button type="submit" 
                                class="px-6 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="bi bi-save mr-2"></i>
                            {{ __('Guardar Dades') }}
                        </button>
                        
                        @if(auth()->user()->teacherProfile && auth()->user()->teacherProfile->payment_status === 'confirmed')
                        <button type="button" 
                                onclick="generatePaymentPDF()"
                                class="px-6 py-2 bg-green-600 text-white font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <i class="bi bi-file-earmark-pdf mr-2"></i>
                            {{ __('Generar PDF') }}
                        </button>
                        @endif
                        
                        <a href="{{ route('dashboard') }}" 
                           class="px-6 py-2 bg-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            {{ __('Cancel·lar') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function toggleIbanVisibility() {
        const ibanInput = document.getElementById('iban');
        const toggleText = document.getElementById('iban-toggle-text');
        
        if (ibanInput.type === 'text') {
            ibanInput.type = 'password';
            toggleText.textContent = 'Mostrar';
        } else {
            ibanInput.type = 'text';
            toggleText.textContent = 'Mostrar';
        }
    }

    function generatePaymentPDF() {
        const button = event.target;
        button.disabled = true;
        button.innerHTML = '<i class="bi bi-hourglass-split mr-2"></i>{{ __("Generant...") }}';
        
        fetch('{{ route("profile.payment.pdf") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Descarregar PDF
                window.open(data.download_url, '_blank');
                
                // Mostrar missatge d'èxit
                alert(data.message);
            } else {
                alert(data.message || 'Error generant el PDF');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error generant el PDF');
        })
        .finally(() => {
            button.disabled = false;
            button.innerHTML = '<i class="bi bi-file-earmark-pdf mr-2"></i>{{ __("Generar PDF") }}';
        });
    }

    // Inicialitzar formulari segons tipus seleccionat
    document.addEventListener('DOMContentLoaded', function() {
        const selectedType = document.querySelector('input[name="payment_type"]:checked');
        if (selectedType) {
            selectPaymentType(selectedType.value);
        }
    });
    </script>
</x-app-layout>
