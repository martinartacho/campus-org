@extends('catalog.layout')

@section('title', 'Catàleg de Cursos')

@php
    $cartItemsCount = count($cartItems ?? []);
@endphp

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">
            Catàleg de Cursos
        </h1>
        <p class="text-xl text-gray-600 max-w-3xl mx-auto">
            Descubre nuestros cursos de la temporada {{ $season->name ?? 'Actual' }} 
            y comienza tu viaje de aprendizaje hoy mismo.
        </p>
        
        @if($season)
            <div class="mt-6 inline-flex items-center px-4 py-2 bg-blue-100 text-blue-800 rounded-full">
                <i class="bi bi-calendar3 me-2"></i>
                Temporada: {{ $season->name }}
                @if($season->isRegistrationOpen())
                    <span class="ml-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full">
                        Matriculación Abierta
                    </span>
                @endif
            </div>
        @endif
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="bi bi-funnel me-2"></i>Filtrar Cursos
        </h2>
        
        <form method="GET" action="{{ route('catalog.index') }}" class="space-y-4">
            <!-- Search -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Buscar por título, código..."
                               class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="bi bi-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                    <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Todas las categorías</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Level -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nivel</label>
                    <select name="level" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Todos los niveles</option>
                        @foreach($levels as $level)
                            <option value="{{ $level }}" {{ request('level') == $level ? 'selected' : '' }}>
                                {{ \App\Models\CampusCourse::LEVELS[$level] ?? $level }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Format -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Formato</label>
                    <select name="format" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Todos los formatos</option>
                        @foreach($formats as $format)
                            <option value="{{ $format }}" {{ request('format') == $format ? 'selected' : '' }}>
                                {{ ucfirst($format) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Price and Hours Range -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Precio Mínimo</label>
                    <input type="number" 
                           name="price_min" 
                           value="{{ request('price_min') }}"
                           placeholder="0"
                           step="0.01"
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Precio Máximo</label>
                    <input type="number" 
                           name="price_max" 
                           value="{{ request('price_max') }}"
                           placeholder="500"
                           step="0.01"
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Horas Mínimas</label>
                    <input type="number" 
                           name="hours_min" 
                           value="{{ request('hours_min') }}"
                           placeholder="0"
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Horas Máximas</label>
                    <input type="number" 
                           name="hours_max" 
                           value="{{ request('hours_max') }}"
                           placeholder="200"
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- Sort and Actions -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                <div class="flex items-center space-x-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ordenar por</label>
                        <select name="sort" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="start_date" {{ request('sort') == 'start_date' ? 'selected' : '' }}>Fecha de inicio</option>
                            <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Título</option>
                            <option value="price" {{ request('sort') == 'price' ? 'selected' : '' }}>Precio</option>
                            <option value="hours" {{ request('sort') == 'hours' ? 'selected' : '' }}>Duración</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">&nbsp;</label>
                        <select name="order" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="asc" {{ request('order') == 'asc' ? 'selected' : '' }}>Ascendente</option>
                            <option value="desc" {{ request('order') == 'desc' ? 'selected' : '' }}>Descendente</option>
                        </select>
                    </div>
                </div>

                <div class="flex space-x-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                        <i class="bi bi-search me-2"></i>Aplicar Filtros
                    </button>
                    <a href="{{ route('catalog.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                        <i class="bi bi-x-circle me-2"></i>Limpiar
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Results Count -->
    <div class="mb-6">
        <p class="text-gray-600">
            @if($courses->total() > 0)
                Mostrando {{ $courses->firstItem() }}-{{ $courses->lastItem() }} de {{ $courses->total() }} cursos
            @else
                No se encontraron cursos con los filtros seleccionados
            @endif
        </p>
    </div>

    <!-- Courses Grid -->
    @if($courses->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach($courses as $course)
                <div class="course-card bg-white rounded-lg shadow-md overflow-hidden">
                    <!-- Course Header -->
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-4">
                        <div class="flex justify-between items-start mb-2">
                            <span class="bg-white text-blue-600 text-xs px-2 py-1 rounded-full font-semibold">
                                {{ $course->code }}
                            </span>
                            @if($course->price > 0)
                                <span class="bg-green-500 text-white text-xs px-2 py-1 rounded-full font-semibold">
                                    {{ number_format($course->price, 2) }} &euro;
                                </span>
                            @else
                                <span class="bg-gray-500 text-white text-xs px-2 py-1 rounded-full font-semibold">
                                    Gratis
                                </span>
                            @endif
                        </div>
                        <h3 class="text-white font-bold text-lg mb-1 line-clamp-2">
                            {{ $course->title }}
                        </h3>
                    </div>

                    <!-- Course Body -->
                    <div class="p-4">
                        <!-- Course Meta -->
                        <div class="flex flex-wrap gap-2 mb-3">
                            @if($course->level)
                                <span class="badge-level bg-purple-100 text-purple-800 rounded-full">
                                    {{ \App\Models\CampusCourse::LEVELS[$course->level] ?? $course->level }}
                                </span>
                            @endif
                            @if($course->format)
                                <span class="badge-level bg-gray-100 text-gray-800 rounded-full">
                                    <i class="bi bi-{{ $course->format === 'online' ? 'camera-video' : ($course->format === 'hybrid' ? 'display' : 'geo-alt') }} me-1"></i>
                                    {{ ucfirst($course->format) }}
                                </span>
                            @endif
                            @if($course->hours)
                                <span class="badge-level bg-blue-100 text-blue-800 rounded-full">
                                    <i class="bi bi-clock me-1"></i>{{ $course->hours }}h
                                </span>
                            @endif
                        </div>

                        <!-- Description -->
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                            {{ Str::limit(strip_tags($course->description), 100) }}
                        </p>

                        <!-- Schedule Info -->
                        @if($course->start_date)
                            <div class="text-sm text-gray-500 mb-4">
                                <i class="bi bi-calendar3 me-1"></i>
                                {{ $course->start_date->format('d/m/Y') }}
                                @if($course->end_date && $course->end_date->format('Y-m-d') !== $course->start_date->format('Y-m-d'))
                                    - {{ $course->end_date->format('d/m/Y') }}
                                @endif
                            </div>
                        @endif

                        <!-- Availability -->
                        <div class="mb-4">
                            @if($course->max_students)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">Plazas disponibles:</span>
                                    <span class="font-semibold {{ $course->available_spots > 5 ? 'text-green-600' : ($course->available_spots > 0 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ $course->available_spots }} / {{ $course->max_students }}
                                    </span>
                                </div>
                                <!-- Progress bar -->
                                <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                    <div class="bg-{{ $course->available_spots > 5 ? 'green' : ($course->available_spots > 0 ? 'yellow' : 'red') }}-500 h-2 rounded-full" 
                                         style="width: {{ (($course->max_students - $course->available_spots) / $course->max_students) * 100 }}%"></div>
                                </div>
                            @else
                                <span class="text-sm text-green-600">
                                    <i class="bi bi-infinity me-1"></i>Plazas ilimitadas
                                </span>
                            @endif
                        </div>

                        <!-- Actions -->
                        <div class="flex space-x-2">
                            <a href="{{ route('catalog.show', $course) }}" 
                               class="flex-1 text-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                                <i class="bi bi-eye me-1"></i>Ver Detalles
                            </a>
                            
                            @if(in_array($course->id, $cartItems))
                                <button onclick="removeFromCart({{ $course->id }})" 
                                        class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition">
                                    <i class="bi bi-cart-x"></i>
                                </button>
                            @elseif(!$course->hasAvailableSpots())
                                <button disabled 
                                        class="px-4 py-2 bg-gray-300 text-gray-500 rounded-md cursor-not-allowed">
                                    <i class="bi bi-x-circle"></i> No Disponible
                                </button>
                            @else
                                <button onclick="addToCart({{ $course->id }})" 
                                        class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition">
                                    <i class="bi bi-cart-plus"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="flex justify-center">
            {{ $courses->links() }}
        </div>
    @else
        <!-- No Results -->
        <div class="text-center py-12">
            <i class="bi bi-search text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">No se encontraron cursos</h3>
            <p class="text-gray-500 mb-6">
                Intenta ajustar los filtros o <a href="{{ route('catalog.index') }}" class="text-blue-600 hover:underline">ver todos los cursos</a>
            </p>
        </div>
    @endif
</div>
@endsection
