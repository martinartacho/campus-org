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
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">
        {{ __('campus.courses') }}
    </h1>

    <div class="flex space-x-3">
        @can('campus.courses.create')
            <a href="{{ route('campus.courses.create') }}"
               class="campus-primary-button">
                {{ __('campus.new_course') }}
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

<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="p-6">
        <div class="mb-4">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="bi bi-search text-gray-400"></i>
                        </div>
                        <input type="text" 
                               id="searchCourse" 
                               placeholder="{{ __('campus.search_course') }}" 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <button id="clearCourseSearch" 
                            class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition duration-200 hidden">
                        <i class="bi bi-x-circle mr-2"></i>{{ __('campus.clear') }}
                    </button>
                    <span id="courseSearchResults" class="text-sm text-gray-500 py-2"></span>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
        <tr>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">
                {{ __('campus.title') }}
            </th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">
                {{ __('campus.season') }}
            </th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">
                {{ __('campus.category') }}
            </th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">
                {{ __('campus.status') }}
            </th>
            <th class="px-4 py-3 text-right text-sm font-medium text-gray-500">
                &nbsp;
            </th>
        </tr>
        </thead>

        <tbody class="bg-white divide-y divide-gray-200">
        @forelse ($courses as $course)
            <tr class="course-row">
                <td class="px-4 py-3">
                    <a href="{{ route('campus.courses.show', $course) }}"
                       class="font-medium text-blue-600 hover:underline">
                        {{ $course->title }}
                    </a>
                </td>
                <td class="px-4 py-3">
                    {{ $course->season?->name ?? '—' }}
                </td>
                <td class="px-4 py-3">
                    {{ $course->category?->name ?? '—' }}
                </td>
                <td class="px-4 py-3">
                    @if($course->is_active)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="bi bi-check-circle me-1"></i> {{ __('campus.active') }}
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <i class="bi bi-x-circle me-1"></i> {{ __('campus.inactive') }}
                        </span>
                    @endif
                </td>
                <td class="px-4 py-3 text-right space-x-2">
                    <div class="flex space-x-2">
                    @can('campus.courses.edit')
                            <a href="{{ route('campus.courses.edit', $course) }}" 
                                class="text-blue-600 hover:text-blue-900"
                                title="{{ __('campus.edit') }}">
                                <i class="bi bi-pencil"></i>
                            </a>
                    @endcan

                    @can('campus.courses.view')
                            <a href="{{ route('campus.courses.show', $course) }}" 
                                class="text-blue-600 hover:text-blue-900"
                                title="{{ __('campus.view') }}">
                                <i class="bi bi-eye"></i>
                            </a>
                    @endcan

                    @can('campus.courses.delete')
                        <form action="{{ route('campus.courses.destroy', $course) }}" 
                                      method="POST" 
                                      class="inline"
                                      onsubmit="return confirm('{{ __('campus.course_delete_confirmation') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-900"
                                            title="{{ __('campus.delete') }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                    @endcan
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                    {{ __('campus.no_courses') }}
                </td>
            </tr>
        @endforelse
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
