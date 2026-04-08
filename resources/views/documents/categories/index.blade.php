@extends('campus.shared.layout')

@section('title', 'Categories de Documents')

@section('breadcrumbs')
<li class="inline-flex items-center">
    <span class="text-gray-400">/</span>
    <a href="{{ route('campus.documents.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600">
        {{ __('Documents') }}
    </a>
</li>
<li class="inline-flex items-center">
    <span class="text-gray-400">/</span>
    <span class="ml-1 text-sm font-medium text-gray-500">{{ __('Categories') }}</span>
</li>
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                <i class="bi bi-folder-tree text-blue-600 mr-3"></i>
                Categories de Documents
            </h1>
            <p class="text-gray-600">Gestiona les categories del repositori documental</p>
        </div>
        @if(true)
            <a href="{{ route('campus.documents.categories.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                <i class="bi bi-plus-circle mr-2"></i>
                Nova Categoria
            </a>
        @endif
    </div>

    <!-- Categories Tree -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($categories as $category)
                <div class="border border-gray-200 rounded-lg p-5 hover:border-blue-300 transition-colors">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900 mb-2">
                                <i class="bi bi-folder-fill text-blue-500 mr-2"></i>
                                {{ $category->name }}
                            </h3>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $category->is_active ? 'Activa' : 'Inactiva' }}
                            </span>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-500 font-medium">{{ $category->documents_count ?? 0 }}</div>
                            <div class="text-xs text-gray-400">documents</div>
                        </div>
                    </div>
                    
                    @if($category->description)
                        <p class="text-sm text-gray-600 mb-4">{{ $category->description }}</p>
                    @endif
                    
                    @if($category->children->count() > 0)
                        <div class="mb-4">
                            <p class="text-xs text-gray-500 mb-2">Subcategories:</p>
                            <div class="flex flex-wrap gap-1">
                                @foreach($category->children->take(3) as $child)
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-gray-100 text-gray-700">
                                        <i class="bi bi-folder text-gray-500 mr-1"></i>
                                        {{ $child->name }}
                                    </span>
                                @endforeach
                                @if($category->children->count() > 3)
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-gray-100 text-gray-700">
                                        +{{ $category->children->count() - 3 }} más
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif
                    
                    <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                        <div class="flex space-x-2">
                            <a href="{{ route('campus.documents.categories.show', $category) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                <i class="bi bi-eye mr-1"></i>
                                Veure
                            </a>
                            <a href="{{ route('campus.documents.index', ['category' => $category->id]) }}" class="text-green-600 hover:text-green-800 text-sm font-medium">
                                <i class="bi bi-files mr-1"></i>
                                Documents
                            </a>
                        </div>
                        
                        @if(true)
                            <div class="flex space-x-2">
                                <a href="{{ route('campus.documents.categories.edit', $category) }}" class="text-gray-600 hover:text-gray-800 text-sm font-medium">
                                    <i class="bi bi-pencil mr-1"></i>
                                    Editar
                                </a>
                                
                                <form method="POST" action="{{ route('campus.documents.categories.toggle', $category) }}" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="text-sm font-medium {{ $category->is_active ? 'text-orange-600 hover:text-orange-800' : 'text-green-600 hover:text-green-800' }}">
                                        <i class="bi bi-{{ $category->is_active ? 'pause-circle' : 'play-circle' }} mr-1"></i>
                                        {{ $category->is_active ? 'Desactivar' : 'Activar' }}
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        
        @if($categories->count() === 0)
            <div class="text-center py-12">
                <i class="bi bi-folder-x text-gray-400 text-5xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No hi ha categories</h3>
                <p class="text-gray-500 mb-6">Comença creant la primera categoria per organitzar els teus documents.</p>
                @if(true)
                    <a href="{{ route('campus.documents.categories.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <i class="bi bi-plus-circle mr-2"></i>
                        Crear Primera Categoria
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
