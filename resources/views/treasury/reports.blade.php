@extends('campus.shared.layout')

@section('title', __('campus.reports'))
@section('subtitle', __('campus.reports_management'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        {{-- Header --}}
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900">
                    <i class="bi bi-graph-up text-blue-600 mr-3"></i>
                    {{ __('campus.reports') }}
                </h1>
                <div class="text-sm text-gray-500">
                    {{ __('campus.reports_management') }}
                </div>
            </div>
        </div>

        {{-- Report Options --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            {{-- Financial Summary --}}
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600 mb-2">
                        <i class="bi bi-currency-euro"></i>
                    </div>
                    <div class="text-lg font-semibold text-gray-800 mb-2">
                        {{ __('campus.financial_summary') }}
                    </div>
                    <div class="text-sm text-gray-500">
                        {{ __('campus.view_financial_overview') }}
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('treasury.reports.financial') }}" 
                           class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('campus.generate_report') }}
                        </a>
                    </div>
                </div>
            </div>

            {{-- Teacher Payments --}}
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600 mb-2">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="text-lg font-semibold text-gray-800 mb-2">
                        {{ __('campus.teacher_payments') }}
                    </div>
                    <div class="text-sm text-gray-500">
                        {{ __('campus.view_teacher_payment_details') }}
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('treasury.reports.payments') }}" 
                           class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('campus.generate_report') }}
                        </a>
                    </div>
                </div>
            </div>

            {{-- RGPD Compliance --}}
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="text-center">
                    <div class="text-3xl font-bold text-purple-600 mb-2">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <div class="text-lg font-semibold text-gray-800 mb-2">
                        {{ __('campus.rgpd_compliance') }}
                    </div>
                    <div class="text-sm text-gray-500">
                        {{ __('campus.view_rgpd_compliance_status') }}
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('treasury.reports.rgpd') }}" 
                           class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('campus.generate_report') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Stats --}}
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">
                {{ __('campus.quick_stats') }}
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-blue-50 rounded">
                    <div class="text-2xl font-bold text-blue-600">0</div>
                    <div class="text-sm text-gray-600">{{ __('campus.total_payments') }}</div>
                </div>
                <div class="text-center p-4 bg-green-50 rounded">
                    <div class="text-2xl font-bold text-green-600">0</div>
                    <div class="text-sm text-gray-600">{{ __('campus.paid_teachers') }}</div>
                </div>
                <div class="text-center p-4 bg-yellow-50 rounded">
                    <div class="text-2xl font-bold text-yellow-600">0</div>
                    <div class="text-sm text-gray-600">{{ __('campus.pending_payments') }}</div>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded">
                    <div class="text-2xl font-bold text-purple-600">0</div>
                    <div class="text-sm text-gray-600">{{ __('campus.rgpd_consent_rate') }}</div>
                </div>
            </div>
        </div>

        {{-- Note --}}
        <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex">
                <i class="bi bi-info-circle text-yellow-600 mr-3"></i>
                <div>
                    <h3 class="text-sm font-medium text-yellow-800">{{ __('campus.note') }}</h3>
                    <p class="text-sm text-yellow-700">
                        {{ __('campus.reports_under_development') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
