   <!--  @if(config('app.debug'))
        <pre class="bg-gray-100 p-3 text-xs rounded border">{{ var_export([
            'path' => 'resources/views/components/dashboard/admin-cards.blade.php',
            'user' => auth()->user()->email,
            'roles' => auth()->user()->roles->pluck('name')->toArray(),
            'error' => $error ?? null,
            'debug' => $debug ?? null,
            'stats' => $stats ?? [],
            'activeRole' => $activeRole ?? null,
            'widgets' => $widgets ?? [],
        ], true) }}
        </pre>
    @endif -->

    @auth
    @php
        $user = Auth::user();
    @endphp

    {{-- ========================= --}}
    {{-- 📊 STATS RÀPIDES --}}
    {{-- ========================= --}}
    @if(!empty($stats))
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="bi bi-speedometer2 me-2"></i>
                {{ __('Visió general') }} 
                @if($activeRole)
                    ( {{ ucfirst($activeRole) }} )
                @endif
            </h2>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <p>path: resources/views/components/dashboard/admin-cards.blade.php</p>

                {{-- Contadores según rol activo --}}
                @switch($activeRole)
                    @case('director')
                        @isset($stats['total_courses'])
                            <div class="bg-blue-50 p-4 rounded-lg border">
                                <div class="text-sm text-gray-600">Cursos totals</div>
                                <div class="text-2xl font-bold text-blue-700">
                                    {{ $stats['total_courses'] }}
                                </div>
                            </div>
                        @endisset
                        @isset($stats['student_count'])
                            <div class="bg-green-50 p-4 rounded-lg border">
                                <div class="text-sm text-gray-600">Estudiants totals</div>
                                <div class="text-2xl font-bold text-green-700">
                                    {{ $stats['student_count'] }}
                                </div>
                            </div>
                        @endisset
                        @isset($stats['teacher_count'])
                            <div class="bg-teal-50 p-4 rounded-lg border">
                                <div class="text-sm text-gray-600">Professors totals</div>
                                <div class="text-2xl font-bold text-teal-700">
                                    {{ $stats['teacher_count'] }}
                                </div>
                            </div>
                        @endisset
                        @break

                    @case('coordinacio')
                        @isset($stats['courses'])
                            <div class="bg-blue-50 p-4 rounded-lg border">
                                <div class="text-sm text-gray-600">Cursos actius</div>
                                <div class="text-2xl font-bold text-blue-700">
                                    {{ $stats['courses'] }}
                                </div>
                            </div>
                        @endisset
                        @isset($stats['students'])
                            <div class="bg-green-50 p-4 rounded-lg border">
                                <div class="text-sm text-gray-600">Matriculats</div>
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
                        @break

                    @case('secretaria')
                        @isset($stats['students'])
                            <div class="bg-green-50 p-4 rounded-lg border">
                                <div class="text-sm text-gray-600">Expedients</div>
                                <div class="text-2xl font-bold text-green-700">
                                    {{ $stats['students'] }}
                                </div>
                            </div>
                        @endisset
                        @isset($stats['registrations'])
                            <div class="bg-purple-50 p-4 rounded-lg border">
                                <div class="text-sm text-gray-600">Matriculacions pendents</div>
                                <div class="text-2xl font-bold text-purple-700">
                                    {{ $stats['registrations'] }}
                                </div>
                            </div>
                        @endisset
                        @isset($stats['courses'])
                            <div class="bg-blue-50 p-4 rounded-lg border">
                                <div class="text-sm text-gray-600">Cursos</div>
                                <div class="text-2xl font-bold text-blue-700">
                                    {{ $stats['courses'] }}
                                </div>
                            </div>
                        @endisset
                        @break

                    @case('gestio')
                        @isset($stats['courses'])
                            <div class="bg-blue-50 p-4 rounded-lg border">
                                <div class="text-sm text-gray-600">Recursos</div>
                                <div class="text-2xl font-bold text-blue-700">
                                    {{ $stats['courses'] }}
                                </div>
                            </div>
                        @endisset
                        @isset($stats['teachers'])
                            <div class="bg-teal-50 p-4 rounded-lg border">
                                <div class="text-sm text-gray-600">Suport</div>
                                <div class="text-2xl font-bold text-teal-700">
                                    {{ $stats['teachers'] }}
                                </div>
                            </div>
                        @endisset
                        @break

                    @default
                        @isset($stats['total_courses'])
                            <div class="bg-blue-50 p-4 rounded-lg border">
                                <div class="text-sm text-gray-600">Cursos (linia 33)</div>
                                <div class="text-2xl font-bold text-blue-700">
                                    {{ $stats['total_courses'] }}
                                </div>
                            </div>
                        @endisset
                        @isset($stats['student_count'])
                            <div class="bg-green-50 p-4 rounded-lg border">
                                <div class="text-sm text-gray-600">Estudiants (linia 42)</div>
                                <div class="text-2xl font-bold text-green-700">
                                    {{ $stats['student_count'] }}
                                </div>
                            </div>
                        @endisset
                        @isset($stats['teacher_count'])
                            <div class="bg-teal-50 p-4 rounded-lg border">
                                <div class="text-sm text-gray-600">Professors (linia 51)</div>
                                <div class="text-2xl font-bold text-teal-700">
                                    {{ $stats['teacher_count'] }}
                                </div>
                            </div>
                        @endisset
                        @isset($stats['total_registrations'])
                            <div class="bg-purple-50 p-4 rounded-lg border">
                                <div class="text-sm text-gray-600">Matriculacions </div>
                                <div class="text-2xl font-bold text-purple-700">
                                    {{ $stats['total_registrations'] }}
                                </div>
                            </div>
                        @endisset
                @endswitch

            </div>
        </div>
    @endif


    {{-- ========================= --}}
    {{-- 🧠 WIDGETS DINÁMICOS DESDE BD --}}
    {{-- ========================= --}}
    {{-- Los widgets ahora se cargan dinámicamente desde dashboard.blade.php 
         según la configuración de la base de datos.
         Ya no hay widgets hardcodeados aquí. --}}

@endauth