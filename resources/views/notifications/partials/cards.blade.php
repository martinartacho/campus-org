@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($notifications as $notification)
        <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200 border border-gray-200">
            <div class="p-6">
                <!-- Header with type badge -->
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-2">
                        @if($notification->type === 'new')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="bi bi-plus-circle mr-1"></i>{{ __('site.Type_New') }}
                            </span>
                        @elseif($notification->type === 'feedback')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <i class="bi bi-chat-dots mr-1"></i>{{ __('site.Type_Feedback') }}
                            </span>
                        @elseif($notification->type === 'system')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                <i class="bi bi-gear mr-1"></i>{{ __('site.Type_System') }}
                            </span>
                        @endif
                        @if(!$notification->isRead())
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="bi bi-circle-fill mr-1"></i>{{ __('site.New') }}
                            </span>
                        @endif
                    </div>
                    <div class="text-xs text-gray-500">
                        {{ $notification->created_at->format('d/m/Y') }}
                    </div>
                </div>

                <!-- Title -->
                <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                    {{ $notification->title }}
                </h3>

                <!-- Content preview -->
                <div class="text-sm text-gray-600 mb-4 line-clamp-3">
                    {!! Str::limit(strip_tags($notification->content), 120) !!}
                </div>

                <!-- Date and sender info -->
                <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
                    <div class="flex items-center">
                        <i class="bi bi-clock mr-1"></i>
                        {{ $notification->created_at->format('H:i') }}
                    </div>
                    @if($notification->sender)
                        <div class="flex items-center">
                            <i class="bi bi-person mr-1"></i>
                            {{ Str::limit($notification->sender->name, 20) }}
                        </div>
                    @endif
                </div>

                <!-- Action buttons -->
                <div class="flex items-center justify-between">
                    <a href="{{ route('notifications.show', $notification) }}" 
                       class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                        <i class="bi {{ $notification->isRead() ? 'bi-eye' : 'bi-eye-slash' }} mr-1"></i>
                        {{ $notification->isRead() ? __('site.View') : __('site.Read') }}
                    </a>

                    @can('edit-notification', $notification)
                        <a href="{{ route('notifications.edit', $notification) }}" 
                           class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                            <i class="bi bi-pencil-square mr-1"></i>
                            {{ __('site.Edit') }}
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    @endforeach
</div>

<!-- Pagination -->
<div class="mt-8">
    {{ $notifications->links() }}
</div>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
