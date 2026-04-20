{{-- resources/views/campus/seasons/create-with-periods.blade.php --}}
@extends('campus.shared.layout')

@section('title', __('campus.create_season_with_periods'))
@section('subtitle', __('campus.create_season_with_periods_subtitle'))

@section('breadcrumbs')
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <a href="{{ route('campus.seasons.index') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2">
                {{ __('campus.seasons') }}
            </a>
        </div>
    </li>
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <a href="{{ route('campus.seasons.create') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2">
                {{ __('campus.create_season') }}
            </a>
        </div>
    </li>
    <li aria-current="page">
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2">{{ __('campus.create_with_periods') }}</span>
        </div>
    </li>
@endsection

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white shadow-sm rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">
                    <i class="bi bi-magic me-2"></i>
                    {{ __('campus.create_season_with_periods') }}
                </h3>
                <p class="mt-1 text-sm text-gray-600">
                    {{ __('campus.create_season_with_periods_description') }}
                </p>
            </div>

            <form action="{{ route('campus.seasons.store-with-periods') }}" method="POST" class="p-6 space-y-6">
                @csrf

                {{-- Información básica --}}
                <div class="space-y-4">
                    <div>
                        <x-input-label for="name" :value="__('campus.season_name') . ' *'" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" 
                                     value="{{ old('name') }}" required 
                                     placeholder="Curs 2026-27" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        <p class="text-xs text-gray-500 mt-1">{{ __('campus.academic_year_format_help') }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="season_start" :value="__('campus.season_start_date') . ' *'" />
                            <x-text-input id="season_start" name="season_start" type="date" 
                                         class="mt-1 block w-full" value="{{ old('season_start', date('Y') . '-09-01') }}" required />
                            <x-input-error :messages="$errors->get('season_start')" class="mt-2" />
                        </div>
                        
                        <div>
                            <x-input-label for="season_end" :value="__('campus.season_end_date') . ' *'" />
                            <x-text-input id="season_end" name="season_end" type="date" 
                                         class="mt-1 block w-full" value="{{ old('season_end', (date('Y') + 1) . '-06-30') }}" required />
                            <x-input-error :messages="$errors->get('season_end')" class="mt-2" />
                        </div>
                    </div>
                </div>

                {{-- Configuración de períodos --}}
                <div class="border-t pt-6">
                    <h4 class="text-md font-medium text-gray-900 mb-4">
                        {{ __('campus.select_period_configuration') }}
                    </h4>
                    
                    <div class="grid grid-cols-1 gap-3">
                        @foreach($configurations as $key => $config)
                            <label class="relative flex items-start p-4 border rounded-lg cursor-pointer hover:bg-gray-50 @if(old('configuration') == $key) bg-blue-50 border-blue-200 @endif">
                                <input type="radio" name="configuration" value="{{ $key }}" 
                                       class="mt-1 mr-3" @if(old('configuration') == $key) checked @endif>
                                
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900">
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
                                    </div>
                                    <div class="text-sm text-gray-500 mt-1">
                                        @switch($key)
                                            @case('two_semesters')
                                                2 períodos de 6 mesos (setembre - gener, febrer - juny)
                                                @break
                                            @case('three_trimesters')
                                                3 períodos de 4 mesos
                                                @break
                                            @case('two_quarters')
                                                2 períodos de 4 mesos
                                                @break
                                            @case('trimester_plus_quarter')
                                                2 períodos: 3 mesos + 4 mesos
                                                @break
                                            @case('four_bimensual')
                                                4 períodos de 2 mesos
                                                @break
                                            @case('monthly')
                                                10 períodos de 1 mes
                                                @break
                                        @endswitch
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    
                    <x-input-error :messages="$errors->get('configuration')" class="mt-2" />
                </div>

                {{-- Estado --}}
                <div class="border-t pt-6">
                    <h4 class="text-md font-medium text-gray-900 mb-4">{{ __('campus.season_status') }}</h4>
                    
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <input id="is_active" name="is_active" type="checkbox" 
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" 
                                   value="1" {{ old('is_active') ? 'checked' : '' }}>
                            <label for="is_active" class="ml-2 text-sm font-medium text-gray-700">
                                {{ __('campus.season_active') }}
                            </label>
                        </div>
                        
                        <div class="flex items-center">
                            <input id="is_current" name="is_current" type="checkbox" 
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" 
                                   value="1" {{ old('is_current') ? 'checked' : '' }}>
                            <label for="is_current" class="ml-2 text-sm font-medium text-gray-700">
                                {{ __('campus.season_current') }}
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Botones --}}
                <div class="flex justify-between items-center pt-6 border-t">
                    <div>
                        <a href="{{ route('campus.seasons.create') }}" class="text-sm text-gray-500 hover:text-gray-700">
                            <i class="bi bi-arrow-left me-1"></i>
                            {{ __('campus.back_to_simple_create') }}
                        </a>
                    </div>
                    
                    <div class="flex space-x-3">
                        <x-secondary-button href="{{ route('campus.seasons.index') }}">
                            <i class="bi bi-x-lg me-2"></i>
                            {{ __('campus.cancel') }}
                        </x-secondary-button>
                        
                        <x-primary-button type="submit">
                            <i class="bi bi-magic me-2"></i>
                            {{ __('campus.create_with_periods') }}
                        </x-primary-button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate dates based on academic year name
    const nameInput = document.getElementById('name');
    const startInput = document.getElementById('season_start');
    const endInput = document.getElementById('season_end');
    
    nameInput.addEventListener('input', function() {
        const match = this.value.match(/(\d{4})-(\d{2})/);
        if (match) {
            const startYear = parseInt(match[1]);
            const endYear = 2000 + parseInt(match[2]);
            
            startInput.value = `${startYear}-09-01`;
            endInput.value = `${endYear}-06-30`;
        }
    });
});
</script>
@endpush
