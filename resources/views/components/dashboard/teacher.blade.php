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

    {{-- WIDGET DE DOCUMENTOS --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <x-dashboard.widgets.teacher_documents />
        
        {{-- Aquí se pueden añadir más widgets en el futuro --}}
    </div>

    {{-- DEBUG --}}
<!--    @if(config('app.debug'))
        <pre class="bg-gray-100 p-3 text-xs rounded border">{{ var_export([
            'teacher' => optional($teacher)->teacher_code,
            'courses' => $teacherCourses->count(),
            'stats' => $stats,
        ], true) }}
        </pre>
    @endif -->

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
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
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
            @php
                $unreadCount = auth()->user()->unreadNotifications()->where('is_published', true)->count();
            @endphp
            <div class="text-2xl font-bold text-blue-600">{{ $unreadCount }}</div>
            <div class="text-xs text-gray-500">@lang('site.Notifications')</div>
        </div>

        {{-- TARGETA DADES BANCÀRIES --}}
        @if($teacher)
        <div class="bg-white border-2 border-green-500 p-4 rounded-lg shadow-lg text-center hover:shadow-xl hover:border-green-600 transition-all duration-300 cursor-pointer"
             onclick="window.location.href='{{ route('teacher.profile') }}#banking-data'">
            <div class="text-3xl font-bold text-green-600 mb-2">
                <i class="bi bi-bank"></i>
            </div>
            <div class="text-sm font-semibold text-gray-800 mb-2">
                {{ __('Dades Bancàries') }}
            </div>
            <div class="mt-2">
                {{-- Indicador d'estat del PDF --}}
                <x-teacher-pdf-status :teacher="$teacher" />
                
                {{-- Enllaç a PDFs si hi ha PDFs --}}
                @if($teacher && $teacher->hasPdfs())
                    <div class="mt-2">
                        <a href="{{ route('teacher.profile.pdfs') }}" 
                           class="text-xs text-blue-600 hover:text-blue-800 underline flex items-center">
                            <i class="bi bi-file-earmark-pdf mr-1"></i>
                            Veure PDFs
                        </a>
                    </div>
                @endif
            </div>
        </div>
        </div>
        @endif
    </div>
    

    {{-- ELS MEUS CURSOS --}}
    <div class="bg-white p-6 rounded shadow">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800">@lang('campus.my_courses')</h2>
            <div class="text-sm text-gray-600">
                Actiu: <span class="font-bold text-green-600">{{ $stats['active_courses'] ?? 0 }}</span> | 
                Completat: <span class="font-bold text-blue-600">{{ $stats['completed_courses'] ?? 0 }}</span>
            </div>
        </div>
        
        @if(isset($teacherCourses) && count($teacherCourses) > 0)
            <!-- Grid para las tarjetas de cursos - 2 columnas -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($teacherCourses as $course)
                    <div class="border rounded-lg p-4 hover:shadow-md transition-shadow bg-white">
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
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $course->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $course->is_active ? 'Actiu' : 'Completat' }}
                                    </span>
                                </div>
                                
                                {{-- Badge si hay clase ahora --}}
                                @php
                                    $hasClassToday = !empty($course->schedule_info['today_classes']);
                                    $currentClass = $hasClassToday ? 
                                        collect($course->schedule_info['today_classes'])->firstWhere('is_current', true) : null;
                                @endphp
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
                            @php
                                $myRole = trans('campus.teacher_role.' . ($course->pivot->role ?? 'assistant'));
                                $myHours = $course->pivot->sessions_assigned ?? 0;
                            @endphp
                            <div class="flex items-center justify-between bg-gray-50 p-3 rounded">
                                <div>
                                    <div class="text-sm font-medium text-gray-700">@lang('campus.my_role')</div>
                                    <div class="text-lg font-semibold text-blue-600">{{ $myRole }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-gray-700">@lang('campus.course_sessions_assigned')</div>
                                    <div class="text-lg font-semibold text-gray-800">{{ number_format($myHours) }} @lang('campus.sessions')</div>
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

                            {{-- Fechas del curso --}}
                            <div class="flex items-center text-sm text-gray-600 bg-gray-50 p-2 rounded">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ $course->start_date?->format('d/m/Y') }} - {{ $course->end_date?->format('d/m/Y') }}</span>
                                <span class="mx-2">•</span>
                                <span>Sessions: {{ $course->sessions }} total</span>
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
        @else
            <div class="text-center py-8 text-gray-500">
                <p>@lang('campus.no_courses_assigned')</p>
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