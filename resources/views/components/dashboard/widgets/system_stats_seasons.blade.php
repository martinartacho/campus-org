{{-- Widget System Stats: Seasons --}}
<div class="bg-white p-6 rounded-lg shadow-md">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
        <i class="bi bi-calendar-fill text-indigo-600 me-2"></i>
        Temporades Acadèmiques
    </h3>
    
    <div class="grid grid-cols-2 gap-4">
        <div class="text-center">
            <div class="text-2xl font-bold text-indigo-700">
                {{ $stats['total_seasons'] ?? 0 }}
            </div>
            <div class="text-sm text-gray-600">Total Temporades</div>
        </div>
        
        <div class="text-center">
            <div class="text-2xl font-bold text-green-700">
                {{ $stats['current_season'] ? 'Activa' : 'No configurada' }}
            </div>
            <div class="text-sm text-gray-600">Temporada Actual</div>
        </div>
        
        <div class="text-center">
            <div class="text-2xl font-bold text-blue-700">
                {{ $stats['upcoming_seasons'] ?? 0 }}
            </div>
            <div class="text-sm text-gray-600">Properes</div>
        </div>
        
        <div class="text-center">
            <div class="text-2xl font-bold text-orange-700">
                {{ $stats['past_seasons'] ?? 0 }}
            </div>
            <div class="text-sm text-gray-600">Passades</div>
        </div>
    </div>
    
    <div class="mt-4">
        <a href="{{ route('campus.seasons.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
            Gestionar Temporades →
        </a>
    </div>
</div>
