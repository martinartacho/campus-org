{{-- resources/views/components/dashboard/widgets/courses_status.blade.php --}}

<div class="bg-white p-6 rounded-lg shadow-md mb-6">
    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
        <i class="bi bi-book me-2 text-blue-600"></i>
        Estat dels Cursos (widgets/courses_status linia 6)
    </h2>

    @php
        $currentSeason = config('campus.current_season', '2024-25');
        $totalCourses = \App\Models\CampusCourse::whereHas('season', function($query) use ($currentSeason) {
            $query->where('slug', $currentSeason);
        })->count();

        $coursesWithTeacher = \App\Models\CampusCourse::whereHas('season', function($query) use ($currentSeason) {
            $query->where('slug', $currentSeason);
        })->whereHas('teachers')->count();

        $activeCourses = \App\Models\CampusCourse::whereHas('season', function($query) use ($currentSeason) {
            $query->where('slug', $currentSeason);
        })->where('status', 'active')->count();

        // Simplificado: cursos con capacidad máxima alcanzada (matriculaciones >= plazas)
        $fullCourses = \App\Models\CampusCourse::whereHas('season', function($query) use ($currentSeason) {
            $query->where('slug', $currentSeason);
        })->where('max_students', '>', 0)
        ->withCount('students')
        ->get()
        ->filter(function($course) {
            return $course->students_count >= $course->max_students;
        })->count();
    @endphp

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="text-center p-4 bg-blue-50 rounded-lg">
            <div class="text-2xl font-bold text-blue-700">{{ $totalCourses }}</div>
            <div class="text-sm text-gray-600">Total cursos</div>
        </div>
        
        <div class="text-center p-4 bg-green-50 rounded-lg">
            <div class="text-2xl font-bold text-green-700">{{ $coursesWithTeacher }}</div>
            <div class="text-sm text-gray-600">Amb professor</div>
        </div>
        
        <div class="text-center p-4 bg-teal-50 rounded-lg">
            <div class="text-2xl font-bold text-teal-700">{{ $activeCourses }}</div>
            <div class="text-sm text-gray-600">Actius</div>
        </div>
        
        <div class="text-center p-4 bg-red-50 rounded-lg">
            <div class="text-2xl font-bold text-red-700">{{ $fullCourses }}</div>
            <div class="text-sm text-gray-600">Plens</div>
        </div>
    </div>

    @if($totalCourses > $coursesWithTeacher)
        <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
            <div class="text-sm text-yellow-800">
                <i class="bi bi-exclamation-triangle me-1"></i>
                {{ $totalCourses - $coursesWithTeacher }} cursos sense professor assignat
            </div>
        </div>
    @endif
</div>
