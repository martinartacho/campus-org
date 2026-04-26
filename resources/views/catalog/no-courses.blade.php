@extends('catalog.layout')

@section('title', 'No hi ha cursos disponibles')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="text-center">
        <!-- Icon -->
        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-8">
            <i class="bi bi-calendar-x text-gray-400 text-4xl"></i>
        </div>
        
        <!-- Message -->
        <h1 class="text-4xl font-bold text-gray-900 mb-4">
            No hi ha cursos disponibles
        </h1>
        
        <p class="text-xl text-gray-600 mb-8">
            {{ $message ?? 'En aquest moment no hi ha temporades disponibles per a matriculació.' }}
        </p>
        
        <!-- Additional Information -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8 text-left">
            <h2 class="text-lg font-semibold text-blue-900 mb-4">
                <i class="bi bi-info-circle me-2"></i>Què significa això?
            </h2>
            <ul class="space-y-2 text-blue-800">
                <li class="flex items-start">
                    <i class="bi bi-calendar-range me-2 mt-1 flex-shrink-0"></i>
                    <span>La temporada actual pot haver finalitzat el seu període de matriculació</span>
                </li>
                <li class="flex items-start">
                    <i class="bi bi-clock-history me-2 mt-1 flex-shrink-0"></i>
                    <span>La propera temporada encara no està disponible per a inscripció</span>
                </li>
                <li class="flex items-start">
                    <i class="bi bi-gear me-2 mt-1 flex-shrink-0"></i>
                    <span>Estem preparant els nous cursos i estaran disponibles aviat</span>
                </li>
            </ul>
        </div>
        
        <!-- Actions -->
        <div class="space-y-4">
            @if(auth()->check())
                <a href="{{ route('dashboard') }}" 
                   class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                    <i class="bi bi-house me-2"></i>Anar al Meu Campus
                </a>
                
                <div>
                    <a href="{{ route('campus.courses.index') }}" 
                       class="inline-block px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition font-semibold">
                        <i class="bi bi-list-ul me-2"></i>Veure Tots els Cursos (Admin)
                    </a>
                </div>
            @else
                <a href="{{ route('login') }}" 
                   class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sessió
                </a>
                
                <div>
                    <a href="{{ route('register') }}" 
                       class="inline-block px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold">
                        <i class="bi bi-person-plus me-2"></i>Crear Compte
                    </a>
                </div>
            @endif
        </div>
        
        <!-- Contact Information -->
        <div class="mt-12 p-6 bg-gray-50 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="bi bi-question-circle me-2"></i>Necessites més informació?
            </h3>
            <p class="text-gray-600 mb-4">
                Si tens dubtes sobre quan estaran disponibles els cursos o necessites informació específica, 
                no dubtis a contactar-nos.
            </p>
            <div class="flex flex-col sm:flex-row justify-center items-center space-y-2 sm:space-y-0 sm:space-x-6">
                <a href="mailto:info@campus.org" class="flex items-center text-blue-600 hover:text-blue-800">
                    <i class="bi bi-envelope me-2"></i>web@campus.org
                </a>
                {{-- <a href="tel:+34900123456" class="flex items-center text-blue-600 hover:text-blue-800">
                    <i class="bi bi-telephone me-2"></i>+34 900 123 456
                </a> --}}
            </div>
        </div>
        
        <!-- Newsletter Signup -->
        <div class="mt-12 p-6 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg text-white">
            <h3 class="text-xl font-semibold mb-3">
                <i class="bi bi-bell me-2"></i>Mantente Informado
            </h3>
            <p class="mb-4">
                Suscríbete a nuestra newsletter y recibe notificaciones cuando se abran las matriculaciones de nuevos cursos.
            </p>
            <form class="flex flex-col sm:flex-row justify-center items-center space-y-2 sm:space-y-0 sm:space-x-2">
                <input type="email" 
                       placeholder="Tu correo electrónico" 
                       class="px-4 py-2 rounded-md text-gray-900 w-full sm:w-64"
                       required>
                <button type="submit" 
                        class="px-6 py-2 bg-white text-blue-600 rounded-md hover:bg-gray-100 transition font-semibold">
                    Suscribirse
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
