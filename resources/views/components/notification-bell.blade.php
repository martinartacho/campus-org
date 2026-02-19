@auth
<div class="relative" x-data="{ open: false }" @click.outside="open = false">
    <!-- Campana -->
<button @click="open = !open" class="relative p-2 text-gray-700 hover:text-gray-900 focus:outline-none" data-bell-button>

    <i class="bi bi-bell text-xl"></i> {{ __('site.Notifications') }}
    @if($unreadCount = auth()->user()->unreadNotifications->count())
        <span id="unread-count" class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full transform translate-x-1/2 -translate-y-1/2">
            {{ $unreadCount }}
        </span>
    @endif
</button>

    <!-- Dropdown -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg z-50"
         style="display: none;">

        <div class="max-h-96 overflow-y-auto">
            @forelse(auth()->user()->notifications->take(5) as $notification)
                <a href="{{ route('notifications.show', $notification) }}"
                   class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition">
                    <div class="flex justify-between">
                        @if(!$notification->isRead())
                            <span class="font-semibold">{{ $notification->title }}</span>
                            <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full">{{ __('site.New') }}</span>
                            @else
                            <span class="{{ $notification->isRead() ? 'opacity-75' : 'font-bold' }}">{{ $notification->title }}</span>
                            <i class="bi bi-eye"></i>
                        @endif
                    </div>
                    <div class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</div>
                </a>
            @empty
                <div class="px-4 py-3 text-sm text-gray-500">{{ __('site.No_notifications') }}</div>
            @endforelse
        </div>

        <div class="border-t">
            <a href="{{ route('notifications.index') }}"
               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-center">{{ __('site.See_all') }}</a>
        </div>
        
    </div>
</div>
@endauth


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Marcar todas como leídas
        document.querySelector('.mark-all-read-form')?.addEventListener('submit', function(e) {
            e.preventDefault();
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Actualizar UI
                    document.getElementById('unread-count')?.remove();
                    document.querySelectorAll('.unread-badge').forEach(el => el.remove());
                    // Mostrar notificación de éxito
                    alert('Todas las notificaciones marcadas como leídas');
                }
            });
        });

        // Actualizar contador
        function updateUnreadCount() {
            fetch('{{ route("notifications.unread-count") }}')
                .then(res => res.json())
                .then(data => {
                    const counter = document.getElementById('unread-count');
                    if (data.count > 0) {
                        if (!counter) {
                            const badge = document.createElement('span');
                            badge.id = 'unread-count';
                            badge.className = 'absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full transform translate-x-1/2 -translate-y-1/2';
                            badge.textContent = data.count;
                            document.querySelector('[data-bell-button]').appendChild(badge);
                        } else {
                            counter.textContent = data.count;
                        }
                    } else if (counter) {
                        counter.remove();
                    }
                });
        }

        // Actualizar cada 30 segundos y al cargar
        setInterval(updateUnreadCount, 30000);
        updateUnreadCount();
    });
</script>
@endpush
