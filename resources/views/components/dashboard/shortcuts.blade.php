{{-- resources/views/components/dashboard/shortcuts.blade.php --}}
@if(isset($quickActions) && !empty($quickActions))
    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="bi bi-lightning-fill text-yellow-500 me-2"></i>
            Accions Ràpides
        </h3>
        
        <div class="flex flex-wrap gap-3 overflow-x-auto pb-2">
            @foreach($quickActions as $actionKey => $action)
                @if(isset($action['route']) && \Illuminate\Support\Facades\Route::has($action['route']))
                    {{-- Seleccionar variant segons el rol actual i tipus d'acció --}}
                    @php
                        $activeRole = request()->get('activeRole') ?? session('activeRole') ?? 'manager';
                        
                        // Variant per defecte segons el rol
                        $variant = 'primary';
                        if (in_array($activeRole, ['manager', 'director'])) {
                            $variant = 'manager';
                        } elseif (in_array($activeRole, ['coordinacio'])) {
                            $variant = 'coordinacio';
                        } elseif (in_array($activeRole, ['gestio'])) {
                            $variant = 'gestio';
                        } elseif (in_array($activeRole, ['comunicacio'])) {
                            $variant = 'comunicacio';
                        }
                        
                        // Variant especial per a accions de creació
                        if (in_array($actionKey, ['add_user', 'add_course', 'add_season'])) {
                            $variant = 'success';
                        }
                    @endphp
                    
                    <x-campus-button href="{{ route($action['route']) }}" variant="{{ $variant }}" size="base" class="me-2 mb-2">
                        <i class="{{ $action['icon'] }} me-2"></i>
                        {{ $action['name'] }}
                    </x-campus-button>
                @endif
            @endforeach
        </div>
    </div>
@endif
