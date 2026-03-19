<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Gestión de Widgets del Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">
                        <i class="bi bi-gear me-2"></i>
                        Gestión de Widgets del Dashboard
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">
                        Configura qué widgets puede ver cada rol del equipo de gestión
                    </p>
                </div>

                @if(session('success'))
                    <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.dashboard-widgets.update') }}">
                    @csrf
                    @method('PUT')
                    
                    <!-- Inputs ocultos para asegurar que todos los roles se procesen -->
                    @foreach($managerRoles as $role)
                        <input type="hidden" name="widgets[{{ $role }}]" value="">
                    @endforeach

                    <div class="space-y-8">
                        @foreach($managerRoles as $role)
                            <div class="border border-gray-200 rounded-lg p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">
                                    <i class="bi bi-person-badge me-2"></i>
                                    {{ ucfirst($role) }}
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($availableWidgets as $widgetKey => $widgetName)
                                        <div class="flex items-center">
                                            <input 
                                                type="checkbox" 
                                                id="widget_{{ $role }}_{{ $widgetKey }}" 
                                                name="widgets[{{ $role }}][]"
                                                value="{{ $widgetKey }}"
                                                {{ in_array($widgetKey, $widgetPermissions[$role]) ? 'checked' : '' }}
                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                            >
                                            <label for="widget_{{ $role }}_{{ $widgetKey }}" class="ml-2 block text-sm text-gray-700">
                                                {{ $widgetName }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-8 flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="bi bi-check-circle me-2"></i>
                            Guardar Configuración
                        </button>
                    </div>
                </form>
            </div>
            </div>
        </div>
    </div>
</x-app-layout>
