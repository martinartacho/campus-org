@extends('campus.shared.layout')

@section('title', 'Dashboard d\'Ajuda')
@section('subtitle', 'Panell de control del sistema d\'ajuda')

@section('content')
<div class="container-fluid">
    <!-- Estadístiques principals -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg mr-4">
                    <i class="bi bi-file-text text-blue-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Articles</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_articles'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg mr-4">
                    <i class="bi bi-check-circle text-green-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">Publicats</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['published_articles'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-lg mr-4">
                    <i class="bi bi-pencil text-yellow-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">Esborranys</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['draft_articles'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-lg mr-4">
                    <i class="bi bi-folder text-purple-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">Categories</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_categories'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Articles per àrea -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Articles per Àrea</h3>
            <div class="space-y-4">
                @foreach($articlesByArea as $area => $count)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-{{ $area == 'cursos' ? 'blue' : ($area == 'matricula' ? 'green' : ($area == 'materiales' ? 'orange' : 'purple')) }}-500 rounded-full mr-3"></div>
                        <span class="text-sm font-medium text-gray-700">{{ ucfirst($area) }}</span>
                    </div>
                    <span class="text-lg font-semibold text-gray-900">{{ $count }}</span>
                </div>
                @endforeach
            </div>
        </div>
        
        <!-- Categories populars -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Categories amb Més Articles</h3>
            <div class="space-y-3">
                @foreach($categoriesWithArticles as $category)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <i class="{{ $category->iconClass }} text-gray-600 mr-3"></i>
                        <span class="text-sm font-medium text-gray-700">{{ $category->name }}</span>
                    </div>
                    <span class="text-lg font-semibold text-gray-900">{{ $category->articles_count }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Accés ràpid a funcionalitats -->
    <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Accés Ràpid</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <a href="{{ route('campus.help.articles.create') }}" class="block">
                <div class="bg-blue-50 border border-blue-200 hover:bg-blue-100 p-6 rounded-lg transition-colors">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-lg mr-4">
                            <i class="bi bi-plus-circle text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-blue-800 mb-1">Crear Article</h4>
                            <p class="text-sm text-blue-600">Afegir nou article d'ajuda</p>
                        </div>
                    </div>
                </div>
            </a>
            
            <a href="{{ route('campus.help.articles.index') }}" class="block">
                <div class="bg-green-50 border border-green-200 hover:bg-green-100 p-6 rounded-lg transition-colors">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-lg mr-4">
                            <i class="bi bi-list-ul text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-green-800 mb-1">Gestionar Articles</h4>
                            <p class="text-sm text-green-600">Llistar i editar articles</p>
                        </div>
                    </div>
                </div>
            </a>
            
            <a href="{{ route('campus.help.categories.create') }}" class="block">
                <div class="bg-purple-50 border border-purple-200 hover:bg-purple-100 p-6 rounded-lg transition-colors">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 rounded-lg mr-4">
                            <i class="bi bi-folder-plus text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-purple-800 mb-1">Crear Categoria</h4>                           
                            <p class="text-sm text-purple-600">Organitzar categories</p>
                        </div>
                    </div>
                </div>
            </a>
            
            <a href="{{ route('campus.help.categories.index') }}" class="block">
                <div class="bg-orange-50 border border-orange-200 hover:bg-orange-100 p-6 rounded-lg transition-colors">
                    <div class="flex items-center">
                        <div class="p-3 bg-orange-100 rounded-lg mr-4">
                            <i class="bi bi-folder text-orange-600 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-orange-800 mb-1">Gestionar Categories</h4>
                            <p class="text-sm text-orange-600">Administrar categories</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Articles recents -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Articles Recents</h3>
            <div class="space-y-4">
                @foreach($recentArticles as $article)
                <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                    <div class="p-2 bg-blue-100 rounded-lg flex-shrink-0">
                        <i class="bi bi-file-text text-blue-600"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-medium text-gray-900 truncate">{{ $article->title }}</h4>
                        <p class="text-xs text-gray-600 mb-2">{{ Str::limit(strip_tags($article->content), 80) }}</p>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">
                                {{ $article->updated_at->format('d/m/Y H:i') }}
                            </span>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('campus.help.articles.edit', $article) }}" 
                                   class="text-blue-600 hover:text-blue-800 text-xs">
                                    <i class="bi bi-pencil"></i> Editar
                                </a>
                                <a href="{{ route('campus.help.articles.show', $article) }}" 
                                   class="text-green-600 hover:text-green-800 text-xs">
                                    <i class="bi bi-eye"></i> Veure
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        
        <!-- Activitat recent -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Activitat Recent (Últims 7 dies)</h3>
            <div class="space-y-3">
                @foreach($recentActivity as $activity)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-{{ $activity->status == 'validated' ? 'green' : ($activity->status == 'draft' ? 'yellow' : 'red') }}-500 rounded-full mr-3"></div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $activity->title }}</p>
                            <p class="text-xs text-gray-600">
                                Actualitzat per {{ $activity->updatedBy?->name ?? 'Sistema' }}
                            </p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-500">
                        {{ $activity->updated_at->format('d/m H:i') }}
                    </span>
                </div>
                @endforeach
                
                @if($recentActivity->count() === 0)
                <div class="text-center py-8">
                    <i class="bi bi-clock-history text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">No hi ha activitat recent</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
