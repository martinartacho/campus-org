{{-- resources/views/components/dashboard/widgets/support_tickets.blade.php --}}

<div class="bg-white p-6 rounded-lg shadow-md mb-6">
    <!-- Header con acordeón -->
    <div x-data="{ open: false }" class="border-b border-gray-200">
        <button @click="open = !open" 
                class="w-full flex items-center justify-between py-3 text-left hover:bg-gray-50 transition-colors">
            <div class="flex items-center">
                <i class="bi bi-headset me-2 text-purple-600"></i>
                <h2 class="text-lg font-bold text-gray-800">Tiquets de Suport</h2>
            </div>
            <i class="bi transition-transform duration-200" 
               :class="open ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
        </button>
        
        <!-- Contenido del acordeón (solo visible cuando open = true) -->
        <div x-show="open" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform -translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform -translate-y-2"
             class="pt-4">
            
            @php
                // Usar datos reales del modelo SupportRequest
                $openTickets = \App\Models\SupportRequest::where('status', 'open')->count();
                $pendingTickets = \App\Models\SupportRequest::where('status', 'pending')->count();
                $resolvedToday = \App\Models\SupportRequest::where('status', 'resolved')
                    ->whereDate('updated_at', today())->count();
                $totalTickets = \App\Models\SupportRequest::count();
            @endphp

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                <div class="text-center p-4 bg-red-50 rounded-lg">
                    <div class="text-2xl font-bold text-red-700">{{ $openTickets }}</div>
                    <div class="text-sm text-gray-600">Oberts</div>
                </div>
                
                <div class="text-center p-4 bg-yellow-50 rounded-lg">
                    <div class="text-2xl font-bold text-yellow-700">{{ $pendingTickets }}</div>
                    <div class="text-sm text-gray-600">Pendents</div>
                </div>
                
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <div class="text-2xl font-bold text-green-700">{{ $resolvedToday }}</div>
                    <div class="text-sm text-gray-600">Resolts avui</div>
                </div>
                
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <div class="text-2xl font-bold text-blue-700">{{ $totalTickets }}</div>
                    <div class="text-sm text-gray-600">Total</div>
                    @if($openTickets + $pendingTickets > 0)
                        <div class="mt-2">
                            <a href="{{ route('admin.support-requests.index') }}" 
                               class="inline-flex items-center text-xs text-orange-600 hover:text-orange-800">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                {{ $openTickets + $pendingTickets }} pendents
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Lista de tickets recientes -->
            <div class="space-y-2">
                @php
                    $recentTickets = \App\Models\SupportRequest::with('user')
                        ->latest()
                        ->limit(5)
                        ->get();
                @endphp
                
                @forelse($recentTickets as $ticket)
                    <div class="flex justify-between items-center text-sm border-b pb-2">
                        <div class="flex-1">
                            <div class="font-medium">{{ $ticket->subject }}</div>
                            <div class="text-gray-500 text-xs">{{ $ticket->user->name ?? 'Usuari desconegut' }} • {{ $ticket->created_at->diffForHumans() }}</div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($ticket->status == 'open') bg-red-100 text-red-800
                                @elseif($ticket->status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($ticket->status == 'resolved') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $ticket->status == 'open' ? 'Obert' : ($ticket->status == 'pending' ? 'Pendent' : 'Resolt') }}
                            </span>
                            <div class="text-gray-400 text-xs">
                                #{{ $ticket->id }}
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No hi ha tiquets de suport recents</p>
                @endforelse
            </div>

            @if($totalTickets > 0)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <a href="{{ route('admin.support-requests.index') }}" 
                       class="inline-flex items-center text-sm text-purple-600 hover:text-purple-800">
                        Veure tots els tiquets de suport 
                        <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
