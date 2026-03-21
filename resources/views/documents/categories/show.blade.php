@extends('campus.shared.layout')

@section('title', $category->name)

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
                        <a href="{{ route('campus.documents.categories.index') }}" class="text-gray-500 hover:text-gray-700">
                            Categories
                        </a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="bi bi-chevron-right text-gray-400"></i>
                        <span class="ml-2 text-gray-900 font-medium">{{ $category->name }}</span>
                    </div>
                </li>
            </ol>
        </nav>
        
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    <i class="bi bi-folder-fill text-blue-600 mr-3"></i>
                    {{ $category->name }}
                </h1>
                <p class="text-gray-600">{{ $category->description }}</p>
            </div>
            
            <div class="flex space-x-2">
                @if(auth()->user()->hasAnyRole(['admin', 'super-admin', 'secretaria']))
                    <a href="{{ route('campus.documents.categories.edit', $category) }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <i class="bi bi-pencil mr-2"></i>
                        Editar
                    </a>
                @endif
                
                <a href="{{ route('campus.documents.index', ['category' => $category->id]) }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <i class="bi bi-files mr-2"></i>
                    Veure Documents
                </a>
            </div>
        </div>
    </div>

    <!-- Category Information -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Main Info -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informació de la Categoria</h2>
                
                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Slug</h3>
                        <p class="text-gray-900">{{ $category->slug }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Descripció</h3>
                        <p class="text-gray-900">{{ $category->description ?: 'Sense descripció' }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Categoria Pare</h3>
                        <p class="text-gray-900">
                            @if($category->parent)
                                <a href="{{ route('campus.documents.categories.show', $category->parent) }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $category->parent->name }}
                                </a>
                            @else
                                Categoria arrel (sense pare)
                            @endif
                        </p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Ordre de visualització</h3>
                        <p class="text-gray-900">{{ $category->sort_order }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Estat</h3>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $category->is_active ? 'Activa' : 'Inactiva' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div>
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Estadístiques</h2>
                
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Documents totals</span>
                        <span class="text-lg font-semibold text-gray-900">{{ $category->documents_count ?? 0 }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Subcategories</span>
                        <span class="text-lg font-semibold text-gray-900">{{ $category->children->count() }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Data de creació</span>
                        <span class="text-sm text-gray-900">{{ $category->created_at->format('d/m/Y') }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Última actualització</span>
                        <span class="text-sm text-gray-900">{{ $category->updated_at->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Subcategories -->
    @if($category->children->count() > 0)
        <div class="bg-white shadow rounded-lg p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Subcategories</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($category->children as $child)
                    <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="font-medium text-gray-900">
                                <i class="bi bi-folder text-blue-500 mr-2"></i>
                                {{ $child->name }}
                            </h3>
                            <span class="text-sm text-gray-500">{{ $child->documents_count ?? 0 }} docs</span>
                        </div>
                        <p class="text-sm text-gray-600 mb-3">{{ $child->description }}</p>
                        <div class="flex justify-between items-center">
                            <a href="{{ route('campus.documents.categories.show', $child) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Veure detall
                            </a>
                            @if(auth()->user()->hasAnyRole(['admin', 'super-admin', 'secretaria']))
                                <a href="{{ route('campus.documents.categories.edit', $child) }}" class="text-gray-600 hover:text-gray-800 text-sm font-medium">
                                    <i class="bi bi-pencil mr-1"></i>
                                    Editar
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Recent Documents -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Documents Recents</h2>
            <a href="{{ route('campus.documents.index', ['category' => $category->id]) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                Veure tots →
            </a>
        </div>
        
        @if($category->documents->count() > 0)
            <div class="space-y-3">
                @foreach($category->documents->take(5) as $document)
                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <div class="flex items-center">
                            <i class="{{ $document->file_icon }} text-blue-600 mr-3"></i>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">{{ $document->title }}</h4>
                                <p class="text-xs text-gray-500">{{ $document->file_name }} • {{ $document->file_size_formatted ?? $document->file_size . ' bytes' }}</p>
                            </div>
                        </div>
                        <a href="{{ route('campus.documents.download', $document) }}" class="text-blue-600 hover:text-blue-800">
                            <i class="bi bi-download"></i>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <i class="bi bi-file-earmark text-gray-400 text-4xl mb-2"></i>
                <p class="text-gray-500">No hi ha documents en aquest categoria</p>
                @if(auth()->user()->hasAnyRole(['admin', 'super-admin', 'secretaria']))
                    <a href="{{ route('campus.documents.create', ['category' => $category->id]) }}" class="mt-2 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <i class="bi bi-plus-circle mr-2"></i>
                        Crear Primer Document
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
