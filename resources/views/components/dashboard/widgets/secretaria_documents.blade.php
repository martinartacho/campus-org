{{-- resources/views/components/dashboard/widgets/secretaria_documents.blade.php --}}

@isset($stats['total_documents'])
<div class="bg-white shadow rounded-lg p-6 border-l-4 border-purple-500">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">
            <i class="bi bi-file-earmark-text text-purple-600 mr-2"></i>
            Documents de Secretaria
        </h3>
        <div class="flex items-center">
            <span class="text-sm text-gray-500 mr-2">Total:</span>
            <span class="text-2xl font-bold text-purple-900">{{ $stats['total_documents'] ?? 0 }}</span>
        </div>
    </div>
    
    <div class="space-y-3">
        <!-- Documents per Category -->
        @if(isset($stats['documents_by_category']))
            @foreach($stats['documents_by_category'] as $category => $count)
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-600">{{ $category }}</span>
                    <span class="text-sm font-medium text-gray-900">{{ $count }}</span>
                </div>
            @endforeach
        @endif
        
        <!-- Recent Uploads -->
        @if(isset($stats['recent_documents']) && count($stats['recent_documents']) > 0)
            <div class="mt-4 pt-4 border-t border-gray-200">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Pujats Recentment</h4>
                <div class="space-y-2">
                    @foreach($stats['recent_documents'] as $document)
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $document->title }}</p>
                                <p class="text-xs text-gray-500">{{ $document->created_at->format('d/m/Y') }}</p>
                            </div>
                            <a href="{{ route('campus.documents.download', $document) }}" 
                               class="ml-2 text-purple-600 hover:text-purple-800 text-sm">
                                <i class="bi bi-download"></i>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        
        <!-- Download Statistics -->
        @if(isset($stats['total_downloads']))
            <div class="mt-4 pt-4 border-t border-gray-200">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Descarregues (30 dies)</span>
                    <span class="text-sm font-medium text-gray-900">{{ $stats['total_downloads'] }}</span>
                </div>
            </div>
        @endif
    </div>
    
    <div class="mt-4 pt-4 border-t border-gray-200">
        <a href="{{ route('campus.documents.index') }}" 
           class="inline-flex items-center text-purple-600 hover:text-purple-800 text-sm font-medium">
            Veure tots els documents
            <i class="bi bi-arrow-right ml-1"></i>
        </a>
    </div>
</div>
@endif
