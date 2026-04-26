@extends('catalog.layout')

@section('title', 'Cistella Buida')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">
            <i class="bi bi-cart-x me-2"></i>
            Cistella Buida
        </h1>
        <p class="text-gray-600 text-lg">
            La teva cistella de compres està buida. Afegeix alguns cursos per començar!
        </p>
    </div>

    <!-- Empty Cart Illustration -->
    <div class="bg-gray-50 rounded-lg p-12 text-center mb-8">
        <div class="inline-flex items-center justify-center w-24 h-24 bg-gray-200 rounded-full mb-6">
            <i class="bi bi-cart text-gray-400 text-4xl"></i>
        </div>
        
        <h2 class="text-xl font-semibold text-gray-800 mb-4">
            No tens cursos a la teva cistella
        </h2>
        
        <p class="text-gray-600 mb-8 max-w-md mx-auto">
            Explora el nostre catàleg de cursos i afegeix els que t'interessen per començar el teu procés de matriculació.
        </p>
        
        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('catalog.index') }}" 
               class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                <i class="bi bi-search me-2"></i>
                Explorar Cursos
            </a>
            
            <a href="{{ route('catalog.index') }}" 
               class="inline-flex items-center justify-center px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold">
                <i class="bi bi-book me-2"></i>
                Veure Catàleg
            </a>
        </div>
    </div>

    <!-- Featured Courses -->
    @if($featuredCourses = \App\Models\CampusCourse::where('is_public', true)
            ->where('is_active', true)
            ->whereIn('status', ['planning', 'active', 'registration', 'in_progress'])
            ->with(['season', 'category'])
            ->limit(3)
            ->get())
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="bi bi-star me-2"></i>
            Cursos Destacados
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($featuredCourses as $course)
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                <h4 class="font-semibold text-gray-900 mb-2">{{ $course->title }}</h4>
                <p class="text-sm text-gray-600 mb-3">{{ Str::limit($course->description, 80) }}</p>
                
                <div class="flex justify-between items-center mb-3">
                    <span class="text-lg font-bold text-blue-600">
                        {{ number_format($course->price, 2) }} &euro;
                    </span>
                    <span class="text-xs text-gray-500">
                        {{ $course->hours }}h
                    </span>
                </div>
                
                <a href="{{ route('catalog.show', $course) }}" 
                   class="block w-full text-center bg-blue-50 text-blue-600 py-2 rounded hover:bg-blue-100 transition text-sm font-semibold">
                    Ver Detalles
                </a>
            </div>
            @endforeach
        </div>
        
        <div class="text-center mt-6">
            <a href="{{ route('catalog.index') }}" 
               class="inline-flex items-center text-blue-600 hover:text-blue-800 font-semibold">
                Ver todos los cursos
                <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
    @endif

    <!-- Help Section -->
    <div class="bg-blue-50 rounded-lg p-6 mt-8">
        <h3 class="text-lg font-semibold text-blue-900 mb-3">
            <i class="bi bi-question-circle me-2"></i>
            ¿Necesitas ayuda?
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="font-semibold text-blue-800 mb-2">¿Cómo añadir cursos?</h4>
                <p class="text-blue-700 text-sm">
                    Navega pel nostre catàleg, selecciona els cursos que t'interessen i fes clic a "Afegir a la Cistella".
                </p>
            </div>
            
            <div>
                <h4 class="font-semibold text-blue-800 mb-2">¿Proceso de matriculación?</h4>
                <p class="text-blue-700 text-sm">
                    Un cop a la cistella, pots procedir amb la matriculació i pagament de forma segura a través de Stripe.
                </p>
            </div>
        </div>
        
        <div class="text-center mt-6">
            <a href="mailto:info@campus.org" 
               class="inline-flex items-center text-blue-600 hover:text-blue-800 font-semibold">
                <i class="bi bi-envelope me-2"></i>
                Contactar soporte
            </a>
        </div>
    </div>
</div>
@endsection
