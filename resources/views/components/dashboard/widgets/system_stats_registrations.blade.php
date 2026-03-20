{{-- Widget System Stats: Registrations --}}
<div class="bg-white p-6 rounded-lg shadow-md">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
        <i class="bi bi-person-check-fill text-purple-600 me-2"></i>
        Matriculacions
    </h3>
    
    <div class="grid grid-cols-2 gap-4">
        <div class="text-center">
            <div class="text-2xl font-bold text-purple-700">
                {{ $stats['total_registrations'] ?? 0 }}
            </div>
            <div class="text-sm text-gray-600">Total Matriculacions</div>
        </div>
        
        <div class="text-center">
            <div class="text-2xl font-bold text-green-700">
                {{ $stats['active_registrations'] ?? 0 }}
            </div>
            <div class="text-sm text-gray-600">Actives</div>
        </div>
        
        <div class="text-center">
            <div class="text-2xl font-bold text-blue-700">
                {{ $stats['completed_registrations'] ?? 0 }}
            </div>
            <div class="text-sm text-gray-600">Completades</div>
        </div>
        
        <div class="text-center">
            <div class="text-2xl font-bold text-orange-700">
                {{ $stats['pending_registrations'] ?? 0 }}
            </div>
            <div class="text-sm text-gray-600">Pendents</div>
        </div>
    </div>
    
    <div class="mt-4">
        <a href="{{ route('manager.registrations.index') }}" class="text-purple-600 hover:text-purple-800 text-sm font-medium">
            Gestionar Matriculacions →
        </a>
    </div>
</div>
