{{-- resources/views/components/dashboard/widgets/secretaria_documents.blade.php --}}

@isset($stats['total_documents'])
<div class="bg-white p-6 rounded-lg shadow-md">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
        <i class="bi bi-file-earmark-text text-purple-600 me-2"></i>
        Documents de Secretaria
    </h3>
    
    <div class="grid grid-cols-2 gap-4">
        <div class="text-center">
            <div class="text-2xl font-bold text-purple-600">{{ $stats['total_documents'] ?? 0 }}</div>
            <div class="text-sm text-gray-600">Documents totals</div>
        </div>
        <div class="text-center">
            <div class="text-2xl font-bold text-blue-600">{{ count($stats['recent_documents'] ?? []) }}</div>
            <div class="text-sm text-gray-600">Pujats recentment</div>
        </div>
        <div class="text-center">
            <div class="text-2xl font-bold text-green-600">{{ $stats['total_downloads'] ?? 0 }}</div>
            <div class="text-sm text-gray-600">Descarregues (30d)</div>
        </div>
        <div class="text-center">
            <div class="text-2xl font-bold text-orange-600">{{ count($stats['documents_by_category'] ?? []) }}</div>
            <div class="text-sm text-gray-600">Categories</div>
        </div>
    </div>
    
    <div class="mt-4">
        <a href="{{ route('campus.documents.index') }}" class="text-purple-600 hover:text-purple-800 text-sm font-medium">
            Gestionar Documents ->
        </a>
    </div>
</div>
@endif
