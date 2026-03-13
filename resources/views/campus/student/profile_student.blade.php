@extends('campus.shared.layout')

@section('title', __('campus.student_profile'))
@section('subtitle', __('campus.my_academic_info'))

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
                {{ __('campus.student_profile') }}
            </span>
        </div>
    </li>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Perfil del Estudiante --}}
    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">
                <i class="bi bi-person-circle mr-2"></i>{{ __('campus.student_profile') }}
            </h2>
        </div>
        
        <div class="p-6">
            @if(auth()->user()->student)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Información Personal --}}
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('campus.personal_info') }}</h3>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('campus.full_name') }}</label>
                                <p class="mt-1 text-sm text-gray-900">{{ auth()->user()->name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('campus.email') }}</label>
                                <p class="mt-1 text-sm text-gray-900">{{ auth()->user()->email }}</p>
                            </div>
                            @if(auth()->user()->student->phone)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('campus.phone') }}</label>
                                <p class="mt-1 text-sm text-gray-900">{{ auth()->user()->student->phone }}</p>
                            </div>
                            @endif
                            @if(auth()->user()->student->birth_date)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('campus.birth_date') }}</label>
                                <p class="mt-1 text-sm text-gray-900">{{ auth()->user()->student->birth_date->format('d/m/Y') }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    {{-- Información Académica --}}
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('campus.academic_info') }} Información Académica</h3>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('campus.student_code') }}</label>
                                <p class="mt-1 text-sm text-gray-900">{{ auth()->user()->student->student_code ?? '---' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('campus.enrollment_date') }}</label>
                                <p class="mt-1 text-sm text-gray-900">{{ auth()->user()->student->created_at->format('d/m/Y') }}</p>
                            </div>
                            @if(auth()->user()->student->specialization)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('campus.specialization') }}</label>
                                <p class="mt-1 text-sm text-gray-900">{{ auth()->user()->student->specialization }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                {{-- Acciones --}}
                <div class="mt-6 pt-6 border-t border-gray-200">
                    
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('profile.edit') }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                            <i class="bi bi-pencil mr-2"></i>{{ __('campus.edit_profile') }}
                        </a>
                        <a href="{{ route('campus.student.history') }}" 
                           class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">
                            <i class="bi bi-clock-history mr-2"></i>{{ __('campus.academic_history') }}
                        </a>
                        <a href="{{ route('campus.student.registrations') }}" 
                           class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-md hover:bg-purple-700">
                            <i class="bi bi-file-earmark-text mr-2"></i>{{ __('campus.my_registrations') }}
                        </a>
                    </div>
                </div>
            @else
                <div class="text-center py-8">
                    <i class="bi bi-exclamation-triangle text-gray-400 text-4xl mb-3"></i>
                    <p class="text-gray-600">{{ __('campus.student_profile_not_found') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection