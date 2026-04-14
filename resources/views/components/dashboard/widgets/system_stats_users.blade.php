{{-- Widget System Stats: Users --}}
@isset($stats['total_users'])
<div class="bg-white p-6 rounded-lg shadow-md">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
        <i class="bi bi-people-fill text-blue-600 me-2"></i>
        Usuaris del Sistema
    </h3>
    
    <div class="grid grid-cols-2 gap-4">
        <div class="text-center">
            <div class="text-2xl font-bold text-blue-700">
                {{ $stats['total_users'] ?? 0 }}
            </div>
            <div class="text-sm text-gray-600">Total Usuaris</div>
        </div>
        
        <div class="text-center">
            <div class="text-2xl font-bold text-green-700">
                {{ $stats['active_users'] ?? 0 }}
            </div>
            <div class="text-sm text-gray-600">Actius</div>
        </div>
        
        <div class="text-center">
            <div class="text-2xl font-bold text-orange-700">
                {{ $stats['teacher_count'] ?? 0 }}
            </div>
            <div class="text-sm text-gray-600">Professors</div>
        </div>
        
        <div class="text-center">
            <div class="text-2xl font-bold text-purple-700">
                {{ $stats['student_count'] ?? 0 }}
            </div>
            <div class="text-sm text-gray-600">Estudiants</div>
        </div>
    </div>
    
    <div class="mt-4">
        <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
            Gestionar Usuaris ->
        </a>
    </div>
</div>
@endisset
