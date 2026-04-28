<?php

namespace App\Exports;

use App\Models\CampusCourse;
use App\Models\CampusNonLectiveDay;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CalendarExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $month;
    protected $year;

    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

    public function collection()
    {
        $data = [];
        
        // 1. Cursos del mes
        $startDate = \Carbon\Carbon::create($this->year, $this->month, 1);
        $endDate = $startDate->copy()->endOfMonth();
        
        $season = \App\Models\CampusSeason::getDefaultForCalendar();
        $courses = CampusCourse::where('season_id', $season->id ?? null)
            ->whereNotNull('schedule')
            ->where('schedule', '!=', '[]')
            ->with(['space', 'timeSlot'])
            ->orderBy('title')
            ->get();

        foreach ($courses as $course) {
            if ($course->schedule && is_array($course->schedule)) {
                foreach ($course->schedule as $session) {
                    $sessionDate = \Carbon\Carbon::parse($session['date']);
                    
                    if ($sessionDate->month == $this->month && $sessionDate->year == $this->year) {
                        $data[] = [
                            'Tipus' => 'Curs',
                            'Data' => $session['date'],
                            'Hora' => $session['time'],
                            'Codi' => $course->code,
                            'Títol' => $course->title,
                            'Espai' => $course->space->name ?? 'Sense assignar',
                            'Professor' => $course->teacher->name ?? 'Sense assignar',
                            'Hores' => $course->hours,
                            'Sessions' => count($course->schedule),
                        ];
                    }
                }
            }
        }

        // 2. Dies no lectius
        $nonLectiveDays = CampusNonLectiveDay::whereMonth('date', $this->month)
            ->whereYear('date', $this->year)
            ->where('is_active', true)
            ->orderBy('date')
            ->get();

        foreach ($nonLectiveDays as $day) {
            $data[] = [
                'Tipus' => 'Dia No Lectiu',
                'Data' => $day->date,
                'Hora' => '',
                'Codi' => '',
                'Títol' => $day->description,
                'Espai' => '',
                'Professor' => '',
                'Hores' => '',
                'Sessions' => '',
            ];
        }

        // Ordenar per data
        usort($data, function($a, $b) {
            return strcmp($a['Data'], $b['Data']);
        });

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'Tipus',
            'Data',
            'Hora', 
            'Codi',
            'Títol',
            'Espai',
            'Professor',
            'Hores',
            'Sessions',
        ];
    }

    public function title(): string
    {
        $monthName = \Carbon\Carbon::create($this->year, $this->month, 1)->translatedFormat('F');
        return "Calendari {$monthName} {$this->year}";
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A' => ['font' => ['bold' => true]],
        ];
    }
}
