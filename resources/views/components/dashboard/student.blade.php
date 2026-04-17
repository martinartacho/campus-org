{{-- Dashboard Estudiant --}}
@props([
    'student' => null,
    'studentStats' => [],
    'studentCourses' => collect(),
    'recentActivity' => collect(),
    'upcomingClasses' => collect(),
    'grades' => collect(),
    'debug' => null,
    'error' => null,
])

<div class="space-y-6">

    {{-- ERROR --}}
    @if($error)
        <div class="bg-red-100 text-red-800 p-4 rounded">
            {{ $error }}
        </div>
        @return
    @endif

    {{-- PERFIL NO ENCONTRADO --}}
    @if(!$student)
        <div class="bg-yellow-100 text-yellow-800 p-4 rounded">
            {{ __('campus.student') }} {{ __('campus.no_records') }}
        </div>
        @return
    @endif
    {{-- HEADER --}}
    <div class="bg-white p-6 rounded shadow">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    {{ auth()->user()->name }}
                </h1>
                @if($student)
                <div class="flex items-center gap-4 mt-2 text-sm text-gray-600">
                    <span class="font-medium">{{ __('campus.code') }}: {{ $student->student_code }}</span>
                    @if($student->specialization)
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
                            {{ $student->specialization }} 
                        </span>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Estadístiques de l'Estudiant --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg mr-3">
                    <i class="bi bi-book text-blue-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">{{ __('campus.enrolled_courses') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $studentStats['total_courses'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg mr-3">
                    <i class="bi bi-check-circle text-green-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">{{ __('campus.active_courses') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $studentStats['active_courses'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg mr-3">
                    <i class="bi bi-clock text-yellow-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">{{ __('campus.pending_courses') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $studentStats['pending_courses'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg mr-3">
                    <i class="bi bi-trophy text-purple-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">{{ __('campus.completed_courses') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $studentStats['completed_courses'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Widget de Documents --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <x-dashboard.widgets.student_documents />
        
        {{-- Aquí se pueden añadir más widgets en el futuro --}}
    </div>

    {{-- Llista de Cursos Matriculats --}}
    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('campus.my_courses') }}</h2>
        </div>
        <div class="p-6">
            @if(isset($studentCourses) && $studentCourses->get()->count() > 0)
                {{-- DEBUG: Solo mostrar en desarrollo --}}
                @if(config('app.debug'))
                    @php
                        $coursesCollection = $studentCourses->get();
                    @endphp
                    
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                        <h4 class="font-semibold text-yellow-800 mb-2">DEBUG: studentCourses</h4>
                        <p class="text-sm text-yellow-700 mt-2">
                            <strong>Type:</strong> {{ get_class($studentCourses) }}<br>
                            <strong>Count:</strong> {{ $studentCourses->get()->count() }}<br>
                            <strong>First item type:</strong> {{ get_class($studentCourses->get()->first() ?? 'null') }}<br>
                            <strong>SQL Query:</strong> {{ $studentCourses->toSql() }}
                        </p>
                        @php
                            $coursesCollection = $studentCourses->get();
                            if($coursesCollection->count() > 0) {
                                echo '<pre class="text-xs bg-white p-2 rounded border overflow-auto max-h-40">' . json_encode($coursesCollection->first()->toArray(), JSON_PRETTY_PRINT) . '</pre>';
                            } else {
                                echo '<p class="text-sm text-red-600">No courses found</p>';
                            }
                        @endphp
                    </div>
                @endif
                
                @php
                    $coursesCollection = $studentCourses->get();
                @endphp
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($coursesCollection as $course)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between mb-3">
                                <h3 class="font-semibold text-gray-900">{{ $course->title }}</h3>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                    {{ ucfirst($course->academic_status) }}
                                </span>
                            </div>
                            
                            <div class="space-y-2 text-sm text-gray-600">
                                <div class="flex items-center">
                                    <i class="bi bi-tag mr-2"></i>
                                    <span>{{ $course->code }}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="bi bi-calendar-range mr-2"></i>
                                    <span>{{ $course->sessions }} sessions</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="bi bi-bar-chart mr-2"></i>
                                    <span>{{ ucfirst($course->level) }}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="bi bi-laptop mr-2"></i>
                                    <span>{{ $course->format }}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="bi bi-people mr-2"></i>
                                    <span>{{ $course->max_students }} places</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="bi bi-book text-gray-400 text-4xl mb-3"></i>
                    <p class="text-gray-600">{{ __('campus.no_active_registrations') }}</p>
                    <a href="{{ route('campus.courses.index') }}" 
                       class="mt-3 inline-flex items-center text-blue-600 hover:text-blue-800">
                        <i class="bi bi-search mr-2"></i>
                        {{ __('campus.explore_courses') }}
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Actividad Reciente --}}
    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('campus.recent_activity') }}</h2>
        </div>
        <div class="p-6">
            @if(isset($recentActivity) && $recentActivity->count() > 0)
                <div class="space-y-3">
                    @foreach($recentActivity as $activity)
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-{{ $activity['color'] ?? 'gray' }}-100 rounded-full flex items-center justify-center">
                                    <i class="bi {{ $activity['icon'] ?? 'bi-circle' }} text-{{ $activity['color'] ?? 'gray' }}-600"></i>
                                </div>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900">{{ $activity['title'] }}</p>
                                <p class="text-xs text-gray-500">{{ $activity['date']->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <p class="text-gray-600">{{ __('campus.no_recent_activity') }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Próximas Clases - PENDENT --}}
    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('campus.upcoming_classes') }}</h2>
        </div>
        <div class="p-6">
            <div class="text-center py-4">
                <p class="text-gray-600">--</p>
                <p class="text-sm text-gray-500 mt-1">{{ __('campus.functionality_pending') }}</p>
            </div>
        </div>
    </div>

    {{-- Accions Ràpides --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-lg shadow border border-gray-200 opacity-60">
            <div class="flex items-center">
                <div class="p-2 bg-gray-100 rounded-lg mr-3">
                    <i class="bi bi-search text-gray-400 text-xl"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-500">{{ __('campus.coming_soon') }}</p>
                    <p class="text-sm text-gray-400">{{ __('campus.functionality_pending') }}</p>
                </div>
            </div>
        </div>
        
        <a href="{{ route('campus.student.profile') }}" 
           class="bg-white p-4 rounded-lg shadow border border-gray-200 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg mr-3">
                    <i class="bi bi-person text-green-600 text-xl"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900">{{ __('campus.profile') }}</p>
                    <p class="text-sm text-gray-600">{{ __('campus.update_your_data') }}</p>
                </div>
            </div>
        </a>
        
        <a href="{{ route('campus.student.registrations') }}" 
           class="bg-white p-4 rounded-lg shadow border border-gray-200 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg mr-3">
                    <i class="bi bi-file-earmark-text text-purple-600 text-xl"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900">{{ __('campus.registrations') }}</p>
                    <p class="text-sm text-gray-600">{{ __('campus.view_your_history') }}</p>
                </div>
            </div>
        </a>

       <!--  <a href="{{ route('campus.student.history') }}" 
         {{-- TODO: No és necesari, mateix que registrations --}}
           class="bg-white p-4 rounded-lg shadow border border-gray-200 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg mr-3">
                    <i class="bi bi-file-earmark-text text-purple-600 text-xl"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900">{{ __('Històric') }}</p>
                    <p class="text-sm text-gray-600">{{ __('Veure el teu historial') }}</p>
                </div>
            </div>
        </a> -->
    </div>

    {{-- Debug/Error Info --}}
    @if(config('app.debug') && ($debug || $error))
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            @if($error)
                <div class="text-red-800">
                    <strong>Error:</strong> {{ $error }}
                </div>
            @endif
            @if($debug)
                <div class="text-yellow-800 text-sm mt-2">
                    <strong>Debug:</strong> 
                     <pre class="text-xs">{{ json_encode($debug, JSON_PRETTY_PRINT) }}</pre>
                </div>
            @endif
        </div>
    @endif

</div>