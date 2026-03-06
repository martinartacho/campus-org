@extends('campus.shared.layout')

@section('title', 'Validar Ordres WordPress')
@section('subtitle', 'Revisar i assignar cursos a les ordres importades')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Validar Ordres WordPress</h1>
        <p class="mt-2 text-gray-600">Revisa i assigna cursos a les ordres importades</p>
    </div>

    <!-- Ordres Pendents de Validació -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Ordres per Processar</h3>
            <span class="text-sm text-gray-500">{{ $pendingOrdres->total() }} ordres</span>
        </div>
        
        <!-- Filtre per Codi WP i Cercador Global -->
        <div class="mb-4">
            <div class="flex items-center space-x-4">
                <div class="flex-1 max-w-xs">
                    <input type="text" 
                           id="codeFilter" 
                           placeholder="Filtrar per Codi WP..." 
                           value="{{ request('code_filter') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex-1 max-w-md">
                    <input type="text" 
                           id="globalSearch" 
                           placeholder="Cercar per alumne, email, curs o codi..." 
                           value="{{ request('search') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <button onclick="clearFilters()" class="px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-md">
                    <i class="bi bi-x-circle mr-1"></i>Netejar
                </button>
            </div>
            <div class="mt-2 text-sm text-gray-600">
                <span id="searchResults">
                    @if(request('search') || request('code_filter'))
                        Mostrant {{ $pendingOrdres->count() }} de {{ $pendingOrdres->total() }} ordres (filtrat)
                    @else
                        Mostrant {{ $pendingOrdres->count() }} de {{ $pendingOrdres->total() }} ordres
                    @endif
                </span>
            </div>
        </div>
        
        <!-- Accions Massives -->
        <div class="mb-4 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <input type="checkbox" id="selectAll" class="mr-2">
                <label for="selectAll" class="text-sm text-gray-600">Seleccionar tots</label>
            </div>
            <div class="flex items-center space-x-2">
                <button onclick="bulkAutoMatch()" class="px-3 py-2 text-sm bg-orange-600 text-white rounded-md hover:bg-orange-700">
                    <i class="bi bi-link-45deg mr-1"></i>Aparellar
                </button>
                <button onclick="bulkAssignCode()" class="px-3 py-2 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50" id="bulkAssignCodeBtn" disabled>
                    <i class="bi bi-tag mr-1"></i>Assignar Codi
                </button>
                <button onclick="bulkChangePayment()" class="px-3 py-2 text-sm bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50" id="bulkChangePaymentBtn" disabled>
                    <i class="bi bi-credit-card mr-1"></i>Canviar Pagament
                </button>
                <button onclick="bulkProcess()" class="px-3 py-2 text-sm bg-purple-600 text-white rounded-md hover:bg-purple-700 disabled:opacity-50" id="bulkProcessBtn" disabled>
                    <i class="bi bi-check-circle mr-1"></i>Processar
                </button>
                <button onclick="bulkDelete()" class="px-3 py-2 text-sm bg-red-600 text-white rounded-md hover:bg-red-700 disabled:opacity-50" id="bulkDeleteBtn" disabled>
                    <i class="bi bi-trash mr-1"></i>Eliminar
                </button>
            </div>
        </div>
        
        @if($pendingOrdres->count() > 0)
            <!-- Taula Compacta amb Scroll Horizontal -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="ordresTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left">
                                <input type="checkbox" class="pending-select-all">
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alumne</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Curs</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Accions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($pendingOrdres as $ordre)
                            <tr class="hover:bg-gray-50 ordre-row" 
                                data-code="{{ $ordre->wp_code }}" 
                                data-name="{{ $ordre->wp_first_name }} {{ $ordre->wp_last_name }}" 
                                data-email="{{ $ordre->wp_email }}" 
                                data-course="{{ $ordre->wp_item_name }}"
                                data-status="{{ $ordre->validation_status }}">
                                <td class="px-4 py-4">
                                    <input type="checkbox" name="ordre_ids[]" value="{{ $ordre->id }}" class="pending-checkbox bulk-checkbox">
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-sm">
                                        <div class="font-medium text-gray-900">{{ $ordre->wp_first_name }} {{ $ordre->wp_last_name }}</div>
                                        <div class="text-gray-500 text-xs">{{ $ordre->wp_email }}</div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-sm">
                                        <div class="text-gray-900 font-medium truncate max-w-xs" title="{{ $ordre->wp_item_name }}">
                                            {{ $ordre->wp_item_name }}
                                        </div>
                                        <div class="flex items-center space-x-2 mt-1">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                {{ $ordre->wp_code }}
                                            </span>
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                                @if($ordre->isPaid()) bg-green-100 text-green-800
                                                @else bg-yellow-100 text-yellow-800 @endif">
                                                {{ $ordre->payment_status_label }}
                                            </span>
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                                @if($ordre->validation_status === 'matched') bg-blue-100 text-blue-800
                                                @elseif($ordre->validation_status === 'pending') bg-yellow-100 text-yellow-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ $ordre->validation_status }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                    @if($ordre->validation_status === 'matched')
                                        <button type="button" 
                                                onclick="bulkProcess()" 
                                                class="text-green-600 hover:text-green-900 mr-2">
                                            <i class="bi bi-check-circle"></i> Processar
                                        </button>
                                    @else
                                        <button type="button" 
                                                onclick="showMatchModal({{ $ordre->id }})" 
                                                class="text-blue-600 hover:text-blue-900 mr-2">
                                            <i class="bi bi-link-45deg"></i> Assignar
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Paginació -->
            <div class="mt-4">
                {{ $pendingOrdres->links() }}
            </div>
        @else
            <div class="text-center py-8">
                <i class="bi bi-check-circle text-green-500 text-4xl mb-4"></i>
                <p class="text-gray-600">No hi ha ordres pendents de validació</p>
            </div>
        @endif
    </div>

    <!-- Ordres amb Revisió Manual -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Revisió Manual Requerida</h3>
            <span class="text-sm text-gray-500">{{ $manualOrdres->total() }} ordres</span>
        </div>
        
        @if($manualOrdres->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alumne</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Curs WP</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Codi WP</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Curs Assignat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Accions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($manualOrdres as $ordre)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $ordre->wp_first_name }} {{ $ordre->wp_last_name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $ordre->wp_item_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        {{ $ordre->wp_code }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($ordre->course)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            {{ $ordre->course->title }}
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            Sense assignar
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $ordre->validation_notes ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button type="button" 
                                            onclick="showMatchModal({{ $ordre->id }})" 
                                            class="text-blue-600 hover:text-blue-900 mr-2">
                                        <i class="bi bi-link-45deg"></i> Assignar
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <i class="bi bi-check-circle text-green-500 text-4xl mb-4"></i>
                <p class="text-gray-600">No hi ha ordres amb revisió manual</p>
            </div>
        @endif
    </div>

    <!-- Ordres amb Errors -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Ordres amb Errors</h3>
            <span class="text-sm text-gray-500">{{ $errorOrdres->total() }} ordres</span>
        </div>
        
        @if($errorOrdres->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alumne</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Codi WP</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Error</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Accions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($errorOrdres as $ordre)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $ordre->wp_first_name }} {{ $ordre->wp_last_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        {{ $ordre->wp_code }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-red-600">
                                    {{ $ordre->validation_notes }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button type="button" 
                                            onclick="showMatchModal({{ $ordre->id }})" 
                                            class="text-blue-600 hover:text-blue-900">
                                        <i class="bi bi-arrow-clockwise"></i> Reintentar
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <p>No hi ha ordres amb errors</p>
            </div>
        @endif
    </div>
</div>

<!-- Modal de Matching -->
<div id="matchModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3">
            <h3 class="text-lg font-semibold text-gray-900">Assignar Curs a l'Ordre</h3>
            <button type="button" onclick="closeMatchModal()" class="text-gray-400 hover:text-gray-900">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        
        <form id="matchForm" action="{{ route('campus.ordres.process') }}" method="POST">
            @csrf
            <input type="hidden" name="season_id" value="{{ \App\Models\CampusSeason::where('is_current', true)->first()?->id ?? 1 }}">
            <input type="hidden" id="ordreId" name="ordre_ids[]" value="">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Dades de l'Ordre</label>
                    <div class="bg-gray-50 p-3 rounded text-sm">
                        <div><strong>Nom:</strong> <span id="modalName"></span></div>
                        <div><strong>Email:</strong> <span id="modalEmail"></span></div>
                        <div><strong>Curs WP:</strong> <span id="modalCourse"></span></div>
                        <div><strong>Codi WP:</strong> <span id="modalCode"></span></div>
                    </div>
                </div>
                
                <div>
                    <label for="courseSelect" class="block text-sm font-medium text-gray-700 mb-2">Seleccionar Curs</label>
                    <select id="courseSelect" name="course_id" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Selecciona un curs --</option>
                    </select>
                </div>
                
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeMatchModal()" class="btn btn-secondary">
                        Cancel·lar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle mr-2"></i>Assignar i Processar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Carregar cursos dinàmicament
let courses = [];

document.addEventListener('DOMContentLoaded', function() {
    loadCourses();
    setupBulkActions();
    setupFilter();
});

function loadCourses() {
    fetch('/campus/api/courses')
        .then(response => response.json())
        .then(data => {
            courses = data;
            updateCourseSelect();
        })
        .catch(error => {
            console.error('Error carregant cursos:', error);
        });
}

// Configurar accions massives
function setupBulkActions() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.bulk-checkbox');
    const bulkButtons = ['bulkAssignCodeBtn', 'bulkChangePaymentBtn', 'bulkDeleteBtn'];
    
    // Seleccionar tots
    selectAll.addEventListener('change', function() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkButtons();
    });
    
    // Actualitzar botons quan canvia la selecció
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkButtons);
    });
}

// Actualitzar estat dels botons massius
function updateBulkButtons() {
    const checkedBoxes = document.querySelectorAll('.bulk-checkbox:checked');
    const hasSelection = checkedBoxes.length > 0;
    
    console.log('updateBulkButtons cridat. checkedBoxes:', checkedBoxes.length, 'hasSelection:', hasSelection);
    
    document.getElementById('bulkAssignCodeBtn').disabled = !hasSelection;
    document.getElementById('bulkChangePaymentBtn').disabled = !hasSelection;
    document.getElementById('bulkProcessBtn').disabled = !hasSelection;
    document.getElementById('bulkDeleteBtn').disabled = !hasSelection;
    
    console.log('bulkProcessBtn.disabled:', document.getElementById('bulkProcessBtn').disabled);
}

// Configurar filtre
function setupFilter() {
    const codeFilter = document.getElementById('codeFilter');
    const globalSearch = document.getElementById('globalSearch');
    let searchTimeout;
    
    // Filtre per codi WP
    codeFilter.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            performServerSearch();
        }, 300); // 300ms delay
    });
    
    // Cercador global
    globalSearch.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            performServerSearch();
        }, 300); // 300ms delay
    });
}

