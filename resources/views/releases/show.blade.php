@extends('campus.shared.layout')

@section('breadcrumbs')
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <a href="{{ route('dashboard') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2">
                @lang('campus.dashboard')
            </a>
        </div>
    </li>
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <a href="{{ route('releases.index') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2">
                {{ __('Release Notes') }}
            </a>
        </div>
    </li>
    <li aria-current="page">
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2">
                {{ $release->title }}
            </span>
        </div>
    </li>
@endsection

@section('title', $release->title)
@section('subtitle', 'Release Notes - ' . $release->version)

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-start justify-between">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <h1 class="text-3xl font-bold text-gray-900">{{ $release->title }}</h1>
                    
                    <!-- Badge de tipus -->
                    @switch($release->type)
                        @case('major')
                            <span class="px-3 py-1 bg-red-100 text-red-700 text-sm font-medium rounded-full">Major</span>
                            @break
                        @case('minor')
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-sm font-medium rounded-full">Minor</span>
                            @break
                        @case('patch')
                            <span class="px-3 py-1 bg-green-100 text-green-700 text-sm font-medium rounded-full">Patch</span>
                            @break
                    @endswitch
                </div>
                
                <p class="text-lg text-gray-600 mb-4">{{ $release->summary }}</p>
                
                <div class="flex items-center gap-6 text-sm text-gray-500">
                    <span>
                        <i class="bi bi-calendar3 mr-1"></i>
                        Publicat el {{ $release->published_at->format('d/m/Y H:i') }}
                    </span>
                    <span>
                        <i class="bi bi-git mr-1"></i>
                        {{ $release->getCommitCount() }} commits
                    </span>
                    <span>
                        <i class="bi bi-person mr-1"></i>
                        {{ $release->createdBy->name ?? 'System' }}
                    </span>
                </div>
            </div>
            
            <div class="flex gap-2">
                <a href="{{ route('releases.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors">
                    <i class="bi bi-arrow-left mr-2"></i>Tornar
                </a>
            </div>
        </div>
    </div>

    <!-- Alerta de canvis disruptius -->
    @if($release->hasBreakingChanges())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-8">
            <div class="flex items-start">
                <i class="bi bi-exclamation-triangle-fill text-red-600 text-xl mr-3 mt-0.5"></i>
                <div>
                    <h3 class="text-lg font-semibold text-red-800 mb-2">⚠️ Canvis Disruptius</h3>
                    <p class="text-red-700 mb-3">
                        Aquest release conté canvis que poden afectar la teva configuració actual. 
                        Revisa la documentació abans d'actualitzar.
                    </p>
                    @if(!empty($release->breaking_changes))
                        <ul class="space-y-2">
                            @foreach($release->breaking_changes as $change)
                                <li class="text-red-700">
                                    <strong>{{ $change['title'] ?? 'Canvi disruptiu' }}</strong>
                                    @if(isset($change['hash']))
                                        <span class="text-sm text-red-600 ml-2">({{ $change['hash'] }})</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Estadístiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 text-center">
            <div class="text-3xl font-bold text-blue-600 mb-2">{{ $release->getFeatureCount() }}</div>
            <div class="text-sm text-gray-600">🆕 Novetats</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 text-center">
            <div class="text-3xl font-bold text-purple-600 mb-2">{{ count($release->improvements ?? []) }}</div>
            <div class="text-sm text-gray-600">🔧 Millores</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 text-center">
            <div class="text-3xl font-bold text-green-600 mb-2">{{ $release->getFixCount() }}</div>
            <div class="text-sm text-gray-600">🐛 Correccions</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 text-center">
            <div class="text-3xl font-bold text-orange-600 mb-2">{{ count($release->commits ?? []) }}</div>
            <div class="text-sm text-gray-600">📝 Commits</div>
        </div>
    </div>

    <!-- Mòduls afectats -->
    @if(!empty($release->affected_modules))
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">📦 Mòduls Afectats</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($release->affected_modules as $module)
                    <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-md font-medium">
                        {{ ucfirst($module) }}
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Contingut del Release -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">📋 Detalls del Release</h2>
        
        <div class="prose prose-lg max-w-none">
            {!! $release->getFormattedContent() !!}
        </div>
    </div>

    <!-- Llista de Commits -->
    @if(!empty($release->commits))
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mt-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">📝 Commits Inclosos</h2>
            
            <div class="space-y-3">
                @foreach($release->commits as $commit)
                    @if(is_array($commit))
                    <div class="flex items-start justify-between p-3 bg-gray-50 rounded-md">
                        <div class="flex-1">
                            <div class="font-mono text-sm text-gray-700 mb-1">{{ $commit['hash'] ?? '' }}</div>
                            <div class="text-gray-900">{{ $commit['message'] ?? '' }}</div>
                        </div>
                        <div class="text-right text-sm text-gray-500 ml-4">
                            <div>{{ $commit['author'] }}</div>
                            <div>{{ $commit['date'] }}</div>
                        </div>
                    </div>
                    @else
                    <div class="flex items-start justify-between p-3 bg-gray-50 rounded-md">
                        <div class="flex-1">
                            <div class="font-mono text-sm text-gray-700 mb-1">{{ $commit }}</div>
                            <div class="text-gray-900">{{ $commit }}</div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <!-- Navegació -->
    <div class="flex justify-between items-center mt-8">
        <a href="{{ route('releases.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors">
            <i class="bi bi-arrow-left mr-2"></i>Tornar a tots els releases
        </a>
        
        @if(auth()->check() && auth()->user()->hasRole('admin'))
            <a href="{{ route('admin.releases.edit', $release->id) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                <i class="bi bi-pencil mr-2"></i>Editar
            </a>
        @endif
    </div>
</div>
@endsection
