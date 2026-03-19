{{-- resources/views/components/dashboard/system-stats.blade.php --}}

@if(!empty($stats))
<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
        <i class="bi bi-graph-up me-2"></i>
        {{ __('Estadístiques del Sistema') }} ( path: resources/views/components/dashboard/system-stats.blade.php )
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
                <div class="mt-2 grid grid-cols-2 gap-1 text-xs">
                    <span class="text-blue-700">Actius: {{ $stats['active_users'] ?? 0 }}</span>
                    <span class="text-blue-700">Nous: {{ $stats['new_users'] ?? 0 }}</span>
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
                    <span class="text-green-700">Ple: {{ $stats['full_courses'] ?? 0 }}</span>
                </div>
                <div class="mt-3 pt-2 border-t border-green-200">
                    <span class="text-xs text-green-600 hover:text-green-800 flex items-center">
                        Gestionar cursos <i class="bi bi-arrow-right-short ms-1"></i>
                    </span>
                </div>
            </div>
        </a>
        
        {{-- PROFESSORS --}}
        <a href="{{ route('campus.teachers.index') }}" class="block transition-transform hover:scale-[1.02]">
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-lg border border-purple-200 hover:border-purple-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-purple-800">{{ __('Professors') }}</p>
                        <p class="text-2xl font-bold text-purple-900">{{ $stats['total_teachers'] ?? 0 }}</p>
                    </div>
                    <div class="p-2 bg-purple-200 rounded-lg">
                        <i class="bi bi-person-workspace text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-2 grid grid-cols-2 gap-1 text-xs">
                    <span class="text-purple-700">Actius: {{ $stats['active_teachers'] ?? 0 }}</span>
                    <span class="text-purple-700">Pendents: {{ $stats['pending_teachers'] ?? 0 }}</span>
                </div>
                <div class="mt-3 pt-2 border-t border-purple-200">
                    <span class="text-xs text-purple-600 hover:text-purple-800 flex items-center">
                        Gestionar professors <i class="bi bi-arrow-right-short ms-1"></i>
                    </span>
                </div>
            </div>
        </a>
        
        {{-- ESTUDIANTS --}}
        <a href="{{ route('campus.students.index') }}" class="block transition-transform hover:scale-[1.02]">
            <div class="bg-gradient-to-br from-amber-50 to-amber-100 p-4 rounded-lg border border-amber-200 hover:border-amber-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-amber-800">{{ __('Estudiants') }}</p>
                        <p class="text-2xl font-bold text-amber-900">{{ $stats['total_students'] ?? 0 }}</p>
                    </div>
                    <div class="p-2 bg-amber-200 rounded-lg">
                        <i class="bi bi-mortarboard text-amber-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-2 grid grid-cols-2 gap-1 text-xs">
                    <span class="text-amber-700">Actives: {{ $stats['active_registrations'] ?? 0 }}</span>
                    <span class="text-amber-700">Completades: {{ $stats['completed_registrations'] ?? 0 }}</span>
                </div>
                <div class="mt-3 pt-2 border-t border-amber-200">
                    <span class="text-xs text-amber-600 hover:text-amber-800 flex items-center">
                        Gestionar estudiants <i class="bi bi-arrow-right-short ms-1"></i>
                    </span>
                </div>
            </div>
        </a>
        
    </div>
    
    {{-- Segona fila --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
        
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
                <div class="mt-2 grid grid-cols-2 gap-1 text-xs">
                    <span class="text-cyan-700">Actual: {{ $stats['current_season'] ?? 0 }}</span>
                    <span class="text-cyan-700">Passades: {{ $stats['past_seasons'] ?? 0 }}</span>
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
                        <p class="text-sm font-medium text-red-800">{{ __('Esdeveniments') }}</p>
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
                    <span class="text-emerald-700">Resolts: {{ $stats['resolved_feedback'] ?? 0 }}</span>
                </div>
                <div class="mt-3 pt-2 border-t border-emerald-200">
                    <span class="text-xs text-emerald-600 hover:text-emerald-800 flex items-center">
                        Gestionar feedback <i class="bi bi-arrow-right-short ms-1"></i>
                    </span>
                </div>
            </div>
        </a>
        
        {{-- SUPORT --}}
        <a href="{{ route('admin.support-requests.index') }}" class="block transition-transform hover:scale-[1.02]">
            <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 p-4 rounded-lg border border-indigo-200 hover:border-indigo-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-indigo-800">Suport</p>
                        <p class="text-2xl font-bold text-indigo-900">{{ App\Models\SupportRequest::count() }}</p>
                    </div>
                    <div class="p-2 bg-indigo-200 rounded-lg">
                        <i class="bi bi-headset text-indigo-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-2 grid grid-cols-2 gap-1 text-xs">
                    <span class="text-indigo-700">Pendents: {{ App\Models\SupportRequest::where('status', 'pending')->count() }}</span>
                    <span class="text-indigo-700">Crítics: {{ App\Models\SupportRequest::where('urgency', 'critical')->where('status', '!=', 'resolved')->count() }}</span>
                </div>
                <div class="mt-3 pt-2 border-t border-indigo-200">
                    <span class="text-xs text-indigo-600 hover:text-indigo-800 flex items-center">
                        Gestionar suport <i class="bi bi-arrow-right-short ms-1"></i>
                    </span>
                </div>
            </div>
        </a>
        
    </div>
</div>
@endif