// Realitzar cerca al servidor
function performServerSearch() {
    const codeFilter = document.getElementById('codeFilter').value;
    const globalSearch = document.getElementById('globalSearch').value;
    
    // Construir URL amb paràmetres
    const url = new URL(window.location.href);
    url.searchParams.set('search', globalSearch);
    url.searchParams.set('code_filter', codeFilter);
    url.searchParams.set('page', '1'); // Tornar a pàgina 1
    
    // Redirigir a la nova URL
    window.location.href = url.toString();
}

// Netejar tots els filtres
function clearFilters() {
    const url = new URL(window.location.href);
    url.searchParams.delete('search');
    url.searchParams.delete('code_filter');
    url.searchParams.set('page', '1');
    
    window.location.href = url.toString();
}

// Accions massives
function bulkAutoMatch() {
    const confirmAutoMatch = confirm('Estàs segur que vols executar l\'auto-matching? Això intentarà assignar automàticament cursos basant-se en els codis.');
    if (!confirmAutoMatch) return;
    
    fetch('/campus/ordres/auto-match', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload(); // Recarregar per veure canvis
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de connexió');
    });
}

function bulkAssignCode() {
    const selectedIds = getSelectedIds();
    if (selectedIds.length === 0) return;
    
    const newCode = prompt('Introdueix el nou codi per als registres seleccionats:');
    if (!newCode) return;
    
    fetch('/campus/ordres/bulk-assign-code', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            ordre_ids: selectedIds,
            code: newCode
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload(); // Recarregar per veure canvis
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de connexió');
    });
}

