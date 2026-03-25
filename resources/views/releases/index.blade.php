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
    <li aria-current="page">
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2">
                {{ __('Release Notes') }}
            </span>
        </div>
    </li>
@endsection

@section('title', 'Release Notes')
@section('subtitle', 'Historial de canvis i novetats de Campus UPG')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">
            <i class="bi bi-journal-text text-blue-600 mr-3"></i>
            Release Notes
        </h1>
        <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            Mantingue't informatat de les últimes novetats, millores i correccions de Campus UPG
        </p>
    </div>

    <!-- Filtres -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-8">
        <div class="flex flex-wrap gap-4 items-center">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipus de release</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tots</option>
                    <option value="major">Major (🔴)</option>
                    <option value="minor">Minor (🟡)</option>
                    <option value="patch">Patch (🟢)</option>
                </select>
            </div>
            
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">Cerca</label>
                <input type="text" placeholder="Cercar releases..." 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex gap-2 items-end">
                <a href="{{ route('releases.feed') }}" class="px-4 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600 transition-colors">
                    <i class="bi bi-rss mr-2"></i>RSS Feed
                </a>
            </div>
        </div>
    </div>

    <!-- Llista de Releases -->
    <div class="space-y-6">
        @forelse($releases as $release)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                <div class="p-6">
                    <!-- Capçalera -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h2 class="text-xl font-semibold text-gray-900">
                                    <a href="{{ route('releases.show', $release->slug) }}" class="hover:text-blue-600 transition-colors">
                                        {{ $release->title }}
                                    </a>
                                </h2>
                                
                                <!-- Badge de tipus -->
                                @switch($release->type)
                                    @case('major')
                                        <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-medium rounded-full">Major</span>
                                        @break
                                    @case('minor')
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-medium rounded-full">Minor</span>
                                        @break
                                    @case('patch')
                                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">Patch</span>
                                        @break
                                @endswitch
                            </div>
                            
                            <p class="text-gray-600 mb-2">{{ $release->summary }}</p>
                            
                            <div class="flex items-center gap-4 text-sm text-gray-500">
                                <span>
                                    <i class="bi bi-calendar3 mr-1"></i>
                                    {{ $release->published_at->format('d/m/Y') }}
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
                    </div>

                    <!-- Mòduls afectats -->
                    @if(!empty($release->affected_modules))
                        <div class="flex flex-wrap gap-2 mb-4">
                            @foreach($release->affected_modules as $module)
                                <span class="px-2 py-1 bg-blue-50 text-blue-700 text-xs rounded-md">
                                    {{ ucfirst($module) }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    <!-- Estadístiques -->
                    <div class="grid grid-cols-3 gap-4 text-center border-t border-gray-200 pt-4">
                        <div>
                            <div class="text-2xl font-bold text-blue-600">{{ $release->getFeatureCount() }}</div>
                            <div class="text-sm text-gray-600">Novetats</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-green-600">{{ count($release->improvements ?? []) }}</div>
                            <div class="text-sm text-gray-600">Millores</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-orange-600">{{ $release->getFixCount() }}</div>
                            <div class="text-sm text-gray-600">Correccions</div>
                        </div>
                    </div>

                    <!-- Canvis disruptius -->
                    @if($release->hasBreakingChanges())
                        <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-md">
                            <div class="flex items-center text-red-700">
                                <i class="bi bi-exclamation-triangle-fill mr-2"></i>
                                <span class="font-medium">Aquest release conté canvis disruptius</span>
                            </div>
                        </div>
                    @endif

                    <!-- Botó d'acció -->
                    <div class="mt-4">
                        <a href="{{ route('releases.show', $release->slug) }}" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                            <i class="bi bi-eye mr-2"></i>
                            Veure detalls
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <i class="bi bi-journal-x text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">No hi ha releases publicats</h3>
                <p class="text-gray-500">Encara no hem publicat cap release notes.</p>
            </div>
        @endforelse
    </div>

    <!-- Paginació -->
    @if($releases->hasPages())
        <div class="mt-8">
            {{ $releases->links() }}
        </div>
    @endif
</div>
@endsection
