@props(['title' => 'Temporada Actual', 'color' => 'blue', 'season' => null])

<div class="bg-{{ $color }}-50 border border-{{ $color }}-200 rounded-lg p-4 shadow-sm">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="flex-shrink-0">
                <i class="bi bi-calendar3 text-{{ $color }}-600 text-xl"></i>
            </div>
            <div>
                <h3 class="text-sm font-medium text-{{ $color }}-900">{{ $title }}</h3>
                <p class="text-lg font-semibold text-{{ $color }}-700">
                    {{ $season?->name ?? 'Cap temporada seleccionada' }}
                </p>
                @if($season)
                    <p class="text-xs text-{{ $color }}-600">
                        {{ $season->season_start->format('d/m/Y') }} - {{ $season->season_end->format('d/m/Y') }}
                    </p>
                    @if($season->is_current)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $color }}-100 text-{{ $color }}-800">
                            <i class="bi bi-check-circle-fill mr-1"></i>
                            Actual
                        </span>
                    @endif
                @endif
            </div>
        </div>
        
        @if(auth()->user()->hasRole('admin') || auth()->user()->can('manage_seasons'))
            <div class="flex-shrink-0">
                <button onclick="openSeasonSelector()" class="text-{{ $color }}-600 hover:text-{{ $color }}-800">
                    <i class="bi bi-gear text-lg"></i>
                </button>
            </div>
        @endif
    </div>
</div>

@if(auth()->user()->hasRole('admin') || auth()->user()->can('manage_seasons'))
    <!-- Modal de selección de temporada (oculto por defecto) -->
    <div id="seasonSelectorModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Seleccionar Temporada Actual</h3>
                
                <div class="space-y-2">
                    @foreach(\App\Models\CampusSeason::orderBy('season_start', 'desc')->get() as $season)
                        <div class="flex items-center justify-between p-3 border rounded hover:bg-gray-50">
                            <div>
                                <div class="font-medium">{{ $season->name }}</div>
                                <div class="text-sm text-gray-500">
                                    {{ $season->season_start->format('d/m/Y') }} - {{ $season->season_end->format('d/m/Y') }}
                                </div>
                                @if($season->isFuture())
                                    <span class="text-xs text-orange-600">Futura</span>
                                @elseif($season->isPast())
                                    <span class="text-xs text-gray-600">Pasada</span>
                                @else
                                    <span class="text-xs text-green-600">Actual</span>
                                @endif
                            </div>
                            <button 
                                onclick="setCurrentSeason({{ $season->id }})"
                                class="px-3 py-1 text-sm rounded 
                                    @if($season->is_current)
                                        bg-green-100 text-green-800
                                    @else
                                        bg-blue-100 text-blue-800 hover:bg-blue-200
                                    @endif">
                                @if($season->is_current)
                                    Actual
                                @else
                                    Establecer
                                @endif
                            </button>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-4 flex justify-end">
                    <button onclick="closeSeasonSelector()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openSeasonSelector() {
            document.getElementById('seasonSelectorModal').classList.remove('hidden');
        }
        
        function closeSeasonSelector() {
            document.getElementById('seasonSelectorModal').classList.add('hidden');
        }
        
        function setCurrentSeason(seasonId) {
            if (confirm('¿Estás seguro de cambiar la temporada actual?')) {
                const url = '{{ route("campus.seasons.setAsCurrent", ['season' => ':seasonId']) }}';
                const finalUrl = url.replace(':seasonId', seasonId);
                
                fetch(finalUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ season_id: seasonId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al cambiar la temporada');
                });
            }
        }
    </script>
@endif
