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
    protected $seasonId;

    public function __construct($seasonId = null)
    {
        $this->seasonId = $seasonId;
    }

    public function collection()
    {
        $data = [];
        
        // Obtenir temporada
        $season = $this->seasonId ? 
            \App\Models\CampusSeason::find($this->seasonId) : 
            \App\Models\CampusSeason::getDefaultForCalendar();
            
        if (!$season) {
            return collect($data);
        }
        
        // Obtenir rang de dates de la temporada (o per defecte)
        if ($season->start_date && $season->end_date) {
            $startDate = \Carbon\Carbon::parse($season->start_date);
            $endDate = \Carbon\Carbon::parse($season->end_date);
        } else {
            // Rang per defecte: 1r de setembre a 31 de desembre
            $startDate = \Carbon\Carbon::create(now()->year, 9, 1);
            $endDate = \Carbon\Carbon::create(now()->year, 12, 31);
        }
        
        // 1. Tots els cursos de la temporada
        $courses = CampusCourse::where('season_id', $season->id)
            ->whereNotNull('schedule')
            ->where('schedule', '!=', '[]')
            ->with(['space', 'timeSlot'])
            ->orderBy('title')
            ->get();

        foreach ($courses as $course) {
            if ($course->schedule && is_array($course->schedule)) {
                foreach ($course->schedule as $session) {
                    $sessionDate = \Carbon\Carbon::parse($session['date']);
                    
                    // Només sessions dins del rang de la temporada
                    if ($sessionDate->between($startDate, $endDate)) {
                        $data[] = [
                            'Tipus' => 'Curs',
                            'Data' => $session['date'],
                            'Hora' => $session['time'],
                            'Codi' => $course->code,
                            'Títol' => $course->title,
                            'Espai' => $course->space->name ?? 'Sense assignar',
                            'Professor' => $course->teacher->name ?? 'Sense assignar',
                            'Hores' => '',
                            'Sessions' => count($course->schedule),
                            'Mes' => $sessionDate->translatedFormat('F'),
                        ];
                    }
                }
            }
        }

        // 2. Tots els dies no lectius de la temporada
        $nonLectiveDays = CampusNonLectiveDay::whereBetween('date', [$startDate, $endDate])
            ->where('is_active', true)
            ->orderBy('date')
            ->get();

        foreach ($nonLectiveDays as $day) {
            $dayDate = \Carbon\Carbon::parse($day->date);
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
                'Mes' => $dayDate->translatedFormat('F'),
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
            'Mes',
        ];
    }

    public function title(): string
    {
        $season = $this->seasonId ? 
            \App\Models\CampusSeason::find($this->seasonId) : 
            \App\Models\CampusSeason::getDefaultForCalendar();
            
        return $season ? "Calendari {$season->name}" : "Calendari Completa";
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A' => ['font' => ['bold' => true]],
        ];
    }
}
