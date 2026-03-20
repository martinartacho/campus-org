   <!--  @if(config('app.debug'))
        <pre class="bg-gray-100 p-3 text-xs rounded border">{{ var_export([
            'path' => 'resources/views/components/dashboard/admin-cards.blade.php',
            'user' => auth()->user()->email,
            'roles' => auth()->user()->roles->pluck('name')->toArray(),
            'error' => $error ?? null,
            'debug' => $debug ?? null,
            'stats' => $stats ?? [],
            'activeRole' => $activeRole ?? null,
            'widgets' => $widgets ?? [],
        ], true) }}
        </pre>
    @endif -->

    @auth
    @php
        $user = Auth::user();
    @endphp

    {{-- ========================= --}}
    {{-- 📊 STATS RÀPIDES --}}
    {{-- ========================= --}}



    {{-- ========================= --}}
    {{-- 🧠 WIDGETS DINÁMICOS DESDE BD --}}
    {{-- ========================= --}}
    {{-- Los widgets ahora se cargan dinámicamente desde dashboard.blade.php 
         según la configuración de la base de datos.
         Ya no hay widgets hardcodeados aquí. --}}

@endauth