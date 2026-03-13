@extends('campus.shared.layout')

@section('title', __('campus.my_registrations'))
@section('subtitle', __('campus.registration_history'))

@section('breadcrumbs')
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <a href="{{ route('dashboard') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2">
                @lang('site.Dashboard')
            </a>
        </div>
    </li>
    <li aria-current="page">
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2">
                {{ __('campus.my_registrations') }}
            </span>
        </div>
    </li>
@endsection

@section('content')
<div class="max-w-6xl mx-auto">
    {{-- Lista de Matrículas --}}
    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">
                <i class="bi bi-file-earmark-text mr-2"></i>{{ __('campus.my_registrations') }}
            </h2>
        </div>
        
        <div class="p-6">
            @if(auth()->user()->student && auth()->user()->student->registrations->count() > 0)
                <div class="space-y-4">
                    @foreach(auth()->user()->student->registrations as $registration)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <h3 class="font-semibold text-gray-900">{{ $registration->course->title }}</h3>
                                    <p class="text-sm text-gray-600">{{ $registration->course->code }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                    @if($registration->status == 'confirmed') bg-green-100 text-green-800
                                    @elseif($registration->status == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($registration->status == 'cancelled') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($registration->status) }}
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                                <div>
                                    <p class="font-medium">{{ __('campus.start_date') }}:</p>
                                    <p>{{ $registration->course->start_date->format('d/m/Y') }}</p>
                                </div>
                                <div>
                                    <p class="font-medium">{{ __('campus.end_date') }}:</p>
                                    <p>{{ $registration->course->end_date->format('d/m/Y') }}</p>
                                </div>
                                <div>
                                    <p class="font-medium">{{ __('campus.season') }}:</p>
                                    <p>{{ $registration->season->name }}</p>
                                </div>
                                <div>
                                    <p class="font-medium">{{ __('campus.amount') }}:</p>
                                    <p>€{{ number_format($registration->amount, 2) }}</p>
                                </div>
                                <div>
                                    <p class="font-medium">{{ __('campus.registration_date') }}:</p>
                                    <p>{{ $registration->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="bi bi-file-earmark text-gray-400 text-4xl mb-3"></i>
                    <p class="text-gray-600">{{ __('campus.no_registrations_found') }}</p>
                    <a href="{{ route('campus.courses.index') }}" 
                       class="mt-3 inline-flex items-center text-blue-600 hover:text-blue-800">
                        <i class="bi bi-search mr-2"></i>
                        {{ __('campus.explore_courses') }}
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection