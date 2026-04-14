{{-- resources/views/components/dashboard/widgets/manager_visio_general.blade.php --}}

<div class="bg-white p-6 rounded-lg shadow-md w-full">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
        <i class="bi bi-graph-up text-blue-600 me-2"></i>
        Visió general - Manager
    </h3>
    
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="text-center">
            <div class="text-2xl font-bold text-blue-700">
                @isset($stats['total_users'])
                    {{ $stats['total_users'] }}
                @else
                    {{ \App\Models\User::count() }}
                @endisset
            </div>
            <div class="text-sm text-gray-600">Usuaris</div>
        </div>
        
        <div class="text-center">
            <div class="text-2xl font-bold text-orange-700">
                @isset($stats['teacher_count'])
                    {{ $stats['teacher_count'] }}
                @else
                    {{ \App\Models\CampusTeacher::count() }}
                @endisset
            </div>
            <div class="text-sm text-gray-600">Professorat</div>
        </div>
        
        <div class="text-center">
            <div class="text-2xl font-bold text-green-700">
                @isset($stats['total_courses'])
                    {{ $stats['total_courses'] }}
                @else
                    {{ \App\Models\CampusCourse::count() }}
                @endisset
            </div>
            <div class="text-sm text-gray-600">Cursos</div>
        </div>
        
        <div class="text-center">
            <div class="text-2xl font-bold text-purple-700">
                @isset($stats['total_registrations'])
                    {{ $stats['total_registrations'] }}
                @else
                    {{ \App\Models\CampusCourseStudent::count() }}
                @endisset
            </div>
            <div class="text-sm text-gray-600">Matriculacions</div>
        </div>
    </div>
</div>
