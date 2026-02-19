<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
           {{ __('site.Create Event') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('admin.events.store') }}" method="POST">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700">{{ __('site.Title') }} *</label>
                                <input type="text" name="title" id="title" value="{{ old('title') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                           
                            <div>
                                <label for="event_type_id" class="block text-sm font-medium text-gray-700">{{ __('site.Event Types') }}</label>
                                <select name="event_type_id" id="event_type_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">{{ __('site.Select an event type') }}</option>
                                    @foreach($eventTypes as $type)
                                        <option value="{{ $type->id }}" {{ old('event_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                    @endforeach
                                </select>
                                @error('event_type_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="start" class="block text-sm font-medium text-gray-700">{{ __('site.Start Date/Time') }} *</label>
                                <input type="datetime-local" name="start" id="start" value="{{ old('start', now()->format('Y-m-d\TH:i')) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('start')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="end" class="block text-sm font-medium text-gray-700">{{ __('site.End Date/Time') }}</label>
                                <input type="datetime-local" name="end" id="end" value="{{ old('end', now()->addHour()->format('Y-m-d\TH:i')) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('end')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="color" class="block text-sm font-medium text-gray-700">{{ __('site.Color') }}</label>
                                <input type="color" name="color" id="color" value="{{ old('color', '#3c8dbc') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm h-10">
                                @error('color')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="max_users" class="block text-sm font-medium text-gray-700">{{ __('site.Maximum Users') }}</label>
                                <input type="number" name="max_users" id="max_users" value="{{ old('max_users') }}" min="1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('max_users')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" name="visible" id="visible" value="1" {{ old('visible', true) ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                <label for="visible" class="ml-2 block text-sm text-gray-900">{{ __('site.Visible') }}</label>
                                @error('visible')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="start_visible" class="block text-sm font-medium text-gray-700">{{ __('site.Visible From') }}</label>
                                <input type="datetime-local" name="start_visible" id="start_visible" value="{{ old('start_visible', now()->format('Y-m-d\TH:i')) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('start_visible')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="end_visible" class="block text-sm font-medium text-gray-700">{{ __('site.Visible Until') }}</label>
                                <input type="datetime-local" name="end_visible" id="end_visible" value="{{ old('end_visible') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('end_visible')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700">{{ __('site.Description') }}</label>
                                <textarea name="description" id="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-4 border-t pt-4 mt-4">
                                @if(request()->is('admin/events/create'))
                                    <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-4" role="alert">
                                        <p class="font-bold">{{ __('site.Information') }}</p>
                                        <p>{{ __('site.To create recurring events, select a recurrence type other than "None".') }}</p>
                                    </div>
                                @endif
                                <h4 class="text-lg font-medium text-gray-900 mb-">{{ __('site.Recurrence Settings') }}</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="recurrence_type" class="block text-sm font-medium text-gray-700">{{ __('site.Recurrence Type') }}</label>
                                    <select name="recurrence_type" id="recurrence_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="none" {{ old('recurrence_type', isset($event) ? $event->recurrence_type : 'none') == 'none' ? 'selected' : '' }}>{{ __('site.None') }}</option>
                                        <option value="daily" {{ old('recurrence_type', isset($event) ? $event->recurrence_type : 'none') == 'daily' ? 'selected' : '' }}>{{ __('site.Daily') }}</option>
                                        <option value="weekly" {{ old('recurrence_type', isset($event) ? $event->recurrence_type : 'none') == 'weekly' ? 'selected' : '' }}>{{ __('site.Weekly') }}</option>
                                        <option value="monthly" {{ old('recurrence_type', isset($event) ? $event->recurrence_type : 'none') == 'monthly' ? 'selected' : '' }}>{{ __('site.Monthly') }}</option>
                                        <option value="yearly" {{ old('recurrence_type', isset($event) ? $event->recurrence_type : 'none') == 'yearly' ? 'selected' : '' }}>{{ __('site.Yearly') }}</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="recurrence_interval" class="block text-sm font-medium text-gray-700">{{ __('site.Repeat Every') }}</label>
                                    <div class="mt-1 flex rounded-md shadow-sm">
                                        <input type="number" name="recurrence_interval" id="recurrence_interval" min="1" value="{{ old('recurrence_interval', isset($event) ? $event->recurrence_interval : 1) }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-20 rounded-none rounded-l-md sm:text-sm border-gray-300">
                                        <span class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm" id="recurrence_interval_label">
                                        {{ __('site.days') }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="recurrence_end_date" class="block text-sm font-medium text-gray-700">{{ __('site.End Date/Time') }}</label>
                                    <input type="date" name="recurrence_end_date" id="recurrence_end_date" value="{{ old('recurrence_end_date', isset($event) && $event->recurrence_end_date ? $event->recurrence_end_date->format('Y-m-d') : '') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                                
                                <div>
                                    <label for="recurrence_count" class="block text-sm font-medium text-gray-700">{{ __('site.Number of Occurrences') }}</label>
                                    <input type="number" name="recurrence_count" id="recurrence_count" min="1" value="{{ old('recurrence_count', isset($event) ? $event->recurrence_count : '') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                            </div>
                        </div>
                        </div>
                        
                        <div class="mt-6 flex justify-end">
                            <a href="{{ route('admin.events.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                               {{ __('site.Cancel') }}
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                               {{ __('site.Save Event') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
    
        const recurrenceType = document.getElementById('recurrence_type');
        const intervalLabel = document.getElementById('recurrence_interval_label');
        const endDateField = document.getElementById('recurrence_end_date').closest('div');
        const countField = document.getElementById('recurrence_count').closest('div');
        
        function updateRecurrenceFields() {
            const type = recurrenceType.value;
            let label = '';
            
            // Actualizar etiqueta del intervalo
            switch(type) {
                case 'daily':
                    label = '{{ __("site.days") }}';
                    break;
                case 'weekly':
                    label = '{{ __("site.weeks") }}';
                    break;
                case 'monthly':
                    label = '{{ __("site.months") }}';
                    break;
                case 'yearly':
                    label = '{{ __("site.years") }}';
                    break;
                default:
                    label = '{{ __("site.days") }}';
            }
            
            intervalLabel.textContent = label;
            
            // Mostrar/ocultar campos según el tipo de recurrencia
            if (type === 'none') {
                endDateField.style.display = 'none';
                countField.style.display = 'none';
            } else {
                endDateField.style.display = 'block';
                countField.style.display = 'block';
            }
        }
        
        recurrenceType.addEventListener('change', updateRecurrenceFields);
        updateRecurrenceFields(); // Initial call
    });
</script>
@endpush
</x-app-layout>