@extends('campus.shared.layout')

@section('title', 'Calendari Mensual' . ($selectedSeason ? ' - ' . $selectedSeason->name : ''))

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Capçalera -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Calendari Mensual</h1>
            @if($selectedSeason)
                <p class="text-gray-600 mt-1">
                    {{ $selectedSeason->name }} - {{ $currentMonth->format('F Y') }}
                </p>
            @endif
        </div>
        <div class="flex gap-4">
            <!-- Navegació de mesos -->
            <div class="flex items-center gap-2">
                <a href="{{ route('campus.resources.calendar.monthly', ['month' => $currentMonth->copy()->subMonth()->format('Y-m')]) }}" 
                   class="bg-gray-600 text-white px-3 py-2 rounded hover:bg-gray-700">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <span class="font-semibold text-gray-700 px-3">
                    {{ $currentMonth->format('F Y') }}
                </span>
                <a href="{{ route('campus.resources.calendar.monthly', ['month' => $currentMonth->copy()->addMonth()->format('Y-m')]) }}" 
                   class="bg-gray-600 text-white px-3 py-2 rounded hover:bg-gray-700">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </div>
            
            <!-- Selector de temporada -->
            @if($selectedSeason)
                <select id="seasonSelector" class="border rounded px-3 py-2">
                    <option value="{{ $selectedSeason->id }}" selected>{{ $selectedSeason->name }}</option>
                </select>
            @endif
            
            <!-- Enllaços de navegació -->
            <a href="{{ route('campus.resources.calendar') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                <i class="fas fa-calendar-week mr-2"></i>Vista Setmanal
            </a>
            <a href="{{ route('campus.resources.calendar.quarterly') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                <i class="fas fa-calendar mr-2"></i>Vista Quadrimestral
            </a>
            <a href="{{ route('campus.resources.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                <i class="fas fa-th-large mr-2"></i>Recursos
            </a>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Filtre per espai -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Espai</label>
                <select id="spaceFilter" class="w-full border rounded px-3 py-2">
                    <option value="">Tots els espais</option>
                    @foreach($spaces as $space)
                        <option value="{{ $space->id }}">{{ $space->name }} ({{ $space->type }})</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Filtre per curs -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Curs</label>
                <select id="courseFilter" class="w-full border rounded px-3 py-2">
                    <option value="">Tots els cursos</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->code }} - {{ $course->title }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Filtre per estat -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Estat</label>
                <select id="statusFilter" class="w-full border rounded px-3 py-2">
                    <option value="">Tots els estats</option>
                    @foreach(\App\Models\CampusCourseSchedule::STATUSES as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Calendari Mensual -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Capçalera del mes -->
        <div class="bg-gray-50 border-b border-gray-200 p-4">
            <h2 class="text-xl font-bold text-gray-800 text-center">
                {{ $currentMonth->format('F') }} {{ $currentMonth->year }}
            </h2>
        </div>
        
        <!-- Dies de la setmana -->
        <div class="grid grid-cols-7 border-b border-gray-200">
            <div class="text-sm font-medium text-gray-700 text-center py-3 bg-gray-50 border-r border-gray-200">Dilluns</div>
            <div class="text-sm font-medium text-gray-700 text-center py-3 bg-gray-50 border-r border-gray-200">Dimarts</div>
            <div class="text-sm font-medium text-gray-700 text-center py-3 bg-gray-50 border-r border-gray-200">Dimecres</div>
            <div class="text-sm font-medium text-gray-700 text-center py-3 bg-gray-50 border-r border-gray-200">Dijous</div>
            <div class="text-sm font-medium text-gray-700 text-center py-3 bg-gray-50 border-r border-gray-200">Divendres</div>
            <div class="text-sm font-medium text-gray-700 text-center py-3 bg-gray-50 border-r border-gray-200">Dissabte</div>
            <div class="text-sm font-medium text-gray-700 text-center py-3 bg-gray-50">Diumenge</div>
        </div>
        
        <!-- Calendari del mes -->
        <div class="grid grid-cols-7 gap-0 border-collapse">
            @php
                $daysInMonth = $currentMonth->daysInMonth;
                $firstDayOfMonth = $currentMonth->copy()->startOfMonth();
                $firstDayOfWeek = $firstDayOfMonth->dayOfWeekIso - 1; // Convertir de 1-7 a 0-6 (Dilluns=0)
            @endphp
            
            <!-- Espais buits abans del primer dia -->
            @for($i = 0; $i < $firstDayOfWeek; $i++)
                <div class="h-32 border border-gray-100 bg-gray-50"></div>
            @endfor
            
            <!-- Dies del mes -->
            @for($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $currentDay = $currentMonth->copy()->day($day);
                    $daySchedules = $monthlySchedules->filter(function($schedule) use ($currentDay) {
                        return \Carbon\Carbon::parse($schedule->start_date)->isSameDay($currentDay);
                    });
                    $isToday = $currentDay->isToday();
                    $isPast = $currentDay->isPast();
                    $isWeekend = $currentDay->isWeekend();
                @endphp
                
                <div class="h-32 border border-gray-200 border-r-0 border-b-0 {{ $isToday ? 'bg-blue-50' : ($isWeekend ? 'bg-gray-50' : ($isPast ? 'bg-gray-50' : 'bg-white')) }} p-2 overflow-hidden relative">
                    <!-- Número del dia -->
                    <div class="flex justify-between items-start mb-1">
                        <div class="text-sm font-medium {{ $isToday ? 'text-blue-600' : ($isWeekend ? 'text-gray-500' : 'text-gray-700') }}">
                            {{ $day }}
                        </div>
                        @if($daySchedules->count() > 0)
                            <div class="text-xs bg-blue-100 text-blue-800 px-1 rounded-full">
                                {{ $daySchedules->count() }}
                            </div>
                        @endif
                    </div>
                    
                    <!-- Horaris del dia -->
                    @if($daySchedules->count() > 0)
                        <div class="space-y-1 overflow-y-auto max-h-20">
                            @foreach($daySchedules->take(3) as $schedule)
                                @php
                                    $colorClass = 'bg-blue-100 text-blue-800 border-blue-200';
                                    if ($schedule->status === 'conflict') {
                                        $colorClass = 'bg-red-100 text-red-800 border-red-200';
                                    } elseif ($schedule->status === 'pending') {
                                        $colorClass = 'bg-yellow-100 text-yellow-800 border-yellow-200';
                                    }
                                @endphp
                                
                                <div class="text-xs p-1 rounded {{ $colorClass }} border truncate cursor-pointer hover:opacity-80" 
                                     data-space="{{ $schedule->space_id }}"
                                     data-course="{{ $schedule->course_id }}"
                                     data-status="{{ $schedule->status }}"
                                     title="{{ $schedule->course->title }} - {{ $schedule->space->name }} ({{ $schedule->timeSlot->start_time }})">
                                    <div class="font-medium truncate">{{ $schedule->course->code }}</div>
                                    <div class="truncate">{{ $schedule->space->name }}</div>
                                    <div class="truncate">{{ $schedule->timeSlot->start_time }}</div>
                                </div>
                            @endforeach
                            
                            @if($daySchedules->count() > 3)
                                <div class="text-xs text-gray-500 text-center bg-gray-100 rounded p-1">
                                    +{{ $daySchedules->count() - 3 }} més
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @endfor
            
            <!-- Espais buits després de l'últim dia -->
            @php
                $totalCells = $firstDayOfWeek + $daysInMonth;
                $remainingCells = 7 - ($totalCells % 7);
                if ($remainingCells < 7) {
                    for($i = 0; $i < $remainingCells; $i++) {
                        echo '<div class="h-32 border border-gray-100 bg-gray-50"></div>';
                    }
                }
            @endphp
        </div>
    </div>

    <!-- Llegenda -->
    <div class="mt-6 bg-white p-4 rounded-lg shadow-md">
        <h4 class="font-semibold text-gray-800 mb-3">Llegenda</h4>
        <div class="flex flex-wrap gap-4">
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-blue-100 border border-blue-200 rounded"></div>
                <span class="text-sm text-gray-700">Assignat</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-yellow-100 border border-yellow-200 rounded"></div>
                <span class="text-sm text-gray-700">Pendent</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-red-100 border border-red-200 rounded"></div>
                <span class="text-sm text-gray-700">Conflicte</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-blue-50 border border-blue-200 rounded"></div>
                <span class="text-sm text-gray-700">Avui</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-gray-50 border border-gray-200 rounded"></div>
                <span class="text-sm text-gray-700">Cap de setmana</span>
            </div>
        </div>
    </div>

    <!-- Estadístiques del mes -->
    <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-lg shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Cursos del Mes</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $monthlySchedules->pluck('course_id')->unique()->count() }}</p>
                </div>
                <div class="p-3 bg-indigo-100 rounded-lg">
                    <i class="bi bi-book text-indigo-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-lg shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Horaris</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $monthlySchedules->count() }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i class="bi bi-calendar-check text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-lg shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Conflictes</p>
                    <p class="text-2xl font-bold text-red-600">{{ $monthlySchedules->where('status', 'conflict')->count() }}</p>
                </div>
                <div class="p-3 bg-red-100 rounded-lg">
                    <i class="bi bi-exclamation-triangle text-red-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-lg shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Espais Utilitzats</p>
                    <p class="text-2xl font-bold text-green-600">{{ $monthlySchedules->pluck('space_id')->unique()->count() }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-lg">
                    <i class="bi bi-building text-green-600"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script per als filtres -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const spaceFilter = document.getElementById('spaceFilter');
    const courseFilter = document.getElementById('courseFilter');
    const statusFilter = document.getElementById('statusFilter');
    
    function applyFilters() {
        const spaceId = spaceFilter.value;
        const courseId = courseFilter.value;
        const status = statusFilter.value;
        
        // Filtrar visualment els horaris
        document.querySelectorAll('[data-schedule]').forEach(element => {
            const scheduleSpace = element.dataset.space;
            const scheduleCourse = element.dataset.course;
            const scheduleStatus = element.dataset.status;
            
            let show = true;
            
            if (spaceId && scheduleSpace !== spaceId) show = false;
            if (courseId && scheduleCourse !== courseId) show = false;
            if (status && scheduleStatus !== status) show = false;
            
            element.style.display = show ? '' : 'none';
        });
    }
    
    spaceFilter.addEventListener('change', applyFilters);
    courseFilter.addEventListener('change', applyFilters);
    statusFilter.addEventListener('change', applyFilters);
});
</script>
@endsection
