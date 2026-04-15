{{-- resources/views/components/dashboard/widgets/secretaria_certificates.blade.php --}}

@isset($stats['total_certificates'])
<div class="bg-white p-6 rounded-lg shadow-md">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
        <i class="bi bi-award text-green-600 me-2"></i>
        Certificats
    </h3>
    
    <div class="grid grid-cols-2 gap-4">
        <div class="text-center">
            <div class="text-2xl font-bold text-green-600">{{ $stats['total_certificates'] ?? 0 }}</div>
            <div class="text-sm text-gray-600">Certificats totals</div>
        </div>
        <div class="text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $stats['monthly_certificates'] ?? 0 }}</div>
            <div class="text-sm text-gray-600">Aquest mes</div>
        </div>
        <div class="text-center">
            <div class="text-2xl font-bold text-purple-600">{{ $stats['yearly_certificates'] ?? 0 }}</div>
            <div class="text-sm text-gray-600">Aquest any</div>
        </div>
        <div class="text-center">
            <div class="text-2xl font-bold text-orange-600">{{ $stats['by_type_certificates'] ?? 0 }}</div>
            <div class="text-sm text-gray-600">Per tipus</div>
        </div>
    </div>
    
    <div class="mt-4">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
            <div class="flex items-center">
                <i class="bi bi-info-circle text-blue-600 me-2"></i>
                <span class="text-sm text-blue-800">
                    Mòdul en desenvolupament - Funcions de creació i gestió disponibles properament
                </span>
            </div>
        </div>
    </div>
</div>
@endif
