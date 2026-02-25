<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
                <i class="bi bi-people-fill mr-2"></i>
                {{ __('site.User Management') }}
            </h2>
            <a href="{{ route('admin.users.create') }}">
                <x-primary-button>
                    <i class="bi bi-plus-lg mr-1"></i>{{ __('site.Create User') }} 
                </x-primary-button>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('warning'))
                        <div class="p-4 mb-4 text-sm text-yellow-700 bg-yellow-100 border border-yellow-300 rounded">
                            {{ session('warning') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 border border-red-300 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Filtros de búsqueda -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-lg font-semibold mb-4 text-gray-700">
                            <i class="bi bi-funnel mr-2"></i>{{ __('Filters') }}
                        </h3>
                        
                        <form method="GET" action="{{ route('admin.users.index') }}" class="space-y-4">
                            <!-- Primera fila de filtros -->
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <!-- Filtro por nombre -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        {{ __('Name') }}
                                    </label>
                                    <input type="text" 
                                           name="search_name" 
                                           value="{{ request('search_name') }}"
                                           placeholder="{{ __('Search by name') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>

                                <!-- Filtro por email -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        {{ __('Email') }}
                                    </label>
                                    <input type="email" 
                                           name="search_email" 
                                           value="{{ request('search_email') }}"
                                           placeholder="{{ __('Search by email') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>

                                <!-- Filtro por rol -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        {{ __('Role') }}
                                    </label>
                                    <select name="search_role" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        <option value="">{{ __('All roles') }}</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}" 
                                                    {{ request('search_role') == $role->name ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Segunda fila de filtros -->
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <!-- Filtro por fecha de registro -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        {{ __('Registration date') }}
                                    </label>
                                    <div class="flex space-x-2">
                                        <input type="date" 
                                               name="search_date_from" 
                                               value="{{ request('search_date_from') }}"
                                               placeholder="{{ __('From') }}"
                                               class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        <input type="date" 
                                               name="search_date_to" 
                                               value="{{ request('search_date_to') }}"
                                               placeholder="{{ __('To') }}"
                                               class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    </div>
                                </div>
                            </div>

                            <!-- Botones de acción y ordenamiento -->
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mt-6">
                                <div class="flex flex-wrap gap-2">
                                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        <i class="bi bi-search mr-2"></i>{{ __('Search') }}
                                    </button>
                                    <a href="{{ route('admin.users.index') }}" 
                                       class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                        <i class="bi bi-x-circle mr-2"></i>{{ __('Clear') }}
                                    </a>
                                </div>
                                
                                <!-- Ordenamiento -->
                                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                                    <label class="text-sm font-medium text-gray-700 whitespace-nowrap">{{ __('Sort by') }}:</label>
                                    <select name="sort_by" 
                                            onchange="this.form.submit()"
                                            class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>
                                            {{ __('Registration date') }}
                                        </option>
                                        <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>
                                            {{ __('Name') }}
                                        </option>
                                        <option value="email" {{ request('sort_by') == 'email' ? 'selected' : '' }}>
                                            {{ __('Email') }}
                                        </option>
                                    </select>
                                    <select name="sort_order" 
                                            onchange="this.form.submit()"
                                            class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>
                                            {{ __('Desc') }}
                                        </option>
                                        <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>
                                            {{ __('Asc') }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Resultados -->
                    <div class="mb-4 text-sm text-gray-600">
                        @if(request()->hasAny(['search_name', 'search_email', 'search_role', 'search_date_from', 'search_date_to']))
                            <span class="font-medium">{{ __('Showing') }} {{ $users->firstItem() }}-{{ $users->lastItem() }} {{ __('of') }} {{ $users->total() }} {{ __('filtered users') }}</span>
                        @else
                            <span class="font-medium">{{ __('Showing') }} {{ $users->firstItem() }}-{{ $users->lastItem() }} {{ __('of') }} {{ $users->total() }} {{ __('users') }}</span>
                        @endif
                    </div>

                    <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-white divide-y divide-gray-200">
                                <tr class="border-b">
                                    <th class="text-left px-4 py-2 w-1/3 whitespace-nowrap">
                                        <a href="?sort_by=name&sort_order={{ request('sort_by') == 'name' && request('sort_order') == 'asc' ? 'desc' : 'asc' }}{{ request()->getQueryString() ? '&' . http_build_query(request()->except(['sort_by', 'sort_order'])) : '' }}" 
                                           class="text-gray-700 hover:text-gray-900 font-medium">
                                            {{ __('site.Name') }}
                                            @if(request('sort_by') == 'name')
                                                <i class="bi bi-{{ request('sort_order') == 'asc' ? 'arrow-up' : 'arrow-down' }} ml-1"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="text-left px-4 py-2 w-2/20 whitespace-nowrap">
                                        <a href="?sort_by=email&sort_order={{ request('sort_by') == 'email' && request('sort_order') == 'asc' ? 'desc' : 'asc' }}{{ request()->getQueryString() ? '&' . http_build_query(request()->except(['sort_by', 'sort_order'])) : '' }}" 
                                           class="text-gray-700 hover:text-gray-900 font-medium">
                                            {{ __('site.Email') }}
                                            @if(request('sort_by') == 'email')
                                                <i class="bi bi-{{ request('sort_order') == 'asc' ? 'arrow-up' : 'arrow-down' }} ml-1"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="text-left px-4 py-2 w-2/20 whitespace-nowrap">{{ __('site.rol') }}</th>
                                    <th class="text-left px-4 py-2 w-1/5 whitespace-nowrap">
                                        <a href="?sort_by=created_at&sort_order={{ request('sort_by') == 'created_at' && request('sort_order') == 'asc' ? 'desc' : 'asc' }}{{ request()->getQueryString() ? '&' . http_build_query(request()->except(['sort_by', 'sort_order'])) : '' }}" 
                                           class="text-gray-700 hover:text-gray-900 font-medium">
                                            {{ __('site.registration_date') }}
                                            @if(request('sort_by') == 'created_at')
                                                <i class="bi bi-{{ request('sort_order') == 'asc' ? 'arrow-up' : 'arrow-down' }} ml-1"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="text-left px-4 py-2 whitespace-nowrap">{{ __('site.Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($users as $user)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="text-left px-4 py-2 w-1/3 whitespace-nowrap">
                                        <div class="font-medium text-gray-900">{!! $user->name !!}</div>
                                    </td>
                                    <td class="text-left px-4 py-2 w-2/20 whitespace-nowrap">
                                        <div class="text-gray-600">{!! $user->email !!}</div>
                                    </td>
                                    <td class="text-left px-4 py-2 w-2/20 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded-full bg-indigo-100 text-indigo-800">
                                            {{ $user->getRoleNames()->first() ?? __('No role') }}
                                        </span>
                                    </td>
                                    <td class="py-3 whitespace-nowrap">
                                        <div class="text-sm text-gray-600">{{ $user->created_at->format('d/m/Y') }}</div>
                                        <div class="text-xs text-gray-400">{{ $user->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('admin.users.edit', $user) }}" 
                                               class="text-indigo-600 hover:text-indigo-900"
                                               title="{{ __('site.Edit') }}">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>

                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-900"
                                                        title="{{ __('site.Delete') }}"
                                                        onclick="return confirm('{{ addslashes(__('Are you sure to delete this user?')) }}')">
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
                    
                    @if($users->count() > 0)
                        <div class="mt-4">
                            {{ $users->links() }}
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <i class="bi bi-search text-4xl mb-2"></i>
                            <p>{{ __('No users found with the current filters') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>