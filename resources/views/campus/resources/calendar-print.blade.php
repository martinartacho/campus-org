@extends('campus.shared.layout-print')

@section('title', 'Calendari ' . $currentMonth->translatedFormat('F Y'))

@section('content')
<style>
    @page {
        size: A4 landscape;
        margin: 1cm;
    }
    
    body {
        font-family: Arial, sans-serif;
        font-size: 12px;
        margin: 0;
        padding: 0;
    }
    
    .calendar-header {
        text-align: center;
        margin-bottom: 20px;
        page-break-after: avoid;
    }
    
    .calendar-title {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 5px;
    }
    
    .calendar-subtitle {
        font-size: 14px;
        color: #666;
        margin-bottom: 10px;
    }
    
    .calendar-table {
        width: 100%;
        border-collapse: collapse;
        page-break-inside: avoid;
    }
    
    .calendar-table th {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        padding: 8px;
        text-align: center;
        font-weight: bold;
        font-size: 11px;
    }
    
    .calendar-table td {
        border: 1px solid #dee2e6;
        vertical-align: top;
        padding: 4px;
        height: 80px;
        page-break-inside: avoid;
    }
    
    .day-number {
        font-weight: bold;
        font-size: 11px;
        margin-bottom: 2px;
    }
    
    .day-non-lective {
        background-color: #ffebee;
        color: #c62828;
    }
    
    .day-weekend {
        background-color: #f5f5f5;
        color: #666;
    }
    
    .day-today {
        background-color: #e3f2fd;
        color: #1976d2;
    }
    
    .course-item {
        font-size: 9px;
        line-height: 1.2;
        margin-bottom: 2px;
        padding: 2px;
        background-color: #e3f2fd;
        border-radius: 2px;
        page-break-inside: avoid;
    }
    
    .course-code {
        font-weight: bold;
        color: #1565c0;
    }
    
    .course-info {
        color: #424242;
    }
    
    .day-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2px;
    }
    
    .course-count {
        background-color: #1976d2;
        color: white;
        font-size: 8px;
        padding: 1px 3px;
        border-radius: 2px;
    }
    
    .legend {
        margin-top: 15px;
        font-size: 10px;
        page-break-inside: avoid;
    }
    
    .legend-item {
        display: inline-block;
        margin-right: 15px;
    }
    
    .legend-color {
        display: inline-block;
        width: 12px;
        height: 12px;
        margin-right: 3px;
        border: 1px solid #ccc;
    }
</style>

<div class="calendar-header">
    <div class="calendar-title">
        Calendari {{ $currentMonth->translatedFormat('F Y') }}
    </div>
    @if($selectedSeason)
        <div class="calendar-subtitle">
            {{ $selectedSeason->name }}
        </div>
    @endif
</div>

<table class="calendar-table">
    <thead>
        <tr>
            <th>Dilluns</th>
            <th>Dimarts</th>
            <th>Dimecres</th>
            <th>Dijous</th>
            <th>Divendres</th>
            <th>Dissabte</th>
            <th>Diumenge</th>
        </tr>
    </thead>
    <tbody>
        @php
            $firstDayOfWeek = $currentMonth->copy()->startOfMonth()->dayOfWeekIso;
            $daysInMonth = $currentMonth->daysInMonth;
            $totalCells = ceil(($firstDayOfWeek - 1 + $daysInMonth) / 7) * 7;
            
            for ($row = 0; $row < 7; $row++) {
                for ($col = 0; $col < 7; $col++) {
                    $cellIndex = $row * 7 + $col;
                    
                    if ($cellIndex < $firstDayOfWeek - 1 || $cellIndex >= $firstDayOfWeek - 1 + $daysInMonth) {
                        // Cella buida
                        echo '<td>&nbsp;</td>';
                    } else {
                        $day = $cellIndex - $firstDayOfWeek + 2;
                        $currentDay = $currentMonth->copy()->day($day);
                        $dayKey = $currentDay->format('Y-m-d');
                        $daySchedules = $monthlySchedules->get($dayKey, collect());
                        $isToday = $currentDay->isToday();
                        $isWeekend = $currentDay->isWeekend();
                        $isNonLective = in_array($dayKey, $nonLectiveDays);
                        
                        $dayClass = '';
                        if ($isToday) $dayClass = 'day-today';
                        elseif ($isNonLective) $dayClass = 'day-non-lective';
                        elseif ($isWeekend) $dayClass = 'day-weekend';
                        
                        echo '<td class="' . $dayClass . '">';
                        
                        // Capçalera del dia
                        echo '<div class="day-header">';
                        echo '<span class="day-number">' . $day . '</span>';
                        if ($daySchedules->count() > 0) {
                            echo '<span class="course-count">' . $daySchedules->count() . '</span>';
                        }
                        echo '</div>';
                        
                        // Cursos del dia
                        foreach ($daySchedules as $scheduleData) {
                            $course = $scheduleData['course'];
                            $session = $scheduleData['session'];
                            $space = $scheduleData['space'];
                            
                            // Calcular comptador de sessions
                            $totalSessions = $course->sessions ?? 1;
                            $currentSessionIndex = 0;
                            
                            if ($course->schedule && is_array($course->schedule)) {
                                foreach ($course->schedule as $index => $sched) {
                                    if ($sched['date'] == $session['date'] && $sched['time'] == $session['time']) {
                                        $currentSessionIndex = $index + 1;
                                        break;
                                    }
                                }
                            }
                            
                            echo '<div class="course-item">';
                            if ($currentSessionIndex == $totalSessions) {
                                echo '<span class="course-code">' . $course->code . ' ✓ ' . $totalSessions . '/' . $totalSessions . '</span><br>';
                            } else {
                                echo '<span class="course-code">' . $course->code . ' ' . $currentSessionIndex . '/' . $totalSessions . '</span><br>';
                            }
                            echo '<span class="course-info">' . $session['time'] . ' ' . ($space->name ?? '') . '</span>';
                            echo '</div>';
                        }
                        
                        echo '</td>';
                    }
                    
                    if ($col == 6) {
                        echo '</tr><tr>';
                    }
                }
            }
        @endphp
    </tbody>
</table>

<div class="legend">
    <div class="legend-item">
        <span class="legend-color" style="background-color: #e3f2fd;"></span>
        Curs assignat
    </div>
    <div class="legend-item">
        <span class="legend-color" style="background-color: #ffebee;"></span>
        Dia no lectiu
    </div>
    <div class="legend-item">
        <span class="legend-color" style="background-color: #f5f5f5;"></span>
        Cap de setmana
    </div>
    <div class="legend-item">
        <span class="legend-color" style="background-color: #e3f2fd;"></span>
        Avui
    </div>
</div>

@endsection
