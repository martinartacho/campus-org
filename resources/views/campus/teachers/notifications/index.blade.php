@extends('campus.shared.layout')

@section('title', __('campus.teacher_notifications'))
@section('subtitle', __('campus.notification_history'))

@section('breadcrumbs')
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <a href="{{ route('campus.teachers.index') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2">
                {{ __('campus.teachers') }}
            </a>
        </div>
    </li>
    <li aria-current="page">
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2">
                {{ __('campus.teacher_notifications') }}
            </span>
        </div>
    </li>
@endsection

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Capçalera -->
    <div class="bg-white shadow-sm rounded-lg border mb-6">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">
                    <i class="bi bi-bell-fill me-2 text-blue-600"></i>
                    {{ __('campus.teacher_notifications') }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    {{ __('campus.notification_history_description') }}
                </p>
            </div>
            <a href="{{ route('campus.teachers.notifications.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="bi bi-plus-circle me-2"></i>
                {{ __('campus.create_notification') }}
            </a>
        </div>

        <!-- Filtres -->
        <div class="p-6 border-b bg-gray-50">
            <form method="GET" action="{{ route('campus.teachers.notifications.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Cerca -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('campus.search') }}
                        </label>
                        <input type="text" 
                               id="search" 
                               name="search" 
                               value="{{ request('search') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                               placeholder="{{ __('campus.search_notifications') }}">
                    </div>

                    <!-- Data d'inici -->
                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('campus.date_from') }}
                        </label>
                        <input type="date" 
                               id="date_from" 
                               name="date_from" 
                               value="{{ request('date_from') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Data final -->
                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('campus.date_to') }}
                        </label>
                        <input type="date" 
                               id="date_to" 
                               name="date_to" 
                               value="{{ request('date_to') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Remitent -->
                    <div>
                        <label for="sender_id" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('campus.sender') }}
                        </label>
                        <select id="sender_id" 
                                name="sender_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <option value="">{{ __('campus.all_senders') }}</option>
                            @foreach($senders as $sender)
                                <option value="{{ $sender->id }}" {{ request('sender_id') == $sender->id ? 'selected' : '' }}>
                                    {{ $sender->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Botons de filtre -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('campus.teachers.notifications.index') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        {{ __('campus.clear_filters') }}
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="bi bi-search me-2"></i>
                        {{ __('campus.filter') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Llistat de notificacions -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('campus.notification') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('campus.sender') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('campus.recipients') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('campus.date') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('campus.status') }}
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('campus.actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($notifications as $notification)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        @switch($notification->type)
                                            @case('info')
                                                <i class="bi bi-info-circle-fill text-blue-500"></i>
                                                @break
                                            @case('warning')
                                                <i class="bi bi-exclamation-triangle-fill text-yellow-500"></i>
                                                @break
                                            @case('success')
                                                <i class="bi bi-check-circle-fill text-green-500"></i>
                                                @break
                                            @case('error')
                                                <i class="bi bi-x-circle-fill text-red-500"></i>
                                                @break
                                            @default
                                                <i class="bi bi-bell-fill text-gray-500"></i>
                                        @endswitch
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $notification->title }}
                                        </div>
                                        <div class="text-sm text-gray-500 truncate max-w-xs">
                                            {{ Str::limit($notification->content, 80) }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    {{ $notification->sender?->name ?? __('campus.system') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    {{ $notification->recipients->count() }} {{ __('campus.teachers') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    {{ $notification->created_at->format('d/m/Y H:i') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
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
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                <a href="{{ route('campus.teachers.notifications.show', $notification) }}" 
                                   class="text-blue-600 hover:text-blue-900 mr-3">
                                    {{ __('campus.view') }}
                                </a>
                                @if(auth()->user()->hasRole(['admin', 'manager']))
                                    <button onclick="deleteNotification({{ $notification->id }})" 
                                            class="text-red-600 hover:text-red-900">
                                        {{ __('campus.delete') }}
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <i class="bi bi-bell text-4xl mb-3"></i>
                                    <div class="text-lg font-medium">{{ __('campus.no_notifications') }}</div>
                                    <div class="text-sm mt-1">
                                        {{ __('campus.no_notifications_description') }}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginació -->
        @if($notifications->hasPages())
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Formulari d'eliminació -->
<form id="delete-form" method="POST" action="#" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function deleteNotification(id) {
    if (confirm('{{ __("campus.confirm_delete_notification") }}')) {
        const form = document.getElementById('delete-form');
        form.action = '{{ route("campus.teachers.notifications.destroy", ":id") }}'.replace(':id', id);
        form.submit();
    }
}
</script>
@endsection
