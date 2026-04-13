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
            
            {{-- Widgets específicos para Manager Roles --}}
            @php
                $widgetsManager = [];
                if (isset($widgets) && is_array($widgets)) {
                    // Filtrar widgets para roles manager (solo los específicos)
                    $allowedManagerWidgets = [
                        'manager_visio_general',
                        'secretaria_documents',
                        'secretaria_registrations',
                        'secretaria_certificates'
                    ];
                    
                    foreach ($widgets as $widget) {
                        $widgetName = basename(str_replace('.', '/', $widget));
                        if (in_array($widgetName, $allowedManagerWidgets)) {
                            $widgetsManager[] = $widget;
                        }
                    }
                }
            @endphp
            
            @foreach($widgetsManager ?? [] as $widget)
                @include($widget)
            @endforeach

        </div>
    </div>
</x-app-layout>
