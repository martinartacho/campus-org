@extends('campus.shared.layout')

@section('title', __('campus.registrations_management'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">{{ __('campus.registration_records') }}</h1>
        <div class="flex gap-4">
            <a href="{{ url('/campus/registrations-import') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                <i class="fas fa-upload mr-2"></i>{{ __('campus.import_registrations') }}
            </a>
            <button onclick="exportRegistrations()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                <i class="fas fa-download mr-2"></i>{{ __('campus.export_registrations') }}
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</div>
            <div class="text-sm text-gray-600">{{ __('campus.total_registrations') }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-2xl font-bold text-green-600">{{ $stats['confirmed'] }}</div>
            <div class="text-sm text-gray-600">{{ __('campus.confirmed_registrations') }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</div>
            <div class="text-sm text-gray-600">{{ __('campus.pending_registrations') }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-2xl font-bold text-blue-600">{{ $stats['paid'] }}</div>
            <div class="text-sm text-gray-600">{{ __('campus.paid_registrations') }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-2xl font-bold text-purple-600">€{{ number_format($stats['total_amount'], 2) }}</div>
            <div class="text-sm text-gray-600">{{ __('campus.total_amount') }}</div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" placeholder="{{ __('campus.search_by_nif_name_course') }}" 
                       value="{{ request('search') }}" 
                       class="w-full border rounded px-3 py-2">
            </div>
            <select name="status" class="border rounded px-3 py-2">
                <option value="">{{ __('campus.all_statuses') }}</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('campus.registration_status_pending') }}</option>
                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>{{ __('campus.registration_status_confirmed') }}</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ __('campus.registration_status_cancelled') }}</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('campus.registration_status_completed') }}</option>
            </select>
            <select name="payment_status" class="border rounded px-3 py-2">
                <option value="">{{ __('campus.all_payments') }}</option>
                <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>{{ __('campus.payment_status_pending') }}</option>
                <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>{{ __('campus.payment_status_paid') }}</option>
                <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>{{ __('campus.payment_status_partial') }}</option>
            </select>
            <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                <i class="fas fa-search mr-2"></i>{{ __('campus.search') }}
            </button>
        </form>
    </div>

    <!-- Registrations Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('campus.id') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('campus.student') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('campus.course') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('campus.date') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('campus.status') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('campus.payment') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('campus.amount') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('campus.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($registrations as $registration)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $registration->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $registration->student->first_name }}</div>
                                <div class="text-sm text-gray-500">{{ $registration->student->dni }}</div>
                                <div class="text-sm text-gray-500">{{ $registration->student->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $registration->course->title }}</div>
                                <div class="text-sm text-gray-500">{{ $registration->course->code }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $registration->registration_date }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($registration->status == 'confirmed') bg-green-100 text-green-800
                                    @elseif($registration->status == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($registration->status == 'cancelled') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $registration->formatted_status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($registration->payment_status == 'paid') bg-green-100 text-green-800
                                    @elseif($registration->payment_status == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($registration->payment_status == 'partial') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $registration->formatted_payment_status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                €{{ number_format($registration->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <form action="{{ url("/campus/registrations/{$registration->id}") }}" method="POST" onsubmit="return confirm('{{ __('campus.delete_registration_confirm') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                {{ __('campus.no_registrations_found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            <div class="flex items-center justify-between">
                <div class="flex-1 flex justify-between sm:hidden">
                    {{ $registrations->links() }}
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            {{ __('campus.showing_from_to', [
                                'from' => $registrations->firstItem(),
                                'to' => $registrations->lastItem(),
                                'total' => $registrations->total()
                            ]) }}
                        </p>
                    </div>
                    <div>
                        {{ $registrations->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function exportRegistrations() {
    fetch('{{ url("/campus/registrations-export") }}')
        .then(response => response.json())
        .then(data => {
            // Convert JSON to CSV
            const csv = convertToCSV(data);
            downloadCSV(csv, 'registrations_export.csv');
        })
        .catch(error => console.error('Error:', error));
}

function convertToCSV(data) {
    if (data.length === 0) return '';
    
    const headers = Object.keys(data[0]);
    const csvHeaders = headers.join(',');
    const csvRows = data.map(row => 
        headers.map(header => `"${row[header] || ''}"`).join(',')
    );
    
    return [csvHeaders, ...csvRows].join('\n');
}

function downloadCSV(csv, filename) {
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', filename);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>
@endsection
