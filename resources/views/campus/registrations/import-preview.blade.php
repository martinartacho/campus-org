@extends('campus.shared.layout')

@section('title', __('campus.import_preview'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">{{ __('campus.import_preview_title') }}</h1>
            <x-campus-button type="link" href="{{ route('campus.campus.registrations.import.form') }}" variant="secondary">
                <i class="fas fa-arrow-left mr-2"></i>{{ __('campus.back') }}
            </x-campus-button>
        </div>

        <!-- Validation Summary -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <h3 class="text-lg font-semibold text-yellow-900 mb-3">
                <i class="fas fa-info-circle mr-2"></i>{{ __('campus.import_validation_priority') }}
            </h3>
            <div class="text-sm text-yellow-800">
                <p class="mb-2"><strong>{{ __('campus.import_priority_order') }}:</strong></p>
                <ol class="ml-4 list-decimal space-y-1">
                    <li>{{ __('campus.import_priority_course') }} ✅</li>
                    <li>{{ __('campus.import_priority_fields') }}</li>
                </ol>
                <p class="mt-2"><em>{{ __('campus.import_priority_note') }}</em></p>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-2xl font-bold text-blue-600">{{ $validation['total_rows'] }}</div>
                <div class="text-sm text-gray-600">{{ __('campus.import_total_rows') }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-2xl font-bold text-green-600">{{ $validation['valid_count'] }}</div>
                <div class="text-sm text-gray-600">{{ __('campus.import_valid_rows') }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-2xl font-bold text-red-600">{{ $validation['error_count'] }}</div>
                <div class="text-sm text-gray-600">{{ __('campus.import_error_rows') }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-2xl font-bold {{ $validation['has_errors'] ? 'text-red-600' : 'text-green-600' }}">
                    {{ $validation['has_errors'] ? __('campus.import_no') : __('campus.import_yes') }}
                </div>
                <div class="text-sm text-gray-600">{{ __('campus.import_can_import') }}</div>
            </div>
        </div>

        <!-- Season Info -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <h3 class="text-lg font-semibold text-blue-900 mb-2">
                <i class="fas fa-calendar mr-2"></i>{{ __('campus.import_season_info') }}
            </h3>
            <p class="text-sm text-blue-800">
                <strong>{{ __('campus.import_season') }}:</strong> {{ $season->name }}<br>
                <strong>{{ __('campus.import_period') }}:</strong> {{ $season->start_date }} - {{ $season->end_date }}
            </p>
        </div>

        @if($validation['has_errors'])
            <!-- Errors Section -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <h3 class="text-lg font-semibold text-red-900 mb-4">
                    <i class="fas fa-exclamation-triangle mr-2"></i>{{ __('campus.import_validation_errors') }}
                </h3>
                
                <div class="bg-white rounded-lg overflow-hidden">
                    <div class="max-h-96 overflow-y-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 sticky top-0">
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

                <div class="mt-4 text-center">
                    <p class="text-sm text-red-800">{{ __('campus.import_errors_prevent') }}</p>
                </div>
            </div>
        @endif

        <!-- Invalid Courses Summary -->
        @if(!empty($validation['invalid_courses']))
            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-6">
                <h3 class="text-lg font-semibold text-orange-900 mb-4">
                    <i class="fas fa-exclamation-triangle mr-2"></i>{{ __('campus.import_invalid_courses_summary') }}
                </h3>
                <p class="text-sm text-orange-800 mb-3">{{ __('campus.import_invalid_courses_help') }}</p>
                <div class="bg-white rounded-lg overflow-hidden">
                    <div class="max-h-64 overflow-y-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-orange-100 sticky top-0">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('campus.import_col_code') }}</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('campus.import_affected_rows') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($validation['invalid_courses'] as $code => $count)
                                    <tr class="hover:bg-orange-50">
                                        <td class="px-4 py-2 text-sm font-medium text-orange-600">{{ $code }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-900">{{ $count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <!-- Valid Data Preview -->
        @if($validation['valid_count'] > 0)
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="bg-green-100 px-4 py-2">
                    <h3 class="text-sm font-medium text-green-900">
                        <i class="fas fa-check-circle mr-2"></i>{{ __('campus.import_valid_data_preview') }} ({{ $validation['valid_count'] }})
                    </h3>
                </div>
                <div class="max-h-96 overflow-y-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('campus.import_row') }}</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('campus.student') }}</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('campus.email') }}</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('campus.course') }}</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('campus.import_col_code') }}</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('campus.import_col_quantity') }}</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('campus.status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($validation['valid_rows'] as $row)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $row['row_number'] }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $row['first_name'] }} {{ $row['last_name'] }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $row['email'] }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $row['course_name'] }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $row['course_code'] }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $row['quantity'] }}</td>
                                    <td class="px-4 py-2">
                                        @if($row['is_confirmed'])
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                {{ __('campus.registration_status_confirmed') }}
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                {{ __('campus.registration_status_pending') }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex justify-between">
            <x-campus-button type="link" href="{{ route('campus.campus.registrations.import.form') }}" variant="secondary">
                <i class="fas fa-arrow-left mr-2"></i>{{ __('campus.import_cancel') }}
            </x-campus-button>
            
            <!-- Botón de importación siempre visible -->
            <form method="POST" action="{{ route('campus.campus.registrations.import.process') }}" class="inline">
                @csrf
                <input type="hidden" name="file_path" value="{{ $filePath }}">
                <input type="hidden" name="season_id" value="{{ $season->id }}">
                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">
                    <i class="fas fa-check mr-2"></i>{{ __('campus.import_confirm_valid') }} ({{ $validation['valid_count'] }})
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
