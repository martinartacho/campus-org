@extends('campus.shared.layout')

@section('title', 'Configuración de Dashboard')

@section('subtitle', 'Gestión de Widgets y Acciones Rápidas por Rol')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                <i class="bi bi-gear-fill text-blue-600 mr-3"></i>
                Configuración de Dashboard
            </h1>
            <p class="text-gray-600">
                Gestiona los widgets y acciones rápidas disponibles para cada rol
            </p>
        </div>
        <div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                <i class="bi bi-shield-check mr-2"></i>
                Gestión de Permisos
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center">
                <i class="bi bi-check-circle text-green-600 mr-3"></i>
                <span class="text-green-800">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Tabs para Widgets y Quick Actions -->
    <div class="border-b border-gray-200 mb-8">
        <nav class="-mb-px flex space-x-8">
            <button class="py-2 px-1 border-b-2 border-blue-500 font-medium text-sm text-blue-600" 
                    onclick="showTab('widgets')" id="widgets-tab">
                <i class="bi bi-grid-3x3-gap mr-2"></i>
                Widgets por Rol
            </button>
            <button class="py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300" 
                    onclick="showTab('quickactions')" id="quickactions-tab">
                <i class="bi bi-lightning-fill mr-2"></i>
                Acciones Rápidas
            </button>
        </nav>
    </div>

            <!-- Tab Content -->
<div id="widgets" class="tab-content">
    <div class="bg-white shadow rounded-lg p-6">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">
                <i class="bi bi-grid-3x3-gap text-blue-600 mr-2"></i>
                Configuración de Widgets por Rol
            </h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 300px;">
                                Widget
                            </th>
                            @foreach($roles as $role)
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ ucfirst($role) }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($widgets as $widgetKey => $widget)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <i class="{{ $widget['icon'] }} text-blue-600 mr-3 text-lg"></i>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $widget['name'] }}</div>
                                            <div class="text-sm text-gray-500">{{ $widget['description'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                @foreach($roles as $role)
                                    <td class="px-6 py-4 text-center">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox" 
                                                   name="widgets[{{ $role }}][{{ $widgetKey }}]" 
                                                   value="1"
                                                   @isset($widgetPermissions[$role][$widgetKey])
                                                       @if($widgetPermissions[$role][$widgetKey]) checked @endif
                                                   @else
                                                       checked
                                                   @endisset
                                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                            <span class="ml-2 text-sm text-gray-600">
                                                {{ $widgetPermissions[$role][$widgetKey] ?? true ? '✓' : '✗' }}
                                            </span>
                                        </label>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="quickactions" class="tab-content hidden">
    <div class="bg-white shadow rounded-lg p-6">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">
                <i class="bi bi-lightning-fill text-yellow-600 mr-2"></i>
                Configuración de Acciones Rápidas por Rol
            </h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 300px;">
                                Acción Rápida
                            </th>
                            @foreach($roles as $role)
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ ucfirst($role) }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($quickActions as $actionKey => $action)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <i class="{{ $action['icon'] }} text-yellow-600 mr-3 text-lg"></i>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $action['name'] }}</div>
                                            <div class="text-sm text-gray-500">{{ $action['description'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                @foreach($roles as $role)
                                    <td class="px-6 py-4 text-center">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox" 
                                                   name="quick_actions[{{ $role }}][{{ $actionKey }}]" 
                                                   value="1"
                                                   @isset($quickActionPermissions[$role][$actionKey])
                                                       @if($quickActionPermissions[$role][$actionKey]) checked @endif
                                                   @else
                                                       @if(in_array($actionKey, ['help_center'])) checked @endif
                                                   @endisset
                                                   class="rounded border-gray-300 text-yellow-600 shadow-sm focus:ring-yellow-500">
                                            <span class="ml-2 text-sm text-gray-600">
                                                {{ ($quickActionPermissions[$role][$actionKey] ?? in_array($actionKey, ['help_center'])) ? '✓' : '✗' }}
                                            </span>
                                        </label>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Formularios fuera de tabs -->
<form id="widgetsForm" action="{{ route('admin.dashboard_widgets.update_widgets') }}" method="POST" style="display: none;">
    @csrf
    @method('PUT')
</form>

<form id="quickactionsForm" action="{{ route('admin.dashboard_widgets.update_quick_actions') }}" method="POST" style="display: none;">
    @csrf
    @method('PUT')
</form>

<!-- Botones de acción -->
<div class="mt-8 flex justify-between bg-white shadow rounded-lg p-6">
    <div>
        <button type="button" onclick="submitForm('widgets')" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-4">
            <i class="bi bi-save mr-2"></i>
            Guardar Configuración de Widgets
        </button>
        <button type="button" onclick="submitForm('quickactions')" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
            <i class="bi bi-save mr-2"></i>
            Guardar Configuración de Acciones Rápidas
        </button>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        <i class="bi bi-arrow-left mr-2"></i>
        Volver al Dashboard
    </a>
</div>
        </div>
        

<script>
// Tab switching functionality
function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    // Remove active state from all tab buttons
    document.querySelectorAll('nav button').forEach(btn => {
        btn.classList.remove('border-blue-500', 'text-blue-600');
        btn.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab
    document.getElementById(tabName).classList.remove('hidden');
    
    // Add active state to selected tab button
    const activeBtn = document.getElementById(tabName + '-tab');
    activeBtn.classList.remove('border-transparent', 'text-gray-500');
    activeBtn.classList.add('border-blue-500', 'text-blue-600');
}

// Submit form functionality
function submitForm(formType) {
    const form = document.getElementById(formType + 'Form');
    const tabContent = document.getElementById(formType);
    
    // Copy all inputs from tab content to form
    const inputs = tabContent.querySelectorAll('input[type="checkbox"]');
    inputs.forEach(input => {
        // Create a hidden input in the form
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = input.name;
        hiddenInput.value = input.checked ? '1' : '0';
        form.appendChild(hiddenInput);
    });
    
    // Submit the form
    form.submit();
}

// Auto-guardar cambios
document.addEventListener('change', function(e) {
    if (e.target.type === 'checkbox') {
        // Opcional: Auto-guardar después de cada cambio
        // console.log('Cambio detectado:', e.target.name, e.target.checked);
    }
});
</script>
@endsection
