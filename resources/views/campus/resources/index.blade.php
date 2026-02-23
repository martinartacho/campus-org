@extends('campus.shared.layout')

@section('title', 'Re-Cursos - Gestió de Recursos')

@section('content')
<div class="container mx-auto px-4 py-8">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Re-Cursos</h1>
        <div class="flex gap-4">
            <a href="{{ route('campus.resources.calendar') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                <i class="fas fa-calendar-alt mr-2"></i>Calendari
            </a>
        </div>
    </div>

    {{-- Gestió de Recursos --}}
    @if(auth()->user()->can('campus.seasons.view') || auth()->user()->can('campus.spaces.view') || 
        auth()->user()->can('campus.time_slots.view') || auth()->user()->can('campus.teachers.view') ||
        auth()->user()->can('campus.courses.view'))
    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
            <i class="bi bi-grid-3x3-gap me-2"></i>
            Gestió de Recursos
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            
            {{-- Temporada --}}
            @can('campus.seasons.view')
            <a href="{{ route('campus.seasons.index') }}" 
               class="bg-gray-50 hover:bg-gray-100 p-4 rounded-lg border border-gray-200 transition duration-150">
                <div class="flex items-center">
                    <div class="p-2 bg-orange-100 rounded-lg me-3">
                        <i class="bi bi-calendar-range text-orange-600 text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Temporada</h3>
                        <p class="text-sm text-gray-600">Temporada activa o actual</p>
                    </div>
                </div>
            </a>
            @endcan
            
            {{-- Espais --}}
            <a href="{{ route('campus.resources.spaces') }}" 
               class="bg-gray-50 hover:bg-gray-100 p-4 rounded-lg border border-gray-200 transition duration-150">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg me-3">
                        <i class="bi bi-building text-green-600 text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Espais</h3>
                        <p class="text-sm text-gray-600">Gestió d'espais físics</p>
                    </div>
                </div>
            </a>
            
            {{-- Horaris / Franjes horàries --}}
            <a href="{{ route('campus.resources.timeslots') }}" 
               class="bg-gray-50 hover:bg-gray-100 p-4 rounded-lg border border-gray-200 transition duration-150">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg me-3">
                        <i class="bi bi-clock text-purple-600 text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Horaris</h3>
                        <p class="text-sm text-gray-600">Franjes horàries</p>
                    </div>
                </div>
            </a>
            
            {{-- Professorat --}}
            @can('campus.teachers.view')
            <a href="{{ route('campus.teachers.index') }}" 
               class="bg-gray-50 hover:bg-gray-100 p-4 rounded-lg border border-gray-200 transition duration-150">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg me-3">
                        <i class="bi bi-people text-blue-600 text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Professorat</h3>
                        <p class="text-sm text-gray-600">Gestió de professors</p>
                    </div>
                </div>
            </a>
            @endcan
            
            {{-- Cursos --}}
            @can('campus.courses.view')
            <a href="{{ route('campus.courses.index') }}" 
               class="bg-gray-50 hover:bg-gray-100 p-4 rounded-lg border border-gray-200 transition duration-150">
                <div class="flex items-center">
                    <div class="p-2 bg-indigo-100 rounded-lg me-3">
                        <i class="bi bi-book text-indigo-600 text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Cursos</h3>
                        <p class="text-sm text-gray-600">Gestió de cursos</p>
                    </div>
                </div>
            </a>
            @endcan
            
        </div>

        
    </div>
    @endif

    {{-- SECCIÓ 3: ACCIONS RÀPIDES --}}
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Accions Ràpides</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- AGENDA --}}
            <a href="{{ route('campus.resources.calendar') }}" class="block">
                <div class="bg-blue-50 border border-blue-200 hover:bg-blue-100 p-4 rounded-lg transition-colors">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg me-3">
                            <i class="bi bi-calendar-week text-blue-600 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-blue-800">Agenda</h3>
                            <p class="text-sm text-blue-600">Veure agenda d'activitats</p>
                        </div>
                    </div>
                </div>
            </a>
            
            {{-- Franjes Horàries --}}
            <a href="{{ route('campus.resources.timeslots') }}" class="block">
                <div class="bg-orange-50 border border-orange-200 hover:bg-orange-100 p-4 rounded-lg transition-colors">
                    <div class="flex items-center">
                        <div class="p-2 bg-orange-100 rounded-lg me-3">
                            <i class="bi bi-calendar3 text-orange-600 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-orange-800">Calendar</h3>
                            <p class="text-sm text-orange-600">Gestionar calendar de recursos</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Estadístiques Ràpides --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Espais totals</p>
                    <p class="text-2xl font-bold text-gray-800">{{ App\Models\CampusSpace::count() }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-lg">
                    <i class="bi bi-building text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Franjes horàries</p>
                    <p class="text-2xl font-bold text-gray-800">{{ App\Models\CampusTimeSlot::count() }}</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-lg">
                    <i class="bi bi-clock text-purple-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Assignacions</p>
                    <p class="text-2xl font-bold text-gray-800">{{ App\Models\CampusCourseSchedule::count() }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i class="bi bi-calendar-check text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Cursos</p>
                    <p class="text-2xl font-bold text-gray-800">{{ App\Models\CampusCourse::count() }}</p>
                </div>
                <div class="p-3 bg-indigo-100 rounded-lg">
                    <i class="bi bi-book text-indigo-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Professorat</p>
                    <p class="text-2xl font-bold text-gray-800">{{ App\Models\CampusTeacher::count() }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i class="bi bi-people text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Conflictes</p>
                    <p class="text-2xl font-bold text-red-600">{{ App\Models\CampusCourseSchedule::where('status', 'conflict')->count() }}</p>
                </div>
                <div class="p-3 bg-red-100 rounded-lg">
                    <i class="bi bi-exclamation-triangle text-red-600"></i>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
