@extends('campus.shared.layout')

@section('title', __('campus.help_center'))

@section('subtitle', __('campus.help_subtitle'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">
            <i class="bi bi-question-circle text-blue-600 mr-3"></i>
            Centre d'Ajuda
        </h1>
        <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            {{ __('campus.help_description') }}
        </p>
    </div>

    <!-- Filtres de cerca -->
    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
        <h3 class="text-lg font-semibold mb-4 text-gray-700">
            <i class="bi bi-funnel mr-2"></i>{{ __('campus.search') }} Articles
        </h3>
        
        <form method="GET" action="{{ url('/help') }}" class="space-y-4">
            <!-- Primera fila de filtres -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Filtre per cerca -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('campus.search_help') }}
                    </label>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="{{ __('campus.search_help') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Filtre per àrea -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('campus.area') }}
                    </label>
                    <select name="area" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">{{ __('campus.all_areas') }}</option>
                        @foreach($areas as $areaKey => $area)
                        <option value="{{ $areaKey }}" {{ request('area') == $areaKey ? 'selected' : '' }}>
                            {{ $area['name'] }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Botó de cerca -->
                <div class="flex items-end">
                    <button type="submit" 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-colors">
                        <i class="bi bi-search mr-2"></i>{{ __('campus.search') }}
                    </button>
                </div>
            </div>
            
            <!-- Botons de filtre ràpid per àrea -->
            <div class="flex flex-wrap gap-2 mt-4">
                <a href="{{ url('/help') }}" 
                   class="inline-flex items-center px-3 py-1 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-full text-sm transition-colors">
                    {{ __('campus.all_areas') }}
                </a>
                @foreach($areas as $areaKey => $area)
                <a href="{{ url('/help?area=' . $areaKey) }}" 
                   class="inline-flex items-center px-3 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-full text-sm transition-colors">
                    <i class="{{ $area['icon'] }} mr-1"></i>
                    {{ $area['name'] }}
                </a>
                @endforeach
            </div>
        </form>
    </div>

    <!-- Articles Recents -->
    <div class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">
            <h4 class="text-lg font-semibold 
            @if(request('area'))
                {{ __('campus.articles_in_area') }}: {{ $areas[request('area')]['name'] ?? request('area') }}
            @elseif(request('search'))
                {{ __('campus.search_results') }}: "{{ request('search') }}"
            @else
                {{ __('campus.recent_articles') }}
            @endif
        </h2>
        
        @if($filteredArticles->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($filteredArticles as $article)
            <a href="{{ url('/help/' . $article->slug) }}" class="block bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-start space-x-4">
                    <div class="bg-gray-100 w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="bi bi-file-text text-gray-600"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-lg font-semibold text-gray-900 mb-1">{{ $article->title }}</h4>
                        <p class="text-sm text-gray-600 mb-3">{{ Str::limit(strip_tags($article->content), 100) }}...</p>
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ ucfirst($article->area) }}
                            </span>
                            <span class="text-xs text-gray-500">
                                {{ $article->updated_at->format('d/m/Y') }}
                            </span>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        @else
        <div class="text-center py-8">
            <i class="bi bi-search text-4xl text-gray-300 mb-4"></i>
            <p class="text-gray-500">
                @if(request('search'))
                    No s'han trobat resultats per a "{{ request('search') }}"
                @elseif(request('area'))
                    No hi ha articles disponibles en aquesta àrea
                @else
                    No hi ha articles disponibles
                @endif
            </p>
        </div>
        @endif
    </div>


    <!-- Accions Ràpides -->
    <div class="bg-blue-50 rounded-xl p-8 text-center">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('campus.cannot_find_what_looking') }}</h2>
        <p class="text-gray-600 mb-6">{{ __('campus.support_team_ready') }}</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors">
                <i class="bi bi-chat-dots mr-2"></i>
                {{ __('campus.contact_support') }}
            </button>
            <button class="bg-white hover:bg-gray-50 text-gray-700 px-6 py-3 rounded-lg border border-gray-300 transition-colors">
                <i class="bi bi-envelope mr-2"></i>
                {{ __('campus.send_suggestion') }}
            </button>
        </div>
    </div>
</div>

<style>
/* Animaciones */
.hover\:shadow-md {
    transition: box-shadow 0.3s ease;
}

/* Scrollbar personalizado */
.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
@endsection
