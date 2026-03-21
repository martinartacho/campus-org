@extends('campus.shared.layout')

@section('title', 'Editar Document - ' . $document->title)

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <!-- Header -->
    <div class="mb-8">
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li>
                    <a href="{{ route('campus.documents.index') }}" class="text-gray-500 hover:text-gray-700">
                        <i class="bi bi-house-door mr-2"></i>
                        Documents
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="bi bi-chevron-right text-gray-400"></i>
                        <span class="ml-2 text-gray-500">{{ $document->category->name }}</span>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="bi bi-chevron-right text-gray-400"></i>
                        <span class="ml-2 text-gray-900 font-medium">{{ Str::limit($document->title, 50) }}</span>
                    </div>
                </li>
            </ol>
        </nav>
        
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    <i class="{{ $document->file_icon }} text-blue-600 mr-3"></i>
                    Editar Document
                </h1>
                @if($document->reference)
                    <p class="text-gray-600">Referència: {{ $document->reference }}</p>
                @endif
            </div>
            
            <div class="flex space-x-2">
                <a href="{{ route('campus.documents.show', $document) }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <i class="bi bi-eye mr-2"></i>
                    Veure
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white shadow rounded-lg">
        <form action="{{ route('campus.documents.update', $document) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Informació Bàsica -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Informació Bàsica</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Títol -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Títol *</label>
                            <input type="text" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title', $document->title) }}"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <!-- Referència -->
                        <div>
                            <label for="reference" class="block text-sm font-medium text-gray-700 mb-2">Referència</label>
                            <input type="text" 
                                   id="reference" 
                                   name="reference" 
                                   value="{{ old('reference', $document->reference) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    
                    <!-- Descripció -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descripció</label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $document->description) }}</textarea>
                    </div>
                </div>

                <!-- Metadades -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Metadades</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Categoria -->
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Categoria *</label>
                            <select id="category_id" 
                                    name="category_id" 
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            @if($document->category_id == $category->id) selected @endif>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Data del document -->
                        <div>
                            <label for="document_date" class="block text-sm font-medium text-gray-700 mb-2">Data del document</label>
                            <input type="date" 
                                   id="document_date" 
                                   name="document_date" 
                                   value="{{ old('document_date', $document->document_date?->format('Y-m-d')) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <!-- Etiquetes -->
                        <div>
                            <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">Etiquetes</label>
                            <input type="text" 
                                   id="tags" 
                                   name="tags" 
                                   value="{{ old('tags', $document->tags) }}"
                                   placeholder="Separades per comes"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Permisos -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Permisos d'accés</h2>
                    
                    <div class="space-y-4">
                        <!-- Públic -->
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       id="is_public" 
                                       name="is_public" 
                                       value="1"
                                       @if(old('is_public', $document->is_public)) checked @endif
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Document públic</span>
                            </label>
                            <p class="mt-1 text-sm text-gray-500">
                                Si es marca com a públic, tots els usuaris podran veure aquest document.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Botons -->
                <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                    <a href="{{ route('campus.documents.show', $document) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <i class="bi bi-arrow-left mr-2"></i>
                        Cancel·lar
                    </a>
                    
                    <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="bi bi-check-lg mr-2"></i>
                        Actualitzar Document
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Dropdown menu functionality
document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('editDropdown');
    
    if (dropdown && !dropdown.contains(e.target)) {
        dropdown.classList.toggle('hidden');
    }
});
</script>
@endsection
