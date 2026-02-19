@extends('campus.shared.layout')

@section('title', __('campus.treasury'))
@section('subtitle', __('campusAccés a la zona de tresoreria'))

@section('content')
<div class="container mx-auto py-8">
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif
    
    <!-- Mensaje de éxito -->

    <div class="mb-8 p-6 bg-green-50 border border-green-200 rounded-lg">
        <div class="flex items-center mb-4">
            <svg class="w-8 h-8 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <div>
                <h1 class="text-2xl font-bold text-green-800">Dades registrades correctament</h1>
                <p class="text-green-700 mt-1">{{ $message ?? 'Les dades s\'han registrat correctament.' }}</p>
            </div>
        </div>
        
        <!-- Información del consentimiento -->
        @if($latestConsent)
            <div class="mt-4 p-4 bg-white border border-green-100 rounded">
                <h3 class="font-medium text-green-800 mb-2">📄 Consentiment RGPD</h3>
                <p class="text-sm text-gray-600 mb-3">
                    Consentiment actualitzat el {{ \Carbon\Carbon::parse($latestConsent->updated_at)->format('d/m/Y H:i') }}
                </p>
                
                @if($latestConsent->document_path)
                    <a href="{{ route('consents.download', $latestConsent) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Descarregar PDF del consentiment
                    </a>
                @endif
            </div>
        @endif
        
        <!-- Información del pago -->
        @if($token->metadata && isset($token->metadata['payment_data_completed']))
            <div class="mt-4 p-4 bg-white border border-green-100 rounded">
                <h3 class="font-medium text-green-800 mb-2">💰 Dades de pagament</h3>
                <p class="text-sm text-gray-600">
                    @if(isset($token->metadata['payment_option']))
                        @php
                            $paymentOptions = [
                                'own_fee' => 'Faré el cobrament',
                                'ceded_fee' => 'Cedeixo la titularitat',
                                'waived_fee' => 'Renuncio voluntàriament al cobrament'
                            ];
                        @endphp
                        Opció seleccionada: <strong>{{ $paymentOptions[$token->metadata['payment_option']] ?? $token->metadata['payment_option'] }}</strong>
                    @endif
                    @if(isset($token->metadata['payment_completed_at']))
                        <br>Completat el: {{ \Carbon\Carbon::parse($token->metadata['payment_completed_at'])->format('d/m/Y H:i') }}
                    @endif
                </p>
            </div>
        @endif
    </div>
    
    <!-- Enlaces de acción -->
    <div class="mt-8 flex flex-col sm:flex-row gap-4">
        <!-- Botón para descargar PDF del consentimiento -->
        @if($latestConsent && $latestConsent->document_path)
            <a href="{{ route('consents.download', $latestConsent) }}" 
               class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white rounded hover:bg-blue-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Descarregar PDF
            </a>
        @endif

        {{--  Si Ja ha omplert el formulariPot fer login --}}
        <a href="{{ url('/') }}" 
           class="inline-flex items-center justify-center px-6 py-3 bg-green-600 text-white rounded hover:bg-green-700">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Entrar al campus
        </a>

        <!-- Botón para cerrar ventana -->
        <button onclick="window.close()" 
                class="inline-flex items-center justify-center px-6 py-3 bg-gray-600 text-white rounded hover:bg-gray-700">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Tancar finestra
        </button>

         @if($teacher)
            <div class="mt-4">
                <p class="text-gray-600 mb-2">Si no recordes la teva contrasenya:</p>
                <a href="{{ route('password.request') }}" 
                   class="inline-flex items-center text-blue-600 hover:text-blue-800">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    Recuperar contrasenya
                </a>
            </div>
        @endif
        
    </div>
    
    <!-- Resumen de datos -->
    @if($teacher && $latestPayment && $courseInfo)
        <div class="mt-8 p-6 bg-gray-50 border border-gray-200 rounded-lg">
            <h3 class="text-lg font-semibold mb-4">📋 Resum del procés completat</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Datos del profesor -->
                <div>
                    <h4 class="font-medium text-gray-700 mb-2">👤 Professor</h4>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li><strong>Nom:</strong> {{ $teacher->first_name }} {{ $teacher->last_name }}</li>
                        <li><strong>Codi:</strong> {{ $teacher->teacher_code }}</li>
                        <li><strong>Estat:</strong> 
                            <span class="px-2 py-1 rounded text-xs {{ $teacher->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $teacher->status === 'active' ? 'Actiu' : 'Inactiu' }}
                            </span>
                        </li>
                    </ul>
                </div>
                
                <!-- Datos del curso -->
                <div>
                    <h4 class="font-medium text-gray-700 mb-2">📚 Activitat formativa</h4>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li><strong>Codi:</strong> {{ $courseInfo->code }}</li>
                        <li><strong>Títol:</strong> {{ $courseInfo->title }}</li>
                        <li><strong>Hores:</strong> {{ $courseInfo->hours }}h</li>
                        <li><strong>Crèdits:</strong> {{ $courseInfo->credits }}</li>
                    </ul>
                </div>
                
                <!-- Opción de pago -->
                <div>
                    <h4 class="font-medium text-gray-700 mb-2">💰 Opció de cobrament</h4>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>
                            <strong>Tipo:</strong> 
                            <span class="px-2 py-1 rounded text-xs {{ 
                                $latestPayment->needs_payment === 'waived_fee' ? 'bg-yellow-100 text-yellow-800' : 
                                ($latestPayment->needs_payment === 'ceded_fee' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') 
                            }}">
                                {{ 
                                    $latestPayment->needs_payment === 'waived_fee' ? 'Renúncia' : 
                                    ($latestPayment->needs_payment === 'ceded_fee' ? 'Cedut' : 'Propi') 
                                }}
                            </span>
                        </li>
                        <li><strong>Factura:</strong> {{ $latestPayment->invoice ? 'Sí' : 'No' }}</li>
                        @if($latestPayment->updated_at)
                            <li><strong>Completat:</strong> {{ \Carbon\Carbon::parse($latestPayment->updated_at)->format('d/m/Y H:i') }}</li>
                        @endif
                    </ul>
                </div>
            </div>
            
            <!-- Información adicional -->
            @if($latestConsent && $latestConsent->document_path)
                <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded">
                    <p class="text-sm text-blue-800">
                        <strong>📄 Document de consentiment generat:</strong> 
                        El PDF final ha estat creat i emmagatzemat correctament.
                    </p>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection