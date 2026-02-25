@extends('campus.shared.layout')

@section('title', __('campus.courses'))
@section('subtitle', __('campus.courses'))

@section('breadcrumbs')
    <li aria-current="page">
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2">{{ __('campus.courses') }}</span>
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
                <a href="{{ route('importar.cursos') }}"
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

                        <!-- Filtro por temporada -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('campus.season') }}
                            </label>
                            <select name="search_season" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">{{ __('All seasons') }}</option>
                                @foreach($seasons as $season)
                                    <option value="{{ $season->id }}" 
                                            {{ request('search_season') == $season->id ? 'selected' : '' }}>
                                        {{ $season->name ?? $season->season_start . ' - ' . $season->season_end }}
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
                            <a href="{{ route('campus.courses.index') }}" 
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
                                <option value="start_date" {{ request('sort_by') == 'start_date' ? 'selected' : '' }}>
                                    {{ __('campus.start_date') }}
                                </option>
                                <option value="title" {{ request('sort_by') == 'title' ? 'selected' : '' }}>
                                    {{ __('campus.title') }}
                                </option>
                                <option value="code" {{ request('sort_by') == 'code' ? 'selected' : '' }}>
                                    {{ __('campus.code') }}
                                </option>
                                <option value="price" {{ request('sort_by') == 'price' ? 'selected' : '' }}>
                                    {{ __('campus.price') }}
                                </option>
                                <option value="hours" {{ request('sort_by') == 'hours' ? 'selected' : '' }}>
                                    {{ __('campus.hours') }}
                                </option>
                                <option value="max_students" {{ request('sort_by') == 'max_students' ? 'selected' : '' }}>
                                    {{ __('campus.max_students') }}
                                </option>
                            </select>
                            <select name="sort_order" 
                                    onchange="this.form.submit()"
                                    class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
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
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $courses->links() }}
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
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchCourse');
        const clearButton = document.getElementById('clearCourseSearch');
        const resultsSpan = document.getElementById('courseSearchResults');
        const rows = document.querySelectorAll('.course-row');
        const totalRows = rows.length;
        
        function updateCourseSearch() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            let visibleCount = 0;
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const isVisible = text.includes(searchTerm);
                row.style.display = isVisible ? '' : 'none';
                if (isVisible) visibleCount++;
            });
            
            // Update results counter
            if (searchTerm) {
                resultsSpan.textContent = `${visibleCount} / ${totalRows} {{ __('campus.results') }}`;
                clearButton.classList.remove('hidden');
            } else {
                resultsSpan.textContent = '';
                clearButton.classList.add('hidden');
            }
        }
        
        searchInput.addEventListener('input', updateCourseSearch);
        
        clearButton.addEventListener('click', function() {
            searchInput.value = '';
            updateCourseSearch();
            searchInput.focus();
        });
        
        // Initial display
        updateCourseSearch();
    });

    // Descargar plantilla
    window.downloadTemplate = function() {
        const template = 
`category,code,title,slug,description,credits,hours,max_students,price,level,schedule_days,schedule_times,start_date,end_date,requirements,objectives,professor,location,calendar_dates,registration_price,format
Salut i Infermeria,SAN101,PEDIATRIA,pediatria,"Curs de pediatria per a professionals de la salut",4,30,25,20.00,intermediate,Dilluns,10:00-11:30,2026-02-16,2026-03-16,"Titol d'infermeria o medicina","Actualització de coneixements en pediatria","Anna Estapé","CTUG. ROCA UMBERT","16/2, 23/2, 2/3, 9/3, 16/3",20.00,Presencial
Educació i Pedagogia,EDU201,TDAH,tdah,"Estratègies educatives per al TDAH",3,25,30,25.00,beginner,Dimecres,16:00-18:00,2026-02-19,2026-04-02,"Interès en educació especial","Estratègies pràctiques per a l'aula","Marta Soler","UPC VALLÈS","19/2, 26/2, 5/3, 12/3, 19/3, 26/3",25.00,Semipresencial
Ciències Socials i Humanitats,,INTEL·LIGÈNCIA EMOCIONAL,intelligencia-emocional,"Desenvolupament d'habilitats emocionals",2,20,35,15.00,beginner,Dijous,18:00-20:00,2026-02-20,2026-03-20,"Cap requeriment previ","Millora de competències emocionals","Laura Martínez","ONLINE","20/2, 27/2, 6/3, 13/3, 20/3",15.00,Online
Tecnologia,Nova Categoria,PROGRAMACIÓ WEB,programacio-web,"Curs complet de desenvolupament web",5,40,20,30.00,intermediate,Dimarts,Dilluns,18:00-21:00,2026-02-25,2026-05-20,"Coneixements bàsics d'informàtica","Full stack development amb HTML, CSS, JavaScript","Carlos Rodríguez","Campus Digital","25/2, 4/3, 11/3, 18/3, 25/3, 1/4, 8/4, 15/4, 22/4, 29/4",30.00,Híbrid
Arts i Disseny,Disseny Gràfic,DISENY UX/UI,disseny-ux-ui,"Disseny d'experiències d'usuari i interfícies",3,35,25,35.00,intermediate,Divendres,17:00-20:00,2026-02-28,2026-04-25,"Coneixements bàsics de disseny","Creació de prototips i disseny visual","Sofia López","Escola d'Art","28/2, 7/3, 14/3, 21/3, 28/3, 4/4, 11/4, 18/4, 25/4",35.00,Presencial`;
        
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
