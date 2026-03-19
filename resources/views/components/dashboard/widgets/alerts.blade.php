{{-- resources/views/components/dashboard/widgets/alerts.blade.php --}}

<div class="bg-white p-6 rounded-lg shadow-md mb-6">
    <!-- Header sin acordeón (siempre visible) -->
    <div class="border-b border-gray-200 pb-3">
        <div class="flex items-center">
            <i class="bi bi-exclamation-triangle me-2 text-orange-600"></i>
            <h2 class="text-lg font-bold text-gray-800">Alertes i Notificacions</h2>
        </div>
    </div>

    @php
        $alerts = [];
        
        // Alerta: Cursos sense professor
        $coursesWithoutTeacher = \App\Models\CampusCourse::whereDoesntHave('teachers')
            ->count();
        if ($coursesWithoutTeacher > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'bi-person-x',
                'title' => 'Cursos sense professor',
                'message' => "{$coursesWithoutTeacher} cursos no tenen professor assignat",
                'count' => $coursesWithoutTeacher,
                'link' => 'https://dev.upg.cat/campus/courses',
                'link_text' => 'Veure cursos'
            ];
        }

        // Alerta: Cursos plens
        $fullCourses = \App\Models\CampusCourse::where('max_students', '>', 0)
            ->withCount('students')
            ->get()
            ->filter(function($course) {
                return $course->students_count >= $course->max_students;
            })->count();
        if ($fullCourses > 0) {
            $alerts[] = [
                'type' => 'info',
                'icon' => 'bi-people-fill',
                'title' => 'Cursos plens',
                'message' => "{$fullCourses} cursos han arribat al màxim d'estudiants",
                'count' => $fullCourses
            ];
        }

        // Alerta: Matriculacions pendents - ELIMINADO COMPLETAMENTE
        // $pendingRegistrations = \App\Models\CampusCourseStudent::where('academic_status', 'enrolled')
        //     ->count();
        // if ($pendingRegistrations > 0) {
        //     $alerts[] = [
        //         'type' => 'danger',
        //         'icon' => 'bi-clock-fill',
        //         'title' => 'Matriculacions pendents',
        //         'message' => "{$pendingRegistrations} matriculacions esperant aprovació",
        //         'count' => $pendingRegistrations
        //     ];
        // }

        // Alerta: Datos bancarios en preparación
        $pendingBankData = \App\Models\CampusTeacherPayment::where('needs_payment', true)
            ->count();
        if ($pendingBankData > 0) {
            $alerts[] = [
                'type' => 'info',
                'icon' => 'bi-clock-history',
                'title' => 'Dades bancàries en preparació',
                'message' => "{$pendingBankData} professors/es en procés de preparació de dades",
                'count' => $pendingBankData,
                'link' => 'https://dev.upg.cat/campus/treasury/teachers',
                'link_text' => 'Veure detalls'
            ];
        }
    @endphp

    @forelse($alerts as $alert)
        <div class="mb-3 p-4 rounded-lg border 
            @if($alert['type'] == 'danger') bg-red-50 border-red-200
            @elseif($alert['type'] == 'warning') bg-yellow-50 border-yellow-200
            @elseif($alert['type'] == 'info') bg-blue-50 border-blue-200
            @else bg-gray-50 border-gray-200 @endif">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="bi {{ $alert['icon'] }} text-lg 
                        @if($alert['type'] == 'danger') text-red-600
                        @elseif($alert['type'] == 'warning') text-yellow-600
                        @elseif($alert['type'] == 'info') text-blue-600
                        @else text-gray-600 @endif"></i>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium 
                        @if($alert['type'] == 'danger') text-red-800
                        @elseif($alert['type'] == 'warning') text-yellow-800
                        @elseif($alert['type'] == 'info') text-blue-800
                        @else text-gray-800 @endif">
                        {{ $alert['title'] }}
                    </h3>
                    <p class="text-sm mt-1 
                        @if($alert['type'] == 'danger') text-red-700
                        @elseif($alert['type'] == 'warning') text-yellow-700
                        @elseif($alert['type'] == 'info') text-blue-700
                        @else text-gray-700 @endif">
                        {{ $alert['message'] }}
                    </p>
                    @isset($alert['link'])
                        <div class="mt-2">
                            <a href="{{ $alert['link'] }}" 
                               class="inline-flex items-center text-xs font-medium 
                                   @if($alert['type'] == 'danger') text-red-600 hover:text-red-800
                                   @elseif($alert['type'] == 'warning') text-yellow-600 hover:text-yellow-800
                                   @elseif($alert['type'] == 'info') text-blue-600 hover:text-blue-800
                                   @else text-gray-600 hover:text-gray-800 @endif">
                                <i class="bi bi-arrow-right me-1"></i>
                                {{ $alert['link_text'] ?? 'Veure detalls' }}
                            </a>
                        </div>
                    @endisset
                </div>
                <div class="ml-3 flex-shrink-0">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($alert['type'] == 'danger') bg-red-100 text-red-800
                        @elseif($alert['type'] == 'warning') bg-yellow-100 text-yellow-800
                        @elseif($alert['type'] == 'info') bg-blue-100 text-blue-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ $alert['count'] }}
                    </span>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-8 text-gray-500">
            <i class="bi bi-check-circle text-4xl text-green-500 mb-2"></i>
            <p class="text-sm">No hi ha alertes actives</p>
        </div>
    @endforelse
</div>
