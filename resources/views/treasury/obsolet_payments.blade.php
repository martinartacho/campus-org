@extends('campus.shared.layout')

@section('title', __('campus.payments'))
@section('subtitle', __('campus.payments_management'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        {{-- Header --}}
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900">
                    <i class="bi bi-cash-stack text-green-600 mr-3"></i>
                    {{ __('campus.payments') }}
                </h1>
                <div class="text-sm text-gray-500">
                    {{ __('campus.payments_management') }}
                </div>
            </div>
        </div>

        {{-- Pagos Table --}}
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('campus.teacher') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('campus.course') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('campus.amount') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('campus.date') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('campus.actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($payments as $payment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $payment->teacher->user->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $payment->course->title ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ number_format($payment->amount ?? 0, 2, ',', '.') }} €
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $payment->created_at?->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if($payment->receipt_path)
                                        <a href="{{ route('payments.download', $payment->id) }}" 
                                           class="text-blue-600 hover:text-blue-900">
                                            <i class="bi bi-download"></i>
                                            {{ __('campus.download') }}
                                        </a>
                                    @else
                                        <span class="text-gray-400">{{ __('campus.pending') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    {{ __('campus.no_payments_found') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination --}}
            @if($payments->hasPages())
                <div class="mt-6">
                    {{ $payments->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
