<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('site.Notification') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6 space-y-6">

                <!-- Encabezado -->
                <div>
                    <h2 class="text-2xl font-semibold text-gray-800">{{ $notification->title }}</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ __('site.Sent by') }}: <span class="font-medium text-gray-700">{{ $notification->sender->name }}</span>
                        {{ __('site.the') }} {{ $notification->created_at->format('d/m/Y H:i') }}
                    </p>
                </div>

                <!-- Contenido -->
                <div class="prose max-w-none text-gray-700">
                    {!! nl2br(e($notification->content)) !!}
                </div>

                <!-- Destinatarios -->
                <div>
                    <p class="text-sm text-gray-600">
                        <strong>Destinatarios:</strong>
                        @if($notification->recipient_type === 'all')
                            {{ __('site.All_users') }}
                        @elseif($notification->recipient_type === 'role')
                        {{ __('site.Users_role') }}: <span class="font-medium">{{ $notification->recipient_role }}</span>
                        @else
                        {{ __('site.Specific_users') }}: <span class="font-medium">{{ count($notification->recipient_ids) }}</span>
                        @endif
                    </p>
                </div>

                <!-- Estado -->
                <div>
                    <p class="text-sm text-gray-600">
                        <strong>{{ __('site.State') }}:</strong>
                        @if($notification->is_published)
                        {{ __('site.Published_on') }} <span class="font-medium">{{ $notification->published_at->format('d/m/Y H:i') }}</span>
                        @else
                            <span class="text-red-500 font-medium">{{ __('site.Not published') }}</span>
                        @endif
                    </p>
                </div>

                <!-- Acciones -->
                <div class="flex justify-between pt-4">
                    <a href="{{ route('notifications.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                        {{ __('site.go_back') }}
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
