@extends('campus.shared.layout')

@section('title', __('Veure Release') . ' ' . $release->version)

@section('actions')
   
@endsection
@section('content')
<div class="max-w-5xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Capçalera -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-8">
        <div class="px-4 py-5 sm:px-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900">
                        <i class="bi bi-file-earmark-text mr-3"></i>
                        {{ $release->title }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ __('Versió') }}: {{ $release->version }} | 
                        {{ __('Tipus') }}: {{ $release->type }} | 
                        {{ __('Estat') }}: 
                        @if($release->isPublished())
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                {{ __('Publicat') }}
                            </span>
                        @elseif($release->isDraft())
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                {{ __('Esborrany') }}
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                {{ __('Arxivat') }}
                            </span>
                        @endif
                    </p>
                </div>
                
                <div class="flex space-x-3">
                    @if(!$release->isPublished())
                        <form action="{{ route('admin.releases.publish', $release->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <i class="bi bi-upload mr-2"></i>
                                {{ __('Publicar') }}
                            </button>
                        </form>
                    @endif
                    
                    <a href="{{ route('admin.releases.edit', $release->id) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 text-gray-700 rounded-md text-sm font-medium rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="bi bi-pencil mr-2"></i>
                        {{ __('Editar') }}
                    </a>
                    
                    <form action="{{ route('admin.releases.destroy', $release->id) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Estàs segur que vols eliminar aquest release?') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <i class="bi bi-trash mr-2"></i>
                            {{ __('Eliminar') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Contingut del Release -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">{{ __('Contingut del Release') }}</h2>
            
            <!-- Resum -->
            @if($release->summary)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h3 class="text-lg font-semibold text-blue-900 mb-2">
                        <i class="bi bi-info-circle text-blue-600 mr-2"></i>
                        {{ __('Resum') }}
                    </h3>
                    <p class="text-gray-700">{{ $release->summary }}</p>
                </div>
            @endif

            <!-- Contingut principal -->
            <div class="prose prose-lg max-w-none">
                {!! $release->getFormattedContent() !!}
            </div>
        </div>
    </div>

    <!-- Canvis i Novetats -->
    @if(!empty($release->features) || !empty($release->improvements) || !empty($release->fixes) || !empty($release->breaking_changes))
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mt-8">
            <div class="px-4 py-5 sm:px-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">{{ __('Canvis i Novetats') }}</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if(!empty($release->features))
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                <i class="bi bi-star text-green-600 mr-2"></i>
                                {{ __('Noves Funcionalitats') }}
                            </h3>
                            <ul class="list-disc list-inside space-y-2 text-gray-600">
                                @foreach($release->features as $feature)
                                    <li>{{ is_array($feature) ? json_encode($feature) : $feature }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(!empty($release->improvements))
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                <i class="bi bi-arrow-up text-blue-600 mr-2"></i>
                                {{ __('Millores') }}
                            </h3>
                            <ul class="list-disc list-inside space-y-2 text-gray-600">
                                @foreach($release->improvements as $improvement)
                                    <li>{{ is_array($improvement) ? json_encode($improvement) : $improvement }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(!empty($release->fixes))
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                <i class="bi bi-tools text-orange-600 mr-2"></i>
                                {{ __('Correccions') }}
                            </h3>
                            <ul class="list-disc list-inside space-y-2 text-gray-600">
                                @foreach($release->fixes as $fix)
                                    <li>{{ is_array($fix) ? json_encode($fix) : $fix }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(!empty($release->breaking_changes))
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                <i class="bi bi-exclamation-triangle text-red-600 mr-2"></i>
                                {{ __('Canvis Trencants') }}
                            </h3>
                            <ul class="list-disc list-inside space-y-2 text-gray-600">
                                @foreach($release->breaking_changes as $change)
                                    <li>{{ is_array($change) ? json_encode($change) : $change }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Mòduls Afectats -->
    @if(!empty($release->affected_modules))
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mt-8">
            <div class="px-4 py-5 sm:px-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">{{ __('Mòduls Afectats') }}</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach($release->affected_modules as $module)
                        <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm">
                            {{ is_array($module) ? json_encode($module) : $module }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Informació de Creació -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg mt-8">
        <div class="px-4 py-5 sm:px-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">{{ __('Informació de Creació') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <strong>{{ __('Creat per') }}:</strong> {{ $release->createdBy ? $release->createdBy->name : '-' }}
                </div>
                <div>
                    <strong>{{ __('Data Creació') }}:</strong> {{ $release->created_at->format('d/m/Y H:i') }}
                </div>
                @if($release->publishedBy)
                    <div>
                        <strong>{{ __('Publicat per') }}:</strong> {{ $release->publishedBy->name }}
                    </div>
                    <div>
                        <strong>{{ __('Data Publicació') }}:</strong> {{ $release->published_at ? $release->published_at->format('d/m/Y H:i') : '-' }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Navegació -->
    <div class="flex justify-between items-center mt-8">
        <a href="{{ route('admin.releases.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors">
            <i class="bi bi-arrow-left mr-2"></i>
            {{ __('Tornar a la llista') }}
        </a>
        
        @if($release->isPublished())
            <a href="{{ route('releases.show', $release->slug) }}" class="px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest bg-indigo-600 hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150" target="_blank">
                <i class="bi bi-eye mr-2"></i>
                {{ __('Veure públic') }}
            </a>
        @endif
    </div>
</div>
@endsection
