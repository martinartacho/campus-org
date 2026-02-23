{{-- resources/views/campus/seasons/edit.blade.php --}}
@extends('campus.shared.layout')

@section('title', __('campus.edit_season'))
@section('subtitle', __('campus.edit_season') . ': ' . $season->name)

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
            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2">{{ __('campus.edit') }}</span>
        </div>
    </li>
@endsection

@section('content')
    <form action="{{ route('campus.seasons.update', $season) }}" method="POST">
        @csrf
        @method('PUT')
        
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
                                     value="{{ old('name', $season->name) }}" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    
                    {{-- Descripción --}}
                    <div class="md:col-span-2">
                        <x-input-label for="description" :value="__('campus.season_description')" />
                        <textarea id="description" name="description" rows="3" 
                                 class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $season->description) }}</textarea>
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
                    {{-- Fecha inicio --}}
                    <div>
                        <x-input-label for="season_start" :value="__('campus.season_start_date') . ' *'" />
                        <x-text-input id="season_start" name="season_start" type="date" 
                                     class="mt-1 block w-full" 
                                     value="{{ old('season_start', $season->season_start->format('Y-m-d')) }}" required />
                        <x-input-error :messages="$errors->get('season_start')" class="mt-2" />
                    </div>
                    
                    {{-- Fecha fin --}}
                    <div>
                        <x-input-label for="season_end" :value="__('campus.season_end_date') . ' *'" />
                        <x-text-input id="season_end" name="season_end" type="date" 
                                     class="mt-1 block w-full" 
                                     value="{{ old('season_end', $season->season_end->format('Y-m-d')) }}" required />
                        <x-input-error :messages="$errors->get('season_end')" class="mt-2" />
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
                                   value="1" {{ old('is_active', $season->is_active) ? 'checked' : '' }}>
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
                                   value="1" {{ old('is_current', $season->is_current) ? 'checked' : '' }}>
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="is_current" class="font-medium text-gray-700">{{ __('campus.season_current') }}</label>
                            <p class="text-gray-500">{{ __('campus.season_current_help') }}</p>
                        </div>
                    </div>
    
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="status" name="status" type="checkbox" 
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" 
                                   value="1" {{ old('status', $season->status) ? 'checked' : '' }}>
                        </div>
                    <div class="ml-3 text-sm">
                        <label for="status" class="font-medium text-gray-700">{{ __('campus.season_status') }}</label>
                        <p class="text-gray-500">{{ __('campus.season_status_help') }}</p>
                    </div>
                    

                </div>
            </div>
            
            {{-- Botones --}}
            <div class="flex justify-end space-x-4">
                <x-secondary-button href="{{ route('campus.seasons.index') }}">
                    <i class="bi bi-x-lg me-2"></i>
                    {{ __('campus.cancel') }}
                </x-secondary-button>
                
                <x-primary-button type="submit">
                    <i class="bi bi-check-lg me-2"></i>
                    {{ __('campus.update_season') }}
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
    });
</script>
@endpush