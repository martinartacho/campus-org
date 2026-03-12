@extends('campus.shared.layout')

@section('title', __('campus.import_registrations'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">{{ __('campus.import_registrations') }}</h1>
            <x-campus-button type="link" href="{{ route('campus.registrations.index') }}" variant="secondary">
                <i class="fas fa-arrow-left mr-2"></i>{{ __('campus.back') }}
            </x-campus-button>
        </div>

        <!-- Instructions -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <h3 class="text-lg font-semibold text-blue-900 mb-3">
                <i class="fas fa-info-circle mr-2"></i>{{ __('campus.import_instructions') }}
            </h3>
            <div class="text-sm text-blue-800 space-y-2">
                <p><strong>{{ __('campus.import_format') }}:</strong> CSV (máximo 5MB)</p>
                <p><strong>{{ __('campus.import_columns') }}:</strong></p>
                <ul class="ml-4 list-disc">
                    <li>{{ __('campus.import_col_name') }} ({{ __('campus.required') }})</li>
                    <li>{{ __('campus.import_col_lastname') }}</li>
                    <li>{{ __('campus.import_col_email') }} ({{ __('campus.required') }})</li>
                    <li>{{ __('campus.import_col_phone') }}</li>
                    <li>{{ __('campus.import_col_course') }}</li>
                    <li>{{ __('campus.import_col_code') }} ({{ __('campus.required') }})</li>
                    <li>{{ __('campus.import_col_quantity') }} ({{ __('campus.required') }})</li>
                </ul>
                <p class="mt-2"><strong>{{ __('campus.import_note') }}:</strong> {{ __('campus.import_note_text') }}</p>
            </div>
        </div>

        <!-- Upload Form -->
        <div class="bg-white rounded-lg shadow p-6">
            <form method="POST" action="{{ route('campus.campus.registrations.import.validate') }}" enctype="multipart/form-data">
                @csrf
                
                <!-- Season Selection -->
                <div class="mb-6">
                    <label for="season_id" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('campus.import_season') }} <span class="text-red-500">*</span>
                    </label>
                    <select id="season_id" name="season_id" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">{{ __('campus.import_select_season') }}</option>
                        @foreach($seasons as $season)
                            <option value="{{ $season->id }}">{{ $season->name }} ({{ $season->start_date }} - {{ $season->end_date }})</option>
                        @endforeach
                    </select>
                    @error('season_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- File Upload -->
                <div class="mb-6">
                    <label for="csv_file" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('campus.import_file') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="file" 
                           id="csv_file" 
                           name="csv_file" 
                           accept=".csv,.txt"
                           required
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="mt-1 text-sm text-gray-500">{{ __('campus.import_file_help') }}</p>
                    @error('csv_file')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <x-campus-button type="submit" variant="primary">
                        <i class="fas fa-upload mr-2"></i>{{ __('campus.import_validate') }}
                    </x-campus-button>
                </div>
            </form>
            <div>
                aqui las variables:
                File: {{ session('validated_file_path') }}    Season: {{ session('validated_season_id') }}
            </div>

            @if(session('validated_file_path'))
            <!-- Botón de importación siempre visible -->
            <form method="POST" action="{{ route('campus.campus.registrations.import.process') }}" class="inline">
                @csrf
                <input type="hidden" name="file_path" value="{{ session('validated_file_path', '') }}">
                <input type="hidden" name="season_id" value="{{ session('validated_season_id', '') }}">
                <x-campus-button variant="success">
                    <i class="fas fa-download mr-2"></i>{{ __('campus.import_direct') }}
                </x-campus-button>
            </form>
        @endif
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mt-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-400 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mt-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Validation Errors -->
        @if(session('validation_errors'))
            @php
                $validation = session('validation_errors');
            @endphp
            <div class="mt-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-red-900 mb-4">
                    <i class="fas fa-exclamation-triangle mr-2"></i>{{ __('campus.import_validation_errors') }}
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="bg-white rounded p-3">
                        <p class="text-sm font-medium text-gray-900">{{ __('campus.import_total_rows') }}: <span class="text-blue-600">{{ $validation['total_rows'] }}</span></p>
                    </div>
                    <div class="bg-white rounded p-3">
                        <p class="text-sm font-medium text-gray-900">{{ __('campus.import_valid_rows') }}: <span class="text-green-600">{{ $validation['valid_count'] }}</span></p>
                    </div>
                    <div class="bg-white rounded p-3">
                        <p class="text-sm font-medium text-gray-900">{{ __('campus.import_error_rows') }}: <span class="text-red-600">{{ $validation['error_count'] }}</span></p>
                    </div>
                    <div class="bg-white rounded p-3">
                        <p class="text-sm font-medium text-gray-900">{{ __('campus.import_can_import') }}: <span class="{{ $validation['has_errors'] ? 'text-red-600' : 'text-green-600' }}">{{ $validation['has_errors'] ? __('campus.import_no') : __('campus.import_yes') }}</span></p>
                    </div>
                </div>

                @if($validation['error_count'] > 0)
                    <div class="bg-white rounded-lg overflow-hidden">
                        <div class="bg-red-100 px-4 py-2">
                            <h4 class="text-sm font-medium text-red-900">{{ __('campus.import_error_details') }} ({{ $validation['error_count'] }})</h4>
                        </div>
                        <div class="max-h-64 overflow-y-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('campus.import_row') }}</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('campus.import_field') }}</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('campus.import_error') }}</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('campus.import_data') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($validation['errors'] as $error)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $error['row'] }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $error['field'] }}</td>
                                            <td class="px-4 py-2 text-sm text-red-600">{{ $error['error'] }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-500">{{ $error['data'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
