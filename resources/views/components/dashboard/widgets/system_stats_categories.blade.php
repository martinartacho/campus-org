{{-- Widget System Stats: Categories --}}
<div class="bg-white p-6 rounded-lg shadow-md">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
        <i class="bi bi-tags-fill text-orange-600 me-2"></i>
        Categories del Sistema
    </h3>
    
    <div class="grid grid-cols-2 gap-4">
        <div class="text-center">
            <div class="text-2xl font-bold text-orange-700">
                {{ $stats['total_categories'] ?? 0 }}
            </div>
            <div class="text-sm text-gray-600">Total Categories</div>
        </div>
        
        <div class="text-center">
            <div class="text-2xl font-bold text-green-700">
                {{ $stats['categories_with_courses'] ?? 0 }}
            </div>
            <div class="text-sm text-gray-600">Amb Cursos</div>
        </div>
        
        <div class="text-center">
            <div class="text-2xl font-bold text-blue-700">
                {{ $stats['empty_categories'] ?? 0 }}
            </div>
            <div class="text-sm text-gray-600">Buides</div>
        </div>
        
        <div class="text-center">
            <div class="text-2xl font-bold text-purple-700">
                {{ $stats['active_categories'] ?? 0 }}
            </div>
            <div class="text-sm text-gray-600">Actives</div>
        </div>
    </div>
    
    <div class="mt-4">
        <a href="{{ route('campus.categories.index') }}" class="text-orange-600 hover:text-orange-800 text-sm font-medium">
            Gestionar Categories →
        </a>
    </div>
</div>
