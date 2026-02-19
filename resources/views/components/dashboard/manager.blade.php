@props([
    'stats' => [],
    'debug' => null,
    'error' => null,
])

<div class="bg-white p-6 rounded shadow">
    <h3 class="text-lg font-semibold mb-4">
        {{--  ({{ __('Equip Tècnic ') }}) conegut pel sobre nom d'<strong>ET</strong>  --}}
       {{ __('campus.campus_manager') }}
    </h3>

    
    <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg border border-blue-200 hover:border-blue-300">
        <div class="flex items-center justify-between">
            <h4 class="text-lg font-medium text-blue-800">{{ __('Estadístiques') }}</h4>
            
            
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @isset($stats['courses'])
                <span class="text-blue-700">{{ __('campus.courses') }}: {{ $stats['courses'] }}</span>
            @endisset

            @isset($stats['teachers'])
                <span class="text-blue-700">{{ __('campus.teachers') }}: {{ $stats['teachers'] }} </span>
            @endisset
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @isset($stats['students'])
                <span class="text-blue-700">{{ __('campus.students') }}: {{ $stats['students'] }}</span>
            @endisset

            @isset($stats['registrations'])
                <span class="text-blue-700">{{ __('campus.registrations') }}: {{ $stats['registrations'] }} </span>
            @endisset
        </div>
    </div>
</div> 


<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- CURSOS --}}
        @can('campus.courses.view')
            <a href="{{ route('manager.courses.index') }}" class="block transition-transform hover:scale-[1.02]">
            <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-lg border border-green-200 hover:border-green-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-800">{{ __('Cursos') }}</p>
                        <p class="text-2xl font-bold text-green-900">
                            @isset($stats['courses'])
                            {{ $stats['courses'] }}
                            @endisset
                        </p>
                    </div>
                    <div class="p-2 bg-green-200 rounded-lg">
                        <i class="bi bi-book text-green-600 text-xl"></i>
                    </div>
                </div>
                
                <div class="mt-3 pt-2 border-t border-green-200">
                    <span class="text-xs text-green-600 hover:text-green-800 flex items-center">
                        Gestionar cursos <i class="bi bi-arrow-right-short ms-1"></i>
                    </span>
                </div>
            </div>
            </a>
        @endcan
        
        {{-- inscripcions --}}
        @can('campus.registrations.view')
            <a href="{{ route('manager.registrations.index') }}" class="block transition-transform hover:scale-[1.02]">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg border border-blue-200 hover:border-blue-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-800">{{ __('Inscripcions') }}</p>
                        <p class="text-2xl font-bold text-blue-900">
                            @isset($stats['registrations'])
                            {{ $stats['registrations'] }}
                            @endisset
                        </p>
                    </div>
                    <div class="p-2 bg-blue-200 rounded-lg">
                        <i class="bi bi-people text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-2 grid grid-cols-2 gap-1 text-xs">
                        <span class="text-green-700">Actius: {{ $stats['active_courses'] ?? 0 }}</span>
                        <span class="text-green-700">Inactius: {{ $stats['inactive_courses'] ?? 0 }}</span>
                    </div>
                
                <div class="mt-3 pt-2 border-t border-blue-200">
                    <span class="text-xs text-blue-600 hover:text-blue-800 flex items-center">
                        Veure inscripcions <i class="bi bi-arrow-right-short ms-1"></i>
                    </span>
                </div>
            </div>
            </a>
        @endcan


    </div>
</div>

