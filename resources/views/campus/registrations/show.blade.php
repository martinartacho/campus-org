@extends('campus.shared.layout')

@section('title', __('campus.registration_details'))

@section('breadcrumbs')
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <a href="{{ route('campus.registrations.index') }}"
               class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2">
                {{ __('campus.registrations') }}
            </a>
        </div>
    </li>
    <li aria-current="page">
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2">
                {{ $registration->registration_code }}
            </span>
        </div>
    </li>
@endsection

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">
                    {{ __('campus.registration_details') }}
                </h1>
                <p class="text-gray-600">
                    {{ __('campus.registration_code') }}: <span class="font-mono font-semibold">{{ $registration->registration_code }}</span>
                </p>
            </div>
            <div class="flex space-x-2">
                @if(auth()->user()->can('campus.registrations.edit'))
                    <a href="{{ route('campus.registrations.edit', $registration->id) }}" 
                       class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="bi bi-pencil mr-2"></i>{{ __('campus.edit') }}
                    </a>
                @endif
                
                @if($registration->status === 'pending' && auth()->user()->can('campus.registrations.validate'))
                    <form action="{{ route('campus.registrations.validate', $registration->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" 
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            <i class="bi bi-check-circle mr-2"></i>{{ __('campus.validate_registration') }}
                        </button>
                    </form>
                @endif
                
                @if(auth()->user()->can('campus.registrations.delete'))
                    <form action="{{ route('campus.registrations.destroy', $registration->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                onclick="return confirm('{{ __('campus.confirm_delete_registration') }}')"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            <i class="bi bi-trash mr-2"></i>{{ __('campus.delete') }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Student & Course Info -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    {{ __('campus.registration_information') }}
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Student Info -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-3">{{ __('campus.student') }}</h3>
                        <div class="space-y-2">
                            <div>
                                <span class="text-sm text-gray-500">{{ __('campus.name') }}:</span>
                                <p class="font-medium">{{ $registration->student->first_name }} {{ $registration->student->last_name }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">{{ __('campus.dni') }}:</span>
                                <p class="font-medium">{{ $registration->student->dni ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">{{ __('campus.email') }}:</span>
                                <p class="font-medium">{{ $registration->student->email ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">{{ __('campus.phone') }}:</span>
                                <p class="font-medium">{{ $registration->student->phone ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Course Info -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-3">{{ __('campus.course') }}</h3>
                        <div class="space-y-2">
                            <div>
                                <span class="text-sm text-gray-500">{{ __('campus.title') }}:</span>
                                <p class="font-medium">{{ $registration->course->title }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">{{ __('campus.code') }}:</span>
                                <p class="font-medium font-mono">{{ $registration->course->code }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">{{ __('campus.season') }}:</span>
                                <p class="font-medium">{{ $registration->course->season->name ?? 'N/A' }}</p>
                            </div>
                            @if($registration->course->start_date && $registration->course->end_date)
                            <div>
                                <span class="text-sm text-gray-500">{{ __('campus.duration') }}:</span>
                                <p class="font-medium">
                                    {{ $registration->course->start_date->format('d/m/Y') }} - 
                                    {{ $registration->course->end_date->format('d/m/Y') }}
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    {{ __('campus.payment_information') }}
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <span class="text-sm text-gray-500">{{ __('campus.amount') }}:</span>
                            <p class="text-xl font-bold text-gray-900">€{{ number_format($registration->amount, 2) }}</p>
                        </div>
                        
                        <div>
                            <span class="text-sm text-gray-500">{{ __('campus.payment_status') }}:</span>
                            <p class="font-medium">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs
                                    @if($registration->payment_status === 'paid')
                                        bg-green-100 text-green-800
                                    @elseif($registration->payment_status === 'partial')
                                        bg-yellow-100 text-yellow-800
                                    @elseif($registration->payment_status === 'cancelled')
                                        bg-red-100 text-red-800
                                    @else
                                        bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $registration->formatted_payment_status }}
                                </span>
                            </p>
                        </div>
                        
                        @if($registration->payment_method)
                        <div>
                            <span class="text-sm text-gray-500">{{ __('campus.payment_method') }}:</span>
                            <p class="font-medium">{{ $registration->payment_method }}</p>
                        </div>
                        @endif
                    </div>

                    <div class="space-y-4">
                        <div>
                            <span class="text-sm text-gray-500">{{ __('campus.registration_date') }}:</span>
                            <p class="font-medium">{{ $registration->registration_date->format('d/m/Y') }}</p>
                        </div>
                        
                        @if($registration->payment_due_date)
                        <div>
                            <span class="text-sm text-gray-500">{{ __('campus.payment_due_date') }}:</span>
                            <p class="font-medium {{ $registration->payment_due_date->isPast() ? 'text-red-600' : '' }}">
                                {{ $registration->payment_due_date->format('d/m/Y') }}
                                @if($registration->payment_due_date->isPast())
                                    <span class="text-xs text-red-600 ml-1">{{ __('campus.overdue') }}</span>
                                @endif
                            </p>
                        </div>
                        @endif
                        
                        <div>
                            <span class="text-sm text-gray-500">{{ __('campus.registration_status') }}:</span>
                            <p class="font-medium">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs
                                    @if($registration->status === 'confirmed')
                                        bg-green-100 text-green-800
                                    @elseif($registration->status === 'pending')
                                        bg-yellow-100 text-yellow-800
                                    @elseif($registration->status === 'cancelled')
                                        bg-red-100 text-red-800
                                    @elseif($registration->status === 'completed')
                                        bg-purple-100 text-purple-800
                                    @else
                                        bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $registration->formatted_status }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Payment History -->
                @if($registration->payment_history && count($registration->payment_history) > 0)
                <div class="mt-6 pt-6 border-t">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">{{ __('campus.payment_history') }}</h3>
                    <div class="space-y-2">
                        @foreach($registration->payment_history as $payment)
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                            <div>
                                <span class="font-medium">€{{ number_format($payment['amount'], 2) }}</span>
                                <span class="text-sm text-gray-500 ml-2">{{ $payment['method'] ?? 'N/A' }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($payment['date'])->format('d/m/Y H:i') }}</span>
                                @if($payment['reference'])
                                <span class="text-xs text-gray-400 block">{{ $payment['reference'] }}</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Notes -->
            @if($registration->notes)
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    {{ __('campus.notes') }}
                </h2>
                <p class="text-gray-700 whitespace-pre-wrap">{{ $registration->notes }}</p>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Academic Status -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    {{ __('campus.academic_status') }}
                </h2>
                
                @if($courseStudent)
                    <div class="space-y-3">
                        <div>
                            <span class="text-sm text-gray-500">{{ __('campus.academic_status') }}:</span>
                            <p class="font-medium">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs
                                    @if($courseStudent->academic_status === 'active')
                                        bg-green-100 text-green-800
                                    @elseif($courseStudent->academic_status === 'completed')
                                        bg-purple-100 text-purple-800
                                    @elseif($courseStudent->academic_status === 'dropped')
                                        bg-red-100 text-red-800
                                    @else
                                        bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $courseStudent->getStatusLabel()['label'] }}
                                </span>
                            </p>
                        </div>
                        
                        <div>
                            <span class="text-sm text-gray-500">{{ __('campus.enrollment_date') }}:</span>
                            <p class="font-medium">{{ $courseStudent->enrollment_date->format('d/m/Y') }}</p>
                        </div>
                        
                        @if($courseStudent->final_grade)
                        <div>
                            <span class="text-sm text-gray-500">{{ __('campus.final_grade') }}:</span>
                            <p class="font-medium">{{ $courseStudent->final_grade }}</p>
                        </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-exclamation-triangle text-yellow-500 text-2xl mb-2"></i>
                        <p class="text-sm text-gray-600">{{ __('campus.no_academic_record') }}</p>
                        @if($registration->status === 'confirmed')
                        <p class="text-xs text-gray-500 mt-1">{{ __('campus.validate_to_create_academic_record') }}</p>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Quick Actions -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    {{ __('campus.quick_actions') }}
                </h2>
                
                <div class="space-y-2">
                    @if($registration->status === 'pending' && auth()->user()->can('campus.registrations.validate'))
                        <form action="{{ route('campus.registrations.validate', $registration->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                                <i class="bi bi-check-circle mr-2"></i>{{ __('campus.validate_registration') }}
                            </button>
                        </form>
                    @endif
                    
                    @if(auth()->user()->can('campus.registrations.edit'))
                        <a href="{{ route('campus.registrations.edit', $registration->id) }}" 
                           class="block w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm text-center">
                            <i class="bi bi-pencil mr-2"></i>{{ __('campus.edit_registration') }}
                        </a>
                    @endif
                    
                    <a href="{{ route('campus.registrations.index') }}" 
                       class="block w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm text-center">
                        <i class="bi bi-arrow-left mr-2"></i>{{ __('campus.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
