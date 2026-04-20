{{-- Generación Automática de Períodos --}}
@if ($season->isAcademicYear())
<div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
    <h3 class="text-lg font-medium text-blue-900 mb-3">
        <i class="bi bi-magic me-2"></i>
        {{ __('campus.generate_periods') }}
    </h3>
    
    <form action="{{ route('campus.seasons.generatePeriods', $season->id) }}" method="POST" class="space-y-4">
        @csrf
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                {{ __('campus.select_period_configuration') }}
            </label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach(App\Services\SeasonPeriodGenerator::getPredefinedConfigurations() as $key => $config)
                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="configuration" value="{{ $key }}" class="mr-3">
                        <span class="text-sm">
                            @switch($key)
                                @case('two_semesters')
                                    2 Semestres
                                    @break
                                @case('three_trimesters')
                                    3 Trimestres
                                    @break
                                @case('two_quarters')
                                    2 Quadrimestres
                                    @break
                                @case('trimester_plus_quarter')
                                    1 Trimestre + 1 Quadrimestre
                                    @break
                                @case('four_bimensual')
                                    4 Bimensuals
                                    @break
                                @case('monthly')
                                    10 Mensuals
                                    @break
                            @endswitch
                        </span>
                    </label>
                @endforeach
            </div>
        </div>
        
        <div class="flex justify-end space-x-3">
            <x-secondary-button type="button" onclick="this.closest('form').reset()">
                {{ __('campus.cancel') }}
            </x-secondary-button>
            <x-primary-button type="submit">
                <i class="bi bi-magic me-2"></i>
                {{ __('campus.generate_periods') }}
            </x-primary-button>
        </div>
    </form>
</div>
@endif
