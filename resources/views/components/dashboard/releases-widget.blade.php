{{-- Widget de Release Notes per Dashboard --}}
@if(isset($latestRelease))
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
            <i class="bi bi-journal-text text-blue-600 mr-2"></i>
            {{ __('Últimes Novetats') }}
        </h2>
        
        <div class="space-y-4">
            <!-- Release més recent -->
            <div class="border-l-4 border-blue-500 pl-4">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900 mb-1">
                            <a href="{{ route('releases.show', $latestRelease->slug) }}" class="hover:text-blue-600 transition-colors">
                                {{ $latestRelease->title }}
                            </a>
                        </h3>
                        
                        <p class="text-sm text-gray-600 mb-2">{{ $latestRelease->summary }}</p>
                        
                        <div class="flex items-center gap-3 text-xs text-gray-500">
                            <span>
                                <i class="bi bi-calendar3"></i>
                                {{ $latestRelease->published_at->format('d/m/Y') }}
                            </span>
                            <span>
                                <i class="bi bi-git"></i>
                                {{ $latestRelease->getCommitCount() }} commits
                            </span>
                            
                            @switch($latestRelease->type)
                                @case('major')
                                    <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full">Major</span>
                                    @break
                                @case('minor')
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full">Minor</span>
                                    @break
                                @case('patch')
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full">Patch</span>
                                    @break
                            @endswitch
                        </div>
                        
                        @if($latestRelease->hasBreakingChanges())
                            <div class="mt-2 text-xs text-red-600 font-medium">
                                <i class="bi bi-exclamation-triangle mr-1"></i>
                                Conté canvis disruptius
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Estadístiques ràpides -->
            <div class="grid grid-cols-3 gap-2 text-center pt-3 border-t border-gray-200">
                <div>
                    <div class="text-lg font-bold text-blue-600">{{ $latestRelease->getFeatureCount() }}</div>
                    <div class="text-xs text-gray-600">Novetats</div>
                </div>
                <div>
                    <div class="text-lg font-bold text-green-600">{{ $latestRelease->getFixCount() }}</div>
                    <div class="text-xs text-gray-600">Correccions</div>
                </div>
                <div>
                    <div class="text-lg font-bold text-purple-600">{{ count($latestRelease->improvements ?? []) }}</div>
                    <div class="text-xs text-gray-600">Millores</div>
                </div>
            </div>
            
            <!-- Enllaços -->
            <div class="flex gap-2 pt-3 border-t border-gray-200">
                <a href="{{ route('releases.show', $latestRelease->slug) }}" 
                    class="flex-1 text-center px-3 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition-colors">
                    <i class="bi bi-eye mr-1"></i>
                    Veure detalls
                </a>
                <a href="{{ route('releases.index') }}" 
                    class="flex-1 text-center px-3 py-2 border border-gray-300 text-gray-700 text-sm rounded-md hover:bg-gray-50 transition-colors">
                    <i class="bi bi-list mr-1"></i>
                    Tots els releases
                </a>
            </div>
        </div>
    </div>
@else
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
            <i class="bi bi-journal-text text-blue-600 mr-2"></i>
            {{ __('Release Notes') }}
        </h2>
        
        <div class="text-center py-8">
            <i class="bi bi-journal-x text-4xl text-gray-300 mb-3"></i>
            <p class="text-gray-500">Encara no hi ha releases publicats</p>
        </div>
    </div>
@endif
