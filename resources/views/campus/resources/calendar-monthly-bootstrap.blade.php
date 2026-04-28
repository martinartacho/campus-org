@extends('campus.shared.layout')

@section('title', 'Calendari Mensual' . ($selectedSeason ? ' - ' . $selectedSeason->name : ''))

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container mx-auto px-4 py-8">
    <!-- Capçalera -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark">Calendari Mensual</h1>
            @if($selectedSeason)
                <p class="text-muted mt-1">
                    {{ $selectedSeason->name }} - {{ $currentMonth->format('F Y') }}
                </p>
            @endif
        </div>
        <div class="d-flex gap-2">
            <!-- Navegació de mesos -->
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('campus.resources.calendar.export', ['season_id' => $selectedSeason->id ?? null]) }}" 
                   class="btn btn-success btn-sm no-print" title="Exportar temporada completa a Excel">
                    <i class="bi bi-file-earmark-excel"></i>
                </a>
                <button onclick="printCalendar()" class="btn btn-outline-success btn-sm no-print" title="Imprimir (Ctrl+P)">
                    <i class="bi bi-printer"></i>
                </button>
                
                <!-- Selector ràpid de mesos -->
                <select id="monthSelector" class="form-select form-select-sm no-print" style="width: 200px;">
                    @php
                        $currentYear = now()->year;
                        $months = [];
                        for($y = $currentYear; $y <= $currentYear + 2; $y++) {
                            for($m = 1; $m <= 12; $m++) {
                                $monthDate = \Carbon\Carbon::create($y, $m, 1);
                                $months[] = [
                                    'value' => $monthDate->format('Y-m'),
                                    'label' => $monthDate->translatedFormat('F Y'),
                                    'selected' => $monthDate->format('Y-m') === $currentMonth->format('Y-m')
                                ];
                            }
                        }
                    @endphp
                    
                    @foreach($months as $month)
                        <option value="{{ $month['value'] }}" {{ $month['selected'] ? 'selected' : '' }}>
                            {{ $month['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Enllaços de navegació -->
            <a href="{{ route('campus.resources.calendar') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-calendar-week me-1"></i>Vista Setmanal
            </a>
            <a href="{{ route('campus.resources.calendar.quarterly') }}" class="btn btn-success btn-sm">
                <i class="bi bi-calendar me-1"></i>Vista Quadrimestral
            </a>
            <a href="{{ route('campus.resources.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-grid-3x3-gap me-1"></i>Recursos
            </a>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <!-- Filtre per espai -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Espai</label>
                    <select id="spaceFilter" class="form-select">
                        <option value="">Tots els espais</option>
                        @foreach($spaces as $space)
                            <option value="{{ $space->id }}">{{ $space->name }} ({{ $space->type }})</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Filtre per curs -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Curs</label>
                    <select id="courseFilter" class="form-select">
                        <option value="">Tots els cursos</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->code }} - {{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Filtre per estat -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Estat</label>
                    <select id="statusFilter" class="form-select">
                        <option value="">Tots els estats</option>
                        @foreach(\App\Models\CampusCourseSchedule::STATUSES as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendari Mensual -->
    <div class="card">
        <!-- Capçalera del mes -->
        <div class="card-header bg-light">
            <h2 class="h5 text-center mb-0 fw-bold">
                {{ $currentMonth->format('F') }} {{ $currentMonth->year }}
            </h2>
        </div>
        
        <!-- Dies de la setmana -->
        <div class="table-responsive">
            <table class="table table-bordered table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center fw-semibold">Dilluns</th>
                        <th class="text-center fw-semibold">Dimarts</th>
                        <th class="text-center fw-semibold">Dimecres</th>
                        <th class="text-center fw-semibold">Dijous</th>
                        <th class="text-center fw-semibold">Divendres</th>
                        <th class="text-center fw-semibold">Dissabte</th>
                        <th class="text-center fw-semibold">Diumenge</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $daysInMonth = $currentMonth->daysInMonth;
                        $firstDayOfMonth = $currentMonth->copy()->startOfMonth();
                        $firstDayOfWeek = $firstDayOfMonth->dayOfWeekIso - 1; // Convertir de 1-7 a 0-6 (Dilluns=0)
                        $totalCells = $firstDayOfWeek + $daysInMonth;
                        $rows = ceil($totalCells / 7);
                    @endphp
                    
                    @for($row = 0; $row < $rows; $row++)
                        <tr>
                            @for($col = 0; $col < 7; $col++)
                                @php
                                    $cellIndex = $row * 7 + $col;
                                @endphp
                                
                                @if($cellIndex < $firstDayOfWeek || $cellIndex >= $firstDayOfWeek + $daysInMonth)
                                    <!-- Cella buida -->
                                    <td class="bg-light" style="height: 120px;"></td>
                                @else
                                    @php
                                        $day = $cellIndex - $firstDayOfWeek + 1;
                                        $currentDay = $currentMonth->copy()->day($day);
                                        // Obtenir sessions per aquest dia específic
                                        $dayKey = $currentDay->format('Y-m-d');
                                        $daySchedules = $monthlySchedules->get($dayKey, collect());
                                        $isToday = $currentDay->isToday();
                                        $isPast = $currentDay->isPast();
                                        $isWeekend = $currentDay->isWeekend();
                                        $isNonLective = in_array($dayKey, $nonLectiveDays);
                                        
                                        $bgClass = 'bg-white';
                                        if ($isToday) $bgClass = 'bg-primary bg-opacity-10';
                                        elseif ($isNonLective) $bgClass = 'bg-danger bg-opacity-10';
                                        elseif ($isWeekend) $bgClass = 'bg-light';
                                        elseif ($isPast) $bgClass = 'bg-light';
                                    @endphp
                                    
                                    <td class="{{ $bgClass }} p-2 day-cell" style="height: auto; min-height: 200px; vertical-align: top; cursor: pointer;" 
    data-date="{{ $currentDay->format('Y-m-d') }}" 
    title="Clica per marcar/desmarcar dia no lectiu">
                                        <!-- Número del dia -->
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="fw-semibold {{ $isToday ? 'text-primary' : ($isNonLective ? 'text-danger' : ($isWeekend ? 'text-muted' : 'text-dark')) }}">
                                                {{ $day }}
                                                @if($isNonLective)
                                                    <i class="bi bi-x-circle-fill text-danger small ms-1"></i>
                                                @endif
                                            </div>
                                            @if($daySchedules->count() > 0)
                                                <span class="badge bg-primary rounded-pill">
                                                    {{ $daySchedules->where('course', '!=', null)->count() }}
                                                </span>
                                            @endif
                                        </div>
                                        
                                        <!-- Totes les franges horaries del dia -->
                                        <div class="time-slots-container">
                                            @foreach($daySchedules as $scheduleData)
                                                @php
                                                    $course = $scheduleData['course'];
                                                    $session = $scheduleData['session'];
                                                    $space = $scheduleData['space'];
                                                    $timeSlot = $scheduleData['timeSlot'];
                                                    $isEmpty = $session['is_empty'] ?? false;
                                                @endphp
                                                
                                                <!-- Curs assignat -->
                                                    <div class="small p-1 mb-1 rounded bg-primary text-white text-decoration-none cursor-pointer" 
                                                         data-space="{{ $space->id }}"
                                                         data-course="{{ $course->id }}"
                                                         data-date="{{ $session['date'] }}"
                                                         data-time="{{ $session['time'] }}"
                                                         title="{{ $course->title }} - {{ $space->name }} ({{ $session['time'] }})">
                                                        <div class="fw-bold">{{ $course->code }}</div>
                                                        <div class="small">{{ $session['time'] }} {{ $space->name }}</div>
                                                    </div>
                                            @endforeach
                                        </div>
                                    </td>
                                @endif
                            @endfor
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>

    <!-- Llegenda -->
    <div class="card mt-4">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-3">Llegenda</h5>
            <div class="d-flex flex-wrap gap-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-primary rounded" style="width: 20px; height: 20px;"></div>
                    <span class="small">Assignat</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-warning rounded" style="width: 20px; height: 20px;"></div>
                    <span class="small">Pendent</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-danger rounded" style="width: 20px; height: 20px;"></div>
                    <span class="small">Conflicte</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-primary bg-opacity-10 border border-primary rounded" style="width: 20px; height: 20px;"></div>
                    <span class="small">Avui</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-light border border-secondary rounded" style="width: 20px; height: 20px;"></div>
                    <span class="small">Cap de setmana</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadístiques del mes -->
    <div class="row mt-4">
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Cursos del Mes</p>
                            <p class="h4 mb-0 fw-bold">{{ $monthlySchedules->pluck('course_id')->unique()->count() }}</p>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                            <i class="bi bi-book text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Total Horaris</p>
                            <p class="h4 mb-0 fw-bold">{{ $monthlySchedules->count() }}</p>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded-circle p-2">
                            <i class="bi bi-calendar-check text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Conflictes</p>
                            <p class="h4 mb-0 fw-bold text-danger">{{ $monthlySchedules->where('status', 'conflict')->count() }}</p>
                        </div>
                        <div class="bg-danger bg-opacity-10 rounded-circle p-2">
                            <i class="bi bi-exclamation-triangle text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Espais Utilitzats</p>
                            <p class="h4 mb-0 fw-bold text-success">{{ $monthlySchedules->pluck('space_id')->unique()->count() }}</p>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded-circle p-2">
                            <i class="bi bi-building text-success"></i>
                        </div>
                    </div>
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

function printCalendar() {
    window.print();
}

// Auto-impressió opcional
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.key === 'p') {
        e.preventDefault();
        printCalendar();
    }
});

// Selector de mesos
document.addEventListener('DOMContentLoaded', function() {
    const monthSelector = document.getElementById('monthSelector');
    if (monthSelector) {
        monthSelector.addEventListener('change', function() {
            const selectedMonth = this.value;
            window.location.href = `/campus/resources/calendar/monthly-bootstrap?month=${selectedMonth}`;
        });
    }
    
    // Gestió de dies no lectius
    document.querySelectorAll('.day-cell').forEach(cell => {
        cell.addEventListener('click', function(e) {
            // Si no és un curs, permet marcar/desmarcar
            if (!e.target.closest('.bg-primary')) {
                const date = this.dataset.date;
                toggleNonLectiveDay(date);
            }
        });
    });
});

function toggleNonLectiveDay(date) {
    console.log('Toggling non-lective day:', date);
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        console.error('CSRF token not found');
        alert('Error: CSRF token no trobat');
        return;
    }
    
    fetch('/campus/resources/calendar/toggle-non-lective', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken.getAttribute('content')
        },
        body: JSON.stringify({
            date: date
        })
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            // Recarregar la pàgina per veure els canvis
            window.location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al marcar/desmarcar dia no lectiu: ' + error.message);
    });
}
</script>
@endsection

@push('styles')
<style>
    .calendar-container {
        max-width: 1400px;
        margin: 0 auto;
    }
    
    .calendar-table {
        font-size: 0.85rem;
    }
    
    .calendar-table th {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        padding: 0.5rem;
        text-align: center;
        font-weight: 600;
    }
    
    .calendar-table td {
        border: 1px solid #dee2e6;
        vertical-align: top;
    }
    
    .time-slots-container {
        max-height: none;
        overflow: visible;
    }
    
    /* Estils per impressió */
    @media print {
        .no-print {
            display: none !important;
        }
        
        .calendar-container {
            max-width: 100%;
            margin: 0;
            padding: 10px;
        }
        
        .calendar-table {
            font-size: 0.75rem;
        }
        
        .calendar-table td {
            height: auto !important;
            min-height: 150px !important;
            page-break-inside: avoid;
        }
        
        .time-slots-container {
            page-break-inside: avoid;
        }
    }
</style>
@endpush
