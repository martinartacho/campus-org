<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Manager - {{ ucfirst($activeRole ?? 'manager') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Usar rol activo si existe, sino verificar todos los roles --}}
            @php
                $activeRole = $activeRole ?? session('active_role') ?? 'manager';
                $user = auth()->user();
            @endphp

            {{-- Dashboard Manager --}}
            <x-dashboard.manager :stats="$stats ?? []" />
            
            {{-- Widgets específicos para Manager Roles basados en permisos reales --}}
            @php
                $widgetsManager = [];
                if (isset($widgets) && is_array($widgets)) {
                    // Obtener widgets permitidos para este rol desde la base de datos
                    $allowedWidgets = \App\Models\DashboardWidgetPermission::getWidgetsForRole($activeRole);
                    
                    foreach ($widgets as $widget) {
                        $widgetName = basename(str_replace('.', '/', $widget));
                        if (in_array($widgetName, $allowedWidgets)) {
                            $widgetsManager[] = $widget;
                        }
                    }
                }
            @endphp
            
            {{-- Grid responsive para widgets individuales --}}
            @if(count($widgetsManager) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
                    @foreach($widgetsManager as $widget)
                        @include($widget)
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
