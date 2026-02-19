{{-- resources/views/components/dashboard/teacher.blade.php --}}

@props([
    'teacher' => null,
    'season' => null,
    'seasons' => collect(),
    'teacherCourses' => collect(),
    'allCourses' => collect(),
    'stats' => [],
    'consentments' => collect(),
    'currentSeason' => null,
    'debug' => null,
    'error' => null,
])

<div class="space-y-6">



    {{-- CARDS SUPERIORES --}}
    @include('components.dashboard-teacher-cards')

    {{-- DEBUG --}}
   {{--  @if(config('app.debug'))
        <pre class="bg-gray-100 p-3 text-xs rounded border">{{ var_export([
            'teacher' => optional($teacher)->teacher_code,
            'courses' => $teacherCourses->count(),
            'stats' => $stats,
        ], true) }}
        </pre>
    @endif --}}

    {{-- ERROR GLOBAL --}}
    @if($error)
        <div class="bg-red-100 text-red-800 p-4 rounded">
            {{ $error }}
        </div>
        @return
    @endif

    {{-- PERFIL NO ENCONTRADO --}}
    @if(!$teacher)
        <div class="bg-yellow-100 text-yellow-800 p-4 rounded">
            @lang('campus.teacher') @lang('campus.no_records')
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
                @if($teacher)
                <div class="flex items-center gap-4 mt-2 text-sm text-gray-600">
                    <span class="font-medium">@lang('campus.code'): {{ $teacher->teacher_code }}</span>
                    @if($teacher->specialization)
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
                            {{ $teacher->specialization }} 
                        </span>
                    @endif
                </div>
                @endif
            </div>
            
            @if($season)
                <div class="text-right">
                    <div class="text-sm text-gray-500">@lang('campus.current') @lang('campus.season'):</div>
                    <div class="font-semibold text-gray-700">{{ $season->name }}</div>
                    <div class="text-xs text-gray-500">
                        {{ $season->season_start?->format('d/m/Y') }} - {{ $season->season_end?->format('d/m/Y') }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- STATS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded shadow text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $stats['total_courses'] ?? 0 }}</div>
            <div class="text-xs text-gray-500">@lang('campus.total_courses_teacher')</div>
        </div>

        <div class="bg-white p-4 rounded shadow text-center">
            <div class="text-2xl font-bold text-green-600">{{ $stats['total_students'] ?? 0 }}</div>
            <div class="text-xs text-gray-500">@lang('campus.total_students_teacher')</div>
        </div>

        <div class="bg-white p-4 rounded shadow text-center">
            <div class="text-2xl font-bold text-purple-600">{{ $stats['today_classes'] ?? 0 }}</div>
            <div class="text-xs text-gray-500">@lang('campus.today_classes_teacher')</div>
        </div>

        <div class="bg-white p-4 rounded shadow text-center">
            <div class="text-2xl font-bold text-orange-600">{{ $stats['upcoming_registrations'] ?? 0 }}</div>
            <div class="text-xs text-gray-500">@lang('campus.pending_registrations_teacher')</div>
        </div>

        {{-- <div class="bg-white p-4 rounded shadow text-center">
            <div class="text-2xl font-bold text-purple-600">{{ $stats['completed_consents'] ?? 0 }}</div>
            <div class="text-xs text-gray-500">Consentiments completats</div>
        </div> --}}
    </div>

    {{-- CURSOS --}}
    <div class="bg-white p-6 rounded shadow">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">@lang('campus.my_courses')</h2>
            <div class="text-sm text-gray-500">
                @lang('campus.active'): {{ $stats['active_courses'] ?? 0 }} | 
                @lang('campus.completed'): {{ $stats['completed_courses'] ?? 0 }}
            </div>
        </div>

        @if($teacherCourses->isEmpty())
            <div class="text-center py-12">
                <p class="mt-4 text-gray-500">
                    @lang('campus.no_courses')
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @foreach($teacherCourses as $course)
                    @php
                        // Status del curso
                        $statusColors = [
                            'active' => ['bg' => 'bg-green-100', 'text' => 'text-green-800'],
                            'upcoming' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800'],
                            'completed' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800']
                        ];
                        $statusColor = $statusColors[$course->status] ?? $statusColors['upcoming'];
                        
                        // Información del profesor actual en este curso
                        $myRole = trans('campus.teacher_role.' . ($course->pivot->role ?? 'assistant'));
                        $myHours = $course->pivot->hours_assigned ?? 0;
                        
                        // Horario de hoy
                        $hasClassToday = !empty($course->schedule_info['today_classes']);
                        $currentClass = $hasClassToday ? 
                            collect($course->schedule_info['today_classes'])->firstWhere('is_current', true) : null;
                    @endphp
                    
                    <div class="border rounded-lg p-5 hover:shadow-lg transition-shadow duration-200 bg-white">
                        {{-- Header del curso --}}
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1">
                                <div class="flex items-start gap-2">
                                    <div class="flex-1">
                                        <h3 class="font-bold text-lg text-gray-800 leading-tight">
                                            {{ $course->title }}
                                        </h3>
                                        <div class="text-sm text-gray-500 mt-1">
                                            @lang('campus.course_code'): <span class="font-medium">{{ $course->code }}</span>
                                            @if($course->category)
                                                <span class="ml-3">• {{ $course->category->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusColor['bg'] }} {{ $statusColor['text'] }}">
                                        @lang('campus.course_status_'.$course->status)
                                    </span>
                                </div>
                                
                                {{-- Badge si hay clase ahora --}}
                                @if($currentClass)
                                    <div class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 animate-pulse">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                        @lang('campus.class_today') {{ $currentClass['start'] }} - {{ $currentClass['end'] }}
                                    </div>
                                @elseif($hasClassToday)
                                    <div class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                        @lang('campus.class_today')
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Información del curso --}}
                        <div class="space-y-3 mb-4">
                            {{-- Mi rol y horas --}}
                            <div class="flex items-center justify-between bg-gray-50 p-3 rounded">
                                <div>
                                    <div class="text-sm font-medium text-gray-700">@lang('campus.my_role')</div>
                                    <div class="text-lg font-semibold text-blue-600">{{ $myRole }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-gray-700">@lang('campus.course_hours_assigned')</div>
                                    <div class="text-lg font-semibold text-gray-800">{{ $myHours }}h</div>
                                </div>
                            </div>

                            {{-- Horario --}}
                            @if(!empty($course->schedule_info) && $course->schedule_info['has_schedule'])
                                <div class="border-l-4 border-blue-500 bg-blue-50 p-3 rounded-r">
                                    <div class="flex items-center text-sm text-blue-700">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="font-medium">@lang('campus.course_schedule'):</span>
                                    </div>
                                    <div class="mt-1 text-sm text-blue-600">{{ $course->schedule_info['formatted'] }}</div>
                                </div>
                            @endif

                            {{-- Otros profesores --}}
                            @if(!empty($course->other_teachers) && $course->other_teachers->isNotEmpty())
                                <div class="border-t pt-3">
                                    <div class="text-sm font-medium text-gray-700 mb-2">@lang('campus.other_teachers')</div>
                                    <div class="space-y-2">
                                        @foreach($course->other_teachers as $otherTeacher)
                                            <div class="flex items-center justify-between text-sm">
                                                <div class="flex items-center">
                                                    <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center text-xs font-medium text-gray-600 mr-2">
                                                        {{ substr($otherTeacher->first_name, 0, 1) }}{{ substr($otherTeacher->last_name, 0, 1) }}
                                                    </div>
                                                    <span>{{ $otherTeacher->first_name }} {{ $otherTeacher->last_name }}</span>
                                                </div>
                                                <div class="text-gray-600">
                                                    <span class="font-medium">{{ trans('campus.teacher_role.' . ($otherTeacher->pivot->role ?? 'assistant')) }}</span>
                                                    <span class="ml-2">• {{ $otherTeacher->pivot->hours_assigned ?? 0 }}h</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="text-sm text-gray-500 italic">@lang('campus.no_other_teachers')</div>
                            @endif

                            {{-- Estudiantes --}}
                            <div class="flex items-center justify-between bg-green-50 p-3 rounded border border-green-100">
                                <div>
                                    <div class="text-sm font-medium text-gray-700">@lang('campus.enrolled_students')</div>
                                    <div class="text-2xl font-bold text-green-600">
                                        {{ $course->confirmed_students_count }}
                                    </div>
                                </div>
                                <div class="text-right">
                                    @if($course->max_students)
                                        <div class="text-sm text-gray-600">
                                            @lang('campus.available_spots', [
                                                'available' => $course->available_spots,
                                                'total' => $course->max_students
                                            ])
                                        </div>
                                    @endif
                                    <div class="mt-2">
                                        <a href="{{ route('campus.teacher.courses.students', $course->id) }}" 
                                           class="inline-flex items-center text-sm font-medium text-green-700 hover:text-green-800">
                                            @lang('campus.view_students')
                                            <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            {{-- Indicador de consentimiento --}}
                            @if($course->consent_status)
                                <div class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                                    {{ $course->consent_status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $course->consent_status === 'completed' ? '📄 Consentiment Completat' : '📝 Consentiment Pendent' }}
                                </div>
                            @endif

                            {{-- Fechas del curso --}}
                            <div class="flex items-center text-sm text-gray-600 bg-gray-50 p-2 rounded">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ $course->start_date?->format('d/m/Y') }} - {{ $course->end_date?->format('d/m/Y') }}</span>
                                <span class="mx-2">•</span>
                                <span>@lang('campus.course_hours_total'): {{ $course->hours }}h</span>
                                @if($course->credits)
                                    <span class="mx-2">•</span>
                                    <span>@lang('campus.credits'): {{ $course->credits }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- Acciones --}}
                        <div class="flex flex-wrap gap-2 pt-4 border-t">
                            {{-- Enlace a estudiantes --}}
                            <a href="{{ route('campus.teacher.courses.students', $course->id) }}" 
                               class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
                                </svg>
                                @lang('campus.view_students')
                            </a>

                            {{-- Enlace a materiales (pendiente) --}}
                            <button type="button" 
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                                    disabled
                                    title="@lang('campus.materials_pending')">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                </svg>
                                @lang('campus.view_materials')
                            </button>

                            {{-- Más información del curso --}}
                            <a href="{{ route('campus.courses.show', $course->id) }}" 
                               class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                </svg>
                                @lang('campus.view')
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- CONSENTIMIENTOS Y PAGOS --}}
    @if($consentments && $consentments->isNotEmpty())
        <div class="bg-white p-6 rounded shadow mt-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-800">📄 Consentiments i Pagaments</h2>
                <div class="text-sm text-gray-500">
                    Temporada: {{ $currentSeason->name ?? '---' }}
                </div>
            </div>
            
            <!-- Lista de consentimientos por curso -->
            <div class="space-y-4">
                @foreach($consentments as $consent)
                    <div class="border rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-800">{{ $consent->course->title ?? 'Curso no encontrado' }}</h3>
                                <p class="text-sm text-gray-600">
                                    {{ $consent->course->code ?? '---' }} • {{ $consent->season }}
                                </p>
                                @if($consent->accepted_at)
                                    <p class="text-xs text-gray-500 mt-1">
                                        Acceptat: {{ $consent->accepted_at->format('d/m/Y H:i') }}
                                    </p>
                                @endif
                            </div>
                            <div class="text-right ml-4">
                                @if($consent->document_path)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        ✅ Completat
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        📝 Pendent
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        @if($consent->document_path)
                            <div class="mt-3 flex items-center justify-between">
                                <div class="text-sm text-gray-600">
                                    PDF generat: {{ $consent->updated_at?->format('d/m/Y H:i') }}
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ route('consents.download', $consent) }}" 
                                       class="inline-flex items-center text-sm font-medium text-blue-700 hover:text-blue-800">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Descarregar PDF
                                    </a>
                                    <a href="{{ route('campus.courses.show', $consent->course_id) }}" 
                                       class="inline-flex items-center text-sm font-medium text-green-700 hover:text-green-800">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"/>
                                        </svg>
                                        Veure curs
                                    </a>
                                </div>
                            </div>
                        @else
                            {{-- Consentimiento pendiente - mostrar enlace para completar --}}
                            <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-blue-800 font-medium">
                                            📝 Necessites completar el consentiment per aquest curs
                                        </p>
                                        <p class="text-xs text-blue-600 mt-1">
                                            Per generar el PDF final, accedeix al formulari de teacher-access
                                        </p>
                                    </div>
                                    <a href="{{ route('teacher.access.form', ['token' => 'generar-nuevo-token', 'purpose' => 'payments', 'courseCode' => $consent->course->code]) }}" 
                                       class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm-6 4h6v-6"/>
                                        </svg>
                                        Completar consentiment
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            
            @if($consentments->whereNull('document_path')->count() > 0)
                <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded">
                    <p class="text-sm text-yellow-800">
                        <strong>⚠️ Tens {{ $consentments->whereNull('document_path')->count() }} consentiments pendents de completar.</strong>
                        Si us plau, accedeix als formularis de teacher-access per finalitzar el procés.
                    </p>
                </div>
            @endif
        </div>
    @endif

</div>