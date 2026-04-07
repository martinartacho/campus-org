{{-- resources/views/components/dashboard/widgets/secretaria_documents.blade.php --}}

@isset($stats['total_documents'])
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
            <i class="bi bi-file-earmark-text text-purple-600 me-2"></i>
            Documents de Secretaria
        </h3>
        @if(auth()->user()->hasAnyRole(['admin', 'super-admin', 'secretaria']))
            <div class="flex space-x-2">
                <a href="{{ route('campus.documents.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <i class="bi bi-plus-circle mr-2"></i>
                    Nou Document
                </a>
            </div>
        @endif

        
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        
        {{-- DOCUMENTS TOTAL --}}
        <a href="{{ route('campus.documents.index') }}" class="block transition-transform hover:scale-[1.02]">
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-lg border border-purple-200 hover:border-purple-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-purple-800">Documents totals</p>
                        <p class="text-2xl font-bold text-purple-900">{{ $stats['total_documents'] ?? 0 }}</p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="bi bi-file-earmark-text text-purple-600 text-xl"></i>
                    </div>
                </div>
                
                @if(isset($stats['documents_by_category']) && count($stats['documents_by_category']) > 0)
                    <div class="mt-2 grid grid-cols-2 gap-1 text-xs">
                        @foreach($stats['documents_by_category'] as $category => $count)
                            @if($loop->first)
                                <span class="text-purple-700">{{ $category }}: {{ $count }}</span>
                            @endif
                            @if($loop->index == 1)
                                <span class="text-purple-700">+{{ count($stats['documents_by_category']) - 1 }} categories</span>
                                @break
                            @endif
                        @endforeach
                    </div>
                @endif
                
                <div class="mt-3 pt-2 border-t border-purple-200">
                    <span class="text-xs text-purple-600 hover:text-purple-800 flex items-center">
                        Gestionar documents <i class="bi bi-arrow-right-short ms-1"></i>
                    </span>
                </div>
            </div>
        </a>
        
        {{-- RECENT UPLOADS --}}
        @if(isset($stats['recent_documents']) && count($stats['recent_documents']) > 0)
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg border border-blue-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-800">Pujats recentment</p>
                        <p class="text-2xl font-bold text-blue-900">{{ count($stats['recent_documents']) }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="bi bi-upload text-blue-600 text-xl"></i>
                    </div>
                </div>
                
                @if(isset($stats['recent_documents'][0]))
                    <div class="mt-2 text-xs">
                        <span class="text-blue-700">Últim: {{ Str::limit($stats['recent_documents'][0]->title, 20) }}</span>
                    </div>
                @endif
                
                <div class="mt-3 pt-2 border-t border-blue-200">
                    <span class="text-xs text-blue-600">
                        Últims 5 documents
                    </span>
                </div>
            </div>
        @endif
        
        {{-- DOWNLOADS --}}
        @if(isset($stats['total_downloads']))
            <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-lg border border-green-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-800">Descarregues (30d)</p>
                        <p class="text-2xl font-bold text-green-900">{{ $stats['total_downloads'] }}</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="bi bi-download text-green-600 text-xl"></i>
                    </div>
                </div>
                
                <div class="mt-3 pt-2 border-t border-green-200">
                    <span class="text-xs text-green-600">
                        Últims 30 dies
                    </span>
                </div>
            </div>
        @endif
        
        {{-- CATEGORIES --}}
        <a href="{{ route('campus.documents.categories.index') }}" class="block transition-transform hover:scale-[1.02]">
            <div class="bg-gradient-to-br from-orange-50 to-orange-100 p-4 rounded-lg border border-orange-200 hover:border-orange-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-orange-800">Categories</p>
                        <p class="text-2xl font-bold text-orange-900">{{ count($stats['documents_by_category'] ?? []) }}</p>
                    </div>
                    <div class="bg-orange-100 p-3 rounded-full">
                        <i class="bi bi-folder text-orange-600 text-xl"></i>
                    </div>
                </div>
                
                @if(isset($stats['documents_by_category']) && count($stats['documents_by_category']) > 0)
                    @php
                        $mostCommonCategory = collect($stats['documents_by_category'])->sortByDesc(function($count) {
                            return $count;
                        })->keys()->first();
                        $totalDocsInCategories = collect($stats['documents_by_category'])->sum();
                    @endphp
                    <div class="mt-2 text-xs">
                        <span class="text-orange-700">Més comuna: {{ $mostCommonCategory ?? 'N/A' }}</span>
                    </div>
                    <div class="mt-1 text-xs">
                        <span class="text-orange-600">{{ $totalDocsInCategories }} documents categoritzats</span>
                    </div>
                @else
                    <div class="mt-2 text-xs">
                        <span class="text-orange-700">Sense categories definides</span>
                    </div>
                    <div class="mt-1 text-xs">
                        <span class="text-orange-600">Crea categories per organitzar</span>
                    </div>
                @endif
                
                <div class="mt-3 pt-2 border-t border-orange-200">
                    <span class="text-xs text-orange-600 hover:text-orange-800 flex items-center">
                        Gestionar categories <i class="bi bi-arrow-right-short ms-1"></i>
                    </span>
                </div>
            </div>
        </a>
        
    </div>
    
    {{-- DETALLES ADICIONALES --}}
    @if(isset($stats['recent_documents']) && count($stats['recent_documents']) > 0)
        <div class="mt-6 pt-4 border-t border-gray-200">
            <h4 class="text-sm font-medium text-gray-700 mb-3">Documents recents</h4>
            <div class="space-y-2">
                @foreach($stats['recent_documents'] as $document)
                    <div class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $document->title }}</p>
                            <p class="text-xs text-gray-500">{{ $document->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <a href="{{ route('campus.documents.download', $document) }}" 
                           class="ml-3 text-purple-600 hover:text-purple-800 text-sm p-1">
                            <i class="bi bi-download"></i>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endif