function bulkChangePayment() {
    const selectedIds = getSelectedIds();
    if (selectedIds.length === 0) return;
    
    // Crear modal per seleccionar estat de pagament
    const modal = document.createElement('div');
    modal.innerHTML = `
        <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center;">
            <div style="background: white; padding: 20px; border-radius: 8px; min-width: 300px;">
                <h3 style="margin: 0 0 15px 0;">Canviar Estat de Pagament</h3>
                <p style="margin: 0 0 15px 0; color: #666;">Selecciona el nou estat de pagament per ${selectedIds.length} registres:</p>
                <select id="paymentStatusSelect" style="width: 100%; padding: 8px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px;">
                    <option value="paid">Pagat</option>
                    <option value="pending">Pendent</option>
                </select>
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button onclick="this.closest('div[style*=position]').remove()" style="padding: 8px 16px; border: 1px solid #ddd; background: #f5f5f5; border-radius: 4px; cursor: pointer;">Cancel·lar</button>
                    <button onclick="confirmBulkPayment()" style="padding: 8px 16px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">Confirmar</button>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
}

function confirmBulkPayment() {
    const selectedIds = getSelectedIds();
    const paymentStatus = document.getElementById('paymentStatusSelect').value;
    
    // Tancar modal
    document.querySelector('div[style*=position]').remove();
    
    fetch('/campus/ordres/bulk-change-payment', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            ordre_ids: selectedIds,
            payment_status: paymentStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload(); // Recarregar per veure canvis
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de connexió');
    });
}

function bulkProcess() {
    console.log('bulkProcess() cridat');
    
    const selectedIds = getSelectedIds();
    console.log('selectedIds:', selectedIds);
    
    if (selectedIds.length === 0) {
        console.log('No hi ha IDs seleccionats');
        return;
    }
    
    const confirmProcess = confirm('Estàs segur que vols processar ' + selectedIds.length + ' ordres? Això crearà les inscripcions finals als cursos.');
    if (!confirmProcess) return;
    
    console.log('Enviant petició de processament...');
    
    fetch('/campus/ordres/process', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            ordre_ids: selectedIds,
            season_id: {{ \App\Models\CampusSeason::where('is_current', true)->first()?->id ?? 1 }}
        })
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            alert(data.message);
            location.reload(); // Recarregar per veure canvis
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de connexió: ' + error.message);
    });
}

function bulkDelete() {
    const selectedIds = getSelectedIds();
    if (selectedIds.length === 0) return;
    
    const confirmDelete = confirm('Estàs segur que vols eliminar ' + selectedIds.length + ' registres?');
    if (!confirmDelete) return;
    
    fetch('/campus/ordres/bulk-delete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            ordre_ids: selectedIds
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload(); // Recarregar per veure canvis
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de connexió');
    });
}

function getSelectedIds() {
    const checkboxes = document.querySelectorAll('.bulk-checkbox:checked');
    const ids = Array.from(checkboxes).map(cb => cb.value);
    console.log('getSelectedIds - checkboxes trobats:', checkboxes.length, 'IDs:', ids);
    return ids;
}

function updateCourseSelect() {
    const select = document.getElementById('courseSelect');
    select.innerHTML = '<option value="">-- Selecciona un curs --</option>';
    
    courses.forEach(course => {
        const option = document.createElement('option');
        option.value = course.id;
        option.textContent = `${course.title} (${course.code})`;
        select.appendChild(option);
    });
}

// Modal functions
function showMatchModal(ordreId) {
    const ordre = findOrdreById(ordreId);
    if (!ordre) return;
    
    document.getElementById('ordreId').value = ordreId;
    document.getElementById('modalName').textContent = `${ordre.wp_first_name} ${ordre.wp_last_name}`;
    document.getElementById('modalEmail').textContent = ordre.wp_email;
    document.getElementById('modalCourse').textContent = ordre.wp_item_name;
    document.getElementById('modalCode').textContent = ordre.wp_code;
    
    // Auto-seleccionar curs si ja té
    if (ordre.course_id) {
        document.getElementById('courseSelect').value = ordre.course_id;
    }
    
    document.getElementById('matchModal').classList.remove('hidden');
}

function closeMatchModal() {
    document.getElementById('matchModal').classList.add('hidden');
}

function findOrdreById(id) {
    // Combinar totes les ordres de la pàgina
    const allOrdres = [
        @json($pendingOrdres->items()),
        @json($manualOrdres->items()),
        @json($errorOrdres->items())
    ].flat();
    
    return allOrdres.find(o => o.id == id);
}

// Select all functionality
document.getElementById('selectAllPending')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.pending-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});

document.querySelector('.pending-select-all')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.pending-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});
</script>
@endpush

@push('styles')
<style>
    .btn {
        @apply px-4 py-2 rounded-md font-medium text-sm transition-colors duration-200;
    }
    .btn-primary {
        @apply bg-blue-600 text-white hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2;
    }
    .btn-secondary {
        @apply bg-gray-600 text-white hover:bg-gray-700 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2;
    }
    .btn-success {
        @apply bg-green-600 text-white hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2;
    }
    .btn-sm {
        @apply px-3 py-1 text-xs;
    }
</style>
@endpush
