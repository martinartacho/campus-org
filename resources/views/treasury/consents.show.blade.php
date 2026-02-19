@extends('campus.shared.layout')

@section('title', __('campus.consent_details'))
@section('subtitle', __('campus.consent_details'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        {{-- Header --}}
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        <i class="bi bi-file-earmark-text text-green-600 mr-3"></i>
                        {{ __('campus.consent_details') }}
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ __('campus.consent_id') }}: {{ $consent->id }}
                    </p>
                </div>
                
                <div class="text-right">
                    <a href="{{ route('treasury.consents') }}" 
                       class="text-blue-600 hover:text-blue-800">
                        <i class="bi bi-arrow-left mr-2"></i>
                        {{ __('campus.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>

        {{-- Detalles del consentimiento --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Información del profesor --}}
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="bi bi-person-circle text-blue-600 mr-2"></i>
                    {{ __('campus.teacher_info') }}
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('campus.name') }}</label>
                        <div class="mt-1 p-3 bg-gray-50 rounded">
                            {{ $consent->teacher->user->name }}
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('campus.email') }}</label>
                        <div class="mt-1 p-3 bg-gray-50 rounded">
                            {{ $consent->teacher->user->email }}
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('campus.phone') }}</label>
                        <div class="mt-1 p-3 bg-gray-50 rounded">
                            {{ $consent->teacher->user->phone ?? __('campus.not_specified') }}
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('campus.teacher_code') }}</label>
                        <div class="mt-1 p-3 bg-gray-50 rounded">
                            {{ $consent->teacher->teacher_code ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Información del curso --}}
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="bi bi-book text-green-600 mr-2"></i>
                    {{ __('campus.course_info') }}
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('campus.title') }}</label>
                        <div class="mt-1 p-3 bg-gray-50 rounded">
                            {{ $consent->course->title }}
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('campus.code') }}</label>
                        <div class="mt-1 p-3 bg-gray-50 rounded">
                            {{ $consent->course->code }}
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('campus.category') }}</label>
                        <div class="mt-1 p-3 bg-gray-50 rounded">
                            {{ $consent->course->category->name ?? '-' }}
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('campus.season') }}</label>
                        <div class="mt-1 p-3 bg-gray-50 rounded">
                            {{ $consent->season }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Información del consentimiento --}}
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="bi bi-shield-check text-green-600 mr-2"></i>
                    {{ __('campus.consent_info') }}
                </h2>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('campus.accepted_at') }}</label>
                            <div class="mt-1 p-3 bg-gray-50 rounded">
                                {{ $consent->accepted_at?->format('d/m/Y H:i') }}
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('campus.document_path') }}</label>
                            <div class="mt-1 p-3 bg-gray-50 rounded">
                                {{ $consent->document_path }}
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('campus.checksum') }}</label>
                            <div class="mt-1 p-3 bg-gray-50 rounded font-mono text-sm">
                                {{ $consent->checksum ?? '-' }}
                            </div>
                        </div>
                    </div>
                    
                    @if($consent->document_path)
                        <div class="mt-6">
                            <a href="{{ route('consents.download', $consent) }}" 
                               class="bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700 text-center">
                                <i class="bi bi-download mr-2"></i>
                                {{ __('campus.download_pdf') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
