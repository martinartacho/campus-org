{{-- resources/views/components/dashboard/widgets/manager_visio_general.blade.php --}}

<div class="bg-white p-6 rounded-lg shadow-md">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
        <i class="bi bi-graph-up text-blue-600 me-2"></i>
        Visió general - Manager
    </h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        
        {{-- USUARIS --}}
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg border border-blue-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-800">Usuaris</p>
                    <p class="text-2xl font-bold text-blue-900">
                        @isset($stats['total_users'])
                            {{ $stats['total_users'] }}
                        @else
                            {{ \App\Models\User::count() }}
                        @endisset
                    </p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="bi bi-people-fill text-blue-600 text-xl"></i>
                </div>
            </div>
            
            <div class="mt-2 text-xs">
                <span class="text-blue-700">Usuaris registrats</span>
            </div>
            
            <div class="mt-3 pt-2 border-t border-blue-200">
                <span class="text-xs text-blue-600">
                    Total del sistema
                </span>
            </div>
        </div>
        
        {{-- PROFESSORAT --}}
        <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-lg border border-green-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-green-800">Professorat</p>
                    <p class="text-2xl font-bold text-green-900">
                        @isset($stats['teacher_count'])
                            {{ $stats['teacher_count'] }}
                        @else
                            {{ \App\Models\CampusTeacher::count() }}
                        @endisset
                    </p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="bi bi-person-workspace text-green-600 text-xl"></i>
                </div>
            </div>
            
            <div class="mt-2 text-xs">
                <span class="text-green-700">Professors actius</span>
            </div>
            
            <div class="mt-3 pt-2 border-t border-green-200">
                <span class="text-xs text-green-600">
                    Cos docent
                </span>
            </div>
        </div>
        
        {{-- CURSOS --}}
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-lg border border-purple-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-purple-800">Cursos</p>
                    <p class="text-2xl font-bold text-purple-900">
                        @isset($stats['total_courses'])
                            {{ $stats['total_courses'] }}
                        @else
                            {{ \App\Models\CampusCourse::count() }}
                        @endisset
                    </p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <i class="bi bi-book-fill text-purple-600 text-xl"></i>
                </div>
            </div>
            
            <div class="mt-2 text-xs">
                <span class="text-purple-700">Cursos disponibles</span>
            </div>
            
            <div class="mt-3 pt-2 border-t border-purple-200">
                <span class="text-xs text-purple-600">
                    Catàleg complet
                </span>
            </div>
        </div>
        
        {{-- MATRICULACIONS --}}
        <div class="bg-gradient-to-br from-orange-50 to-orange-100 p-4 rounded-lg border border-orange-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-orange-800">Matriculacions</p>
                    <p class="text-2xl font-bold text-orange-900">
                        @isset($stats['total_registrations'])
                            {{ $stats['total_registrations'] }}
                        @else
                            {{ \App\Models\CampusCourseStudent::count() }}
                        @endisset
                    </p>
                </div>
                <div class="bg-orange-100 p-3 rounded-full">
                    <i class="bi bi-person-check-fill text-orange-600 text-xl"></i>
                </div>
            </div>
            
            <div class="mt-2 text-xs">
                <span class="text-orange-700">Matriculacions totals</span>
            </div>
            
            <div class="mt-3 pt-2 border-t border-orange-200">
                <span class="text-xs text-orange-600">
                    Totes les temporades
                </span>
            </div>
        </div>
        
    </div>
</div>
