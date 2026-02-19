@props(['stats' => []])

<div class="space-y-6">
    
    {{-- SECCIÓ 1: CARDS D'ACCÉS RÀPID --}}
    @include('components.dashboard-admin-cards')
    
    {{-- SECCIÓ 2: ESTADÍSTIQUES DEL SISTEMA (CON ENLACES) --}}
    @if(!empty($stats))
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
            <i class="bi bi-graph-up me-2"></i>
            {{ __('Estadístiques del Sistema') }}
        </h2>
        
        {{-- Primera fila --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            
            {{-- USUARIS --}}
            <a href="{{ route('admin.users.index') }}" class="block transition-transform hover:scale-[1.02]">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg border border-blue-200 hover:border-blue-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-800">{{ __('site.Users') }}</p>
                            <p class="text-2xl font-bold text-blue-900">{{ $stats['total_users'] ?? 0 }}</p>
                        </div>
                        <div class="p-2 bg-blue-200 rounded-lg">
                            <i class="bi bi-people text-blue-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="mt-2 grid grid-cols-3 gap-1 text-xs">
                        <span class="text-blue-700">Admin: {{ $stats['admin_count'] ?? 0 }}</span>
                        <span class="text-blue-700">Profs: {{ $stats['teacher_count'] ?? 0 }}</span>
                        <span class="text-blue-700">Est: {{ $stats['student_count'] ?? 0 }}</span>
                    </div>
                    <div class="mt-3 pt-2 border-t border-blue-200">
                        <span class="text-xs text-blue-600 hover:text-blue-800 flex items-center">
                            Gestionar usuaris <i class="bi bi-arrow-right-short ms-1"></i>
                        </span>
                    </div>
                </div>
            </a>
            
            {{-- CURSOS --}}
            <a href="{{ route('campus.courses.index') }}" class="block transition-transform hover:scale-[1.02]">
                <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-lg border border-green-200 hover:border-green-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-green-800">{{ __('Cursos') }}</p>
                            <p class="text-2xl font-bold text-green-900">{{ $stats['total_courses'] ?? 0 }}</p>
                        </div>
                        <div class="p-2 bg-green-200 rounded-lg">
                            <i class="bi bi-book text-green-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="mt-2 grid grid-cols-2 gap-1 text-xs">
                        <span class="text-green-700">Actius: {{ $stats['active_courses'] ?? 0 }}</span>
                        <span class="text-green-700">Inactius: {{ $stats['inactive_courses'] ?? 0 }}</span>
                    </div>
                    <div class="mt-3 pt-2 border-t border-green-200">
                        <span class="text-xs text-green-600 hover:text-green-800 flex items-center">
                            Gestionar cursos <i class="bi bi-arrow-right-short ms-1"></i>
                        </span>
                    </div>
                </div>
            </a>
            
            {{-- CATEGORIES --}}
            <a href="{{ route('campus.categories.index') }}" class="block transition-transform hover:scale-[1.02]">
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-lg border border-purple-200 hover:border-purple-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-purple-800">{{ __('Categories') }}</p>
                            <p class="text-2xl font-bold text-purple-900">{{ $stats['total_categories'] ?? 0 }}</p>
                        </div>
                        <div class="p-2 bg-purple-200 rounded-lg">
                            <i class="bi bi-folder text-purple-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="mt-2 text-xs">
                        <span class="text-purple-700">{{ $stats['categories_with_courses'] ?? 0 }} amb cursos</span>
                    </div>
                    <div class="mt-3 pt-2 border-t border-purple-200">
                        <span class="text-xs text-purple-600 hover:text-purple-800 flex items-center">
                            Gestionar categories <i class="bi bi-arrow-right-short ms-1"></i>
                        </span>
                    </div>
                </div>
            </a>
            
            {{-- MATRICULACIONS --}}
            <a href="{{ route('campus.registrations.index') }}" class="block transition-transform hover:scale-[1.02]">
                <div class="bg-gradient-to-br from-amber-50 to-amber-100 p-4 rounded-lg border border-amber-200 hover:border-amber-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-amber-800">{{ __('Matriculacions') }}</p>
                            <p class="text-2xl font-bold text-amber-900">{{ $stats['total_registrations'] ?? 0 }}</p>
                        </div>
                        <div class="p-2 bg-amber-200 rounded-lg">
                            <i class="bi bi-clipboard-check text-amber-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="mt-2 grid grid-cols-2 gap-1 text-xs">
                        <span class="text-amber-700">Actives: {{ $stats['active_registrations'] ?? 0 }}</span>
                        <span class="text-amber-700">Completades: {{ $stats['completed_registrations'] ?? 0 }}</span>
                    </div>
                    <div class="mt-3 pt-2 border-t border-amber-200">
                        <span class="text-xs text-amber-600 hover:text-amber-800 flex items-center">
                            Gestionar matriculacions <i class="bi bi-arrow-right-short ms-1"></i>
                        </span>
                    </div>
                </div>
            </a>
            
        </div>
        
        {{-- Segunda fila --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            
            {{-- TEMPORADES --}}
            <a href="{{ route('campus.seasons.index') }}" class="block transition-transform hover:scale-[1.02]">
                <div class="bg-gradient-to-br from-cyan-50 to-cyan-100 p-4 rounded-lg border border-cyan-200 hover:border-cyan-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-cyan-800">{{ __('Temporades') }}</p>
                            <p class="text-2xl font-bold text-cyan-900">{{ $stats['total_seasons'] ?? 0 }}</p>
                        </div>
                        <div class="p-2 bg-cyan-200 rounded-lg">
                            <i class="bi bi-calendar-range text-cyan-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="mt-2 text-xs">
                        <span class="text-cyan-700">Actual: {{ $stats['current_season'] ?? 'No configurada' }}</span>
                    </div>
                    <div class="mt-3 pt-2 border-t border-cyan-200">
                        <span class="text-xs text-cyan-600 hover:text-cyan-800 flex items-center">
                            Gestionar temporades <i class="bi bi-arrow-right-short ms-1"></i>
                        </span>
                    </div>
                </div>
            </a>
            
            {{-- ESDEVENIMENTS --}}
            <a href="{{ route('admin.events.index') }}" class="block transition-transform hover:scale-[1.02]">
                <div class="bg-gradient-to-br from-red-50 to-red-100 p-4 rounded-lg border border-red-200 hover:border-red-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-red-800">{{ __('site.Events') }}</p>
                            <p class="text-2xl font-bold text-red-900">{{ $stats['total_events'] ?? 0 }}</p>
                        </div>
                        <div class="p-2 bg-red-200 rounded-lg">
                            <i class="bi bi-calendar-event text-red-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="mt-2 grid grid-cols-2 gap-1 text-xs">
                        <span class="text-red-700">Pròxims: {{ $stats['upcoming_events'] ?? 0 }}</span>
                        <span class="text-red-700">Passats: {{ $stats['past_events'] ?? 0 }}</span>
                    </div>
                    <div class="mt-3 pt-2 border-t border-red-200">
                        <span class="text-xs text-red-600 hover:text-red-800 flex items-center">
                            Gestionar esdeveniments <i class="bi bi-arrow-right-short ms-1"></i>
                        </span>
                    </div>
                </div>
            </a>
            
            {{-- FEEDBACK --}}
            <a href="{{ route('admin.feedback.index') }}" class="block transition-transform hover:scale-[1.02]">
                <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 p-4 rounded-lg border border-emerald-200 hover:border-emerald-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-emerald-800">{{ __('site.Feedback') }}</p>
                            <p class="text-2xl font-bold text-emerald-900">{{ $stats['total_feedback'] ?? 0 }}</p>
                        </div>
                        <div class="p-2 bg-emerald-200 rounded-lg">
                            <i class="bi bi-chat-left-text text-emerald-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="mt-2 grid grid-cols-2 gap-1 text-xs">
                        <span class="text-emerald-700">Pendents: {{ $stats['pending_feedback'] ?? 0 }}</span>
                        <span class="text-emerald-700">Respost: {{ $stats['responded_feedback'] ?? 0 }}</span>
                    </div>
                    <div class="mt-3 pt-2 border-t border-emerald-200">
                        <span class="text-xs text-emerald-600 hover:text-emerald-800 flex items-center">
                            Gestionar feedback <i class="bi bi-arrow-right-short ms-1"></i>
                        </span>
                    </div>
                </div>
            </a>
            
        </div>
    </div>
    @endif
    
    {{-- SECCIÓ 3: ACCIONS RÀPIDES --}}
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
            <i class="bi bi-lightning-charge me-2"></i>
            {{ __('Accions Ràpides') }}
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            
            {{-- AFEGIR USUARI --}}
            <a href="{{ route('admin.users.create') }}" class="block">
                <div class="bg-blue-50 border border-blue-200 hover:bg-blue-100 p-4 rounded-lg transition-colors">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg me-3">
                            <i class="bi bi-person-plus text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-blue-800">{{ __('Afegir Usuari') }}</h3>
                            <p class="text-sm text-blue-600">Crear nou usuari al sistema</p>
                        </div>
                    </div>
                </div>
            </a>
            
            {{-- AFEGIR CURS --}}
            <a href="{{ route('campus.courses.create') }}" class="block">
                <div class="bg-green-50 border border-green-200 hover:bg-green-100 p-4 rounded-lg transition-colors">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg me-3">
                            <i class="bi bi-journal-plus text-green-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-green-800">{{ __('Afegir Curs') }}</h3>
                            <p class="text-sm text-green-600">Crear nou curs al campus</p>
                        </div>
                    </div>
                </div>
            </a>
            
            {{-- AFEGIR TEMPORADA --}}
            <a href="{{ route('campus.seasons.create') }}" class="block">
                <div class="bg-cyan-50 border border-cyan-200 hover:bg-cyan-100 p-4 rounded-lg transition-colors">
                    <div class="flex items-center">
                        <div class="p-2 bg-cyan-100 rounded-lg me-3">
                            <i class="bi bi-calendar-plus text-cyan-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-cyan-800">{{ __('Afegir Temporada') }}</h3>
                            <p class="text-sm text-cyan-600">Crear nova temporada acadèmica</p>
                        </div>
                    </div>
                </div>
            </a>
            
        </div>
    </div>
</div>