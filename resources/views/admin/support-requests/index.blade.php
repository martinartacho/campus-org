@extends('campus.shared.layout')

@section('title', 'Gestió de Sol·licituds de Suport')
@section('subtitle', 'Administra les sol·licituds de servei i incidències')

@section('content')
<div class="container mx-auto py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <i class="bi bi-ticket-perforated text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Total</p>
                        <p class="text-2xl font-semibold">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-lg">
                        <i class="bi bi-clock text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Pendents</p>
                        <p class="text-2xl font-semibold">{{ $stats['pending'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <i class="bi bi-arrow-repeat text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">En procés</p>
                        <p class="text-2xl font-semibold">{{ $stats['in_progress'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <i class="bi bi-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Resoltes</p>
                        <p class="text-2xl font-semibold">{{ $stats['resolved'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-red-100 rounded-lg">
                        <i class="bi bi-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Crítics</p>
                        <p class="text-2xl font-semibold text-red-600">{{ $stats['critical'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <form method="GET" action="{{ route('admin.support-requests.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cerca</label>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Nom, email, descripció..."
                               class="border p-2 w-full rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Estat</label>
                        <select name="status" 
                                class="border p-2 w-full rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Tots</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendent</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>En procés</option>
                            <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolt</option>
                            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Tancat</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipus</label>
                        <select name="type" 
                                class="border p-2 w-full rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Tots</option>
                            <option value="service" {{ request('type') == 'service' ? 'selected' : '' }}>🚀 Nou servei</option>
                            <option value="incident" {{ request('type') == 'incident' ? 'selected' : '' }}>⚠️ Incidència</option>
                            <option value="improvement" {{ request('type') == 'improvement' ? 'selected' : '' }}>💡 Millora</option>
                            <option value="consultation" {{ request('type') == 'consultation' ? 'selected' : '' }}>❓ Consulta</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Urgència</label>
                        <select name="urgency" 
                                class="border p-2 w-full rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Totes</option>
                            <option value="low" {{ request('urgency') == 'low' ? 'selected' : '' }}>Baixa</option>
                            <option value="medium" {{ request('urgency') == 'medium' ? 'selected' : '' }}>Mitjana</option>
                            <option value="high" {{ request('urgency') == 'high' ? 'selected' : '' }}>Alta</option>
                            <option value="critical" {{ request('urgency') == 'critical' ? 'selected' : '' }}>Crítica</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-between items-center">
                    <div class="flex space-x-2">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                            <i class="bi bi-search mr-2"></i>Filtrar
                        </button>
                        <a href="{{ route('admin.support-requests.index') }}" 
                           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                            <i class="bi bi-arrow-clockwise mr-2"></i>Netejar
                        </a>
                    </div>
                    
                    @if(request()->hasAny(['search', 'status', 'type', 'urgency']))
                        <span class="text-sm text-gray-600">
                            {{ $supportRequests->total() }} resultats trobats
                        </span>
                    @endif
                </div>
            </form>
        </div>

        <!-- Lista de solicitudes -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            @if($supportRequests->count() > 0)
                <form method="POST" action="{{ route('admin.support-requests.bulk-update') }}" id="bulkForm">
                    @csrf
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="status" id="bulkStatus">
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left">
                                        <input type="checkbox" id="selectAll" class="rounded">
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 350px;">
                                        Sol·licitud
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tipus / Urgència
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Estat / Data
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Accions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($supportRequests as $request)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <input type="checkbox" name="request_ids[]" value="{{ $request->id }}" 
                                                   class="request-checkbox rounded">
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-start">
                                                <div class="flex-shrink-0">
                                                    @if($request->user)
                                                        <img src="{{ $request->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($request->name) . '&background=3B82F6&color=fff' }}" 
                                                             alt="{{ $request->name }}" 
                                                             class="h-10 w-10 rounded-full">
                                                    @else
                                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                            <i class="bi bi-person text-gray-600"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="ml-4 flex-1 min-w-0">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $request->name }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $request->email }}
                                                    </div>
                                                    @if($request->module)
                                                        <div class="text-xs text-gray-400 mt-1">
                                                            <i class="bi bi-window mr-1"></i>{{ $request->module }}
                                                        </div>
                                                    @endif
                                                    <div class="text-sm text-gray-700 mt-1" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4;">
                                                        {{ $request->description }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col space-y-1">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    {{ $request->type == 'service' ? 'bg-blue-100 text-blue-800' : '' }}
                                                    {{ $request->type == 'incident' ? 'bg-red-100 text-red-800' : '' }}
                                                    {{ $request->type == 'improvement' ? 'bg-green-100 text-green-800' : '' }}
                                                    {{ $request->type == 'consultation' ? 'bg-purple-100 text-purple-800' : '' }}">
                                                    {{ $request->type_label }}
                                                </span>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    {{ $request->urgency == 'low' ? 'bg-green-100 text-green-800' : '' }}
                                                    {{ $request->urgency == 'medium' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                    {{ $request->urgency == 'high' ? 'bg-orange-100 text-orange-800' : '' }}
                                                    {{ $request->urgency == 'critical' ? 'bg-red-100 text-red-800' : '' }}">
                                                    {{ $request->urgency_label }}
                                                </span>
                                                @if($request->department)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        <i class="bi bi-building mr-1"></i>{{ $request->department }}
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col space-y-1">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    {{ $request->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                    {{ $request->status == 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                                                    {{ $request->status == 'resolved' ? 'bg-green-100 text-green-800' : '' }}
                                                    {{ $request->status == 'closed' ? 'bg-gray-100 text-gray-800' : '' }}">
                                                    {{ $request->status_label }}
                                                </span>
                                                <span class="text-xs text-gray-500">
                                                    {{ $request->created_at->format('d/m/Y H:i') }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('admin.support-requests.show', $request) }}" 
                                                   class="text-blue-600 hover:text-blue-900" title="Veure detalls">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                                                                <form method="POST" 
                                                      action="{{ route('admin.support-requests.destroy', $request) }}" 
                                                      class="inline"
                                                      onsubmit="return confirm('Estàs segur que vols eliminar aquesta sol·licitud?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Eliminar">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Bulk actions -->
                    <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <span class="text-sm text-gray-700">
                                    <span id="selectedCount">0</span> seleccionades
                                </span>
                                <select id="bulkActionSelect" 
                                        class="border p-2 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Acció massiva</option>
                                    <option value="pending">Marcar com a pendent</option>
                                    <option value="in_progress">Marcar com a en procés</option>
                                    <option value="resolved">Marcar com a resolt</option>
                                    <option value="closed">Marcar com a tancat</option>
                                    <option value="notify">Enviar notificació</option>
                                                                    </select>

                            </div>
                        </div>
                    </div>
                </form>
            @else
                <div class="text-center py-12">
                    <i class="bi bi-inbox text-6xl text-gray-300"></i>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No s'han trobat sol·licituds</h3>
                    <p class="mt-2 text-gray-500">No hi ha sol·licituds que coincideixin amb els filtres seleccionats.</p>
                </div>
            @endif
        </div>

        <!-- Paginación -->
        @if($supportRequests->hasPages())
            <div class="mt-6">
                {{ $supportRequests->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all checkboxes
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.request-checkbox');
    const selectedCount = document.getElementById('selectedCount');
    const bulkActionSelect = document.getElementById('bulkActionSelect');
    const bulkForm = document.getElementById('bulkForm');

    function updateSelectedCount() {
        const checked = document.querySelectorAll('.request-checkbox:checked');
        selectedCount.textContent = checked.length;
    }

    selectAll.addEventListener('change', function() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectedCount();
    });

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });

    bulkActionSelect.addEventListener('change', function() {
        if (this.value) {
            // Check if there are selected items
            const checkboxes = document.querySelectorAll('.request-checkbox:checked');
            
            if (checkboxes.length === 0) {
                alert('Si us plau, selecciona almenys una sol·licitud per aplicar aquesta acció.');
                this.value = ''; // Reset select
                return;
            }
            
            // Handle different actions
            if (this.value === 'notify') {
                // Send notifications to selected requests
                sendBulkNotifications(checkboxes);
                this.value = ''; // Reset select
                return;
            }
            
                        
            // Handle status changes with AJAX
            const status = this.value;
            const requestIds = Array.from(checkboxes).map(checkbox => checkbox.value);
            
            // Send AJAX request
            fetch('/admin/support-requests/bulk-update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken ? csrfToken.getAttribute('content') : '',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    request_ids: requestIds,
                    status: status,
                    _token: csrfToken ? csrfToken.getAttribute('content') : ''
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    // Redirect to support requests page
                    window.location.href = '{{ route("admin.support-requests.index") }}';
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error en actualitzar les sol·licituds');
            });
        }
    });
});


function sendBulkNotifications(checkboxes) {
    const count = checkboxes.length;
    
    if (!confirm(`Vols enviar una notificació a ${count} sol·licitud(s) seleccionada(s)?`)) {
        return;
    }
    
    // Collect request IDs
    const requestIds = Array.from(checkboxes).map(checkbox => checkbox.value);
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    
    // Send AJAX request
    fetch('/admin/support-requests/bulk-notify', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken ? csrfToken.getAttribute('content') : '',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            request_ids: requestIds,
            message: 'Actualització de les vostres sol·licituds de suport',
            _token: csrfToken ? csrfToken.getAttribute('content') : ''
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            // Redirect to support requests page
            window.location.href = '{{ route("admin.support-requests.index") }}';
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error en enviar les notificacions');
    });
}

</script>
@endpush
