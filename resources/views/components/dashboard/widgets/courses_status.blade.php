{{-- resources/views/components/dashboard/widgets/courses_status.blade.php --}}

<div class="bg-white p-6 rounded-lg shadow-md mb-6">
    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
        <i class="bi bi-book me-2 text-blue-600"></i>
        Estat dels Cursos
    </h2>

    @php
        // Opción 1: Todos los cursos (sin filtro de temporada)
        $totalCourses = \App\Models\CampusCourse::count();
        
        $coursesWithTeacher = \App\Models\CampusCourse::whereHas('teachers')->count();
        
        $activeCourses = \App\Models\CampusCourse::where('status', 'active')->count();
        
        // Cursos con capacidad máxima alcanzada
        $fullCourses = \App\Models\CampusCourse::where('max_students', '>', 0)
            ->withCount('students')
            ->get()
            ->filter(function($course) {
                return $course->students_count >= $course->max_students;
            })->count();
            
        // Opción 2: Si quieres filtrar por temporada actual (descomenta):
        /*
        $currentSeason = \App\Models\CampusSeason::where('is_current', true)->first();
        if ($currentSeason) {
            $seasonSlug = $currentSeason->slug;
            
            $totalCourses = \App\Models\CampusCourse::whereHas('season', function($query) use ($seasonSlug) {
                $query->where('slug', $seasonSlug);
            })->count();
            
            $coursesWithTeacher = \App\Models\CampusCourse::whereHas('season', function($query) use ($seasonSlug) {
                $query->where('slug', $seasonSlug);
            })->whereHas('teachers')->count();
            
            $activeCourses = \App\Models\CampusCourse::whereHas('season', function($query) use ($seasonSlug) {
                $query->where('slug', $seasonSlug);
            })->where('status', 'active')->count();
            
            $fullCourses = \App\Models\CampusCourse::whereHas('season', function($query) use ($seasonSlug) {
                $query->where('slug', $seasonSlug);
            })->where('max_students', '>', 0)
                ->withCount('students')
                ->get()
                ->filter(function($course) {
                    return $course->students_count >= $course->max_students;
                })->count();
        }
        */
    @endphp

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="text-center p-4 bg-blue-50 rounded-lg">
            <div class="text-2xl font-bold text-blue-700">{{ $totalCourses }}</div>
            <div class="text-sm text-gray-600">Total cursos</div>
        </div>
        
        <div class="text-center p-4 bg-green-50 rounded-lg">
            <div class="text-2xl font-bold text-green-700">{{ $coursesWithTeacher }}</div>
            <div class="text-sm text-gray-600">Amb professor</div>
            @if($totalCourses - $coursesWithTeacher > 0)
                <div class="mt-2">
                    <a href="{{ route('campus.courses.index') }}" 
                       class="inline-flex items-center text-xs text-orange-600 hover:text-orange-800">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        {{ $totalCourses - $coursesWithTeacher }} sense professor
                    </a>
                </div>
            @endif
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
