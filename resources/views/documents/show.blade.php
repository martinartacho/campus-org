@extends('layouts.app')

@section('title', $document->title)

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
                        <span class="ml-2 text-gray-900 font-medium">{{ $document->title }}</span>
                    </div>
                </li>
            </ol>
        </nav>
        
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    <i class="{{ $document->file_icon }} text-blue-600 mr-3"></i>
                    {{ $document->title }}
                </h1>
                @if($document->reference)
                    <p class="text-gray-600">Referència: {{ $document->reference }}</p>
                @endif
            </div>
            
            <div class="flex space-x-2">
                <a href="{{ route('campus.documents.download', $document) }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <i class="bi bi-download mr-2"></i>
                    Descarregar
                </a>
                
                @if(auth()->user()->hasAnyRole(['admin', 'super-admin']) || $document->uploaded_by === auth()->id())
                    <div class="relative">
                        <button class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <i class="bi bi-three-dots"></i>
                        </button>
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 hidden">
                            <div class="py-1">
                                <a href="{{ route('campus.documents.download', $document) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="bi bi-download mr-2"></i>Descarregar
                                </a>
                                
                                @if(auth()->user()->hasAnyRole(['admin', 'super-admin']) || $document->uploaded_by === auth()->id())
                                    <a href="{{ route('campus.documents.edit', $document) }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="bi bi-pencil mr-2"></i>
                                        Editar
                                    </a>
                                    
                                    <form action="{{ route('campus.documents.destroy', $document) }}" method="POST" onsubmit="return confirm('Estàs segur que vols eliminar aquest document?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                            <i class="bi bi-trash mr-2"></i>
                                            Eliminar
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Document Info -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Description -->
            @if($document->description)
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Descripció</h2>
                    <div class="prose prose-gray max-w-none">
                        {!! nl2br(e($document->description)) !!}
                    </div>
                </div>
            @endif

            <!-- File Info -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informació del Fitxer</h2>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Nom del fitxer</p>
                        <p class="font-medium">{{ $document->file_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Mida</p>
                        <p class="font-medium">{{ $document->formatted_file_size }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Tipus</p>
                        <p class="font-medium">{{ $document->file_type }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Descarregues</p>
                        <p class="font-medium">{{ $document->download_count }}</p>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="{{ route('campus.documents.download', $document) }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <i class="bi bi-download mr-2"></i>
                        Descarregar Fitxer
                    </a>
                </div>
            </div>

            <!-- Tags -->
            @if($document->tags)
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Etiquetes</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach(explode(',', $document->tags) as $tag)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                {{ trim($tag) }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Recent Downloads -->
            @if($recentDownloads->count() > 0)
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Descarregues Recents</h2>
                    <div class="space-y-3">
                        @foreach($recentDownloads as $download)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="bi bi-person-fill text-blue-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $download->user->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $download->downloaded_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                                <span class="text-sm text-gray-500">{{ $download->ip_address }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Metadata -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Metadades</h2>
                
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-500">Categoria</p>
                        <p class="font-medium">
                            <a href="{{ route('campus.documents.index', ['category' => $document->category->id]) }}" class="text-blue-600 hover:text-blue-800">
                                {{ $document->category->name }}
                            </a>
                        </p>
                    </div>
                    
                    @if($document->document_date)
                        <div>
                            <p class="text-sm text-gray-500">Data del document</p>
                            <p class="font-medium">{{ $document->document_date->format('d/m/Y') }}</p>
                        </div>
                    @endif
                    
                    <div>
                        <p class="text-sm text-gray-500">Pujat per</p>
                        <p class="font-medium">{{ $document->uploader->name }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500">Data de pujada</p>
                        <p class="font-medium">{{ $document->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500">Últim accés</p>
                        <p class="font-medium">
                            @if($document->last_accessed_at)
                                {{ $document->last_accessed_at->format('d/m/Y H:i') }}
                            @else
                                Mai encara
                            @endif
                        </p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500">Visibilitat</p>
                        <p class="font-medium">
                            @if($document->is_public)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="bi bi-globe mr-1"></i>
                                    Públic
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="bi bi-lock mr-1"></i>
                                    Restringit
                                </span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Access Control -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Control d'Accés</h2>
                
                @if($document->is_public)
                    <p class="text-sm text-gray-600 mb-3">
                        Aquest document és públic i visible per tots els usuaris autenticats.
                    </p>
                @else
                    @if(!empty($document->access_roles))
                        <p class="text-sm text-gray-600 mb-3">
                            Accessible per als rols:
                        </p>
                        <div class="space-y-2">
                            @foreach($document->access_roles as $role)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ ucfirst($role) }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-600">
                            Accessible segons la configuració de la categoria.
                        </p>
                    @endif
                @endif
            </div>

            <!-- Actions -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Accions</h2>
                
                <div class="space-y-3">
                    <a href="{{ route('campus.documents.download', $document) }}" 
                       class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <i class="bi bi-download mr-2"></i>
                        Descarregar
                    </a>
                    
                    @if(auth()->user()->hasAnyRole(['admin', 'super-admin']) || $document->uploaded_by === auth()->id())
                        <a href="{{ route('campus.documents.edit', $document) }}" 
                           class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <i class="bi bi-pencil mr-2"></i>
                            Editar
                        </a>
                        
                        <form action="{{ route('campus.documents.destroy', $document) }}" method="POST" onsubmit="return confirm('Estàs segur que vols eliminar aquest document?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
                                <i class="bi bi-trash mr-2"></i>
                                Eliminar
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
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
