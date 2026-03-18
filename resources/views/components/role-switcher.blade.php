{{-- resources/views/components/role-switcher.blade.php --}}

@if(auth()->check() && auth()->user()->roles->count() > 1)
<div class="relative">
    <button 
        x-data="{ open: false }" 
        @click="open = !open" 
        class="flex items-center space-x-2 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
    >
        <i class="bi bi-person-circle"></i>
        <span>{{ auth()->user()->name }}</span>
        <i class="bi bi-chevron-down text-xs"></i>
    </button>

    <div 
        x-show="open" 
        @click.away="open = false"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute right-0 z-50 mt-2 w-64 bg-white rounded-md shadow-lg border border-gray-200"
    >
        <div class="py-2">
            <div class="px-4 py-2 border-b border-gray-200">
                <p class="text-sm font-medium text-gray-900">Hola, {{ auth()->user()->first_name ?? auth()->user()->name }}!</p>
                <p class="text-xs text-gray-500">Gestiona els teus rols</p>
            </div>
            
            @foreach(auth()->user()->roles as $role)
                <a href="{{ route('dashboard.switch.role', $role->name) }}" 
                   class="flex items-center px-4 py-3 text-sm hover:bg-gray-50 transition-colors">
                    <div class="flex-1">
                        <div class="font-medium text-gray-900">{{ ucfirst($role->name) }}</div>
                        <div class="text-xs text-gray-500">{{ $role->permissions->where('name', 'like', 'campus.%')->count() }} permisos campus</div>
                    </div>
                    @if(request()->get('active_role') == $role->name)
                        <i class="bi bi-check-circle text-green-600"></i>
                    @endif
                </a>
            @endforeach
            
            <div class="border-t border-gray-200 mt-2 pt-2">
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                    <i class="bi bi-gear me-2"></i> Configuració del compte
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                        <i class="bi bi-box-arrow-right me-2"></i> Tancar sessió
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
