{{-- Widget de Release Notes per Dashboard - Compact --}}
@if(isset($latestRelease))
    <a href="{{ route('releases.show', $latestRelease->slug) }}" class="block transition-transform hover:scale-[1.02]">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg border border-blue-200 hover:border-blue-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-800">{{ __('Últimes Novetats') }}</p>
                    <p class="text-xl font-bold text-blue-900">{{ $latestRelease->version }}</p>
                </div>
                <div class="p-2 bg-blue-200 rounded-lg">
                    <i class="bi bi-journal-text text-blue-600 text-xl"></i>
                </div>
            </div>
            
            <div class="mt-2 text-xs text-blue-700">
                {{ Str::limit($latestRelease->summary, 60) }}
            </div>
            
            <div class="mt-3 grid grid-cols-3 gap-1 text-xs">
                <span class="text-blue-700">🆕 {{ $latestRelease->getFeatureCount() }}</span>
                <span class="text-blue-700">🔧 {{ count($latestRelease->improvements ?? []) }}</span>
                <span class="text-blue-700">🐛 {{ $latestRelease->getFixCount() }}</span>
            </div>
            
            @if($latestRelease->hasBreakingChanges())
                <div class="mt-2 text-xs text-red-600 font-medium">
                    <i class="bi bi-exclamation-triangle mr-1"></i>
                    Canvis disruptius
                </div>
            @endif
            
            <div class="mt-3 pt-2 border-t border-blue-200">
                <span class="text-xs text-blue-600 hover:text-blue-800 flex items-center">
                    Veure detalls <i class="bi bi-arrow-right-short ms-1"></i>
                </span>
            </div>
        </div>
    </a>
@else
    <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg border border-blue-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-blue-800">{{ __('Release Notes') }}</p>
                <p class="text-xl font-bold text-blue-900">Cap</p>
            </div>
            <div class="p-2 bg-blue-200 rounded-lg">
                <i class="bi bi-journal-x text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>
@endif
