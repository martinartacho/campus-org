{{-- Widget System Stats: Events --}}
<div class="bg-white p-6 rounded-lg shadow-md">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
        <i class="bi bi-calendar-event-fill text-red-600 me-2"></i>
        Esdeveniments
    </h3>
    
    <div class="grid grid-cols-2 gap-4">
        <div class="text-center">
            <div class="text-2xl font-bold text-red-700">
                {{ $stats['total_events'] ?? 0 }}
            </div>
            <div class="text-sm text-gray-600">Total Esdeveniments</div>
        </div>
        
        <div class="text-center">
            <div class="text-2xl font-bold text-green-700">
                {{ $stats['upcoming_events'] ?? 0 }}
            </div>
            <div class="text-sm text-gray-600">Properes</div>
        </div>
        
        <div class="text-center">
            <div class="text-2xl font-bold text-blue-700">
                {{ $stats['past_events'] ?? 0 }}
            </div>
            <div class="text-sm text-gray-600">Passats</div>
        </div>
        
        <div class="text-center">
            <div class="text-2xl font-bold text-orange-700">
                {{ $stats['active_events'] ?? 0 }}
            </div>
            <div class="text-sm text-gray-600">Actius</div>
        </div>
    </div>
    
    <div class="mt-4">
        <a href="{{ route('admin.events.index') }}" class="text-red-600 hover:text-red-800 text-sm font-medium">
            Gestionar Esdeveniments →
        </a>
    </div>
</div>
