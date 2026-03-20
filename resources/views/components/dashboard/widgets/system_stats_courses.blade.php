{{-- Widget System Stats: Courses --}}
<div class="bg-white p-6 rounded-lg shadow-md">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
        <i class="bi bi-book-fill text-green-600 me-2"></i>
        Cursos del Sistema
    </h3>
    
    <div class="grid grid-cols-2 gap-4">
        <div class="text-center">
            <div class="text-2xl font-bold text-green-700">
                {{ $stats['total_courses'] ?? 0 }}
            </div>
            <div class="text-sm text-gray-600">Total Cursos</div>
        </div>
        
        <div class="text-center">
            <div class="text-2xl font-bold text-blue-700">
                {{ $stats['active_courses'] ?? 0 }}
            </div>
            <div class="text-sm text-gray-600">Actius</div>
        </div>
        
        <div class="text-center">
            <div class="text-2xl font-bold text-orange-700">
                {{ $stats['inactive_courses'] ?? 0 }}
            </div>
            <div class="text-sm text-gray-600">Inactius</div>
        </div>
        
        <div class="text-center">
            <div class="text-2xl font-bold text-purple-700">
                {{ $stats['categories_with_courses'] ?? 0 }}
            </div>
            <div class="text-sm text-gray-600">Categories</div>
        </div>
    </div>
    
    <div class="mt-4">
        <a href="{{ route('campus.courses.index') }}" class="text-green-600 hover:text-green-800 text-sm font-medium">
            Gestionar Cursos →
        </a>
    </div>
</div>
