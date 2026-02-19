<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
                <i class="bi bi-people-fill mr-2"></i>
                {{ __('site.Notifications') }}
            </h2>

            @can('create-notification')
            <a href="{{ route('notifications.create') }}">
                <x-primary-button>
                    <i class="bi bi-plus-lg mr-1"></i>{{ __('site.Create Notification') }} 
                </x-primary-button>
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif
                    
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('site.Title') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('site.registration_date') }}
                                </th>
                                @can('publish-notification')
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('site.Sender') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('site.Recipients') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('site.Published') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('site.Delivery') }}
                                    </th>
                                @endcan
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('site.Actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($notifications as $notification)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $notification->title }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $notification->created_at->format('d/m/Y H:i') }}</div>
                                    </td>
                                    @can('publish-notification')
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $notification->sender->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($notification->recipient_type === 'all')
                                                {{ __('site.All_users') }}
                                            @elseif($notification->recipient_type === 'role')
                                                {{ $notification->recipient_role }}
                                            @else 
                                                {{ is_array($notification->recipient_ids) 
                                                    ? count($notification->recipient_ids) 
                                                    : count(explode(',', $notification->recipient_ids)) 
                                                }} {{ __('site.Users') }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($notification->is_published)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    {{ __('site.Published') }}
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    {{ __('site.Not_published') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($notification->is_published)
                                                <div class="flex items-center space-x-2">
                                                    @if($notification->email_sent_count > 0)
                                                        <span class="inline-flex items-center">
                                                            <i class="bi bi-envelope mr-1"></i>
                                                            <span class="font-bold">{{ $notification->email_sent_count }}</span>
                                                        </span>
                                                    @endif
                                                    @if($notification->web_sent_count > 0)
                                                        <span class="inline-flex items-center">
                                                            <i class="bi bi-bell mr-1"></i>
                                                            <span class="font-bold">{{ $notification->web_sent_count }}</span>
                                                        </span>
                                                    @endif
                                                    @if($notification->push_sent_count > 0)
                                                        <span class="inline-flex items-center">
                                                            <i class="bi bi-phone mr-1"></i>
                                                            <span class="font-bold">{{ $notification->push_sent_count }}</span>
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    {{ __('site.Not_published') }}
                                                </span>
                                            @endif
                                        </td>
                                    @endcan
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-1">
                                            @can('view-notification', $notification)
                                                <a href="{{ route('notifications.show', $notification) }}" 
                                                class="text-indigo-600 hover:text-indigo-900" 
                                                title="{{ __('site.View_notification') }}">
                                                    <i class="bi {{ $notification->isRead() ? 'bi-eye' : 'bi-eye-slash' }}"></i>
                                                </a>
                                            @endcan

                                            @can('edit-notification', $notification)
                                                <a href="{{ route('notifications.edit', $notification) }}" 
                                                class="text-blue-600 hover:text-blue-900" 
                                                title="{{ __('site.Edit_notification') }}">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                            @endcan

                                            @can('delete-notification', $notification)
                                                <form action="{{ route('notifications.destroy', $notification) }}" method="POST" class="inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                        onclick="return confirm('{{ __('site.Are you sure?') }}')"
                                                        class="text-red-600 hover:text-red-900"
                                                        title="{{ __('site.Delete_notification') }}">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan

                                            @can('publish-notification')
                                                @unless($notification->is_published)
                                                    <form action="{{ route('notifications.publish', $notification) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="text-green-600 hover:text-green-900" title="{{ __('site.Publish') }}">
                                                            <i class="bi bi-send-check"></i>
                                                        </button>
                                                    </form>
                                                @endunless
                                            @endcan
                                            <!-- Acciones de send -->
                                            @can('publish-notification')
                                                @if($notification->is_published)
                                                    @if($notification->email_pending_count > 0)
                                                        <form method="POST" action="{{ route('notifications.send-email', $notification) }}" 
                                                            class="inline send-form">
                                                            @csrf
                                                            <button type="submit" class="text-green-600 hover:text-green-900" title="{{ __('site.Send_email') }}">
                                                                <i class="bi bi-envelope"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                
                                                    @if($notification->web_pending_count > 0)
                                                        <form method="POST" action="{{ route('notifications.send-web', $notification) }}" 
                                                            class="inline send-form">
                                                            @csrf
                                                            <button type="submit" class="text-blue-600 hover:text-blue-900" title="{{ __('site.Send_web') }}">
                                                                <i class="bi bi-bell"></i>
                                                            </button>
                                                        </form>
                                                    @endif

                                                    @if($notification->push_pending_count > 0)
                                                        <form method="POST" action="{{ route('notifications.send-push', $notification) }}" 
                                                            class="inline send-form">
                                                            @csrf
                                                            <button type="submit" class="text-purple-600 hover:text-purple-900" title="{{ __('site.Send_push') }}">
                                                                <i class="bi bi-phone"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4">
                        {{ $notifications->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelectorAll('.send-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const button = form.querySelector('button');
                const originalInnerHTML = button.innerHTML;
                
                // Mostrar spinner en el botón
                button.innerHTML = '<i class="bi bi-arrow-repeat animate-spin"></i>';
                button.disabled = true;
                
                // Mostrar SweetAlert2 de carga
                Swal.fire({
                    title: '{{ __("site.Processing") }}',
                    text: '{{ __("site.Please wait") }}...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                        
                        // Enviar el formulario después de un pequeño retraso para permitir que se vea el loader
                        setTimeout(() => {
                            form.submit();
                        }, 100);
                    }
                });
            });
        });
    </script>
</x-app-layout>