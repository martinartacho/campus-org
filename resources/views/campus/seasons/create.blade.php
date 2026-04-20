{{-- resources/views/campus/seasons/create.blade.php --}}
@extends('campus.shared.layout')

@section('title', __('campus.new_season'))
@section('subtitle', __('campus.new_season'))

@section('breadcrumbs')
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <a href="{{ route('campus.seasons.index') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2">
                {{ __('campus.seasons') }}
            </a>
        </div>
    </li>
    <li aria-current="page">
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2">{{ __('campus.new_season') }}</span>
        </div>
    </li>
@endsection

@section('content')
    <form action="{{ route('campus.seasons.store') }}" method="POST">
        @csrf
        
        <div class="space-y-6">
            {{-- Información básica --}}
            <div class="bg-white p-6 rounded-lg border border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="bi bi-info-circle me-2"></i>
                    {{ __('campus.season_basic_info') }}
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Nombre --}}
                    <div>
                        <x-input-label for="name" :value="__('campus.season_name') . ' *'" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" 
                                     value="{{ old('name') }}" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    
                    {{-- Tipo --}}
                    <div>
                        <x-input-label for="type" :value="__('campus.season_type') . ' *'" />
                        <select id="type" name="type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            <option value="">{{ __('campus.select_type') }}</option>
                            <option value="annual" {{ old('type') == 'annual' ? 'selected' : '' }}>{{ __('campus.annual') }}</option>
                            <option value="semester" {{ old('type') == 'semester' ? 'selected' : '' }}>{{ __('campus.semester') }}</option>
                            <option value="trimester" {{ old('type') == 'trimester' ? 'selected' : '' }}>{{ __('campus.trimester') }}</option>
                            <option value="quarter" {{ old('type') == 'quarter' ? 'selected' : '' }}>{{ __('campus.quarter') }}</option>
                            <option value="bimensual" {{ old('type') == 'bimensual' ? 'selected' : '' }}>{{ __('campus.bimensual') }}</option>
                            <option value="monthly" {{ old('type') == 'monthly' ? 'selected' : '' }}>{{ __('campus.monthly') }}</option>
                            <option value="custom" {{ old('type') == 'custom' ? 'selected' : '' }}>{{ __('campus.custom_period') }}</option>
                        </select>
                        <x-input-error :messages="$errors->get('type')" class="mt-2" />
                    </div>
                    
                    {{-- Parent --}}
                    <div>
                        <x-input-label for="parent_id" :value="__('campus.season_parent')" />
                        <select id="parent_id" name="parent_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">{{ __('campus.no_parent') }}</option>
                            @foreach(\App\Models\CampusSeason::academicYears()->get() as $academicYear)
                                <option value="{{ $academicYear->id }}" {{ old('parent_id') == $academicYear->id ? 'selected' : '' }}>
                                    {{ $academicYear->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('parent_id')" class="mt-2" />
                    </div>
                    
                    {{-- Descripción --}}
                    <div class="md:col-span-2">
                        <x-input-label for="description" :value="__('campus.season_description')" />
                        <textarea id="description" name="description" rows="3" 
                                 class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>
                </div>
            </div>
            
            {{-- Fechas --}}
            <div class="bg-white p-6 rounded-lg border border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="bi bi-calendar-range me-2"></i>
                    {{ __('campus.season_dates') }}
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Fecha inicio temporada --}}
                    <div>
                        <x-input-label for="season_start" :value="__('campus.season_start_date') . ' *'" />
                        <x-text-input id="season_start" name="season_start" type="date" 
                                    class="mt-1 block w-full" value="{{ old('season_start') }}" required />
                        <x-input-error :messages="$errors->get('season_start')" class="mt-2" />
                    </div>
                    
                    {{-- Fecha fin temporada --}}
                    <div>
                       <x-input-label for="season_end" :value="__('campus.season_end_date') . ' *'" />
                        <x-text-input id="season_end" name="season_end" type="date" 
                                    class="mt-1 block w-full" value="{{ old('season_end') }}" required />
                        <x-input-error :messages="$errors->get('season_end')" class="mt-2" />
                    </div>
                    
                    {{-- Fecha inicio registro --}}
                    <div>
                        <x-input-label for="registration_start" :value="__('campus.registration_start_date')" />
                        <x-text-input id="registration_start" name="registration_start" type="date" 
                                    class="mt-1 block w-full" value="{{ old('registration_start') }}" />
                        <x-input-error :messages="$errors->get('registration_start')" class="mt-2" />
                        <p class="text-xs text-gray-500 mt-1">{{ __('campus.registration_start_help') }}</p>
                    </div>
                    
                    {{-- Fecha fin registro --}}
                    <div>
                        <x-input-label for="registration_end" :value="__('campus.registration_end_date')" />
                        <x-text-input id="registration_end" name="registration_end" type="date" 
                                    class="mt-1 block w-full" value="{{ old('registration_end') }}" />
                        <x-input-error :messages="$errors->get('registration_end')" class="mt-2" />
                        <p class="text-xs text-gray-500 mt-1">{{ __('campus.registration_end_help') }}</p>
                    </div>
                </div>
            </div>
            
            {{-- Estado --}}
            <div class="bg-white p-6 rounded-lg border border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="bi bi-toggle-on me-2"></i>
                    {{ __('campus.season_status') }}
                </h3>
                
                <div class="space-y-4">
                    {{-- Activa --}}
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="is_active" name="is_active" type="checkbox" 
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" 
                                   value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="is_active" class="font-medium text-gray-700">{{ __('campus.season_active') }}</label>
                            <p class="text-gray-500">{{ __('campus.season_active_help') }}</p>
                        </div>
                    </div>
                    
                    {{-- Actual --}}
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="is_current" name="is_current" type="checkbox" 
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" 
                                   value="1" {{ old('is_current') ? 'checked' : '' }}>
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="is_current" class="font-medium text-gray-700">{{ __('campus.season_current') }}</label>
                            <p class="text-gray-500">{{ __('campus.season_current_help') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Botó de creació de sub-períodes --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="text-lg font-medium text-blue-900 mb-3">
                    <i class="bi bi-plus-circle me-2"></i>
                    {{ __('campus.create_sub_periods') }}
                </h3>
                
                <form action="{{ route('campus.seasons.store') }}" method="POST" class="space-y-4" id="sub-periods-form">
                    @csrf
                    <input type="hidden" name="create_sub_periods" value="1">
                    
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
                            {{ __('campus.create_sub_periods') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
            
            {{-- Botones --}}
            <div class="flex justify-end space-x-4">
                <x-secondary-button href="{{ route('campus.seasons.index') }}">
                    <i class="bi bi-x-lg me-2"></i>
                    {{ __('campus.cancel') }}
                </x-secondary-button>
                
                <x-primary-button type="submit">
                    <i class="bi bi-check-lg me-2"></i>
                    {{ __('campus.create_season') }}
                </x-primary-button>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Validación de fechas
        const startDate = document.getElementById('season_start');
        const endDate = document.getElementById('season_end');
        
        function validateDates() {
            if (startDate.value && endDate.value) {
                const start = new Date(startDate.value);
                const end = new Date(endDate.value);
                
                if (end < start) {
                    endDate.setCustomValidity(@json(__('campus.date_validation_error')));
                } else {
                    endDate.setCustomValidity('');
                }
            }
        }
        
        startDate.addEventListener('change', validateDates);
        endDate.addEventListener('change', validateDates);
        
        // Marcar como actual también marca como activa
        const isCurrent = document.getElementById('is_current');
        const isActive = document.getElementById('is_active');
        
        if (isCurrent && isActive) {
            isCurrent.addEventListener('change', function() {
                if (this.checked) {
                    isActive.checked = true;
                }
            });
        }
    });
</script>
@endpush