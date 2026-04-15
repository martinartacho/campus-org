{{-- resources/views/components/dashboard/actions.blade.php --}}
{{-- Cuando se usa este componente, se ejecuta el código de abajo --}}
@auth
    @php
        $user = Auth::user();
    @endphp

    @if(config('app.debug'))
        <pre class="bg-gray-100 p-3 text-xs rounded border">{{ var_export([
            'path' => 'resources/views/components/dashboard/actions.blade.php',
            'error' => $error ?? null,
            'debug' => $debug ?? null,
            'stats' => $stats ?? [],
        ], true) }}
        </pre>
    @endif
 
    {{-- ========================= --}}
    {{-- 📊 STATS RÀPIDES --}}
    {{-- ========================= --}}
    @if(!empty($stats))
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="bi bi-speedometer2 me-2"></i>
                {{ __('Visió general ') }} ( {{ count($stats) }} en {{ __('Actions') }})
            </h2>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

                @isset($stats['courses'])
                    <div class="bg-blue-50 p-4 rounded-lg border">
                        <div class="text-sm text-gray-600">Cursos </div>
                        <div class="text-2xl font-bold text-blue-700">
                            {{ $stats['courses'] }}
                        </div>
                    </div>
                @endisset

                @isset($stats['students'])
                    <div class="bg-green-50 p-4 rounded-lg border">
                        <div class="text-sm text-gray-600">Estudiants</div>
                        <div class="text-2xl font-bold text-green-700">
                            {{ $stats['students'] }}
                        </div>
                    </div>
                @endisset

                @isset($stats['teachers'])
                    <div class="bg-teal-50 p-4 rounded-lg border">
                        <div class="text-sm text-gray-600">Professors</div>
                        <div class="text-2xl font-bold text-teal-700">
                            {{ $stats['teachers'] }}
                        </div>
                    </div>
                @endisset

                @isset($stats['registrations'])
                    <div class="bg-purple-50 p-4 rounded-lg border">
                        <div class="text-sm text-gray-600">Matriculacions</div>
                        <div class="text-2xl font-bold text-purple-700">
                            {{ $stats['registrations'] }}
                        </div>
                    </div>
                @endisset

            </div>
        </div>
    @endif


    {{-- ========================= --}}
    {{-- 🧠 WIDGET 1: INSCRIPCIONS RECENTS --}}
    {{-- ========================= --}}
    @can('campus.registrations.view')
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <i class="bi bi-clock-history me-2"></i>
                Últimes matriculacions
            </h2>

            @php
                $recentRegistrations = \App\Models\Registration::with('student', 'course')
                    ->latest()
                    ->limit(5)
                    ->get();
            @endphp

            <div class="space-y-2">
                @forelse($recentRegistrations as $reg)
                    <div class="flex justify-between text-sm border-b pb-2">
                        <div>
                            <strong>{{ $reg->student->name ?? '-' }}</strong>
                            <div class="text-gray-500">
                                {{ $reg->course->title ?? '-' }}
                            </div>
                        </div>
                        <div class="text-gray-400">
                            {{ $reg->created_at->diffForHumans() }}
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">No hi ha matriculacions recents</p>
                @endforelse
            </div>
        </div>
    @endcan


    {{-- ========================= --}}
    {{-- 🧠 WIDGET 2: CURSOS SENSE PROFESSOR --}}
    {{-- ========================= --}}
    @can('campus.courses.view')
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <i class="bi bi-exclamation-triangle me-2 text-yellow-600"></i>
                Cursos sense professor assignat
            </h2>

            @php
                $coursesWithoutTeacher = \App\Models\Course::whereNull('teacher_id')
                    ->latest()
                    ->limit(5)
                    ->get();
            @endphp

            <div class="space-y-2">
                @forelse($coursesWithoutTeacher as $course)
                    <div class="text-sm border-b pb-2">
                        <strong>{{ $course->title }}</strong>
                    </div>
                @empty
                    <p class="text-gray-500">Tots els cursos tenen professor assignat</p>
                @endforelse
            </div>
        </div>
    @endcan


    {{-- ========================= --}}
    {{-- 🧠 WIDGET 3: ALUMNES RECENTS --}}
    {{-- ========================= --}}
    @can('campus.students.view')
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <i class="bi bi-person-plus me-2"></i>
                Nous estudiants
            </h2>

            @php
                $recentStudents = \App\Models\Student::latest()
                    ->limit(5)
                    ->get();
            @endphp

            <div class="space-y-2">
                @forelse($recentStudents as $student)
                    <div class="flex justify-between text-sm border-b pb-2">
                        <div>
                            <strong>{{ $student->name }}</strong>
                        </div>
                        <div class="text-gray-400">
                            {{ $student->created_at->diffForHumans() }}
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">No hi ha nous estudiants</p>
                @endforelse
            </div>
        </div>
    @endcan

@endauth