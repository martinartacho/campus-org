@extends('catalog.layout')

@section('title', 'Matriculación Completada')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Success Header -->
    <div class="text-center mb-8">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="bi bi-check-circle-fill text-green-600 text-4xl"></i>
        </div>
        
        <h1 class="text-4xl font-bold text-gray-900 mb-4">
            ¡Matriculación Completada!
        </h1>
        
        <p class="text-xl text-gray-600">
            @if($isFree)
                Te has matriculado correctamente en tus cursos seleccionados.
            @else
                El teu pagament s'ha processat correctament i la teva matriculació està confirmada.
            @endif
        </p>
    </div>

    <!-- Registration Details -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">
            <i class="bi bi-clipboard-check me-2"></i>Detalles de tu Matriculación
        </h2>
        
        <div class="space-y-4">
            @foreach($registrations as $registration)
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <!-- Course Info -->
                        <div class="flex-1 mb-4 md:mb-0">
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">
                                {{ $registration->course->title }}
                            </h3>
                            <div class="flex flex-wrap gap-2 mb-2">
                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                                    {{ $registration->course->code }}
                                </span>
                                <span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full">
                                    {{ $registration->registration_code }}
                                </span>
                                @if($registration->course->season)
                                    <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">
                                        {{ $registration->course->season->name }}
                                    </span>
                                @endif
                            </div>
                            
                            <div class="text-sm text-gray-600">
                                @if($registration->course->start_date)
                                    <div class="flex items-center mb-1">
                                        <i class="bi bi-calendar3 me-2"></i>
                                        Inicio: {{ $registration->course->start_date->format('d/m/Y') }}
                                    </div>
                                @endif
                                @if($registration->course->hours)
                                    <div class="flex items-center mb-1">
                                        <i class="bi bi-clock me-2"></i>
                                        Duración: {{ $registration->course->hours }} horas
                                    </div>
                                @endif
                                @if($registration->course->location)
                                    <div class="flex items-center mb-1">
                                        <i class="bi bi-geo-alt me-2"></i>
                                        Ubicación: {{ $registration->course->location }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Status and Price -->
                        <div class="text-center md:text-right">
                            <div class="mb-3">
                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">
                                    <i class="bi bi-check-circle me-1"></i>Confirmada
                                </span>
                            </div>
                            
                            @if($registration->amount > 0)
                                <div class="text-lg font-bold text-gray-900">
                                    {{ number_format($registration->amount, 2) }} &euro;
                                </div>
                                <div class="text-sm text-gray-500">Pagado</div>
                            @else
                                <div class="text-lg font-bold text-green-600">
                                    GRATIS
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Payment Information -->
    @if(!$isFree && isset($session))
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">
                <i class="bi bi-receipt me-2"></i>Informació del Pagament
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-semibold text-gray-800 mb-2">Resum del Pagament</h3>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-600">ID de Transacció:</dt>
                            <dd class="font-mono">{{ $session->payment_intent }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Data del Pagament:</dt>
                            <dd>{{ \Carbon\Carbon::createFromTimestamp($session->created)->format('d/m/Y H:i') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Import Total:</dt>
                            <dd class="font-semibold">{{ number_format($session->amount_total / 100, 2) }} &euro;</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Mètode de Pagament:</dt>
                            <dd>{{ $session->payment_method_types[0] ?? 'Targeta' }}</dd>
                        </div>
                    </dl>
                </div>
                
                <div>
                    <h3 class="font-semibold text-gray-800 mb-2">Factura</h3>
                    <p class="text-gray-600 text-sm mb-3">
                        Recibirás un correo electrónico con la factura detallada de tu matriculación.
                    </p>
                    <a href="{{ route('registration.invoice', $registration->id) }}" 
                       target="_blank"
                       class="inline-block px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition text-sm">
                        <i class="bi bi-download me-2"></i>Descargar Factura
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Next Steps -->
    <div class="bg-blue-50 rounded-lg p-6 mb-8">
        <h2 class="text-xl font-bold text-blue-900 mb-4">
            <i class="bi bi-info-circle me-2"></i>Próximos Pasos
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="font-semibold text-blue-800 mb-2">Antes del Curso</h3>
                <ul class="space-y-2 text-blue-700 text-sm">
                    <li class="flex items-start">
                        <i class="bi bi-check-circle me-2 mt-0.5"></i>
                        <span>Recibirás un email de confirmación con todos los detalles</span>
                    </li>
                    <li class="flex items-start">
                        <i class="bi bi-check-circle me-2 mt-0.5"></i>
                        <span>Te contactaremos antes del inicio para recordatorio</span>
                    </li>
                    <li class="flex items-start">
                        <i class="bi bi-check-circle me-2 mt-0.5"></i>
                        <span>Prepara los materiales necesarios para el primer día</span>
                    </li>
                </ul>
            </div>
            
            <div>
                <h3 class="font-semibold text-blue-800 mb-2">Durante el Curso</h3>
                <ul class="space-y-2 text-blue-700 text-sm">
                    <li class="flex items-start">
                        <i class="bi bi-check-circle me-2 mt-0.5"></i>
                        <span>Accede al campus virtual para seguir tu progreso</span>
                    </li>
                    <li class="flex items-start">
                        <i class="bi bi-check-circle me-2 mt-0.5"></i>
                        <span>Participa activamente en las clases y actividades</span>
                    </li>
                    <li class="flex items-start">
                        <i class="bi bi-check-circle me-2 mt-0.5"></i>
                        <span>Contacta con soporte si necesitas ayuda</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Contact Information -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-4">
            <i class="bi bi-question-circle me-2"></i>¿Necesitas Ayuda?
        </h2>
        
        <p class="text-gray-600 mb-4">
            Si tienes alguna pregunta sobre tu matriculación o los cursos, no dudes en contactarnos:
        </p>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
            <div class="p-4 bg-gray-50 rounded-lg">
                <i class="bi bi-envelope text-blue-600 text-2xl mb-2"></i>
                <h3 class="font-semibold text-gray-900 mb-1">Email</h3>
                <a href="mailto:info@campus.org" class="text-blue-600 hover:text-blue-800 text-sm">
                    info@campus.org
                </a>
            </div>
            
            <div class="p-4 bg-gray-50 rounded-lg">
                <i class="bi bi-telephone text-blue-600 text-2xl mb-2"></i>
                <h3 class="font-semibold text-gray-900 mb-1">Teléfono</h3>
                <a href="tel:+34900123456" class="text-blue-600 hover:text-blue-800 text-sm">
                    +34 900 123 456
                </a>
            </div>
            
            <div class="p-4 bg-gray-50 rounded-lg">
                <i class="bi bi-clock text-blue-600 text-2xl mb-2"></i>
                <h3 class="font-semibold text-gray-900 mb-1">Horario</h3>
                <p class="text-gray-600 text-sm">
                    L-V: 9:00 - 18:00
                </p>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4">
        @if(auth()->check())
            <a href="{{ route('dashboard') }}" 
               class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold text-center">
                <i class="bi bi-house me-2"></i>Ir a Mi Campus
            </a>
        @else
            <a href="{{ route('login') }}" 
               class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold text-center">
                <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
            </a>
        @endif
        
        <a href="{{ route('catalog.index') }}" 
           class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition font-semibold text-center">
            <i class="bi bi-search me-2"></i>Explorar Más Cursos
        </a>
    </div>
</div>
@endsection
