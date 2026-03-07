@extends('campus.shared.layout')

@section('title', __('campus.courses'))
@section('subtitle', __('campus.courses'))

@section('breadcrumbs')
    <li aria-current="page">
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2">
                {{ __('campus.courses') }}             
            </span>
        </div>
    </li>
@endsection



@section('content')
<div class="container mx-auto px-4 py-8">
    
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">{{ __('campus.courses') }}</h1>
        <div class="flex space-x-3">
            @can('campus.courses.create')
                <a href="{{ route('campus.courses.create') }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                    <i class="fas fa-plus mr-2"></i>{{ __('campus.new_course') }}
                </a>
            @endcan
            
            @can('campus.courses.create')
                <a href="{{ route('campus.campus.courses.import') }}"
                   class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    <i class="bi bi-upload mr-2"></i>{{ __('campus.import_courses') }}
                </a>
            @else
                <span class="text-gray-400 text-sm px-4 py-2 bg-gray-200 rounded-md cursor-not-allowed">
                    <i class="bi bi-upload mr-2"></i>{{ __('campus.import_courses') }} (Sense permisos)
                </span>
            @endcan
        </div>
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
                
                <form method="GET" action="{{ route('campus.courses.index') }}" class="space-y-4">
                    <!-- Primera fila de filtros -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Filtro por código -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('campus.code') }}
                            </label>
                            <input type="text" 
                                   name="search_code" 
                                   value="{{ request('search_code') }}"
                                   placeholder="{{ __('Search by code') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <!-- Filtro por título -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('campus.title') }}
                            </label>
                            <input type="text" 
                                   name="search_title" 
                                   value="{{ request('search_title') }}"
                                   placeholder="{{ __('Search by title') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <!-- Filtro por temporada - Compacto -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('campus.season') }}
                                @if(session('selected_season'))
                                    <span class="text-xs text-gray-500 ml-1">(guardado)</span>
                                @endif
                            </label>
                            <select name="search_season" 
                                    onchange="this.form.submit()"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                <option value="">{{ __('All seasons') }}</option>
                                @foreach($seasons as $season)
                                    <option value="{{ $season->id }}" 
                                            {{ (request('search_season') ?: session('selected_season')) == $season->id ? 'selected' : '' }}>
                                        {{ $season->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Botones de acción y ordenamiento -->
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mt-6">
                        <div class="flex flex-wrap gap-2">
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <i class="bi bi-search mr-2"></i>{{ __('Search') }}
                            </button>
                            <!-- <a href="{{ route('campus.campus.courses.clear-season') }}" 
                                class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                <i class="bi bi-x-circle mr-2"></i>{{ __('Clear') }}
                            </a> -->
                        </div>
                        
                        <!-- Ordenamiento -->
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                            <label class="text-sm font-medium text-gray-700 whitespace-nowrap">{{ __('Sort by') }}:</label>
                            <select name="sort_by" 
                                    onchange="this.form.submit()"
                                    class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="start_date" {{ request('sort_by') == 'start_date' ? 'selected' : '' }}>
                                    {{ __('campus.start_date') }}
                                </option>
                                <option value="title" {{ request('sort_by') == 'title' ? 'selected' : '' }}>
                                    {{ __('campus.title') }}
                                </option>
                                <option value="code" {{ request('sort_by') == 'code' ? 'selected' : '' }}>
                                    {{ __('campus.code') }}
                                </option>
                                <!-- <option value="price" {{ request('sort_by') == 'price' ? 'selected' : '' }}>
                                    {{ __('campus.price') }}
                                </option> -->
                                <option value="hours" {{ request('sort_by') == 'hours' ? 'selected' : '' }}>
                                    {{ __('campus.hours') }}
                                </option>
                                <option value="max_students" {{ request('sort_by') == 'max_students' ? 'selected' : '' }}>
                                    {{ __('campus.max_students') }}
                                </option>
                            </select>
                            <select name="sort_order" 
                                    onchange="this.form.submit()"
                                    class="flex-shrink-0  px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
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
        </div>
    </div>

    <!-- Results table -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'code', 'sort_order' => request('sort_by') == 'code' && request('sort_order') == 'asc' ? 'desc' : 'asc']) }}" class="text-gray-700 hover:text-blue-600 flex items-center">
                                {{ __('campus.code') }}
                                @if(request('sort_by') == 'code')
                                    <i class="bi bi-chevron-{{ request('sort_order') == 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @else 
                                <i class="bi bi-chevron-{{ request('sort_order') == 'asc' ? 'down' : 'up' }} ml-1"></i>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'title', 'sort_order' => request('sort_by') == 'title' && request('sort_order') == 'asc' ? 'desc' : 'asc']) }}" class="text-gray-700 hover:text-blue-600 flex items-center">
                                {{ __('campus.title') }}
                                @if(request('sort_by') == 'title')
                                    <i class="bi bi-chevron-{{ request('sort_order') == 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @else 
                                    <i class="bi bi-chevron-{{ request('sort_order') == 'asc' ? 'down' : 'up' }} ml-1"></i>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'category', 'sort_order' => request('sort_by') == 'category' && request('sort_order') == 'asc' ? 'desc' : 'asc']) }}" class="text-gray-700 hover:text-blue-600 flex items-center">
                                {{ __('campus.category') }}
                                @if(request('sort_by') == 'category')
                                    <i class="bi bi-chevron-{{ request('sort_order') == 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @else 
                                    <i class="bi bi-chevron-{{ request('sort_order') == 'asc' ? 'down' : 'up' }} ml-1"></i>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'season', 'sort_order' => request('sort_by') == 'season' && request('sort_order') == 'asc' ? 'desc' : 'asc']) }}" class="text-gray-700 hover:text-blue-600 flex items-center">
                                {{ __('campus.season') }}
                                @if(request('sort_by') == 'season')
                                    <i class="bi bi-chevron-{{ request('sort_order') == 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @else 
                                    <i class="bi bi-chevron-{{ request('sort_order') == 'asc' ? 'down' : 'up' }} ml-1"></i>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'dates', 'sort_order' => request('sort_by') == 'dates' && request('sort_order') == 'asc' ? 'desc' : 'asc']) }}" class="text-gray-700 hover:text-blue-600 flex items-center">
                                {{ __('campus.dates') }}
                                @if(request('sort_by') == 'dates')
                                    <i class="bi bi-chevron-{{ request('sort_order') == 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @else 
                                    <i class="bi bi-chevron-{{ request('sort_order') == 'asc' ? 'down' : 'up' }} ml-1"></i>
                                @endif
                            </a>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($courses as $course)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <div class="flex items-center space-x-2">
                                    @if($course->isBaseCourse())
                                        <i class="bi bi-star-fill mr-1"></i>
                                    @endif
                                    {{ $course->code }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900" style="max-width: 200px; width: 200px;">
                                <div class="space-y-1">
                                    <div class="truncate" title="{{ $course->title }}">
                                        {{ Str::limit($course->title, 100) }}
                                    </div>
                                    @if(strlen($course->title) > 100)
                                        <div class="text-xs text-gray-400 truncate" title="{{ $course->title }}">
                                            {{ Str::substr($course->title, 100, 100) }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $course->category->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <div class="max-w-xs">
                                    @if($course->season)
                                        <div class="font-medium">{{ $course->season->academic_year }}</div>
                                        <div class="text-xs text-gray-400">{{ $course->season->name }}</div>
                                    @else
                                        <span>N/A</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <div class="max-w-xs">
                                    <div class="font-medium">{{ $course->start_date->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-400">{{ $course->end_date->format('d/m/Y') }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    @if($course->is_active)
                                        <i class="bi bi-check-circle-fill text-green-500" title="{{ __('Active') }}"></i>
                                        <span class="text-xs text-green-600">{{ __('Active') }}</span>
                                    @else
                                        <i class="bi bi-x-circle-fill text-red-500" title="{{ __('Inactive') }}"></i>
                                        <span class="text-xs text-red-600">{{ __('Inactive') }}</span>
                                    @endif
                                    @if($course->is_public)
                                        <i class="bi bi-globe text-blue-500" title="{{ __('Public') }}"></i>
                                    @else
                                        <i class="bi bi-lock text-gray-400" title="{{ __('Private') }}"></i>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @can('campus.courses.view')
                                    <a href="{{ route('campus.courses.show', $course) }}" 
                                       class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                @endcan
                                
                                @if($course->isBaseCourse())
                                    @can('campus.courses.create')
                                        <a href="{{ route('campus.courses.create') }}?parent_id={{ $course->id }}" 
                                           class="text-green-600 hover:text-green-900 mr-3"
                                           title="Crear instància d'aquest curs base">
                                            <i class="bi bi-plus-circle"></i>
                                        </a>
                                    @endcan
                                @endif
                               
                                    @can('campus.courses.edit')
                                        <a href="{{ route('campus.courses.edit', $course) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 mr-3">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endcan
                                
                                
                              
                                @can('campus.courses.delete')
                                    <form action="{{ route('campus.courses.destroy', $course) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900"
                                                onclick="return confirm('{{ __('Are you sure?') }}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                {{ __('No courses found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($courses->hasPages())
        <div class="mt-6">
            {{ $courses->links() }}
        </div>
    @endif
</div>
<div class="p-6 bg-white border-b border-gray-200">
    <h2 class="text-lg font-medium text-gray-900">
        {{ __('campus.import_information') }}
    </h2>
    <p class="text-xs text-gray-500 mt-1">
        {{ __('campus.import_information_alert') }}
    <a href="#" onclick="return downloadTemplate();" class="text-indigo-600 hover:underline">
        📥 {{ __('campus.download_template') }}
    </a>
</p>
<div class="mt-2 p-3 bg-blue-50 rounded text-xs text-blue-800">
    <strong>{{ __('campus.code_protocol') }}:</strong><br>
    {{ __('campus.code_protocol_description') }}<br>
    &nbsp;&nbsp;- {{ __('campus.code_protocol_prefix') }}<br>
    &nbsp;&nbsp;- {{ __('campus.code_protocol_suffix') }}<br>
    &nbsp;&nbsp;- {{ __('campus.code_protocol_result') }}
</div>
<div class="mt-2 p-3 bg-green-50 rounded text-xs text-green-800">
    <strong>{{ __('campus.category_protocol') }}:</strong><br>
    {{ __('campus.category_protocol_description') }}<br>
    &nbsp;&nbsp;- {{ __('campus.category_protocol_name') }}<br>
    &nbsp;&nbsp;- {{ __('campus.category_protocol_slug') }}<br>
    &nbsp;&nbsp;- {{ __('campus.category_protocol_description') }}<br>
    &nbsp;&nbsp;- {{ __('campus.category_protocol_order') }}<br>
    • {{ __('campus.category_protocol_report') }}
</div>
</div>


@endsection

<script>
window.downloadTemplate = function() {
    const template = `first_name,last_name,email,code,title,slug,description,credits,hours,sessions,max_students,price,level,schedule,start_date,end_date,location,format,is_active,is_public,requirements,objectives,metadata,created_at,updated_at
Pepito,Grillo,CREACIO@campus.test,CREACIO,Creació literària: el microrelat,creacio-literaria-el-microrelat,"Creació literària: el microrelat","7","7","7","30","50.00","beginner",,"2025-09-16","2026-01-31",,,"1","1",,,,,
Chi,Kung,CHIKUNG@campus.test,CHIKUNG1,Chi Kung dilluns,chi-kung-dilluns,"Chi Kung (grups dilluns)","27","27","27","30","50.00","beginner",,"2025-09-16","2026-01-31",,,"1","1",,,,,
,,COMFUNC@campus.test,COMFUNC,Com funciona la terra que trepitgem,com-funciona-la-terra-que-trepitgem-1771350937,"Com funciona la terra que trepitgem","8","8","8","30","50.00",,,,,,,,,,,,
,,NUTRICIO@campus.test,NUTRICIO,Nutrició i dietoteràpia,nutricio-i-dietoterapia-1771350937,"Nutrició i dietoteràpia",,,,,,,,,,,,,,,,,,`;
    
    const blob = new Blob([template], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'plantilla_cursos.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
    return false;
}
</script>

