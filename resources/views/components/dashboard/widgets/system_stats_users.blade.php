{{-- Widget System Stats: Users --}}
@isset($stats['total_users'])
<div class="bg-white p-6 rounded-lg shadow-md">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
        <i class="bi bi-people-fill text-blue-600 me-2"></i>
        Usuaris del Sistema
    </h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        
        {{-- TOTAL USUARIS --}}
        <a href="{{ route('admin.users.index') }}" class="block transition-transform hover:scale-[1.02]">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg border border-blue-200 hover:border-blue-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-800">Total Usuaris</p>
                        <p class="text-2xl font-bold text-blue-900">{{ $stats['total_users'] ?? 0 }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
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
        
        {{-- ESTUDIANTS --}}
        @if(isset($stats['student_count']))
            <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-lg border border-green-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-800">Estudiants</p>
                        <p class="text-2xl font-bold text-green-900">{{ $stats['student_count'] }}</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="bi bi-mortarboard text-green-600 text-xl"></i>
                    </div>
                </div>
                
                <div class="mt-2 text-xs">
                    <span class="text-green-700">Registrats al sistema</span>
                </div>
                
                <div class="mt-3 pt-2 border-t border-green-200">
                    <span class="text-xs text-green-600">
                        Perfil estudiantil
                    </span>
                </div>
            </div>
        @endif
        
        {{-- PROFESSORS --}}
        @if(isset($stats['teacher_count']))
            <a href="{{ route('campus.teachers.index') }}" class="block transition-transform hover:scale-[1.02]">
                <div class="bg-gradient-to-br from-orange-50 to-orange-100 p-4 rounded-lg border border-orange-200 hover:border-orange-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-orange-800">Professors</p>
                            <p class="text-2xl font-bold text-orange-900">{{ $stats['teacher_count'] }}</p>
                        </div>
                        <div class="bg-orange-100 p-3 rounded-full">
                            <i class="bi bi-person-workspace text-orange-600 text-xl"></i>
                        </div>
                    </div>
                    
                    @if(isset($stats['active_teachers']))
                        <div class="mt-2 text-xs">
                            <span class="text-orange-700">{{ $stats['active_teachers'] }} actius</span>
                        </div>
                    @endif
                    
                    <div class="mt-3 pt-2 border-t border-orange-200">
                        <span class="text-xs text-orange-600 hover:text-orange-800 flex items-center">
                            Gestionar professorat <i class="bi bi-arrow-right-short ms-1"></i>
                        </span>
                    </div>
                </div>
            </a>
        @endif
        
        {{-- ADMINS --}}
        @if(isset($stats['admin_count']))
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-lg border border-purple-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-purple-800">Admins</p>
                        <p class="text-2xl font-bold text-purple-900">{{ $stats['admin_count'] }}</p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="bi bi-shield-check text-purple-600 text-xl"></i>
                    </div>
                </div>
                
                <div class="mt-2 text-xs">
                    <span class="text-purple-700">Accés complet</span>
                </div>
                
                <div class="mt-3 pt-2 border-t border-purple-200">
                    <span class="text-xs text-purple-600">
                        Administració
                    </span>
                </div>
            </div>
        @endif
        
    </div>
</div>
@endisset
