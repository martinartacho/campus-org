@extends('catalog.layout')

@section('title', $course->title)

@php
    $cartItemsCount = $isInCart ? 1 : 0;
@endphp

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-8">
        <ol class="flex items-center space-x-2 text-sm text-gray-500">
            <li>
                <a href="{{ route('catalog.index') }}" class="hover:text-blue-600">
                    <i class="bi bi-house"></i> Catálogo
                </a>
            </li>
            <li class="flex items-center">
                <i class="bi bi-chevron-right mx-2"></i>
                <span class="text-gray-900">{{ $course->title }}</span>
            </li>
        </ol>
    </nav>

    <!-- Course Header -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-4">
                        <span class="bg-white text-blue-600 px-3 py-1 rounded-full font-semibold">
                            {{ $course->code }}
                        </span>
                        @if($course->level)
                            <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full font-semibold">
                                {{ \App\Models\CampusCourse::LEVELS[$course->level] ?? $course->level }}
                            </span>
                        @endif
                        @if($course->format)
                            <span class="bg-white text-blue-600 px-3 py-1 rounded-full font-semibold">
                                <i class="bi bi-{{ $course->format === 'online' ? 'camera-video' : ($course->format === 'hybrid' ? 'display' : 'geo-alt') }} me-1"></i>
                                {{ ucfirst($course->format) }}
                            </span>
                        @endif
                    </div>
                    
                    <h1 class="text-3xl font-bold text-white mb-4">{{ $course->title }}</h1>
                    
                    <div class="flex flex-wrap gap-4 text-white">
                        @if($course->hours)
                            <div class="flex items-center">
                                <i class="bi bi-clock me-2"></i>
                                <span>{{ $course->hours }} horas</span>
                            </div>
                        @endif
                        @if($course->sessions)
                            <div class="flex items-center">
                                <i class="bi bi-calendar-week me-2"></i>
                                <span>{{ $course->sessions }} sesiones</span>
                            </div>
                        @endif
                        @if($course->location)
                            <div class="flex items-center">
                                <i class="bi bi-geo-alt me-2"></i>
                                <span>{{ $course->location }}</span>
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="mt-6 md:mt-0 text-center md:text-right">
                    <div class="text-white mb-4">
                        @if($course->price > 0)
                            <div class="text-4xl font-bold">{{ number_format($course->price, 2) }} &euro;</div>
                            <div class="text-blue-100">Precio total</div>
                        @else
                            <div class="text-4xl font-bold">GRATIS</div>
                            <div class="text-blue-100">Sin coste</div>
                        @endif
                    </div>
                    
                    @if($isInCart)
                        <button onclick="removeFromCart({{ $course->id }})" 
                                class="px-6 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition font-semibold">
                            <i class="bi bi-cart-x me-2"></i>Eliminar del Carrito
                        </button>
                    @elseif(!$course->hasAvailableSpots())
                        <button disabled 
                                class="px-6 py-3 bg-gray-400 text-white rounded-lg cursor-not-allowed font-semibold">
                            <i class="bi bi-x-circle me-2"></i>No Hay Plazas
                        </button>
                    @else
                        <button onclick="addToCart({{ $course->id }})" 
                                class="px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition font-semibold">
                            <i class="bi bi-cart-plus me-2"></i>Añadir al Carrito
                        </button>
                    @endif
                    
                    <div class="mt-3">
                        <a href="{{ route('cart.index') }}" class="text-white hover:text-blue-200 underline text-sm">
                            <i class="bi bi-cart me-1"></i>Ver carrito
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Description -->
            @if($course->description)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">
                        <i class="bi bi-file-text me-2"></i>Descripción del Curso
                    </h2>
                    <div class="prose prose-lg max-w-none text-gray-700">
                        {!! $course->description !!}
                    </div>
                </div>
            @endif

            <!-- Schedule -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    <i class="bi bi-calendar3 me-2"></i>Calendario y Horario
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-semibold text-gray-800 mb-3">Fechas del Curso</h3>
                        <div class="space-y-2">
                            <div class="flex items-center text-gray-600">
                                <i class="bi bi-calendar-event me-2 text-blue-500"></i>
                                <span><strong>Inicio:</strong> {{ $course->start_date?->format('d/m/Y') ?? 'Por determinar' }}</span>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <i class="bi bi-calendar-event me-2 text-blue-500"></i>
                                <span><strong>Fin:</strong> {{ $course->end_date?->format('d/m/Y') ?? 'Por determinar' }}</span>
                            </div>
                            @if($course->season)
                                <div class="flex items-center text-gray-600">
                                    <i class="bi bi-calendar-range me-2 text-blue-500"></i>
                                    <span><strong>Temporada:</strong> {{ $course->season->name }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="font-semibold text-gray-800 mb-3">Información Adicional</h3>
                        <div class="space-y-2">
                            @if($course->hours)
                                <div class="flex items-center text-gray-600">
                                    <i class="bi bi-clock me-2 text-blue-500"></i>
                                    <span><strong>Duración:</strong> {{ $course->hours }} horas</span>
                                </div>
                            @endif
                            @if($course->sessions)
                                <div class="flex items-center text-gray-600">
                                    <i class="bi bi-calendar-week me-2 text-blue-500"></i>
                                    <span><strong>Sesiones:</strong> {{ $course->sessions }}</span>
                                </div>
                            @endif
                            @if($course->location)
                                <div class="flex items-center text-gray-600">
                                    <i class="bi bi-geo-alt me-2 text-blue-500"></i>
                                    <span><strong>Ubicación:</strong> {{ $course->location }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                @if($course->schedule && !empty($course->schedule))
                    <div class="mt-6">
                        <h3 class="font-semibold text-gray-800 mb-3">Horario Detallado</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            @foreach($course->schedule as $daySchedule)
                                <div class="flex items-center justify-between py-2 border-b border-gray-200 last:border-0">
                                    <span class="font-medium text-gray-700">{{ $daySchedule['day'] }}</span>
                                    <span class="text-gray-600">{{ $daySchedule['start'] }} - {{ $daySchedule['end'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Requirements -->
            @if($course->requirements)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">
                        <i class="bi bi-list-check me-2"></i>Requisitos Previos
                    </h2>
                    <div class="text-gray-700">
                        @if(is_array($course->requirements))
                            <ul class="list-disc list-inside space-y-2">
                                @foreach($course->requirements as $requirement)
                                    <li>{{ $requirement }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p>{{ $course->requirements }}</p>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Objectives -->
            @if($course->objectives)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">
                        <i class="bi bi-trophy me-2"></i>Objetivos del Curso
                    </h2>
                    <div class="text-gray-700">
                        @if(is_array($course->objectives))
                            <ul class="list-disc list-inside space-y-2">
                                @foreach($course->objectives as $objective)
                                    <li>{{ $objective }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p>{{ $course->objectives }}</p>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Teachers -->
            @if($course->teachers && $course->teachers->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">
                        <i class="bi bi-people me-2"></i>Profesorado
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($course->teachers as $teacher)
                            <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                                <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">
                                    {{ strtoupper(substr($teacher->first_name, 0, 1) . substr($teacher->last_name, 0, 1)) }}
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">
                                        {{ $teacher->first_name }} {{ $teacher->last_name }}
                                    </h4>
                                    <p class="text-sm text-gray-600">
                                        {{ $teacher->teacher_code }}
                                        @if($teacher->pivot->role)
                                            - {{ ucfirst($teacher->pivot->role) }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Availability Card -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="bi bi-info-circle me-2"></i>Información de Matriculación
                </h3>
                
                <div class="space-y-4">
                    <!-- Availability Status -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-600">Plazas disponibles:</span>
                            <span class="font-semibold {{ $course->available_spots > 5 ? 'text-green-600' : ($course->available_spots > 0 ? 'text-yellow-600' : 'text-red-600') }}">
                                @if($course->max_students)
                                    {{ $course->available_spots }} / {{ $course->max_students }}
                                @else
                                    Ilimitadas
                                @endif
                            </span>
                        </div>
                        @if($course->max_students)
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-{{ $course->available_spots > 5 ? 'green' : ($course->available_spots > 0 ? 'yellow' : 'red') }}-500 h-2 rounded-full" 
                                     style="width: {{ (($course->max_students - $course->available_spots) / $course->max_students) * 100 }}%"></div>
                            </div>
                        @endif
                    </div>

                    <!-- Registration Period -->
                    @if($course->season)
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-gray-600">Período de matriculación:</span>
                                @if($course->season->isRegistrationOpen())
                                    <span class="text-green-600 font-semibold">Abierto</span>
                                @else
                                    <span class="text-red-600 font-semibold">Cerrado</span>
                                @endif
                            </div>
                            @if($course->season->registration_start && $course->season->registration_end)
                                <div class="text-sm text-gray-500">
                                    {{ $course->season->registration_start->format('d/m/Y') }} - 
                                    {{ $course->season->registration_end->format('d/m/Y') }}
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Course Status -->
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Estado del curso:</span>
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-sm font-semibold">
                                {{ $course->getStatusLabel() }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category Info -->
            @if($course->category)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="bi bi-tag me-2"></i>Categoría
                    </h3>
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="bi bi-folder text-purple-600"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">{{ $course->category->name }}</h4>
                            <p class="text-sm text-gray-600">Categoría principal</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Related Courses -->
            @if($relatedCourses->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="bi bi-collection me-2"></i>Cursos Relacionados
                    </h3>
                    <div class="space-y-3">
                        @foreach($relatedCourses as $relatedCourse)
                            <div class="border-l-4 border-blue-500 pl-4">
                                <a href="{{ route('catalog.show', $relatedCourse) }}" 
                                   class="block hover:text-blue-600 transition">
                                    <h4 class="font-semibold text-gray-900">{{ $relatedCourse->title }}</h4>
                                    <div class="flex items-center space-x-3 text-sm text-gray-600 mt-1">
                                        <span>{{ $relatedCourse->code }}</span>
                                        @if($relatedCourse->price > 0)
                                            <span>{{ number_format($relatedCourse->price, 2) }} &euro;</span>
                                        @else
                                            <span>Gratis</span>
                                        @endif
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Help Section -->
            <div class="bg-blue-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-blue-900 mb-3">
                    <i class="bi bi-question-circle me-2"></i>¿Necesitas Ayuda?
                </h3>
                <p class="text-blue-800 text-sm mb-4">
                    Si tienes dudas sobre este curso o el proceso de matriculación, 
                    no dudes en contactarnos.
                </p>
                <div class="space-y-2">
                    <a href="mailto:{{ env('MAIL_ADDRESS_CONTACTE', 'info@campus.org') }}" class="flex items-center text-blue-600 hover:text-blue-800 text-sm">
                        <i class="bi bi-envelope me-2"></i>{{ env('MAIL_ADDRESS_CONTACTE', 'info@campus.org') }}
                    </a>
                    <a href="tel:+34900123456" class="flex items-center text-blue-600 hover:text-blue-800 text-sm">
                        <i class="bi bi-telephone me-2"></i>+34 900 123 456
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
