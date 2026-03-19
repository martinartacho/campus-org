{{-- resources/views/components/dashboard/widgets/support_tickets.blade.php --}}

<div class="bg-white p-6 rounded-lg shadow-md mb-6">
    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
        <i class="bi bi-headset me-2 text-purple-600"></i>
        Tiquets de Suport (widgets/support_tickets linia 6)
    </h2>

    @php
        // TEMPORAL: Datos de ejemplo mientras no existe el modelo SupportTicket
        $openTickets = 0;
        $pendingTickets = 2;
        $resolvedToday = 5;
        $totalTickets = 15;
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
        </div>
    </div>

    @php
        // TEMPORAL: Datos de ejemplo mientras no existe el modelo SupportTicket
        $recentTickets = collect([
            (object) ['subject' => 'Problema con login', 'user' => (object) ['name' => 'Joan Pérez'], 'status' => 'open', 'created_at' => now()->subMinutes(30)],
            (object) ['subject' => 'Error en matriculación', 'user' => (object) ['name' => 'Maria García'], 'status' => 'pending', 'created_at' => now()->subHours(2)],
        ]);
    @endphp

    <div class="space-y-2">
        <h3 class="text-sm font-semibold text-gray-700 mb-2">Tiquets recents</h3>
        @forelse($recentTickets as $ticket)
            <div class="flex justify-between items-center text-sm border-b pb-2">
                <div class="flex-1">
                    <div class="font-medium">{{ $ticket->subject }}</div>
                    <div class="text-gray-500">{{ $ticket->user->name ?? '-' }}</div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="px-2 py-1 text-xs rounded-full 
                        @if($ticket->status == 'open') bg-red-100 text-red-800
                        @elseif($ticket->status == 'pending') bg-yellow-100 text-yellow-800
                        @else bg-green-100 text-green-800 @endif">
                        {{ $ticket->status }}
                    </span>
                    <div class="text-gray-400">
                        {{ $ticket->created_at->diffForHumans() }}
                    </div>
                </div>
            </div>
        @empty
            <p class="text-gray-500 text-sm">No hi ha tiquets pendents</p>
        @endforelse
    </div>
</div>
