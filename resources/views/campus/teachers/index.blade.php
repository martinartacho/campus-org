@extends('campus.shared.layout')

@section('title', __('Professors'))
@section('subtitle', __('Gestió del professorat'))

@section('breadcrumbs')
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <a href="{{ route('dashboard') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2">
                @lang('campus.dashboard')
            </a>
        </div>
    </li>
    <li aria-current="page">
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2">
                {{ __('Professors') }}
            </span>
        </div>
    </li>
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Professors</h1>
        @can('campus.teachers.create')
            <a href="{{ route('campus.teachers.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                <i class="fas fa-plus mr-2"></i>Nou Professor
            </a>
        @endcan
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="p-6">
            <!-- Filtros avanzados -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <h3 class="text-lg font-semibold mb-4 text-gray-700">
                    <i class="bi bi-funnel mr-2"></i>{{ __('Filters') }}
                </h3>
                
                <form method="GET" action="{{ route('campus.teachers.index') }}" class="space-y-4">
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
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <!-- Filtro por teléfono -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Phone') }}
                            </label>
                            <input type="text" 
                                   name="search_phone" 
                                   value="{{ request('search_phone') }}"
                                   placeholder="{{ __('Search by phone') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <!-- Segunda fila de filtros -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Filtro por DNI -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('DNI/Fiscal ID') }}
                            </label>
                            <input type="text" 
                                   name="search_dni" 
                                   value="{{ request('search_dni') }}"
                                   placeholder="{{ __('Search by DNI') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <!-- Filtro por estado -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Status') }}
                            </label>
                            <select name="search_status" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">{{ __('All statuses') }}</option>
                                <option value="active" {{ request('search_status') == 'active' ? 'selected' : '' }}>
                                    {{ __('Active') }}
                                </option>
                                <option value="inactive" {{ request('search_status') == 'inactive' ? 'selected' : '' }}>
                                    {{ __('Inactive') }}
                                </option>
                                <option value="on_leave" {{ request('search_status') == 'on_leave' ? 'selected' : '' }}>
                                    {{ __('On Leave') }}
                                </option>
                            </select>
                        </div>

                        <!-- Filtro por especialización -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Specialization') }}
                            </label>
                            <select name="search_specialization" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">{{ __('All specializations') }}</option>
                                @foreach($specializations as $specialization)
                                    <option value="{{ $specialization }}" 
                                            {{ request('search_specialization') == $specialization ? 'selected' : '' }}>
                                        {{ $specialization }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Tercera fila de filtros -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <!-- Filtro por número de cursos -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Number of courses') }}
                            </label>
                            <div class="flex space-x-2">
                                <input type="number" 
                                       name="search_courses_min" 
                                       value="{{ request('search_courses_min') }}"
                                       placeholder="{{ __('Min') }}"
                                       min="0"
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <input type="number" 
                                       name="search_courses_max" 
                                       value="{{ request('search_courses_max') }}"
                                       placeholder="{{ __('Max') }}"
                                       min="0"
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <!-- Filtro por fecha de contratación -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Hiring Date') }}
                            </label>
                            <div class="flex space-x-2">
                                <input type="date" 
                                       name="search_date_from" 
                                       value="{{ request('search_date_from') }}"
                                       placeholder="{{ __('From') }}"
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <input type="date" 
                                       name="search_date_to" 
                                       value="{{ request('search_date_to') }}"
                                       placeholder="{{ __('To') }}"
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción y ordenamiento -->
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mt-6">
                        <div class="flex flex-wrap gap-2">
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <i class="bi bi-search mr-2"></i>{{ __('Search') }}
                            </button>
                            <a href="{{ route('campus.teachers.index') }}" 
                               class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                <i class="bi bi-x-circle mr-2"></i>{{ __('Clear') }}
                            </a>
                        </div>
                        
                        <!-- Ordenamiento -->
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                            <label class="text-sm font-medium text-gray-700 whitespace-nowrap">{{ __('Sort by') }}:</label>
                            <select name="sort_by" 
                                    onchange="this.form.submit()"
                                    class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="last_name" {{ request('sort_by') == 'last_name' ? 'selected' : '' }}>
                                    {{ __('Last Name') }}
                                </option>
                                <option value="first_name" {{ request('sort_by') == 'first_name' ? 'selected' : '' }}>
                                    {{ __('First Name') }}
                                </option>
                                <option value="email" {{ request('sort_by') == 'email' ? 'selected' : '' }}>
                                    {{ __('Email') }}
                                </option>
                                <option value="courses_count" {{ request('sort_by') == 'courses_count' ? 'selected' : '' }}>
                                    {{ __('Number of Courses') }}
                                </option>
                                <option value="hiring_date" {{ request('sort_by') == 'hiring_date' ? 'selected' : '' }}>
                                    {{ __('Hiring Date') }}
                                </option>
                            </select>
                            <select name="sort_order" 
                                    onchange="this.form.submit()"
                                    class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>
                                    {{ __('Asc') }}
                                </option>
                                <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>
                                    {{ __('Desc') }}
                                </option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Resultados -->
            <div class="mb-4 text-sm text-gray-600">
                @if(request()->hasAny(['search_name', 'search_email', 'search_phone', 'search_dni', 'search_city', 'search_status', 'search_courses_min', 'search_courses_max', 'search_date_from', 'search_date_to', 'search_specialization']))
                    <span class="font-medium">{{ __('Showing') }} {{ $teachers->firstItem() }}-{{ $teachers->lastItem() }} {{ __('of') }} {{ $teachers->total() }} {{ __('filtered teachers') }}</span>
                @else
                    <span class="font-medium">{{ __('Showing') }} {{ $teachers->firstItem() }}-{{ $teachers->lastItem() }} {{ __('of') }} {{ $teachers->total() }} {{ __('teachers') }}</span>
                @endif
            </div>

            <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                <a href="?sort_by=last_name&sort_order={{ request('sort_by') == 'last_name' && request('sort_order') == 'asc' ? 'desc' : 'asc' }}{{ request()->getQueryString() ? '&' . http_build_query(request()->except(['sort_by', 'sort_order'])) : '' }}" 
                                   class="text-gray-700 hover:text-gray-900">
                                    {{ __('Name') }}
                                    @if(request('sort_by') == 'last_name')
                                        <i class="bi bi-{{ request('sort_order') == 'asc' ? 'arrow-up' : 'arrow-down' }} ml-1"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                <a href="?sort_by=email&sort_order={{ request('sort_by') == 'email' && request('sort_order') == 'asc' ? 'desc' : 'asc' }}{{ request()->getQueryString() ? '&' . http_build_query(request()->except(['sort_by', 'sort_order'])) : '' }}" 
                                   class="text-gray-700 hover:text-gray-900">
                                    {{ __('Email') }}
                                    @if(request('sort_by') == 'email')
                                        <i class="bi bi-{{ request('sort_order') == 'asc' ? 'arrow-up' : 'arrow-down' }} ml-1"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                {{ __('Phone') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                <a href="?sort_by=courses_count&sort_order={{ request('sort_by') == 'courses_count' && request('sort_order') == 'asc' ? 'desc' : 'asc' }}{{ request()->getQueryString() ? '&' . http_build_query(request()->except(['sort_by', 'sort_order'])) : '' }}" 
                                   class="text-gray-700 hover:text-gray-900">
                                    {{ __('Courses') }}
                                    @if(request('sort_by') == 'courses_count')
                                        <i class="bi bi-{{ request('sort_order') == 'asc' ? 'arrow-up' : 'arrow-down' }} ml-1"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                {{ __('Actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($teachers as $teacher)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold">
                                                {{ strtoupper(substr($teacher->first_name, 0, 1) . substr($teacher->last_name, 0, 1)) }}
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $teacher->first_name }} {{ $teacher->last_name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $teacher->fiscal_id ?? 'Sense DNI' }}
                                            </div>
                                            @if($teacher->city)
                                                <div class="text-xs text-gray-400">
                                                    <i class="bi bi-geo-alt mr-1"></i>{{ $teacher->city }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $teacher->email }}</div>
                                    @if($teacher->user)
                                        <div class="text-xs text-gray-500">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Actiu
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $teacher->phone ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $teacher->courses->count() }} cursos
                                    </div>
                                    @if($teacher->courses->count() > 0)
                                        <div class="text-xs text-gray-500">
                                            {{ $teacher->courses->sum('pivot.hours_assigned') }} h totals
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        @can('campus.teachers.view')
                                            <a href="{{ route('campus.teachers.show', $teacher) }}" 
                                               class="text-blue-600 hover:text-blue-900" title="Veure">
                                                <i class="bi bi-eye"></i> 
                                            </a>
                                        @endcan
                                        @can('campus.teachers.edit')
                                            <a href="{{ route('campus.teachers.edit', $teacher) }}" 
                                               class="text-indigo-600 hover:text-indigo-900" title="Editar">
                                                <i class="bi bi-pencil"></i> 
                                            </a>
                                        @endcan
                                        @can('campus.teachers.delete')
                                            <form action="{{ route('campus.teachers.destroy', $teacher) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Estàs segur que vols eliminar aquest professor?');"
                                                  class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-900" 
                                                        title="Eliminar">
                                                    <i class="bi bi-trash"></i> 
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    <i class="bi bi-search text-4xl mb-2"></i>
                                    <p>{{ __('No teachers found with current filters') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($teachers->count() > 0)
                <div class="mt-6">
                    {{ $teachers->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
