@extends('layouts.app')

@section('title', 'Documentació del Campus')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                <i class="bi bi-file-earmark-text text-blue-600 mr-3"></i>
                Documentació del Campus
            </h1>
            <p class="text-gray-600">
                Repositori documental intern del campus
            </p>
        </div>
        @if(auth()->user()->hasAnyRole(['admin', 'super-admin', 'secretaria']))
            <a href="{{ route('campus.documents.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                <i class="bi bi-plus-circle mr-2"></i>
                Nou Document
            </a>
        @endif
    </div>

    <!-- Filtres -->
    <div class="bg-white shadow rounded-lg p-6 mb-8">
        <form method="GET" action="{{ route('campus.documents.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Cercar -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cercar</label>
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               value="{{ $searchTerm ?? '' }}"
                               placeholder="Títol, descripció, etiquetes..."
                               class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="bi bi-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>

                <!-- Categoria -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Categoria</label>
                    <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Totes les categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $selectedCategory == $category->id ? 'selected' : '' }}>
                                {{ str_repeat('→ ', $category->parent_id ? 1 : 0) }} {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Any -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Any</label>
                    <select name="year" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Tots els anys</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Botons -->
                <div class="flex items-end space-x-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        <i class="bi bi-funnel mr-2"></i>
                        Filtrar
                    </button>
                    <a href="{{ route('campus.documents.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                        <i class="bi bi-x-circle mr-2"></i>
                        Netejar
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Categories Tree -->
    @if(!$selectedCategory && !$searchTerm && !$selectedYear)
        <div class="bg-white shadow rounded-lg p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">
                <i class="bi bi-folder-tree text-blue-600 mr-2"></i>
                Categories
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($categories as $category)
                    <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="font-medium text-gray-900">
                                <i class="bi bi-folder-fill text-blue-500 mr-2"></i>
                                {{ $category->name }}
                            </h3>
                            <span class="text-sm text-gray-500">{{ $category->documents_count ?? 0 }} docs</span>
                        </div>
                        <p class="text-sm text-gray-600 mb-3">{{ $category->description }}</p>
                        <a href="{{ route('campus.documents.index', ['category' => $category->id]) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Veure documents →
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Documents List -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">
                <i class="bi bi-files text-blue-600 mr-2"></i>
                Documents ({{ $documents->total() }})
            </h2>
        </div>
        
        @if($documents->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($documents as $document)
                    <div class="p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center mb-2">
                                    <i class="{{ $document->file_icon }} text-blue-500 mr-3 text-xl"></i>
                                    <h3 class="text-lg font-medium text-gray-900">
                                        <a href="{{ route('campus.documents.show', $document) }}" class="hover:text-blue-600">
                                            {{ $document->title }}
                                        </a>
                                    </h3>
                                </div>
                                
                                @if($document->description)
                                    <p class="text-gray-600 mb-2">{{ Str::limit($document->description, 150) }}</p>
                                @endif
                                
                                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                                    <span>
                                        <i class="bi bi-folder mr-1"></i>
                                        {{ $document->category->name }}
                                    </span>
                                    @if($document->document_date)
                                        <span>
                                            <i class="bi bi-calendar mr-1"></i>
                                            {{ $document->document_date->format('d/m/Y') }}
                                        </span>
                                    @endif
                                    <span>
                                        <i class="bi bi-person mr-1"></i>
                                        {{ $document->uploader->name }}
                                    </span>
                                    <span>
                                        <i class="bi bi-clock mr-1"></i>
                                        {{ $document->created_at->format('d/m/Y') }}
                                    </span>
                                    <span>
                                        <i class="bi bi-download mr-1"></i>
                                        {{ $document->download_count }}
                                    </span>
                                    <span>
                                        <i class="bi bi-file-earmark mr-1"></i>
                                        {{ $document->formatted_file_size }}
                                    </span>
                                </div>
                                
                                @if($document->tags)
                                    <div class="flex flex-wrap gap-1 mt-2">
                                        @foreach(explode(',', $document->tags) as $tag)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ trim($tag) }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            
                            <div class="flex items-center space-x-2 ml-4">
                                <a href="{{ route('campus.documents.download', $document) }}" 
                                   class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    <i class="bi bi-download mr-2"></i>
                                    Descarregar
                                </a>
                                
                                @if(auth()->user()->hasAnyRole(['admin', 'super-admin']) || $document->uploaded_by === auth()->id())
                                    <div class="relative">
                                        <button class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 hidden">
                                            <div class="py-1">
                                                <a href="{{ route('campus.documents.edit', $document) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <i class="bi bi-pencil mr-2"></i>Editar
                                                </a>
                                                <form action="{{ route('campus.documents.destroy', $document) }}" method="POST" onsubmit="return confirm('Estàs segur que vols eliminar aquest document?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                                        <i class="bi bi-trash mr-2"></i>Eliminar
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $documents->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <i class="bi bi-file-earmark text-gray-400 text-5xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No s'han trobat documents</h3>
                <p class="text-gray-500 mb-4">
                    @if($selectedCategory || $searchTerm || $selectedYear)
                        Prova amb altres criteris de cerca.
                    @else
                        Encara no hi ha documents disponibles.
                    @endif
                </p>
                @if($selectedCategory || $searchTerm || $selectedYear)
                    <a href="{{ route('campus.documents.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <i class="bi bi-x-circle mr-2"></i>
                        Netejar
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

<script>
// Dropdown menu functionality
document.addEventListener('click', function(e) {
    if (e.target.closest('.relative')) {
        const dropdown = e.target.closest('.relative');
        const menu = dropdown.querySelector('.absolute');
        
        if (e.target.closest('button') || e.target.closest('a')) {
            e.stopPropagation();
            menu.classList.toggle('hidden');
        }
    } else {
        document.querySelectorAll('.absolute').forEach(menu => {
            menu.classList.add('hidden');
        });
    }
});
</script>
@endsection
