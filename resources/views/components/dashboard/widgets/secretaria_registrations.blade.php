{{-- resources/views/components/dashboard/widgets/secretaria_registrations.blade.php --}}

@isset($stats['pending_registrations'])
<div class="bg-white p-6 rounded-lg shadow-md">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
        <i class="bi bi-person-check text-orange-600 me-2"></i>
        Matrícules (WP/WooCommerce)
    </h3>
    
    <div class="grid grid-cols-2 gap-4">
        <div class="text-center">
            <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending_registrations'] ?? 0 }}</div>
            <div class="text-sm text-gray-600">Pendents</div>
        </div>
        <div class="text-center">
            <div class="text-2xl font-bold text-green-600">{{ $stats['active_registrations'] ?? 0 }}</div>
            <div class="text-sm text-gray-600">Actives</div>
        </div>
        <div class="text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $stats['completed_registrations'] ?? 0 }}</div>
            <div class="text-sm text-gray-600">Completades</div>
        </div>
        <div class="text-center">
            <div class="text-2xl font-bold text-purple-600">
                {{ ($stats['pending_registrations'] ?? 0) + ($stats['active_registrations'] ?? 0) + ($stats['completed_registrations'] ?? 0) }}
            </div>
            <div class="text-sm text-gray-600">Total</div>
        </div>
    </div>
    
    <div class="mt-4">
        <a href="{{ route('campus.registrations.index') }}" class="text-orange-600 hover:text-orange-800 text-sm font-medium">
            Gestionar Matrícules ->
        </a>
    </div>
</div>
@endif
