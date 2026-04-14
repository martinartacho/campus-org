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
            
            {{-- Separar manager_visio_general (primera fila) y otros widgets (segunda fila) --}}
            @if(count($widgetsManager) > 0)
                {{-- Primera fila: manager_visio_general (amplada completa) --}}
                @php
                    $visioGeneralWidget = null;
                    $otherWidgets = [];
                    
                    foreach($widgetsManager as $widget) {
                        $widgetName = basename(str_replace('.', '/', $widget));
                        if ($widgetName === 'manager_visio_general') {
                            $visioGeneralWidget = $widget;
                        } else {
                            $otherWidgets[] = $widget;
                        }
                    }
                @endphp
                
                @if($visioGeneralWidget)
                    <div class="mt-6">
                        @include($visioGeneralWidget)
                    </div>
                @endif
                
                {{-- Segunda fila: otros widgets en grid --}}
                @if(count($otherWidgets) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
                        @foreach($otherWidgets as $widget)
                            @include($widget)
                        @endforeach
                    </div>
                @endif
            @endif

        </div>
    </div>
</x-app-layout>
