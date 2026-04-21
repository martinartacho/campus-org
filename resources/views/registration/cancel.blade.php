@extends('catalog.layout')

@section('title', 'Pago Cancelado')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Cancel Header -->
    <div class="text-center mb-8">
        <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="bi bi-exclamation-triangle-fill text-yellow-600 text-4xl"></i>
        </div>
        
        <h1 class="text-4xl font-bold text-gray-900 mb-4">
            Pago Cancelado
        </h1>
        
        <p class="text-xl text-gray-600">
            Has cancelado el proceso de pago. Tu matriculación no ha sido completada.
        </p>
    </div>

    <!-- Information -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">
            <i class="bi bi-info-circle me-2"></i>¿Qué ha pasado?
        </h2>
        
        <div class="space-y-4 text-gray-700">
            <p>
                Has cancelado el proceso de pago en Stripe. Esto significa que:
            </p>
            
            <ul class="space-y-2 list-disc list-inside ml-4">
                <li>No se ha realizado ningún cargo en tu tarjeta</li>
                <li>Tu matriculación no ha sido confirmada</li>
                <li>Los cursos siguen disponibles en tu carrito</li>
                <li>Las plazas no han sido reservadas</li>
            </ul>
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-4">
                <h3 class="font-semibold text-blue-900 mb-2">
                    <i class="bi bi-lightbulb me-2"></i>Buenas noticias
                </h3>
                <p class="text-blue-800">
                    ¡No te preocupes! Los cursos que tenías en tu carrito siguen disponibles. 
                    Puedes volver a intentar el proceso de matriculación cuando quieras.
                </p>
            </div>
        </div>
    </div>

    <!-- Options -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Try Again -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-arrow-clockwise text-green-600 text-2xl"></i>
                </div>
                
                <h3 class="text-xl font-bold text-gray-900 mb-2">
                    Intentar de Nuevo
                </h3>
                
                <p class="text-gray-600 mb-4">
                    Si fue un error o cambias de opinión, puedes volver a completar tu matriculación.
                </p>
                
                <a href="{{ route('registration.create') }}" 
                   class="inline-block px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold">
                    <i class="bi bi-arrow-clockwise me-2"></i>Reintentar Matriculación
                </a>
            </div>
        </div>
        
        <!-- Review Cart -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-cart text-blue-600 text-2xl"></i>
                </div>
                
                <h3 class="text-xl font-bold text-gray-900 mb-2">
                    Revisar Carrito
                </h3>
                
                <p class="text-gray-600 mb-4">
                    Quizás quieres modificar los cursos seleccionados o añadir/eliminar algún curso.
                </p>
                
                <a href="{{ route('cart.index') }}" 
                   class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                    <i class="bi bi-cart me-2"></i>Ver Mi Carrito
                </a>
            </div>
        </div>
    </div>

    <!-- Help Section -->
    <div class="bg-yellow-50 rounded-lg p-6 mb-8">
        <h2 class="text-xl font-bold text-yellow-900 mb-4">
            <i class="bi bi-question-circle me-2"></i>¿Necesitas Ayuda?
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="font-semibold text-yellow-800 mb-2">Problemas comunes</h3>
                <ul class="space-y-2 text-yellow-700 text-sm">
                    <li class="flex items-start">
                        <i class="bi bi-info-circle me-2 mt-0.5"></i>
                        <span><strong>Tarjeta rechazada:</strong> Verifica los datos o contacta con tu banco</span>
                    </li>
                    <li class="flex items-start">
                        <i class="bi bi-info-circle me-2 mt-0.5"></i>
                        <span><strong>Error de conexión:</strong> Revisa tu internet e intenta de nuevo</span>
                    </li>
                    <li class="flex items-start">
                        <i class="bi bi-info-circle me-2 mt-0.5"></i>
                        <span><strong>Dudas sobre el curso:</strong> Revisa la información del curso</span>
                    </li>
                </ul>
            </div>
            
            <div>
                <h3 class="font-semibold text-yellow-800 mb-2">Contacto Directo</h3>
                <p class="text-yellow-700 text-sm mb-3">
                    Si sigues teniendo problemas, estamos aquí para ayudarte:
                </p>
                <div class="space-y-2">
                    <a href="mailto:soporte@campus.org" class="flex items-center text-yellow-600 hover:text-yellow-800 text-sm">
                        <i class="bi bi-envelope me-2"></i>soporte@campus.org
                    </a>
                    <a href="tel:+34900123456" class="flex items-center text-yellow-600 hover:text-yellow-800 text-sm">
                        <i class="bi bi-telephone me-2"></i>+34 900 123 456
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alternative Options -->
    <div class="bg-gray-50 rounded-lg p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">
            <i class="bi bi-grid-3x3-gap me-2"></i>Otras Opciones
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('catalog.index') }}" 
               class="block p-4 bg-white rounded-lg hover:shadow-md transition text-center">
                <i class="bi bi-search text-blue-600 text-2xl mb-2"></i>
                <h3 class="font-semibold text-gray-900 mb-1">Explorar Cursos</h3>
                <p class="text-gray-600 text-sm">Descubre más cursos disponibles</p>
            </a>
            
            @if(auth()->check())
                <a href="{{ route('dashboard') }}" 
                   class="block p-4 bg-white rounded-lg hover:shadow-md transition text-center">
                    <i class="bi bi-house text-green-600 text-2xl mb-2"></i>
                    <h3 class="font-semibold text-gray-900 mb-1">Mi Campus</h3>
                    <p class="text-gray-600 text-sm">Gestiona tus matrículas</p>
                </a>
            @else
                <a href="{{ route('login') }}" 
                   class="block p-4 bg-white rounded-lg hover:shadow-md transition text-center">
                    <i class="bi bi-box-arrow-in-right text-green-600 text-2xl mb-2"></i>
                    <h3 class="font-semibold text-gray-900 mb-1">Iniciar Sesión</h3>
                    <p class="text-gray-600 text-sm">Accede a tu cuenta</p>
                </a>
            @endif
            
            <a href="mailto:info@campus.org" 
               class="block p-4 bg-white rounded-lg hover:shadow-md transition text-center">
                <i class="bi bi-envelope text-purple-600 text-2xl mb-2"></i>
                <h3 class="font-semibold text-gray-900 mb-1">Contactar</h3>
                <p class="text-gray-600 text-sm">Envíanos tus dudas</p>
            </a>
        </div>
    </div>
</div>
@endsection
