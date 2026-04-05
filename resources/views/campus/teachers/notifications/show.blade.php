@extends('campus.shared.layout')

@section('title', __('campus.notification_details'))
@section('subtitle', __('campus.teacher_notifications'))

@section('breadcrumbs')
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <a href="{{ route('campus.teachers.index') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2">
                {{ __('campus.teachers') }}
            </a>
        </div>
    </li>
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <a href="{{ route('campus.teachers.notifications.index') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2">
                {{ __('campus.teacher_notifications') }}
            </a>
        </div>
    </li>
    <li aria-current="page">
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2">
                {{ __('campus.notification_details') }}
            </span>
        </div>
    </li>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Capçalera -->
    <div class="bg-white shadow-sm rounded-lg border mb-6">
        <div class="px-6 py-4 border-b">
            <div class="flex justify-between items-start">
                <div class="flex items-center">
                    <div class="flex-shrink-0 mr-4">
                        @switch($notification->type)
                            @case('info')
                                <i class="bi bi-info-circle-fill text-blue-500 text-2xl"></i>
                                @break
                            @case('warning')
                                <i class="bi bi-exclamation-triangle-fill text-yellow-500 text-2xl"></i>
                                @break
                            @case('success')
                                <i class="bi bi-check-circle-fill text-green-500 text-2xl"></i>
                                @break
                            @case('error')
                                <i class="bi bi-x-circle-fill text-red-500 text-2xl"></i>
                                @break
                            @default
                                <i class="bi bi-bell-fill text-gray-500 text-2xl"></i>
                        @endswitch
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">
                            {{ $notification->title }}
                        </h2>
                        <div class="mt-1 flex items-center space-x-4 text-sm text-gray-500">
                            <span>
                                <i class="bi bi-person mr-1"></i>
                                {{ $notification->sender?->name ?? __('campus.system') }}
                            </span>
                            <span>
                                <i class="bi bi-calendar mr-1"></i>
                                {{ $notification->created_at->format('d/m/Y H:i') }}
                            </span>
                            <span>
                                <i class="bi bi-people mr-1"></i>
                                {{ $notification->recipients->count() }} {{ __('campus.recipients') }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                        @if($notification->send_immediately && $notification->sent_at) 
                            bg-green-100 text-green-800
                        @elseif($notification->send_immediately && !$notification->sent_at)
                            bg-yellow-100 text-yellow-800
                        @else
                            bg-gray-100 text-gray-800
                        @endif">
                        @if($notification->send_immediately && $notification->sent_at)
                            {{ __('campus.sent') }}
                        @elseif($notification->send_immediately && !$notification->sent_at)
                            {{ __('campus.pending') }}
                        @else
                            {{ __('campus.draft') }}
                        @endif
                    </span>
                    @if($notification->sent_at)
                        <span class="text-sm text-gray-500">
                            {{ __('campus.sent_at') }}: {{ $notification->sent_at->format('d/m/Y H:i') }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Contingut -->
        <div class="px-6 py-4">
            <div class="prose max-w-none">
                <h3 class="text-sm font-medium text-gray-700 mb-2">{{ __('campus.notification_content') }}</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-gray-800 whitespace-pre-wrap">{{ $notification->content }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Destinataris -->
    <div class="bg-white shadow-sm rounded-lg border">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="bi bi-people-fill me-2 text-blue-600"></i>
                {{ __('campus.notification_recipients') }}
            </h3>
            <p class="text-sm text-gray-600 mt-1">
                {{ __('campus.notification_recipients_description') }}
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('campus.teacher') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('campus.email') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('campus.payment_type') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('campus.iban_status') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('campus.pdf_status') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($notification->recipients->sortBy(function($recipient) {
                        return ($recipient->teacherProfile->last_name ?? '') . ' ' . ($recipient->teacherProfile->first_name ?? '');
                    }) as $recipient)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $recipient->teacherProfile?->first_name }} {{ $recipient->teacherProfile?->last_name }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    {{ $recipient->email }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    @switch($recipient->teacherProfile?->payment_type)
                                        @case('waived')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ __('campus.payment_waived') }}
                                            </span>
                                            @break
                                        @case('own')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ __('campus.payment_own') }}
                                            </span>
                                            @break
                                        @case('ceded')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                {{ __('campus.payment_ceded') }}
                                            </span>
                                            @break
                                        @default
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ __('campus.undefined') }}
                                            </span>
                                    @endswitch
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    @if($recipient->teacherProfile?->iban)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="bi bi-check-circle-fill mr-1"></i>
                                            {{ __('campus.has_iban') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="bi bi-x-circle-fill mr-1"></i>
                                            {{ __('campus.no_iban') }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    @if($recipient->teacherProfile?->hasPdfs())
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="bi bi-file-earmark-pdf-fill mr-1"></i>
                                            {{ __('campus.has_pdfs') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="bi bi-file-earmark-x mr-1"></i>
                                            {{ __('campus.no_pdfs') }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Estadístiques -->
        <div class="px-6 py-4 border-t bg-gray-50">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900">
                        {{ $notification->recipients->count() }}
                    </div>
                    <div class="text-sm text-gray-600">{{ __('campus.total_recipients') }}</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">
                        {{ $notification->recipients->where('teacherProfile.payment_type', 'waived')->count() }}
                    </div>
                    <div class="text-sm text-gray-600">{{ __('campus.payment_waived') }}</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">
                        {{ $notification->recipients->filter(fn($r) => $r->teacherProfile?->iban)->count() }}
                    </div>
                    <div class="text-sm text-gray-600">{{ __('campus.with_iban') }}</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-orange-600">
                        {{ $notification->recipients->filter(fn($r) => $r->teacherProfile?->hasPdfs())->count() }}
                    </div>
                    <div class="text-sm text-gray-600">{{ __('campus.with_pdfs') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botons d'acció -->
    <div class="flex justify-between items-center mt-6">
        <a href="{{ route('campus.teachers.notifications.index') }}" 
           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <i class="bi bi-arrow-left me-2"></i>
            {{ __('campus.back_to_notifications') }}
        </a>
        
        @if(auth()->user()->hasRole(['admin', 'manager']))
            <button onclick="deleteNotification({{ $notification->id }})" 
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                <i class="bi bi-trash me-2"></i>
                {{ __('campus.delete_notification') }}
            </button>
        @endif
    </div>
</div>

<!-- Formulari d'eliminació -->
<form id="delete-form" method="POST" action="{{ route('campus.teachers.notifications.destroy', $notification) }}" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function deleteNotification() {
    if (confirm('{{ __("campus.confirm_delete_notification") }}')) {
        document.getElementById('delete-form').submit();
    }
}
</script>
@endsection
