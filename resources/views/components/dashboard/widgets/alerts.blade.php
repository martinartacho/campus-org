{{-- resources/views/components/dashboard/widgets/alerts.blade.php --}}

<div class="bg-white p-6 rounded-lg shadow-md mb-6">
    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
        <i class="bi bi-exclamation-triangle me-2 text-orange-600"></i>
        Alertes i Notificacions
    </h2>

    @php
        $alerts = [];
        
        // Alerta: Cursos sense professor
        $coursesWithoutTeacher = \App\Models\CampusCourse::whereDoesntHave('teachers')
            ->whereHas('season', function($query) {
                $query->where('slug', config('campus.current_season', '2024-25'));
            })
            ->count();
        if ($coursesWithoutTeacher > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'bi-person-x',
                'title' => 'Cursos sense professor',
                'message' => "{$coursesWithoutTeacher} cursos no tenen professor assignat",
                'count' => $coursesWithoutTeacher
            ];
        }

        // Alerta: Cursos plens
        $fullCourses = \App\Models\CampusCourse::where('max_students', '>', 0)
            ->whereHas('season', function($query) {
                $query->where('slug', config('campus.current_season', '2024-25'));
            })
            ->whereRaw('(SELECT COUNT(*) FROM campus_registrations WHERE campus_registrations.course_id = campus_courses.id) >= max_students')
            ->count();
        if ($fullCourses > 0) {
            $alerts[] = [
                'type' => 'info',
                'icon' => 'bi-people-fill',
                'title' => 'Cursos plens',
                'message' => "{$fullCourses} cursos han arribat al màxim d'estudiants",
                'count' => $fullCourses
            ];
        }

        // Alerta: Matriculacions pendents
        $pendingRegistrations = \App\Models\CampusRegistration::where('status', 'pending')->count();
        if ($pendingRegistrations > 0) {
            $alerts[] = [
                'type' => 'danger',
                'icon' => 'bi-clock-fill',
                'title' => 'Matriculacions pendents',
                'message' => "{$pendingRegistrations} matriculacions esperant aprovació",
                'count' => $pendingRegistrations
            ];
        }

        // Alerta: Pagaments pendents
        $pendingPayments = \App\Models\CampusTeacherPayment::where('needs_payment', true)->count();
        if ($pendingPayments > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'bi-credit-card',
                'title' => 'Pagaments pendents',
                'message' => "{$pendingPayments} pagaments de professorat pendents",
                'count' => $pendingPayments
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
